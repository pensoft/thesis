<?php
/**
 *
 * Този клас ще реализира показването на 1 обект от документа.
 * Той ще реализира показването на структурата на документна, както и показването на коментарите/промените.
 *
 * За целта ще му се подава id-то на инстанса, който трябва да покажем. По него ще вземем id-то на документа.
 * Ако на инстанса не му е указано да се появява в лявото дърво - отваряме default-ния
 * instance(1-я instance на 1-во ниво в документа).
 *
 * Разчитаме темплейтите за дървото да са подадени като масив под ключе tree_templs в масива с параметрите
 * @author peterg
 *
 */
class cdisplay_document extends csimple {
	var $m_documentId;
	var $m_instanceId;
	var $m_con;
	var $m_documentTree;
	var $m_getFieldDataFromRequest;
	var $m_getObjectModeFromRequest;
	var $m_fieldValidationInfo;
	var $m_lock_res;
	var $m_template_xsl_dir_name;
	var $m_documentIsReadOnly;
	var $m_inPreviewMode;
	var $m_documentHasUnprocessedChanges;
	var $m_documentXml;
	var $m_displayUnconfirmedObjects;


	function __construct($pFieldTempl){
		parent::__construct($pFieldTempl);
		$this->m_con = new DBCn();
		$this->m_con->Open();
		$this->m_instanceId = $pFieldTempl['instance_id'];
		$this->m_inPreviewMode = $this->m_pubdata['preview_mode'];
		$this->m_displayUnconfirmedObjects = $this->m_pubdata['display_unconfirmed_objects'];

		if(!$this->m_instanceId && $this->m_documentId){
			$this->m_instanceId = getDocumentFirstInstanceId($this->m_documentId);
		}

		$this->getDocumentData();
		$this->initCommentsData();
		$this->initCommentForm();

		if((int)$this->m_inPreviewMode) {
			saveDocumentXML((int)$this->m_documentId);
		}
		
		if ($this->m_documentIsReadOnly && !(int)$this->m_inPreviewMode) {
			header("Location: /preview.php?document_id=$this->m_documentId");
			exit();
		}

		if(checkIfDocumentHasUnprocessedChanges($this->m_documentId, $this->m_documentHasUnprocessedChanges, $this->m_documentXml) && !(int)$this->m_inPreviewMode){
			header("Location: /preview.php?document_id=$this->m_documentId");
			exit();
		}

		$lDocument = new cdocument(
			array(
				'document_id' => (int)$this->m_documentId,
			)
		);

		$this->m_lock_res = $lDocument->lock($pFieldTempl['lock_operation_code']);

		if (!$this->m_lock_res && !(int)$this->m_pubdata['dont_redir_to_view'] ) {
			header("Location: /preview.php?document_id=$this->m_documentId");
			exit();
		}

		$this->m_documentTree = $this->getDocumentTree();
		$this->m_getFieldDataFromRequest = (int)$pFieldTempl['get_data_from_request'];
		$this->m_getObjectModeFromRequest = ( int ) $pFieldTempl['get_object_mode_from_request'];
		$this->m_fieldValidationInfo = $pFieldTempl['field_validation_info'];


	}

	/**
	 * @return the $m_instanceId
	 */
	public function getInstanceId() {
		return $this->m_instanceId;
	}

	/**
	 * @return the $m_documentId
	 */
	public function getDocumentId() {
		return $this->m_documentId;
	}

	/**
	 * @return the document_name
	 */
	public function getDocumentName() {
		return $this->m_pubdata['document_name'];
	}

	/**
	 * @return the document_is_locked
	 */
	public function getDocumentIsLock() {
		return $this->m_pubdata['document_is_locked'];
	}

	/**
	 * @return the document_lock_usr_id
	 */
	public function getDocumentLockUserId() {
		return $this->m_pubdata['document_lock_usr_id'];
	}

	/**
	 * @return the m_template_xsl_dir_name
	 */
	public function getDocumentXSLDirName() {
		return $this->m_template_xsl_dir_name;
	}

	/**
	 * Взимаме информация за документа на база на подаденото instance_id.
	 * При нужда го променяме на 1-я instance на документа.
	 */
	protected function getDocumentData(){
		$this->m_con->CloseRs();
		//echo 'SELECT * FROM spGetDocumentDataByInstance(' . $this->m_instanceId . ');';
		$this->m_con->Execute('SELECT * FROM spGetDocumentDataByInstance(' . $this->m_instanceId . ');');
		$this->m_con->MoveFirst();
		$this->m_documentId = (int)$this->m_con->mRs['document_id'];
		$this->m_template_xsl_dir_name = $this->m_con->mRs['xsl_dir_name'];
		$this->m_pubdata['document_id'] = $this->m_documentId;
		$this->m_pubdata['document_name'] = $this->m_con->mRs['document_name'];
		$this->m_pubdata['document_is_locked'] = (int)$this->m_con->mRs['is_locked'];
		$this->m_pubdata['document_lock_usr_id'] = (int)$this->m_con->mRs['lock_usr_id'];
		$this->m_documentIsReadOnly = (int)$this->m_con->mRs['document_is_readonly'];
		$this->m_documentHasUnprocessedChanges = (int)$this->m_con->mRs['document_has_unprocessed_changes'];
		$this->m_documentXml = $this->m_con->mRs['document_xml'];

		if(!$this->m_con->mRs['display_instance_in_tree']){
			(int)$this->m_con->mRs['root_instance_id'];
		}
		$this->m_pubdata['instance_id'] = $this->m_instanceId;

	}

