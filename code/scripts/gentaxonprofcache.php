<?php
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 'On');
ini_set('log_errors', 'On');
if ($argc!=4) 
	trigger_error("Error! Ussage: " .$argv[0]." <file with taxon names> <baseurl including {taxon_name}> <0 dont delete file or 1 delete file>", E_USER_ERROR);

trigger_error($argv[0]." start processing file ".$argv[1], E_USER_NOTICE);
$taxstr=file_get_contents($argv[1]);
$taxarr=explode("\n", $taxstr);
$urlarr=array();
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output="";
foreach($taxarr as $key => $val) {
	$urlarr[$key]=str_replace("{taxon_name}", urlencode($val), $argv[2]);
	trigger_error($argv[0]." fetching ".$urlarr[$key], E_USER_NOTICE);
	curl_setopt($ch, CURLOPT_URL, $urlarr[$key]);
	$output = curl_exec($ch);
}
curl_close($ch);

if ($argv[3]==1) {
	$lstr=sys_get_temp_dir();
	if (!strncmp( $lstr  ,  $argv[1]  ,  strlen($lstr))) {
		unlink($argv[1]);
	} else 
		trigger_error($argv[0]." can't delete file ".$argv[1]." because the file is not in tempory directory - ".$lstr, E_USER_NOTICE);
}
trigger_error($argv[0]." end processing file ".$argv[1], E_USER_NOTICE);
?>