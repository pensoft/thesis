<?php

require_once(getenv("DOCUMENT_ROOT") . "/lib/conf.php");

$lFileName = PATH_STORIES . $_REQUEST['filename'];
if(is_file($lFileName)){
	echo file_get_contents($lFileName);
}


?>