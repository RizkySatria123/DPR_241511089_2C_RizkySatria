<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Penggajian Anggota</title>
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
        <div>
          <h1 class="h4 mb-1">Ringkasan Take Home Pay Anggota</h1>
          <p class="text-muted mb-0">Pantau total komponen gaji dan take home pay setiap anggota DPR.</p>
        </div>
        <div class="d-flex gap-2">
          <a href="<?= base_url('admin/anggota') ?>" class="btn btn-outline-secondary">Kelola Anggota</a>
          <a href="<?= base_url('admin/komponen-gaji') ?>" class="btn btn-outline-success">Kelola Komponen Gaji</a>
        </div>
      </div>

      <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
      <?php endif; ?>

      <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <p class="text-muted mb-1">Total Anggota</p>
              <p class="h3 fw-bold mb-0"><?= esc(number_format($summary['totalAnggota'] ?? 0)) ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <p class="text-muted mb-1">Anggota Memiliki Komponen</p>
              <p class="h3 fw-bold mb-0"><?= esc(number_format($summary['anggotaDenganKomponen'] ?? 0)) ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <p class="text-muted mb-1">Total Komponen Aktif</p>
              <p class="h3 fw-bold mb-0"><?= esc(number_format($summary['totalKomponen'] ?? 0)) ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <p class="text-muted mb-1">Total Take Home Pay</p>
              <p class="h5 fw-bold mb-1">Rp <?= number_format((float) ($summary['totalNominal'] ?? 0), 2, ',', '.') ?></p>
              <small class="text-muted">Rata-rata: Rp <?= number_format((float) ($summary['averageNominal'] ?? 0), 2, ',', '.') ?></small>
            </div>
          </div>
        </div>
      </div>

      <?php if (! empty($summary['tertinggiNama'])): ?>
        <div class="alert alert-info mb-4">
          <strong><?= esc($summary['tertinggiNama']) ?></strong> memiliki take home pay tertinggi sebesar
          <strong>Rp <?= number_format((float) ($summary['tertinggiNominal'] ?? 0), 2, ',', '.') ?></strong>.
        </div>
      <?php endif; ?>

      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
          <h2 class="h6 mb-0">Detail Take Home Pay per Anggota</h2>
          <small class="text-muted">Gunakan tombol "Kelola" untuk menambah atau menghapus komponen.</small>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th scope="col">ID</th>
                  <th scope="col">Nama Lengkap</th>
                  <th scope="col">Jabatan</th>
                  <th scope="col">Status</th>
                  <th scope="col">Jumlah Anak</th>
                  <th scope="col" class="text-center"># Komponen</th>
                  <th scope="col">Take Home Pay</th>
                  <th scope="col" class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php if (! empty($rows)): ?>
                  <?php foreach ($rows as $row): ?>
                    <?php
                      $anggotaId   = (int) ($row['id_anggota'] ?? 0);
                      $status      = trim((string) ($row['status_pernikahan'] ?? '-'));
                      $jumlahAnak  = (int) ($row['jumlah_anak'] ?? 0);
                      $totalKompon = (int) ($row['total_komponen'] ?? 0);
                      $takeHome    = (float) ($row['total_nominal'] ?? 0);
                    ?>
                    <tr>
                      <td><?= esc($anggotaId) ?></td>
                      <td><?= esc($row['display_name'] ?? '-') ?></td>
                      <td><?= esc($row['jabatan'] ?? '-') ?></td>
                      <td><?= esc($status !== '' ? $status : '-') ?></td>
                      <td><?= esc($jumlahAnak) ?></td>
                      <td class="text-center">
                        <span class="badge bg-<?= $totalKompon > 0 ? 'primary' : 'secondary' ?>">
                          <?= esc($totalKompon) ?>
                        </span>
                      </td>
                      <td>Rp <?= number_format($takeHome, 2, ',', '.') ?></td>
                      <td class="text-center">
                        <a href="<?= base_url('admin/penggajian/anggota/' . $anggotaId) ?>" class="btn btn-sm btn-outline-primary">Kelola</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="8" class="text-center text-muted py-4">Belum ada data anggota.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  </body>
</html>
