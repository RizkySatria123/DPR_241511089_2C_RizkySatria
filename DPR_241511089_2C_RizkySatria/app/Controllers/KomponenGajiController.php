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
        'Bulan'       => 'Per Bulan',
        'Persidangan' => 'Per Sidang',
        'Sekali'      => 'Sekali Bayar',
        'Hari'        => 'Per Hari',
    ];

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

        foreach ($komponenList as $row) {
            $kategori = trim((string) ($row['kategori'] ?? ''));
            $nominal  = isset($row['nominal'])
                ? (float) $row['nominal']
                : (float) ($row['nominal_default'] ?? 0.0);

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

        return view('komponen_gaji/index', [
            'komponen'        => $komponenList,
            'kategoriOptions' => $this->kategoriOptions,
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

        $validationRules = [
            'nama_komponen' => 'required|string|min_length[3]|max_length[150]',
            'kategori'      => 'required|in_list[' . implode(',', array_keys($this->kategoriOptions)) . ']',
            'jabatan'       => 'required|in_list[' . implode(',', array_keys($this->jabatanOptions)) . ']',
            'nominal'       => 'required|decimal',
            'satuan'        => 'required|string|max_length[30]',
            'deskripsi'     => 'permit_empty|string',
            'keterangan'    => 'permit_empty|string',
        ];

        if (! $this->validate($validationRules)) {
            return redirect()->back()
                ->with('error', 'Validasi gagal. Mohon cek kembali input Anda.')
                ->withInput();
        }

        $data = [
            'nama_komponen' => trim((string) $this->request->getPost('nama_komponen')),
            'kategori'      => (string) $this->request->getPost('kategori'),
            'jabatan'       => (string) $this->request->getPost('jabatan'),
            'nominal'       => (float) $this->request->getPost('nominal'),
            'satuan'        => trim((string) $this->request->getPost('satuan')),
            'deskripsi'     => trim((string) $this->request->getPost('deskripsi')) ?: null,
            'keterangan'    => trim((string) $this->request->getPost('keterangan')) ?: null,
        ];

        if ($data['deskripsi'] === null) {
            unset($data['deskripsi']);
        }

        if ($data['keterangan'] === null) {
            unset($data['keterangan']);
        }

        try {
            $this->model->insert($data, false);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal menyimpan komponen gaji: {message}', ['message' => $e->getMessage()]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
        }

        return redirect()->to(base_url('admin/komponen-gaji'))
            ->with('success', 'Komponen gaji berhasil ditambahkan.');
    }
}
