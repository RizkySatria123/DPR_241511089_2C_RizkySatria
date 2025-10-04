<?php

namespace App\Controllers;

use App\Models\AnggotaModel;
use App\Models\KomponenGajiModel;
use App\Models\PenggajianModel;
use CodeIgniter\HTTP\RedirectResponse;

class PenggajianController extends BaseController
{
    private PenggajianModel $penggajianModel;
    private AnggotaModel $anggotaModel;
    private KomponenGajiModel $komponenModel;

    /** @var array<string, string> */
    private array $satuanOptions = [
        'Hari'    => 'Per Hari',
        'Bulan'   => 'Per Bulan',
        'Periode' => 'Per Periode',
    ];

    /** @var array<string, string> */
    private array $kategoriOptions = [
        'Gaji Pokok'        => 'Gaji Pokok',
        'Tunjangan Melekat' => 'Tunjangan Melekat',
        'Tunjangan Lain'    => 'Tunjangan Lain',
    ];

    /** @var array<string, string> */
    private array $satuanNormalizationMap = [
        'per bulan'    => 'Bulan',
        'bulan'        => 'Bulan',
        'per sidang'   => 'Periode',
        'sidang'       => 'Periode',
        'persidangan'  => 'Periode',
        'sekali bayar' => 'Periode',
        'sekali'       => 'Periode',
        'per hari'     => 'Hari',
        'hari'         => 'Hari',
        'per periode'  => 'Periode',
        'periode'      => 'Periode',
    ];

    public function __construct()
    {
        $this->penggajianModel = new PenggajianModel();
        $this->anggotaModel    = new AnggotaModel();
        $this->komponenModel   = new KomponenGajiModel();
    }

    private function ensureAdmin(): ?RedirectResponse
    {
        $session = session();

        if (! $session->get('isLoggedIn')) {
            $session->set('redirect_url', current_url());

            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu.');
        }

        if (strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Anda tidak memiliki akses.');
        }

        return null;
    }

    private function normalizeSatuan(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        $trimmed = trim($value);
        $key     = strtolower($trimmed);

        return $this->satuanNormalizationMap[$key] ?? $trimmed;
    }

    private function anggotaFullName(array $anggota): string
    {
        $gelarDepan    = trim((string) ($anggota['gelar_depan'] ?? ''));
        $gelarBelakang = trim((string) ($anggota['gelar_belakang'] ?? ''));
        $namaInti      = trim((string) ($anggota['nama_depan'] ?? '') . ' ' . ($anggota['nama_belakang'] ?? ''));

        $full = trim(($gelarDepan ? $gelarDepan . ' ' : '') . $namaInti . ($gelarBelakang ? ', ' . $gelarBelakang : ''));

        return $full !== '' ? $full : $namaInti;
    }

    private function availableKomponenForAnggota(array $anggota, array $excludeIds = []): array
    {
        $builder = $this->komponenModel->builder();
        $builder
            ->select('*')
            ->orderBy('nama_komponen', 'ASC');

        $jabatan = trim((string) ($anggota['jabatan'] ?? ''));

        if ($jabatan !== '') {
            $builder->groupStart()
                ->where('jabatan', $jabatan)
                ->orWhere('jabatan', 'Semua')
                ->groupEnd();
        }

        $rows = $builder->get()->getResultArray();

        if ($excludeIds === []) {
            return $rows;
        }

        $excludeLookup = array_flip(array_map('intval', $excludeIds));

        return array_values(array_filter($rows, static function (array $row) use ($excludeLookup): bool {
            $id = (int) ($row['id_komponen_gaji'] ?? 0);

            return ! isset($excludeLookup[$id]);
        }));
    }

    public function manage(int $anggotaId)
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $anggotaEntity = $this->anggotaModel->find($anggotaId);

        if (! $anggotaEntity) {
            return redirect()->to(base_url('admin/anggota'))->with('error', 'Data anggota tidak ditemukan.');
        }

        $anggota = (array) $anggotaEntity;

