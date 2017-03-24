<?php
ini_set('display_errors', 'On');
ini_set('html_errors', 0);
error_reporting(-1);
require_once 'config/class.sicyca.php';
$datetime = new DateTime('today');
//echo $datetime->format('Y-m-d h');

date_default_timezone_set('Asia/Jakarta');


$today = getdate();
//print_r($today);
$tanggal = $today['mday'];
$bulan = $today['mon'];
$tahun = $today['year'];
$jam = $today['hours'];
$menit = $today['minutes'];
$detik = $today['seconds'];


$hariini = $tanggal."-".$bulan."-".$tahun." ".$jam.":".$menit.":".$detik;

//echo $hariini;




$sicyca = new sicyca();
$sicyca->send($hariini);
?>
