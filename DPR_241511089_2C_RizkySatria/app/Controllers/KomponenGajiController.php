<?php

namespace App\Controllers;

use App\Models\KomponenGajiModel;
use CodeIgniter\HTTP\RedirectResponse;

class KomponenGajiController extends BaseController
{
    private KomponenGajiModel $model;

    /** @var array<string, string> */
    private array $kategoriOptions = [
        'Gaji Pokok'         => 'Gaji Pokok',
        'Tunjangan Melekat'  => 'Tunjangan Melekat',
        'Tunjangan Lain'     => 'Tunjangan Lain',
    ];

    /** @var array<string, string> */
    private array $jabatanOptions = [
        'Ketua'       => 'Ketua',
        'Wakil Ketua' => 'Wakil Ketua',
        'Anggota'     => 'Anggota',
        'Semua'       => 'Semua',
    ];

    /** @var array<string, string> */
    private array $satuanOptions = [
        'Hari'    => 'Per Hari',
        'Bulan'   => 'Per Bulan',
        'Periode' => 'Per Periode',
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

    private function normalizeSatuan(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        $trimmed = trim($value);
        $key     = strtolower($trimmed);

        return $this->satuanNormalizationMap[$key] ?? $trimmed;
    }

    private function collectFormData(): array
    {
        $data = [
            'nama_komponen' => trim((string) $this->request->getPost('nama_komponen')),
            'kategori'      => (string) $this->request->getPost('kategori'),
            'jabatan'       => (string) $this->request->getPost('jabatan'),
            'nominal'       => (float) $this->request->getPost('nominal'),
            'satuan'        => $this->normalizeSatuan((string) $this->request->getPost('satuan')),
        ];

        return $data;
    }

    private function komponenValidationRules(): array
    {
        return [
            'nama_komponen' => 'required|string|min_length[3]|max_length[150]',
            'kategori'      => 'required|in_list[' . implode(',', array_keys($this->kategoriOptions)) . ']',
            'jabatan'       => 'required|in_list[' . implode(',', array_keys($this->jabatanOptions)) . ']',
            'nominal'       => 'required|decimal',
            'satuan'        => 'required|in_list[' . implode(',', array_keys($this->satuanOptions)) . ']',
        ];
    }

    public function __construct()
    {
        $this->model = new KomponenGajiModel();
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

    public function index()
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $komponenList = $this->model
            ->orderBy('id_komponen_gaji', 'ASC')
            ->findAll();
        $summary = [
            'total'            => count($komponenList),
            'total_gaji'       => 0,
            'total_tunjangan'  => 0,
            'total_nominal'    => 0.0,
            'last_created'     => null,
        ];

        foreach ($komponenList as &$row) {
            $kategori = trim((string) ($row['kategori'] ?? ''));
            $nominal  = isset($row['nominal'])
                ? (float) $row['nominal']
                : (float) ($row['nominal_default'] ?? 0.0);

            $row['satuan'] = $this->normalizeSatuan($row['satuan'] ?? '');

            if (stripos($kategori, 'gaji') !== false) {
                $summary['total_gaji']++;
            } elseif ($kategori !== '') {
                $summary['total_tunjangan']++;
            }

            $summary['total_nominal'] += $nominal;

            $createdAt = $row['created_at'] ?? ($row['updated_at'] ?? null);

            if (! empty($createdAt)) {
                if ($summary['last_created'] === null || $createdAt > $summary['last_created']) {
                    $summary['last_created'] = $createdAt;
                }
            }
        }
        unset($row);

        return view('komponen_gaji/index', [
            'komponen'        => $komponenList,
            'kategoriOptions' => $this->kategoriOptions,
            'satuanOptions'   => $this->satuanOptions,
            'summary'         => $summary,
        ]);
    }

    public function create()
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        helper('form');

        return view('komponen_gaji/create', [
            'kategoriOptions' => $this->kategoriOptions,
            'jabatanOptions'  => $this->jabatanOptions,
            'satuanOptions'   => $this->satuanOptions,
        ]);
    }

    public function store(): RedirectResponse
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        helper(['form']);

        if (! $this->validate($this->komponenValidationRules())) {
            return redirect()->back()
                ->with('error', 'Validasi gagal. Mohon cek kembali input Anda.')
                ->withInput();
        }

        $data = $this->collectFormData();

        try {
            $this->model->insert($data, false);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal menyimpan komponen gaji: {message}', ['message' => $e->getMessage()]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
        }

        return redirect()->to(base_url('admin/komponen-gaji'))
            ->with('success', 'Komponen gaji berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        helper('form');

        $komponen = $this->model->find($id);

        if (! $komponen) {
            return redirect()->to(base_url('admin/komponen-gaji'))
                ->with('error', 'Data komponen gaji tidak ditemukan.');
        }

        $komponen = (array) $komponen;
        $komponen['satuan'] = $this->normalizeSatuan($komponen['satuan'] ?? '');

        return view('komponen_gaji/edit', [
            'komponen'        => $komponen,
            'kategoriOptions' => $this->kategoriOptions,
            'jabatanOptions'  => $this->jabatanOptions,
            'satuanOptions'   => $this->satuanOptions,
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        helper(['form']);

        if (! $this->model->find($id)) {
            return redirect()->to(base_url('admin/komponen-gaji'))
                ->with('error', 'Data komponen gaji tidak ditemukan.');
        }

        if (! $this->validate($this->komponenValidationRules())) {
            return redirect()->back()
                ->with('error', 'Validasi gagal. Mohon cek kembali input Anda.')
                ->withInput();
        }

        $data = $this->collectFormData();

        try {
            $this->model->update($id, $data);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal memperbarui komponen gaji {id}: {message}', [
                'id'      => $id,
                'message' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui data.')->withInput();
        }

        return redirect()->to(base_url('admin/komponen-gaji'))
            ->with('success', 'Komponen gaji berhasil diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        if (! $this->model->find($id)) {
            return redirect()->to(base_url('admin/komponen-gaji'))
                ->with('error', 'Data komponen gaji tidak ditemukan.');
        }

        $db = db_connect();

        $isReferenced = $db->table('penggajian')
            ->where('id_komponen_gaji', $id)
            ->countAllResults();

        if ($isReferenced > 0) {
            return redirect()->to(base_url('admin/komponen-gaji'))
                ->with('error', 'Komponen gaji sedang digunakan dalam data penggajian dan tidak dapat dihapus.');
        }

        try {
            $builder = $db->table('komponen_gaji');
            $builder->where('id_komponen_gaji', $id);
            $builder->delete();

            if ($db->affectedRows() === 0) {
                throw new \RuntimeException('Delete query tidak berhasil dijalankan.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'Gagal menghapus komponen gaji {id}: {message}', [
                'id'      => $id,
                'message' => $e->getMessage(),
            ]);

            return redirect()->to(base_url('admin/komponen-gaji'))
                ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }

        return redirect()->to(base_url('admin/komponen-gaji'))
            ->with('success', 'Komponen gaji berhasil dihapus.');
    }
}
