<?php

/**
 * A model class to handle document manipulation
 * @author peterg
 *
 */
class mDocuments_Model extends emBase_Model {

	//@formatter:off
	/**
	 * This function tries to create a new document / update a current document from the
	 * passed pwt document. If there is a document from this document and it is incomplete -
	 * we return this document. If there is a document from the specified document which is not incomplete and it
	 * is not waiting for a new author version - we return an error. The return format should be the following
	 * array(
	 * 		document_id => the pjs id of the document,
	 * 		document_state => whether the document is newly imported or it is being updated (e.g. after an author round - a new author version has been created)
	 * 		err_cnt => # of errors,
	 * 		err_msgs => an array of error msgs
	 * )
	 * @param unknown_type $pPwtDocumentId
	 * @param unknown_type $pDocumentXml
	 */
	//@formatter:on
	function CreateNewDocumentFromPwtDocument($pPwtDocumentId, $pJournalId, $pDocumentXml, $pUid, $pCommentsXml = ''){
		$lCon = $this->m_con;
		$lResult = array(
			'document_id' => 0,
			'err_cnt' => 0,
			'err_msgs' => array(),
		);

		$lSql = 'SELECT * FROM spCreateDocumentFromPwtDocument(' . (int)$pPwtDocumentId . ', ' . (int) $pJournalId
			. ', ' . ' \'' . q($pDocumentXml) . '\', ' . (int)$pUid . ', ' . E_ROUND_TYPE . ',  \'' . q($pCommentsXml) . '\')';

//  		var_dump($lSql);
		if(!$lCon->Execute($lSql)){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $lCon->GetLastError());
		}else{
			$lResult['document_id'] = $lCon->mRs['document_id'];
			$lResult['document_state'] = $lCon->mRs['state_id'];
			$lResult['event_id'] = $lCon->mRs['event_id'];
		}

