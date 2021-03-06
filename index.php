<?php
require 'vendor/autoload.php';
require_once 'functions.php';
require_once 'database.php';
require_once 'services/printing.php';
$url = explode('/', getenv('REQUEST_URI'));
$page = isset($url[3]) ? explode('?', $url[3]) : [''];
session_start();

switch ($page[0]) {
  case 'pegawai':
    privateRoute();
    render('pegawai', 'Pegawai');
    break;
  case 'jabatan':
    privateRoute();
    render('jabatan', 'Jabatan');
    break;
  case 'unit':
    privateRoute();
    render('unit_kerja', 'Unit Kerja');
    break;
  case 'pengguna':
    privateRoute();
    render('pengguna', 'Pengguna');
    break;
  case 'login':
    render('login', 'Login');
    break;
  case 'printpdf':
    $print = new Printing();
    $print->pdf();
    break;
  case 'printexcel':
    $print = new Printing();
    $print->excel();
    break;
  case 'printword':
    $print = new Printing();
    $print->word();
    break;
  case 'logout':
    logout();
    break;
  default:
    render('home', 'Home');
    break;
}
