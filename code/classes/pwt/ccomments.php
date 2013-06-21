<?php
/**
 *
 * Class for comments
 *
 */
class ccomments extends csimple {
	var $m_documentId;
	var $form;
	var $comments;
	var $m_con;
	var $m_rootmsgid;
	var $m_inPreviewMode;
	var $m_useAsAjaxSrv;
	function __construct($pFieldTempl) {
		$this->m_con = new DBCn();
		$this->m_con->Open();
		parent::__construct($pFieldTempl);
		$this->m_documentId = (int)$this->m_pubdata['document_id'];
		$this->m_pubdata['commentform'] = '';
		$this->m_pubdata['comments'] = '';
		$this->m_inPreviewMode = $this->m_pubdata['comments_in_preview_mode'];
		$this->m_useAsAjaxSrv = (int)$this->m_pubdata['use_as_ajax_srv'];
		if($this->m_inPreviewMode){
			$this->m_pubdata['showtype'] = 3;
		}
		switch ((int)$this->m_pubdata['showtype']) {
			case 0:
				$this->GetCommentsByInstanceId();
				break;
			case 1:
				$this->NewCommentByInstanceId();
				break;
			case 2:
				$this->AnswerToComment();
				break;
			case 3:
				$this->GetCommentsByDocumentId();
				break;
		}
	}

	//Get comments by root_object_instance_id

