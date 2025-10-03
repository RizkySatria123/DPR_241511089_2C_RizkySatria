<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Komponen Gaji DPR</title>
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
        <h1 class="h4 mb-0">Daftar Komponen Gaji & Tunjangan</h1>
        <a href="<?= base_url('admin/komponen-gaji/create') ?>" class="btn btn-primary">Tambah Komponen</a>
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
              <th scope="col">Nama Komponen</th>
              <th scope="col">Kategori</th>
              <th scope="col">Nominal Default</th>
              <th scope="col">Deskripsi</th>
              <th scope="col">Dibuat</th>
            </tr>
          </thead>
          <tbody>
            <?php if (! empty($komponen)): ?>
              <?php foreach ($komponen as $row): ?>
                <tr>
                  <td><?= esc($row['id_komponen'] ?? '-') ?></td>
                  <td><?= esc($row['nama'] ?? '-') ?></td>
                  <td><?= esc($kategoriOptions[$row['kategori']] ?? ucfirst((string) $row['kategori'])) ?></td>
                  <td>Rp <?= number_format((float) ($row['nominal_default'] ?? 0), 2, ',', '.') ?></td>
                  <td><?= esc($row['deskripsi'] ?? '-') ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="text-center text-muted">Belum ada komponen gaji.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  </body>
</html>
