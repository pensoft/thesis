<?php
require_once PATH_CLASSES . 'diff.php';
require_once PATH_CLASSES . 'comments.php';
class mVersions extends emBase_Model {
	var $m_commentsModel;
	function __construct(){
		parent::__construct();
		$this->m_commentsModel = new mComments();
	}
	/**
	 * Returns a list of all the comments for the specific version
	 * @param unknown_type $pVersionId
	 */
	function GetVersionComments($pVersionId, $pFilterVersionRoleVisibility = false){
		$lResult = array();
		$lRootCommentSelectName = 'pjs.msg';
		if($pFilterVersionRoleVisibility){
			$lRootCommentSelectName = ' (SELECT * FROM pjs.spGetVersionRoleFilteredMsgRootIds(' . $pVersionId . ')) ';
		}

		$lSql = '
			SELECT m2.id as id, m2.rootid,
					m2.document_id as document_id,
					m2.author as author,
					m2.msg as msg,
					m2.subject as subject,
					m2.usr_id as usr_id,
					m2.lastmoddate as lastmoddate,
					m2.flags as flags,
					m2.ord as ord,
					u.photo_id as photo_id,
					u.first_name || \' \' || u.last_name as fullname,
					m2.mdate as mdate,
					coalesce(m2.start_object_instances_id, 0) as start_instance_id,
					coalesce(m2.end_object_instances_id, 0) as end_instance_id,
					coalesce(m2.start_object_field_id, 0) as start_field_id,
					coalesce(m2.end_object_field_id, 0) as end_field_id,
					coalesce(m2.start_offset, 0) as start_offset,
					coalesce(m2.end_offset, 0) as end_offset,
					EXTRACT(EPOCH FROM m2.mdate) as mdate_in_seconds,
					EXTRACT(EPOCH FROM m2.lastmoddate) as lastmoddate_in_seconds,
					m2.original_id,
					m2.is_resolved::int as is_resolved,
					m2.resolve_uid,
					coalesce(u2.first_name, \'\') || \' \' || coalesce(u2.last_name, \'\') as resolve_fullname,
					m2.resolve_date,
					m2.is_disclosed::int as is_disclosed,
					m2.undisclosed_usr_id,
					uu.name as undisclosed_user_fullname
			FROM ' . $lRootCommentSelectName . ' m1
			JOIN pjs.msg m2 ON (m1.id = m2.rootid)
			JOIN usr u ON m2.usr_id = u.id
			LEFT JOIN usr u2 ON m2.resolve_uid = u2.id
			LEFT JOIN undisclosed_users uu ON uu.id = m2.undisclosed_usr_id
			LEFT JOIN usr_titles ut ON ut.id = u.usr_title_id
			WHERE m2.version_id =' .  $pVersionId. '
			ORDER BY m2.rootid, m2.ord, m2.mdate
		';
// 		var_dump($lSql);
		$this->m_con->Execute($lSql);
		while(!$this->m_con->Eof()){
			$lResult[] = $this->m_con->mRs;
			$this->m_con->MoveNext();
		}
// 		var_dump($lSql);
		return $lResult;
	}
	
	function CheckIfVersionIsReadonly($pVersionId){
		$this->m_con->Execute('SELECT pjs.spCheckIfPjsVersionIsReadonly(' . (int)$pVersionId . ') as is_readonly');		
		return $this->m_con->mRs['is_readonly'];
	}

	function GetVersionFieldValueFromXml($pVersionId, $pInstanceId, $pFieldId){
		$lSql = '
			SELECT
				id, xml
			FROM pjs.pwt_document_versions
			WHERE version_id = ' . $pVersionId;
		$this->m_con->Execute($lSql);
		if(!(int)$this->m_con->mRs['id']){
			return;
		}
		$lDom = new DOMDocument('1.0', 'utf-8');
		if(!$lDom->loadXML($this->m_con->mRs['xml'])){
			return;
		}
		$lXPath = new DOMXPath($lDom);
		$lFieldQuery = '//*[@instance_id="' . $pInstanceId . '"]/fields/*[@id="' . $pFieldId . '"]/value';
		$lFieldNodes = $lXPath->query($lFieldQuery);
		if(!$lFieldNodes->length){
			return ;
		}
		$lFieldNode = $lFieldNodes->item(0);
		$lResult = '';
		foreach ($lFieldNode->childNodes as $lChild){
			$lResult .= $lDom->saveXML($lChild, LIBXML_NOEMPTYTAG);
		}
// 		var_dump($lResult);
		return $lResult;
	}

	function GetVersionFieldPreviousValue($pVersionId, $pInstanceId, $pFieldId){
		$lCon = $this->m_con;
		//First check if there is an unprocessed change in this field, which is not of type rej/acc all
		$lSql = '
			SELECT vc.*
			FROM pjs.pwt_document_version_changes vc
			JOIN pjs.pwt_document_versions dv ON dv.id = cv.pwt_document_version_id
			WHERE dv.version_id = ' . (int)$pVersionId . ' AND vc.instance_id = ' . $pInstanceId . '
				AND vc.field_id = ' . $pFieldId . '
		';
		$lCon->Execute($lSql);
		if((int)$lCon->mRs['id']){
			return $lCon->mRs['value'];
		}
		//If there is no such unprocessed change then the previous value is the one in the xml
		return $this->GetVersionFieldValueFromXml($pVersionId, $pInstanceId, $pFieldId);
	}


	/**
	 * Returns the document type of the document containing the specifiv version
	 *
	 * @param $pVersionId unknown_type
	 */
	function GetVersionDocumentSrcType($pVersionId) {
// 		$lSql = 'SELECT document_source_id
// 			FROM pjs.document_versions v
// 			JOIN pjs.documents d ON d.id = v.document_id
// 			WHERE v.id = ' . (int) $pVersionId . '
// 		';
// 		$this->m_con->Execute($lSql);
// 		return (int) $this->m_con->mRs['document_source_id'];
		$lDocumentInfo = $this->GetVersionDocumentInfo($pVersionId);
		return $lDocumentInfo['document_source_id'];
	}

	function GetDocumentVersionInfo($pVersionId) {
		$lResult = array();

		$lSql = '
		SELECT
			dv.uid,
			dv.version_num,
			dv.version_type_id,
			d.document_source_id,
			d.id as document_id,
			d.submitting_author_id,
			d.document_review_type_id,
			coalesce(drru.round_id, 0) as version_round_id,
			du.role_id
		FROM pjs.document_versions dv
		JOIN pjs.documents d ON d.id = dv.document_id
		LEFT JOIN pjs.document_review_round_users drru ON drru.document_version_id = dv.id
		LEFT JOIN pjs.document_users du ON du.id = drru.document_user_id
		WHERE dv.id = ' . (int)$pVersionId;

		$this->m_con->Execute($lSql);
		$lResult['document_source_id'] = (int)$this->m_con->mRs['document_source_id'];
		$lResult['document_id'] = (int)$this->m_con->mRs['document_id'];
		$lResult['submitting_author_id'] = (int)$this->m_con->mRs['submitting_author_id'];
		$lResult['document_review_type_id'] = (int)$this->m_con->mRs['document_review_type_id'];
		$lResult['uid'] = (int)$this->m_con->mRs['uid'];
		$lResult['version_num'] = (int)$this->m_con->mRs['version_num'];
		$lResult['version_type_id'] = (int)$this->m_con->mRs['version_type_id'];
		$lResult['role_id'] = (int)$this->m_con->mRs['role_id'];
		$lResult['version_round_id'] = (int)$this->m_con->mRs['version_round_id'];

		return $lResult;
	}

	/**
	 * Returns information about the document which the specified version belongs to
	 * @param unknown_type $pVersionId
	 */
	function GetVersionDocumentInfo($pVersionId){
		$lSql = 'SELECT d.*
		FROM pjs.document_versions v
		JOIN pjs.documents d ON d.id = v.document_id
		WHERE v.id = ' . (int) $pVersionId . '
		';
		$this->m_con->Execute($lSql);
		$lResult = array();
		$lResult['document_source_id'] = (int)$this->m_con->mRs['document_source_id'];
		$lResult['document_id'] = (int)$this->m_con->mRs['id'];
		$lResult['submitting_author_id'] = (int)$this->m_con->mRs['submitting_author_id'];
		$lResult['document_review_type_id'] = (int)$this->m_con->mRs['document_review_type_id'];

		return $lResult;
	}

	function GetVersionRoundId($pVersionId){
		$lSql = 'SELECT ru.round_id
		FROM pjs.document_review_round_users ru
		WHERE ru.document_version_id = ' . (int) $pVersionId . '
		LIMIT 1
		';
		$this->m_con->Execute($lSql);
		return (int) $this->m_con->mRs['round_id'];
	}

	function GetRoundUserIdRoundId($pRoundUserId){
		$lSql = 'SELECT ru.round_id
		FROM pjs.document_review_round_users ru
		WHERE ru.id = ' . (int) $pRoundUserId . '
		LIMIT 1
		';
		$this->m_con->Execute($lSql);
		return (int) $this->m_con->mRs['round_id'];
	}

	function GetVersionDocumentPwtId($pVersionId) {
		$lSql = 'SELECT d.pwt_id
		FROM pjs.document_versions v
		JOIN pjs.pwt_documents d ON d.document_id = v.document_id
		WHERE v.id = ' . (int) $pVersionId . '
		';
		$this->m_con->Execute($lSql);
		return (int) $this->m_con->mRs['pwt_id'];
	}

	function GetRoundUserIdVersionId($pRoundUserId){
		$lSql = 'SELECT ru.document_version_id
		FROM pjs.document_review_round_users ru
		WHERE ru.id = ' . (int) $pRoundUserId . '
		LIMIT 1
		';
		$this->m_con->Execute($lSql);
		return (int) $this->m_con->mRs['document_version_id'];
	}

	protected function ExecuteSingleSql($pSql) {
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		try{
			$lCon = $this->m_con;
			if(! $lCon->Execute($pSql)){
				throw new Exception($lCon->GetLastError());
			}
			$lResult['success_msg'] = getstr('pjs.actionSuccessfullyPerformed');
		}catch(Exception $pException){
			$lResult['err_cnt'] ++;
			$lResult['err_msgs'][] = array(
				'err_msg' => $pException->getMessage()
			);
		}
		return $lResult;
	}

	function SavePwtVersionChange($pVersionId, $pFieldId, $pInstanceId, $pContent, $pUid) {
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);

		$lPreviousFieldValue = $this->GetVersionFieldPreviousValue($pVersionId, $pInstanceId, $pFieldId);
// 		var_dump($lPreviousFieldValue);
		$lCommentsModel = $this->m_commentsModel;
		$lModifiedComments = $lCommentsModel->RecalculateFieldCommentPositions($pVersionId, $pInstanceId, $pFieldId, $pContent, $lPreviousFieldValue, false);
		var_dump($lModifiedComments);
		try{
			$lSql = 'BEGIN;';
			if(!$this->m_con->Execute($lSql)){
				throw new Exception($this->m_con->GetLastError());
			}
			$lCommentsModel->SaveCommentRecalculatedPositions($lModifiedComments, $this->m_con);
			$pContent = RemoveFieldCommentNodes($pContent);

			$lSql = 'SELECT * FROM spSavePwtVersionChange(' . (int) $pVersionId . ', ' . (int) $pFieldId . ', ' . (int) $pInstanceId . ', \'' . q($pContent) . '\',' . (int) $pUid . ');';
			if(!$this->m_con->Execute($lSql)){
				throw new Exception($this->m_con->GetLastError());
			}

			$lSql = 'COMMIT;';
			if(!$this->m_con->Execute($lSql)){
				throw new Exception($this->m_con->GetLastError());
			}

			$lResult['success_msg'] = getstr('pjs.actionSuccessfullyPerformed');
		}catch(Exception $pException){
			$this->m_con->Execute('ROLLBACK;');
			$lResult['err_cnt'] ++;
			$lResult['err_msgs'][] = array(
				'err_msg' => $pException->getMessage()
			);
		}
// 		var_dump($lSql);
		return $lResult;
	}

