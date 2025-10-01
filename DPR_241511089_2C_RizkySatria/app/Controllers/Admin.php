<?php

namespace App\Controllers;

class Admin extends BaseController
{
    public function index()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu.');
        }

        $data = [
            'title' => 'Dashboard Admin',
            'username' => session()->get('username'),
            'role' => session()->get('role'),
        ];

        return view('admin/dashboard', $data);
    }
}
