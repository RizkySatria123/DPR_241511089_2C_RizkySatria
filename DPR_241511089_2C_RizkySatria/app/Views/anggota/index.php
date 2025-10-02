<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Anggota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container">
        <a class="navbar-brand" href="<?= base_url('admin') ?>">Gaji DPR</a>
        <div class="d-flex ms-auto">
          <form action="<?= base_url('logout') ?>" method="post" class="d-inline">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-light">Logout</button>
          </form>
        </div>
      </div>
    </nav>

    <div class="container py-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Daftar Anggota DPR</h1>
        <a href="<?= base_url('admin/anggota/create') ?>" class="btn btn-primary">Tambah Anggota Baru</a>
      </div>

      <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
      <?php endif; ?>

      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead>
            <tr>
              <th scope="col">ID</th>
              <th scope="col">Nama Lengkap</th>
              <th scope="col">Jabatan</th>
              <th scope="col">Status Pernikahan</th>
              <th scope="col">Jumlah Anak</th>
              <th scope="col" class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($anggota)): ?>
              <?php foreach ($anggota as $i => $row): ?>
                <tr>
                  <td><?= esc($row['id'] ?? ($i + 1)) ?></td>
                  <td>
                    <?php
                      $gelarDepan   = trim((string) ($row['gelar_depan'] ?? ''));
                      $gelarBelakang = trim((string) ($row['gelar_belakang'] ?? ''));
                      $namaLengkap  = trim($row['nama_depan'] . ' ' . $row['nama_belakang']);
                      $namaDenganGelar = trim(($gelarDepan ? $gelarDepan . ' ' : '') . $namaLengkap . ($gelarBelakang ? ', ' . $gelarBelakang : ''));
                    ?>
                    <?= esc($namaDenganGelar !== '' ? $namaDenganGelar : $namaLengkap) ?>
                  </td>
                  <td><?= esc($row['jabatan'] ?? '-') ?></td>
                  <td><?= esc($row['status_pernikahan'] ?? '-') ?></td>
                  <td><?= esc($row['jumlah_anak'] ?? 0) ?></td>
                  <td class="text-center">
                    <div class="btn-group btn-group-sm" role="group" aria-label="Aksi anggota">
                      <button type="button" class="btn btn-outline-secondary" disabled title="Fitur edit segera hadir">Edit</button>
                      <button type="button" class="btn btn-outline-danger" disabled title="Fitur hapus segera hadir">Hapus</button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="text-center text-muted">Belum ada data.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  </body>
</html>
