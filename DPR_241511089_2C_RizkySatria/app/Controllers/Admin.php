<?php

namespace App\Controllers;

use App\Models\AnggotaModel;
use App\Models\KomponenGajiModel;
use App\Models\PenggajianModel;

class Admin extends BaseController
{
    public function index()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu.');
        }

        $anggotaModel    = new AnggotaModel();
        $komponenModel   = new KomponenGajiModel();
        $penggajianModel = new PenggajianModel();

        $recentAnggota = $anggotaModel
            ->findAll(5);

        $totalAnggota = $anggotaModel->countAll();
        $totalKomponen = $komponenModel->countAll();
        $totalPenggajian = $penggajianModel->countAllAssignments();
        $totalAnggotaDenganPenggajian = $penggajianModel->countDistinctAnggota();

        $data = [
            'title' => 'Dashboard Admin',
            'username' => session()->get('username'),
            'role' => session()->get('role'),
            'totalAnggota' => $totalAnggota,
            'totalKomponen' => $totalKomponen,
            'totalPenggajian' => $totalPenggajian,
            'totalAnggotaDenganPenggajian' => $totalAnggotaDenganPenggajian,
            'recentAnggota' => $recentAnggota,
        ];

        return view('admin/dashboard', $data);
    }

    // Retain dashboard method for backward compatibility if any links still use it
    public function dashboard()
    {
        return $this->index();
    }
}
