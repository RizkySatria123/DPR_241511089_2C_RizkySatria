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
          <a class="btn btn-outline-light" href="<?= base_url('logout') ?>">Logout</a>
        </div>
      </div>
    </nav>

    <div class="container py-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Daftar Anggota</h1>
        <a href="<?= base_url('admin/anggota/create') ?>" class="btn btn-primary">Tambah Anggota</a>
      </div>

      <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
      <?php endif; ?>

      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
            <tr>
              <th>#</th>
              <th>Nama</th>
              <th>Gelar</th>
              <th>Jabatan</th>
              <th>Status Pernikahan</th>
              <th>Jumlah Anak</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($anggota)): ?>
              <?php foreach ($anggota as $i => $row): ?>
                <tr>
                  <td><?= $i + 1 ?></td>
                  <td><?= esc($row['nama_depan'] . ' ' . $row['nama_belakang']) ?></td>
                  <td><?= esc(trim(($row['gelar_depan'] ?? '') . ' ' . ($row['gelar_belakang'] ?? ''))) ?></td>
                  <td><?= esc($row['jabatan'] ?? '-') ?></td>
                  <td><?= esc($row['status_pernikahan'] ?? '-') ?></td>
                  <td><?= esc($row['jumlah_anak'] ?? 0) ?></td>
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
