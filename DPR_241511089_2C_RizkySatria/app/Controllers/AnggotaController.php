<?php

namespace App\Controllers;

use App\Models\AnggotaModel;
use CodeIgniter\HTTP\RedirectResponse;

class AnggotaController extends BaseController
{
    private AnggotaModel $model;

    private array $jabatanOptions = ['Ketua', 'Wakil Ketua', 'Anggota'];
    private array $statusOptions = ['Belum Menikah', 'Menikah', 'Cerai'];

    public function __construct()
    {
        $this->model = new AnggotaModel();
    }

    /**
     * Ensure the user is logged in as admin.
     */
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

    /**
     * Show anggota listing.
     */
    public function index()
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $anggota = $this->model->findAll();

        return view('anggota/index', [
            'anggota'        => $anggota,
            'jabatanOptions' => $this->jabatanOptions,
            'statusOptions'  => $this->statusOptions,
        ]);
    }

    /**
     * Display the form to create a new anggota entry.
     */
    public function create()
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        helper('form');

        return view('anggota/create', [
            'jabatanOptions' => $this->jabatanOptions,
            'statusOptions'  => $this->statusOptions,
        ]);
    }

    /**
     * Persist anggota data.
     */
    public function store(): RedirectResponse
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        helper(['form']);

        $rules = [
            'nama_depan'        => 'required|string|min_length[2]|max_length[100]',
            'nama_belakang'     => 'required|string|min_length[2]|max_length[100]',
            'gelar_depan'       => 'permit_empty|string|max_length[50]',
            'gelar_belakang'    => 'permit_empty|string|max_length[50]',
            'jabatan'           => 'required|in_list[' . implode(',', $this->jabatanOptions) . ']',
            'status_pernikahan' => 'required|in_list[' . implode(',', $this->statusOptions) . ']',
            'jumlah_anak'       => 'required|integer|greater_than_equal_to[0]|less_than[50]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', 'Validasi gagal. Mohon cek input Anda.')->withInput();
        }

        $data = [
            'nama_depan'        => trim((string) $this->request->getPost('nama_depan')),
            'nama_belakang'     => trim((string) $this->request->getPost('nama_belakang')),
            'gelar_depan'       => trim((string) $this->request->getPost('gelar_depan')),
            'gelar_belakang'    => trim((string) $this->request->getPost('gelar_belakang')),
            'jabatan'           => (string) $this->request->getPost('jabatan'),
            'status_pernikahan' => (string) $this->request->getPost('status_pernikahan'),
            'jumlah_anak'       => (int) $this->request->getPost('jumlah_anak'),
        ];

        try {
            $this->model->insert($data, false);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal menyimpan data anggota: {message}', ['message' => $e->getMessage()]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
        }

        return redirect()->to(base_url('admin/anggota'))->with('success', 'Anggota baru berhasil ditambahkan.');
    }
}
