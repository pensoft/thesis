<?php

class New_Comment_Form_Wrapper extends eForm_Wrapper {
	var $m_versionId;
	var $m_uid;
	var $m_userFullname;
	function __construct($pData) {
		$this->m_versionId = (int)$pData['version_id'];
		$this->m_uid = $pData['uid'];
		$this->m_userFullname = $pData['user_fullname'];
		$pData['fields_metadata'] = $this->GetFieldsMetadata();
		parent::__construct($pData);

	}

	protected function GetFieldsMetadata() {
		return array(
			'version_id' => array(
				'VType' => 'int',
				'CType' => 'hidden',
				'DisplayName' => '',
				'DefValue' => $this->m_rootId,
				'AllowNulls' => false
			),
			'comment_id' => array(
				'VType' => 'int',
				'CType' => 'hidden',
				'DisplayName' => '',
			),
			'start_instance_id' => array(
				'VType' => 'int',
				'CType' => 'hidden',
				'DisplayName' => '',
				'AddTags' => array(
					'id' => 'previewNewCommentStartInstanceId',
				),
				'AllowNulls' => true,
			),
			'start_field_id' => array(
				'VType' => 'int',
				'CType' => 'hidden',
				'DisplayName' => '',
				'AddTags' => array(
					'id' => 'previewNewCommentStartFieldId',
				),
				'AllowNulls' => true,
			),
			'start_offset' => array(
				'VType' => 'int',
				'CType' => 'hidden',
				'DisplayName' => '',
				'AddTags' => array(
					'id' => 'previewNewCommentStartOffset',
				),
				'AllowNulls' => true,
			),
			'end_instance_id' => array(
				'VType' => 'int',
				'CType' => 'hidden',
				'DisplayName' => '',
				'AddTags' => array(
					'id' => 'previewNewCommentEndInstanceId',
				),
				'AllowNulls' => true,
			),
			'end_field_id' => array(
				'VType' => 'int',
				'CType' => 'hidden',
				'DisplayName' => '',
				'AddTags' => array(
					'id' => 'previewNewCommentEndFieldId',
				),
				'AllowNulls' => true,
			),
			'end_offset' => array(
				'VType' => 'int',
				'CType' => 'hidden',
				'DisplayName' => '',
				'AddTags' => array(
					'id' => 'previewNewCommentEndOffset',
				),
				'AllowNulls' => true,
			),

			'msg' => array(
				'VType' => 'string',
				'CType' => 'textarea',
				'Checks' => array(),
				'AllowNulls' => true,
				// ~ CKMAXSTRLEN('{msg}', 4096),
				'AddTags' => array(
					'onfocus' => 'changeFocus(1, this)',
					'onblur' => 'changeFocus(2, this)',
					'fldattr' => '0',
					'class' => 'P-Comments-Revisions-Txt'
				),
				'DisplayName' => getstr('pwt.comments.msg')
			),
			'save' => array(
				'CType' => 'action',
				'DisplayName' => 'Comment',
				'SQL' => 'SELECT * FROM pjs.spNewComment({version_id}, {msg}, {start_instance_id}, {start_field_id}, {start_offset}, {end_instance_id}, {end_field_id}, {end_offset},
					\'' . $_SERVER['REMOTE_ADDR'] . '\',
					' . (int) $this->m_uid . ', \'' . q($this->m_userFullname) . '\')',
				'RedirUrl' => '',
				'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW
			)
		);
	}
}

?>