<?php 
require_once 'functions.php';
$url = explode('/', getenv('REQUEST_URI'));
$page = isset($url[3]) ? explode('?', $url[3]) : [''];

switch ($page[0]) {
  case 'pegawai':
    render('pegawai', 'Pegawai');
    break;
  case 'jabatan':
    render('jabatan', 'Jabatan');
    break;
  case 'unit':
    render('unit_kerja', 'Unit Kerja');
    break;
  
  default:
    render('pegawai', 'Pegawai');
    break;
}

?>