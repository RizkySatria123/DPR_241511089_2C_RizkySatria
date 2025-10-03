<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Komponen Gaji</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container">
        <a class="navbar-brand" href="<?= base_url('admin') ?>">Gaji DPR</a>
        <div class="d-flex ms-auto">
          <a href="<?= base_url('admin/komponen-gaji') ?>" class="btn btn-outline-light me-2">Daftar Komponen</a>
          <form action="<?= base_url('logout') ?>" method="post" class="d-inline">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-light">Logout</button>
          </form>
        </div>
      </div>
    </nav>

    <div class="container py-4">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="card shadow-sm border-0">
            <div class="card-body p-4">
              <h1 class="h4 mb-4">Tambah Komponen Gaji &amp; Tunjangan</h1>

              <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger mb-4"><?= esc(session()->getFlashdata('error')) ?></div>
              <?php endif; ?>

              <form action="<?= base_url('admin/komponen-gaji') ?>" method="post" class="row g-3">
                <?= csrf_field() ?>

                <div class="col-md-8">
                  <label for="nama" class="form-label">Nama Komponen</label>
                  <input type="text" name="nama" id="nama" class="form-control" value="<?= old('nama') ?>" required>
                </div>

                <div class="col-md-4">
                  <label for="kategori" class="form-label">Kategori</label>
                  <select name="kategori" id="kategori" class="form-select" required>
                    <option value="" hidden>Pilih kategori</option>
                    <?php foreach ($kategoriOptions as $value => $label): ?>
                      <option value="<?= esc($value) ?>" <?= old('kategori') === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-md-6">
                  <label for="nominal_default" class="form-label">Nominal Default (Rp)</label>
                  <input type="number" step="0.01" min="0" name="nominal_default" id="nominal_default" class="form-control" value="<?= old('nominal_default', '0') ?>" required>
                  <small class="text-muted">Gunakan titik sebagai pemisah desimal, misal 15000000.50</small>
                </div>

                <div class="col-12">
                  <label for="deskripsi" class="form-label">Deskripsi (opsional)</label>
                  <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3"><?= old('deskripsi') ?></textarea>
                </div>

                <div class="col-12 d-flex justify-content-between">
                  <a href="<?= base_url('admin/komponen-gaji') ?>" class="btn btn-outline-secondary">Batal</a>
                  <button type="submit" class="btn btn-primary">Simpan Komponen</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  </body>
</html>
