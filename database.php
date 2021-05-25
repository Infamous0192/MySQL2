<?php

class Database
{
  public $conn;

  function __construct()
  {
    $this->conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if (!$this->conn) {
      die("ERROR: Can't connect to database");
    }
  }

  function query($query)
  {
    $result = mysqli_query($this->conn, $query);
    $rows = [];

    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }

    return $rows;
  }

  function upload()
  {
    $namaFile = $_FILES['foto']['name'];
    $ukuranFile = $_FILES['foto']['size'];
    $error = $_FILES['foto']['error'];
    $tmpName = $_FILES['foto']['tmp_name'];

    // cek apakah tidak ada gambar yang diupload
    if ($error === 4) {
      echo "<script>
				alert('pilih foto terlebih dahulu!');
			  </script>";
      return false;
    }

    // cek apakah yang diupload adalah gambar
    $ekstensiGambarValid = ['jpg', 'jpeg', 'png'];
    $ekstensiGambar = explode('.', $namaFile);
    $ekstensiGambar = strtolower(end($ekstensiGambar));

    if (!in_array($ekstensiGambar, $ekstensiGambarValid)) {
      echo "<script>
				alert('yang anda upload bukan gambar!');
			  </script>";
      return false;
    }

    // cek jika ukurannya terlalu besar
    if ($ukuranFile > 10000000) {
      echo "<script>
				alert('ukuran gambar terlalu besar!');
			  </script>";
      return false;
    }

    // lolos pengecekan, gambar siap diupload
    // generate nama gambar baru
    $namaFileBaru = uniqid();
    $namaFileBaru .= '.';
    $namaFileBaru .= $ekstensiGambar;

    move_uploaded_file($tmpName, 'img/' . $namaFileBaru);

    return $namaFileBaru;
  }

  function login($data)
  {
    $username = $data['username'];
    $password = $data['password'];

    $user = $this->query("SELECT * FROM pengguna WHERE username='$username'");

    if (!count($user)) {
      echo '<div class="alert alert-danger" role="alert">User tidak ditemukan</div>';
      return;
    }

    $hashedPassword = md5($password);

    if ($user[0]['password'] !== $hashedPassword) {
      echo '<div class="alert alert-danger" role="alert">Password salah</div>';
      return;
    }

    $_SESSION['username'] = $username;
    $_SESSION['nama'] = $user[0]['nama'];

    if ($data['remember']) {
      setcookie('username', $username, time() + 3600);
      setcookie('password', $password, time() + 3600);
    }

    header('Location: home');
  }

  function tambahPegawai($data)
  {
    $this->conn;

    $nama = htmlspecialchars($data["nama"]);
    $unit = htmlspecialchars($data["unit"]);
    $jabatan = htmlspecialchars($data["jabatan"]);
    $tempat = htmlspecialchars($data["tempat"]);
    $tanggal = htmlspecialchars($data["tanggal"]);
    $username = $data['username'];

    $pengguna = $this->query("SELECT id_pengguna FROM pengguna WHERE username='$username'")[0]['id_pengguna'];

    $foto = $this->upload();

    if (!$foto) return false;

    $query = "INSERT INTO pegawai VALUES ('', '$unit', '$jabatan', '$pengguna', '$nama', '$tempat', '$tanggal', '$foto')";
    mysqli_query($this->conn, $query);

    return mysqli_affected_rows($this->conn);
  }

  function tambahJabatan($data)
  {
    $jabatan = $data['jabatan'];

    $query = "INSERT INTO jabatan VALUES ('', '$jabatan')";
    mysqli_query($this->conn, $query);

    return mysqli_affected_rows($this->conn);
  }

  function tambahUnit($data)
  {
    $unit = $data['unit'];

    $query = "INSERT INTO unit_kerja VALUES ('', '$unit')";
    mysqli_query($this->conn, $query);

    return mysqli_affected_rows($this->conn);
  }

  function hapusUnit($id)
  {
    mysqli_query($this->conn, "DELETE FROM unit_kerja WHERE id_unitkerja = $id");

    return mysqli_affected_rows($this->conn);
  }

  function hapusPegawai($nip)
  {
    mysqli_query($this->conn, "DELETE FROM pegawai WHERE nip = $nip");

    return mysqli_affected_rows($this->conn);
  }

  function hapusJabatan($id)
  {
    mysqli_query($this->conn, "DELETE FROM jabatan WHERE id_jabatan = $id");

    return mysqli_affected_rows($this->conn);
  }

  function ubahUnit($data)
  {
    $id = $data['id'];
    $unit = $data['unit'];
    $query = "UPDATE unit_kerja SET nama_unitkerja = '$unit' WHERE id_unitkerja = $id";
    mysqli_query($this->conn, $query);

    return mysqli_affected_rows($this->conn);
  }

  function ubahJabatan($data)
  {
    $id = $data['id'];
    $jabatan = $data['jabatan'];
    $query = "UPDATE jabatan SET nama_jabatan = '$jabatan' WHERE id_jabatan = $id";
    mysqli_query($this->conn, $query);

    return mysqli_affected_rows($this->conn);
  }

  function tambahPengguna($data)
  {
    $username = $data['username'];
    $password = $data['password'];
    $nama = $data['nama'];
    $hashedPassword = md5($password);

    $query = "INSERT INTO pengguna VALUES ('', '$username', '$hashedPassword', '$nama')";
    mysqli_query($this->conn, $query);

    return mysqli_affected_rows($this->conn);
  }

  function hapusPengguna($id)
  {
    $query = "DELETE FROM pengguna WHERE id_pengguna=$id";
    mysqli_query($this->conn, $query);

    return mysqli_affected_rows($this->conn);
  }
}
