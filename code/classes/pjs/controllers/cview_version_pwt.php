<?php
class cView_Version_Pwt extends cView_Version {
	var $m_versionXml;
	var $m_documentPwtId;
	var $m_xmlPreview;
	var $m_ReadOnlyPreview = 0;
	var $m_viewMode = 0;
	var $m_roundData = array();
	var $m_submitAction;
	var $m_round_user_id;
	var $m_reviewer_uid;
	var $m_reviewer_role;
	var $m_hasEditorPermissions;
	var $m_tempViewObject;
	var $m_PollQuestionsData = array();
	var $m_PollAnswersData = array();

	function InitObjects() {
		global $user;
		
		$this->m_tempViewObject = $this->CreateViewObject();

		// check for read only or edit version mode
		$this->CheckVersionReadOnly();
		// get some additional data
		$this->m_documentPwtId = $this->m_versionModel->GetVersionDocumentPwtId($this->m_versionId);
		$this->GetVersionXml();

		$this->m_hasEditorPermissions = false;
		if($this->m_versionModel->CheckUserSpecificRole($this->GetUserId(), (int)$this->m_documentId)){
			$this->m_hasEditorPermissions = true;
		}


		if($this->m_errCnt){
			$pViewPageObjectsDataArray = array(
			'preview' => array(
				'ctype' => 'evSimple_Block_Display',
				'name_in_viewobject' => 'no_premissions',
				'controller_data' => $this->m_errMsgs,
			));
		} else {
			$lFormErrsCnt = 0;
			$this->m_PollQuestionsData = $this->m_versionModel->GetPollQuestions((int)$this->m_documentData['journal_id'], (int)$this->m_versionId);
			// reviewers changes list
			$lESECheckUserRole = $this->m_hasEditorPermissions;
			if(($this->m_viewingRole == SE_ROLE || $this->m_viewingRole == JOURNAL_EDITOR_ROLE) && $lESECheckUserRole){
				$lVersionUidChanges = $this->m_versionModel->GetVersionPwtChangeUserIds($this->m_versionId);
// 				var_dump($lVersionUidChanges);
				$lVersionUserLegend = array(
					'ctype' => 'evList_Display',
					'name_in_viewobject' => 'user_legend',
					'controller_data' => $lVersionUidChanges,
					'has_editor_permissions' => $this->m_hasEditorPermissions,
					'current_user_id' => $this->GetUserId(),
				);

			}

			// left col
			$lDocumentVersionLeftCol = new evList_Display(array(
				'controller_data' => $this->m_versionModel->GetDocumentStructure((int)$this->m_documentId, $this->m_versionId),
				'name_in_viewobject' => 'document_structure',
			));

			$lFieldsMetadataTempl = $this->GetFormFieldTempls();
			if($this->m_viewingRole == AUTHOR_ROLE) {
				$lForm = '';
				$lReviewerPoll = '';
				$lUserLegend = '';
				$lAuthorData = $this->m_versionModel->GetAuthorsDetails((int)$this->m_versionId);
				$lAuthorName = $lAuthorData['first_name'] . ' ' . $lAuthorData['last_name'];
				$lAuthorVersionNumber = $lAuthorData['version_num'];
			} elseif($this->m_viewingRole == PUBLIC_ROLE) {
				$lForm = '';
				$lReviewerPoll = '';
				$lUserLegend = '';
				$lAuthorData = $this->m_versionModel->GetAuthorsDetails((int)$this->m_versionId);
				$lAuthorName = $lAuthorData['first_name'] . ' ' . $lAuthorData['last_name'];
				$lAuthorVersionNumber = $lAuthorData['version_num'];
			} elseif($this->m_viewingRole == SE_ROLE || $this->m_viewingRole == JOURNAL_EDITOR_ROLE) {
				$lNameInViewObjects = 'semode';
				$lSEDecisionData = $this->m_versionModel->GetSEDecisionDetails((int)$this->m_versionId);
				$lSEDecisionName = $lSEDecisionData['decision'];
				$lSEName = $lSEDecisionData['se_name'];

				if($lESECheckUserRole) {
					if(!(int)$this->m_ReadOnlyPreview) {
						$lForm = new viewVersion_Form_Wrapper(array(
							'ctype' => 'viewVersion_Form_Wrapper',
							'page_controller_instance' => $this,
							'name_in_viewobject' => $lNameInViewObjects,
							'view_object' => $this->m_tempViewObject,
							'use_captcha' => 0,
							'm_debug' => true,
							'form_method' => 'POST',
							'form_name' => 'document_review_form',
							'dont_close_session' => true,
							'version_id' => (int)$this->m_versionId,
							'document_id' => (int)$this->m_documentId,
							'role' => (int)$this->m_viewingRole,
							'user_role' => $this->m_reviewer_role,
							'fields_metadata' => $lFieldsMetadataTempl,
							'version_uid' => $this->m_versionUID,
							'round_id' => $this->m_roundId,
						));
						$lForm->Display();
						$lFormErrsCnt = $lForm->GetErrorCount();

					}

					$lReviewerPoll = array(
						'ctype' => 'evList_Display',
						'name_in_viewobject' => 'reviewerpoll',
						'role' => (int)$this->m_viewingRole,
						'controller_data' => $this->m_versionModel->GetReviewersAnswers((int)$this->m_documentId, $this->m_versionUserRoundId),
					);
				} else {
					$lForm = '';
					$lReviewerPoll = '';
				}
			} elseif($this->m_viewingRole == DEDICATED_REVIEWER_ROLE) {
				$lNameInViewObjects = 'reviewermode';
				
				//$i = 1;
				global $gQuestions;
				$gQuestions = array();
				foreach ($this->m_PollQuestionsData as $key => $value) {
					//$lQuestions .= ($i == count($this->m_PollQuestionsData) ? $value['id'] : $value['id'] . ',' );
					$gQuestions[] = $value['id'];
					//$i++;
				}
				$lForm = new viewVersion_Form_Wrapper(array(
					'ctype' => 'viewVersion_Form_Wrapper',
					'page_controller_instance' => $this,
					'name_in_viewobject' => $lNameInViewObjects,
					'view_object' => $this->m_tempViewObject,
					'use_captcha' => 0,
					'm_debug' => true,
					'form_method' => 'POST',
					'form_name' => 'document_review_form',
					'dont_close_session' => true,
					'version_id' => (int)$this->m_versionId,
					'document_id' => (int)$this->m_documentId,
					'role' => (int)$this->m_viewingRole,
					'user_role' => $this->m_reviewer_role,
					'fields_metadata' => $lFieldsMetadataTempl,
					'view_mode' => $this->m_viewMode,
					'version_uid' => $this->m_versionUID,
					'round_id' => $this->m_roundId,
				));
				$lForm->Display();
				$lFormErrsCnt = $lForm->GetErrorCount();
				$lReviewerPoll = '';
				$lUserLegend = '';
			} elseif($this->m_viewingRole == CE_ROLE) {
				$lNameInViewObjects = 'cemode';
				if(!(int)$this->m_ReadOnlyPreview) {
					$lForm = new viewVersion_Form_Wrapper(array(
						'ctype' => 'viewVersion_Form_Wrapper',
						'page_controller_instance' => $this,
						'name_in_viewobject' => $lNameInViewObjects,
						'view_object' => $this->m_tempViewObject,
						'use_captcha' => 0,
						'm_debug' => true,
						'form_method' => 'POST',
						'form_name' => 'document_review_form',
						'dont_close_session' => true,
						'version_id' => (int)$this->m_versionId,
						'document_id' => (int)$this->m_documentId,
						'role' => (int)$this->m_viewingRole,
						'user_role' => $this->m_reviewer_role,
						'fields_metadata' => $lFieldsMetadataTempl,
						'version_uid' => $this->m_versionUID,
						'round_id' => $this->m_roundId,
					));
					$lForm->Display();
					$lFormErrsCnt = $lForm->GetErrorCount();
				}
			}
			$lCommentsData = $this->m_versionModel->GetVersionComments($this->m_versionId, true);

			$lComments = array(
				'ctype' => 'evList_Group_Display',
				'name_in_viewobject' => 'comments',
				'splitcol' => 'rootid',
				'controller_data' => $lCommentsData,
				'has_editor_permissions' => $this->m_hasEditorPermissions,
				'current_user_id' => $this->GetUserId(),
				'version_is_readonly' => $this->m_ReadOnlyPreview,
			);

			$lNewCommentForm = new New_Comment_Form_Wrapper(array(
				'page_controller_instance' => $this,
				'name_in_viewobject' => 'new_comment_form',
				'view_object' => $this->m_pageView,
				'use_captcha' => 0,
				'm_debug' => false,
				'uid' => $user->id,
				'user_fullname' => $user->fullname,
				'version_id' => $this->m_versionId,
				'form_method' => 'POST',
				'form_name' => 'newCommentForm',
			));


			$pViewPageObjectsDataArray = array(
				'preview' => array(
					'ctype' => 'evSimple_Block_Display',
					'name_in_viewobject' => 'preview',
					'author_name' => $lAuthorName,
					'author_version_num' => $lAuthorVersionNumber,
					'version_id' => $this->m_versionId,
					'controller_data' => $this->m_errMsgs,
					'fullname' => $this->m_user->fullname,
					'previewpicid' => $this->m_user->photo_id,
					'role' => $this->m_viewingRole,
					'user_legend' => $lVersionUserLegend,
					'current_user_id' => $user->id,
					'current_user_name' => $user->fullname,
					'previewmode' => $this->m_viewMode,
					'structure' => $lDocumentVersionLeftCol,
					'readonly' => (int)$this->m_viewMode,
					'read_only' => ($this->m_viewMode ? $this->m_viewMode : 2),
					'is_se' => $lESECheckUserRole,
					'name' => ($lSEName ? $lSEName : $this->m_roundData['name']),
					'decision' => ($lSEDecisionName ? $lSEDecisionName : $this->m_roundData['decision']),
					'se_opening' => (($this->m_viewingRole == SE_ROLE || $this->m_viewingRole == JOURNAL_EDITOR_ROLE) ? 1 : 0),
					'form' => $lForm,
					'reviewerpoll' => $lReviewerPoll,
					'comments' => &$lComments,
					'new_comment_form' => $lNewCommentForm,
					'version_is_readonly' => $this->m_ReadOnlyPreview,
				),
				'form' => $lForm,
				'form_has_errors' => $lFormErrsCnt,
				'users_with_changes' => $lVersionUidChanges,
			);

		}
		
		$this->m_pageView = $this->CreateViewObject($pViewPageObjectsDataArray);
		$lCommentReplyForms = array();
		foreach ($lCommentsData as $lCurrentComment) {
			$lCommentRootId = $lCurrentComment['rootid'];
			if(!array_key_exists($lCommentRootId, $lCommentReplyForms)){
				$lForm = new Comment_Reply_Form_Wrapper(array(
						'page_controller_instance' => $this,
						'name_in_viewobject' => 'comment_reply_form',
						'view_object' => $this->m_pageView,
						'use_captcha' => 0,
						'm_debug' => false,
						'uid' => $user->id,
						'user_fullname' => $user->fullname,
						'rootid' => $lCommentRootId,
						'form_method' => 'POST',
						'form_name' => 'comment_reply_form' . $lCommentRootId,
				));

				$lCommentReplyForms[$lCommentRootId] = $lForm->Display();
			}
		}
		$lComments['comment_reply_forms'] = $lCommentReplyForms;
		$lCommentEditForms = array();
		foreach ($lCommentsData as $lCurrentComment) {
			$lCommentId = $lCurrentComment['id'];
			if(!array_key_exists($lCommentId, $lCommentEditForms)){
				$lForm = new Comment_Edit_Form_Wrapper(array(
					'page_controller_instance' => $this,
					'name_in_viewobject' => 'edit_comment_form',
					'view_object' => $this->m_pageView,
					'use_captcha' => 0,
					'm_debug' => false,
					'uid' => $this->GetUserId(),
					'user_fullname' => $user->fullname,
					'version_id' => $this->m_versionId,
					'document_id' => $this->m_documentId,
					'form_method' => 'POST',
					'comment_id' => $lCommentId,
					'form_name' => 'comment_edit_' . $lCommentId
				));
		
				$lCommentEditForms[$lCommentId] = $lForm->Display();
			}
		}
		$lComments['comment_edit_forms'] = $lCommentEditForms;
	}

