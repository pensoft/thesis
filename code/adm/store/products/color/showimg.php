<?php

require_once(getenv("DOCUMENT_ROOT") . "/lib/static.php");

$f = basename(s($_GET['filename']));

$i = new cgetimage(array(
	'fname' => $f,
));

$i->Display();

?>