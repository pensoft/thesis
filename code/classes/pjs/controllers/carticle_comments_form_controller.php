<?php

class cArticle_Comments_Form_Controller extends cBase_Controller {
	var $m_action_result;
	var $m_articlesModel;
	var $m_tempPageView;
	var $m_articleId;
	var $m_errCnt = 0;
	var $m_errMsg = '';
	var $m_success = 0;
	var $m_successMsg = '';

	function __construct() {
		global $rewrite;
		parent::__construct();
		$pViewPageObjectsDataArray = array ();
		$this->m_action_result = array ();
		$this->m_articlesModel = new mArticles();
		$this->m_tempPageView = new pArticles_Ajax_Srv(array());
		$this->m_articleId = (int) $this->GetValueFromRequestWithoutChecks('article_id');
		
		if($this->GetUserId()) {
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
					'AllowNulls' => false,
					'AddTags' => array(
						'cols' => '',
						'rows' => '',
						'class' => 'article_comment_textarea',
						//'id' => 'article_comment_textarea',
					),
					'RichText' => 1,
				),
				'comment' => array(
					'CType' => 'action',
					'SQL' => 'SELECT * FROM pjs."spProcessArticleComment"(1, null, {user_id}, {article_id}, {journal_id}, {message})',
					'DisplayName' => '',
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
				// 'showedit' => array(
					// 'CType' => 'action',
					// 'SQL' => '/*{user_id}{article_id}{journal_id}{message}*/',
					// 'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
				// )
			);
			
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
			$lForm = new evSimple_Block_Display(array(
				'name_in_viewobject' => 'forum_no_logged_user',
				'view_object' => $this->m_tempPageView,
				'article_id' => $this->m_articleId,
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
					if(!$lForm->GetFieldValue('message') && $lForm->GetCurrentAction() == 'comment') {
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