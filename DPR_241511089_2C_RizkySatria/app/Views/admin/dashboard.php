<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container">
        <a class="navbar-brand" href="#">Gaji DPR</a>
        <div class="d-flex ms-auto">
          <span class="navbar-text text-white me-3">Halo, <?= esc($username ?? 'Pengguna') ?> (<?= esc($role ?? '-') ?>)</span>
          <form action="<?= base_url('logout') ?>" method="post" class="d-inline">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-light">Logout</button>
          </form>
        </div>
      </div>
    </nav>

    <div class="container py-4">
      <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
      <?php endif; ?>
      <div class="p-5 mb-4 bg-light rounded-3">
        <div class="container-fluid py-5">
          <h1 class="display-6 fw-bold">Dashboard Admin</h1>
          <p class="col-md-8 fs-5">Selamat datang di Aplikasi Penghitungan & Transparansi Gaji DPR.</p>
        </div>
      </div>

      <div class="row g-4 mb-4">
        <div class="col-md-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <h2 class="h6 text-muted">Total Anggota DPR</h2>
              <p class="display-5 fw-bold mb-2"><?= esc($totalAnggota ?? 0) ?></p>
              <a class="btn btn-sm btn-outline-primary" href="<?= base_url('admin/anggota') ?>">Lihat Semua Anggota</a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <h2 class="h6 text-muted">Total Komponen Gaji</h2>
              <p class="display-5 fw-bold mb-2"><?= esc($totalKomponen ?? 0) ?></p>
              <a class="btn btn-sm btn-outline-success" href="<?= base_url('admin/komponen-gaji') ?>">Kelola Komponen</a>
            </div>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <h2 class="h6 mb-0">Data Anggota Terbaru</h2>
          <a href="<?= base_url('admin/anggota/create') ?>" class="btn btn-sm btn-primary">Tambah Anggota</a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th scope="col">ID</th>
                  <th scope="col">Nama Lengkap</th>
                  <th scope="col">Jabatan</th>
                  <th scope="col">Status Pernikahan</th>
                  <th scope="col">Jumlah Anak</th>
                </tr>
              </thead>
              <tbody>
                <?php if (! empty($recentAnggota)): ?>
                  <?php foreach ($recentAnggota as $row): ?>
                    <?php
                      $gelarDepan    = trim((string) ($row['gelar_depan'] ?? ''));
                      $gelarBelakang = trim((string) ($row['gelar_belakang'] ?? ''));
                      $namaInti      = trim((string) ($row['nama_depan'] ?? '') . ' ' . ($row['nama_belakang'] ?? ''));
                      $namaLengkap   = trim(($gelarDepan ? $gelarDepan . ' ' : '') . $namaInti . ($gelarBelakang ? ', ' . $gelarBelakang : ''));
                    ?>
                    <tr>
                      <td><?= esc($row['id'] ?? '-') ?></td>
                      <td><?= esc($namaLengkap !== '' ? $namaLengkap : $namaInti) ?></td>
                      <td><?= esc($row['jabatan'] ?? '-') ?></td>
                      <td><?= esc($row['status_pernikahan'] ?? '-') ?></td>
                      <td><?= esc($row['jumlah_anak'] ?? 0) ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5" class="text-center text-muted py-4">Belum ada data anggota.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="card-footer bg-white text-end">
          <a href="<?= base_url('admin/anggota') ?>" class="btn btn-sm btn-outline-secondary">Lihat Selengkapnya</a>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
