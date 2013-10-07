<?php
// Disable error reporting because it can break the json output
// ini_set('error_reporting', 'off');

class cComment_Ajax_Srv extends cBase_Controller {
	var $m_errCnt = 0;
	var $m_errMsgs = array();
	var $m_action;
	var $m_action_result;
	var $m_commentsModel;
	var $m_tempPageView;
	function __construct() {
		parent::__construct();
		$pViewPageObjectsDataArray = array();
		$this->m_action = $this->GetValueFromRequestWithoutChecks('action');
		$this->m_action_result = array();
		$this->m_commentsModel = new mComments();
		$this->m_tempPageView = new pPage_Comments_Ajax_View();

		switch ($this->m_action) {
			default :
				$this->m_errCnt ++;
				$this->m_errMsgs[] = array(
					'err_msg' => getstr('pjs.unrecognizedAction')
				);
				break;
			case 'new_comment' :
				$this->CreateNewComment();
				break;
			case 'edit_comment' :
				$this->EditComment();
				break;
			case 'new_reply' :
				$this->CreateNewReply();
				break;
			case 'recalculate_position' :
				$this->RecalculateCommentPosition();
				break;
			case 'delete_comment' :
				$this->DeleteComment();
				break;
			case 'resolve_comment' :
				$this->ResolveComment();
				break;
			case 'get_filtered_ids_list':
				$this->GetVersionFilteredRootIdsList();
				break;

		}

		$lResultArr = array_merge($this->m_action_result, array(
			'err_cnt' => $this->m_errCnt,
			'err_msgs' => $this->m_errMsgs
		));
		// var_dump($lResultArr);
		$this->m_pageView = new pPage_Comments_Ajax_View(&$lResultArr);
	}

	function CreateNewComment() {
		global $user;
		$lVersionId = (int) $this->GetValueFromRequestWithoutChecks('version_id');
		if(! $lVersionId){
			$lErrMsg = 'pjs.mustSpecifyVersionId';
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => getstr($lErrMsg)
			);
			return;
		}
		$lVersionCommentsNum = $this->m_commentsModel->GetVersionCommentsNum($lVersionId);
		$lForm = new New_Comment_Form_Wrapper(array(
			'page_controller_instance' => $this,
			'name_in_viewobject' => 'new_comment_form',
			'view_object' => $this->m_pageView,
			'use_captcha' => 0,
			'm_debug' => false,
			'uid' => $user->id,
			'user_fullname' => $user->fullname,
			'version_id' => $lVersionId,
			'form_method' => 'POST',
			'form_name' => 'newCommentForm'
		));
		$lCommentId = $lForm->GetFieldValue('comment_id');

