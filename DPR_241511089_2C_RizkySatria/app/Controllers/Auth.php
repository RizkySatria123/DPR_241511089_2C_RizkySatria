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
        helper(['form', 'url', 'security']);

        // Only allow POST for login
        if (! $this->request->is('post')) {
            return redirect()->to(base_url('login'));
        }

        // Rate limit: max 5 attempts per minute per IP
        $throttler = service('throttler');
        $key = 'login-' . $this->request->getIPAddress();
        if (! $throttler->check($key, 5, MINUTE)) {
            return redirect()->back()->with('error', 'Terlalu banyak percobaan. Coba lagi dalam satu menit.')->withInput();
        }

        $validationRules = [
            'username' => 'required|min_length[3]|max_length[50]|alpha_numeric_punct',
            'password' => 'required|min_length[8]|max_length[255]',
        ];

        if (! $this->validate($validationRules)) {
            return redirect()->back()->with('error', 'Input tidak valid.')->withInput();
        }

        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        $model = new PenggunaModel();

        try {
            // Query via Model uses Query Builder with bindings (mitigates SQL Injection)
            $user = $model->where('username', $username)->first();
        } catch (\Throwable $e) {
            log_message('error', 'DB error saat login: {message}', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan. Coba lagi nanti.')->withInput();
        }

        // Do not reveal which field is wrong
        if (! $user || ! password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Kredensial tidak valid.')->withInput();
        }

        // Rehash if needed
        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            try {
                $model->update($user['id_pengguna'] ?? $user['id'] ?? null, [
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                ]);
            } catch (\Throwable $e) {
                log_message('warning', 'Gagal rehash password user {id}: {message}', [
                    'id' => $user['id_pengguna'] ?? $user['id'] ?? 'unknown',
                    'message' => $e->getMessage(),
                ]);
            }
        }

        // Regenerate session ID to prevent session fixation, destroy old session
        session()->regenerate(true);

        // Save required fields to session (minimal data)
        session()->set([
            'id_pengguna' => $user['id_pengguna'] ?? $user['id'] ?? null,
            'username'    => (string) $user['username'],
            'role'        => $user['role'] ?? 'user',
            'isLoggedIn'  => true,
        ]);

        // Regenerate CSRF token after privilege change
        if (function_exists('csrf_regenerate')) {
            csrf_regenerate();
        }

        // Redirect to intended page or admin
        $redirectTo = session('redirect_url') ?? base_url('admin');
        session()->remove('redirect_url');

        return redirect()->to($redirectTo);
    }

    /**
     * Logout and destroy the session.
     */
    public function logout(): RedirectResponse
    {
        // Enforce POST for logout to avoid CSRF via GET
        if (! $this->request->is('post')) {
            return redirect()->to(base_url('login'));
        }

        session()->destroy();

        // Proactively expire session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires'  => time() - 42000,
                'path'     => $params['path'] ?? '/',
                'domain'   => $params['domain'] ?? '',
                'secure'   => (bool) ($params['secure'] ?? false),
                'httponly' => true,
                'samesite' => $params['samesite'] ?? 'Lax',
            ]);
        }

        return redirect()->to(base_url('login'))->with('error', 'Anda telah keluar.');
    }
}
