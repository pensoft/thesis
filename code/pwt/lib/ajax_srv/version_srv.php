<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . '/lib/static.php');
// ini_set('display_errors', 'off');

session_write_close();

$gAction = $_REQUEST ['action'];
$gDocumentId = $_REQUEST ['document_id'];
$lResult = array (
		'err_cnt' => 0,
		'err_msg' => '' 
);

try {
	if (! checkIfPreviewCanBeEdited($gDocumentId) && $gAction != 'get_version_user_display_names') {
		throw new Exception(getstr('pwt.theCurrentUserCannotEditTheDocumentAtThisPoint'));
	}
	$lCon = new DBCn();
	$lCon->Open();
	switch ($gAction) {
		default :
			throw new Exception(getstr('pwt.unknownAction'));
			break;
		case 'get_version_user_display_names' :
			$lResult ['result'] = GetVersionUserDisplayNames(0, $gDocumentId);
			break;
		case 'accept_all_changes' :
			AcceptRejectAllChanges($gDocumentId);
			break;
		case 'reject_all_changes' :
			AcceptRejectAllChanges($gDocumentId, false);
			break;
		case 'save_version_change' :
			$gFieldId = ( int ) $_REQUEST ['field_id'];
			$gInstanceId = ( int ) $_REQUEST ['instance_id'];
			$gContent = $_REQUEST ['content'];
			// Store the value of the field in the request because cdocument saver works with $_REQUEST
			$_REQUEST [$gInstanceId . INSTANCE_FIELD_NAME_SEPARATOR . $gFieldId] = $gContent;
			$lSaveDocument = new cdocument_saver(array (
					'document_id' => $gDocumentId,
					'root_instance_id' => $gInstanceId,
					'instance_ids' => array (
							$gInstanceId 
					),
					'auto_save_on' => true,
					'explicit_field_id' => $gFieldId 
			));
			$lSaveDocument->GetData();
			if ($lSaveDocument->HasErrors()) {
				throw new Exception($lSaveDocument->GetErrorMsg());
			}
			break;
		case 'save_fig_caption_change' :
			$gFigId = ( int ) $_REQUEST ['fig_id'];
			$gPlateNum = ( int ) $_REQUEST ['plate_num'];
			$gIsPlate = ( int ) $_REQUEST ['is_plate'];
			$gContent = $_REQUEST ['content'];
			SaveFigCaption($gDocumentId, $gFigId, $gIsPlate, $gPlateNum, $gContent);
			break;
		case 'save_table_change' :
			$gTableId = ( int ) $_REQUEST ['table_id'];
			$gModifiedElementIsTitle = ( int ) $_REQUEST ['modified_element_is_title'];
			$gContent = $_REQUEST ['content'];
			SaveTableChange($gDocumentId, $gTableId, $gModifiedElementIsTitle, $gContent);
			break;
	}
} catch ( Exception $pException ) {
	$lResult ['err_cnt'] = 1;
	$lResult ['err_msg'] = $pException->getMessage();
}

displayAjaxResponse($lResult);
?>