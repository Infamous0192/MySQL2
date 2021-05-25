<?php
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

function render($path, $title)
{
	include './layouts/header.php';

	include "./views/$path.php";

	include './layouts/footer.php';
}

function logout()
{
	setcookie('username', '', time() - 3600);
	setcookie('password', '', time() - 3600);
	session_unset();
	session_destroy();
	header('Location: home');
}