	function PwtVersionAcceptAllChanges($pVersionId, $pUid) {
		$lSql = 'SELECT * FROM spPwtVersionAcceptAllChanges(' . (int) $pVersionId . ',' . (int) $pUid . ');';
		return $this->ExecuteSingleSql($lSql);
	}

	function PwtVersionRejectAllChanges($pVersionId, $pUid) {
		$lSql = 'SELECT * FROM spPwtVersionRejectAllChanges(' . (int) $pVersionId . ',' . (int) $pUid . ');';
		return $this->ExecuteSingleSql($lSql);
	}

	/**
	 * Returns the path to the xsl directory of the pmt document for the
	 * specified version
	 *
	 * @param $pVersionId unknown_type
	 * @return number
	 */
	function GetVersionPmtDocumentXslPath($pVersionId) {
		$lSql = 'SELECT xsl_dir_name
		FROM pjs.document_versions v
		JOIN pjs.pwt_documents d ON d.document_id = v.document_id
		JOIN pwt.documents pd ON pd.id = d.pwt_id
		JOIN pwt.templates t ON t.id = pd.template_id
		WHERE v.id = ' . (int) $pVersionId . '
		';
		$this->m_con->Execute($lSql);
		return $this->m_con->mRs['xsl_dir_name'];
	}

