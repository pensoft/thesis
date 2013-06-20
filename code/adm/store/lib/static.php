<?php

$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/conf.php');
require_once($docroot . '/lib/static.php');


function CKIPADDR($pFld) {
	return array('Expr' => '!ip2long(gethostbyname(' . $pFld . '))', 'ErrStr' => 'CKIPADDR');
}

?>