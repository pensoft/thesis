<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
require_once PATH_CLASSES . 'comments.php';
ini_set('display_errors', 'off');

$lInstanceId = (int)$_REQUEST['instance_id'];
$lFieldId = (int)$_REQUEST['field_id'];
$lCommentId = (int)$_REQUEST['comment_id'];
$lPositionType = (int)$_REQUEST['position_fix_type'];
$lFieldHtmlValue = $_REQUEST['field_html_value'];

$lCon = new DBCn();
$lCon->Open();
$lSql = '
	SELECT value_str
	FROM pwt.instance_field_values
	WHERE instance_id = ' . (int)$lInstanceId  . ' AND field_id = ' . (int)$lFieldId . '
';
$lCon->Execute($lSql);
$lFieldRealValue = $lCon->mRs['value_str'];
$lRealPos = CalculateCommentRealPosition($lFieldHtmlValue, $lFieldRealValue, $lCommentId, $lPositionType == COMMENT_START_POS_TYPE);

$lSql = 'UPDATE pwt.msg SET
	' . ($lPositionType == COMMENT_START_POS_TYPE ? 'start_offset' : 'end_offset') . ' = ' . (int)$lRealPos . '
WHERE id = ' . (int)$lCommentId;
$lCon->Execute($lSql);

displayAjaxResponse($lResult);
?>