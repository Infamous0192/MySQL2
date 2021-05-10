<?php
require_once 'config.php';
// Connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
	die("ERROR: Can't connect to database");
}

function render($path, $title)
{
	include './layouts/header.php';

	include "./views/$path.php";

	include './layouts/footer.php';
}

function query($query)
{
	global $conn;
	$result = mysqli_query($conn, $query);
	$rows = [];

	while ($row = mysqli_fetch_assoc($result)) {
		$rows[] = $row;
	}

	return $rows;
}

function tambahPegawai($data)
{
	global $conn;

	$nama = htmlspecialchars($data["nama"]);
	$unit = htmlspecialchars($data["unit"]);
	$jabatan = htmlspecialchars($data["jabatan"]);
	$tempat = htmlspecialchars($data["tempat"]);
	$tanggal = htmlspecialchars($data["tanggal"]);

	$foto = upload();

	if (!$foto) return false;

	$query = "INSERT INTO pegawai VALUES ('', '$unit', '$jabatan', '$nama', '$tempat', '$tanggal', '$foto')";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

function tambahJabatan($data)
{
	global $conn;
	$jabatan = $data['jabatan'];

	$query = "INSERT INTO jabatan VALUES ('', '$jabatan')";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

function tambahUnit($data)
{
	global $conn;
	$unit = $data['unit'];

	$query = "INSERT INTO unit_kerja VALUES ('', '$unit')";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

function hapusUnit($id)
{
	global $conn;

	mysqli_query($conn, "DELETE FROM unit_kerja WHERE id_unitkerja = $id");

	return mysqli_affected_rows($conn);
}

function hapusPegawai($nip)
{
	global $conn;

	mysqli_query($conn, "DELETE FROM pegawai WHERE nip = $nip");

	return mysqli_affected_rows($conn);
}

function hapusJabatan($id)
{
	global $conn;

	mysqli_query($conn, "DELETE FROM jabatan WHERE id_jabatan = $id");

	return mysqli_affected_rows($conn);
}

function ubahUnit($data)
{
	global $conn;

	$id = $data['id'];
	$unit = $data['unit'];
	$query = "UPDATE unit_kerja SET nama_unitkerja = '$unit' WHERE id_unitkerja = $id";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

function ubahJabatan($data)
{
	global $conn;

	$id = $data['id'];
	$jabatan = $data['jabatan'];
	$query = "UPDATE jabatan SET nama_jabatan = '$jabatan' WHERE id_jabatan = $id";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
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

function ubahPegawai($data)
{
	global $conn;

	$nip = $data["nip"];
	$nama = htmlspecialchars($data["nama"]);
	$unit = htmlspecialchars($data["unit"]);
	$jabatan = htmlspecialchars($data["jabatan"]);
	$tempat = htmlspecialchars($data["tempat"]);
	$tanggal = htmlspecialchars($data["tanggal"]);
	$gambarLama = htmlspecialchars($data["gambarLama"]);

	// cek apakah user pilih gambar baru atau tidak
	if ($_FILES['foto']['error'] === 4) {
		$gambar = $gambarLama;
	} else {
		$gambar = upload();
	}

	$query = "UPDATE mahasiswa SET
				nama_pegawai = '$nama',
				id_unitkerja = '$unit',
				id_jabatan = '$jabatan',
				tempat_lahir = '$tempat',
				tanggal_lahir = '$tanggal',
				foto = '$gambar'
			  WHERE nip = $nip
			";

	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

function cari($keyword)
{
	$query = "SELECT * FROM mahasiswa
				WHERE
			  nama LIKE '%$keyword%' OR
			  alamat LIKE '%$keyword%' OR
			  hobi LIKE '%$keyword%'
			";
	return query($query);
}