		return $lResult;
	}

	/**
	 * This function returns metadata about the specified document (i.e author_id, document_source_id, creation_step etc.)
	 * @param bigint $pDocumentId
	 */
	function GetDocumentInfo($pDocumentId, $pRoleType = 0){
		$lCon = $this->m_con;
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
		);


		$lSql = 'SELECT * FROM spGetDocumentInfo(' . (int)$pDocumentId . ', ' . (int)$pRoleType . ')';
		//~ echo $lSql;
		// 		var_dump($lSql);
		if(!$lCon->Execute($lSql)){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $lCon->GetLastError());
		}else{
			$lResult += $lCon->mRs;
		}

		return $lResult;
	}

	/**
	 * Returns a list of the subject editors which have been assigned to the specified document
	 * @param int $pDocumentId
	 */
	function GetAssignedSEList($pDocumentId){

		$lSql = 'SELECT u.*
		FROM pjs.document_users du
		JOIN usr u ON u.id = du.uid
		WHERE du.document_id = ' . (int)$pDocumentId . ' AND du.role_id = ' . (int) SE_ROLE . '
		';

		$lResult = array();
		$this->m_con->Execute($lSql);
		while(!$this->m_con->Eof()){
			$lResult[] = $this->m_con->mRs;
			$this->m_con->MoveNext();
		}
// 		var_dump($lSql);
		return $lResult;
	}

	/**
	 * Returns a list of the layout editors which have been assigned to the specified document
	 * @param int $pDocumentId
	 */
	function GetAssignedLEList($pDocumentId){

		$lSql = 'SELECT u.*
		FROM pjs.document_users du
		JOIN usr u ON u.id = du.uid
		WHERE du.document_id = ' . (int)$pDocumentId . ' AND du.role_id = ' . (int) LE_ROLE . '
		';

		$lResult = array();
		$this->m_con->Execute($lSql);
		while(!$this->m_con->Eof()){
			$lResult[] = $this->m_con->mRs;
			$this->m_con->MoveNext();
		}
		// 		var_dump($lSql);
		return $lResult;
	}

	/**
	 * Returns a list of the copy editors which have been assigned to the specified document
	 * @param int $pDocumentId
	 */
	function GetAssignedCEList($pDocumentId){

		$lSql = 'SELECT u.*
		FROM pjs.document_users du
		JOIN usr u ON u.id = du.uid
		WHERE du.document_id = ' . (int)$pDocumentId . ' AND du.role_id = ' . (int) CE_ROLE . '
		';

		$lResult = array();
		$this->m_con->Execute($lSql);
		while(!$this->m_con->Eof()){
			$lResult[] = $this->m_con->mRs;
			$this->m_con->MoveNext();
		}
		// 		var_dump($lSql);
		return $lResult;
	}

	/**
	 * Returns a list of the invited dedicated reviewers for the current review round of the specified document
	 * @param int $pDocumentId
	 */
	function GetAssignedDedicatedReviewersList($pDocumentId){

		$lSql = 'SELECT
			u.*,
			ist.name as invitation_state_name,
			i.date_invited,
			i.date_confirmed,
			i.date_canceled,
			i.due_date,
			ist.id as invitation_state,
			i.id as invitation_id,
			de.name as decision_name,
			drrus.id as usr_state,
			rd.decision_id,
			rd.due_date as review_usr_due_date,
			rd.id as reviwer_id,
			i.round_id as round_id,
			drr.enough_reviewers,
			coalesce(drr.round_number, 1) as round_number,
			drrt.name as round_name,
			drru.document_version_id as reviwer_document_version_id
		FROM pjs.document_user_invitations i
		JOIN pjs.document_user_invitation_states ist ON ist.id = i.state_id
		JOIN pjs.documents d ON d.id = i.document_id AND d.state_id = ' . (int)DOCUMENT_IN_REVIEW_STATE . ' AND d.current_round_id = i.round_id

		LEFT JOIN pjs.document_review_rounds drr ON drr.id = d.current_round_id
		LEFT JOIN pjs.document_review_round_types drrt ON drrt.id = drr.round_type_id
		LEFT JOIN pjs.document_users dus ON dus.document_id = d.id AND dus.uid = i.uid AND dus.role_id = i.role_id
		LEFT JOIN pjs.document_review_round_users rd ON rd.round_id = i.round_id AND rd.document_user_id = dus.id
		LEFT JOIN pjs.document_review_round_users drru ON drru.id = rd.id
		LEFT JOIN pjs.document_review_round_user_states drrus ON drrus.id = rd.state_id
		LEFT JOIN pjs.document_review_round_decisions de ON de.id = rd.decision_id

		JOIN usr u ON u.id = i.uid
		WHERE i.document_id = ' . (int)$pDocumentId . ' AND i.role_id = ' . (int) DEDICATED_REVIEWER_ROLE . ' AND i.due_date IS NOT NULL
		';
		return $this->ArrayOfRows($lSql, 0);
	}

	function AddRemoveSE($pDocumentID, $pSEId, $pUid, $pAdd = true){
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		try{
			$lCon = $this->m_con;
			//The checks whether the current user is journal manager or the specified se is SE of the journal of the document are performed in the SP

			$lSql = 'SELECT * FROM pjs."spDocumentSE"(' . (int) $pAdd . ', ' . (int)$pDocumentID . ', ' . (int)$pSEId . ', ' . (int)$pUid . ');';
			//~ echo $lSql;
			//~ exit;
			if(!$lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			}
			$lResult['success_msg'] = getstr('pjs.actionSuccessfullyPerformed');
			$lResult['event_id'][] = $lCon->mRs['event_id'];
			if($lCon->mRs['event_id_sec']){
				$lResult['event_id'][] = $lCon->mRs['event_id_sec'];
			}
		}catch(Exception $pException){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $pException->getMessage());
		}
		return $lResult;
	}

	function ReInviteDocumentReviewer($pInvitedUsrId, $pDocumentId, $pUsrId, $pRoundId = 0){
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);

		try{
			$lCon = $this->m_con;
			//The checks whether the current user is journal manager or the specified se is SE of the journal of the document are performed in the SP

			$lSql = 'SELECT * FROM pjs."spDocumentInviteReviewer"(
					' . (int)$pDocumentId . ',
					ARRAY[' . $pInvitedUsrId . ']::int[],
					' . $pUsrId . ',
					' . DEDICATED_REVIEWER_ROLE . ',
					' . $pRoundId . ')';

			//~ echo $lSql;
			//~ exit;
			if(!$lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			} else {
				$lResult['event_id'] = (int)$lCon->mRs['event_id'];
			}
			$lResult['success_msg'] = getstr('pjs.actionSuccessfullyPerformed');
		}catch(Exception $pException){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $pException->getMessage());
		}
		return $lResult;
	}

	function AddRemoveLE($pDocumentID, $pLEId, $pUid, $pAdd = true){
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		try{
			$lCon = $this->m_con;
			//The checks whether the current user is journal manager or the specified se is SE of the journal of the document are performed in the SP
// 			var_dump($pAdd);
			$lSql = 'SELECT * FROM spDocumentLE(' . (int) $pAdd . ', ' . (int)$pDocumentID . ', ' . (int)$pLEId . ', ' . (int)$pUid . ');';
// 			var_dump($lSql);
			if(!$lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			} else {
				$lResult['event_id'][] = $lCon->mRs['event_id'];
				if($lCon->mRs['event_id_sec']) {
					$lResult['event_id'][] = $lCon->mRs['event_id_sec'];
				}
			}
			$lResult['success_msg'] = getstr('pjs.actionSuccessfullyPerformed');
		}catch(Exception $pException){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $pException->getMessage());
		}
		return $lResult;
	}

	function AddRemoveCE($pDocumentID, $pCEId, $pUid, $pAdd = true, $pCurrentRoundId){
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		try{
			$lCon = $this->m_con;
			//The checks whether the current user is journal manager or the specified se is SE of the journal of the document are performed in the SP

			$lSql = 'SELECT * FROM spDocumentCE(' . (int) $pAdd . ', ' . (int)$pDocumentID . ', ' . (int)$pCurrentRoundId . ',' . (int)$pCEId . ', ' . (int)$pUid . ');';
			if(!$lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			} else {
				$lResult['event_id'][] = $lCon->mRs['event_id'];
				if($lCon->mRs['event_id_sec']) {
					$lResult['event_id'][] = $lCon->mRs['event_id_sec'];
				}
			}
			$lResult['success_msg'] = getstr('pjs.actionSuccessfullyPerformed');
		}catch(Exception $pException){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $pException->getMessage());
		}
		return $lResult;
	}

	/**
	 * Creates an invitation for the specified user and role
	 * @param unknown_type $pDocumentID
	 * @param unknown_type $pReviewerId
	 * @param unknown_type $pUid
	 * @param unknown_type $pRole
	 * @throws Exception
	 * @return Ambigous <multitype:NULL , multitype:number multitype: Ambigous <unknown, mixed> >
	 */
	function InviteReviewer($pDocumentID, $pReviewerIds, $pUid, $pRole = DEDICATED_REVIEWER_ROLE, $pRoundId = null){
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		try{
			if(!$pRoundId) {
				$pRoundId = 'null';
			}

			$lCon = $this->m_con;
			//The checks whether the current user is journal manager or the specified se is SE of the journal of the document are performed in the SP

			$lSql = 'SELECT * FROM pjs."spDocumentInviteReviewer"(' . (int)$pDocumentID . ', ' . $pReviewerIds . ', ' . (int)$pUid . ', ' . (int)$pRole . ', ' . $pRoundId . ');';
			//var_dump($lSql);
			//exit;
			if(!$lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			} else {
				while(!$lCon->Eof()){
					$lResult['data']['event_id'][] = $lCon->mRs['event_id'];
					$lCon->MoveNext();
				}
				$lResult['success_msg'] = getstr('pjs.actionSuccessfullyPerformed');
			}

		}catch(Exception $pException){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $pException->getMessage());
		}
		return $lResult;
	}

	function InviteReviewerAsGhost($pUid, $pDocumentID, $pCurrentRoundId){
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		try{
			if(!$pRoundId) {
				$pRoundId = 'null';
			}

			$lCon = $this->m_con;
			$lSql = "SELECT * FROM pjs.\"spInviteReviewerAsGhost\"($pUid, $pDocumentID, $pCurrentRoundId)";
			//~ var_dump($lSql);
			//~ exit;
			if(!$lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			}
			$lResult['success_msg'] = getstr('pjs.actionSuccessfullyPerformed');
		}catch(Exception $pException){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $pException->getMessage());
		}
		return $lResult;
	}
	
	function SaveReviewerRole($lReviewer, $lCurrentRound, $lRole, $lDoc_id){
		if ($lRole == 0)
			$lRole = 'NULL';
		
		$SQL = "SELECT * FROM pjs.\"spInviteReviewerAsGhost\"($lReviewer , $lDoc_id, $lCurrentRound);
				UPDATE pjs.document_user_invitations 
				 SET role_id = $lRole
				 WHERE round_id = $lCurrentRound AND uid = $lReviewer";
		$this->ArrayOfRows($SQL, 1);
		return array('dont_redirect' => true);
	}

	/**
	 * Confirms/cancels the reviewer invitation (SE confirm/cancel is performed)
	 * @param unknown_type $pInvitationId
	 * @param unknown_type $pSEId
	 * @param unknown_type $pConfirm
	 * @throws Exception
	 * @return Ambigous <multitype:NULL , multitype:number multitype: Ambigous <unknown, mixed> >
	 */
	function SECancelConfirmReviewerInvitation($pDocumentId, $pInvitationId, $pSEId, $pInvitedReviewerId = null, $pConfirm = true){
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		try{

			if(!$pInvitedReviewerId) {
				$pInvitedReviewerId = 'null';
			}

			$lCon = $this->m_con;
			//The checks whether the current user is journal manager or the specified se is SE of the journal of the document are performed in the SP
// 			var_dump($pConfirm);
// 			$pConfirm = false;
			$lSql = 'SELECT * FROM spSECancelConfirmReviewerInvitation(
				' . (int)$pConfirm . ',
				' . (int)$pInvitationId . ',
				' . (int)$pSEId . ',
				' . $pInvitedReviewerId . ',
				' . (int)$pDocumentId . '
			);';
 			//var_dump($lSql);
			//exit;

			if(!$lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			} else {
				$lResult['event_id'] = $lCon->mRs['event_id'];
				$lResult['event_id_sec'] = $lCon->mRs['event_id_sec'];
			}

			$lResult['success_msg'] = getstr('pjs.actionSuccessfullyPerformed');
		}catch(Exception $pException){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $pException->getMessage());
		}
		return $lResult;
	}

	/**
	 * Confirms/cancels the reviewer invitation (self confirm/cancel is performed)
	 * @param unknown_type $pInvitationId
	 * @param unknown_type $pUid
	 * @param unknown_type $pConfirm
	 * @throws Exception
	 */
	function CancelConfirmReviewerInvitation($pDocumentId, $pInvitationId, $pUid, $pConfirm = true){
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		try{

			$lCon = $this->m_con;
			//The checks whether the current user is journal manager or the specified se is SE of the journal of the document are performed in the SP
			// 			var_dump($pConfirm);
			// 			$pConfirm = false;
			$lSql = 'SELECT * FROM spCancelConfirmReviewerInvitation(' . (int)$pConfirm . ', ' . (int)$pInvitationId . ', ' . (int)$pUid . ', ' . (int)$pDocumentId . ');';
			// 			var_dump($lSql);

			if(!$lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			} else {
				$lResult['event_id'] = $lCon->mRs['event_id'];
				$lResult['event_id_sec'] = $lCon->mRs['event_id_sec'];
			}
			$lResult['success_msg'] = getstr('pjs.actionSuccessfullyPerformed');
		}catch(Exception $pException){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $pException->getMessage());
		}
		return $lResult;
	}
	
	/**
	 * Confirms/cancels the reviewer invitation (self confirm/cancel is performed)
	 * @param unknown_type $pInvitationId
	 * @param unknown_type $pUid
	 * @param unknown_type $pConfirm
	 * @throws Exception
	 */
	function AutomaticallyCancelReviewerInvitation($pDocumentId, $pInvitationId){
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		try{

			$lCon = $this->m_con;
			//The checks whether the current user is journal manager or the specified se is SE of the journal of the document are performed in the SP
			// 			var_dump($pConfirm);
			// 			$pConfirm = false;
			$lSql = 'SELECT * FROM pjs."spAutomaticallyCancelReviewerInvitation"(' . (int)$pInvitationId . ', ' . (int)$pDocumentId . ');';
			// 			var_dump($lSql);

			if(!$lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			} else {
				$lResult['event_id'] = $lCon->mRs['event_id'];
				$lResult['event_id_sec'] = $lCon->mRs['event_id_sec'];
			}
			$lResult['success_msg'] = getstr('pjs.actionSuccessfullyPerformed');
		}catch(Exception $pException){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $pException->getMessage());
		}
		return $lResult;
	}

	function UndiscloseRoundUserVersionIfNecessary($pRoundUserId){
		$lCon = $this->m_con;
		$lSql = 'SELECT * FROM pjs.spUndiscloseRoundUserVersionIfNecessary(' . (int)$pRoundUserId . ');';
		$lRes = $lCon->Execute($lSql);
// 		var_dump($this->m_con->GetLastError());
// 		var_dump($lRes);
		return $lRes;
	}

	function InsertDecisionComments($pRoundUserId){
		$lCon = $this->m_con;
		$lSql = 'SELECT * FROM pjs.spInsertDecisionComments(' . (int)$pRoundUserId . ');';
		$lRes = $lCon->Execute($lSql);
// 		var_dump($this->m_con->GetLastError());
// 		var_dump($lRes);
		return $lRes;
	}

	/**
	 * Saves the reviewer decision to the db
	 * @param unknown_type $pRoundUserId
	 * @param unknown_type $pDecisionId
	 * @param unknown_type $pDecisionNotes
	 * @param unknown_type $pUid
	 * @throws Exception
	 * @return Ambigous <multitype:NULL , multitype:number multitype: Ambigous <unknown, mixed> >
	 */
	function SaveReviewerDecision($pDocumentId, $pRoundUserId, $pDecisionId, $pDecisionNotes, $pUid){
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		$lCon = $this->m_con;
		try{
			//The checks whether the current user is journal manager or the specified se is SE of the journal of the document are performed in the SP
			// 			var_dump($pConfirm);
			// 			$pConfirm = false;
			$lSql = 'BEGIN;';
			if(!$lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			}
			if(!$this->InsertDecisionComments($pRoundUserId)){
				throw new Exception(getstr('pjs.couldNotCreateDecisionComments'));
			}
			$lSql = 'BEGIN; SELECT * FROM spSaveReviewerDecision(' . (int)$pRoundUserId . ', ' . (int)$pDecisionId . ', \'' . q($pDecisionNotes) . '\', ' . (int)$pUid . ', ' . (int)$pDocumentId . ');';
			 			//~ var_dump($lSql);
						//~ exit;

			if(!$lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			} else {
				if((int)$lCon->mRs['event_id_sec']) {
					$lResult['data']['event_id'][] = (int)$lCon->mRs['event_id_sec'];
				}
				$lResult['data']['event_id'][] = (int)$lCon->mRs['event_id'];				
				if(!$this->UndiscloseRoundUserVersionIfNecessary($pRoundUserId)){
					throw new Exception(getstr('pjs.couldNotUndiscloseUser'));
				}
				if(!$lCon->Execute('COMMIT;')){
					throw new Exception(getstr('pjs.couldNotCommitTransaction'));
				}

			}
			$lResult['success_msg'] = getstr('pjs.actionSuccessfullyPerformed');
		}catch(Exception $pException){
			$lCon->Execute('ROLLBACK;');
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $pException->getMessage());
		}
