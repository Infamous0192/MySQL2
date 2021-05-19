<?php
$url = explode('/', getenv('REQUEST_URI'));
$action = isset($url[4]) ? explode('?', $url[4]) : [''];

switch (strtolower($action[0])) {
  case 'hapus':
    if (hapusPegawai($_GET['nip']) > 0) {
      header('Location: index.php');
    }
    break;
  case 'ubah':
    if (isset($_POST["submit"]) && ubahPegawai($_POST) > 0) {
      header('Location: index.php');
    }
    break;
  default:
    if (isset($_POST["submit"]) > 0) {
      tambahPengguna($_POST);
      tambahPegawai($_POST);
      echo "
        <script>
          alert('Pegawai berhasil ditambahkan!');
        </script>
      ";
    }
}
$search = isset($_GET['search']) ? $_GET['search'] : '';
$limit = 2;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$skip = $limit * ($page - 1);
$count = query("SELECT COUNT(*) total FROM pegawai")[0]['total'];

$data = query("SELECT * FROM pegawai p JOIN jabatan j ON p.id_jabatan=j.id_jabatan JOIN unit_kerja u ON p.id_unitkerja=u.id_unitkerja WHERE p.nama_pegawai LIKE '%$search%' LIMIT $skip, $limit");

$jabatan = query("SELECT * FROM jabatan");
$unit = query("SELECT * FROM unit_kerja");
?>