	/**
	 * CheckVersionReadOnly
	 * checks if objects must be in read only mode (view mode)
	 *
	 * @return void
	 */
	private function CheckVersionReadOnly() {	
		$this->m_ReadOnlyPreview = (int)$this->m_versionModel->CheckIfVersionIsReadonly($this->m_versionId);
	}

	/**
	 * GetVersionXml
	 * gets the version xml
	 *
	 * @return void
	 */
	private function GetVersionXml() {
		try{
			if($this->m_versionModel->CheckIfVersionHasUnprocessedPwtChanges($this->m_versionId)){
				$lProcession = $this->m_versionModel->ProcessVersionPwtChanges($this->m_versionId);

				if($lProcession['err_cnt']){
					throw  new Exception($lProcession['err_msgs'][0]['err_msg']);
				}
			}
			$lXmlData = $this->m_versionModel->GetVersionPwtXml($this->m_versionId);
			if($lXmlData['err_cnt']){
				throw new Exception($lProcession['err_msgs'][0]['err_msg']);
			}
			$this->m_versionXml = $lXmlData['xml'];
		}catch(Exception $pException){
			$this->m_errCnt++;
			$this->m_errMsgs[] = array('err_msg' => $pException->getMessage());
		}
	}

	private function GetFormFieldTempls() {
		$this->m_roundData = $this->m_versionModel->GetUserRoundDataByVersion($this->m_versionId);
		$this->m_reviewer_role = $this->m_roundData['reviewer_role'];
		$lSaveFormJSAction = 'return SaveReviewForm();';
		$lReviewFormJSAction = 'return SubmitReviewForm();';
		if ((int)$this->m_ReadOnlyPreview){
			$this->m_viewMode = 1;
			$lSubmitOrClose = 'popupClosingAndReloadParent()';
		} else {
			// save action
			switch ($this->m_viewingRole){
				case (int) JOURNAL_EDITOR_ROLE :
				case (int) SE_ROLE :
					$this->m_submitAction = '\'save_se_decision\'';
					$lSubmitDisplayName = getstr('admin.article_versions.review');
					break;
				case (int) COMMUNITY_REVIEWER_ROLE :
				case (int) DEDICATED_REVIEWER_ROLE :
					$this->m_submitAction = '\'save_reviewer_decision\'';
					$lSubmitDisplayName = getstr('admin.article_versions.review');
					break;
				case (int) CE_ROLE :
					$this->m_submitAction = '\'save_ce_decision\'';
					$lSubmitDisplayName = getstr('admin.article_versions.ce_review');
					break;
			}
			$this->m_viewMode = 0;
			$this->m_round_user_id = (int)$this->m_roundData['id'];
			$this->m_reviewer_uid = $this->m_roundData['reviewer_uid'];
			$lSubmitOrClose = 'savePopUpUserDecision(' . $this->m_submitAction . ', ' . (int)$this->round_user_id . ', '. (int)$this->m_documentId . ', ' . $this->m_viewingRole . ', ' . $this->reviewer_uid . ', 1); return false';
			$lSaveBtnEvent = 'serializeAndSubmitFormThenCloseAndRefresh(); return false;';
		}

		switch ($this->m_viewingRole) {
			case SE_ROLE:
			case E_ROLE:
			case CE_ROLE:
				if(!$this->m_ReadOnlyPreview) {

					$lSubmitReviewText = getstr('admin.article_versions.review');
					if(in_array($this->m_viewingRole, array(SE_ROLE, E_ROLE))) {
						$lSubmitReviewText = getstr('pjs.submitSEDecisionButtonText');
					}

					$lFieldsMetadataTempl = array(
						'url_params' => array (
							'VType' => 'string' ,
							'CType' => 'hidden',
							'AllowNulls' => true,
						),
						'close' => array (
							'VType' => 'int' ,
							'CType' => 'hidden',
							'AllowNulls' => true,
						),
						'previewmode' => array (
							'VType' => 'int' ,
							'CType' => 'hidden',
							'DefValue' => $this->m_viewMode,
							'AllowNulls' => true,
							'AddTags' => array(
								'id' => 'previewMode',
							)
						),
						'id' => array (
							'VType' => 'int' ,
							'CType' => 'hidden',
							'AllowNulls' => true,
						),
						'notes_to_author' => array(
							'CType' => 'textarea',
							'VType' => 'string',
							'DisplayName' => getstr('admin.article_versions.forauthor'),
							'AllowNulls' => true,
							'AddTags' => array(
								'cols' => '',
								'rows' => '',
								'class' => 'review',
							)
						),
						'decision_id' => array(
							'VType' => 'string',
							'CType' => 'radio',
							'DisplayName' => getstr('admin.article_versions.recomend'),
							'AllowNulls' => true,
							'TransType' => MANY_TO_SQL_ARRAY,
							'SrcValues' => $this->m_versionModel->getDecisions($this->m_viewingRole, $this->m_versionId),
						),
						'new' => array(
							'CType' => 'action',
							'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_SHOW | ACTION_FETCH,
							'SQL' => 'SELECT * FROM spSaveDocument_review_round_users_form(2, ' . ($this->m_versionId) . ', ' . ($this->m_roundId ? $this->m_roundId : ($this->m_versionUserRoundId ? $this->m_versionUserRoundId : 'null')) . ', null, null, null, null, null, null)/*{id}{close}{url_params}*/',
						),
						'save' => array(
							'CType' => 'action',
							'SQL' => 'SELECT spSaveDocument_review_round_users_form(1, ' . ($this->m_versionId) . ', ' . ($this->m_roundId ? $this->m_roundId : ($this->m_versionUserRoundId ? $this->m_versionUserRoundId : 'null')) . ', {decision_id}, {notes_to_author}, null, null, null, ' . (int)$this->m_viewingRole . ')/*{id}{close}{url_params}*/',
							'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC,
							'DisplayName' => getstr('admin.article_versions.save'),
							'AddTags' => array(
								'class' => 'inputBtn',
								'onclick' => $lSaveFormJSAction,
							)
						),
						'review' => array(
							'CType' => 'action',
							'SQL' => 'SELECT spSaveDocument_review_round_users_form(1, ' . ($this->m_versionId) . ', ' . ($this->m_roundId ? $this->m_roundId : ($this->m_versionUserRoundId ? $this->m_versionUserRoundId : 'null')) . ', {decision_id}, {notes_to_author}, null, null, null, ' . (int)$this->m_viewingRole . ')/*{id}{close}{url_params}*/',
							'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC,
							'DisplayName' => $lSubmitReviewText,
							'AddTags' => array(
								'class' => 'inputBtn',
								'onclick' => 'if(confirm(\'' . getstr('pjs.SE_E_CE_submit_review_confirmation') . '\')){' . $lReviewFormJSAction . '} else {return false;}',
							)
						),
					);
				}
			break;
			case (int) COMMUNITY_REVIEWER_ROLE :
			case (int) DEDICATED_REVIEWER_ROLE :
				$this->m_PollAnswersData = $this->m_versionModel->GetPollAnswers($this->m_versionId);
				
				$lQuestionsArr = array();
				$lQuestionsAddToChecks = '';
				$lUpdateSql = '';
				$lWhereCondSqlAdd = (
					count($this->m_PollAnswersData) ? 
					'rel_element_id = {id}' : 
					'rel_element_id = currval(\'pjs.document_review_round_users_form_id_seq\')'
				);
				foreach ($this->m_PollQuestionsData as $key => $value) {
					$lQuestionName = 'question' . $value['id'];
					$lQuestionsAddToChecks .= '{question' . $value['id'] . '}';
					if($_REQUEST[$lQuestionName]) {
						$lDefault = $_REQUEST[$lQuestionName];
					} elseif ((int)$this->m_PollAnswersData[$lQuestionName]) {
						$lDefault = (int)$this->m_PollAnswersData[$lQuestionName];
					} else {
						$lDefault = null;
					}
					
					$lUpdateSql .= '
						UPDATE pjs.poll_answers 
						SET 
							answer_id = {' . $lQuestionName . '} 
						WHERE poll_id = ' . $value['id'] . ' 
							AND rel_element_type = ' . REVIEWER_POLL_ELEMENT_TYPE . '
							AND ' . $lWhereCondSqlAdd . ';
					';
					$lQuestionsArr[] = array(
						$lQuestionName => array(
							'VType' => 'int',
							'CType' => 'radio',
							'DisplayName' => $value['label'],
							'AllowNulls' => true,
							'SrcValues' => array(
								1 => '',
								2 => '',
								3 => '',
								4 => '',
							),
							'DefValue' => $lDefault,
						)
					);
				}
				
				$lFieldsMetadataTempl = array(
					'url_params' => array (
						'VType' => 'string' ,
						'CType' => 'hidden',
						'AllowNulls' => true,
					),
					'close' => array (
						'VType' => 'int' ,
						'CType' => 'hidden',
						'AllowNulls' => true,
					),
					'previewmode' => array (
						'VType' => 'int' ,
						'CType' => 'hidden',
						'DefValue' => $this->m_viewMode,
						'AllowNulls' => true,
						'AddTags' => array(
							'id' => 'previewMode',
						)
					),
					'id' => array (
						'VType' => 'int' ,
						'CType' => 'hidden',
						'AllowNulls' => true,
					),
					'question0' => array(
						'VType' => 'int',
						'CType' => 'radio',
						'DisplayName' => 'Poll',
						'AllowNulls' => true,
						'SrcValues' => array(
							1 => '',
							2 => '',
							3 => '',
							4 => '',
						),
					),
					'notes_to_author' => array(
						'CType' => 'textarea',
						'VType' => 'string',
						'DisplayName' => getstr('admin.article_versions.forauthor'),
						'AllowNulls' => true,
						'AddTags' => array(
							'cols' => '',
							'rows' => '',
							'class' => 'review',
						)
					),
					'notes_to_editor' => array(
						'CType' => 'textarea',
						'VType' => 'string',
						'DisplayName' => getstr('admin.article_versions.foreditor'),
						'AllowNulls' => true,
						'AddTags' => array(
							'cols' => '',
							'rows' => '',
							'class' => 'review',
						)
					),
					'decision_id' => array(
						'VType' => 'string',
						'CType' => 'radio',
						'DisplayName' => getstr('admin.article_versions.recomend'),
						'AllowNulls' => true,
						'TransType' => MANY_TO_SQL_ARRAY,
						'SrcValues' => $this->m_versionModel->getDecisions($this->m_viewingRole, $this->m_versionId),
					),
					'disclose_name' => array(
						'VType' => 'int',
						'CType' => 'checkbox',
						'DisplayName' => getstr('admin.article_versions.recomend.option6'),
						'AllowNulls' => true,
						'TransType' => MANY_TO_BIT,
						'DefValue' => 1,
						'SrcValues' => array(
							1 => '',
						),
						'AddTags' => array()
					),
					// 'publish_review' => array(
						// 'VType' => 'int',
						// 'CType' => 'checkbox',
						// 'DisplayName' => getstr('admin.article_versions.recomend.option7'),
						// 'AllowNulls' => true,
						// 'TransType' => MANY_TO_BIT,
						// 'SrcValues' => array(
							// 1 => '',
						// ),
						// 'AddTags' => array()
					// ),
					'new' => array(
						'CType' => 'action',
						'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_SHOW | ACTION_FETCH,
						'SQL' => 'SELECT * FROM spSaveDocument_review_round_users_form(2, ' . ($this->m_versionId) . ', ' . ($this->m_roundId ? $this->m_roundId : ($this->m_versionUserRoundId ? $this->m_versionUserRoundId : 'null')) . ', null, null, null, null, null, null);/*{question0}{id}{close}{url_params}' . $lQuestionsAddToChecks . '*/',
					),
					'save' => array(
						'CType' => 'action',
						'SQL' => '
						BEGIN;
							SELECT spSaveDocument_review_round_users_form(1, ' . ($this->m_versionId) . ', ' . ($this->m_roundId ? $this->m_roundId : ($this->m_versionUserRoundId ? $this->m_versionUserRoundId : 'null')) . ', {decision_id}, {notes_to_author}, {notes_to_editor}, {disclose_name}, 1, null);/*{question0}{id}{close}{url_params}' . $lQuestionsAddToChecks . '*/ 
						' . $lUpdateSql . '
						COMMIT;
						',
						'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC,
						'DisplayName' => getstr('admin.article_versions.save'),
						'AddTags' => array(
							'class' => 'inputBtn',
							'onclick' => $lSaveFormJSAction,
						)
					),
					'review' => array(
						'CType' => 'action',
						'SQL' => '
						BEGIN;
							SELECT spSaveDocument_review_round_users_form(1, ' . ($this->m_versionId) . ', ' . ($this->m_roundId ? $this->m_roundId : ($this->m_versionUserRoundId ? $this->m_versionUserRoundId : 'null')) . ', {decision_id}, {notes_to_author}, {notes_to_editor}, {disclose_name}, 1, null);/*{question0}{id}{close}{url_params}' . $lQuestionsAddToChecks . '*/
						' . $lUpdateSql . '
						COMMIT;	
						',
						
						'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC,
						'DisplayName' => getstr('admin.article_versions.review'),
						'AddTags' => array(
							'class' => 'inputBtn',
							'onclick' => 'if(confirm(\'' . getstr('pjs.R_submit_review_confirmation') . '\')){' . $lReviewFormJSAction . '} else {return false;}',
							//'onclick' => $lSubmitOrClose,
						),
					),
				);
				foreach ($lQuestionsArr as $key => $value) {
					$lFieldsMetadataTempl = array_merge($value, $lFieldsMetadataTempl);	
				}
			break;
		}
		return $lFieldsMetadataTempl;
	}
	
	function CreateViewObject($pParams = array()){
		if(!is_array($pParams)){
			$pParams = array();
		}
		$lPageIsCalledFromAjax = (int)$this->GetValueFromRequestWithoutChecks('ajax_form_submit');
		$pParams = array_merge($this->m_commonObjectsDefinitions, $pParams);
		if(!$lPageIsCalledFromAjax){
			return new pView_Version_Pwt($pParams);
		}else{
			return new pView_Version_Pwt_Ajax($pParams);
		}
	}

	function Display(){
		return $this->m_pageView->Display();
	}
}



?>