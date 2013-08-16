<?php
// Disable error reporting because it can break the json output
//ini_set('error_reporting', 'off');

class cDocument_Ajax_Srv extends cBase_Controller {
	var $m_errCnt = 0;
	var $m_errMsgs = array();
	var $m_action;
	var $m_action_result = array();
	var $m_eventsParamString = '';
	function __construct() {
		parent::__construct();
		$pViewPageObjectsDataArray = array();
		$this->m_action = $this->GetValueFromRequestWithoutChecks('action');
		switch ($this->m_action) {
			default :
				$this->m_errCnt ++;
				$this->m_errMsgs[] = array(
					'err_msg' => getstr('pjs.unrecognizedAction')
				);
				break;
			case 'add_document_se' :
			case 'remove_document_se' :
				$this->AddRemoveDocumentSE();
				break;
			case 'add_document_le' :
			case 'remove_document_le' :
				$this->AddRemoveDocumentLE();
				break;
			case 'add_document_ce' :
			case 'remove_document_ce' :
				$this->AddRemoveDocumentCE();
				break;
			case 'inviteReviewers' :
				$this->InviteReviewers();
				break;
			case 'invite_reviewer_as_ghost':
				$this->InviteReviewerAsGhost();
				break;
			case 'saveReviewerRole':
				$this->saveReviewerRole();
				break;
			case 'se_confirm_reviewer_invitation' :
			case 'se_cancel_reviewer_invitation' :
				$this->SECancelConfirmReviewerInvitation();
				break;
			case 'confirm_reviewer_invitation' :
			case 'cancel_reviewer_invitation' :
				$this->CancelConfirmReviewerInvitation();
				break;
			case 'save_reviewer_decision' :
				$this->SaveReviewerDecision();
				break;
			case 'save_se_decision' :
				$this->SaveSEDecision();
				break;
			case 'save_le_decision' :
				$this->SaveLEDecision();
				break;
			case 'save_ce_decision' :
				$this->SaveCEDecision();
				break;
			case 'submit_author_version_for_review' :
				$this->SubmitAuthorVersionForReview();
				break;
			case 'proceed_document_to_layout_editing' :
				$this->ProceedDocumentToLayoutEditing();
				break;
			case 'proceed_document_to_copy_editing' :
				$this->ProceedDocumentToCopyEditing();
				break;
			case 'save_author_layout_decision' :
				$this->SaveAuthorLayoutDecision();
				break;
			case 'editor_proceed_document_to_layout' :
				$this->EditorProceedDocumentToLayoutEditing();
				break;
			case 'save_editor_notes' :
			case 'save_le_notes' :
				$this->SaveEditorNotes();
				break;
			case 'save_editor_decision' :
				$this->SaveEditorDecision();
				break;
			case 'save_serej_decision' :
				$this->SaveSERejectDecision();
				break;
			//case 'remove_reviewer' :
			case 'reinvite_reviewer' :
				$this->ReInviteDocumentReviewer();
				break;
			case 'merge_versions' :
				$this->MergeReviewersVersions((int)$this->GetValueFromRequestWithoutChecks('roundid'));
				break;
			case 'disable_inviting_users' :
				$this->DisableInvitingUsers((int)$this->GetValueFromRequestWithoutChecks('roundid'), (int)$this->GetValueFromRequestWithoutChecks('document_id'));
				break;
			case 'check_reviewers' :
				$this->CheckInvitedReviewersForRound((int)$this->GetValueFromRequestWithoutChecks('roundid'), (int)$this->GetValueFromRequestWithoutChecks('document_id'));
				$this->CheckSubmittedReviewersForRound((int)$this->GetValueFromRequestWithoutChecks('roundid'), (int)$this->GetValueFromRequestWithoutChecks('document_id'));
				break;
			case 'manage_user_invitations_and_reviews' :
				$this->ManageUserInvitationsAndReviews(	(int)$this->GetValueFromRequestWithoutChecks('round_id'),
															(int)$this->GetValueFromRequestWithoutChecks('document_id'),
															$this->GetValueFromRequestWithoutChecks('invited_users_ids'),
															$this->GetValueFromRequestWithoutChecks('non_submited_users_ids'));
				break;
			case 'save_le_xml_version' :
				$this->SaveLEXMLVersion();
				break;
			case 'revert_le_xml_version' :
				$this->RevertLEXMLVersion();
				break;
		}

		$lResultArr = array_merge($this->m_action_result, array(
			'err_cnt' => $this->m_errCnt,
			'err_msgs' => $this->m_errMsgs,
			'url_params' => $this->m_eventsParamString,
		));
		$this->m_pageView = new epPage_Json_View($lResultArr);
	}

