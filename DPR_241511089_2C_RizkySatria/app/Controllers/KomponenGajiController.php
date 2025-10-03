<?php

namespace App\Controllers;

use App\Models\KomponenGajiModel;
use CodeIgniter\HTTP\RedirectResponse;

class KomponenGajiController extends BaseController
{
    private KomponenGajiModel $model;

    /** @var array<string, string> */
    private array $kategoriOptions = [
        'gaji'      => 'Komponen Gaji',
        'tunjangan' => 'Tunjangan',
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

        $komponenList = $this->model->findAll();

        return view('komponen_gaji/index', [
            'komponen'        => $komponenList,
            'kategoriOptions' => $this->kategoriOptions,
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
        ]);
    }

    public function store(): RedirectResponse
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        helper(['form']);

        $validationRules = [
            'nama'            => 'required|string|min_length[3]|max_length[100]',
            'kategori'        => 'required|in_list[' . implode(',', array_keys($this->kategoriOptions)) . ']',
            'nominal_default' => 'required|decimal',
            'deskripsi'       => 'permit_empty|string',
        ];

        if (! $this->validate($validationRules)) {
            return redirect()->back()
                ->with('error', 'Validasi gagal. Mohon cek kembali input Anda.')
                ->withInput();
        }

        $data = [
            'nama'            => trim((string) $this->request->getPost('nama')),
            'kategori'        => (string) $this->request->getPost('kategori'),
            'nominal_default' => (float) $this->request->getPost('nominal_default'),
            'deskripsi'       => trim((string) $this->request->getPost('deskripsi')) ?: null,
        ];

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
