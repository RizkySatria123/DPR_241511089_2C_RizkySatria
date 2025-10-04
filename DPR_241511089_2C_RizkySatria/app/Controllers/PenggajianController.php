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

    public function index()
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $rows = $this->penggajianModel->getTakeHomeSummary();

        $totalKomponen = 0;
        $totalNominal  = 0.0;
        $anggotaDenganKomponen = 0;
        $topTakeHome = ['nama' => null, 'total' => 0.0];

        foreach ($rows as &$row) {
            $row['total_komponen'] = (int) ($row['total_komponen'] ?? 0);
            $row['total_nominal']  = (float) ($row['total_nominal'] ?? 0);
            $row['display_name']   = $this->anggotaFullName($row);

            $totalKomponen += $row['total_komponen'];
            $totalNominal  += $row['total_nominal'];

            if ($row['total_komponen'] > 0) {
                $anggotaDenganKomponen++;
            }

            if ($row['total_nominal'] > $topTakeHome['total']) {
                $topTakeHome = [
                    'nama'  => $row['display_name'],
                    'total' => $row['total_nominal'],
                ];
            }
        }
        unset($row);

        $averageNominal = $anggotaDenganKomponen > 0
            ? $totalNominal / $anggotaDenganKomponen
            : 0.0;

        $summary = [
            'totalAnggota'            => count($rows),
            'totalKomponen'           => $totalKomponen,
            'totalNominal'            => $totalNominal,
            'averageNominal'          => $averageNominal,
            'anggotaDenganKomponen'   => $anggotaDenganKomponen,
            'tertinggiNama'           => $topTakeHome['nama'],
            'tertinggiNominal'        => $topTakeHome['total'],
        ];

        return view('penggajian/index', [
            'rows'    => $rows,
            'summary' => $summary,
        ]);
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
            'anggotaId'          => $anggotaId,
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

        $rawKomponenIds = $this->request->getPost('id_komponen_gaji');
        $komponenIds = array_values(array_unique(array_filter(array_map('intval', (array) $rawKomponenIds), static fn (int $id): bool => $id > 0)));

        if ($komponenIds === []) {
            return redirect()->back()->with('error', 'Pilih minimal satu komponen gaji yang ingin ditambahkan.')->withInput();
        }

        $jabatanAnggota = trim((string) ($anggota['jabatan'] ?? ''));

        $addedCount      = 0;
        $duplicateNames  = [];
        $invalidNames    = [];

        foreach ($komponenIds as $komponenId) {
            $komponen = $this->komponenModel->find($komponenId);

            if (! $komponen) {
                $invalidNames[] = (string) $komponenId;
                continue;
            }

            $namaKomponen   = trim((string) ($komponen['nama_komponen'] ?? 'Komponen #' . $komponenId));
            $jabatanKomponen = trim((string) ($komponen['jabatan'] ?? ''));

            if ($jabatanKomponen !== '' && $jabatanKomponen !== 'Semua' && $jabatanKomponen !== $jabatanAnggota) {
                $invalidNames[] = $namaKomponen;
                continue;
            }

            if ($this->penggajianModel->exists($anggotaId, $komponenId)) {
                $duplicateNames[] = $namaKomponen;
                continue;
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

            $addedCount++;
        }

        if ($addedCount === 0) {
            $messages = [];
            if ($duplicateNames !== []) {
                $messages[] = 'Komponen yang dipilih sudah terdaftar.';
            }
            if ($invalidNames !== []) {
                $messages[] = 'Beberapa komponen tidak sesuai dengan jabatan anggota.';
            }

            return redirect()->back()->with('error', implode(' ', $messages) ?: 'Tidak ada komponen yang ditambahkan.')->withInput();
        }

        $successMessage = $addedCount . ' komponen gaji berhasil ditambahkan.';

        $warningParts = [];
        if ($duplicateNames !== []) {
            $warningParts[] = 'Duplikat diabaikan: ' . implode(', ', array_unique($duplicateNames)) . '.';
        }

        if ($invalidNames !== []) {
            $warningParts[] = 'Tidak ditambahkan karena tidak sesuai jabatan: ' . implode(', ', array_unique($invalidNames)) . '.';
        }

        $redirect = redirect()->to(base_url('admin/penggajian/anggota/' . $anggotaId))
            ->with('success', $successMessage);

        if ($warningParts !== []) {
            $redirect->with('warning', implode(' ', $warningParts));
        }

        return $redirect;
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

    public function bulkDelete(int $anggotaId): RedirectResponse
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $anggotaEntity = $this->anggotaModel->find($anggotaId);

        if (! $anggotaEntity) {
            return redirect()->to(base_url('admin/anggota'))->with('error', 'Data anggota tidak ditemukan.');
        }

        $rawIds = $this->request->getPost('komponen_ids');
        $komponenIds = array_values(array_unique(array_filter(array_map('intval', (array) $rawIds), static fn (int $id): bool => $id > 0)));

        if ($komponenIds === []) {
            return redirect()->back()->with('error', 'Pilih minimal satu komponen yang ingin dihapus.');
        }

        $removedCount = 0;
        $notFound     = [];

        foreach ($komponenIds as $komponenId) {
            $komponen = $this->komponenModel->find($komponenId);
            $namaKomponen = $komponen['nama_komponen'] ?? 'Komponen #' . $komponenId;

            if (! $this->penggajianModel->exists($anggotaId, $komponenId)) {
                $notFound[] = $namaKomponen;
                continue;
            }

            try {
                if ($this->penggajianModel->remove($anggotaId, $komponenId)) {
                    $removedCount++;
                }
            } catch (\Throwable $e) {
                log_message('error', 'Gagal menghapus penggajian anggota {anggota} komponen {komponen}: {pesan}', [
                    'anggota'  => $anggotaId,
                    'komponen' => $komponenId,
                    'pesan'    => $e->getMessage(),
                ]);

                return redirect()->to(base_url('admin/penggajian/anggota/' . $anggotaId))
                    ->with('error', 'Terjadi kesalahan saat menghapus data penggajian.');
            }
        }

        if ($removedCount === 0) {
            return redirect()->to(base_url('admin/penggajian/anggota/' . $anggotaId))
                ->with('error', 'Tidak ada komponen yang dihapus.');
        }

        $message = $removedCount . ' komponen gaji berhasil dihapus.';

        $redirect = redirect()->to(base_url('admin/penggajian/anggota/' . $anggotaId))
            ->with('success', $message);

        if ($notFound !== []) {
            $redirect->with('warning', 'Komponen tidak ditemukan atau sudah dihapus: ' . implode(', ', array_unique($notFound)) . '.');
        }

        return $redirect;
    }

    public function detail(int $anggotaId)
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $anggotaEntity = $this->anggotaModel->find($anggotaId);

        if (! $anggotaEntity) {
            return redirect()->to(base_url('admin/penggajian'))
                ->with('error', 'Data anggota tidak ditemukan.');
        }

        $anggota = (array) $anggotaEntity;

        $assignments = $this->penggajianModel->getAssignmentsForAnggota($anggotaId);

        $totalNominal = 0.0;
        $totalsByKategori = [];
        $totalsBySatuan   = [];

        foreach ($assignments as &$row) {
            $row['satuan']  = $this->normalizeSatuan($row['satuan'] ?? '');
            $row['kategori'] = $this->kategoriOptions[$row['kategori']] ?? ($row['kategori'] ?? '-');
            $row['nominal']  = (float) ($row['nominal'] ?? 0);

            $totalNominal += $row['nominal'];

            $totalsByKategori[$row['kategori']] = ($totalsByKategori[$row['kategori']] ?? 0) + $row['nominal'];
            $satuanLabel = $this->satuanOptions[$row['satuan']] ?? $row['satuan'];
            $totalsBySatuan[$satuanLabel] = ($totalsBySatuan[$satuanLabel] ?? 0) + $row['nominal'];
        }
        unset($row);

        ksort($totalsByKategori);
        ksort($totalsBySatuan);

        $data = [
            'anggota'            => $anggota,
            'assignments'        => $assignments,
            'totalNominal'       => $totalNominal,
            'totalKomponen'      => count($assignments),
            'totalsByKategori'   => $totalsByKategori,
            'totalsBySatuan'     => $totalsBySatuan,
            'anggotaDisplayName' => $this->anggotaFullName($anggota),
            'satuanOptions'      => $this->satuanOptions,
        ];

        return view('penggajian/detail', $data);
    }
}
