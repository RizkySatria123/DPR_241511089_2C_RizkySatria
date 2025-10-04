<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Penggajian Anggota</title>
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
          <h1 class="h4 mb-1">Kelola Penggajian Anggota</h1>
          <p class="text-muted mb-0">Menambahkan komponen gaji untuk <?= esc($anggotaDisplayName) ?></p>
        </div>
        <div class="d-flex gap-2">
          <a href="<?= base_url('admin/anggota') ?>" class="btn btn-outline-secondary">Kembali ke Daftar Anggota</a>
        </div>
      </div>

      <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
      <?php endif; ?>

      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
          <dl class="row mb-0">
            <dt class="col-sm-3">Nama Lengkap</dt>
            <dd class="col-sm-9"><?= esc($anggotaDisplayName) ?></dd>
            <dt class="col-sm-3">Jabatan</dt>
            <dd class="col-sm-9"><?= esc($anggota['jabatan'] ?? '-') ?></dd>
            <dt class="col-sm-3">Status Pernikahan</dt>
            <dd class="col-sm-9"><?= esc($anggota['status_pernikahan'] ?? '-') ?></dd>
            <dt class="col-sm-3">Jumlah Anak</dt>
            <dd class="col-sm-9"><?= esc($anggota['jumlah_anak'] ?? 0) ?></dd>
          </dl>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-lg-7">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
              <h2 class="h6 mb-0">Komponen Gaji yang Sudah Ditambahkan</h2>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th scope="col">Komponen</th>
                      <th scope="col">Kategori</th>
                      <th scope="col">Nominal</th>
                      <th scope="col">Satuan</th>
                      <th scope="col" class="text-center">Aksi</th>
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
                          <td class="text-center">
                            <form action="<?= base_url('admin/penggajian/anggota/' . ($anggota['id_anggota'] ?? $anggota['id'] ?? 0) . '/hapus/' . ($row['id_komponen_gaji'] ?? 0)) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus komponen dari penggajian anggota ini?');">
                              <?= csrf_field() ?>
                              <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="5" class="text-center text-muted py-4">Belum ada komponen gaji yang ditambahkan.</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
              <h2 class="h6 mb-0">Tambah Komponen Gaji</h2>
            </div>
            <div class="card-body">
              <?php if (! empty($availableKomponen)): ?>
                <form action="<?= base_url('admin/penggajian/anggota/' . ($anggota['id_anggota'] ?? $anggota['id'] ?? 0)) ?>" method="post">
                  <?= csrf_field() ?>
                  <div class="mb-3">
                    <label for="id_komponen_gaji" class="form-label">Pilih Komponen</label>
                    <select class="form-select" id="id_komponen_gaji" name="id_komponen_gaji" required>
                      <option value="" selected disabled>-- Pilih komponen gaji --</option>
                      <?php foreach ($availableKomponen as $komponen): ?>
                        <?php
                          $idKomponen = (int) ($komponen['id_komponen_gaji'] ?? 0);
                          $nama       = trim((string) ($komponen['nama_komponen'] ?? '-'));
                          $kategori   = trim((string) ($komponen['kategori'] ?? '-'));
                          $nominal    = (float) ($komponen['nominal'] ?? 0);
                          $satuan     = $satuanOptions[$komponen['satuan'] ?? ''] ?? ($komponen['satuan'] ?? '');
                        ?>
                        <option value="<?= esc($idKomponen) ?>">
                          <?= esc($nama) ?> - <?= esc($kategori) ?> (Rp <?= number_format($nominal, 2, ',', '.') ?>, <?= esc($satuan) ?>)
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <button type="submit" class="btn btn-primary w-100">Tambah Komponen</button>
                </form>
              <?php else: ?>
                <div class="alert alert-info mb-0">
                  Semua komponen gaji yang relevan sudah ditambahkan untuk anggota ini.
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  </body>
</html>
