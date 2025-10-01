<?php

namespace App\Controllers;

use App\Models\PenggunaModel;
use CodeIgniter\HTTP\RedirectResponse;

class Auth extends BaseController
{
    /**
     * Show the login form.
     */
    public function index()
    {
        helper(['form', 'url']);

        if (session()->get('isLoggedIn')) {
            return redirect()->to(base_url('admin'));
        }

        return view('auth/login');
    }

    /**
     * Handle login POST
     */
    public function login(): RedirectResponse
    {
        helper(['form', 'url']);

        $validationRules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (! $this->validate($validationRules)) {
            return redirect()->back()->with('error', 'Username dan password wajib diisi.')->withInput();
        }

        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        $model = new PenggunaModel();

        // Find user by username
        $user = $model->where('username', $username)->first();

        if (! $user) {
            return redirect()->back()->with('error', 'Username atau password salah.')->withInput();
        }

        // Verify password with hash stored in DB
        if (! password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Username atau password salah.')->withInput();
        }

        // Regenerate session ID to prevent session fixation
        session()->regenerate();

        // Save required fields to session
        session()->set([
            'id_pengguna' => $user['id_pengguna'] ?? $user['id'] ?? null,
            'username'    => $user['username'],
            'role'        => $user['role'] ?? 'user',
            'isLoggedIn'  => true,
        ]);

        // Redirect to admin dashboard
        return redirect()->to(base_url('admin'));
    }

    /**
     * Logout and destroy the session.
     */
    public function logout(): RedirectResponse
    {
        session()->destroy();
        return redirect()->to(base_url('login'));
    }
}
