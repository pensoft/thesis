<?php
$docroot = getenv('DOCUMENT_ROOT');
$gDontRedirectToLogin = 1;
require_once($docroot . '/lib/static.php');
ini_set('display_errors', 'off');

$lInstanceId = (int)$_REQUEST['instance_id'];



header("Pragma: public");
header("Expires: 0"); 
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
try{
	$lCon = new DBCn();
	$lCon->Open();
	$lSql = '
		SELECT i.id, v1.value_int as figure_num, i.document_id
		FROM pwt.document_object_instances i
		JOIN pwt.instance_field_values v ON v.instance_id = i.id AND v.field_id = ' . (int)FIGURE_TYPE_FIELD_ID . '
		JOIN pwt.instance_field_values v1 ON v1.instance_id = i.id AND v1.field_id = ' . (int)FIGURE_NUM_FIELD_ID . ' 
		WHERE i.id = ' . (int) $lInstanceId . ' AND i.object_id = ' . FIGURE_OBJECT_ID . ' AND i.is_confirmed = true AND v.value_int = ' . (int)FIGURE_TYPE_PLATE_ID . '
	';
	$lCon->Execute($lSql);
	if(!(int)$lCon->mRs['id']){
		throw new Exception(getstr('pwt.noSuchPlate'));		
	}
	$lPicExt = '.jpg';
	$lFigureNum = (int)$lCon->mRs['figure_num'];
	$lDocumentId = (int)$lCon->mRs['document_id'];
	$lSql = '
		SELECT m.id, spGetPlatePartLetter(i.id) as plate_number
		FROM pwt.document_object_instances p
		JOIN pwt.document_object_instances i ON i.document_id = p.document_id AND p.pos = substring(i.pos, 1, char_length(p.pos))
		JOIN pwt.instance_field_values v ON v.instance_id = i.id AND v.field_id = ' . (int)PLATE_PIC_ID_FIELD_ID . '
		JOIN pwt.media m ON m.id = v.value_int 
		WHERE p.id = ' . (int) $lInstanceId . ' AND i.is_confirmed = true  	
	';
	
	$lCon->Execute($lSql);
	$lFiles = array();
	while(!$lCon->Eof()){
		$lFilePath = PATH_DL . 'big_' . $lCon->mRs['id'] . $lPicExt;
		if(file_exists($lFilePath)){
			$lFiles[] = array(
				'path' => $lFilePath,
				'name' => $lFigureNum . '_' . $lCon->mRs['plate_number'] . $lPicExt,
			);
		}
		$lCon->MoveNext();
	}
	
	if(!count($lFiles)){
		throw new Exception(getstr('pwt.plateHasNoImages'));		
	}
	
	$lTempFile = tempnam(sys_get_temp_dir(), 'plate_');
	if($lTempFile === false){
		throw new Exception(getstr('pwt.couldNotCreateTempFile'));
	}
	$lCreateZip = create_zip($lFiles, $lTempFile, true);
	$lZipContents = file_get_contents($lTempFile);
	unlink($lTempFile);
	if(!$lCreateZip || $lZipContents === false){		
		throw new Exception(getstr('pwt.couldNotCreateZipFile'));
	}
	
	header("Content-Type: application/zip");
	header('Content-Disposition: attachment; filename="fig_' . $lDocumentId . '_' . $lFigureNum . '.zip"');
	echo $lZipContents;
}catch(Exception $pException){
	echo $pException->getMessage();
	exit;
}



/* creates a compressed zip file */
function create_zip($pFiles = array(),$pDestination = '',$pOverwrite = false) {
	//if the zip file already exists and overwrite is false, return false
	if(file_exists($pDestination) && !$pOverwrite) { return false; }
	//vars
	$lValidFiles = array();
	//if files were passed in...
	if(is_array($pFiles)) {
		//cycle through each file
		foreach($pFiles as $lFile) {
			//make sure the file exists
			if(file_exists($lFile['path'])) {
				$lValidFiles[] = $lFile;
			}
		}
	}
	//if we have good files...
	if(count($lValidFiles)) {
		//create the archive
		$lZip = new ZipArchive();
		if($lZip->open($pDestination,$pOverwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		}
		//add the files
		foreach($lValidFiles as $lFile) {
			$lZip->addFile($lFile['path'], $lFile['name']);
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
		
		//close the zip -- done!
		$lZip->close();
		
		//check to make sure the file exists
		return file_exists($pDestination);
	}
	else
	{
		return false;
	}
}

?>