<div class="container">
  <div class="d-flex justify-content-between">
    <h3>Daftar Pegawai</h3>

    <!-- Button trigger modal -->
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal">Tambah</button>

    <!-- Modal -->
    <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form id="form" method="POST" action="" enctype="multipart/form-data">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Tambah pegawai</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <!-- Nama -->
              <div class="mb-3">
                <label for="nama" class="form-label">Nama pegawai</label>
                <input type="text" class="form-control" name="nama" id="nama" required>
              </div>

              <!-- Username -->
              <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" name="username" id="username" required>
              </div>

              <!-- Password -->
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" id="password" required>
              </div>

              <!-- Tempat lahir -->
              <div class="mb-3">
                <label for="tempat" class="form-label">Tempat lahir</label>
                <input type="text" class="form-control" name="tempat" id="tempat" required>
              </div>

              <!-- Tanggal lahir -->
              <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal lahir</label>
                <input type="date" class="form-control" name="tanggal" id="tanggal" required>
              </div>

              <!-- Jabatan -->
              <div class="mb-3">
                <label for="jabatan" class="form-label">Jabatan</label>
                <select id="jabatan" name="jabatan" class="form-select" aria-label="Default select example">
                  <option selected>Pilih jabatan</option>
                  <?php foreach ($jabatan as $option) : ?>
                    <option value="<?= $option['id_jabatan'] ?>"><?= $option['nama_jabatan'] ?></option>
                  <?php endforeach ?>
                </select>
              </div>

              <!-- Unit kerja -->
              <div class="mb-3">
                <label for="unit" class="form-label">Unit kerja</label>
                <select id="unit" name="unit" class="form-select" aria-label="Default select example">
                  <option selected>Pilih unit kerja</option>
                  <?php foreach ($unit as $option) : ?>
                    <option value="<?= $option['id_unitkerja'] ?>"><?= $option['nama_unitkerja'] ?></option>
                  <?php endforeach ?>
                </select>
              </div>
              <div class="mb-3">
                <label for="foto" class="form-label">Pas foto</label>
                <input class="form-control" type="file" id="foto" name="foto">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" name="submit" value="submit" class="btn btn-primary">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="my-2">
    <h5>Pencarian</h5>
    <form action="">
      <div class="mb-3">
        <input type="text" class="form-control" name="search">
      </div>
    </form>
  </div>

  <table class="table">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">NIP</th>
        <th scope="col">Nama Pegawai</th>
        <th scope="col">Unit kerja</th>
        <th scope="col">Jabatan</th>
        <th scope="col">Tempat, tanggal lahir</th>
        <th scope="col">Foto</th>
        <th scope="col">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php $index = 1 ?>
      <?php foreach ($data as $row) : ?>
        <tr>
          <th scope="row"><?= $index ?></th>
          <th scope="row"><?= $row['nip'] ?></th>
          <td><?= $row['nama_pegawai'] ?></td>
          <td><?= $row['nama_unitkerja'] ?></td>
          <td><?= $row['nama_jabatan'] ?></td>
          <td><?= $row['tempat_lahir'] . ', ' . $row['tanggal_lahir'] ?></td>
          <td><img width="100" src="<?= '/' . FOLDER_NAME . '/img/' . $row['foto'] ?>" alt=""></td>
          <td>
            <a href="<?= BASE_URL . '/pegawai/hapus?nip=' . $row['nip'] ?>">
              <button class="btn btn-danger">Hapus</button>
            </a>
            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#ubah<?= $row['nip'] ?>">Ubah</button>
            <div class="modal fade" id="ubah<?= $row['nip'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form id="form" method="POST" action="<?= BASE_URL . '/pegawai/ubah' ?>" enctype="multipart/form-data">
                    <input type="hidden" name="nip" value="<?= $row['nip'] ?>">
                    <input type="hidden" name="gambarLama" value="<?= $row['foto'] ?>">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Ubah pegawai</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <!-- Nama -->
                      <div class="mb-3">
                        <label for="nama" class="form-label">Nama pegawai</label>
                        <input type="text" class="form-control" name="nama" id="nama" value="<?= $row['nama_pegawai'] ?>" required>
                      </div>

                      <!-- Tempat lahir -->
                      <div class="mb-3">
                        <label for="tempat" class="form-label">Tempat lahir</label>
                        <input type="text" class="form-control" name="tempat" id="tempat" value="<?= $row['tempat_lahir'] ?>" required>
                      </div>

                      <!-- Tanggal lahir -->
                      <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal lahir</label>
                        <input type="date" class="form-control" name="tanggal" id="tanggal" value="<?= $row['tanggal_lahir'] ?>" required>
                      </div>

                      <!-- Jabatan -->
                      <div class="mb-3">
                        <label for="jabatan" class="form-label">Jabatan</label>
                        <select id="jabatan" name="jabatan" class="form-select" aria-label="Default select example">
                          <option selected>Pilih jabatan</option>
                          <?php foreach ($jabatan as $option) : ?>
                            <option value="<?= $option['id_jabatan'] ?>"><?= $option['nama_jabatan'] ?></option>
                          <?php endforeach ?>
                        </select>
                      </div>

                      <!-- Unit kerja -->
                      <div class="mb-3">
                        <label for="unit" class="form-label">Unit kerja</label>
                        <select id="unit" name="unit" class="form-select" aria-label="Default select example">
                          <option selected>Pilih unit kerja</option>
                          <?php foreach ($unit as $option) : ?>
                            <option value="<?= $option['id_unitkerja'] ?>"><?= $option['nama_unitkerja'] ?></option>
                          <?php endforeach ?>
                        </select>
                      </div>
                      <div class="mb-3">
                        <label for="foto" class="form-label">Pas foto</label>
                        <input class="form-control" type="file" id="foto" name="foto">
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="submit" name="submit" value="submit" class="btn btn-primary">Submit</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </td>
        </tr>
        <?php $index++; ?>
      <?php endforeach ?>
    </tbody>
  </table>
  <nav aria-label="Page navigation">
    <ul class="pagination">
      <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>"><a class="page-link" href="<?= '?page=' . ($page - 1) . "&search=$search" ?>">Previous</a></li>
      <li class="page-item <?= (($page * $limit) - count($data) >= $count - $limit) ? 'disabled' : '' ?>""><a class=" page-link" href="<?= "?page=" . ($page + 1) . "&search=$search" ?>">Next</a></li>
    </ul>
  </nav>
</div>