<nav class="navbar navbar-expand-lg navbar-light bg-light mb-5">
  <div class="container">
    <div>
      <a class="navbar-brand" href="<?= BASE_URL . '/'; ?>">Manajemen pegawai</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
    <div class="collapse navbar-collapse d-flex w-full justify-content-between" id="navbarNavDropdown">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="<?= BASE_URL . '/'; ?>">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="<?= BASE_URL . '/pegawai'; ?>">Pegawai</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="<?= BASE_URL . '/jabatan'; ?>">Jabatan</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="<?= BASE_URL . '/unit'; ?>">Unit Kerja</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="<?= BASE_URL . '/pengguna'; ?>">Pengguna</a>
        </li>
      </ul>
      <div>
        <?php if (!isset($_SESSION['username'])) : ?>
          <a href="<?= BASE_URL . '/login'; ?>">
            <button class="btn btn-outline-primary rounded-pill px-4">Login</button>
          </a>
        <?php else : ?>
          <a href="<?= BASE_URL . '/logout'; ?>">
            <button class="btn btn-outline-danger rounded-pill px-4">Logout</button>
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>