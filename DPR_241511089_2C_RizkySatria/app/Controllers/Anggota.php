<?php

namespace App\Controllers;

use App\Models\AnggotaModel;

class Anggota extends BaseController
{
    private array $jabatanOptions = ['Ketua', 'Wakil Ketua', 'Anggota'];
    private array $statusOptions = ['Belum Menikah', 'Menikah', 'Cerai'];

    private function ensureAdmin()
    {
        $session = session();
        if (! $session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Anda tidak memiliki akses.');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $model   = new AnggotaModel();
        $anggota = $model->orderBy('id', 'DESC')->findAll();

        return view('anggota/index', [
            'anggota'        => $anggota,
            'jabatanOptions' => $this->jabatanOptions,
            'statusOptions'  => $this->statusOptions,
        ]);
    }

    public function create()
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        return view('anggota/create', [
            'jabatanOptions' => $this->jabatanOptions,
            'statusOptions'  => $this->statusOptions,
        ]);
    }

    public function store()
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        helper(['form']);

        $rules = [
            'nama_depan'       => 'required',
            'nama_belakang'    => 'required',
            'gelar_depan'      => 'permit_empty|string',
            'gelar_belakang'   => 'permit_empty|string',
            'jabatan'          => 'required|in_list[' . implode(',', $this->jabatanOptions) . ']',
            'status_pernikahan'=> 'required|in_list[' . implode(',', $this->statusOptions) . ']',
            'jumlah_anak'      => 'required|integer|greater_than_equal_to[0]',
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

        $model = new AnggotaModel();
        $model->insert($data);

        return redirect()->to(base_url('admin/anggota'))
            ->with('success', 'Anggota baru berhasil ditambahkan.');
    }
}