	/**
	 * Returns the list of user who have changes in the specified version
	 * @param unknown_type $pVersionId
	 */
	function GetVersionPwtChangeUserIds($pVersionId){
		$lSql = '(SELECT DISTINCT u.id, coalesce(u.first_name, \'\') || \' \' || coalesce(u.last_name, \'\') as user_name,
				CASE WHEN u.id = v.uid THEN 2 ELSE 1 END as ord, 1 as has_changes,
				u.id as undisclosed_real_usr_id, null as undisclosed_user_fullname, 1 as is_disclosed
			FROM pjs.pwt_document_versions pdv
			JOIN pjs.document_versions v ON v.id = pdv.version_id
			JOIN public.usr u ON u.id = ANY(pdv.change_user_ids) OR (u.id = v.uid AND v.is_disclosed = false)
			WHERE v.id = ' . (int) $pVersionId . ')
		UNION
			(SELECT DISTINCT uu.id, coalesce(u.first_name, \'\') || \' \' || coalesce(u.last_name, \'\') as user_name,
				CASE WHEN uu.id = v.undisclosed_usr_id THEN 2 ELSE 1 END as ord, 1 as has_changes,
				u.id as undisclosed_real_usr_id, uu.name as undisclosed_user_fullname, 0 as is_disclosed
			FROM pjs.pwt_document_versions pdv
			JOIN pjs.document_versions v ON v.id = pdv.version_id
			JOIN public.undisclosed_users uu ON uu.id = ANY(pdv.change_user_ids) OR uu.id = v.undisclosed_usr_id
			JOIN public.usr u ON u.id = uu.uid
			WHERE v.id = ' . (int) $pVersionId . ')
		UNION (
			SELECT DISTINCT coalesce(uu.id, u.id) as id, coalesce(u.first_name, \'\') || \' \' || coalesce(u.last_name, \'\') as user_name,
				0 as ord, 0 as has_changes,
				u.id as undisclosed_real_usr_id, uu.name as undisclosed_user_fullname, m.is_disclosed::int as is_disclosed
			FROM pjs.pwt_document_versions pdv
			JOIN pjs.document_versions v ON v.id = pdv.version_id
			JOIN pjs.msg m ON m.version_id = v.id
			JOIN public.usr u ON u.id = m.usr_id
			LEFT JOIN public.undisclosed_users uu ON uu.id = m.undisclosed_usr_id
			WHERE v.id = ' . (int) $pVersionId . ' AND u.id <> ALL(pdv.change_user_ids) AND u.id <> v.uid
				AND (uu.id IS NULL OR uu.id <> ALL(pdv.change_user_ids))
		)
		ORDER BY ord DESC, id ASC
		';
		$this->m_con->Execute($lSql);
		$lResult = array();
		while(!$this->m_con->Eof()){
			$lResult[] = array(
				'id' => $this->m_con->mRs['id'],
				'name' => $this->m_con->mRs['user_name'],
				'undisclosed_real_usr_id' => $this->m_con->mRs['undisclosed_real_usr_id'],
				'undisclosed_user_fullname' => $this->m_con->mRs['undisclosed_user_fullname'],
				'is_disclosed' => $this->m_con->mRs['is_disclosed'],
			);
			$this->m_con->MoveNext();
		}
		return $lResult;
	}

	/**
	 * Returns the list of user who have changes in the specified version
	 * @param unknown_type $pVersionId
	 */
	function GetVersionUserDisplayNames($pVersionId){
		$lSql = '(SELECT DISTINCT u.id, coalesce(u.first_name, \'\') || \' \' || coalesce(u.last_name, \'\') as user_name,
		CASE WHEN u.id = v.uid THEN 2 ELSE 1 END as ord, 1 as has_changes,
		u.id as undisclosed_real_usr_id, null as undisclosed_user_fullname, 1 as is_disclosed
		FROM pjs.pwt_document_versions pdv
		JOIN pjs.document_versions v ON v.id = pdv.version_id
		JOIN public.usr u ON u.id = ANY(pdv.change_user_ids) OR (u.id = v.uid AND v.is_disclosed = true)
		WHERE v.id = ' . (int) $pVersionId . ')
		UNION
		(SELECT DISTINCT uu.id, coalesce(u.first_name, \'\') || \' \' || coalesce(u.last_name, \'\') as user_name,
		CASE WHEN uu.id = v.undisclosed_usr_id THEN 2 ELSE 1 END as ord, 1 as has_changes,
		u.id as undisclosed_real_usr_id, uu.name as undisclosed_user_fullname, 0 as is_disclosed
		FROM pjs.pwt_document_versions pdv
		JOIN pjs.document_versions v ON v.id = pdv.version_id
		JOIN public.undisclosed_users uu ON uu.id = ANY(pdv.change_user_ids) OR uu.id = v.undisclosed_usr_id
		JOIN public.usr u ON u.id = uu.uid
		WHERE v.id = ' . (int) $pVersionId . ')
		ORDER BY ord DESC, id ASC
		';
		$this->m_con->Execute($lSql);
		$lResult = array();
		while(!$this->m_con->Eof()){
			$lResult[] = array(
				'id' => $this->m_con->mRs['id'],
				'name' => $this->m_con->mRs['user_name'],
				'undisclosed_real_usr_id' => $this->m_con->mRs['undisclosed_real_usr_id'],
				'undisclosed_user_fullname' => $this->m_con->mRs['undisclosed_user_fullname'],
				'is_disclosed' => $this->m_con->mRs['is_disclosed'],
			);
			$this->m_con->MoveNext();
		}
		return $lResult;
	}

	/**
	 * Checks whether the specified user can view the specified version
	 *
	 * @param $pVersionId unknown_type
	 * @param $pUid unknown_type
	 */
	function CheckIfUserCanViewDocumentVersion($pDocumentId, $pVersionId, $pUid) {

		$lResult = array();

		$lSql = 'SELECT * FROM pjs."spCheckIfUserCanViewDocumentVersion"(' . (int)$pDocumentId . ', ' . (int)$pVersionId . ', ' . (int)$pUid . ')';
		$this->m_con->Execute($lSql);
		$lResult['result'] = $this->m_con->mRs['result'];

		return $lResult;
	}

	/**
	 * Returns a list of the changes that have not been processed for the
	 * specific pmt document version
	 *
	 * @param $pVersionId unknown_type
	 */
	function GetVersionPwtChanges($pVersionId) {
		$lCon = $this->m_con;
		$lSql = 'SELECT pvc.id, pvc.instance_id, pvc.field_id, pvc.state_id, pvc.value as content
		FROM pjs.pwt_document_versions pv
		JOIN pjs.pwt_document_version_changes pvc ON pvc.pwt_document_version_id = pv.id
		WHERE pv.version_id = ' . (int) $pVersionId . ' AND pvc.state_id <> ' . (int) VERSION_CHANGE_PROCESSED_STATE_ID . '
		';
		$lResult = array();
		$lCon->Execute($lSql);
		while(! $lCon->Eof()){
			$lResult[$lCon->mRs['id']] = $lCon->mRs;
			$lCon->MoveNext();
		}
		// var_dump($lResult);
		return $lResult;
	}

	/**
	 * Returns the xml of the specified
	 *
	 * @param $pVersionId unknown_type
	 * @return multitype:Ambigous <NULL, multitype:, string>
	 */
	function GetVersionPwtXml($pVersionId) {
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
			'xml' => false,
			'pwt_document_id' => false,
			'is_cached' => 0,
		);
		$lCon = $this->m_con;
		$lSql = 'SELECT coalesce(pv.xml_with_changes, pv.xml) as xml, pv.id, pd.pwt_id,
			CASE WHEN coalesce(pv.xml_with_changes::text, \'\') <> \'\' THEN 1 ELSE 0 END as is_cached
		FROM pjs.pwt_document_versions pv
		JOIN pjs.document_versions dv ON dv.id = pv.version_id
		JOIN pjs.pwt_documents pd ON pd.document_id = dv.document_id
		WHERE pv.version_id = ' . (int) $pVersionId . '
		';

// 		$lSql = 'SELECT coalesce(pv.xml) as xml, pv.id, pd.pwt_id
// 		FROM pjs.pwt_document_versions pv
// 		JOIN pjs.document_versions dv ON dv.id = pv.version_id
// 		JOIN pjs.pwt_documents pd ON pd.document_id = dv.document_id
// 		WHERE pv.version_id = ' . (int) $pVersionId . '
// 		';
		// ~ echo $lSql;
		$lResult = array();
		if(! $lCon->Execute($lSql) || ! $lCon->mRs['id']){
			$lResult['err_cnt'] = 1;
			$lResult['err_msgs'][] = array(
				'err_msg' => getstr('pjs.noSuchPmtVersion')
			);
			return $lResult;
		}
		$lResult['xml'] = $lCon->mRs['xml'];
		$lResult['is_cached'] = $lCon->mRs['is_cached'];
		$lResult['pwt_document_id'] = $lCon->mRs['pwt_id'];
		// var_dump($lResult);
		return $lResult;
	}

	/**
	 * Checks whether the specified version has unprocessed changes
	 *
	 * @param $pVersionId unknown_type
	 */
	function CheckIfVersionHasUnprocessedPwtChanges($pVersionId) {
		$lCon = $this->m_con;
		$lSql = 'SELECT pvc.id, pvc.instance_id, pvc.field_id, pvc.state_id, pvc.value as content
		FROM pjs.pwt_document_versions pv
		JOIN pjs.pwt_document_version_changes pvc ON pvc.pwt_document_version_id = pv.id
		WHERE pv.version_id = ' . (int) $pVersionId . ' AND pvc.state_id <> ' . (int) VERSION_CHANGE_PROCESSED_STATE_ID . '
		LIMIT 1
		';
		$lResult = array();
		$lCon->Execute($lSql);
		if((int) $lCon->mRs['id']){
			return true;
		}
		return false;
	}

	/**
	 * Process all the changes for the specific pmt version.
	 * Everything is done in a single transaction
	 *
	 * @param $pVersionId unknown_type
	 * @param $pUid unknown_type
	 * @throws Exception
	 * @return multitype:number multitype: boolean |Ambigous <multitype:number
	 *         multitype: boolean , multitype:boolean NULL >
	 */
	function ProcessVersionPwtChanges($pVersionId) {
// 		error_reporting(-1);
		/**
		 * Process a single instance field
		 * @param DomNode $pNode
		 * @param unknown_type $pChange
		 * @param DBCn $pCon
		 * @param unknown_type $pVersionId
		 * @param unknown_type $pInstanceId
		 * @param unknown_type $pFieldId
		 * @param mComments $pCommentsModel
		 * @throws Exception
		 */
		function ProcessSingleChange(&$pNode, $pChange, &$pCon, $pVersionId, $pInstanceId, $pFieldId, &$pCommentsModel) {
			$lChangeType = $pChange['state_id'];
			if($lChangeType != VERSION_CHANGE_ACCEPT_ALL_CHANGES_STATE_ID && $lChangeType != VERSION_CHANGE_REJECT_ALL_CHANGES_STATE_ID)
				return;

			$lPreviousValue = '';
			foreach ($pNode->childNodes as $lChild) {
				$lPreviousValue .= $pNode->ownerDocument->saveXml($lChild);
			}

			$lFieldComments = $pCommentsModel->GetVersionFieldComments($pVersionId, $pInstanceId, $pFieldId);
			InsertFieldNodeCommentPositionNodes($pNode, $lFieldComments);
// 			var_dump($pNode->ownerDocument->saveXml($pNode));

			$lXPath = new DOMXPath($pNode->ownerDocument);
			$lInsertChanges = $lXPath->query('//' . CHANGE_INSERT_NODE_NAME, $pNode);
			$lRemoveChanges = $lXPath->query('//' . CHANGE_DELETE_NODE_NAME, $pNode);
			// var_dump($pNode->ownerDocument->saveXml($pNode));
			// var_dump($lInsertChanges->length);

			/**
			 * @TODO - to implement change acc/rej to preserve the comment tags
			 * This should prevent $pCommentsModel from performing diffs
			 * to calculate the new comment positions because all
			 * the comment tags would be in the xml
			 */

			if($lChangeType == VERSION_CHANGE_ACCEPT_ALL_CHANGES_STATE_ID){
				// echo 1;
				for($i = $lInsertChanges->length - 1; $i >= 0; -- $i){
					// Copy all nodes before the change and remove the change
					// node
					$lChange = $lInsertChanges->item($i);
					while($lChange->hasChildNodes()){
						$lChange->parentNode->insertBefore($lChange->firstChild, $lChange);
					}
					$lChange->parentNode->removeChild($lChange);
				}
				for($i = $lRemoveChanges->length - 1; $i >= 0; -- $i){
					// Remove all the nodes
					$lChange = $lRemoveChanges->item($i);
					$lChange->parentNode->removeChild($lChange);
				}
			}
			if($lChangeType == VERSION_CHANGE_REJECT_ALL_CHANGES_STATE_ID){
				for($i = $lInsertChanges->length - 1; $i >= 0; -- $i){
					// Remove the newly proposed parts
					$lChange = $lInsertChanges->item($i);
					$lChange->parentNode->removeChild($lChange);
				}
				for($i = $lRemoveChanges->length - 1; $i >= 0; -- $i){
					// Return the previous content
					$lChange = $lRemoveChanges->item($i);
					while($lChange->hasChildNodes()){
						$lChange->parentNode->insertBefore($lChange->firstChild, $lChange);
					}
					$lChange->parentNode->removeChild($lChange);
				}
			}
			$lResult = '';

			foreach ($pNode->childNodes as $lChild) {
				$lResult .= $pNode->ownerDocument->saveXml($lChild);
			}
			$lModifiedComments = $pCommentsModel->RecalculateFieldCommentPositions($pVersionId, $pInstanceId, $pFieldId, $lResult, $lPreviousValue, false);

// 			var_dump(($lResult));
// 			var_dump($lModifiedComments);
// 			exit;

			$pCommentsModel->SaveCommentRecalculatedPositions($lModifiedComments, $pCon);
			//Remove the comment nodes from both the xml and the change value
			$lResult = RemoveFieldCommentNodes($lResult);
			RemoveFieldNodeCommentNodes($pNode);


			//Save the change to the db
			$lSql = ' UPDATE pjs.pwt_document_version_changes SET
					value = \'' . q($lResult) . '\'
				WHERE id = ' . (int)$pChange['id'];
			if(!$pCon->Execute($lSql)){
				throw new Exception(getstr('pjs.couldNotUpdateChangeContent'));
			}
			// var_dump($pNode->ownerDocument->saveXml($pNode));
		}

		$lCon = $this->m_con;
		$lCommentsModel = $this->m_commentsModel;
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
			'result' => false
		);
		try{
			if(! $lCon->Execute('START TRANSACTION;')){
				throw new Exception(getstr('pjs.couldNotStartTransaction'));
			}
			$lResult['result'] = true;
			$lVersionXmlData = $this->GetVersionPwtXml($pVersionId);
			if($lVersionXmlData['err_cnt']){
				throw new Exception($lVersionXmlData['err_msgs'][0]['err_msg']);
			}

			$lDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
			if(! $lDom->loadXML($lVersionXmlData['xml'])){
				throw new Exception(getstr('pjs.couldNotLoadVersionXML'));
			}
			$lXPath = new DOMXPath($lDom);
			$lVersionChanges = $this->GetVersionPwtChanges($pVersionId);
			if(! count($lVersionChanges)){
				$lResult['result'] = true;
				return $lResult;
			}
			// Process each change
			foreach($lVersionChanges as $lCurrentChange){
				$lNodeQuery = '/document/objects//*[@instance_id="' . $lCurrentChange['instance_id'] . '"]/fields/*[@id="' . $lCurrentChange['field_id'] . '"]/value';
				$lNodeResult = $lXPath->query($lNodeQuery);
				// var_dump($lVersionChanges);
				if($lNodeResult->length){
					$lValueNode = $lNodeResult->item(0);
					$lFragment = $lDom->createDocumentFragment();
// 					var_dump($lCurrentChange['content']);
					$lPreparedValue = prepareXmlValue($lCurrentChange['content']);
					while($lValueNode->hasChildNodes()){
						$lValueNode->removeChild($lValueNode->firstChild);
					}
					if(@$lFragment->appendXML($lPreparedValue)){
						// ProcessSingleChange($lFragment,
						// $lCurrentChange['state_id']);
						// var_dump($lPreparedValue);
						$lValueNode->appendChild($lFragment);
						ProcessSingleChange($lValueNode, $lCurrentChange, $lCon, $pVersionId, $lCurrentChange['instance_id'], $lCurrentChange['field_id'], $lCommentsModel);
					}else{
						// var_dump($lRealValue);
						$lValueNode->appendChild($lDom->createTextNode($lCurrentChange['content']));
					}
				}
			}
			// var_dump($lDom->saveXML());
			// Update the version xml
			$lSql = 'UPDATE pjs.pwt_document_versions SET
				xml = \'' . q($lDom->saveXML()) . '\'
				WHERE version_id = ' . (int) $pVersionId . '
			';
			if(! $lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			}
// 			throw new Exception('asd');
			// Mark the changes as processed
			$lSql = 'UPDATE pjs.pwt_document_version_changes pvc SET
				 before_processing_state_id = pvc.state_id,
				 state_id = ' . (int) VERSION_CHANGE_PROCESSED_STATE_ID . '
				FROM pjs.pwt_document_versions pv
				WHERE pvc.pwt_document_version_id = pv.id AND pv.version_id = ' . (int) $pVersionId . ' AND pvc.state_id <> ' . (int) VERSION_CHANGE_PROCESSED_STATE_ID . '
			';
			if(! $lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			}
			if(! $lCon->Execute('COMMIT TRANSACTION;')){
				throw new Exception(getstr('pjs.couldNotCommitTransaction'));
			}
		}catch(Exception $pException){
			$lCon->Execute('ROLLBACK TRANSACTION;');
			$lResult['err_cnt'] = 1;
			$lResult['err_msgs'][] = array(
				'err_msg' => $pException->getMessage()
			);
			$lResult['result'] = false;
		}
		return $lResult;
	}

	function getDecisions($pRole, $pVersionId) {
		$lCon = $this->m_con;
		$lSql = '
			SELECT drr.round_number
			FROM pjs.document_review_round_users drru
			JOIN pjs.document_review_rounds drr ON drr.id = drru.round_id
			WHERE drru.document_version_id = ' . (int)$pVersionId;
		$lCon->Execute($lSql);
		$lRoundNumber = (int)$lCon->mRs['round_number'];
		$lAnd = '';

		switch ($lRoundNumber) {
			case REVIEW_ROUND_ONE :
				if($pRole == CE_ROLE)
					$lAnd .= ' id = 1';
				else
					$lAnd .= ' id <= 5';
				break;
			case REVIEW_ROUND_TWO :
				$lAnd .= ' id <= 5';
				break;
			case REVIEW_ROUND_THREE :
				$lAnd .= ' id = 1 OR id = 2 OR id = 5';
				break;
		}
		$lResult = array();

		$lSql = 'SELECT * FROM pjs.document_review_round_decisions WHERE ' . $lAnd . ' ORDER BY name';
		$lCon->Execute($lSql);
		while(! $lCon->Eof()){
			$lResult[] = $lCon->mRs;
			$lCon->MoveNext();
		}
		return $lResult;
	}

	function getReviwerData($pRoundUserId) {
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT u.id, v.id as document_version, v.uid
					FROM pjs.document_review_round_users u
					LEFT JOIN pjs.document_versions v ON v.id = u.document_version_id
					WHERE u.id = ' . $pRoundUserId;
		$lCon->Execute($lSql);
		$lResult = $lCon->mRs;
		return $lResult;
	}

