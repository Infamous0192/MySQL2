<?php
$url = explode('/', getenv('REQUEST_URI'));
$action = isset($url[4]) ? explode('?', $url[4]) : [''];

switch (strtolower($action[0])) {
  case 'hapus':
    if ($db->hapusUnit($_GET['id']) > 0) {
      header('Location: index.php/unit');
    }
    break;
  case 'ubah':
    if (isset($_POST["submit"]) && $db->ubahUnit($_POST) > 0) {
      header('Location: index.php/unit');
    }
    break;
  default:
    if (isset($_POST["submit"]) && $db->tambahUnit($_POST) > 0) {
      echo "
        <script>
          alert('Unit berhasil ditambahkan!');
        </script>
      ";
    }
}
$search = isset($_GET['search']) ? $_GET['search'] : '';
$limit = 2;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$skip = $limit * ($page - 1);
$count = query("SELECT COUNT(*) total FROM unit_kerja")[0]['total'];

$data = query("SELECT * FROM unit_kerja WHERE nama_unitkerja LIKE '%$search%' LIMIT $skip, $limit");
?>

<div class="container">
  <div class="d-flex justify-content-between">
    <h3>Daftar Unit Kerja</h3>

    <!-- Button trigger modal -->
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal">Tambah</button>

    <!-- Modal -->
    <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form id="form" method="POST" action="">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Tambah unit kerja</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label for="unit" class="form-label">Nama unit</label>
                <input type="text" class="form-control" name="unit" id="unit" required>
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
        <th scope="col">Nama Unit Kerja</th>
        <th scope="col">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php $index = 1 ?>
      <?php foreach ($data as $row) : ?>
        <tr>
          <th scope="row"><?= $index ?></th>
          <th scope="row"><?= $row['id_unitkerja'] ?></th>
          <td><?= $row['nama_unitkerja'] ?></td>
          <td>
            <a href="<?= BASE_URL . '/unit/hapus?id=' . $row['id_unitkerja'] ?>">
              <button class="btn btn-danger">Hapus</button>
            </a>
            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#ubah<?= $row['id_unitkerja'] ?>">Ubah</button>
            <div class="modal fade" id="ubah<?= $row['id_unitkerja'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form id="form" method="POST" action="<?= BASE_URL . '/unit/ubah' ?>">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Ubah unit kerja</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-3">
                        <input type="hidden" name="id" value="<?= $row['id_unitkerja'] ?>">
                        <label for="unit" class="form-label">Nama unit</label>
                        <input type="text" class="form-control" name="unit" id="unit" value="<?= $row['nama_unitkerja'] ?>" required>
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