	function SubmitDocumentVersionToPwt($pVersionId){
		global $user;

		$lVersionsModel = new mVersions();
		$lPwtDocumentData = $lVersionsModel->GetVersionPwtXml($pVersionId);
		$lPwtDocumentId = $lPwtDocumentData['pwt_document_id'];
		$lDocumentXml = $lPwtDocumentData['xml'];
		$lVersionComments = $lVersionsModel->GetVersionComments($pVersionId);
		$lDocumentXml = InsertCommentsInDocumentXml($lDocumentXml, $lVersionComments);
// 		header('Content-type:text/xml');
// 		echo($lDocumentXml);
// 		exit;

		$lPostFields = array(
			'xml' => $lDocumentXml,
			'document_id' => $lPwtDocumentId,
			'user_id' => $user->id,
		);

		$lQueryResult = executeExternalQuery(PWT_PJS_IMPORT_URL, $lPostFields);
		$lQueryResult = json_decode($lQueryResult, true);
		return $lQueryResult;
	}

	function AddRemoveDocumentSE() {
		try{
			if(! $this->GetUserId()){
				throw new Exception(getstr('pjs.onlyLoggedUsersCanPerformThisAction'));
			}
			$lDocumentId = (int) $this->GetValueFromRequestWithoutChecks('document_id');
			$lSEId = (int) $this->GetValueFromRequestWithoutChecks('se_id');
			$lDocumentsModel = new mDocuments_Model();
			$lAdd = true;
			switch ($this->m_action) {
				case 'remove_document_se' :
					$lAdd = false;
			}
			$this->m_action_result = $lDocumentsModel->AddRemoveSE($lDocumentId, $lSEId, $this->GetUserId(), $lAdd);
			if($this->m_action_result['err_cnt']){
				array_walk_recursive($this->m_action_result['err_msgs'], translate_array_elem);
				$this->m_errCnt = $this->m_action_result['err_cnt'];
				$this->m_errMsgs = $this->m_action_result['err_msgs'];
			} else {
				foreach ($this->m_action_result['event_id'] as $key => $value) {
					/**
					 * Manage event task (submitting new document)
					 */
					$lTaskObj = new cTask_Manager(array(
						'event_id' => (int)$value,
					));
					$lTaskObj->Display();
				}
				if(count($this->m_action_result['event_id']) && $lAdd == true) {
					$this->m_eventsParamString = 'event_id[]=' . implode("&event_id[]=", $this->m_action_result['event_id']) . '&e_redirect=1';
				} elseif(count($this->m_action_result['event_id'])){
					$this->m_eventsParamString = 'event_id[]=' . implode("&event_id[]=", $this->m_action_result['event_id']);
				}
			}

		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}

	function AddRemoveDocumentLE() {
		try{
			if(! $this->GetUserId()){
				throw new Exception(getstr('pjs.onlyLoggedUsersCanPerformThisAction'));
			}
			$lDocumentId = (int) $this->GetValueFromRequestWithoutChecks('document_id');
			$lLEId = (int) $this->GetValueFromRequestWithoutChecks('le_id');
			$lDocumentsModel = new mDocuments_Model();
			$lAdd = true;
			switch ($this->m_action) {
				case 'remove_document_le' :
					$lAdd = false;
			}
			$this->m_action_result = $lDocumentsModel->AddRemoveLE($lDocumentId, $lLEId, $this->GetUserId(), $lAdd);
			if($this->m_action_result['err_cnt']){
				$this->m_errCnt = $this->m_action_result['err_cnt'];
				$this->m_errMsgs = $this->m_action_result['err_msgs'];
			} else {
				foreach ($this->m_action_result['event_id'] as $key => $value) {
					/**
					 * Manage event task (submitting new document)
					 */
					$lTaskObj = new cTask_Manager(array(
						'event_id' => (int)$value,
					));
					$lTaskObj->Display();
				}
				if(count($this->m_action_result['event_id'])) {
					$lRoleId = JOURNAL_EDITOR_ROLE;
					$this->m_eventsParamString = 'event_id[]=' . implode("&event_id[]=", $this->m_action_result['event_id']) . '&role_redirect=' . $lRoleId;
				}
			}

		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}

	function AddRemoveDocumentCE() {
		try{
			if(! $this->GetUserId()){
				throw new Exception(getstr('pjs.onlyLoggedUsersCanPerformThisAction'));
			}
			$lDocumentId = (int) $this->GetValueFromRequestWithoutChecks('document_id');
			$lCEId = (int) $this->GetValueFromRequestWithoutChecks('ce_id');
			$lCurrentRoundId = (int) $this->GetValueFromRequestWithoutChecks('current_round_id');
			$lDocumentsModel = new mDocuments_Model();
			$lAdd = true;
			switch ($this->m_action) {
				case 'remove_document_ce' :
					$lAdd = false;
			}
			$this->m_action_result = $lDocumentsModel->AddRemoveCE($lDocumentId, $lCEId, $this->GetUserId(), $lAdd, $lCurrentRoundId);
			if($this->m_action_result['err_cnt']){
				$this->m_errCnt = $this->m_action_result['err_cnt'];
				$this->m_errMsgs = $this->m_action_result['err_msgs'];
			} else {
				foreach ($this->m_action_result['event_id'] as $key => $value) {
					/**
					 * Manage event task (submitting new document)
					 */
					$lTaskObj = new cTask_Manager(array(
						'event_id' => (int)$value,
					));
					$lTaskObj->Display();
				}
				if(count($this->m_action_result['event_id'])) {
					$lRoleId = JOURNAL_EDITOR_ROLE;
					$this->m_eventsParamString = 'event_id[]=' . implode("&event_id[]=", $this->m_action_result['event_id']) . '&role_redirect=' . $lRoleId;
				}
			}

		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}

	function InviteReviewers() {
		$lNominatedIds = $this->GetValueFromRequestWithoutChecks('n');
		$lPanelIds =      $this->GetValueFromRequestWithoutChecks('p');
		$lDocumentId = (int) $this->GetValueFromRequestWithoutChecks('doc_id');
		$lRoleId = (int) $this->GetValueFromRequestWithoutChecks('role');

		if(!(count($lNominatedIds)+count($lPanelIds))) {
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array( 'err_msg' => 'pjs.noreviewersselected' );
			//return;
		}
		$lNominatedArr = 'ARRAY['. implode(',', $lNominatedIds) . ']::int[]';
		$lPanelArr 	   = 'ARRAY['. implode(',', $lPanelIds	  ) . ']::int[]';

		try{
			if(! $this->GetUserId()){
				throw new Exception(getstr('pjs.onlyLoggedUsersCanPerformThisAction'));
			}
			$lDocumentsModel = new mDocuments_Model();
			$this->m_action_result = $lDocumentsModel->InviteReviewer($lDocumentId, $lNominatedArr, $this->GetUserId(), DEDICATED_REVIEWER_ROLE);
			if($this->m_action_result['err_cnt']){
				$this->m_errCnt  = $this->m_action_result['err_cnt'];
				$this->m_errMsgs = $this->m_action_result['err_msgs'];
				//return;
			} else {
				foreach ($this->m_action_result['data']['event_id'] as $key => $value) {
					$lTaskObj = new cTask_Manager(array( 'event_id' => (int)$value, 	));
					$lTaskObj->Display();
				}
			}
			$url_params = $this->m_action_result['data']['event_id'];

			$this->m_action_result = $lDocumentsModel->InviteReviewer($lDocumentId, $lPanelArr, $this->GetUserId(), COMMUNITY_REVIEWER_ROLE);
			if($this->m_action_result['err_cnt']){
				$this->m_errCnt  = $this->m_action_result['err_cnt'];
				$this->m_errMsgs = $this->m_action_result['err_msgs'];
				//return;
			} else {
				foreach ($this->m_action_result['data']['event_id'] as $key => $value) {
					$lTaskObj = new cTask_Manager(array( 'event_id' => (int)$value, 	));
					$lTaskObj->Display();
				}
			}
			//var_dump($this->m_action_result['data']['event_id']);
			//var_dump($url_params);
			$merged = array_merge((array)$url_params, (array)$this->m_action_result['data']['event_id']);
			//var_dump($merged);
			if(count($merged))
			{
				if((int)$lRoleId) {
					$this->m_eventsParamString = 'event_id[]=' . implode("&event_id[]=", $merged) . '&role_redirect=' . $lRoleId . '&reviewers_email_flag=1';
				} else {
					$this->m_eventsParamString = 'event_id[]=' . implode("&event_id[]=", $merged) . '&reviewers_email_flag=1';
				}
			}

		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}

	function InviteReviewerAsGhost()
	{
		try{
			if(! $this->GetUserId()){
				throw new Exception(getstr('pjs.onlyLoggedUsersCanPerformThisAction'));
			}

			$lDocumentId = (int) $this->GetValueFromRequestWithoutChecks('document_id');
			$lDedicatedReviewerId = (int) $this->GetValueFromRequestWithoutChecks('reviewer_id');
			$lCurrentRoundId = (int) $this->GetValueFromRequestWithoutChecks('current_round_id');
			$lDocumentsModel = new mDocuments_Model();
			$this->m_action_result = $lDocumentsModel->InviteReviewerAsGhost($lDedicatedReviewerId, $lDocumentId, $lCurrentRoundId);

			if($this->m_action_result['err_cnt']){
				$this->m_errCnt = $this->m_action_result['err_cnt'];
				$this->m_errMsgs = $this->m_action_result['err_msgs'];
			}

		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}
	function saveReviewerRole(){
		try{
			if(! $this->GetUserId()){
				throw new Exception(getstr('pjs.onlyLoggedUsersCanPerformThisAction'));
			}
		$lReviewer 		= (int) $this->GetValueFromRequestWithoutChecks('reviewer_id');
		$lCurrentRound	= (int) $this->GetValueFromRequestWithoutChecks('current_round_id');
		$lRole 			= (int) $this->GetValueFromRequestWithoutChecks('role');
		$lDoc_id		= (int) $this->GetValueFromRequestWithoutChecks('doc_id');
		
		$lDocumentsModel = new mDocuments_Model();
		$this->m_action_result = $lDocumentsModel->SaveReviewerRole($lReviewer, $lCurrentRound, $lRole, $lDoc_id);
		
		if($this->m_action_result['err_cnt']){
			$this->m_errCnt = $this->m_action_result['err_cnt'];
			$this->m_errMsgs = $this->m_action_result['err_msgs'];
		}

		} catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
		
	}
	
	function SECancelConfirmReviewerInvitation() {
		try{
			if(! $this->GetUserId()){
				throw new Exception(getstr('pjs.onlyLoggedUsersCanPerformThisAction'));
			}
			$lInvitationId = (int) $this->GetValueFromRequestWithoutChecks('invitation_id');
			$lDocumentId = (int) $this->GetValueFromRequestWithoutChecks('document_id');
			$lDocumentsModel = new mDocuments_Model();
			$lConfirm = true;
			switch ($this->m_action) {
				case 'se_cancel_reviewer_invitation' :
					$lConfirm = false;
			}

			$lInvitedReviewerId = (int) $this->GetValueFromRequestWithoutChecks('reviewer_id');

			$this->m_action_result = $lDocumentsModel->SECancelConfirmReviewerInvitation($lDocumentId, $lInvitationId, $this->GetUserId(), $lInvitedReviewerId, $lConfirm);
			if($this->m_action_result['err_cnt']){
				$this->m_errCnt = $this->m_action_result['err_cnt'];
				$this->m_errMsgs = $this->m_action_result['err_msgs'];
			} else {
				$lArrEvents = array();
				
				/**
				 * Manage event task (submitting new document)
				 */
				$lTaskObj = new cTask_Manager(array(
					'event_id' => (int)$this->m_action_result['event_id'],
				));
				$lTaskObj->Display();
				
				$lArrEvents[] = (int)$this->m_action_result['event_id'];
			
				if((int)$this->m_action_result['event_id_sec']) {
					$lTaskObj = new cTask_Manager(array(
						'event_id' => (int)$this->m_action_result['event_id_sec'],
					));
					$lTaskObj->Display();	
					$lArrEvents[] = (int)$this->m_action_result['event_id_sec'];
				}
				 
				$this->m_eventsParamString = 'event_id[]=' . implode("&event_id[]=", $lArrEvents);
			}

		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}

	function CancelConfirmReviewerInvitation() {
		try{
			if(! $this->GetUserId()){
				throw new Exception(getstr('pjs.onlyLoggedUsersCanPerformThisAction'));
			}
			$lInvitationId = (int) $this->GetValueFromRequestWithoutChecks('invitation_id');
			$lDocumentId = (int) $this->GetValueFromRequestWithoutChecks('document_id');
			$lDocumentsModel = new mDocuments_Model();
			$lConfirm = true;
			switch ($this->m_action) {
				case 'cancel_reviewer_invitation' :
					$lConfirm = false;
			}
			$this->m_action_result = $lDocumentsModel->CancelConfirmReviewerInvitation($lDocumentId, $lInvitationId, $this->GetUserId(), $lConfirm);
			if($this->m_action_result['err_cnt']){
				$this->m_errCnt = $this->m_action_result['err_cnt'];
				$this->m_errMsgs = $this->m_action_result['err_msgs'];
			} else {
				/**
				 * Manage event task (submitting new document)
				 */
				$lTaskObj = new cTask_Manager(array(
					'event_id' => (int)$this->m_action_result['event_id'],
				));
				$lTaskObj->Display();
			
				$lArrEvents[] = (int)$this->m_action_result['event_id'];
			
				if((int)$this->m_action_result['event_id_sec']) {
					$lTaskObj = new cTask_Manager(array(
						'event_id' => (int)$this->m_action_result['event_id_sec'],
					));
					$lTaskObj->Display();	
					$lArrEvents[] = (int)$this->m_action_result['event_id_sec'];
				}
				 
				$this->m_eventsParamString = 'event_id[]=' . implode("&event_id[]=", $lArrEvents);
			}

		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}

	function SaveReviewerDecision() {
		try{
			if(! $this->GetUserId()){
				throw new Exception(getstr('pjs.onlyLoggedUsersCanPerformThisAction'));
			}
			$lRoundUserId = (int) $this->GetValueFromRequestWithoutChecks('round_user_id');
			$lDecisionId = (int) $this->GetValueFromRequestWithoutChecks('decision_id');
			$lDocumentId = (int) $this->GetValueFromRequestWithoutChecks('document_id');
			$lDecisionNotes = $this->GetValueFromRequestWithoutChecks('decision_notes');
			$lUserId = $this->GetValueFromRequestWithoutChecks('uid');
			$lDocumentsModel = new mDocuments_Model();
			$lVersionsModel = new mVersions();
			$lVersionId = $lVersionsModel->GetRoundUserIdVersionId($lRoundUserId);
// 			var_dump(2);
			$this->m_action_result = $lDocumentsModel->SaveReviewerDecision($lDocumentId, $lRoundUserId, $lDecisionId, $lDecisionNotes, $lUserId);
			$lVersionsModel->SaveReviewerCachedVersion($lVersionId);			
			//Accept all the reviewer changes
			$lVersionsModel->PwtVersionAcceptAllChanges($lVersionId, $lUserId);
			$lVersionsModel->ProcessVersionPwtChanges($lVersionId);
			if($this->m_action_result['err_cnt']){
				$this->m_errCnt = $this->m_action_result['err_cnt'];
				$this->m_errMsgs = $this->m_action_result['err_msgs'];
			} else {
				foreach ($this->m_action_result['data']['event_id'] as $key => $value) {
					/**
					 * Manage event task (submitting new document)
					 */
					$lTaskObj = new cTask_Manager(array(
						'event_id' => (int)$value,
					));
					$lTaskObj->Display();
				}
			}

		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}

	function SaveEditorDecision() {
		try{
			if(! $this->GetUserId()){
				throw new Exception(getstr('pjs.onlyLoggedUsersCanPerformThisAction'));
			}
			$lRoundUserId = (int) $this->GetValueFromRequestWithoutChecks('round_user_id');
			$lDecisionId = (int) $this->GetValueFromRequestWithoutChecks('decision_id');
			$lDocumentId = (int) $this->GetValueFromRequestWithoutChecks('document_id');
			$lDecisionNotes = $this->GetValueFromRequestWithoutChecks('decision_notes');
			$lDocumentsModel = new mDocuments_Model();

			$this->m_action_result = $lDocumentsModel->SaveEditorDecision($lRoundUserId, $lDecisionId, $lDecisionNotes, $this->GetUserId(), $lDocumentId);
			if($this->m_action_result['err_cnt']){
				$this->m_errCnt = $this->m_action_result['err_cnt'];
				$this->m_errMsgs = $this->m_action_result['err_msgs'];
			} else {
				/**
				 * Manage event task (SE decision)
				 */
				$lTaskObj = new cTask_Manager(array(
					'event_id' => (int)$this->m_action_result['event_id'],
				));
				$lTaskObj->Display();
				if((int)$this->m_action_result['event_id']) {
					$this->m_eventsParamString = 'event_id[]=' . (int)$this->m_action_result['event_id'];
				}
			}

		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}

	function SaveSERejectDecision() {
		try{
			if(! $this->GetUserId()){
				throw new Exception(getstr('pjs.onlyLoggedUsersCanPerformThisAction'));
			}
			$lRoundUserId = (int) $this->GetValueFromRequestWithoutChecks('round_user_id');
			$lDecisionId = (int) $this->GetValueFromRequestWithoutChecks('decision_id');
			$lDocumentId = (int) $this->GetValueFromRequestWithoutChecks('document_id');
			$lDecisionNotes = $this->GetValueFromRequestWithoutChecks('decision_notes');
			$lDocumentsModel = new mDocuments_Model();

			$this->m_action_result = $lDocumentsModel->SaveSERejectDecision($lRoundUserId, $lDecisionId, $lDecisionNotes, $this->GetUserId(), $lDocumentId);
			if($this->m_action_result['err_cnt']){
				$this->m_errCnt = $this->m_action_result['err_cnt'];
				$this->m_errMsgs = $this->m_action_result['err_msgs'];
			} else {
				/**
				 * Manage event task (SE decision)
				 */
				$lTaskObj = new cTask_Manager(array(
					'event_id' => (int)$this->m_action_result['event_id'],
				));
				$lTaskObj->Display();
				if((int)$this->m_action_result['event_id']) {
					$this->m_eventsParamString = 'event_id[]=' . (int)$this->m_action_result['event_id'];
				}
			}

		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}

	function SaveSEDecision() {
		global $user;
		try{
			//trigger_error('!!SaveSEDecision!!', E_USER_NOTICE);
			if(! $this->GetUserId()){
				throw new Exception(getstr('pjs.onlyLoggedUsersCanPerformThisAction'));
			}
			$lRoundUserId = (int) $this->GetValueFromRequestWithoutChecks('round_user_id');
			$lDecisionId = (int) $this->GetValueFromRequestWithoutChecks('decision_id');
			$lDecisionNotes = $this->GetValueFromRequestWithoutChecks('decision_notes');
			$lDocumentId = (int)$this->GetValueFromRequestWithoutChecks('document_id');
			$lDocumentsModel = new mDocuments_Model();

			$this->m_action_result = $lDocumentsModel->SaveSEDecision($lRoundUserId, $lDecisionId, $lDecisionNotes, $this->GetUserId(), $lDocumentId);
			if($this->m_action_result['err_cnt']){
				$this->m_errCnt = $this->m_action_result['err_cnt'];
				$this->m_errMsgs = $this->m_action_result['err_msgs'];
			} else {
				if(in_array($lDecisionId, array(ROUND_DECISION_ACCEPT, ROUND_DECISION_ACCEPT_WITH_MINOR_CORRECTIONS, ROUND_DECISION_ACCEPT_WITH_MAJOR_CORRECTIONS))){

					// We should create an author version here and submit it to PWT

					$lVersionsModel = new mVersions();

					$lRoundId = $lVersionsModel->GetRoundUserIdRoundId($lRoundUserId);

					$lAuthorVersionId = $lVersionsModel->CreatePwtAuthorVersionAfterSubmissionRound($lRoundId);
// 					var_dump($lAuthorVersionId);

					if($lAuthorVersionId){
						$lQueryResult = $this->SubmitDocumentVersionToPwt($lAuthorVersionId);

						if($lQueryResult['err_cnt']){
							throw new Exception($lQueryResult['err_msg']);

						}

					}

				}
				/**
				 * Manage event task (SE decision)
				 */
				$lTaskObj = new cTask_Manager(array(
					'event_id' => (int)$this->m_action_result['event_id'],
				));
				$lTaskObj->Display();
				if((int)$this->m_action_result['event_id']) {
					$this->m_eventsParamString = 'event_id[]=' . (int)$this->m_action_result['event_id'];
				}
			}

		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}

	function SaveLEDecision() {
		try{
			if(! $this->GetUserId()){
				throw new Exception(getstr('pjs.onlyLoggedUsersCanPerformThisAction'));
			}
			$lRoundUserId = (int) $this->GetValueFromRequestWithoutChecks('round_user_id');
			$lDecisionId = (int) $this->GetValueFromRequestWithoutChecks('decision_id');
			$lDecisionNotes = $this->GetValueFromRequestWithoutChecks('decision_notes');
			$lDocumentsModel = new mDocuments_Model();

			$this->m_action_result = $lDocumentsModel->SaveLEDecision($lRoundUserId, $lDecisionId, $lDecisionNotes, $this->GetUserId());
			if($this->m_action_result['err_cnt']){
				$this->m_errCnt = $this->m_action_result['err_cnt'];
				$this->m_errMsgs = $this->m_action_result['err_msgs'];
			} else {
				/**
				 * Manage event task (SE decision)
				 */
				$lTaskObj = new cTask_Manager(array(
					'event_id' => (int)$this->m_action_result['event_id'],
				));
				$lTaskObj->Display();
				/*if((int)$this->m_action_result['event_id']) {
					$this->m_eventsParamString = 'event_id[]=' . (int)$this->m_action_result['event_id'];
				}*/
			}

		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}

	function SaveCEDecision() {
		global $user;
		try{
			if(! $this->GetUserId()){
				throw new Exception(getstr('pjs.onlyLoggedUsersCanPerformThisAction'));
			}
			$lRoundUserId = (int) $this->GetValueFromRequestWithoutChecks('round_user_id');
			$lDecisionId = (int) $this->GetValueFromRequestWithoutChecks('decision_id');
			$lDecisionNotes = $this->GetValueFromRequestWithoutChecks('decision_notes');
			$lDocumentsModel = new mDocuments_Model();

			$this->m_action_result = $lDocumentsModel->SaveCEDecision($lRoundUserId, $lDecisionId, $lDecisionNotes, $this->GetUserId());
			if($this->m_action_result['err_cnt']){
				$this->m_errCnt = $this->m_action_result['err_cnt'];
				$this->m_errMsgs = $this->m_action_result['err_msgs'];
			} else {

				if(in_array($lDecisionId, array(ROUND_DECISION_ACCEPT))){
					// We should create an author version here and submit it to PWT
					$lVersionsModel = new mVersions();
					$lRoundId = $lVersionsModel->GetRoundUserIdRoundId($lRoundUserId);
					$lAuthorVersionId = $lVersionsModel->CreatePwtAuthorVersionAfterSubmissionRound($lRoundId, CE_ROLE);

					if($lAuthorVersionId){
						// The author version has been created successfully
						$lQueryResult = $this->SubmitDocumentVersionToPwt($lAuthorVersionId);
						if($lQueryResult['err_cnt']){
							throw new Exception($lQueryResult['err_msg']);
						}
					}

				}
				/**
				 * Manage event task (CE decision)
				 */
				$lTaskObj = new cTask_Manager(array(
					'event_id' => (int)$this->m_action_result['event_id'],
				));
				$lTaskObj->Display();
				if((int)$this->m_action_result['event_id']) {
					$this->m_eventsParamString = 'event_id[]=' . (int)$this->m_action_result['event_id'];
				}

			}
		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}

	function SaveAuthorLayoutDecision() {
		try{
			if(! $this->GetUserId()){
				throw new Exception(getstr('pjs.onlyLoggedUsersCanPerformThisAction'));
			}
			$lDocumentId = (int) $this->GetValueFromRequestWithoutChecks('document_id');
			$lDecisionId = (int) $this->GetValueFromRequestWithoutChecks('decision_id');
			$lDocumentsModel = new mDocuments_Model();

			$this->m_action_result = $lDocumentsModel->SaveAuthorLayoutDecision($lDocumentId, $lDecisionId, $this->GetUserId());
			if($this->m_action_result['err_cnt']){
				$this->m_errCnt = $this->m_action_result['err_cnt'];
				$this->m_errMsgs = $this->m_action_result['err_msgs'];
			}

		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}

	function SubmitAuthorVersionForReview() {
		try{
			if(! $this->GetUserId()){
				throw new Exception(getstr('pjs.onlyLoggedUsersCanPerformThisAction'));
			}
			$lDocumentId = (int) $this->GetValueFromRequestWithoutChecks('document_id');
			$lDocumentsModel = new mDocuments_Model();

			$this->m_action_result = $lDocumentsModel->SubmitAuthorVersionForReview($lDocumentId, $this->GetUserId());
			if($this->m_action_result['err_cnt']){
				$this->m_errCnt = $this->m_action_result['err_cnt'];
				$this->m_errMsgs = $this->m_action_result['err_msgs'];
			} else {
				/**
				 * Manage event task (SE decision)
				 */
				$lTaskObj = new cTask_Manager(array(
					'event_id' => (int)$this->m_action_result['event_id'],
				));
				$lTaskObj->Display();
				if((int)$this->m_action_result['event_id']) {
					$this->m_eventsParamString = 'event_id[]=' . (int)$this->m_action_result['event_id'];
				}
			}

		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}

	function ProceedDocumentToLayoutEditing() {
		try{
			if(! $this->GetUserId()){
				throw new Exception(getstr('pjs.onlyLoggedUsersCanPerformThisAction'));
			}
			$lDocumentId = (int) $this->GetValueFromRequestWithoutChecks('document_id');
			$lDocumentsModel = new mDocuments_Model();

			$this->m_action_result = $lDocumentsModel->ProceedDocumentToLayoutEditing($lDocumentId, $this->GetUserId());
			if($this->m_action_result['err_cnt']){
				$this->m_errCnt = $this->m_action_result['err_cnt'];
				$this->m_errMsgs = $this->m_action_result['err_msgs'];
			} else {
				/**
				 * Manage event task (SE decision)
				 */
				$lTaskObj = new cTask_Manager(array(
					'event_id' => (int)$this->m_action_result['event_id'],
				));
				$lTaskObj->Display();
			}

		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}

	function ProceedDocumentToCopyEditing() {
		try{
			if(! $this->GetUserId()){
				throw new Exception(getstr('pjs.onlyLoggedUsersCanPerformThisAction'));
			}
			$lDocumentId = (int) $this->GetValueFromRequestWithoutChecks('document_id');
			$lDocumentsModel = new mDocuments_Model();

			$this->m_action_result = $lDocumentsModel->ProceedDocumentToCopyEditing($lDocumentId, $this->GetUserId());
			if($this->m_action_result['err_cnt']){
				$this->m_errCnt = $this->m_action_result['err_cnt'];
				$this->m_errMsgs = $this->m_action_result['err_msgs'];
			} else {
				/**
				 * Manage event task (SE decision)
				 */
				$lTaskObj = new cTask_Manager(array(
					'event_id' => (int)$this->m_action_result['event_id'],
				));
				$lTaskObj->Display();
			}

		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}

	function EditorProceedDocumentToLayoutEditing() {
		try{
			if(! $this->GetUserId()){
				throw new Exception(getstr('pjs.onlyLoggedUsersCanPerformThisAction'));
			}
			$lDocumentId = (int) $this->GetValueFromRequestWithoutChecks('document_id');
			$lDocumentsModel = new mDocuments_Model();

			$this->m_action_result = $lDocumentsModel->EditorProceedDocumentToLayoutEditing($lDocumentId, $this->GetUserId());
			if($this->m_action_result['err_cnt']){
				$this->m_errCnt = $this->m_action_result['err_cnt'];
				$this->m_errMsgs = $this->m_action_result['err_msgs'];
			}

		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}

	function SaveEditorNotes() {
		try{
			if(! $this->GetUserId()){
				throw new Exception(getstr('pjs.onlyLoggedUsersCanPerformThisAction'));
			}
			$lDocumentId = (int) $this->GetValueFromRequestWithoutChecks('document_id');
			$lEditorNotes = $this->GetValueFromRequestWithoutChecks('editor_notes');

			$lDocumentsModel = new mDocuments_Model();

			if($this->m_action == 'save_editor_notes') {
				$lMode = 0;
			} else {
				$lMode = 1;
			}

			$this->m_action_result = $lDocumentsModel->SaveEditorNotes($lDocumentId, $this->GetUserId(), $lEditorNotes, (int)$lMode);
			if($this->m_action_result['err_cnt']){
				$this->m_errCnt = $this->m_action_result['err_cnt'];
				$this->m_errMsgs = $this->m_action_result['err_msgs'];
			}

		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}

	function ReInviteDocumentReviewer() {
		try{
			if(! $this->GetUserId()){
				throw new Exception(getstr('pjs.onlyLoggedUsersCanPerformThisAction'));
			}

			$lReviewerId =  $this->GetValueFromRequestWithoutChecks('reviewer_id');
			$lRoundId =  $this->GetValueFromRequestWithoutChecks('round_id');
			$lDocumentId =  $this->GetValueFromRequestWithoutChecks('document_id');

			$lDocumentsModel = new mDocuments_Model();

			$this->m_action_result = $lDocumentsModel->ReInviteDocumentReviewer($lReviewerId, $lDocumentId, $this->GetUserId(), $lRoundId);

			if($this->m_action_result['err_cnt']){
				$this->m_errCnt = $this->m_action_result['err_cnt'];
				$this->m_errMsgs = $this->m_action_result['err_msgs'];
			} else {
				/**
				 * Manage event task (SE decision)
				 */
				$lTaskObj = new cTask_Manager(array(
					'event_id' => (int)$this->m_action_result['event_id'],
				));
				$lTaskObj->Display();
				if((int)$this->m_action_result['event_id']) {
					$this->m_eventsParamString = 'event_id[]=' . (int)$this->m_action_result['event_id'] . '&reviewers_email_flag=1';
				}
			}

		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}
	function MergeReviewersVersions($pRoundId){
		try{
			if ($pRoundId){ // Merge versions
				$lMversions = new mVersions();
				if(!$lMversions->CreatePwtEditorVersionFromReviewerVersions($pRoundId)){
					$this->m_action_result['err_cnt'] = 0;
					$this->m_errCnt++;
				}
			}
		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}
	function DisableInvitingUsers($pRoundId, $pDocumentId) {
		try{
			if ($pRoundId){ // Merge versions
				$lDocumentsModel = new mDocuments_Model();
				$this->m_action_result = $lDocumentsModel->DisableInvitingUsersForRound($pRoundId, $pDocumentId);
			}
		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}

	function CheckInvitedReviewersForRound($pRoundId, $pDocumentId) {
		try{
			if ($pRoundId){ // Merge versions
				$lDocumentsModel = new mDocuments_Model();
				$this->m_action_result = $lDocumentsModel->CheckInvitedReviewersForRound($pRoundId, $pDocumentId);
			}
		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}

	function CheckSubmittedReviewersForRound($pRoundId, $pDocumentId) {
		try{
			if ($pRoundId){ // Merge versions
				$lDocumentsModel = new mDocuments_Model();
				$this->m_action_result = array_merge($this->m_action_result,$lDocumentsModel->CheckNonSubmitedUsersForRound($pRoundId, $pDocumentId));
				//var_dump($this->m_action_result);
			}
		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}

	function ManageUserInvitationsAndReviews($pRoundId, $pDocumentId, $pInvitedUserIds, $pNonSubmUserIds) {
		try{
			$lDocumentsModel = new mDocuments_Model();
			$this->m_action_result = $lDocumentsModel->ManageUserInvitationsAndReviews($pRoundId, $pDocumentId, $pInvitedUserIds, $pNonSubmUserIds);
			if($this->m_action_result['err_cnt']){
				array_walk_recursive($this->m_action_result['err_msgs'], translate_array_elem);
				$this->m_errCnt = $this->m_action_result['err_cnt'];
				$this->m_errMsgs = $this->m_action_result['err_msgs'];
			} else {
				foreach ($this->m_action_result['event_ids'] as $key => $value) {

					/**
					 * Manage event task (submitting new document)
					 */
					$lTaskObj = new cTask_Manager(array(
						'event_id' => (int)$value,
					));
					$lTaskObj->Display();
				}
			}

		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}

	function SaveLEXMLVersion(){
		$lDocumentsModel = new mDocuments_Model();
		$lJournalModel = new mJournal();
		
		$lDocumentId =  $this->GetValueFromRequestWithoutChecks('document_id');
		$lXML =  $this->GetValueFromRequestWithoutChecks('doc_xml');
		
		$lLayoutData = $lJournalModel->GetDocumentLayoutData($lDocumentId);
		if((int)$lLayoutData['uid'] == $this->GetUserId()) {
			$lDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
			// check if xml is valid
			if(!$lDom->loadXML($lXML)){
				$this->m_errCnt ++;
				$this->m_errMsgs[] = array(
					'err_msg' => getstr('pjs.xmlIsNotValid'),
				);
			} else {
				try{
					$lResult = $lDocumentsModel->SaveLEXMLVersion($lLayoutData['le_version_id'], $lXML);
					if((int)$lResult['err_cnt']) {
						$this->m_errCnt = (int)$lResult['err_cnt'];
						$this->m_errMsgs = $lResult['err_msgs'];
					}
				}catch(Exception $lException){
					$this->m_errCnt ++;
					$this->m_errMsgs[] = array(
						'err_msg' => $lException->getMessage()
					);
				}	
			}
		} else {
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => getstr('pjs.no_such_layout_for_this_document'),
			);
		}
		
	}

	function RevertLEXMLVersion(){
		$lDocumentsModel = new mDocuments_Model();
		$lJournalModel = new mJournal();
		
		$lDocumentId =  $this->GetValueFromRequestWithoutChecks('document_id');
		
		$lLayoutData = $lJournalModel->GetDocumentLayoutData($lDocumentId);
		if((int)$lLayoutData['uid'] == $this->GetUserId()) {
			try{
				$lResult = $lDocumentsModel->RevertLEXMLVersion($lDocumentId);
				if((int)$lResult['err_cnt']) {
					$this->m_errCnt = (int)$lResult['err_cnt'];
					$this->m_errMsgs = $lResult['err_msgs'];
				} else {
					
					$lDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
					$lDom->formatOutput = true;
					$lDom->loadXML($lResult['doc_xml']);
					$lLEXMLVersion = $lDom->saveXML();
					
					$this->m_action_result['doc_xml'] = $lLEXMLVersion;
				}
			}catch(Exception $lException){
				$this->m_errCnt ++;
				$this->m_errMsgs[] = array(
					'err_msg' => $lException->getMessage()
				);
			}	
		} else {
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => getstr('pjs.no_such_layout_for_this_document'),
			);
		}
		
	}

}

?>