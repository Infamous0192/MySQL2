<?php
require_once 'config.php';
// Connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
	die("ERROR: Can't connect to database");
}

function privateRoute()
{
	if (!isset($_SESSION['username'])) {
		if (!empty($_COOKIE)) {
			$_SESSION = $_COOKIE;
			header('Location: home');
		}
		header('Location: login');
	}
}

function logout()
{
	setcookie('username', '', time() - 3600);
	setcookie('password', '', time() - 3600);
	session_unset();
	session_destroy();
	header('Location: home');
}

function login($data)
{
	$username = $data['username'];
	$password = $data['password'];

	$user = query("SELECT * FROM pengguna WHERE username='$username'");

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
