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
      <?php if (session()->getFlashdata('warning')): ?>
        <div class="alert alert-warning"><?= esc(session()->getFlashdata('warning')) ?></div>
      <?php endif; ?>

      <?php $anggotaId = (int) ($anggota['id_anggota'] ?? $anggota['id'] ?? 0); ?>

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
              <div class="d-flex justify-content-between align-items-center">
                <h2 class="h6 mb-0">Komponen Gaji yang Sudah Ditambahkan</h2>
                <div class="d-flex align-items-center gap-2">
                  <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" value="" id="select-all-komponen">
                    <label class="form-check-label" for="select-all-komponen">Pilih semua</label>
                  </div>
                  <button type="button" class="btn btn-sm btn-outline-danger" id="bulkDeleteBtn">Hapus Terpilih</button>
                </div>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th scope="col" class="text-center" style="width: 48px;">
                        <span class="visually-hidden">Pilih</span>
                      </th>
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
                          <td class="text-center">
                            <input class="form-check-input komponen-checkbox" type="checkbox" value="<?= esc($row['id_komponen_gaji'] ?? 0) ?>">
                          </td>
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
                        <td colspan="6" class="text-center text-muted py-4">Belum ada komponen gaji yang ditambahkan.</td>
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
                <?php
                  $selectedKomponenOld = old('id_komponen_gaji');
                  if ($selectedKomponenOld === null) {
                    $selectedKomponenOld = [];
                  }
                  if (! is_array($selectedKomponenOld)) {
                    $selectedKomponenOld = [$selectedKomponenOld];
                  }
                  $selectedKomponenLookup = array_flip(array_map('intval', $selectedKomponenOld));
                ?>
                <form action="<?= base_url('admin/penggajian/anggota/' . $anggotaId) ?>" method="post">
                  <?= csrf_field() ?>
                  <div class="mb-3">
                    <label for="id_komponen_gaji" class="form-label">Pilih Komponen</label>
                    <select class="form-select" id="id_komponen_gaji" name="id_komponen_gaji[]" multiple size="6" required>
                      <option value="" disabled>-- Pilih satu atau lebih komponen --</option>
                      <?php foreach ($availableKomponen as $komponen): ?>
                        <?php
                          $idKomponen = (int) ($komponen['id_komponen_gaji'] ?? 0);
                          $nama       = trim((string) ($komponen['nama_komponen'] ?? '-'));
                          $kategori   = trim((string) ($komponen['kategori'] ?? '-'));
                          $nominal    = (float) ($komponen['nominal'] ?? 0);
                          $satuan     = $satuanOptions[$komponen['satuan'] ?? ''] ?? ($komponen['satuan'] ?? '');
                          $isSelected = isset($selectedKomponenLookup[$idKomponen]);
                        ?>
                        <option value="<?= esc($idKomponen) ?>" <?= $isSelected ? 'selected' : '' ?>>
                          <?= esc($nama) ?> - <?= esc($kategori) ?> (Rp <?= number_format($nominal, 2, ',', '.') ?>, <?= esc($satuan) ?>)
                        </option>
                      <?php endforeach; ?>
                    </select>
                    <div class="form-text">Tahan tombol Ctrl (atau Cmd di Mac) untuk memilih lebih dari satu komponen.</div>
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

    <form id="bulkDeleteForm" action="<?= base_url('admin/penggajian/anggota/' . $anggotaId . '/hapus-banyak') ?>" method="post" class="d-none">
      <?= csrf_field() ?>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
      (function () {
        const selectAll = document.getElementById('select-all-komponen');
        const bulkBtn = document.getElementById('bulkDeleteBtn');
        const bulkForm = document.getElementById('bulkDeleteForm');

        const getCheckboxes = () => Array.from(document.querySelectorAll('.komponen-checkbox'));

        if (selectAll) {
          selectAll.addEventListener('change', () => {
            getCheckboxes().forEach(cb => {
              cb.checked = selectAll.checked;
            });
          });
        }

        if (bulkBtn && bulkForm) {
          bulkBtn.addEventListener('click', () => {
            const selected = getCheckboxes().filter(cb => cb.checked);

            if (selected.length === 0) {
              alert('Pilih minimal satu komponen untuk dihapus.');
              return;
            }

            if (! confirm('Hapus komponen terpilih dari penggajian anggota ini?')) {
              return;
            }

            // Hapus input yang ada (selain CSRF)
            Array.from(bulkForm.querySelectorAll('input[name="komponen_ids[]"]')).forEach(el => el.remove());

            selected.forEach(cb => {
              const input = document.createElement('input');
              input.type = 'hidden';
              input.name = 'komponen_ids[]';
              input.value = cb.value;
              bulkForm.appendChild(input);
            });

            bulkForm.submit();
          });
        }
      })();
    </script>
  </body>
</html>
