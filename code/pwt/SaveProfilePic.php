<?php
$gDontRedirectToLogin = 1;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
session_write_close();

$timelimit = (int)UPLOAD_TIME_LIMIT;
if (!$timelimit) 
	$timelimit = 200;
set_time_limit($timelimit);

if(isset($_FILES['uploadfile'])) {
	$lPicId = UploadPic('uploadfile', PATH_PWT_DL, 0, (int)$_REQUEST['userid'] , $lError, 1);
}

if($lPicId) {
	echo '<img class="P-Prof-Pic" src="/showimg.php?filename=c67x70y_' . (int)$lPicId . '.jpg"></img>';
} else {
	echo '<img src="i/add_photo.png"></img>';
}
exit;
//~ $inst = new cpage(array_merge($t, DefObjTempl()), array(G_MAINBODY => 'global.userpicfrm'));
//~ $inst->Display();
?>