	function GetCommentsByInstanceId() {
		$lCon = new DBCn();
		$lCon->Open();
		$lSql = '
			SELECT spGetDocumentLatestCommentRevisionId(i.document_id, 0) as version_id
			FROM pwt.document_object_instances i
			WHERE i.id = '. (int)$this->m_pubdata['instance_id'];
		$lCon->Execute($lSql);
		$lVersionId = (int)$lCon->mRs['version_id'];

		$lSql = 'SELECT m2.id id,
						m2.document_id document_id,
						m2.root_object_instance_id instance_id,
						m2.author author,
						m2.msg msg,
						m2.rootid rootid,
						m2.subject subject,
						m2.usr_id usr_id,
						m2.lastmoddate lastmoddate,
						u.photo_id photo_id,
						u.first_name || \' \' || u.last_name as fullname,
						m2.mdate mdate,
						coalesce(m2.start_object_instances_id, 0) as start_instance_id,
						coalesce(m2.end_object_instances_id, 0) as end_instance_id,
						coalesce(m2.start_object_field_id, 0) as start_field_id,
						coalesce(m2.end_object_field_id, 0) as end_field_id,
						coalesce(m2.start_offset, 0) as start_offset,
						coalesce(m2.end_offset, 0) as end_offset,
						m2.is_resolved::int as is_resolved,
						m2.resolve_uid,
						coalesce(u2.first_name, \'\') || \' \' || coalesce(u2.last_name, \'\') as resolve_fullname,
						m2.resolve_date,
						m2.is_disclosed::int as is_disclosed,
						m2.undisclosed_usr_id,
						uu.name as undisclosed_user_fullname
				FROM (SELECT * FROM pwt.spGetVersionRoleFilteredMsgRootIds(' . $lVersionId . ')) m1
				JOIN pwt.msg m2 ON (m1.id = m2.rootid)
				JOIN usr u ON m2.usr_id = u.id
				LEFT JOIN usr u2 ON m2.resolve_uid = u2.id
				LEFT JOIN undisclosed_users uu ON uu.id = m2.undisclosed_usr_id
				JOIN usr_titles ut ON ut.id = u.usr_title_id
				JOIN pwt.document_object_instances i ON i.id = m2.root_object_instance_id AND m2.revision_id = spGetDocumentLatestCommentRevisionId(i.document_id, 0)
				JOIN pwt.document_object_instances p ON p.document_id = i.document_id AND p.pos = substring(i.pos, 1, char_length(p.pos))
				WHERE p.id = ' .  (int)$this->m_pubdata['instance_id']. '
				ORDER BY m2.rootid, m2.ord, m2.mdate';
// 		var_dump($lSql);
		$this->comments = new crsgroup(
			array(
				'ctype' => 'crsgroup',
				'sqlstr' => $lSql,
				'splitcol' => 'rootid',
				'hideroot' => 1,
				'in_preview_mode' => (int)$this->m_inPreviewMode,
				'templs' => array(
					G_HEADER => 'comments.browseHead',
					G_STARTRS => 'comments.browseStart',
					G_SPLITHEADER => 'comments.browseSplitHead',
					G_ROWTEMPL => 'comments.browseRow',
					G_SPLITFOOTER => 'comments.browseSplitFoot',
					G_ENDRS => 'comments.browseEnd',
					G_FOOTER => 'comments.browseFoot',
					G_NODATA => 'comments.browseNoData',
				),
			)
		);
		$this->comments->GetData();
		$this->m_pubdata['comments'] = $this->comments->Display();
	}

	//Get comments by document_id (all comments for this document. Used in preview mode)

	function GetCommentsByDocumentId() {
		$lCon = new DBCn();
		$lCon->Open();
		$lSql = '
		SELECT spGetDocumentLatestCommentRevisionId(' . (int)$this->m_pubdata['document_id'] . ', 0) as version_id';
		$lCon->Execute($lSql);
		$lVersionId = (int)$lCon->mRs['version_id'];


		$lSql = 'SELECT m2.id id,
						m2.document_id document_id,
						m2.root_object_instance_id instance_id,
						m2.author author,
						m2.msg msg,
						m2.rootid rootid,
						m2.subject subject,
						m2.usr_id usr_id,
						m2.lastmoddate lastmoddate,
						u.photo_id photo_id,
						u.first_name || \' \' || u.last_name as fullname,
						m2.mdate mdate,
						coalesce(m2.start_object_instances_id, 0) as start_instance_id,
						coalesce(m2.end_object_instances_id, 0) as end_instance_id,
						coalesce(m2.start_object_field_id, 0) as start_field_id,
						coalesce(m2.end_object_field_id, 0) as end_field_id,
						coalesce(m2.start_offset, 0) as start_offset,
						coalesce(m2.end_offset, 0) as end_offset,
						m2.is_resolved::int as is_resolved,
						m2.resolve_uid,
						coalesce(u2.first_name, \'\') || \' \' || coalesce(u2.last_name, \'\') as resolve_fullname,
						m2.resolve_date,
						m2.is_disclosed::int as is_disclosed,
						m2.undisclosed_usr_id,
						uu.name as undisclosed_user_fullname
				FROM (SELECT * FROM pwt.spGetVersionRoleFilteredMsgRootIds(' . $lVersionId . ')) m1
				JOIN pwt.msg m2 ON (m1.id = m2.rootid) AND m1.revision_id = spGetDocumentLatestCommentRevisionId(' .  (int)$this->m_pubdata['document_id']. ', 0)
				JOIN usr u ON m2.usr_id = u.id
				LEFT JOIN usr u2 ON m2.resolve_uid = u2.id
				LEFT JOIN undisclosed_users uu ON uu.id = m2.undisclosed_usr_id
				JOIN usr_titles ut ON ut.id = u.usr_title_id
				WHERE m2.document_id =' .  (int)$this->m_pubdata['document_id']. '
				ORDER BY m2.rootid, m2.ord, m2.mdate';
// 		var_dump($lSql);
		$this->comments = new crsgroup(
			array(
				'ctype' => 'crsgroup',
				'sqlstr' => $lSql,
				'splitcol' => 'rootid',
				'hideroot' => 1,
				'in_preview_mode' => (int)$this->m_inPreviewMode,
				'templs' => array(
					G_HEADER => 'comments.browseHead',
					G_STARTRS => 'comments.browseStart',
					G_SPLITHEADER => 'comments.browseSplitHead',
					G_ROWTEMPL => 'comments.browseRow',
					G_SPLITFOOTER => 'comments.browseSplitFoot',
					G_ENDRS => 'comments.browseEnd',
					G_FOOTER => 'comments.browseFoot',
					G_NODATA => 'comments.browseNoData',
				),
			)
		);
		$this->comments->GetData();
		$this->m_pubdata['comments'] = $this->comments->Display();
	}

	//Create new comment by root_object_instance_id
	function NewCommentByInstanceId() {
		global $user;
		$this->form = new ctplkfor(
			array(
				'ctype' => 'ctplkfor',
				'method' => 'POST',
				'setformname' => 'commentpost',
				'flds' => array(
					'comment_id' => array(
						'VType' => 'int',
						'CType' => 'hidden',
						'AllowNulls' => true,
					),

					'instanceid' => array(
						'VType' => 'int',
						'CType' => 'hidden',
						'DisplayName' => '',
						'DefValue' => ((int)$this->m_pubdata['instance_id'] ? (int)$this->m_pubdata['instance_id'] : ''),
						'AllowNulls' => true,
					),

					'start_instance_id' => array(
						'VType' => 'int',
						'CType' => 'hidden',
						'DisplayName' => '',
						'AllowNulls' => true,
						'AddTags' => array(
							'id' => 'comments_start_instance_id',
						),
					),

					'start_field_id' => array(
						'VType' => 'int',
						'CType' => 'hidden',
						'DisplayName' => '',
						'AllowNulls' => true,
						'AddTags' => array(
							'id' => 'comments_start_field_id',
						),
					),

					'start_offset' => array(
						'VType' => 'int',
						'CType' => 'hidden',
						'DisplayName' => '',
						'AllowNulls' => true,
						'AddTags' => array(
							'id' => 'comments_start_offset',
						),
					),

					'end_instance_id' => array(
						'VType' => 'int',
						'CType' => 'hidden',
						'DisplayName' => '',
						'AllowNulls' => true,
						'AddTags' => array(
							'id' => 'comments_end_instance_id',
						),
					),

					'end_field_id' => array(
						'VType' => 'int',
						'CType' => 'hidden',
						'DisplayName' => '',
						'AllowNulls' => true,
						'AddTags' => array(
							'id' => 'comments_end_field_id',
						),
					),

					'end_offset' => array(
						'VType' => 'int',
						'CType' => 'hidden',
						'DisplayName' => '',
						'AllowNulls' => true,
						'AddTags' => array(
							'id' => 'comments_end_offset',
						),
					),

					'documentid' => array(
						'VType' => 'int',
						'CType' => 'hidden',
						'DisplayName' => '',
						'DefValue' => ((int) $this->m_documentId ? (int) $this->m_documentId : ''),
						'AllowNulls' => true,
					),

					'msg' => array(
						'VType' => 'string',
						'CType' => 'textarea',
						'Checks' => array(
							//~ CKMAXSTRLEN('{msg}', 4096),
						),
						'AddTags' => array(
							'onfocus' => 'changeFocus(1, this)',
							'onblur'  => 'changeFocus(2, this)',
							'fldattr'  => '0',
							'class' => 'P-Comments-Revisions-Txt',
						),
						'DisplayName' => getstr('pwt.comments.msg'),
					),
					'save' => array(
						'CType' => 'action',
						'DisplayName' => 'Comment',
						'SQL' => 'SELECT * FROM pwt.spCommentAdd(null, {instanceid}, ' . (int)$this->m_documentId . ', \'' . q(getUserName()) . '\',\'\', {msg}, \'' . $_SERVER['REMOTE_ADDR'] . '\',' . (int) $user->id . ',
							{start_instance_id}, {end_instance_id}, {start_field_id}, {end_field_id}, {start_offset}, {end_offset}) as comment_id',
						'RedirUrl' => $this->m_pubdata['returl'],
						'AddTags' => array(
							'onclick' => 'fillCommentPos();submitPreviewNewComment();return false;',
							'class' => 'P-Grey-Btn-Middle',
						),
						'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
					),
				),
				'templs' => array(
					G_DEFAULT => 'comments.form',
				),
			), 0
		);

		if($this->m_useAsAjaxSrv){
			$this->form->StopErrDisplay(true);
			$this->form->setProp('save', 'ActionMask', ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW);
		}
		if ($this->m_pubdata['formaction'])
			$this->form->SetFormAction($this->m_pubdata['formaction']);

// 		var_dump($_REQUEST);

		$this->form->GetData();
// 		exit;
		if($this->form->KforErrCnt()) {
			$this->form->setProp('save', 'ActionMask', ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW);
		}

		$this->m_pubdata['commentform'] = $this->form->Display();
	}

	//Answer to comment
	function AnswerToComment() {
		global $user;

		$this->form = new ctplkfor(
			array(
				'ctype' => 'ctplkfor',
				'method' => 'POST',
				'setformname' => 'commentpost_' . (int)$this->m_pubdata['rootmsgid'],
				'flds' => array(
					'commentid' => array(
						'VType' => 'int',
						'CType' => 'hidden',
					),

					'instanceid' => array(
						'VType' => 'int',
						'CType' => 'hidden',
						'DisplayName' => '',
						'DefValue' => ((int)$this->m_pubdata['instance_id'] ? (int)$this->m_pubdata['instance_id'] : ''),
						'AllowNulls' => true,
					),

					'rootmsgid' => array(
						'VType' => 'int',
						'CType' => 'hidden',
						'DisplayName' => '',
						'DefValue' => ((int)$this->m_pubdata['rootmsgid'] ? (int)$this->m_pubdata['rootmsgid'] : '0'),
						'AllowNulls' => true,
					),

					'documentid' => array(
						'VType' => 'int',
						'CType' => 'hidden',
						'DisplayName' => '',
						'DefValue' => ((int)$this->m_documentId ? (int)$this->m_documentId : ''),
						'AllowNulls' => true,
					),

					'msg' => array(
						'VType' => 'string',
						'CType' => 'textarea',
						'Checks' => array(
							//~ CKMAXSTRLEN('{msg}', 4096),
						),
						'AddTags' => array(
							'onfocus' => 'changeFocus(1, this)',
							'onblur'  => 'changeFocus(2, this)',
							'fldattr'  => '0',
							'class' => 'P-Comments-Revisions-Txt',
						),
						'DisplayName' => getstr('pwt.comments.msg'),
					),
					'edit' => array(
						'CType' => 'action',
						'DisplayName' => '',
						'SQL' => 'SELECT * FROM pwt.spGetDocumentMetaData({documentid}, ' . (int)$user->id . ')',
						'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_SHOW | ACTION_FETCH | ACTION_EXEC,
					),
					'save' => array(
						'CType' => 'action',
						'DisplayName' => 'Comment',
						'SQL' => 'SELECT * FROM pwt.spCommentAdd({rootmsgid}, {instanceid}, ' . (int)$this->m_documentId . ', \'' . q(getUserName()) . '\',\'\', {msg}, \'' . $_SERVER['REMOTE_ADDR'] . '\',' . (int) $user->id . ',
							null, null, null, null, null, null) as commentid',
						'RedirUrl' => $this->m_pubdata['returl'],
						'AddTags' => array(
							'class' => 'P-Grey-Btn-Middle',
						),
						'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
					),
				),
				'templs' => array(
					G_DEFAULT => 'comments.answerform',
				),
			), 0
		);

		if ($this->m_pubdata['formaction'])
			$this->form->SetFormAction($this->m_pubdata['formaction']);

		$this->form->GetData();
		$this->m_pubdata['commentanswerform'] = $this->form->Display();
		return $this->m_pubdata['commentanswerform'];
	}

	function  Display(){
		return parent::Display();
	}
}
?>