<?php
$gDontRedirectToLogin = 1;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
require_once($docroot . '/registration_flds.php');
ini_set('display_errors', 'off');

$gAction = $_REQUEST['action'];
$gFormName = $_REQUEST['form_name'];
$gFigPlateVal = $_REQUEST['plate_val'];
$lPlateId = (int)$_REQUEST['plate_id'];
$gDocId = (int)$_REQUEST['document_id'];
$lPhotoId = (int)$_REQUEST['photo_id'];
$lPhotoTitle = $_REQUEST['photo_title'];
$lTableTitle = $_REQUEST['table_title'];
$lTableDesc = $_REQUEST['table_desc'];
$lPlateDesc = $_REQUEST['plate_desc'];
$lEdit = (int)$_REQUEST['edit'];


checkIfDocumentIsLockedByTheCurrentUserForAjax(0, $gDocId);

$lResult = array(
	'err_msg' => '',
	'html' => '',
);

switch($gAction){
	default:
	case 'get_figures_form':{

			$lInCitationPopUp = (int)$_REQUEST['citation'];
			$lFigForm = new csimple(
				array(
					'ctype' => 'csimple',
					'document_id' => $gDocId,
					'photo_id' => $lPhotoId,
					'plate_id' => $lPlateId,
					'edit' => $lEdit,
					'citation' => $lInCitationPopUp,
					'templs' => array(
						G_DEFAULT => 'figures.figures_form_' . $gFormName,
					),
				)
			);
			$lResult['html'] = $lFigForm->Display();
			displayAjaxResponse($lResult);
		}
		break;
	case 'get_plate_apperance':{
			$lLimit = 0;
			if($gFigPlateVal == 1 || $gFigPlateVal == 2)
				$lLimit = 2;
			elseif($gFigPlateVal == 3)
				$lLimit = 4;
			elseif($gFigPlateVal == 4)
				$lLimit = 6;
			if($lPlateId) {
				$lFigForm = new crs(
					array('ctype'=>'crs',
						'document_id' => $gDocId,
						'plate_val' => $gFigPlateVal,
						'rand' => rand(1, 100),
						'templs'=>array(
							G_HEADER=>'figures.plate_' . $gFigPlateVal . '_head', G_ROWTEMPL=>'figures.plate_' . $gFigPlateVal . '_row', G_FOOTER =>'figures.plate_' . $gFigPlateVal . '_foot', G_NODATA =>'global.empty',
						),
						'sqlstr'=>'SELECT m.id as picid,
										  m.description as description
							FROM pwt.media m
							JOIN pwt.plates p ON p.id = m.plate_id
							WHERE p.id = ' . (int)$lPlateId . ' AND m.ftype = 0
							ORDER BY m.position
							LIMIT ' . $lLimit,
					)
				);
				$lFigForm->GetData();
				$lResult['html'] = $lFigForm->Display();
				displayAjaxResponse($lResult);
			} else {
				$lFigForm = new csimple(
					array(
						'ctype' => 'csimple',
						'document_id' => $gDocId,
						'plate_val' => $gFigPlateVal,
						'templs' => array(
							G_DEFAULT => 'figures.plate_' . $gFigPlateVal,
						),
					)
				);
				$lResult['html'] = $lFigForm->Display();
				displayAjaxResponse($lResult);
			}
		}
		break;
	case 'save_pic_details':{
			savePhotoTitle($lPhotoId, $lPhotoTitle);
		}
		break;
	case 'save_pic_description':{
			savePhotoDesc($lPhotoId, $lPhotoTitle);
		}
		break;
	case 'save_plate_photo_details':{
			if($lPhotoId) {
				savePhotoDesc($lPhotoId, $lPlateDesc);
				echo $lPhotoId;
			} elseif($lPlateId) {
				savePlateDetails($lPlateDesc, $lPlateId);
				echo $lPlateId;
			}
		}
		break;
	case 'update_plate_type':{
			//~ trigger_error($gDocId . '-' . $lPlateId . '-' . $gFigPlateVal, E_USER_NOTICE);
			updatePlateVal($gDocId, $lPlateId, $gFigPlateVal);
		}
		break;
	case 'save_table_details':{
			$lTableId = (int)$_REQUEST['table_id'];
			if($lTableId) {
				$lResult = saveTableData($gDocId, $lTableTitle, $lTableDesc, $lTableId);
			} else {
				$lResult = saveTableData($gDocId, $lTableTitle, $lTableDesc);
			}
			displayAjaxResponse($lResult);
		}
		break;
	case 'show_table_popup':{
			$lTableId = (int)$_REQUEST['table_id'];
			$lDocumentId = (int)$_REQUEST['document_id'];
			$lShowInCitation = (int)$_REQUEST['show_in_citation'];
			if($lTableId) {
				$lTableRow = new crs(
					array('ctype'=>'crs',
						'templs'=>array(
							G_HEADER=>'', G_ROWTEMPL=>'tables.table_row_popup', G_FOOTER =>'', G_NODATA =>'',
						),
						'sqlstr'=>'SELECT id, document_id, title, description, move_position
									FROM pwt.tables WHERE id = ' . (int) $lTableId. '
									LIMIT 1',
					)
				);
				$lTableRow->GetData();
				$lResult['html'] = $lTableRow->Display();
				displayAjaxResponse($lResult);
			}elseif($lDocumentId){
				if($lShowInCitation)
					$lTemplate = 'tables.tables_popup_inpopup';
				else
					$lTemplate = 'tables.table_row_popup';
				$lTableRow = new crs(
					array('ctype'=>'crs',
						'templs'=>array(
							G_HEADER => $lTemplate, G_ROWTEMPL => '', G_FOOTER => '', G_NODATA => '',
						),
						'sqlstr'=> '',
						'document_id' => $lDocumentId,
						'id' => 0
					)
				);
				$lTableRow->GetData();
				$lResult['html'] = $lTableRow->Display();
				displayAjaxResponse($lResult);
			}
		}
		break;
	case 'update_table_holder':{
			$lTableId = (int)$_REQUEST['table_id'];
			$lCurPos = (int)$_REQUEST['curr_position'];
			$lMinPos = (int)$_REQUEST['min_position'];
			$lMaxPos = (int)$_REQUEST['max_position'];
			$lFullHtml = (int)$_REQUEST['full_html'];

			if(!$lMaxPos || !$lMinPos || !$lCurPos){
				$lCon = new DBCn();
				$lCon->Open();
				$lDocumentId = 0;
				$lSql = '';
				$lSql = 'SELECT * FROM pwt.tables WHERE id = ' . (int)$lTableId . '
				';
				$lCon->Execute($lSql);
				$lCurPos = (int)$lCon->mRs['move_position'];
				$lDocumentId = (int)$lCon->mRs['document_id'];

				$lSql = '
				SELECT min(move_position) as min, max(move_position) as max
				FROM pwt.tables
				WHERE document_id = ' . $lDocumentId . ';
				';
				$lCon->Execute($lSql);
				$lMinPos = (int)$lCon->mRs['min'];
				$lMaxPos = (int)$lCon->mRs['max'];
			}

			if($lTableId) {
				if ($lFullHtml){
					$lTempl = 'tables.single_ajax_row';
				}else{
					$lTempl = 'tables.single_ajax_row_notr';
				}

				$lTableRow = new crs(array(
					'ctype' => 'crs',
					'templs' => array(
						G_HEADER => '',
						G_ROWTEMPL => $lTempl,
						G_FOOTER => '',
						G_NODATA => ''
					),
					'curr_position' => $lCurPos,
					'max_position' => $lMaxPos,
					'min_position' => $lMinPos,
					'sqlstr' => 'SELECT * FROM pwt.tables WHERE id = ' . (int) $lTableId . ' LIMIT 1'
				));
				$lTableRow->GetData();
				$lResult['html'] = $lTableRow->Display();
				displayAjaxResponse($lResult);
			}
		}
		break;
	case 'update_figures_holder':{
			$lCurPos = (int)$_REQUEST['curr_position'];
			$lMinPos = (int)$_REQUEST['min_position'];
			$lMaxPos = (int)$_REQUEST['max_position'];

			if(!$lMaxPos || !$lMinPos || !$lCurPos){
				$lCon = new DBCn();
				$lCon->Open();
				$lDocumentId = 0;
				$lSql = '';
				if(($lPhotoId))
					$lSql = 'SELECT	move_position, document_id
						FROM pwt.media
						WHERE id = ' . (int)$lPhotoId . '
					';
				else{
					$lSql = 'SELECT	max(move_position) as move_position, max(document_id) as document_id
					FROM pwt.media
					WHERE plate_id = ' . (int)$lPlateId . '
					';
				}
				$lCon->Execute($lSql);
				$lCurPos = (int)$lCon->mRs['move_position'];
				$lDocumentId = (int)$lCon->mRs['document_id'];

				$lSql = '
					SELECT min(move_position) as min, max(move_position) as max
					FROM pwt.media
					WHERE document_id = ' . $lDocumentId . ';
				';
				$lCon->Execute($lSql);
				$lMinPos = (int)$lCon->mRs['min'];
				$lMaxPos = (int)$lCon->mRs['max'];
			}

			if((int)$lPhotoId || (int)$lPlateId) {
				if ((int)$_REQUEST['append'])
					$lTempl = 'figures.single_ajax_row';
				else
					$lTempl = 'figures.single_ajax_row_notr';
				$lPlateRow = new crs(
					array('ctype'=>'crs',
						'templs'=>array(
							G_HEADER=>'', G_ROWTEMPL=>$lTempl, G_FOOTER =>'', G_NODATA =>'',
						),
						'curr_position' => $lCurPos,
						'max_position' => $lMaxPos,
						'min_position' => $lMinPos,
						'sqlstr'=>'SELECT
										m.id as photo_id,
										m.document_id,
										m.plate_id,
										m.ftype,
										m.link,
										null as format_type,
										null as photo_ids_arr,
										null as photo_positions_arr,
										m.title as photo_title,
										m.description as photo_desc,
										m.position,
										m.move_position,
										null as plate_desc,
										null as plate_title
									FROM pwt.media m
									WHERE m.plate_id IS NULL AND m.id = ' . ((int) $lPhotoId ? (int)$lPhotoId : (int)$lPlateId) . '
								UNION
									SELECT
										null as photo_id,
										max(m.document_id) as document_id,
										m.plate_id,
										0 as ftype,
										null as link,
										max(p.format_type) as format_type,
										array_agg(m.id) as photo_ids_arr,
										array_agg(m.position) as photo_positions_arr,
										null as photo_title,
										null as photo_desc,
										null as position,
										max(m.move_position) as move_position,
										max(p.description) as plate_desc,
										max(p.title) as plate_title
									FROM pwt.media m
									JOIN pwt.plates p ON p.id = m.plate_id
											WHERE p.id = ' . ((int) $lPhotoId ? (int)$lPhotoId : (int)$lPlateId) . '
											GROUP BY m.plate_id
									LIMIT 1',
					)
				);
				$lPlateRow->GetData();
				$lResult['html'] = $lPlateRow->Display();
				displayAjaxResponse($lResult);
			}
		}
		break;
	case 'delete_plate_photo': {
			if(((int)$lPhotoId || (int)$lPlateId) && (int)$gDocId) {
				$lRes = DeleteFigure($gDocId, $lPlateId, $lPhotoId);

				$lResult['result'] = $lRes['result'];
				$lResult['max_position'] = $lRes['max_position'];
				$lResult['min_position'] = $lRes['min_position'];
				$lResult['curr_position'] = $lRes['curr_position'];

				$lFigures = new crs(
					array('ctype'=>'crs',
						'document_id' => (int)$gDocId,
						'templs'=>array(
							G_HEADER=>'global.empty', G_ROWTEMPL=>'figures.document_figures_row', G_FOOTER =>'global.empty', G_NODATA =>'global.empty',
						),
						'max_position' => $lRes['max_position'],
						'min_position' => $lRes['min_position'],
						'sqlstr'=>'
							(SELECT
									m.id as photo_id,
									m.document_id,
									m.plate_id,
									m.ftype,
									m.link,
									null as format_type,
									null as photo_ids_arr,
									null as photo_positions_arr,
									m.title as photo_title,
									m.description as photo_desc,
									m.position,
									m.move_position,
									null as plate_desc,
									null as plate_title,
									m.lastmod
								FROM pwt.media m
								WHERE m.plate_id IS NULL AND m.document_id = ' . (int)$gDocId . ' AND m.move_position >= ' . (int)$lResult['curr_position'] . ' - 1
							UNION
							SELECT
									null as photo_id,
									max(m.document_id) as document_id,
									m.plate_id,
									0 as ftype,
									null as link,
									max(p.format_type) as format_type,
									array_agg(m.id) as photo_ids_arr,
									array_agg(m.position) as photo_positions_arr,
									null as photo_title,
									null as photo_desc,
									null as position,
									max(m.move_position),
									max(p.description) as plate_desc,
									max(p.title) as plate_title,
									max(p.lastmod) as lastmod
								FROM pwt.media m
								JOIN pwt.plates p ON p.id = m.plate_id
								WHERE m.document_id = ' . (int)$gDocId . ' AND m.move_position >= ' . (int)$lResult['curr_position'] . ' - 1
								GROUP BY m.plate_id
								)
							ORDER BY move_position ASC
						',
						//'pagesize' => 10,
					)
				);
				$lFigures->GetData();
				$lFiguresData = $lFigures->Display();

				$lResult['html'] = $lFiguresData;

				displayAjaxResponse($lResult);
			}
		}
		break;
	case 'delete_table': {
			$lTableId = (int)$_REQUEST['table_id'];
			if((int)$lTableId && (int)$gDocId) {
				$lResult = DeleteTable($gDocId, $lTableId);
				displayAjaxResponse($lResult);
			}
		}
		break;
	case 'move_table': {
			$lTableId = (int)$_REQUEST['table_id'];
			if($lTableId && (int)$gDocId) {
				$lDirection = (int)$_REQUEST['direction'];
				$lPosition = (int)$_REQUEST['position'];
				$lRes = MoveTable($lDirection, $gDocId, $lTableId);
				$lResult['html'] = $lRes['result'];
				$lResult['max_position'] = $lRes['max_position'];
				$lResult['min_position'] = $lRes['min_position'];
				$lResult['curr_position'] = $lRes['curr_position'];
				$lResult['new_position'] = $lRes['new_position'];
				displayAjaxResponse($lResult);
			}
		}
		break;
	case 'move_plate_photo': {
			if(((int)$lPhotoId || (int)$lPlateId) && (int)$gDocId) {
				$lDirection = (int)$_REQUEST['direction'];
				$lPosition = (int)$_REQUEST['position'];
				$lPlateFlag = (int)$_REQUEST['plate_flag'];
				$lRes = MoveFigure($lDirection, $gDocId, $lPhotoId, $lPosition, $lPlateFlag);
				$lResult['html'] = $lRes['result'];
				$lResult['max_position'] = $lRes['max_position'];
				$lResult['min_position'] = $lRes['min_position'];
				$lResult['curr_position'] = $lRes['curr_position'];
				$lResult['new_position'] = $lRes['new_position'];
				displayAjaxResponse($lResult);
			}
		}
		break;
	case 'save_video_details': {
			if($_REQUEST['video_url']) {
				$lVideoId = (int)$_REQUEST['video_id'];
				$lResult = saveVideoDetails((int)$lVideoId, $_REQUEST['video_url'], $_REQUEST['video_title'], $gDocId);
				displayAjaxResponse($lResult);
			}
		}
		break;
}

?>