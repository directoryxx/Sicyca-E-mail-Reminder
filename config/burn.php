<?php
ini_set('display_errors', 'On');
ini_set('html_errors', 0);
error_reporting(-1);
$datetime = new DateTime('today');
//echo $datetime->format('Y-m-d h');

if(date_default_timezone_get()){
 echo 'date_default_timezone_set: ' . date_default_timezone_get() . '<br />';
}

$today = getdate();
//print_r($today);
$tanggal = $today['mday'];
$bulan = $today['mon'];
$tahun = $today['year'];
$jam = $today['hours']+7;
$menit = $today['minutes'];
$detik = $today['seconds'];


$hariini = $tanggal."-".$bulan."-".$tahun." ".$jam.":".$menit.":".$detik;

echo $hariini;


require_once 'class.sicyca.php';

$sicyca = new sicyca();
$sicyca->send($hariini);
//$sicyca->harini();
?>
