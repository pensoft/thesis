<?php

class Comment_Edit_Form_Wrapper extends eForm_Wrapper {
	var $m_commentId;
	var $m_uid;
	var $m_userFullname;
	var $m_documentId;
	function __construct($pData) {
		$this->m_commentId = (int)$pData['comment_id'];
		$this->m_documentId = (int)$pData['document_id'];
		$this->m_uid = $pData['uid'];
		$this->m_userFullname = $pData['user_fullname'];
		$pData['fields_metadata'] = $this->GetFieldsMetadata();
		parent::__construct($pData);

	}
	
	function GetErrorMsg($pSeparator = "\n"){
		$lFieldErrors = $this->m_formController->m_fieldErrors;
		$lGlobalErrors = $this->m_formController->m_globalErrors;
		$lErrorMsg = '';
		foreach ($lFieldErrors as $lFieldName => $lFieldErrors){
			$lFieldMetadata = $this->m_formController->GetFieldMetadata($lFieldName);
			$lFieldDisplayName = $lFieldMetadata['DisplayName'];
			foreach ($lFieldErrors as $lCurrentError){
				if($lErrorMsg){
					$lErrorMsg .= $pSeparator;
				}
				$lErrorMsg .= $lFieldDisplayName . ':' . $lCurrentError;
			}
		}
		foreach ($lGlobalErrors as $lCurrentError) {
			if($lErrorMsg){
				$lErrorMsg .= $pSeparator;
			}
			$lErrorMsg .= $lCurrentError;
		}
		return $lErrorMsg;
	}

	protected function GetFieldsMetadata() {
		return array(
			'comment_id' => array(
				'VType' => 'int',
				'CType' => 'hidden',
				'DisplayName' => '',				
			),
			'document_id' => array(
					'VType' => 'int',
					'CType' => 'hidden',
					'DisplayName' => '',
			),
			'msg' => array(
				'VType' => 'string',
				'CType' => 'textarea',
				'Checks' => array(),
				'AllowNulls' => true,
				// ~ CKMAXSTRLEN('{msg}', 4096),
				'AddTags' => array(
					'onfocus' => 'changeFocus(1, this)',
					'onblur' => 'changeFocus(2, this); submitCommentEdit(' . ( int ) $this->m_commentId . ');return false;',
					'fldattr' => '0',
					'class' => 'P-Comments-Revisions-Txt'
				),
				'DisplayName' => getstr('pwt.comments.msg')
			),
			'new' => array (
					'CType' => 'action',
					'DisplayName' => 'Comment',
					'SQL' => 'SELECT * FROM pjs.spCommentEdit(0, {comment_id}, ' . ( int ) $this->m_documentId . ', null,' . ( int ) $this->m_uid . ')',
					'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW
			),
			'save' => array(
				'CType' => 'action',
				'DisplayName' => 'Comment',
				'SQL' => 'SELECT * FROM pjs.spCommentEdit(1, {comment_id}, ' . ( int ) $this->m_documentId . ', {msg},' . ( int ) $this->m_uid . ')',
				'RedirUrl' => '',
				'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW
			)
		);
	}
	
	protected function PreActionProcessing(){
// 		var_dump($this->m_commentId);			
		$this->m_formController->SetFieldValue('comment_id', $this->m_commentId);
		$this->m_formController->SetFieldValue('document_id', $this->m_documentId);
	}
}

?>