<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Anggota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container">
        <a class="navbar-brand" href="<?= base_url('admin') ?>">Gaji DPR</a>
      </div>
    </nav>

    <div class="container py-4">
      <h1 class="h4 mb-3">Tambah Anggota</h1>
      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
      <?php endif; ?>

  <form action="<?= base_url('admin/anggota') ?>" method="post" class="row g-3">
        <?= csrf_field() ?>
        <div class="col-md-6">
          <label class="form-label">Nama Depan<span class="text-danger">*</span></label>
          <input type="text" name="nama_depan" class="form-control" value="<?= esc(old('nama_depan')) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Nama Belakang<span class="text-danger">*</span></label>
          <input type="text" name="nama_belakang" class="form-control" value="<?= esc(old('nama_belakang')) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Gelar Depan</label>
          <input type="text" name="gelar_depan" class="form-control" value="<?= esc(old('gelar_depan')) ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Gelar Belakang</label>
          <input type="text" name="gelar_belakang" class="form-control" value="<?= esc(old('gelar_belakang')) ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Jabatan<span class="text-danger">*</span></label>
          <select name="jabatan" class="form-select" required>
            <option value="" disabled selected>Pilih jabatan</option>
            <?php foreach (($jabatanOptions ?? []) as $opt): ?>
              <option value="<?= esc($opt) ?>" <?= old('jabatan') === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Status Pernikahan<span class="text-danger">*</span></label>
          <select name="status_pernikahan" class="form-select" required>
            <option value="" disabled selected>Pilih status</option>
            <?php foreach (($statusOptions ?? []) as $opt): ?>
              <option value="<?= esc($opt) ?>" <?= old('status_pernikahan') === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Jumlah Anak<span class="text-danger">*</span></label>
          <input type="number" min="0" name="jumlah_anak" class="form-control" value="<?= esc(old('jumlah_anak', '0')) ?>" required>
        </div>
        <div class="col-12 d-flex gap-2">
          <button type="submit" class="btn btn-primary">Simpan</button>
          <a href="<?= base_url('admin/anggota') ?>" class="btn btn-secondary">Batal</a>
        </div>
      </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  </body>
</html>
