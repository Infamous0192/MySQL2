<?php
$url = explode('/', getenv('REQUEST_URI'));
$action = isset($url[4]) ? explode('?', $url[4]) : [''];

switch (strtolower($action[0])) {
  case 'hapus':
    if ($db->hapusJabatan($_GET['id']) > 0) {
      header('Location: index.php/jabatan');
    }
    break;
  case 'ubah':
    if (isset($_POST["submit"]) && $db->ubahJabatan($_POST) > 0) {
      header('Location: index.php/jabatan');
    }
    break;
  default:
    if (isset($_POST["submit"]) && $db->tambahJabatan($_POST) > 0) {
      echo "
        <script>
          alert('Jabatan berhasil ditambahkan!');
        </script>
      ";
    }
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$limit = 2;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$skip = $limit * ($page - 1);
$count = query("SELECT COUNT(*) total FROM jabatan")[0]['total'];

$data = query("SELECT * FROM jabatan WHERE nama_jabatan LIKE '%$search%' LIMIT $skip, $limit");
?>

<div class="container">
  <div class="d-flex justify-content-between">
    <h3>Daftar Jabatan</h3>

    <!-- Button trigger modal -->
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal">Tambah</button>

    <!-- Modal -->
    <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form id="form" method="POST" action="">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Tambah jabatan</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label for="jabatan" class="form-label">Nama jabatan</label>
                <input type="text" class="form-control" name="jabatan" id="jabatan" required>
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
        <th scope="col">ID</th>
        <th scope="col">Nama Jabatan</th>
        <th scope="col">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php $index = 1 ?>
      <?php foreach ($data as $row) : ?>
        <tr>
          <th scope="row"><?= $index ?></th>
          <th scope="row"><?= $row['id_jabatan'] ?></th>
          <td><?= $row['nama_jabatan'] ?></td>
          <td>
            <a href="<?= BASE_URL . '/jabatan/hapus?id=' . $row['id_jabatan'] ?>">
              <button class="btn btn-danger">Hapus</button>
            </a>
            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#ubah<?= $row['id_jabatan'] ?>">Ubah</button>
            <div class="modal fade" id="ubah<?= $row['id_jabatan'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form id="form" method="POST" action="<?= BASE_URL . '/jabatan/ubah' ?>">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Ubah jabatan kerja</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-3">
                        <input type="hidden" name="id" value="<?= $row['id_jabatan'] ?>">
                        <label for="jabatan" class="form-label">Nama jabatan</label>
                        <input type="text" class="form-control" name="jabatan" id="jabatan" value="<?= $row['nama_jabatan'] ?>" required>
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