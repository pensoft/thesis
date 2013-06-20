<?php

class Comment_Reply_Form_Wrapper extends eForm_Wrapper {
	var $m_rootId;
	var $m_uid;
	var $m_userFullname;
	function __construct($pData) {
		$this->m_rootId = (int)$pData['rootid'];
		$this->m_uid = $pData['uid'];
		$this->m_userFullname = $pData['user_fullname'];
		$pData['fields_metadata'] = $this->GetFieldsMetadata();
		parent::__construct($pData);

	}

	protected function GetFieldsMetadata() {
		return array(
			'rootid' => array(
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
			'msg' => array(
				'VType' => 'string',
				'CType' => 'textarea',
				'Checks' => array(),
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
				'SQL' => 'SELECT * FROM pjs.spNewCommentReply({rootid}, {msg}, \'' . $_SERVER['REMOTE_ADDR'] . '\',' . (int) $this->m_uid . ', \'' . q($this->m_userFullname) . '\')',
				'RedirUrl' => '',
				'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW
			)
		);
	}
}

?>