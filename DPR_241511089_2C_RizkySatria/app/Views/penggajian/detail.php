<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Penggajian Anggota</title>
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
          <h1 class="h4 mb-1">Detail Penggajian Anggota</h1>
          <p class="text-muted mb-0">Ringkasan komponen gaji untuk <?= esc($anggotaDisplayName) ?>.</p>
        </div>
        <div class="d-flex gap-2">
          <a href="<?= base_url('admin/penggajian') ?>" class="btn btn-outline-secondary">Kembali ke Ringkasan</a>
          <a href="<?= base_url('admin/penggajian/anggota/' . ($anggota['id_anggota'] ?? $anggota['id'] ?? 0)) ?>" class="btn btn-primary">Kelola Komponen</a>
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6 col-sm-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <p class="text-muted mb-1">Total Take Home Pay</p>
              <p class="h4 fw-bold mb-0">Rp <?= number_format((float) ($totalNominal ?? 0), 2, ',', '.') ?></p>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <p class="text-muted mb-1">Jumlah Komponen</p>
              <p class="h4 fw-bold mb-0"><?= esc($totalKomponen ?? 0) ?></p>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <p class="text-muted mb-1">Jabatan</p>
              <p class="h6 fw-semibold mb-0"><?= esc($anggota['jabatan'] ?? '-') ?></p>
              <small class="text-muted">Status: <?= esc($anggota['status_pernikahan'] ?? '-') ?></small>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <p class="text-muted mb-1">Jumlah Anak</p>
              <p class="h4 fw-bold mb-0"><?= esc($anggota['jumlah_anak'] ?? 0) ?></p>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 mb-4">
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
              <h2 class="h6 mb-0">Total per Kategori</h2>
            </div>
            <div class="card-body">
              <?php if (! empty($totalsByKategori)): ?>
                <ul class="list-group list-group-flush">
                  <?php foreach ($totalsByKategori as $kategori => $nominal): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      <span><?= esc($kategori) ?></span>
                      <strong>Rp <?= number_format($nominal, 2, ',', '.') ?></strong>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php else: ?>
                <p class="text-muted mb-0">Belum ada komponen tercatat.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
              <h2 class="h6 mb-0">Total per Satuan Pembayaran</h2>
            </div>
            <div class="card-body">
              <?php if (! empty($totalsBySatuan)): ?>
                <ul class="list-group list-group-flush">
                  <?php foreach ($totalsBySatuan as $satuan => $nominal): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      <span><?= esc($satuan) ?></span>
                      <strong>Rp <?= number_format($nominal, 2, ',', '.') ?></strong>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php else: ?>
                <p class="text-muted mb-0">Belum ada komponen tercatat.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
          <h2 class="h6 mb-0">Rincian Komponen</h2>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th scope="col">Komponen</th>
                  <th scope="col">Kategori</th>
                  <th scope="col">Nominal</th>
                  <th scope="col">Satuan</th>
                </tr>
              </thead>
              <tbody>
                <?php if (! empty($assignments)): ?>
                  <?php foreach ($assignments as $row): ?>
                    <tr>
                      <td><?= esc($row['nama_komponen'] ?? '-') ?></td>
                      <td><?= esc($row['kategori'] ?? '-') ?></td>
                      <td>Rp <?= number_format((float) ($row['nominal'] ?? 0), 2, ',', '.') ?></td>
                      <td><?= esc($satuanOptions[$row['satuan']] ?? $row['satuan'] ?? '-') ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="4" class="text-center text-muted py-4">Belum ada komponen gaji yang tercatat.</td>
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