        $assignments = $this->penggajianModel->getAssignmentsForAnggota($anggotaId);
        $assignedIds = array_map(static fn (array $row): int => (int) $row['id_komponen_gaji'], $assignments);

        foreach ($assignments as &$row) {
            $row['satuan']  = $this->normalizeSatuan($row['satuan'] ?? '');
            $row['kategori'] = $this->kategoriOptions[$row['kategori']] ?? $row['kategori'];
        }
        unset($row);

        $availableKomponen = $this->availableKomponenForAnggota($anggota, $assignedIds);

        foreach ($availableKomponen as &$row) {
            $row['satuan'] = $this->normalizeSatuan($row['satuan'] ?? '');
        }
        unset($row);

        return view('penggajian/manage', [
            'anggota'            => $anggota,
            'assignments'        => $assignments,
            'availableKomponen'  => $availableKomponen,
            'satuanOptions'      => $this->satuanOptions,
            'anggotaDisplayName' => $this->anggotaFullName($anggota),
        ]);
    }

    public function store(int $anggotaId): RedirectResponse
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        helper(['form']);

        $anggotaEntity = $this->anggotaModel->find($anggotaId);

        if (! $anggotaEntity) {
            return redirect()->to(base_url('admin/anggota'))->with('error', 'Data anggota tidak ditemukan.');
        }

        $anggota = (array) $anggotaEntity;

        $rules = [
            'id_komponen_gaji' => 'required|is_natural_no_zero',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', 'Pilihan komponen gaji tidak valid.')->withInput();
        }

        $komponenId = (int) $this->request->getPost('id_komponen_gaji');
        $komponen   = $this->komponenModel->find($komponenId);

        if (! $komponen) {
            return redirect()->back()->with('error', 'Komponen gaji tidak ditemukan.')->withInput();
        }

        $jabatanKomponen = trim((string) ($komponen['jabatan'] ?? ''));
        $jabatanAnggota  = trim((string) ($anggota['jabatan'] ?? ''));

        if ($jabatanKomponen !== '' && $jabatanKomponen !== 'Semua' && $jabatanKomponen !== $jabatanAnggota) {
            return redirect()->back()->with('error', 'Komponen gaji tidak sesuai dengan jabatan anggota.')->withInput();
        }

        if ($this->penggajianModel->exists($anggotaId, $komponenId)) {
            return redirect()->back()->with('error', 'Komponen gaji sudah ditambahkan untuk anggota ini.');
        }

        try {
            if (! $this->penggajianModel->assign($anggotaId, $komponenId)) {
                throw new \RuntimeException('Insert gagal dijalankan.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'Gagal menambahkan penggajian untuk anggota {anggota} dan komponen {komponen}: {pesan}', [
                'anggota'  => $anggotaId,
                'komponen' => $komponenId,
                'pesan'    => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menambahkan data penggajian.')->withInput();
        }

        return redirect()->to(base_url('admin/penggajian/anggota/' . $anggotaId))
            ->with('success', 'Komponen gaji berhasil ditambahkan.');
    }

    public function delete(int $anggotaId, int $komponenId): RedirectResponse
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $anggotaEntity = $this->anggotaModel->find($anggotaId);

        if (! $anggotaEntity) {
            return redirect()->to(base_url('admin/anggota'))->with('error', 'Data anggota tidak ditemukan.');
        }

        $anggota = (array) $anggotaEntity;

        try {
            $this->penggajianModel->remove($anggotaId, $komponenId);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal menghapus penggajian anggota {anggota} komponen {komponen}: {pesan}', [
                'anggota'  => $anggotaId,
                'komponen' => $komponenId,
                'pesan'    => $e->getMessage(),
            ]);

            return redirect()->to(base_url('admin/penggajian/anggota/' . $anggotaId))
                ->with('error', 'Terjadi kesalahan saat menghapus data penggajian.');
        }

        return redirect()->to(base_url('admin/penggajian/anggota/' . $anggotaId))
            ->with('success', 'Komponen gaji berhasil dihapus.');
    }
}