// 		var_dump($lResult);
		return $lResult;
	}

	/**
	 * Saves the SE decision to the db
	 * @param unknown_type $pRoundUserId
	 * @param unknown_type $pDecisionId
	 * @param unknown_type $pDecisionNotes
	 * @param unknown_type $pUid
	 * @throws Exception
	 * @return Ambigous <multitype:NULL , multitype:number multitype: Ambigous <unknown, mixed> >
	 */
	function SaveSEDecision($pRoundUserId, $pDecisionId, $pDecisionNotes, $pUid, $pDocumentId){

		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		$lCon = $this->m_con;
		try{
			//The checks whether the current user is journal manager or the specified se is SE of the journal of the document are performed in the SP
			// 			var_dump($pConfirm);
			// 			$pConfirm = false;
			if(!$lCon->Execute('BEGIN;')){
				throw new Exception(getstr('pjs.couldNotBeginTransaction'));
			}
			if(!$this->InsertDecisionComments($pRoundUserId)){
				throw new Exception(getstr('pjs.couldNotCreateDecisionComments'));
			}
			$lSql = 'SELECT * FROM spSaveSEDecision(' . (int)$pRoundUserId . ', ' . (int)$pDecisionId . ', \'' . q($pDecisionNotes) . '\', ' . (int)$pUid . ', ' . (int)$pDocumentId . ');';
 						//var_dump($lSql);

			if(!$lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			} else {
				$lResult['event_id'] = $lCon->mRs['event_id'];
				
				if(!$lCon->Execute('COMMIT;')){
					throw new Exception(getstr('pjs.couldNotCommitTransaction'));
				}
			}
			$lResult['success_msg'] = getstr('pjs.actionSuccessfullyPerformed');
		}catch(Exception $pException){
			$lCon->Execute('ROLLBACK;');
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $pException->getMessage());
		}
		return $lResult;
	}

	function SaveEditorDecision($pRoundUserId, $pDecisionId, $pDecisionNotes, $pUid, $pDocumentId){
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		try{
			$lCon = $this->m_con;
			//The checks whether the current user is journal manager or the specified se is SE of the journal of the document are performed in the SP
			// 			var_dump($pConfirm);
			// 			$pConfirm = false;

			if($pDecisionId == ROUND_DECISION_REJECT) {
				$lDocumentState = DOCUMENT_REJECTED_STATE;
			} elseif ($pDecisionId == ROUND_DECISION_REJECT_BUT_RESUBMISSION) {
				$lDocumentState = DOCUMENT_REJECTED_BUT_RESUBMISSION;
			}

			$lSql = 'SELECT * FROM spSaveEditorRejectedDecision(' . (int)$pDecisionId . ', \'' . q($pDecisionNotes) . '\', ' . (int)$pUid . ', ' . (int)$lDocumentState . ', ' . (int)$pDocumentId . ');';
 						//var_dump($lSql);
						//exit;

			if(!$lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			} else {
				$lResult['event_id'] = $lCon->mRs['event_id'];
			}
			$lResult['success_msg'] = getstr('pjs.actionSuccessfullyPerformed');
		}catch(Exception $pException){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $pException->getMessage());
		}
		return $lResult;
	}

	function SaveSERejectDecision($pRoundUserId, $pDecisionId, $pDecisionNotes, $pUid, $pDocumentId){
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		try{
			$lCon = $this->m_con;
			//The checks whether the current user is journal manager or the specified se is SE of the journal of the document are performed in the SP
			// 			var_dump($pConfirm);
			// 			$pConfirm = false;

			if($pDecisionId == ROUND_DECISION_REJECT) {
				$lDocumentState = DOCUMENT_REJECTED_STATE;
			} elseif ($pDecisionId == ROUND_DECISION_REJECT_BUT_RESUBMISSION) {
				$lDocumentState = DOCUMENT_REJECTED_BUT_RESUBMISSION;
			}

			$lSql = 'SELECT * FROM spSaveSERejectedDecision(' . (int)$pDecisionId . ', \'' . q($pDecisionNotes) . '\', ' . (int)$pUid . ', ' . (int)$lDocumentState . ', ' . (int)$pDocumentId . ');';
 						//var_dump($lSql);
						//exit;

			if(!$lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			} else {
				$lResult['event_id'] = (int)$lCon->mRs['event_id'];
			}
			$lResult['success_msg'] = getstr('pjs.actionSuccessfullyPerformed');
		}catch(Exception $pException){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $pException->getMessage());
		}
		return $lResult;
	}

	/**
	 * Saves the LE decision to the db
	 * @param unknown_type $pRoundUserId
	 * @param unknown_type $pDecisionId
	 * @param unknown_type $pDecisionNotes
	 * @param unknown_type $pUid
	 * @throws Exception
	 * @return Ambigous <multitype:NULL , multitype:number multitype: Ambigous <unknown, mixed> >
	 */
	function SaveLEDecision($pRoundUserId, $pDecisionId, $pDecisionNotes, $pUid){
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		try{
			$lCon = $this->m_con;
			//The checks whether the current user is journal manager or the specified se is SE of the journal of the document are performed in the SP
			// 			var_dump($pConfirm);
			// 			$pConfirm = false;
			$lSql = 'SELECT * FROM spSaveLEDecision(' . (int)$pRoundUserId . ', ' . (int)$pDecisionId . ', \'' . q($pDecisionNotes) . '\', ' . (int)$pUid . ');';
			// 						var_dump($lSql);

			if(!$lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			} else {
				$lResult['event_id'] = $lCon->mRs['event_id'];
			}
			$lResult['success_msg'] = getstr('pjs.actionSuccessfullyPerformed');
		}catch(Exception $pException){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $pException->getMessage());
		}
		return $lResult;
	}

	/**
	 * Saves the CE decision to the db
	 * @param unknown_type $pRoundUserId
	 * @param unknown_type $pDecisionId
	 * @param unknown_type $pDecisionNotes
	 * @param unknown_type $pUid
	 * @throws Exception
	 * @return Ambigous <multitype:NULL , multitype:number multitype: Ambigous <unknown, mixed> >
	 */
	function SaveCEDecision($pRoundUserId, $pDecisionId, $pDecisionNotes, $pUid){
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		try{
			$lCon = $this->m_con;
			//The checks whether the current user is journal manager or the specified se is SE of the journal of the document are performed in the SP
			// 			var_dump($pConfirm);
			// 			$pConfirm = false;
			$lSql = 'SELECT * FROM spSaveCEDecision(' . (int)$pRoundUserId . ', ' . (int)$pDecisionId . ', \'' . q($pDecisionNotes) . '\', ' . (int)$pUid . ');';
// 									var_dump($lSql);

			if(!$lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			} else {
				$lResult['event_id'] = (int)$lCon->mRs['event_id'];
			}
			$lResult['success_msg'] = getstr('pjs.actionSuccessfullyPerformed');
		}catch(Exception $pException){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $pException->getMessage());
		}
		return $lResult;
	}

	/**
	 * Saves the author layout decision to the db
	 * @param unknown_type $pDocumentId
	 * @param unknown_type $pDecisionId
	 * @param unknown_type $pUid
	 * @throws Exception
	 * @return Ambigous <multitype:NULL , multitype:number multitype: Ambigous <unknown, mixed> >
	 */
	function SaveAuthorLayoutDecision($pDocumentId, $pDecisionId, $pUid){
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		try{
			$lCon = $this->m_con;
			//The checks whether the current user is journal manager or the specified se is SE of the journal of the document are performed in the SP
			// 			var_dump($pConfirm);
			// 			$pConfirm = false;
			$lSql = 'SELECT * FROM spSaveAuthorLayoutDecision(' . (int)$pDocumentId . ', ' . (int)$pDecisionId . ', ' . (int)$pUid . ');';
// 									var_dump($lSql);

			if(!$lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			}
			$lResult['success_msg'] = getstr('pjs.actionSuccessfullyPerformed');
		}catch(Exception $pException){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $pException->getMessage());
		}
		return $lResult;
	}

	/**
	 * Checks if a SE can take a decision for the current review round of the document
	 * @param int $pDocumentId
	 */
	function CheckIfSECanTakeDecision($pDocumentId){
		$lSql = 'SELECT result FROM pjs.spCheckIfSECanTakeADecision(' . (int)$pDocumentId . ')';
		if(!$this->m_con->Execute($lSql) || !(int)$this->m_con->mRs['result']){
			return false;
		}
		return true;
	}

	/**
	 * Returns the id from table pjs.document_review_round_users which corresponds to the role of SE for the
	 * specified user for the current round of the specified document.
	 * Returns 0 if the user is not a SE for the specified document, or the document is not in a review round
	 * @param int $pDocumentId
	 * @param int $pUid
	 */
	function GetSEDocumentCurrentRoundNumberAndUserId($pDocumentId, $pUid){
		$lSql = 'SELECT r.id, drr.round_number, drr.round_due_date, r.due_date as user_due_date, drr.id as current_round_id
			FROM pjs.documents d
			JOIN pjs.document_users du ON du.document_id = d.id AND du.role_id = ' . (int) SE_ROLE . '
			JOIN pjs.document_review_rounds drr ON drr.id = d.current_round_id
			JOIN pjs.document_review_round_users r ON r.round_id = d.current_round_id AND r.document_user_id = du.id
			WHERE d.id = ' . (int) $pDocumentId . ' AND d.state_id = ' . (int) DOCUMENT_IN_REVIEW_STATE . '
			LIMIT 1
		';
		$lRes = array();

		$this->m_con->Execute($lSql);
		$lRes['id'] = (int)$this->m_con->mRs['id'];
		$lRes['round_number'] = (int)$this->m_con->mRs['round_number'];
		$lRes['round_due_date'] = $this->m_con->mRs['round_due_date'];
		$lRes['current_round_id'] = $this->m_con->mRs['current_round_id'];
		$lRes['user_due_date'] = $this->m_con->mRs['user_due_date'];

// 		var_dump($this->m_con->mRs['id']);
		return $lRes;
	}

	function GetDocumentCurrentReviewRoundId($pDocumentId){
		$lSql = '
			SELECT drr.id as round_id
			FROM pjs.documents d
			JOIN pjs.document_review_rounds drr ON drr.id = d.current_round_id
			WHERE d.state_id IN (' . DOCUMENT_INCOMPLETE_STATE . ', ' . DOCUMENT_IN_REVIEW_STATE . ')
			AND (
				CASE WHEN d.state_id = ' . DOCUMENT_INCOMPLETE_STATE . '
					THEN drr.round_type_id = ' . E_ROUND_TYPE . '
					ELSE drr.round_type_id = ' . R_ROUND_TYPE . '
				END
			)
			AND d.id = ' . (int)$pDocumentId . '
			ORDER BY drr.id DESC
			LIMIT 1
		';

		$lRes = array();
		$this->m_con->Execute($lSql);
		$lRes['round_id'] = (int)$this->m_con->mRs['round_id'];

		return $lRes;
	}

	function SubmitAuthorVersionForReview($pDocumentId, $pUid){
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		try{
			$lCon = $this->m_con;
			//The checks whether the current user is journal manager or the specified se is SE of the journal of the document are performed in the SP
			// 			var_dump($pConfirm);
			// 			$pConfirm = false;
			$lSql = 'SELECT * FROM spSubmitAuthorVersionForReview(' . (int)$pDocumentId . ', ' . (int)$pUid . ');';
// 									var_dump($lSql);

			if(!$lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			} else {
				$lResult['event_id'] = $lCon->mRs['event_id'];
			}
			$lResult['success_msg'] = getstr('pjs.actionSuccessfullyPerformed');
		}catch(Exception $pException){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $pException->getMessage());
		}
		return $lResult;
	}

	function ProceedDocumentToLayoutEditing($pDocumentId, $pUid){
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		try{
			$lCon = $this->m_con;
			//The checks whether the current user is journal manager or the specified se is SE of the journal of the document are performed in the SP
			// 			var_dump($pConfirm);
			// 			$pConfirm = false;
			$lSql = 'SELECT * FROM spProceedDocumentToLayoutEditing(' . (int)$pDocumentId . ', ' . (int)$pUid . ');';
// 									var_dump($lSql);

			if(!$lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			} else {
				$lResult['event_id'] = $lCon->mRs['event_id'];
			}
			$lResult['success_msg'] = getstr('pjs.actionSuccessfullyPerformed');
		}catch(Exception $pException){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $pException->getMessage());
		}
		return $lResult;
	}

	function ProceedDocumentToCopyEditing($pDocumentId, $pUid){
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		try{
			$lCon = $this->m_con;
			$lSql = 'SELECT * FROM spProceedDocumentToCopyEditing(' . (int)$pDocumentId . ', ' . (int)$pUid . ');';

			if(!$lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			} else {
				$lResult['event_id'] = $lCon->mRs['event_id'];
			}
			$lResult['success_msg'] = getstr('pjs.actionSuccessfullyPerformed');
		}catch(Exception $pException){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $pException->getMessage());
		}
		return $lResult;
	}

	function EditorProceedDocumentToLayoutEditing($pDocumentId, $pUid){
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		try{
			$lCon = $this->m_con;
			//The checks whether the current user is journal manager or the specified se is SE of the journal of the document are performed in the SP
			// 			var_dump($pConfirm);
			// 			$pConfirm = false;
			$lSql = 'SELECT * FROM spEditorProceedDocumentToLayoutEditing(' . (int)$pDocumentId . ', ' . (int)$pUid . ');';
			// 									var_dump($lSql);

			if(!$lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			}
			$lResult['success_msg'] = getstr('pjs.actionSuccessfullyPerformed');
		}catch(Exception $pException){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $pException->getMessage());
		}
		return $lResult;
	}

	function SaveEditorNotes($pDocumentId, $pUid, $pNote, $pMode = 0){
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		try{
			$lCon = $this->m_con;

			if($pMode == 0) {
				$lNoteFld = 'editor_notes';
			} else {
				$lNoteFld = 'layout_notes';
			}

			$lSql = 'UPDATE pjs.documents SET ' . $lNoteFld . ' = \'' . q($pNote) . '\' WHERE id = ' . $pDocumentId;
			//var_dump($lSql);
			//exit;

			if(!$lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			}
			$lResult['success_msg'] = getstr('pjs.actionSuccessfullyPerformed');
		}catch(Exception $pException){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $pException->getMessage());
		}
		return $lResult;
	}

	function GetDocumentReviewRounds($pDocumentId, $pRemoveRejectRounds = 0, $pRoundTypes = array(R_ROUND_TYPE)){
		$lResult = array();

		$lSql = '
			SELECT
				drr.*,
				drrd.name as decision_name,
				drrt.name as round_name,
				dv.version_num,
				drr.create_from_version_id as author_version_id,
				drru.document_version_id as se_version_id
			FROM pjs.document_review_rounds drr
			JOIN pjs.document_review_round_users drru ON drru.id = drr.decision_round_user_id
			JOIN pjs.document_versions dv ON dv.id = drru.document_version_id
			JOIN pjs.document_review_round_types drrt ON drrt.id = drr.round_type_id
			JOIN pjs.document_review_round_decisions drrd ON drrd.id = drr.decision_id
			WHERE drr.decision_id IS NOT NULL
				AND drr.document_id = ' . $pDocumentId . '
				AND drr.round_type_id IN (' . implode(',', $pRoundTypes) . ')
				' . ($pRemoveRejectRounds ?
					' AND (CASE WHEN drr.round_type_id = ' . R_ROUND_TYPE . ' THEN drr.decision_id NOT IN (' . ROUND_DECISION_REJECT . ', ' . ROUND_DECISION_REJECT_BUT_RESUBMISSION . ') ELSE TRUE END)'
						:
					'');

 		//var_dump($lSql);
		$this->m_con->Execute($lSql);
		while(!$this->m_con->Eof()){
			$lResult[] = $this->m_con->mRs;
			$this->m_con->MoveNext();
		}

		return $lResult;
	}

	function GetAssignedDedicatedReviewersListByRound($pDocumentId, $pRoundId){

		$lSql = 'SELECT
			u.*,
			ist.name as invitation_state_name,
			i.date_invited,
			i.date_confirmed,
			i.date_canceled,
			i.due_date,
			ist.id as invitation_state,
			i.id as invitation_id,
			de.name as decision_name,
			rd.decision_id,
			rd.due_date as review_usr_due_date,
			rd.id as reviwer_id,
			i.round_id as round_id,
			rd.document_version_id as reviwer_document_version_id,
			d.id as document_id
		FROM pjs.document_user_invitations i
		JOIN usr u ON u.id = i.uid
		JOIN pjs.document_user_invitation_states ist ON ist.id = i.state_id
		JOIN pjs.documents d ON d.id = i.document_id

		LEFT JOIN pjs.document_users dus ON dus.document_id = d.id AND dus.uid = i.uid AND dus.role_id = i.role_id
		LEFT JOIN pjs.document_review_round_users rd ON rd.round_id = i.round_id AND rd.document_user_id = dus.id
		LEFT JOIN pjs.document_review_round_user_states drrus ON drrus.id = rd.state_id
		LEFT JOIN pjs.document_review_round_decisions de ON de.id = rd.decision_id

		WHERE i.document_id = ' . (int)$pDocumentId . ' AND i.role_id = ' . (int) DEDICATED_REVIEWER_ROLE . ' AND i.round_id = ' . (int)$pRoundId . '
		';

 		//var_dump($lSql);
		//echo '<br><br>';

		$lResult = array();
		$this->m_con->Execute($lSql);

		$lProceedWithSEDecision = true;
		while(!$this->m_con->Eof()){
			$lResult[] = $this->m_con->mRs;
			$this->m_con->MoveNext();
		}

		return $lResult;
	}

	function GetReviewerData($pDocumentId, $pUserId, $pLastRound = 0){
		$lResult = array();

		$lSql = '
			SELECT
				drrt.name as round_name,
				drr.round_number,
				urt.name as usr_role_name,
				rd.document_version_id as user_version_id,
				dv.version_num as author_version_num,
				dv.id as author_version_id,
				urt.id as view_role,
				rd.id as round_user_id,
				rd.decision_id,
				i.state_id as invitation_state_id
			FROM pjs.document_user_invitations i
			JOIN pjs.document_review_rounds drr ON drr.id = i.round_id
			JOIN pjs.document_review_round_types drrt ON drrt.id = drr.round_type_id
			JOIN pjs.document_versions dv ON dv.id = drr.create_from_version_id AND dv.version_type_id = 1
			JOIN usr u ON u.id = i.uid
			JOIN pjs.user_role_types urt ON urt.id = i.role_id
			JOIN pjs.document_user_invitation_states ist ON ist.id = i.state_id
			JOIN pjs.documents d ON d.id = i.document_id

			LEFT JOIN pjs.document_users dus ON dus.document_id = d.id AND dus.uid = i.uid AND dus.role_id = i.role_id
			LEFT JOIN pjs.document_review_round_users rd ON rd.round_id = i.round_id AND rd.document_user_id = dus.id
			LEFT JOIN pjs.document_review_round_user_states drrus ON drrus.id = rd.state_id
			LEFT JOIN pjs.document_review_round_decisions de ON de.id = rd.decision_id

			WHERE i.document_id = ' . $pDocumentId . '
				AND i.role_id IN (' . DEDICATED_REVIEWER_ROLE . ', ' . COMMUNITY_REVIEWER_ROLE . ', ' . PUBLIC_REVIEWER_ROLE . ')
				AND i.uid = ' . $pUserId . '
				AND drrt.id = 1
				' . ($pLastRound ? '' : 'AND drr.decision_id IS NOT NULL') . '
			ORDER BY drr.round_number' . ($pLastRound ? ' DESC LIMIT 1' : ' ASC');

		$this->m_con->Execute($lSql);
		while(!$this->m_con->Eof()){
			$lResult[] = $this->m_con->mRs;
			$this->m_con->MoveNext();
		}

		return $lResult;
	}

	function CheckRoundConditions($pDocument) {

		$lSql = 'spCheckRoundConditions(' . (int)$pDocument . ', )';

		$lResult = array();
		$this->m_con->Execute($lSql);

		$lProceedWithSEDecision = true;
		while(!$this->m_con->Eof()){
			$lResult[] = $this->m_con->mRs;
			$this->m_con->MoveNext();
		}

		return $lResult;
	}

	function GetDocumentAuthorsList($pDocumentId) {
		$lSql = '
			SELECT
				u.first_name,
				u.last_name,
				u.affiliation,
				u.addr_city,
				u.uname,
				du.co_author,
				c.name as country,
				(CASE WHEN (d.id IS NOT NULL) THEN d.submitting_author_id ELSE NULL END) as submitting_author
			FROM pjs.document_users du
			JOIN usr u ON u.id = du.uid
			LEFT JOIN pjs.documents d ON d.submitting_author_id = du.uid AND d.id = ' . (int)$pDocumentId . '
			LEFT JOIN countries c ON c.id = u.country_id
			WHERE du.document_id = ' . (int)$pDocumentId . ' AND du.role_id = ' . (int)AUTHOR_ROLE;

		$lResult = array();
		$this->m_con->Execute($lSql);

		$lProceedWithSEDecision = true;
		while(!$this->m_con->Eof()){
			$lResult[] = $this->m_con->mRs;
			$this->m_con->MoveNext();
		}

		return $lResult;
	}

	function GetDocumentAbstractAndKeyWords($pDocumentId) {
		$lSql = '
			SELECT
				abstract,
				keywords
			FROM pjs.documents
			WHERE id = ' . (int)$pDocumentId;

		$lResult = array();
		$this->m_con->Execute($lSql);


		$lResult['abstract'] = $this->m_con->mRs['abstract'];
		$lResult['keywords'] = $this->m_con->mRs['keywords'];

		return $lResult;
	}

	function GetDocumentIndexedTerms($pDocumentId) {
		$lSql = '
		SELECT
			aggr_concat_coma(DISTINCT s.name) as subject_categories,
			aggr_concat_coma(DISTINCT t.name) as taxon_categories,
			aggr_concat_coma(DISTINCT c.name) as chronological_categories,
			aggr_concat_coma(DISTINCT g.name) as geographical_categories,
			aggr_concat_coma(DISTINCT sa.title) as agencies,
			supporting_agencies_texts as customagencies
		FROM pjs.documents d
		LEFT JOIN subject_categories s ON s.id = ANY(d.subject_categories)
		LEFT JOIN taxon_categories t ON t.id = ANY(d.taxon_categories)
		LEFT JOIN chronological_categories c ON c.id = ANY(d.chronological_categories)
		LEFT JOIN geographical_categories g ON g.id = ANY(d.geographical_categories)
		LEFT JOIN supporting_agencies sa ON sa.id = ANY(d.supporting_agencies_ids)
		WHERE d.id = ' . $pDocumentId . '
		GROUP BY d.id';
		//~ echo $lSql;
		$lResult = array();
		$this->m_con->Execute($lSql);

		$lResult['subject_categories'] = $this->m_con->mRs['subject_categories'];
		$lResult['taxon_categories'] = $this->m_con->mRs['taxon_categories'];
		$lResult['chronological_categories'] = $this->m_con->mRs['chronological_categories'];
		$lResult['geographical_categories'] = $this->m_con->mRs['geographical_categories'];
		$lResult['agencies'] = $this->m_con->mRs['agencies'];
		$lResult['customagencies'] = $this->m_con->mRs['customagencies'];

		return $lResult;
	}

	/**
	 * This function returns metadata about the specified document for submitting purposes
	 * @param bigint $pDocumentId
	 */
	function GetSubmittingDocumentInfo($pDocumentId){
		$lCon = $this->m_con;
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
		);


		$lSql = 'SELECT * FROM pjs."spGetSubmittingDocumentInfo"(' . (int)$pDocumentId . ')';

		// 		var_dump($lSql);
		if(!$lCon->Execute($lSql)){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $lCon->GetLastError());
		}else{
			$lResult += $lCon->mRs;
		}
		//~ print_r($lResult);
		return $lResult;
	}

	/**
	 * This function invites reviewer for document by author
	 * @param bigint $pDocumentId
	 */
	function InviteDocumentReviewerByAuthor($pOper, $pInvitationId = null, $pDocumentId, $pReviewerId, $pUid, $pRoundId, $pAddedByType){
		$lCon = $this->m_con;
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
		);

		$lSql = 'SELECT * FROM pjs.spDocumentInviteReviewerByAuthor(' . (int)$pOper . ', ' . (int)$pInvitationId . ', ' . (int)$pDocumentId . ', ' . (int)$pReviewerId . ', ' . (int)$pUid . ', ' . (int)$pRoundId . ', ' . (int)$pAddedByType . ')';

		//~ var_dump($lSql);
		if(!$lCon->Execute($lSql)){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $lCon->GetLastError());
		}else{
			$lResult += $lCon->mRs;
		}
		//~ print_r($lResult);
		return $lResult;
	}

	/**
	 * This function returns metadata about the specified document (i.e author_id, document_source_id, creation_step etc.)
	 * @param bigint $pDocumentId
	 */
	function GetDocumentInfoForReviewer($pDocumentId){
		$lCon = $this->m_con;
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
		);


		$lSql = 'SELECT * FROM pjs.spGetDocumentInfoForReviewer(' . (int)$pDocumentId . ')';

		if(!$lCon->Execute($lSql)){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $lCon->GetLastError());
		}else{
			$lResult += $lCon->mRs;
		}

		return $lResult;
	}

	function GetAssignedReviewersListByDocument($pDocumentId){

		$lSql = 'SELECT
			u.uname as email,
			u.id as user_id,
			(u.first_name || \' \' || u.last_name) as author_name,
			ist.name as invitation_state_name,
			i.date_invited,
			i.date_confirmed,
			i.date_canceled,
			i.due_date,
			ist.id as invitation_state,
			i.id as invitation_id,
			de.name as decision_name,
			rd.decision_id,
			rd.due_date as review_usr_due_date,
			rd.id as reviwer_id,
			i.round_id as round_id
		FROM pjs.document_user_invitations i
		JOIN usr u ON u.id = i.uid
		JOIN pjs.document_user_invitation_states ist ON ist.id = i.state_id
		JOIN pjs.documents d ON d.id = i.document_id

		LEFT JOIN pjs.document_users dus ON dus.document_id = d.id AND dus.uid = i.uid AND dus.role_id = i.role_id
		LEFT JOIN pjs.document_review_round_users rd ON rd.round_id = i.round_id AND rd.document_user_id = dus.id
		LEFT JOIN pjs.document_review_round_user_states drrus ON drrus.id = rd.state_id
		LEFT JOIN pjs.document_review_round_decisions de ON de.id = rd.decision_id

		WHERE i.document_id = ' . (int)$pDocumentId;

 		//var_dump($lSql);
		//echo '<br><br>';

		$lResult = array();
		$this->m_con->SetFetchReturnType(PGSQL_ASSOC);
		$this->m_con->Execute($lSql);

		$lProceedWithSEDecision = true;
		while(!$this->m_con->Eof()){
			$lResult[] = $this->m_con->mRs;
			$this->m_con->MoveNext();
		}

		return $lResult;
	}

	function GetDocumentSubmittedFilesList($pDocumentId) {
		$lSql = 'SELECT * FROM pjs.document_media WHERE document_id = ' . (int)$pDocumentId;

		$lResult = array();
		$this->m_con->Execute($lSql);

		$lProceedWithSEDecision = true;
		while(!$this->m_con->Eof()){
			$lResult[] = $this->m_con->mRs;
			$this->m_con->MoveNext();
		}

		return $lResult;
	}

	function GetSEDecision($pDocumentId) {
			$lSql = 'SELECT drd.name as decision, dv.version_num, dv.id as se_version_id
			FROM pjs.document_review_rounds dr
			JOIN pjs.document_review_round_users drru ON drru.id = dr.decision_round_user_id
			JOIN pjs.document_versions dv ON dv.id = drru.document_version_id
			JOIN pjs.document_review_round_decisions drd ON drd.id = dr.decision_id
			WHERE dr.document_id = ' . (int)$pDocumentId . ' AND dr.round_type_id = ' . (int)R_ROUND_TYPE . '
			ORDER BY dr.round_number DESC
			LIMIT 1';

		$lResult = array();
		$this->m_con->Execute($lSql);
		$lResult['decision'] = $this->m_con->mRs['decision'];
		$lResult['version_num'] = $this->m_con->mRs['version_num'];
		$lResult['se_version_id'] = $this->m_con->mRs['se_version_id'];

		return $lResult;
	}

	function GetAuthorRoundDetails($pDocumentId, $pRoundType) {
		$lSql = 'SELECT u.first_name, u.last_name, dr.round_due_date, drru.id as user_round_id
			FROM pjs.document_review_rounds dr
			JOIN pjs.document_review_round_users drru ON drru.id = dr.decision_round_user_id
			JOIN pjs.document_users du ON du.id = drru.document_user_id
			JOIN usr u ON u.id = du.uid
			WHERE dr.document_id = ' . (int)$pDocumentId . ' AND dr.round_type_id = ' . (int)$pRoundType . ' AND dr.decision_id IS NULL
			LIMIT 1';

		$lResult = array();
		$this->m_con->Execute($lSql);
		$lResult['first_name'] = $this->m_con->mRs['first_name'];
		$lResult['last_name'] = $this->m_con->mRs['last_name'];
		$lResult['round_due_date'] = $this->m_con->mRs['round_due_date'];
		$lResult['user_round_id'] = $this->m_con->mRs['user_round_id'];
		return $lResult;
	}

	function GetCEData($pDocumentId) {
		$lSql = 'SELECT u.first_name, u.last_name, dv.version_num
			FROM pjs.document_review_rounds dr
			JOIN pjs.document_review_round_users drru ON drru.id = dr.decision_round_user_id
			JOIN pjs.document_versions dv ON dv.id = drru.document_version_id
			JOIN pjs.document_users du ON du.id = drru.document_user_id
			JOIN usr u ON u.id = du.uid
			WHERE dr.document_id = ' . (int)$pDocumentId . ' AND dr.round_type_id = ' . CE_ROUND_TYPE . ' AND dr.decision_id IS NOT NULL
			LIMIT 1';

		$lResult = array();
		$this->m_con->Execute($lSql);
		if((int)$this->m_con->RecordCount()) {
			$lResult['first_name'] = $this->m_con->mRs['first_name'];
			$lResult['last_name'] = $this->m_con->mRs['last_name'];
			$lResult['version_num'] = $this->m_con->mRs['version_num'];
		}
		return $lResult;
	}

	function GetCEDataList($pDocumentId, $pUserId = 0) {
		$lSql = '
			SELECT 
				u.first_name, 
				u.last_name, 
				dv.version_num, 
				dv.id as copy_editor_version_id, 
				dr.create_from_version_id as author_round_version_id 
			FROM pjs.document_review_rounds dr
			JOIN pjs.document_review_round_users drru ON drru.id = dr.decision_round_user_id
			JOIN pjs.document_versions dv ON dv.id = drru.document_version_id
			JOIN pjs.document_users du ON du.id = drru.document_user_id
			JOIN usr u ON u.id = du.uid
			WHERE dr.document_id = ' . (int)$pDocumentId . ' 
				AND dr.round_type_id = ' . CE_ROUND_TYPE . ' 
				AND dr.decision_id IS NOT NULL
				' . ((int)$pUserId ? ' AND u.id = ' . (int)$pUserId : '') . '
			ORDER BY dr.id ASC';

		$lResult = array();
		$this->m_con->Execute($lSql);
		while(!$this->m_con->Eof()){
			$lResult[] = $this->m_con->mRs;
			$this->m_con->MoveNext();
		}
		
		return $lResult;
	}

	function GetLEData($pDocumentId) {
		$lSql = 'SELECT u.first_name, u.last_name, dv.version_num
			FROM pjs.document_review_rounds dr
			JOIN pjs.document_review_round_users drru ON drru.id = dr.decision_round_user_id
			JOIN pjs.document_versions dv ON dv.id = drru.document_version_id
			JOIN pjs.document_users du ON du.id = drru.document_user_id
			JOIN usr u ON u.id = du.uid
			WHERE dr.document_id = ' . (int)$pDocumentId . ' AND dr.round_type_id = ' . LE_ROUND_TYPE . ' AND dr.decision_id IS NOT NULL
			LIMIT 1';

		$lResult = array();
		$this->m_con->Execute($lSql);
		if((int)$this->m_con->RecordCount()) {
			$lResult['first_name'] = $this->m_con->mRs['first_name'];
			$lResult['last_name'] = $this->m_con->mRs['last_name'];
			$lResult['version_num'] = $this->m_con->mRs['version_num'];
		}
		return $lResult;
	}

	function GetLastReviewRoundDecisionData($pDocumentId) {
		$lSql = 'SELECT dr.round_number, drru.decision_date
			FROM pjs.document_review_rounds dr
			JOIN pjs.document_review_round_users drru ON drru.id = dr.decision_round_user_id
			WHERE dr.document_id = ' . (int)$pDocumentId . ' AND dr.round_type_id = ' . R_ROUND_TYPE . ' AND dr.decision_id = 1
			LIMIT 1';

		$lResult = array();
		$this->m_con->Execute($lSql);
		if((int)$this->m_con->RecordCount()) {
			$lResult['round_number'] = $this->m_con->mRs['round_number'];
			$lResult['decision_date'] = $this->m_con->mRs['decision_date'];
		}
		return $lResult;
	}

	function GetReviewerDocumentVersion($pDocumentId, $pUid, $pRoundNumber){
		$lCon = $this->m_con;
		$lSql = '
			SELECT
				drru.document_version_id as document_version_id,
				drr.id as round_id
			FROM pjs.document_users du
			JOIN pjs.document_review_round_users drru ON drru.document_user_id = du.id
			JOIN pjs.document_review_rounds drr ON drr.id = drru.round_id AND round_type_id = ' . R_ROUND_TYPE . '
			WHERE du.uid = ' . $pUid . '
				AND du.role_id IN (' . DEDICATED_REVIEWER_ROLE . ', ' . PUBLIC_REVIEWER_ROLE . ', ' . COMMUNITY_REVIEWER_ROLE . ')
				AND du.document_id = ' . $pDocumentId . '
			ORDER BY drr.round_number DESC
			LIMIT 1';

		$this->m_con->Execute($lSql);
		if(!$lCon->Execute($lSql)){
			throw new Exception($lCon->GetLastError());
		} else {
			$lResult = $lCon->mRs;
		}
		return $lResult;
	}

	function GetInvitedPanelReviewersList($pDocumentId, $pRoundId, $pInReviewState = 0){

		$lSql = 'SELECT
			u.*
		FROM pjs.document_user_invitations i
		JOIN pjs.document_user_invitation_states ist ON ist.id = i.state_id
		JOIN pjs.documents d ON d.id = i.document_id
		JOIN usr u ON u.id = i.uid
		LEFT JOIN pjs.document_review_rounds drr ON drr.id = d.current_round_id
		LEFT JOIN pjs.document_review_round_types drrt ON drrt.id = drr.round_type_id
		LEFT JOIN pjs.document_users du ON du.uid = u.id AND du.document_id = ' . (int)$pDocumentId . ' AND du.role_id IN ('.PUBLIC_REVIEWER_ROLE.', '.COMMUNITY_REVIEWER_ROLE.')
		LEFT JOIN pjs.document_review_round_users drru ON drru.document_user_id = du.id AND drru.round_id = ' . (int)$pRoundId . '

		WHERE i.document_id = ' . (int)$pDocumentId . ' ' . ($pInReviewState ? ' AND d.state_id = ' . DOCUMENT_IN_REVIEW_STATE : '') . '
			AND i.role_id IN ('.PUBLIC_REVIEWER_ROLE.', '.COMMUNITY_REVIEWER_ROLE.')
			AND i.state_id IN ('.REVIEWER_INVITATION_NEW_STATE . ', ' . REVIEWER_CONFIRMED_STATE . ')
			AND i.round_id = ' . (int)$pRoundId .
			'AND (CASE WHEN drru.decision_id IS NULL THEN TRUE ELSE FALSE END)';

 		//var_dump($lSql);
		$lResult = array();
		$this->m_con->Execute($lSql);

		while(!$this->m_con->Eof()){
			$lResult[] = $this->m_con->mRs;
			$this->m_con->MoveNext();
		}

		return $lResult;
	}


	function GetReviewedPanelPublicReviewersList($pDocumentId, $pRoundId){

		$lSql = 'SELECT
			u.*, dv.id as version_id
		FROM pjs.document_review_round_users drru
		JOIN pjs.document_review_rounds drr ON drr.id = drru.round_id
		JOIN pjs.document_users du ON du.id = drru.document_user_id
		JOIN pjs.document_versions dv ON dv.id = drru.document_version_id
		JOIN usr u ON u.id = du.uid
		WHERE drru.decision_id IS NOT NULL
			AND drr.document_id = ' . $pDocumentId . '
			AND drru.round_id = ' . $pRoundId . '
			AND du.role_id IN ('.PUBLIC_REVIEWER_ROLE.', '.COMMUNITY_REVIEWER_ROLE.')';

 		//var_dump($lSql);
		$lResult = array();
		$this->m_con->Execute($lSql);

		while(!$this->m_con->Eof()){
			$lResult[] = $this->m_con->mRs;
			$this->m_con->MoveNext();
		}

		return $lResult;
	}

	function DisableInvitingUsersForRound($pRoundId, $pDocumentId){
		$lCon = $this->m_con;
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
		);

		$lSql = 'SELECT * FROM pjs."spDisableInvitingUsers"(' . (int)$pRoundId . ', ' . (int)$pDocumentId . ')';

 		//var_dump($lSql);
		if(!$lCon->Execute($lSql)){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $lCon->GetLastError());
		}

		return $lResult;
	}

	function UpdateDocumentDueDates($pOper, $pRoundId, $pRoundUserId, $pDueDate){

		$lSql = 'SELECT * FROM pjs.spmanualupdateduedates(' . $pOper . ', null, ' . $pRoundId . ', ' . $pRoundUserId . ', \'' . $pDueDate . '\')';
		$lResult = array();
		$this->m_con->Execute($lSql);

		while(!$this->m_con->Eof()){
			$lResult[] = $this->m_con->mRs;
			$this->m_con->MoveNext();
		}
		return $lResult;
	}

	function CheckInvitedReviewersForRound($pRoundId, $pDocumentId){
		$lCon = $this->m_con;
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
		);

		$lSql = 'SELECT * FROM pjs."spCheckInvitedReviewersForRound"(' . (int)$pDocumentId . ', ' . (int)$pRoundId . ')';
		$this->m_con->Execute($lSql);

		if((int)$this->m_con->RecordCount()) {
			$lResult['invited_users'] = (int)$this->m_con->mRs['invited_users'];
			$lResult['invited_users_ids'] = $this->m_con->mRs['invited_users_ids'];
		}

		return $lResult;
	}

	function CheckNonSubmitedUsersForRound($pRoundId, $pDocumentId){
		$lCon = $this->m_con;
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
		);

		$lSql = 'SELECT * FROM pjs."spCheckNonSubmitedUsersForRound"(' . (int)$pDocumentId . ', ' . (int)$pRoundId . ')';
		//var_dump($lSql);
		$this->m_con->Execute($lSql);

		if((int)$this->m_con->RecordCount()) {
			$lResult['non_submited_users'] = (int)$this->m_con->mRs['non_submited_users'];
			$lResult['non_submited_users_ids'] = $this->m_con->mRs['non_submited_users_ids'];
		}

		return $lResult;
	}

	function ManageUserInvitationsAndReviews($pRoundId, $pDocumentId, $pInvitedUserIds, $pNonSubmUserIds) {
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		try{
			$lCon = $this->m_con;

			$lSql = 'SELECT * FROM pjs."spManageUserInvitationsAndReviews"(' . (int) $pDocumentId . ', ' . (int)$pRoundId . ', \'' . $pInvitedUserIds . '\' , \'' . $pNonSubmUserIds . '\');';
			//~ echo $lSql;
			//~ exit;
			if(!$lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			} else {
				$lNonSubmittedReviewsDocUids = array();
				$lNonSubmittedReviewsDocUids = explode(',', $pNonSubmUserIds);

				foreach ($lNonSubmittedReviewsDocUids as $key => $value) {
					$lVersionModel = new mVersions();
					$lVersionModel->CreatePwtReviewerVersionWithChanges((int)$pRoundId, 0, (int)$value);
				}
				while(!$lCon->Eof()){
					$lResult['event_ids'][] = $lCon->mRs['event_id'];
					$lCon->MoveNext();
				}
				$lResult['success_msg'] = getstr('pjs.actionSuccessfullyPerformed');
			}
		}catch(Exception $pException){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $pException->getMessage());
		}
		return $lResult;
	}

	function CheckAndAddPanelReviewer($pDocumentId, $pUid) {
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);

		try{
			$lCon = $this->m_con;
			$lSql = 'SELECT * FROM pjs."spCheckToAddPanelReviewer"(' . (int)$pDocumentId . ', ' . (int)$pUid . ')';
			$this->m_con->Execute($lSql);

	 		if(!$lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			} else {
				$lResult['version_id'] = $lCon->mRs['version_id'];
				$lResult['round_user_id'] = $lCon->mRs['round_user_id'];
			}
		} catch(Exception $pException) {
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $pException->getMessage());
		}
		return $lResult;
	}
	function UpdateDocumentReviewType($pDocumentId, $pReviewTypeId){

		$lSql = 'SELECT * FROM pjs.spUpdateReviewType(' . $pDocumentId . ', ' . $pReviewTypeId . ')';
		$lResult = array();
		$this->m_con->Execute($lSql);

		while(!$this->m_con->Eof()){
			$lResult[] = $this->m_con->mRs;
			$this->m_con->MoveNext();
		}
		return $lResult;
	}

	function GetDocumenXmlByVersion($pDocumentId, $pVersionType = DOCUMENT_VERSION_AUTHOR_SUBMITTED_TYPE) {
		$lSql = '
			SELECT pdv.xml as document_current_version_xml, dv.id as doc_version_id
			FROM pjs.document_versions dv
			JOIN pjs.pwt_document_versions pdv ON pdv.version_id = dv.id
			WHERE dv.document_id = ' . (int)$pDocumentId . ' AND dv.version_type_id = ' . (int)$pVersionType . '
			ORDER BY dv.id DESC
			LIMIT 1;
		';
		$this->m_con->Execute($lSql);
		return $this->m_con->mRs;
	}

	function CheckDocumentUserForSpecificRole($pUid, $pCheckRole, $pJournalId, $pDocumentId) {
		$lSql = '
			SELECT id
			FROM pjs.journal_users
			WHERE uid = ' . $pUid . ' AND journal_id = ' . $pJournalId;

		$this->m_con->Execute($lSql);
		if($this->m_con->mRs['id']) {
			return TRUE;
		} else {
			$lSql = '
			SELECT id
			FROM pjs.document_users
			WHERE uid = ' . $pUid . ' AND document_id = ' . (int)$pDocumentId;

			$this->m_con->Execute($lSql);
			if($this->m_con->mRs['id']) {
				return TRUE;
			} else {
				return FALSE;
			}

		}
	}
	
	function SaveLEXMLVersion($pVersionId, $pXML){
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
		);
		
		
		$lSql = 'SELECT * FROM pjs."spSaveLEXMLVersion"(' . (int)$pVersionId . ', \'' . q($pXML) . '\'::xml)';
		//$lSql = 'UPDATE pjs.pwt_document_versions SET xml = \'' . q($pXML) . '\'::xml WHERE version_id = ' . (int)$pVersionId;
		if(!$this->m_con->Execute($lSql)){
			$lResult['err_cnt']++;
			//$lResult['err_msgs'][] = array('err_msg' => $this->m_con->GetLastError());
			$lResult['err_msgs'][] = array('err_msg' => getstr('pjs.xmlIsNotValid_OnSaveInDataBase'));
		}
		
		return $lResult;
	}
	
	function RevertLEXMLVersion($pDocumentId){
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
		);
		
		$lSql = 'SELECT doc_xml FROM pjs."spRevertLEVersion"(' . (int)$pDocumentId . ')';
		if(!$this->m_con->Execute($lSql)){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $this->m_con->GetLastError());
		} else {
			$lResult['doc_xml'] = $this->m_con->mRs['doc_xml'];
		}
		
		return $lResult;
	}
	
	/**
	 * This function returns metadata about the specified document (i.e author_id, document_source_id, creation_step etc.) for PDF preview
	 * @param bigint $pDocumentId
	 */
	function GetDocumentInfoForPDF($pDocumentId){
		$lCon = $this->m_con;
		$lResult = array();

		$lSql = 'SELECT * FROM pjs."spGetDocumentInfoForPDF"(' . (int)$pDocumentId . ')';
		$lCon->Execute($lSql);
		$lResult = $lCon->mRs;

		return $lResult;
	}
	
	/**
	 * This function return document journal id
	 * 
	 * @param bigint $pDocumentId
	 * 
	 * @return int
	 */
	function GetDocumentShortInfo($pDocumentId){
		$lCon = $this->m_con;
		$lResult = array();
		$lSql = "SELECT * FROM pjs.documents WHERE id = $pDocumentId";
		$lCon->Execute($lSql);
		$lResult = $lCon->mRs; 
		return $lResult;
	}
	
}

?>