# Aplikasi Penghitungan & Transparansi Gaji DPR – UTS

Nama: Rizky Satria Gunawan  
NIM: 241511089  
Kelas: 2C-D3

Repositori ini berisi implementasi aplikasi web sederhana untuk penghitungan & transparansi gaji DPR menggunakan CodeIgniter 4 (PHP) dan PostgreSQL. Fokus UTS ini adalah fitur Autentikasi (Login/Logout) dan Manajemen Data Anggota (Tambah Data – Admin Only).

## Teknologi
- PHP 8.1+
- CodeIgniter 4 (v4.6.x)
- PostgreSQL
- Composer (manajemen dependency)
- Bootstrap 5 (UI)

## Fitur Utama
1) Login & Logout
- Halaman Login: `GET /login`
- Proses Login: `POST /login`
- Logout: `GET /logout`
- Validasi server-side, verifikasi password dengan `password_verify()`, penyimpanan session `id_pengguna`, `username`, `role`, `isLoggedIn`, dan redirect ke dashboard admin.

2) Dashboard Admin (proteksi login)
- `GET /admin` – menampilkan sapaan user dan tombol logout.

3) Anggota – Admin Only
- Daftar Anggota: `GET /admin/anggota`
- Form Tambah Anggota: `GET /admin/anggota/create`
- Simpan Anggota: `POST /admin/anggota/store`
- Validasi server-side: `nama_depan` & `nama_belakang` wajib, `jumlah_anak` >= 0, pilihan `jabatan` serta `status_pernikahan` harus sesuai daftar yang diizinkan.

## Struktur Berkas (intisari)
- `app/Controllers/Auth.php` – Login/Logout
- `app/Controllers/Admin.php` – Dashboard (proteksi login)
- `app/Controllers/Anggota.php` – Daftar/Tambah Anggota (admin only)
- `app/Models/PenggunaModel.php` – Tabel `pengguna`
- `app/Models/AnggotaModel.php` – Tabel `anggota`
- `app/Views/auth/login.php` – Halaman login (Bootstrap 5)
- `app/Views/admin/dashboard.php` – Dashboard sederhana
- `app/Views/anggota/index.php` – Daftar anggota
- `app/Views/anggota/create.php` – Form tambah anggota
- `app/Config/Routes.php` – Rute aplikasi

## Persiapan Lingkungan
1) Instal dependency PHP via Composer
```powershell
# Jalankan di folder proyek
composer install
```

2) Konfigurasi environment
- Duplikasi file `env` menjadi `.env`, lalu aktifkan & sesuaikan variabel berikut:
```
# .env
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost:8080/'

database.default.hostname = 127.0.0.1
database.default.database = nama_database_anda
database.default.username = user_db_anda
database.default.password = password_db_anda
database.default.DBDriver = Postgre

# Opsional: pastikan session path bisa ditulis
session.savePath = writable/session
```

3) Siapkan database
- Import skema SQL yang sudah disediakan di tugas ini (file .sql yang memuat tabel dan kolom, termasuk `jumlah_anak`).

4) Buat user admin (contoh)
```powershell
# Buat hash password (contoh: Admin#123)
php -r "echo password_hash('Admin#123', PASSWORD_DEFAULT), PHP_EOL;"
```
```sql
-- Simpan ke tabel pengguna (samakan nama kolom dengan skema Anda)
INSERT INTO pengguna (username, password, role, nama_lengkap, email, jumlah_anak)
VALUES ('admin', '<HASIL_HASH_DI_ATAS>', 'admin', 'Admin', 'admin@example.com', 0);
```

## Menjalankan Aplikasi
```powershell
# Menjalankan server pengembangan CodeIgniter
php spark serve
# Akses di browser
# http://localhost:8080
```

## Rute Singkat
- Home: `GET /`
- Login: `GET /login`, `POST /login`
- Logout: `GET /logout`
- Admin Dashboard: `GET /admin` (butuh login)
- Anggota (Admin Only):
	- `GET /admin/anggota`
	- `GET /admin/anggota/create`
	- `POST /admin/anggota/store`

## Validasi & Aturan Field (Tambah Anggota)
- `nama_depan`: required
- `nama_belakang`: required
- `gelar_depan`: opsional
- `gelar_belakang`: opsional
- `jabatan`: required, pilih salah satu dari `['Ketua','Wakil Ketua','Anggota']`
- `status_pernikahan`: required, pilih salah satu opsi yang telah didefinisikan di controller (samakan dengan ENUM DB Anda)
- `jumlah_anak`: required, integer, minimal 0

## Troubleshooting (Saat Pengembangan)
- Whoops!/500 saat akses halaman:
	1. Pastikan dependency lengkap: `composer install`
	2. Periksa log terbaru di `writable/logs/log-YYYY-MM-DD.log`
	3. Pastikan folder `writable/session` ada dan bisa ditulis. Jika perlu, set `session.savePath` ke path yang pasti bisa ditulis.
	4. Jika Debug Toolbar bermasalah di lokal, nonaktifkan kolektor atau set `CI_ENVIRONMENT=production` untuk sementara.

- Tidak bisa login:
	1. Cek data user di tabel `pengguna`
	2. Pastikan kolom `password` berisi hash dari `password_hash()`
	3. Cek `role` berisi `admin` untuk akses fitur admin

## Catatan
- Opsi `status_pernikahan` di form disesuaikan secara statis di `Anggota.php`. Samakan dengan ENUM pada database Anda agar validasi konsisten.
- Fitur tambahan (edit/hapus anggota, role-based filter global, dsb.) dapat ditambahkan kemudian.

## Lisensi
Kode ini mengikuti lisensi dari template CodeIgniter AppStarter (MIT) dan dependensi terkait.