	/**
	 * Връща визуализацията на дървото на документа.
	 */
	function getDocumentTree(){
//		var_dump($this->m_pubdata['tree_templs']);
		//~ echo 'SELECT * FROM spGetDocumentTree(' . $this->m_documentId . ', ' . $this->m_instanceId  . ');';
		global  $user;

		$lDocumentTree = new crsrecursive(array(
			'recursivecolumn'=>'parent_instance_id',
			'templadd'=>'has_children',

			'sqlstr' => 'SELECT * FROM spGetDocumentTreeFast(' . $this->m_documentId . ', ' . $this->m_instanceId  . ');',
			'templs' => $this->m_pubdata['tree_templs'],
			'current_instance_id' => $this->m_instanceId,
			'document_id' => $this->m_documentId,
			'validation_errors' => is_array($this->m_pubdata['xml_validation_field_instances']) ? $this->m_pubdata['xml_validation_field_instances'] : '',
			'is_locked' => $this->getDocumentIsLock(),
			'lock_usr_id' => $this->getDocumentLockUserId(),
			'preview_mode' => (int)$this->m_pubdata['preview_mode'],
			'xml_validation_flag' => (int)$this->m_pubdata['xml_validation_flag'],
			'enable_staff_features' => ENABLE_FEATURES
		));


		$lDocumentTree->GetData();
		$lDocumentTree->DontGetData(true);

		return $lDocumentTree;
	}

	/**
	 * Връща визуализацията на пътеката на документа.
	 */
	function getDocumentPath(){
//		var_dump($this->m_pubdata['tree_templs']);
//		echo 'SELECT * FROM spGetDocumentTree(' . $this->m_documentId . ', ' . $this->m_instanceId  . ');';
		$lPath = new crs(array(
			'sqlstr' => 'SELECT * FROM spGetDocumentPath(' . $this->m_instanceId  . ');',
			'templs' => $this->m_pubdata['path_templs'],
			'current_instance_id' => $this->m_instanceId,
			'document_id' => $this->m_documentId,
			'document_name' => $this->m_pubdata['document_name'],
		));
		$lPath->GetData();
		$lPath->DontGetData(true);
		return $lPath;
	}

	protected function displayInstance($pInstanceId) {
// 		file_put_contents('/var/www/pensoft/log_execute.txt', '');
// 		file_put_contents('/var/www/pensoft/log_stmnt.txt', '');
// 		trigger_error('START DISPLAY ' . USE_PREPARED_STATEMENTS . ' ' .  date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6), E_USER_NOTICE);
		$lInstance = new cdocument_instance(array(
			'templs' => $this->m_pubdata['instance_templs'],
			'container_templs' => $this->m_pubdata['container_templs'],
			'field_templs' => $this->m_pubdata['field_templs'],
			'custom_html_templs' => $this->m_pubdata['custom_html_templs'],
			'action_templs' => $this->m_pubdata['action_templs'],
			'tabbed_element_templs' => $this->m_pubdata['tabbed_element_templs'],

			'document_id' => $this->m_documentId,
			'instance_id' => $pInstanceId,
			'level' => 1,
			'root_instance_id' => $this->m_instanceId,
			'get_data_from_request' => $this->m_getFieldDataFromRequest,
			'get_object_mode_from_request' => $this->m_getObjectModeFromRequest,
			'field_validation_info' => $this->m_fieldValidationInfo,
			'display_unconfirmed_objects' => $this->m_displayUnconfirmedObjects,
			'create_new_preview_generator' => true,
			'use_preview_generator' => true,
			'return_preview_generator_display' => true,
		));
		$lResult = $lInstance->Display();
// 		trigger_error('END DISPLAY ' . USE_PREPARED_STATEMENTS . ' ' . date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6), E_USER_NOTICE);
		return $lResult;
	}

	function initCommentForm() {
		$lCommentForm = new ccomments(array(
			'showtype' => 1,
			'instance_id' => $this->m_instanceId,
			'document_id' => $this->m_documentId,
			'formaction' =>  $_SERVER['REQUEST_URI'],
			'returl' => $_SERVER['REQUEST_URI'],
			'templs' => array(
				G_DEFAULT => 'comments.commentform',
			),
		));
		$lCommentForm->GetData();
		$this->m_pubdata[$this->m_pubdata['comments_form_templ']] = $lCommentForm->Display();
	}

	function initCommentsData() {
		$lComments = new ccomments(array(
			'showtype' => 0,
			'instance_id' => $this->m_instanceId,
			'document_id' => $this->m_documentId,
			'comments_in_preview_mode' => $this->m_pubdata['comments_in_preview_mode'],
			'templs' => array(
				G_DEFAULT => 'comments.wrapper',
			),
		));
		$lComments->GetData();
		$this->m_pubdata[$this->m_pubdata['comments_templ']] = $lComments->Display();
	}

	function  Display(){
		$this->m_pubdata['document_structure'] = $this->m_documentTree->Display();
		$this->m_pubdata['document_object_instance'] = $this->displayInstance($this->m_instanceId);

		return parent::Display();
	}
}
?>