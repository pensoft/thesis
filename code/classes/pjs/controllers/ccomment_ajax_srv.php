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
		$lCommentData = $this->m_commentsModel->GetCommentDetails($pCommentId);
		$lDocumentId = (int)$lCommentData['document_id'];
		$lVersionsModel = new mVersions();
		// var_dump($lCommentData);
		$lCommentReplyForm = array();
		$lCommentObject = new evSimple_Block_Display(array(
			'controller_data' => $lCommentData,
			'name_in_viewobject' => $pNameInViewObject,
			'comment_reply_forms' => &$lCommentReplyForm,
			'view_object' => $this->m_tempPageView,
			'has_editor_permissions' => $lVersionsModel->CheckUserSpecificRole($this->GetUserId(), $lDocumentId) ? true : false,
			'current_user_id' => $this->GetUserId(),
		));
		if($pPlaceReplyForm){
			global $user;
			$lForm = new Comment_Reply_Form_Wrapper(array(
				'page_controller_instance' => $this,
				'name_in_viewobject' => 'comment_reply_form',
				'view_object' => $this->m_tempPageView,
				'use_captcha' => 0,
				'm_debug' => false,
				'uid' => $user->id,
				'user_fullname' => $user->fullname,
				'rootid' => $pCommentId,
				'form_method' => 'POST',
				'form_name' => 'comment_reply_form' . $pCommentId
			));
			$lCommentReplyForm[$pCommentId] = $lForm->Display();
		}

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
		return $this->m_commentsModel->DeleteComment($lCommentId, $this->m_userId);
	}

	function ResolveComment() {
		$lCommentId = (int) $this->GetValueFromRequestWithoutChecks('comment_id');
		$lResolveId = (int) $this->GetValueFromRequestWithoutChecks('resolve');
		$lResult = $this->m_commentsModel->ResolveComment($lCommentId, $lResolveId, $this->m_userId);
		$this->m_action_result = $lResult;
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