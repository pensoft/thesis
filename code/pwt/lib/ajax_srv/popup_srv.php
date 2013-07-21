<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
ini_set('display_errors', 'off');

$gAction = $_REQUEST['action'];
$gParentInstanceId = (int)$_REQUEST['parent_instance_id'];
$gObjectId = (int)$_REQUEST['object_id'];
$gInstanceId = (int)$_REQUEST['instance_id'];

checkIfDocumentIsLockedByTheCurrentUserForAjax($gParentInstanceId, 0);
switch($gAction){
	case 'create_new_popup':
		$lCon = new DBCn();
		$lCon->Open();
		$lSql = 'SELECT i.id, p.popup_template, i.document_id, dto.display_name, p.eval_code
			FROM pwt.document_object_instances i
			JOIN pwt.document_template_objects dto ON dto.parent_id = i.document_template_object_id AND dto.object_id = ' . (int)$gObjectId . '
			LEFT JOIN pwt.template_object_custom_creation_popup p ON p.template_object_id = dto.template_object_id
			WHERE i.id = ' . (int)$gParentInstanceId .'
		';
		$lCon->Execute($lSql);
		if(!$lCon->mRs['id']){
			$lResult = array(
				'err_cnt' => 1,
				'err_msg' => getstr('pwt.instance.thisInstanceCantHaveSuchSubobjects'),
			);
			displayAjaxResponse($lResult);
		}
		$lTemplateName = $lCon->mRs['popup_template'];
		$lDocumentId = (int)$lCon->mRs['document_id'];
		$lDisplayName = $lCon->mRs['display_name'];
		$lEvalCode = $lCon->mRs['eval_code'];

		$lResult = array(
			'err_cnt' => 0,
			'err_msg' => '',
		);
		if(!$lTemplateName){
			if($gObjectId == AUTHOR_OBJECT_ID || $gObjectId == CONTRIBUTOR_OBJECT_ID){
				$lTemplateName = 'popup.default_popup_with_margin';
			}else{
				$lTemplateName = 'popup.default_popup';
			}
			//За стандартните попъпи създаваме обекта и после го показваме
			if(!$lCon->Execute('BEGIN TRANSACTION')){
				$lResult = array(
					'err_cnt' => 1,
					'err_msg' => getstr('pwt.canNotStartTransaction'),
				);
				displayAjaxResponse($lResult);
			}
			$lSql = 'SELECT * FROM spCreateNewInstance(' . (int)$gParentInstanceId . ', ' . (int) $gObjectId . ', ' . (int)$user->id . ');';

			if ($lCon->Execute($lSql)) {
				$lResult['new_instance_id'] = $lCon->mRs['new_instance_id'];
				$lResult['parent_instance_id'] = (int)$lCon->mRs['parent_instance_id'];
				$lResult['display_in_tree'] = (int)$lCon->mRs['display_in_tree'];
				$lResult['container_id'] = (int)$lCon->mRs['container_id'];

				if(!$lCon->Execute('SELECT * FROM pwt.spMarkInstanceAsUnconfirmed(' . (int)$lResult['new_instance_id'] . ', ' . (int)$user->id . ')')){
					$lResult = array(
						'err_cnt' => 1,
						'err_msg' => getstr($lCon->GetLastError()),
					);
					displayAjaxResponse($lResult);
				}

				if(!$lCon->Execute('COMMIT TRANSACTION')){
					$lResult = array(
						'err_cnt' => 1,
						'err_msg' => getstr('pwt.canNotCommitTransaction'),
					);
					displayAjaxResponse($lResult);
				}

				$lInstance = new cdocument_instance(array(
					'templs' => getDocumentInstanceDefaultTempls(),
					'field_templs' => getDocumentFieldDefaultTempls(),
					'container_templs' => getDocumentContainerDefaultTempls(),
					'custom_html_templs' => getDocumentCustomHtmlItemsDefaultTempls(),
					'action_templs' => getDocumentActionsDefaultTempls(),
					'tabbed_element_templs' => getDocumentTabbedElementDefaultTempls(),
					'mode' => (int) INSTANCE_EDIT_MODE,
					'level' => 1,
					'instance_id' => $lResult['new_instance_id'],
					'root_instance_id' => $lResult['new_instance_id'],
					'display_in_tree' => (int)$lResult['display_in_tree'],
					'display_unconfirmed_objects' => 1,
					'get_data_from_request' => FALSE,
				));
// 				$lInstance->m_displayTitleAndTopActions = 0;
// 				$lInstance->m_displayDefaultActions = 0;
				$lResultHtml = new csimple(array(
					'popup_content' => $lInstance->Display(),
					'document_id' => $lDocumentId,
					'instance_id' => $lResult['new_instance_id'],
					'popup_title' => $lDisplayName,
					'parent_instance_id' => $lResult['parent_instance_id'],
					'container_id' => $lResult['container_id'],
					'root_instance_id' => $lResult['new_instance_id'],
					'display_in_tree' => (int)$lResult['display_in_tree'],
					'templs' => array(
						G_DEFAULT => $lTemplateName,
					),
				));
				$lResult['html'] = $lResultHtml->Display();


			}else{
				$lResult['err_cnt']++;
				$lResult['err_msg'] = getstr($lCon->GetLastError());
				displayAjaxResponse($lResult);
			}
		}else{
			$lParametersArray = array(
				'document_id' => $lDocumentId,
				'popup_title' => $lDisplayName,
				'templs' => array(
						G_DEFAULT => $lTemplateName,
				),
			);
			if($lEvalCode != ''){
				eval($lEvalCode);
			}
			//При нестандартните попъпи - директно показваме темплейта. Там ще трябва да се избират някои полета
			//Преди да се създаде обекта
			$lResultHtml = new csimple($lParametersArray);
			$lResult['html'] = $lResultHtml->Display();
		}
		displayAjaxResponse($lResult);
		break;
	case 'open_edit_popup':
		$lCon = new DBCn();
		$lCon->Open();
		$lSql = 'SELECT i.id, p.popup_template, i.document_id, i.display_name, p.eval_code
			FROM pwt.document_object_instances i
			JOIN pwt.document_template_objects dto ON dto.id = i.document_template_object_id
			LEFT JOIN pwt.template_object_custom_edit_popup p ON p.template_object_id = dto.template_object_id
			WHERE i.id = ' . (int)$gInstanceId .'
		';
		$lCon->Execute($lSql);
		if(!$lCon->mRs['id']){
			$lResult = array(
				'err_cnt' => 1,
				'err_msg' => getstr('pwt.instance.noSuchInstance'),
			);
			displayAjaxResponse($lResult);
		}

		$lTemplateName = $lCon->mRs['popup_template'];
		$lDocumentId = (int)$lCon->mRs['document_id'];
		$lDisplayName = $lCon->mRs['display_name'];
		$lEvalCode = $lCon->mRs['eval_code'];

		if(!$lTemplateName){
			$lTemplateName = 'popup.default_edit_popup';
		}
//
// 		$lDocumentId = (int)$lCon->mRs['document_id'];
// 		$lDisplayName = 'Edit Reference';

		$lResult = array(
			'err_cnt' => 0,
			'err_msg' => '',
		);

		$lResult['new_instance_id'] = $gInstanceId;
		$lResult['parent_instance_id'] = (int)$gParentInstanceId;

		$lInstance = new cdocument_instance(array(
			'templs' => getDocumentInstanceDefaultTempls(),
			'field_templs' => getDocumentFieldDefaultTempls(true),
			'container_templs' => getDocumentContainerDefaultTempls(),
			'custom_html_templs' => getDocumentCustomHtmlItemsDefaultTempls(),
			'action_templs' => getDocumentActionsDefaultTempls(),
			'tabbed_element_templs' => getDocumentTabbedElementDefaultTempls(),
			'mode' => (int) INSTANCE_EDIT_MODE,
			'level' => 1,
			'instance_id' => $lResult['new_instance_id'],
			'root_instance_id' => $lResult['new_instance_id'],
			'display_in_tree' => (int)$lResult['display_in_tree'],
			'get_data_from_request' => FALSE,
		));
		$lInstance->m_displayTitleAndTopActions = 0;
		$lInstance->m_displayDefaultActions = 0;
		
		$lParametersArray = array(
			'popup_content' => $lInstance->Display(),
			'document_id' => $lDocumentId,
			'instance_id' => $lResult['new_instance_id'],
			'popup_title' => $lDisplayName,
			'parent_instance_id' => $lResult['parent_instance_id'],
			//'container_id' => $lResult['container_id'],
			'root_instance_id' => $lResult['new_instance_id'],
			//'display_in_tree' => (int)$lResult['display_in_tree'],
			'templs' => array(
				G_DEFAULT => $lTemplateName,
			),
		);			
		if($lEvalCode != ''){
			eval($lEvalCode);
		}
		
		$lResultHtml = new csimple($lParametersArray);
		$lResult['html'] = $lResultHtml->Display();
		

		displayAjaxResponse($lResult);
		break;
}








?>