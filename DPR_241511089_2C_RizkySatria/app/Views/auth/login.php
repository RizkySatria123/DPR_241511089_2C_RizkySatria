<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Aplikasi Gaji DPR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
  <body class="bg-light">
    <div class="container min-vh-100 d-flex align-items-center">
      <div class="row w-100 justify-content-center">
        <div class="col-12 col-sm-10 col-md-7 col-lg-5">
          <div class="card shadow-sm">
            <div class="card-body p-4">
              <h1 class="h4 mb-3 text-center">Login</h1>
              <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger" role="alert">
                  <?= esc(session()->getFlashdata('error')) ?>
                </div>
              <?php endif; ?>

              <form action="<?= base_url('login') ?>" method="post" novalidate>
                <?= csrf_field() ?>
                <div class="mb-3">
                  <label for="username" class="form-label">Username</label>
                  <input type="text" class="form-control" id="username" name="username" value="<?= esc(old('username')) ?>" required autofocus>
                </div>
                <div class="mb-3">
                  <label for="password" class="form-label">Password</label>
                  <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Masuk</button>
              </form>
            </div>
          </div>
          <p class="text-center text-muted mt-3 mb-0">&copy; <?= date('Y') ?> Aplikasi Penghitungan & Transparansi Gaji DPR</p>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