		if($lForm->GetErrorCount() || $lForm->GetCurrentAction() != 'save' || ! $lCommentId){
			if($lForm->GetErrorCount()){
// 				$lErrMsg= $lForm->				
				$lFormGlobalErrors = $lForm->GetFormGlobalErrors();
				$lErrMsg = $lFormGlobalErrors[0];
				if(!$lErrMsg){
					$lErrMsg = 'pjs.couldNotCreateComment';
				}
			}elseif($lForm->GetCurrentAction() != 'save'){
				$lErrMsg = 'pjs.wrongFormAction';
			}elseif(! $lCommentId){
				$lErrMsg = 'pjs.couldNotGetCommentId';
			}
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => getstr($lErrMsg)
			);
			$this->m_action_result['err_msg'] = getstr($lErrMsg);
			return;
		}
		$this->m_action_result['comment_id'] = $lCommentId;

		$this->m_action_result['start_instance_id'] = $lForm->GetFieldValue('start_instance_id');
		$this->m_action_result['start_field_id'] = $lForm->GetFieldValue('start_field_id');
		$this->m_action_result['start_offset'] = $lForm->GetFieldValue('start_offset');

		$this->m_action_result['end_instance_id'] = $lForm->GetFieldValue('end_instance_id');
		$this->m_action_result['end_field_id'] = $lForm->GetFieldValue('end_field_id');
		$this->m_action_result['end_offset'] = $lForm->GetFieldValue('end_offset');

		$lNameInViewObject = 'first_comment_preview';
		$this->m_action_result['is_first'] = true;
		if($lVersionCommentsNum){
			$lNameInViewObject = 'comment_preview';
			$this->m_action_result['is_first'] = false;
		}
		$this->m_action_result['comment_preview'] = $this->GetCommentPreview($lCommentId, $lNameInViewObject, true);

	}
	
	function EditComment() {
		global $user;
		$lDocumentId = (int) $this->GetValueFromRequestWithoutChecks('document_id');
		$lCommentId = (int) $this->GetValueFromRequestWithoutChecks('comment_id');
		if(! $lDocumentId || !$lCommentId){
			$lErrMsg = 'pjs.mustSpecifyDocumentIdAndCommentId';
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
					'err_msg' => getstr($lErrMsg)
			);
			return;
		}		
		$lForm = new Comment_Edit_Form_Wrapper(array(
				'page_controller_instance' => $this,
				'name_in_viewobject' => 'edit_comment_form',
				'view_object' => $this->m_pageView,
				'use_captcha' => 0,
				'm_debug' => false,
				'uid' => $user->id,
				'user_fullname' => $user->fullname,
				'document_id' => $lDocumentId,
				'form_method' => 'POST',
				'comment_id' => $lCommentId,
				'form_name' => 'comment_edit_' . $lCommentId
		));
		$lCommentId = $lForm->GetFieldValue('comment_id');
	
		if($lForm->GetErrorCount() || $lForm->GetCurrentAction() != 'save' || ! $lCommentId){
			if($lForm->GetErrorCount()){
				$lErrMsg = $lForm->GetErrorMsg();
			}elseif($lForm->GetCurrentAction() != 'save'){
				$lErrMsg = 'pjs.wrongFormAction';
			}elseif(! $lCommentId){
				$lErrMsg = 'pjs.couldNotGetCommentId';
			}
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
					'err_msg' => getstr($lErrMsg)
			);
			return;
		}
		$this->m_action_result['comment_id'] = $lCommentId;
		unset($_REQUEST['tAction']);
		unset($_REQUEST['kfor_name']);
		$this->m_action_result['comment_preview'] = $this->GetCommentPreview($lCommentId, 'comment_edit_preview', true);
		$this->m_action_result['html'] = $this->m_action_result['comment_preview'];
		
		$this->m_action_result['is_root'] = (int)$this->m_commentsModel->CheckIfCommentIsRoot($lCommentId);
		$this->m_action_result['is_empty'] = (int)$this->m_commentsModel->CheckIfCommentIsEmpty($lCommentId);
		$this->m_action_result['has_no_children'] = (int)$this->m_commentsModel->CheckIfCommentHasSubcomments($lCommentId);
	
	}

	function CreateNewReply() {
		global $user;
		$lRootId = (int) $this->GetValueFromRequestWithoutChecks('rootid');
		if(! $lRootId){
			$lErrMsg = 'pjs.mustSpecifyRootId';
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => getstr($lErrMsg)
			);
			return;
		}
		$lForm = new Comment_Reply_Form_Wrapper(array(
			'page_controller_instance' => $this,
			'name_in_viewobject' => 'comment_reply_form',
			'view_object' => $this->m_pageView,
			'use_captcha' => 0,
			'm_debug' => false,
			'uid' => $user->id,
			'user_fullname' => $user->fullname,
			'rootid' => $lRootId,
			'form_method' => 'POST',
			'form_name' => 'comment_reply_form' . $lRootId
		));
		$lCommentId = $lForm->GetFieldValue('comment_id');

		if($lForm->GetErrorCount() || $lForm->GetCurrentAction() != 'save' || ! $lCommentId){
			if($lForm->GetErrorCount()){
				$lErrMsg = 'pjs.couldNotCreateComment';
			}elseif($lForm->GetCurrentAction() != 'save'){
				$lErrMsg = 'pjs.wrongFormAction';
			}elseif(! $lCommentId){
				$lErrMsg = 'pjs.couldNotGetCommentId';
			}
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => getstr($lErrMsg)
			);
			return;
		}
		$this->m_action_result['comment_preview'] = $this->GetCommentPreview($lCommentId, 'reply_preview');
	}

	function GetCommentPreview($pCommentId, $pNameInViewObject, $pPlaceReplyForm = false) {
		global $user;
		$lCommentData = $this->m_commentsModel->GetCommentDetails($pCommentId);
		$lDocumentId = (int)$lCommentData['document_id'];
		$lVersionId = (int)$lCommentData['version_id'];		
		$lVersionsModel = new mVersions();
		// var_dump($lCommentData);
		$lCommentReplyForm = array();
		$lCommentEditForm = array();
		$lCommentObject = new evSimple_Block_Display(array(
			'controller_data' => $lCommentData,
			'name_in_viewobject' => $pNameInViewObject,
			'comment_reply_forms' => &$lCommentReplyForm,
			'comment_edit_forms' => &$lCommentEditForm,
			'view_object' => $this->m_tempPageView,			
			'has_editor_permissions' => $lVersionsModel->CheckUserSpecificRole($this->GetUserId(), $lDocumentId) ? true : false,
			'current_user_id' => $this->GetUserId(),
			'version_is_readonly' => $lVersionsModel->CheckIfVersionIsReadonly($lVersionId)
		));
		if($pPlaceReplyForm){
			$lForm = new Comment_Reply_Form_Wrapper(array(
				'page_controller_instance' => $this,
				'name_in_viewobject' => 'comment_reply_form',
				'view_object' => $this->m_tempPageView,
				'use_captcha' => 0,
				'm_debug' => false,
				'uid' => $this->GetUserId(),
				'user_fullname' => $user->fullname,
				'rootid' => $pCommentId,
				'form_method' => 'POST',
				'form_name' => 'comment_reply_form' . $pCommentId
			));
			$lCommentReplyForm[$pCommentId] = $lForm->Display();
		}
		$lEditForm = new Comment_Edit_Form_Wrapper(array(
				'page_controller_instance' => $this,
				'name_in_viewobject' => 'edit_comment_form',
				'view_object' => $this->m_tempPageView,
				'use_captcha' => 0,
				'm_debug' => false,
				'uid' => $this->GetUserId(),
				'user_fullname' => $user->fullname,
				'version_id' => $lVersionId,
				'document_id' => $lDocumentId,
				'form_method' => 'POST',
				'comment_id' => $pCommentId,
				'form_name' => 'comment_edit_' . $pCommentId
		));
		
		
		$lCommentEditForm[$pCommentId] = $lEditForm->Display();
// 		var_dump($lEditForm->Display());

		return $lCommentObject->Display();
	}

	function RecalculateCommentPosition() {
		$lVersionsModel = new mVersions();
		$lCommentId = (int) $this->GetValueFromRequestWithoutChecks('comment_id');
		$lInstanceId = (int) $this->GetValueFromRequestWithoutChecks('instance_id');
		$lFieldId = (int) $this->GetValueFromRequestWithoutChecks('field_id');
		$lPositionFixType = (int) $this->GetValueFromRequestWithoutChecks('position_fix_type');
		$lFieldHtmlValue = $this->GetValueFromRequestWithoutChecks('field_html_value');

		$lVersionId = (int) $this->m_commentsModel->GetCommentVersionId($lCommentId);
		if($lVersionsModel->CheckIfVersionHasUnprocessedPwtChanges($lVersionId)){
			$lVersionsModel->ProcessVersionPwtChanges($lVersionId);
		}
		$lRealFieldValue = $lVersionsModel->GetVersionFieldValueFromXml($lVersionId, $lInstanceId, $lFieldId);
// 		var_dump($lRealFieldValue, $lFieldHtmlValue);

		require_once PATH_CLASSES . 'comments.php';

		// var_dump($lFieldHtmlValue, $lRealFieldValue, $lCommentId,
		// $lPositionFixType);
		$lRealPos = CalculateCommentRealPosition($lFieldHtmlValue, $lRealFieldValue, $lCommentId, $lPositionFixType == COMMENT_START_POS_TYPE);
// 		var_dump($lRealPos);
		$this->m_commentsModel->ChangeCommentOffset($lCommentId, $lRealPos, $lPositionFixType);

	}

	function DeleteComment() {
		$lCommentId = (int) $this->GetValueFromRequestWithoutChecks('comment_id');
		$this->m_action_result = $this->m_commentsModel->DeleteComment($lCommentId, $this->m_userId);
		if($this->m_action_result['err_cnt']){
			$this->m_errCnt++;
			$this->m_errMsgs[]['err_msg'] = $this->m_action_result['err_msg'];
		}
		return $this->m_action_result;
	}

	function ResolveComment() {
		$lCommentId = (int) $this->GetValueFromRequestWithoutChecks('comment_id');
		$lResolveId = (int) $this->GetValueFromRequestWithoutChecks('resolve');
		$lResult = $this->m_commentsModel->ResolveComment($lCommentId, $lResolveId, $this->m_userId);
		$this->m_action_result = $lResult;
		if($this->m_action_result['err_cnt']){
			$this->m_errCnt++;
			$this->m_errMsgs[]['err_msg'] = $this->m_action_result['err_msg'];
		}
		return $lResult;
	}


	function GetVersionFilteredRootIdsList(){
		$lVersionId = (int) $this->GetValueFromRequestWithoutChecks('version_id');
		$lDisplayResolved = (int) $this->GetValueFromRequestWithoutChecks('display_resolved');
		$lDisplayInline = (int) $this->GetValueFromRequestWithoutChecks('display_inline');
		$lDisplayGeneral = (int) $this->GetValueFromRequestWithoutChecks('display_general');
		$lFilterUsers = (int) $this->GetValueFromRequestWithoutChecks('filter_users');
		$lSelectedUsers = $this->GetValueFromRequestWithoutChecks('selected_users');

		$lResult = $this->m_commentsModel->GetVersionFilteredRootIdsList($lVersionId, $lDisplayResolved, $lDisplayInline, $lDisplayGeneral, $lFilterUsers, $lSelectedUsers, $this->m_userId, true);
		$this->m_action_result['visible_rootids'] = $lResult;
		return $lResult;
	}
}

?>