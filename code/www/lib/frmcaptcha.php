<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$t = array(
	'imgsize' => '130x45',
	'fontsize' => 30,
	'symb' => 5,
	'sessnum' => 3,
	'sessname' => 'frmcapt',
	'bgcolor' => 'silver',
);

$captcha = new ccaptcha($t);
$captcha->Display();

?>