	/**
	 * checkForDecision
	 * check if user with specific document version has taken decision
	 *
	 * @param $pVersionId int
	 * @return int
	 */
	function checkForDecision($pVersionId) {
		$lResult = array();
		$lResult['has_decision'] = 0;
		$lCon = $this->m_con;

		$lSql = '
			SELECT id
			FROM pjs.document_review_round_users
			WHERE document_version_id = ' . (int)$pVersionId . '
				AND decision_id IS NOT NULL';

		$lCon->Execute($lSql);
		if($lCon->mRs['id']) {
			$lResult['has_decision'] = 1;
		}

		return $lResult;
	}

	function GetAuthorsDetails($pVersionId) {
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT u.first_name, u.last_name, dv.version_num
		FROM pjs.document_versions dv
		JOIN usr u ON u.id = dv.uid
		WHERE dv.id = ' . $pVersionId;
		//~ d.uid = ' . $pUid . '
		$lCon->Execute($lSql);
		//~ echo $lSql;
		$lResult['first_name'] = $lCon->mRs['first_name'];
		$lResult['last_name'] = $lCon->mRs['last_name'];
		$lResult['version_num'] = $lCon->mRs['version_num'];
		return $lResult;
	}

	// @formatter:off
	/**
	 * Here we will return an array containing all the changed instance fields
	 * by all the reviewers for the specified review round.
	 * The format of the array will be the following
	 * array(
	 * 		instance_id => array(
	 * 			field_id => array(
	 * 				user_id => array(
	 * 					modified_version =>	value //The value which the reviewer set
	 * 					user_name => the name of the user who created the change
	 * 				)
	 * 			)
	 * 		)
	 * )
	 *
	 * @param $pRoundId int
	 */
	// @formatter:on
	function GetRoundPwtReviewerModifiedFields($pRoundId, $pReviewerUID = 0, $pReviewerDocumentUserID = 0) {
		$lCon = $this->m_con;
		$lVersionIds = array();
		$lSql = 'SELECT dv.id
				FROM pjs.document_versions dv 
				JOIN pjs.document_review_round_users rru ON rru.document_version_id = dv.id
				JOIN pjs.document_users du ON du.id = rru.document_user_id
				JOIN public.usr u ON u.id = du.uid
				WHERE rru.round_id = ' . (int) $pRoundId . '
				AND du.role_id IN (' . (int) DEDICATED_REVIEWER_ROLE . ', ' . (int) PUBLIC_REVIEWER_ROLE . ', ' . (int) COMMUNITY_REVIEWER_ROLE . ')
				AND rru.decision_id IS NOT NULL
				' . ($pReviewerUID ? 'AND du.uid = ' . $pReviewerUID : '') . '
				' . ($pReviewerDocumentUserID ? 'AND du.id = ' . $pReviewerDocumentUserID : '');
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			$lVersionIds[] = (int)$lCon->mRs['id']; 
			$lCon->MoveNext();
		}
// 		var_dump($lSql);
		//Process all changes which are not processed
		foreach ($lVersionIds as $lVersionId) {			
			if($this->CheckIfVersionHasUnprocessedPwtChanges($lVersionId)){
				$lProcession = $this->ProcessVersionPwtChanges($lVersionId);

				if($lProcession['err_cnt']){
					throw  new Exception($lProcession['err_msgs'][0]['err_msg']);
				}
			}
		}
		
		
		$lResult = array();
		$lSql = 'SELECT vc.instance_id, vc.field_id, vc.value,
				CASE WHEN dv.is_disclosed = false THEN dv.undisclosed_usr_id ELSE du.uid END as uid,
				coalesce(u.first_name, \'\') || \' \' || coalesce(u.last_name, \'\') as user_name,
				dv.id as version_id
			FROM pjs.pwt_document_version_changes vc
			JOIN pjs.pwt_document_versions pdv ON pdv.id = vc.pwt_document_version_id
			JOIN pjs.document_versions dv ON dv.id = pdv.version_id
			JOIN pjs.document_review_round_users rru ON rru.document_version_id = dv.id
			JOIN pjs.document_users du ON du.id = rru.document_user_id
			JOIN public.usr u ON u.id = du.uid
			WHERE rru.round_id = ' . (int) $pRoundId . '
				AND du.role_id IN (' . (int) DEDICATED_REVIEWER_ROLE . ', ' . (int) PUBLIC_REVIEWER_ROLE . ', ' . (int) COMMUNITY_REVIEWER_ROLE . ')
				AND rru.decision_id IS NOT NULL
				' . ($pReviewerUID ? 'AND du.uid = ' . $pReviewerUID : '') . '
				' . ($pReviewerDocumentUserID ? 'AND du.id = ' . $pReviewerDocumentUserID : '') . '
			ORDER BY vc.instance_id, vc.field_id
		';
		$lCon->Execute($lSql);
		while(! $lCon->Eof()){
			$lInstanceId = $lCon->mRs['instance_id'];
			$lFieldId = $lCon->mRs['field_id'];
			$lValue = $lCon->mRs['value'];
			$lUid = $lCon->mRs['uid'];
			$lVersionId = (int)$lCon->mRs['version_id'];
			$lUserName = trim($lCon->mRs['user_name']);

			if(! is_array($lResult[$lInstanceId])){
				$lResult[$lInstanceId] = array();
			}
			if(! is_array($lResult[$lInstanceId][$lFieldId])){
				$lResult[$lInstanceId][$lFieldId] = array();
			}
			$lResult[$lInstanceId][$lFieldId][$lUid] = array(
				'modified_version' => $lValue,
				'user_name' => $lUserName,
				'version_id' => $lVersionId,
			);

			$lCon->MoveNext();
		}
		return $lResult;
	}

