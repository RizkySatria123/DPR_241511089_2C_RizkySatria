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
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h1 class="h4 mb-0">Daftar Komponen Gaji &amp; Tunjangan</h1>
        <div class="d-flex gap-2">
          <a href="<?= base_url('admin') ?>" class="btn btn-outline-secondary">Kembali ke Dashboard</a>
          <a href="<?= base_url('admin/komponen-gaji/create') ?>" class="btn btn-primary">Tambah Komponen</a>
        </div>
      </div>

      <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
      <?php endif; ?>

      <?php if (! empty($summary)): ?>
        <div class="row g-3 mb-3">
          <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <p class="text-muted mb-1">Total Komponen</p>
                <p class="h3 fw-bold mb-0"><?= esc($summary['total'] ?? 0) ?></p>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <p class="text-muted mb-1">Komponen Gaji</p>
                <p class="h3 fw-bold mb-0"><?= esc($summary['total_gaji'] ?? 0) ?></p>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <p class="text-muted mb-1">Komponen Tunjangan</p>
                <p class="h3 fw-bold mb-0"><?= esc($summary['total_tunjangan'] ?? 0) ?></p>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <p class="text-muted mb-1">Total Nominal Default</p>
                <p class="h5 fw-bold mb-0">Rp <?= number_format((float) ($summary['total_nominal'] ?? 0), 2, ',', '.') ?></p>
              </div>
            </div>
          </div>
        </div>
        <?php if (! empty($summary['last_created'])): ?>
          <div class="alert alert-info py-2 mb-4">
            Data terakhir ditambahkan pada <strong><?= esc(date('d M Y H:i', strtotime($summary['last_created']))) ?></strong>.
          </div>
        <?php endif; ?>
      <?php endif; ?>

      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead>
            <tr>
              <th scope="col">ID</th>
              <th scope="col">Nama Komponen</th>
              <th scope="col">Kategori</th>
              <th scope="col">Jabatan</th>
              <th scope="col">Nominal</th>
              <th scope="col">Satuan</th>
              <th scope="col">Deskripsi</th>
              <th scope="col">Dibuat</th>
              <th scope="col" class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (! empty($komponen)): ?>
              <?php foreach ($komponen as $row): ?>
                <?php
                  $rowId      = $row['id_komponen_gaji'] ?? $row['id_komponen'] ?? '-';
                  $nama       = $row['nama_komponen'] ?? $row['nama'] ?? '-';
                  $kategori   = $row['kategori'] ?? '-';
                  $jabatan    = $row['jabatan'] ?? '-';
                  $nominal    = $row['nominal'] ?? $row['nominal_default'] ?? 0;
                  $satuan     = $row['satuan'] ?? '-';
                  $deskripsi  = $row['deskripsi'] ?? $row['keterangan'] ?? '-';
                  $createdRaw = $row['created_at'] ?? ($row['updated_at'] ?? null);
                ?>
                <tr>
                  <td><?= esc($rowId) ?></td>
                  <td><?= esc($nama) ?></td>
                  <td><?= esc($kategoriOptions[$kategori] ?? $kategori) ?></td>
                  <td><?= esc($jabatan) ?></td>
                  <td>Rp <?= number_format((float) $nominal, 2, ',', '.') ?></td>
                  <td><?= esc($satuan) ?></td>
                  <td><?= esc($deskripsi !== '' ? $deskripsi : '-') ?></td>
                  <td>
                    <?php if (! empty($createdRaw)): ?>
                      <?= esc(date('d M Y H:i', strtotime($createdRaw))) ?>
                    <?php else: ?>
                      -
                    <?php endif; ?>
                  </td>
                  <td class="text-center">
                    <a href="<?= base_url('admin/komponen-gaji/edit/' . $rowId) ?>" class="btn btn-sm btn-outline-primary">Ubah</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="9" class="text-center text-muted">Belum ada komponen gaji.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  </body>
</html>
