<?php

class cArticle_Comments_Form_Controller extends cBase_Controller {
	var $m_action_result;
	var $m_articlesModel;
	var $m_tempPageView;
	var $m_articleId;
	var $m_journalId;
	var $m_errCnt = 0;
	var $m_errMsg = '';
	var $m_success = 0;
	var $m_successMsg = '';
	var $m_PollQuestionsData = array();
	var $m_PollAnswersData = array();
	var $m_Messageid;

	function __construct() {
		global $rewrite;
		parent::__construct();
		$pViewPageObjectsDataArray = array ();
		$this->m_action_result = array ();
		$this->m_articlesModel = new mArticles();
		$this->m_tempPageView = new pArticles_Ajax_Srv(array());
		$this->m_articleId = (int) $this->GetValueFromRequestWithoutChecks('article_id');
		$this->m_journalId = (int) $this->GetValueFromRequestWithoutChecks('journal_id');
		$lShowForm = (int)$this->GetValueFromRequestWithoutChecks('show_form');
		
		$lHideFormFlag = 0;
		if(!$this->GetUserId()) {
			$lHideFormFlag = 1;
		} else {
			$this->m_Messageid = (int)$this->m_articlesModel->GetAOFUserCommentMessageId((int)$this->GetUserId(), (int)$this->m_articleId);
			if((!$this->m_Messageid && !$_REQUEST['tAction']) && !$lShowForm) {
				$lHideFormFlag = 1;
			}
		}
		
		if(!$lHideFormFlag) {
			$this->m_PollQuestionsData = $this->m_articlesModel->GetAOFCommentPollQuestions($this->m_journalId, $this->m_Messageid);
			$this->m_PollAnswersData = $this->m_articlesModel->GetAOFCommentPollAnswers($this->m_Messageid);
			//trigger_error('TEST: ' . $this->m_Messageid, E_USER_NOTICE);
			
			global $gQuestions;
			$gQuestions = array();
			$lQuestionsArr = array();
			$lQuestionsAddToChecks = '';
			$lUpdateSql = '';
			$lWhereCondSqlAdd = (
				count($this->m_PollAnswersData) ? 
				'rel_element_id = {id}' : 
				'rel_element_id = currval(\'pjs.document_review_round_users_form_id_seq\')'
			);
			foreach ($this->m_PollQuestionsData as $key => $value) {
				$gQuestions[] = $value['id'];
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
						AND rel_element_type = ' . AOF_COMMENT_POLL_ELEMENT_TYPE . '
						AND ' . $lWhereCondSqlAdd . ';
				';
				$lQuestionsArr[] = array(
					$lQuestionName => array(
						'VType' => 'int',
						'CType' => 'radio',
						'DisplayName' => $value['label'],
						'AllowNulls' => true,
						'SrcValues' => array(
							1 => 'Yes',
							2 => 'Moderately',
							3 => 'No',
							4 => 'N/A',
						),
						'DefValue' => $lDefault,
					)
				);
			}
			
			$lFieldsMetadataTempl = array(
				'id' => array(
					'CType' => 'hidden',
					'VType' => 'int',
					'AllowNulls' => true,
				),
				'event_id' => array(
					'CType' => 'hidden',
					'VType' => 'int',
					'AllowNulls' => true,
				),
				'journal_id' => array(
					'CType' => 'hidden',
					'VType' => 'int',
					'AllowNulls' => true,
				),
				'article_id' => array(
					'CType' => 'hidden',
					'VType' => 'int',
					'AllowNulls' => true,
					'AddTags' => array(
						'id' => 'comments_article_id'
					),
				),
				'user_id' => array(
					'CType' => 'hidden',
					'VType' => 'int',
					'AllowNulls' => true,
					'DefValue' => (int)$this->GetUserId()
				),
				'message' => array(
					'CType' => 'textarea',
					'VType' => 'string',
					'DisplayName' => getstr('pjs.article_message'),
					'AllowNulls' => true,
					'AddTags' => array(
						'cols' => '',
						'rows' => '',
						'class' => 'article_comment_textarea',
						//'id' => 'article_comment_textarea',
					),
					'RichText' => 1,
				),
				'new' => array(
					'CType' => 'action',
					'SQL' => 'SELECT * FROM pjs."spProcessArticleComment"(1, null, {user_id}, {article_id}, {journal_id}, {message})',
					'DisplayName' => '',
					'ActionMask' =>  ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
					'Hidden' => true,
				),
				'save' => array(
					'CType' => 'action',
					'SQL' => '
						BEGIN;
							SELECT * FROM pjs."spProcessArticleComment"(5, {id}, {user_id}, {article_id}, {journal_id}, {message});
							' . $lUpdateSql . '
						COMMIT;',
					'DisplayName' => '',
					
					'ActionMask' =>  ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
					'Hidden' => true,
				),
				'comment' => array(
					'CType' => 'action',
					'SQL' => '
						BEGIN;
							SELECT * FROM pjs."spProcessArticleComment"(4, {id}, {user_id}, {article_id}, {journal_id}, {message});
							' . $lUpdateSql . '
						COMMIT;	',
					'DisplayName' => 'Post',
					'ActionMask' =>  ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
					'Hidden' => true,
				),
				'delete' => array(
					'CType' => 'action',
					'SQL' => 'SELECT * FROM pjs."spProcessArticleComment"(6, {id}, {user_id}, {article_id}, {journal_id}, {message})',
					'DisplayName' => 'Cancel',
					'ActionMask' =>  ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
					'Hidden' => true,
				),
				'approve' => array(
					'CType' => 'action',
					'SQL' => 'SELECT * FROM pjs."spProcessArticleComment"(2, {id}, {user_id}, {article_id}, {journal_id}, null)',
					'DisplayName' => '',
					'ActionMask' =>  ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
					'Hidden' => true,
				),
				'reject' => array(
					'CType' => 'action',
					'SQL' => 'SELECT * FROM pjs."spProcessArticleComment"(3, {id}, {user_id}, {article_id}, {journal_id}, null)',
					'DisplayName' => '',
					'ActionMask' =>  ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
					'Hidden' => true,
				),
			);
			
			foreach ($lQuestionsArr as $key => $value) {
				$lFieldsMetadataTempl = array_merge($value, $lFieldsMetadataTempl);	
			}
			
			$lForm = new Article_Comments_Form_Wrapper(
				array(
					'page_controller_instance' => $this,
					'name_in_viewobject' => 'comments_form_templ',
					'use_captcha' => 0,
					'form_method' => 'POST',
					'form_action' => '/article_comment_form.php',
					'js_validation' => 0,
					'form_name' => 'article_comments_form',
					'dont_close_session' => true,
					'fields_metadata' => $lFieldsMetadataTempl,
					'htmlformid' => 'article_comments_form',
				)
			);
			
			$lForm->setViewObject($this->m_tempPageView);
			$lFormFlag = 1;
		} else {
			$lNameInViewObject = 'forum_show_comment_link';
			if(!$this->GetUserId()) {
				$lNameInViewObject = 'forum_no_logged_user';	
			}
			
			$lForm = new evSimple_Block_Display(array(
				'name_in_viewobject' => $lNameInViewObject,
				'view_object' => $this->m_tempPageView,
				'article_id' => $this->m_articleId,
				'journal_id' => $this->m_journalId,
			));
		}
		
		$lResult = new evSimple_Block_Display(array(
			'name_in_viewobject' => 'forum_wrapper',
			'view_object' => $this->m_tempPageView,
			'form' => $lForm,
		));
		
		$this->m_action_result['html'] = $lResult->Display();
		if($lFormFlag){
			if($lForm->GetCurrentAction() == 'comment' || $lForm->GetCurrentAction() == 'approve' || $lForm->GetCurrentAction() == 'reject') {
				if($lForm->GetErrorCount()) {
					if(!strip_tags($lForm->GetFieldValue('message')) && $lForm->GetCurrentAction() == 'comment') {
						$lErrMsg = 'pjs.empty_message';
					} else {
						$lFormGlobalErrors = $lForm->GetFormGlobalErrors();
						$lErrMsg = $lFormGlobalErrors[0];	
					}
					$this->m_errCnt++;
					$this->m_errMsg = getstr($lErrMsg);
				} else {
					$this->m_success++;
					if($lForm->GetCurrentAction() == 'comment') {
						$this->m_successMsg = getstr('pjs.article_comment_success');
					} else if($lForm->GetCurrentAction() == 'approve') {
						$this->m_successMsg = getstr('pjs.article_comment_approve_success');
					} else {
						$this->m_successMsg = getstr('pjs.article_comment_reject_success');
					}
				}
			}
		}
		$lResultArr = array_merge($this->m_action_result, array (
			'err_cnt' => $this->m_errCnt,
			'err_msg' => $this->m_errMsg,
			'success' => $this->m_success,
			'success_msg' => $this->m_successMsg,
		));
		// var_dump($lResultArr);
		$this->m_pageView = new pArticles_Ajax_Srv($lResultArr);
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}

?>