	// @formatter:off
	/**
	 * Here we will return an array containing all the changed instance fields
	 * by the editor who took decision for the document in the specified review round.
	 * The format of the array will be the following
	 * array(
	 * 		instance_id => array(
	 * 			field_id => array(
	 * 				original_version => the original version,
	 * 				modified_version => the modified version,
	 * 				uid => the id of the editor
	 * 				user_name => the name of the editor
	 * 			)
	 * 		)
	 * )
	 *
	 * @param $pRoundId int
	 */
	// @formatter:on
	function GetRoundPwtEditorModifiedFields($pRoundId, $pRole = SE_ROLE) {
		$lCon = $this->m_con;
		$lResult = array();
		$lSql = 'SELECT vc.instance_id, vc.field_id, vc.value, u.id as uid,
			coalesce(u.first_name, \'\') || \' \' || coalesce(u.last_name, \'\') as user_name,
			dv.id as version_id
		FROM pjs.pwt_document_version_changes vc
		JOIN pjs.pwt_document_versions pdv ON pdv.id = vc.pwt_document_version_id
		JOIN pjs.document_versions dv ON dv.id = pdv.version_id
		JOIN pjs.document_review_round_users rru ON rru.document_version_id = dv.id
		JOIN pjs.document_review_rounds r ON r.id = rru.round_id AND r.decision_round_user_id = rru.id
		JOIN pjs.document_users du ON du.id = rru.document_user_id
		JOIN public.usr u ON u.id = du.uid
		WHERE r.id = ' . (int) $pRoundId . ' AND (
			du.role_id = ' . (int) $pRole . '
		)
		ORDER BY vc.instance_id, vc.field_id

		';
		$lCon->Execute($lSql);
// 		var_dump($lCon->Eof());
// 		var_dump('AAAAAAAAA');
		while(! $lCon->Eof()){
			$lInstanceId = $lCon->mRs['instance_id'];
			$lFieldId = $lCon->mRs['field_id'];
			$lValue = $lCon->mRs['value'];
			$lUid = $lCon->mRs['uid'];
			$lUserName = trim($lCon->mRs['user_name']);
// 			var_dump($lInstanceId, $lFieldId);

			if(! is_array($lResult[$lInstanceId])){
				$lResult[$lInstanceId] = array();
			}
			if(! is_array($lResult[$lInstanceId][$lFieldId])){
				$lResult[$lInstanceId][$lFieldId] = array();
			}
			$lResult[$lInstanceId][$lFieldId] = array(
				'modified_version' => $lValue,
				'uid' => $lUid,
				'version_id' => $lCon->mRs['version_id'],
				'user_name' => $lUserName,
			);
// 			var_dump('CCCC');
			$lCon->MoveNext();
// 			var_dump('BBBB');
		}
		foreach ($lResult as $lInstanceId => $lInstanceFields){
			foreach ($lInstanceFields as $lFieldId => $lFieldChanges) {
				$lResult[$lInstanceId][$lFieldId]['original_version'] = $this->GetRoundPwtFieldOriginalValue($pRoundId, $lInstanceId, $lFieldId);
			}
		}

// 		var_dump($lResult);
		return $lResult;
	}

	/**
	 * Returns the original value of the specified field
	 * for this round
	 *
	 * @param $pRoundId int
	 * @param $pInstanceId int
	 * @param $pFieldId int
	 */
	function GetRoundPwtFieldOriginalValue($pRoundId, $pInstanceId, $pFieldId) {
		$lOriginalVersionInfo = $this->GetRoundPwtOriginalVersion($pRoundId);
		$lOriginalVersionXml = $lOriginalVersionInfo['xml'];

		$lDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
		if(! $lDom->loadXML($lOriginalVersionXml)){
			// throw new Exception(getstr('pjs.couldNotLoadVersionXML'));
			return false;
		}
		$lXPath = new DOMXPath($lDom);
		$lNodeQuery = '/document/objects//*[@instance_id="' . $pInstanceId . '"]/fields/*[@id="' . $pFieldId . '"]/value';
		$lNodeResult = $lXPath->query($lNodeQuery);
		// var_dump($lVersionChanges);
// 		var_dump($lNodeResult, 1);
		if($lNodeResult->length){
			$lRootNode = $lNodeResult->item(0);
			$lResult = '';
			foreach ($lRootNode->childNodes as $lChild){
				$lResult .= $lDom->saveXML($lChild);
			}
			return trim($lResult);
// 			var_dump($lResult);
// 			return $lNodeResult->item(0)->nodeValue;
		}
		return false;
	}

	/**
	 * Returns an array containing the original pwt version xml and the version id for the specified round
	 *
	 * @param $pRoundId int
	 */
	function GetRoundPwtOriginalVersion($pRoundId) {
		$lSql = 'SELECT dv.xml, dv.version_id
			FROM pjs.pwt_document_versions dv
			JOIN pjs.document_review_rounds r ON r.create_from_version_id = dv.version_id
			WHERE r.id = ' . (int) $pRoundId . '
		';
		$this->m_con->Execute($lSql);
		return array(
			'xml' => $this->m_con->mRs['xml'],
			'version_id' => $this->m_con->mRs['version_id'],
		);
	}

	/**
	 * Returns an array containing the pwt version xml,the version id for the
	 * version of the SE who took decision for the specified review round and
	 * info about the author of the version (the SE)
	 *
	 * @param $pRoundId int
	 */
	function GetReviewRoundSEVersion($pRoundId){
		$lSql = 'SELECT dv.xml, dv.version_id, u.id as uid, coalesce(u.first_name, \'\') || \' \' || coalesce(u.last_name, \'\') as user_name
		FROM pjs.pwt_document_versions dv
		JOIN pjs.document_versions v ON v.id = dv.version_id
		JOIN pjs.document_review_round_users ru ON ru.document_version_id = v.id
		JOIN pjs.document_users du ON du.id = ru.document_user_id AND du.role_id = ' . (int)SE_ROLE . '
		JOIN pjs.document_review_rounds r ON r.id = ru.round_id AND decision_round_user_id = ru.id
		JOIN public.usr u ON u.id = v.uid
		WHERE ru.round_id = ' . (int) $pRoundId . ' AND ru.decision_id IS NOT NULL
		';
// 		var_dump($lSql);
		$this->m_con->Execute($lSql);
		return array(
			'xml' => $this->m_con->mRs['xml'],
			'version_id' => $this->m_con->mRs['version_id'],
			'uid' => $this->m_con->mRs['uid'],
			'user_name' => $this->m_con->mRs['user_name'],
		);
	}

	/**
	 * Returns a list of all the reviewers who reviewed the document and took a decision in the specified round
	 * @param unknown_type $pReviewRoundId
	 */
	function GetReviewRoundReviewersList($pReviewRoundId){
		$lCon = $this->m_con;
		$lResult = array();
		$lSql = 'SELECT u.id as uid,
			coalesce(u.first_name, \'\') || \' \' || coalesce(u.last_name, \'\') as user_name,
			dv.is_disclosed::int as is_disclosed, uu.id as undisclosed_id,
			uu.name as undisclosed_user_name
		FROM pjs.document_review_round_users rru
		JOIN pjs.document_users du ON du.id = rru.document_user_id
		JOIN pjs.document_versions dv ON dv.id = rru.document_version_id
		LEFT JOIN public.undisclosed_users uu ON uu.id = dv.undisclosed_usr_id
		JOIN public.usr u ON u.id = du.uid
		WHERE rru.round_id = ' . (int) $pReviewRoundId . ' AND (
			du.role_id = ' . (int) DEDICATED_REVIEWER_ROLE . ' OR
			du.role_id = ' . (int) PUBLIC_REVIEWER_ROLE . ' OR
			du.role_id = ' . (int) COMMUNITY_REVIEWER_ROLE . '
		) AND rru.decision_id IS NOT NULL
		ORDER BY u.id ASC
		';
		$lCon->Execute($lSql);
		while(! $lCon->Eof()){
			$lResult[] = array(
				'uid' => $lCon->mRs['uid'],
				'user_name' => $lCon->mRs['user_name'],
				'is_disclosed' => $lCon->mRs['is_disclosed'],
				'undisclosed_id' => $lCon->mRs['undisclosed_id'],
				'undisclosed_user_name' => $lCon->mRs['undisclosed_user_name'],
			);

			$lCon->MoveNext();
		}
		return $lResult;
	}


	function GetPwtReviewRoundReviewerChangesPatch($pReviewRoundId, $pReviewerUID = 0, $pReviewerDocumentUserID = 0, &$pMergedComments = null, $pFixComments = false) {
		$lReviewerChanges = $this->GetRoundPwtReviewerModifiedFields($pReviewRoundId, $pReviewerUID, $pReviewerDocumentUserID);
// 		var_dump($lReviewerChanges);
		$lVersionPatches = array();
// 		var_dump($pMergedComments);
		foreach($lReviewerChanges as $lInstanceId => $lInstanceChanges){
			foreach($lInstanceChanges as $lFieldId => $lFieldChanges){
				$lOriginalFieldValue = $this->GetRoundPwtFieldOriginalValue($pReviewRoundId, $lInstanceId, $lFieldId);
// 				var_dump($lOriginalFieldValue);
// 				var_dump($lOriginalFieldValue);
				$lIsFirst = true;
				$lPatch = array();
// 				var_dump($lInstanceId, $lFieldId, $lFieldChanges);
				$lDistinctUserIds = array();
				foreach($lFieldChanges as $lUsrId => $lUsrChanges){
					$lUsrVersionId = $lUsrChanges['version_id'];
					if(!array_key_exists($lUsrVersionId, $lVersionPatches)){
						$lVersionPatches[$lUsrVersionId] = array();
					}
					$lUsrPatch = GetPatch($lOriginalFieldValue, $lUsrChanges['modified_version'], array(array('id' => $lUsrId, 'version_id' => $lUsrVersionId,  'name' => $lUsrChanges['user_name'])));
					if($pFixComments){
						$this->m_commentsModel->RecalculateSingleVersionFieldMergedCommentPositions($pMergedComments, $lUsrVersionId, $lInstanceId, $lFieldId, $lUsrPatch);
					}
					$lVersionPatches[$lUsrVersionId][$lInstanceId][$lFieldId] = $lUsrPatch;
// 					var_dump(($lUsrChanges['modified_version']), strip_tags($lOriginalFieldValue));
					if($lIsFirst){
						$lIsFirst = false;
						$lPatch = $lUsrPatch;
					}else{
						$lPatch = NormalizeChanges($lPatch, $lUsrPatch);
					}
					$lDistinctUserIds[] = $lUsrId;
				}
				if($pFixComments){
// 					var_dump($lPatch);
					$this->m_commentsModel->RecalculateSingleFieldMergedCommentPositions($pMergedComments, $lInstanceId, $lFieldId, $lPatch);
				}

				$lModifiedChanges[$lInstanceId][$lFieldId] = array(
					'patch' => $lPatch,
					'original_version' => $lOriginalFieldValue,
					'user_ids' => array_unique($lDistinctUserIds, SORT_NUMERIC),
				);
			}
		}
// 		var_dump($pMergedComments);
// 		exit;
// 		var_dump($lModifiedChanges);
		$lResult = array(
			'version_patches' => $lVersionPatches,
			'field_patches' => $lModifiedChanges
		);
		return $lResult;
	}

	function SaveReviewerCachedVersion($pVersionId){
		if($this->CheckIfVersionHasUnprocessedPwtChanges($pVersionId)){
			$this->ProcessVersionPwtChanges($pVersionId);
		}
		$lVersionData = $this->GetVersionPwtXml($pVersionId);
		$lVersionXml = $lVersionData['xml'];
		$lCommentsModel = new mComments();
		$lInstanceComments = $lCommentsModel->GetVersionInstanceComments($pVersionId);
		$lVersionXml = InsertDocumentCommentPositionNodes($lVersionXml, $lInstanceComments);
		$lSql = '
			UPDATE pjs.pwt_document_versions SET
				xml_with_changes = \'' . q($lVersionXml) . '\'
			WHERE version_id IN (
				SELECT dv.id
				FROM pjs.document_versions dv
				JOIN pjs.document_review_round_users rru ON rru.document_version_id = dv.id
				JOIN pjs.document_users du ON du.id = rru.document_user_id
				WHERE rru.document_version_id = ' . (int) $pVersionId . '
					AND du.role_id IN (' . (int) DEDICATED_REVIEWER_ROLE . ', ' . (int) PUBLIC_REVIEWER_ROLE . ', ' . (int) COMMUNITY_REVIEWER_ROLE . ')
			)
		';
		$this->m_con->Execute($lSql);
		var_dump($this->m_con->GetLastError());
	}

	/**
	 * Here we will create a version for the reviewer
	 * based on the changes created by him
	 *
	 * @param $pReviewRoundId int
	 * @param $pReviewerUId int
	 */
	function CreatePwtReviewerVersionWithChanges($pReviewRoundId, $pReviewerUId, $pReviewerDocumentUserID = 0) {
		$lOriginalVersionInfo = $this->GetRoundPwtOriginalVersion($pReviewRoundId);
		$lOriginalVersion = $lOriginalVersionInfo['xml'];
		$lDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
// 		var_dump($lOriginalVersion);
		if(! $lDom->loadXML($lOriginalVersion)){
			// throw new Exception(getstr('pjs.couldNotLoadVersionXML'));
			return false;
		}
// 		echo 1;
		$lXPath = new DOMXPath($lDom);
		//The ids of all the users who have made changes to the original version
		$lUsrIds = array();

		$lReviewerChangesPatches = $this->GetPwtReviewRoundReviewerChangesPatch($pReviewRoundId, $pReviewerUId, $pReviewerDocumentUserID);
		$lReviewerChangesPatch = $lReviewerChangesPatches['field_patches'];

		// Generate the patch for each field and usr and update the fields in
		// the xml
		foreach($lReviewerChangesPatch as $lInstanceId => $lInstanceChanges){
			foreach($lInstanceChanges as $lFieldId => $lFieldChanges){
				$lOriginalFieldValue = $lFieldChanges['original_version'];
				$lPatch = $lFieldChanges['patch'];
				$lUsrIds = array_merge($lUsrIds, $lFieldChanges['user_ids']);
// 				var_dump($lPatch);
// 				var_dump($lOriginalFieldValue, $lPatch);
				$lModifiedVersion = ProcessChanges($lOriginalFieldValue, $lPatch);
				$lReviewerChangesPatch[$lInstanceId][$lFieldId]['modified_version'] = $lModifiedVersion;

				$lNodeQuery = '/document/objects//*[@instance_id="' . $lInstanceId . '"]/fields/*[@id="' . $lFieldId . '"]/value';
				$lNodeResult = $lXPath->query($lNodeQuery);
				// var_dump($lVersionChanges);
				if($lNodeResult->length){
					$lValueNode = $lNodeResult->item(0);
					$lFragment = $lDom->createDocumentFragment();
					$lPreparedValue = prepareXmlValue($lModifiedVersion);
					while($lValueNode->hasChildNodes()){
						$lValueNode->removeChild($lValueNode->firstChild);
					}
					if(@$lFragment->appendXML($lPreparedValue)){
						$lValueNode->appendChild($lFragment);
					}else{
						$lValueNode->appendChild($lDom->createTextNode($lModifiedVersion));
					}
				}
			}
		}
		$lUsrIds = array_unique($lUsrIds, SORT_NUMERIC);
		$lUsrIds = array_map('intval', $lUsrIds);
		// Store the editor versions and save the changes in the
		// pjs.pwt_document_version_changes table
		$lDom->encoding = 'UTF-8';
		$lVersionXml = $lDom->saveXML();

// 		var_dump($lDom->encoding);
// 		var_dump($lDom->saveXML());
		$lCon = $this->m_con;
		if(! $lCon->Execute('START TRANSACTION;')){
			return false;
		}

		// Save the version xml
		$lSql = '
				UPDATE pjs.pwt_document_versions SET
					xml_with_changes = \'' . q($lVersionXml) . '\',
					change_user_ids = ARRAY[' . implode(', ', $lUsrIds) . ']::int[]
				WHERE version_id IN (
					SELECT dv.id
					FROM pjs.document_versions dv
					JOIN pjs.document_review_round_users rru ON rru.document_version_id = dv.id
					JOIN pjs.document_users du ON du.id = rru.document_user_id
					WHERE rru.round_id = ' . (int) $pReviewRoundId . '
						AND du.role_id IN (' . (int) DEDICATED_REVIEWER_ROLE . ', ' . (int) PUBLIC_REVIEWER_ROLE . ', ' . (int) COMMUNITY_REVIEWER_ROLE . ')
						AND ' . ($pReviewerUId ? 'du.uid = ' . (int)$pReviewerUId : 'du.id = ' . (int)$pReviewerDocumentUserID) . '
				)
		';

		if(! $lCon->Execute($lSql)){
			$lCon->Execute('ROLLBACK;');
			return false;
		}
		if(! $lCon->Execute('COMMIT TRANSACTION;')){
			return false;
		}
		return true;
	}

	/**
	 * Here we will create a version for the editor
	 * based on the changes created by the reviewers
	 *
	 * @param $pReviewRoundId int
	 */
	function CreatePwtEditorVersionFromReviewerVersions($pReviewRoundId) {
		$lOriginalVersionInfo = $this->GetRoundPwtOriginalVersion($pReviewRoundId);
		$lOriginalVersion = $lOriginalVersionInfo['xml'];
		$lDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
// 		var_dump($lOriginalVersion);
		if(! $lDom->loadXML($lOriginalVersion)){
			// throw new Exception(getstr('pjs.couldNotLoadVersionXML'));
			return false;
		}
// 		echo 1;
		$lXPath = new DOMXPath($lDom);
		//The ids of all the users who have made changes to the original version
		$lUsrIds = array();


		$lMergedComments = $this->m_commentsModel->GetReviewRoundMergedComments($pReviewRoundId);
		$lReviewerChangesPatches = $this->GetPwtReviewRoundReviewerChangesPatch($pReviewRoundId, 0, 0, $lMergedComments, true);
		$lReviewerChangesPatch = $lReviewerChangesPatches['field_patches'];
		// Generate the patch for each field and usr and update the fields in
		// the xml
// 		file_put_contents('/tmp/merge.log', "Review round" . $pReviewRoundId . "\n\n", FILE_APPEND);
		foreach($lReviewerChangesPatch as $lInstanceId => $lInstanceChanges){
			foreach($lInstanceChanges as $lFieldId => $lFieldChanges){
				$lOriginalFieldValue = $lFieldChanges['original_version'];
				$lPatch = $lFieldChanges['patch'];
				$lUsrIds = array_merge($lUsrIds, $lFieldChanges['user_ids']);
// 				var_dump($lPatch);
// 				var_dump($lOriginalFieldValue, $lPatch);
				$lModifiedVersion = ProcessChanges($lOriginalFieldValue, $lPatch);
				$lReviewerChangesPatch[$lInstanceId][$lFieldId]['modified_version'] = $lModifiedVersion;
// 				$lContentsToPut = "\n\n\n\n\n\n";
// 				$lContentsToPut .= var_export($lPatch, 1). "\n\n";
// 				$lContentsToPut .= 'Inst: ' . $lInstanceId . ' Field: ' . $lFieldId . "\n\n";
// 				$lContentsToPut .= 'Orig: ' . $lOriginalFieldValue. "\n\n";
// 				$lContentsToPut .= 'Modified: ' . $lModifiedVersion. "\n\n";
// 				file_put_contents('/tmp/merge.log', $lContentsToPut, FILE_APPEND);
				
				
				$lNodeQuery = '/document/objects//*[@instance_id="' . $lInstanceId . '"]/fields/*[@id="' . $lFieldId . '"]/value';
				$lNodeResult = $lXPath->query($lNodeQuery);
				// var_dump($lVersionChanges);
				if($lNodeResult->length){
					$lValueNode = $lNodeResult->item(0);
					$lFragment = $lDom->createDocumentFragment();
					$lPreparedValue = prepareXmlValue($lModifiedVersion);
					while($lValueNode->hasChildNodes()){
						$lValueNode->removeChild($lValueNode->firstChild);
					}
					if(@$lFragment->appendXML($lPreparedValue)){
						$lValueNode->appendChild($lFragment);
					}else{
						$lValueNode->appendChild($lDom->createTextNode($lModifiedVersion));
					}
				}
			}
		}
		$lUsrIds = array_unique($lUsrIds, SORT_NUMERIC);
		$lUsrIds = array_map('intval', $lUsrIds);
		// Store the editor versions and save the changes in the
		// pjs.pwt_document_version_changes table
		$lDom->encoding = 'UTF-8';
		$lVersionXml = $lDom->saveXML();

// 		var_dump($lDom->encoding);
// 		var_dump($lDom->saveXML());
		$lCon = $this->m_con;
		if(! $lCon->Execute('START TRANSACTION;')){
			return false;
		}
		//Delete previous changes for the editor versions
		$lSql = 'DELETE FROM pjs.pwt_document_version_changes vc
			USING pjs.pwt_document_versions pdv
			JOIN pjs.document_versions dv ON dv.id = pdv.version_id
			JOIN pjs.document_review_round_users rru ON rru.document_version_id = dv.id
			JOIN pjs.document_users du ON du.id = rru.document_user_id
			WHERE rru.round_id = ' . (int) $pReviewRoundId . ' AND (
				du.role_id = ' . (int) SE_ROLE . '
			) AND vc.pwt_document_version_id = pdv.id
		';
// 		var_dump($lSql);
// 		exit;
		if(! $lCon->Execute($lSql)){
			// 					var_dump($lCon->GetLastError());
			$lCon->Execute('ROLLBACK;');
			return false;
		}

		$lSql = 'SELECT dv.*
			FROM pjs.document_versions dv
			JOIN pjs.document_review_round_users rru ON rru.document_version_id = dv.id
			JOIN pjs.document_users du ON du.id = rru.document_user_id
			WHERE rru.round_id = ' . (int) $pReviewRoundId . ' AND (
				du.role_id = ' . (int) SE_ROLE . '
			)
		';
		// 		var_dump($lSql);
		// 		exit;
		if(! $lCon->Execute($lSql)){
			// 					var_dump($lCon->GetLastError());
			$lCon->Execute('ROLLBACK;');
			return false;
		}
		$lSEVersionId = (int)$lCon->mRs['id'];
		if(! $lSEVersionId){
			// 					var_dump($lCon->GetLastError());
			$lCon->Execute('ROLLBACK;');
			return false;
		}


		if(!$this->m_commentsModel->ImportReviewVersionMergedComments($lSEVersionId, $lMergedComments, $this->m_con)){
			$lCon->Execute('ROLLBACK;');
			return false;
		}

		// Create the changes
		foreach($lReviewerChangesPatch as $lInstanceId => $lFieldChanges){
			foreach($lFieldChanges as $lFieldId => $lFieldChanges){
				$lModifiedValue = $lFieldChanges['modified_version'];
				$lSql = 'INSERT INTO pjs.pwt_document_version_changes(pwt_document_version_id, instance_id, field_id, value, state_id)
					SELECT pdv.id, ' . (int) $lInstanceId . ', ' . (int) $lFieldId . ', \'' . q($lModifiedValue) . '\', ' . (int) VERSION_CHANGE_PROCESSED_STATE_ID . '
					FROM pjs.pwt_document_versions pdv
					JOIN pjs.document_versions dv ON dv.id = pdv.version_id
					JOIN pjs.document_review_round_users rru ON rru.document_version_id = dv.id
					JOIN pjs.document_users du ON du.id = rru.document_user_id
					WHERE rru.round_id = ' . (int) $pReviewRoundId . ' AND (
						du.role_id = ' . (int) SE_ROLE . '
					)
				';
// 				var_dump($lSql);
				if(! $lCon->Execute($lSql)){
// 					var_dump($lCon->GetLastError());
					$lCon->Execute('ROLLBACK;');
					return false;
				}
			}
		}

		// Save the version xml
		$lSql = '
				UPDATE pjs.pwt_document_versions SET
					xml = \'' . q($lVersionXml) . '\',
					change_user_ids = ARRAY[' . implode(', ', $lUsrIds) . ']::int[]
				WHERE version_id IN (
					SELECT dv.id
					FROM pjs.document_versions dv
					JOIN pjs.document_review_round_users rru ON rru.document_version_id = dv.id
					JOIN pjs.document_users du ON du.id = rru.document_user_id
					WHERE rru.round_id = ' . (int) $pReviewRoundId . ' AND (
						du.role_id = ' . (int) SE_ROLE . '
					)
				)
		';
// 		var_dump($lSql);
		if(! $lCon->Execute($lSql)){
			$lCon->Execute('ROLLBACK;');
			return false;
		}
		if(! $lCon->Execute('COMMIT TRANSACTION;')){
			return false;
		}
		return true;
	}


	/**
	 * Here we will create a version for the author
	 * based on the changes created by the SE
	 *
	 * @param $pReviewRoundId int
	 */
	function CreatePwtAuthorVersionAfterReviewRound($pReviewRoundId) {
		$lVersionInfo = $this->GetReviewRoundSEVersion($pReviewRoundId);
// 		var_dump($lVersionInfo);
		$lVersion = $lVersionInfo['xml'];
		$lVersionId = $lVersionInfo['version_id'];
		$lCommentsModel = $this->m_commentsModel;

		$lDocumentInfo = $this->GetVersionDocumentInfo($lVersionInfo['version_id']);
		// 		exit;
		$lCon = $this->m_con;
		if(! $lCon->Execute('START TRANSACTION;')){
			return false;
		}

		//Create new author version
		$lSql = 'SELECT * FROM spCreateDocumentVersion(' . (int)$lDocumentInfo['document_id'] . ', ' . (int)$lDocumentInfo['submitting_author_id']
		. ', ' . DOCUMENT_VERSION_AUTHOR_SUBMITTED_TYPE . ', ' . (int)$lVersionInfo['version_id'] . ')';

		if(! $lCon->Execute($lSql)){
			return false;
		}
		$lAuthorVersionId = (int)$lCon->mRs['id'];

		if(!$lAuthorVersionId){
			$lCon->Execute('ROLLBACK');
			return false;
		}

		$lInstanceComments = $lCommentsModel->GetVersionInstanceComments($lAuthorVersionId, $lCon);
		$lVersion = InsertDocumentCommentPositionNodes($lVersion, $lInstanceComments);


		$lDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
		$lDomCopy = new DOMDocument('1.0', DEFAULT_XML_ENCODING);

		if(! $lDom->loadXML($lVersion) || !$lDomCopy->loadXML($lVersion)){
			// throw new Exception(getstr('pjs.couldNotLoadVersionXML'));
			$lCon->Execute('ROLLBACK');
			return false;
		}
		$lRoundReviewers = $this->GetReviewRoundReviewersList($pReviewRoundId);
		$lXPath = new DOMXPath($lDom);
		$lXPathCopy = new DOMXPath($lDomCopy);
		/*
		 * Here we will have to flatten all accepted changes and correct their node names
		 * Theoretically all accepted delete changes have to be flat (i.e. they cannot contain other changes).
		 * The accepted insert changes may contain other changes in them. We will mark them as changes
		 * which have been created by the SE and perform the following changes:
		 * 		- remove all accepted/unaccepted delete changes in them
		 * 		- remove the tags of all accepted/unaccepted insert changes in them
		 */

		//Here we will select the root changes (the changes which are not in other changes)
		//$lAcceptedChangesQuery = '/document/objects//(' . CHANGE_ACCEPTED_INSERT_NODE_NAME . '|' . CHANGE_ACCEPTED_DELETE_NODE_NAME . ')[count(ancestor::' . CHANGE_ACCEPTED_INSERT_NODE_NAME . ') &eq; 0][count(ancestor::' . CHANGE_ACCEPTED_DELETE_NODE_NAME . ') &eq; 0]';
		$lAcceptedChangesQuery = '(/document/objects//' . CHANGE_ACCEPTED_INSERT_NODE_NAME . '|/document/objects//' . CHANGE_ACCEPTED_DELETE_NODE_NAME . ')[count(ancestor::' . CHANGE_ACCEPTED_INSERT_NODE_NAME . ') = 0][count(ancestor::' . CHANGE_ACCEPTED_DELETE_NODE_NAME . ') = 0]';
		$lAcceptedChangesNodes = $lXPath->query($lAcceptedChangesQuery);
// 		var_dump($lAcceptedChangesNodes->length);
		$lModifiedInstanceFields = array();
		for($i = 0, $lLength = $lAcceptedChangesNodes->length; $i < $lLength; ++$i){
			$lChangeNode = $lAcceptedChangesNodes->item($i);
			$lIsDeleteChange = false;
			$lChangeType = (int)CHANGE_INSERT_TYPE;
			if($lChangeNode->nodeName == CHANGE_ACCEPTED_DELETE_NODE_NAME){
				$lIsDeleteChange = true;
				$lChangeType = (int)CHANGE_DELETE_TYPE;
			}
			$lReplacementNode = $lDom->createElement($lIsDeleteChange ? CHANGE_DELETE_NODE_NAME : CHANGE_INSERT_NODE_NAME);
			//Copy the contents of the change
			foreach ($lChangeNode->childNodes as $lChild) {
				$lReplacementNode->appendChild($lChild->cloneNode(true));
			}
// 			var_dump('id', $lDom->saveXML($lReplacementNode), $lChangeNode->getAttribute(CHANGE_ID_ATTRIBUTE_NAME));


			$lChange = createChange($lChangeType, 0, 0, array(array('id' => $lVersionInfo['uid'], 'name' => $lVersionInfo['user_name'])), '');
			SetChangeNodeAttributes($lReplacementNode, $lChange, $lChangeNode->getAttribute(CHANGE_ID_ATTRIBUTE_NAME));
			$lChangeNode->parentNode->replaceChild($lReplacementNode, $lChangeNode);

			$lSubDeleteChangesQuery = './/' . CHANGE_ACCEPTED_DELETE_NODE_NAME . '|.//' . CHANGE_DELETE_NODE_NAME;
			$lSubDeleteNodes = $lXPath->query($lSubDeleteChangesQuery, $lReplacementNode);
// 			var_dump('d', $lSubDeleteNodes->length);
			if($lSubDeleteNodes->length){//Mark the field as modified
				$lInstanceParent = $lXPath->query('./ancestor::*[@instance_id][1]', $lReplacementNode);//The first ancestor with instance_id
				$lFieldParent = $lXPath->query('./ancestor::*[@id][value][1]', $lReplacementNode);//The first ancestor which has an id (field id) and a value subnode
				$lInstanceId = 0;
				$lFieldId = 0;
				if($lInstanceParent->length && $lFieldParent->length){
					$lInstanceId = $lInstanceParent->item(0)->getAttribute('instance_id');
					$lFieldId = $lFieldParent->item(0)->getAttribute('id');
				}
				if($lInstanceId && $lFieldId){
					if(!is_array($lModifiedInstanceFields[$lInstanceId])){
						$lModifiedInstanceFields[$lInstanceId] = array();
					}
					if(!in_array($lFieldId, $lModifiedInstanceFields[$lInstanceId])){
						$lModifiedInstanceFields[$lInstanceId][] = $lFieldId;
					}
				}
			}
			while($lSubDeleteNodes->length){
				$lSubDeleteNode = $lSubDeleteNodes->item(0);
				$lSubDeleteNode->parentNode->removeChild($lSubDeleteNode);
				$lSubDeleteNodes = $lXPath->query($lSubDeleteChangesQuery, $lReplacementNode);
			}
			$lSubInsertChangesQuery = './/' . CHANGE_ACCEPTED_INSERT_NODE_NAME . '|.//' . CHANGE_INSERT_NODE_NAME;
			$lSubInsertNodes = $lXPath->query($lSubInsertChangesQuery, $lReplacementNode);
// 			var_dump('i', $lSubInsertNodes->length);
			while($lSubInsertNodes->length){
				$lSubInsertNode = $lSubInsertNodes->item(0);
				$lParent = $lSubInsertNode->parentNode;
				foreach ($lSubInsertNode->childNodes as $lChild) {
					$lParent->insertBefore($lChild->cloneNode(true), $lSubInsertNode);
				}
				$lParent->removeChild($lSubInsertNode);
				$lSubInsertNodes = $lXPath->query($lSubInsertChangesQuery, $lReplacementNode);
			}
// 			echo "\n\n";

		}

		foreach($lModifiedInstanceFields as $lInstanceId => $lInstanceFields){
			foreach ($lInstanceFields as $lFieldId) {
				$lFieldValueQuery = '//*[@instance_id=\'' . $lInstanceId . '\']/fields/*[@id=\'' . $lFieldId .'\']/value';
				$lNewValueNodes = $lXPath->query($lFieldValueQuery);
				$lPreviousValueNodes = $lXPathCopy->query($lFieldValueQuery);

				if($lNewValueNodes->length && $lPreviousValueNodes->length){
					$lNewFieldValue = getFieldInnerXML($lNewValueNodes->item(0));
					$lPreviousFieldValue = getFieldInnerXML($lPreviousValueNodes->item(0));
					$lCommentsModel->RecalculateFieldCommentPositions($lAuthorVersionId, $lInstanceId, $lFieldId, $lNewFieldValue, $lPreviousFieldValue, true, $this->m_con);
				}
			}
		}

		//The ids of all the users who have made changes to the original version
		$lUsrIds = array($lVersionInfo['uid']);
		foreach ($lRoundReviewers as $lReviewer){
			$lUserId = $lReviewer['uid'];
			if((int)$lReviewer['undisclosed_id']){
				$lUserId = $lReviewer['undisclosed_id'];
			}
			$lUsrIds[] = $lUserId;
		}

		// Store the correct version xml
		$lDom->encoding = 'UTF-8';

		// Remove the comment marker nodes
		RemoveFieldNodeCommentNodes($lDom->documentElement);
		$lVersionXml = $lDom->saveXML();

// 		var_dump($lVersionXml);



		$lUsrIds = array_unique($lUsrIds, SORT_NUMERIC);
		$lUsrIds = array_map('intval', $lUsrIds);

		// 		exit;
		// Save the version xml
		$lSql = '
		UPDATE pjs.pwt_document_versions SET
			xml = \'' . q($lVersionXml) . '\',
			change_user_ids = ARRAY[' . implode(', ', $lUsrIds) . ']::int[]
		WHERE version_id = ' . (int) $lAuthorVersionId . '
		';
		if(! $lCon->Execute($lSql)){
			$lCon->Execute('ROLLBACK;');
			return false;
		}
		if(! $lCon->Execute('COMMIT TRANSACTION;')){
			return false;
		}
		return $lAuthorVersionId;
	}

	/**
	 * Here we will create a version for the author
	 * based on the changes created by the SE
	 *
	 * @param $pReviewRoundId int
	 */
	function CreatePwtAuthorVersionAfterSubmissionRound($pReviewRoundId, $pRole = SE_ROLE) {
		if($pRole == SE_ROLE){
			return $this->CreatePwtAuthorVersionAfterReviewRound($pReviewRoundId);
		}
		$lOriginalVersionInfo = $this->GetRoundPwtOriginalVersion($pReviewRoundId);
		$lOriginalVersion = $lOriginalVersionInfo['xml'];
		$lDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
		if(! $lDom->loadXML($lOriginalVersion)){
			// throw new Exception(getstr('pjs.couldNotLoadVersionXML'));
			return false;
		}
		$lXPath = new DOMXPath($lDom);
		$lReviewerChangesPatches = $this->GetPwtReviewRoundReviewerChangesPatch($pReviewRoundId, 0, 0, $pMergedComments = null, false);
		$lReviewerChangesPatch = $lReviewerChangesPatches['field_patches'];
		$lEditorChangedFields = $this->GetRoundPwtEditorModifiedFields($pReviewRoundId, $pRole);
		//The ids of all the users who have made changes to the original version
		$lUsrIds = array();
// 		var_dump($lEditorChangedFields);

		//Generate the patch for each field and mark the changes in it
		foreach($lEditorChangedFields as $lInstanceId => $lInstanceChanges){
			foreach($lInstanceChanges as $lFieldId => $lFieldChanges){
// 				var_dump($lFieldChanges);
				$lUsrIds[] = $lFieldChanges['uid'];

				$lOriginalFieldValue = $lFieldChanges['original_version'];
				$lModifiedVersion = $lFieldChanges['modified_version'];
// 				var_dump($lModifiedVersion);
				$lPatch = GetPatch($lOriginalFieldValue, RemoveUnacceptedDeleteChanges($lModifiedVersion), array(array('id' => $lFieldChanges['uid'], 'version_id' => $lFieldChanges['version_id'], 'name' => $lFieldChanges['user_name'])));
// 				var_dump($lPatch);
// 				var_dump(GetPatch(RemoveUnacceptedDeleteChanges($lModifiedVersion), $lOriginalFieldValue, array(array('id' => $lFieldChanges['uid'], 'name' => $lFieldChanges['user_name']))));


				$lPatchWithDeletes =  GetPatch($lOriginalFieldValue, $lModifiedVersion, array(array('id' => $lFieldChanges['uid'], 'version_id' => $lFieldChanges['version_id'],  'name' => $lFieldChanges['user_name'])));
				$lUnacceptedChanges = GetUnacceptedChanges($lModifiedVersion);
// 				var_dump($lUnacceptedChanges);

				$lPatch = FixReviewerPatchOrder($lPatch, $lPatchWithDeletes);
// 				var_dump($lOriginalFieldValue);
// 				var_dump(br2nl_custom(html_entity_decode(trim($lModifiedVersion), ENT_COMPAT, 'utf-8')));
// 				var_dump($lPatch);
// 				var_dump($lUnacceptedChanges);

				$lPatch = MarkUnacceptedChangesToPatchChanges($lUnacceptedChanges, $lPatch);
				$lReviewersPatch = $lReviewerChangesPatch[$lInstanceId][$lFieldId]['patch'];

				$lUsrIds = array_merge($lUsrIds, $lReviewerChangesPatch[$lInstanceId][$lFieldId]['user_ids']);
				if(is_array($lReviewersPatch)){
					$lPatch = MarkAcceptedChanges($lPatch, $lReviewersPatch);
				}

				$lModifiedVersion = ProcessChanges($lOriginalFieldValue, $lPatch);
// 				var_dump($lModifiedVersion);

				//Update the xml
				$lNodeQuery = '/document/objects//*[@instance_id="' . $lInstanceId . '"]/fields/*[@id="' . $lFieldId . '"]/value';
				$lNodeResult = $lXPath->query($lNodeQuery);
				// var_dump($lVersionChanges);
				if($lNodeResult->length){
					$lValueNode = $lNodeResult->item(0);
					$lFragment = $lDom->createDocumentFragment();
					$lPreparedValue = prepareXmlValue($lModifiedVersion);
					while($lValueNode->hasChildNodes()){
						$lValueNode->removeChild($lValueNode->firstChild);
					}
					if(@$lFragment->appendXML($lPreparedValue)){
						$lValueNode->appendChild($lFragment);
					}else{
						$lValueNode->appendChild($lDom->createTextNode($lModifiedVersion));
					}
				}
// 				exit;
			}
		}
		// Store the editor versions and save the changes in the
		// pjs.pwt_document_version_changes table
		$lDom->encoding = 'UTF-8';
		$lVersionXml = $lDom->saveXML();
		$lDocumentInfo = $this->GetVersionDocumentInfo($lOriginalVersionInfo['version_id']);
// 		exit;
		$lCon = $this->m_con;
		if(! $lCon->Execute('START TRANSACTION;')){
			return false;
		}
		//Create new author version
		$lSql = 'SELECT * FROM spCreateDocumentVersion(' . (int)$lDocumentInfo['document_id'] . ', ' . (int)$lDocumentInfo['submitting_author_id']
			. ', ' . DOCUMENT_VERSION_AUTHOR_SUBMITTED_TYPE . ', ' . (int)$lOriginalVersionInfo['version_id'] . ')';

		if(! $lCon->Execute($lSql)){
			return false;
		}
		$lAuthorVersionId = (int)$lCon->mRs['id'];

		if(!$lAuthorVersionId){
			$lCon->Execute('ROLLBACK');
			return false;
		}

		$lUsrIds = array_unique($lUsrIds, SORT_NUMERIC);
		$lUsrIds = array_map('intval', $lUsrIds);

// 		exit;
		// Save the version xml
		$lSql = '
				UPDATE pjs.pwt_document_versions SET
					xml = \'' . q($lVersionXml) . '\',
					change_user_ids = ARRAY[' . implode(', ', $lUsrIds) . ']::int[]
				WHERE version_id = ' . (int) $lAuthorVersionId . '
		';
		if(! $lCon->Execute($lSql)){
			$lCon->Execute('ROLLBACK;');
			return false;
		}
		if(! $lCon->Execute('COMMIT TRANSACTION;')){
			return false;
		}
		return $lAuthorVersionId;
	}

	function checkDocumentUserRole($pDocumentId, $pRole){
		global $user;
		$lCon = $this->m_con;
		$lSql = 'SELECT role_id FROM pjs.document_users WHERE uid = ' . $user->id . ' AND document_id = ' . $pDocumentId . ' AND role_id = ' .$pRole;
		//~ echo $lSql;
		if(!$lCon->Execute($lSql)){
			return false;
		} else {
			return $lCon->mRs['role_id'];
		}


	}

	function GetDocumentStructure($pDocumentId, $pVersionId) {
		$lResult = array();
		$lCon = $this->m_con;
		$lDocumentId = $pDocumentId;
		if(!$lDocumentId) {
			$lSql = 'SELECT document_id FROM pjs.document_versions WHERE id = ' . (int)$pVersionId . ' LIMIT 1';
			$lCon->Execute($lSql);
			$lDocumentId = $lCon->mRs['document_id'];
		}

		$lSql = 'SELECT pwt_id as id FROM pjs.pwt_documents WHERE document_id = ' . $lDocumentId;
		$lCon->Execute($lSql);

		if((int)$lCon->mRs['id']) {
			//$lSqlDocumentStructure = 'SELECT * FROM spGetDocumentTree(' . (int)$lCon->mRs['id'] . ',null) WHERE char_length(pos) = 2';
			$lSqlDocumentStructure = '
				select * from (
					SELECT DISTINCT ON (i.id)
						i.display_name as object_name, i.pos as pos
					FROM pwt.document_object_instances i
					LEFT JOIN pwt.document_object_instances i1 ON (i1.parent_id = i.id) AND i1.is_confirmed = TRUE
					LEFT JOIN pwt.instance_field_values f ON (i.id = f.instance_id)
					WHERE i.display_in_tree = true
						AND char_length(i.pos) = 2
						AND i.document_id = ' . (int)$lCon->mRs['id'] . '
						AND i.object_id not in (236, 237) -- figures & tables
						AND (
							i1.id IS NOT NULL OR
							(
								f.value_str <> \'\' OR
								value_int IS NOT NULL OR
								array_upper(f.value_arr_int, 1) IS NOT NULL OR
								array_upper(f.value_arr_str, 1) IS NOT NULL OR
								value_date IS NOT NULL OR
								array_upper(f.value_arr_date, 1) IS NOT NULL
							)
						)
				
						) as a
				order by pos
			';
			//var_dump($lSqlDocumentStructure);
			$lCon->Execute($lSqlDocumentStructure);
			while (!$lCon->Eof()) {
				$lResult[] = $lCon->mRs;
				$lCon->MoveNext();
			}

		}

		return $lResult;
	}
	function GetUserRoundDataByVersion($pVersionId){
		$lCon = $this->m_con;
		$lSql = 'SELECT (u.first_name || \' \' || u.last_name) as name, drrd.name as decision, dru.id, u.id as reviewer_uid, dr.role_id as reviewer_role
			FROM pjs.document_review_round_users dru
			JOIN pjs.document_users dr ON dr.id = dru.document_user_id
			JOIN public.usr u ON u.id = dr.uid
			LEFT JOIN pjs.document_review_round_decisions drrd ON drrd.id = dru.decision_id
			WHERE dru.document_version_id = ' . $pVersionId;
		if(!$lCon->Execute($lSql)){
			return false;
		} else {
			return $lCon->mRs;
		}
	}
	function checkUserPermissionsForDecisionForm($pRole, $pDocumentId){
		global $user;
		$lCon = $this->m_con;
		$lSql = 'SELECT * FROM pjs.spCheckUserPermissionsForDecisionForm(' . $pRole . ', ' . $pDocumentId . ', ' . $user->id . ') as rights';

		if(!$lCon->Execute($lSql)){
			return false;
		} else {
			return (int)$lCon->mRs['rights'];
		}
	}
	function GetReviewersAnswers($pDocumentId, $pRoundId){
		$lResult = array();
		$lFinalRes = array();
		$lCon = $this->m_con;
		
		$lSql = '
			SELECT pa.*, p.label
			FROM pjs.document_review_round_users_form f
			JOIN pjs.poll_answers pa ON pa.document_review_round_users_form_id = f.id
			JOIN pjs.poll p ON p.id = pa.poll_id
			WHERE f.round_id = ' . $pRoundId . ' 
				AND f.decision_id IS NOT NULL 
				AND pa.answer_id IS NOT NULL
			ORDER BY p.ord
		';
		
		$lCon->Execute($lSql);
		while (!$lCon->Eof()) {
			$lResult[$lCon->mRs['poll_id']][$lCon->mRs['answer_id']]++;
			$lResult[$lCon->mRs['poll_id']]['label'] = $lCon->mRs['label'];
			$lResult[$lCon->mRs['poll_id']]['count']++;
			$lCon->MoveNext();
		}
		
		foreach ($lResult as $key => $value) {
			$lFinalRes[] = array(
				'1' => ((int)$value['1'] ? ceil(((int)$value['1']/(int)$value['count'])*100) . '%' : '0%'),
				'2' => ((int)$value['2'] ? ceil(((int)$value['2']/(int)$value['count'])*100) . '%' : '0%'),
				'3' => ((int)$value['3'] ? ceil(((int)$value['3']/(int)$value['count'])*100) . '%' : '0%'),
				'4' => ((int)$value['4'] ? ceil(((int)$value['4']/(int)$value['count'])*100) . '%' : '0%'),
				'label' => $value['label']
			);
			
		}
		
		return $lFinalRes;
	}

	function GetPollQuestions($pJournalId, $pVersionId){
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT p.* 
				FROM pjs.document_review_round_users usr
				JOIN pjs.document_review_round_users_form f ON usr.id = f.document_review_round_user_id
				JOIN pjs.poll_answers a ON a.document_review_round_users_form_id = f.id
				JOIN pjs.poll p ON p.id = a.poll_id
				WHERE usr.document_version_id = ' . $pVersionId . '
				ORDER BY p.ord';
				
		$lCon->Execute($lSql);
		if(!$lCon->RecordCount()){
			$lSql = 'SELECT * FROM pjs.poll WHERE journal_id = ' . (int)$pJournalId . ' AND state = 1 ORDER BY ord';
			$lCon->Execute($lSql);
		}
		
		while (!$lCon->Eof()) {
			$lResult[] = $lCon->mRs;
			$lCon->MoveNext();
		}
		
		return $lResult;
	}

	function GetPollAnswers($pVersionId) {
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT a.poll_id, a.answer_id 
				FROM pjs.document_review_round_users usr
				JOIN pjs.document_review_round_users_form f ON usr.id = f.document_review_round_user_id
				JOIN pjs.poll_answers a ON a.document_review_round_users_form_id = f.id
				WHERE usr.document_version_id = ' . $pVersionId;
		
		$lCon->Execute($lSql);
		while (!$lCon->Eof()) {
			$lResult['question' . $lCon->mRs['poll_id']] = $lCon->mRs['answer_id'];
			$lCon->MoveNext();
		}
		
		return $lResult;
	}

	function GetSEDecisionDetails($pVersionId) {
		$lResult = array();
		$lCon = $this->m_con;

		$lSql = 'SELECT u.first_name || \' \' || u.last_name as se_name, drrd.name as decision
		FROM pjs.document_versions dv
		JOIN usr u ON u.id = dv.uid
		JOIN pjs.document_review_round_users drru ON drru.document_version_id = dv.id
		JOIN pjs.document_review_round_decisions drrd ON drrd.id = drru.decision_id
		WHERE dv.id = ' . (int)$pVersionId;

		$lCon->Execute($lSql);
		$lResult['se_name'] = $lCon->mRs['se_name'];
		$lResult['decision'] = $lCon->mRs['decision'];

		return $lResult;
	}

	function CheckUserSpecificRole($pUserId, $pDocumentId, $pRoles = ARRAY(SE_ROLE, JOURNAL_EDITOR_ROLE)) {
		$lResult = 0;
		$lCon = $this->m_con;

		$lSql = 'SELECT id FROM pjs.document_users WHERE document_id = ' . $pDocumentId . ' AND uid = ' . $pUserId . ' AND role_id IN (' . implode(',', $pRoles) . ') LIMIT 1';

		$lCon->Execute($lSql);
		if($lCon->mRs['id']) {
			$lResult = 1;
		}

		return $lResult;
	}

}
?>