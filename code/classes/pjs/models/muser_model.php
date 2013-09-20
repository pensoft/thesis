<?php

/**
 * A model to implement users functionality (e.g. change profile pic id)
 * @author peterg
 *
 */
class mUser_Model extends emBase_Model {
	// @formatter->off
	/**
	 * Returns an id for a new pic which is being uploaded
	 *
	 * returns an array with the following format (
	 * err_cnt => number of errors
	 * err_msgs => an array containing the error msgs (an array containing
	 * arrays with the following format
	 * err_msg => the msg of the current error
	 * )
	 * id => the id of the pic if there are no errors
	 * )
	 */
	// @formatter->on
	function ChangeUserPreviewPic($pUid, $pPreviewPicID) {
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
			'id' => 0
		);
		$lCon = $this->m_con;
		$lSql = 'SELECT * FROM spChangeUserPreviewPic(' . (int) $pUid . ', ' . (int) $pPreviewPicID . ')';

		if(! $lCon->Execute($lSql)){
			$lResult['err_cnt'] ++;
			$lResult['err_msgs'][] = array(
				'err_msg' => getstr($lCon->GetLastError())
			);
		}

		// var_dump($lResult);
		return $lResult;
	}

	// @formatter->off
	/**
	 * Sets the users previewpicid to null
	 *
	 * returns an array with the following format (
	 * err_cnt => number of errors
	 * err_msgs => an array containing the error msgs (an array containing
	 * arrays with the following format
	 * err_msg => the msg of the current error
	 * )
	 * )
	 */
	// @formatter->on
	function RemoveUserPreviewPic($pUid) {
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
			'id' => 0
		);
		$lCon = $this->m_con;
		$lSql = 'SELECT * FROM spRemoveUserPreviewPic(' . (int) $pUid . ')';

		if(! $lCon->Execute($lSql)){
			$lResult['err_cnt'] ++;
			$lResult['err_msgs'][] = array(
				'err_msg' => getstr($lCon->GetLastError())
			);
		}

		// var_dump($lResult);
		return $lResult;
	}

	// @formatter->off
	/**
	 * Returns the user data for the specified user
	 *
	 * returns an array with the following format (
	 * err_cnt => number of errors
	 * err_msgs => an array containing the error msgs (an array containing
	 * arrays with the following format
	 * err_msg => the msg of the current error
	 * )
	 * key => value //Numerous such pairs containing the actual user data
	 * )
	 */
	// @formatter->on
	function GetUserData($pUid) {
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
			'id' => 0
		);
		$lCon = $this->m_con;
		$lSql = 'SELECT * FROM spGetUserData(' . (int) $pUid . ')';

		if(! $lCon->Execute($lSql)){
			$lResult['err_cnt'] ++;
			$lResult['err_msgs'][] = array(
				'err_msg' => getstr($lCon->GetLastError())
			);
		}
		$lResult = array_merge($lResult, $lCon->mRs);

		// var_dump($lResult);
		return $lResult;
	}

	/**
	 * Returns an array containing all the allowed roles
	 * for the specified user and journal
	 *
	 * @param $pUid int
	 * @param $pJournalId int
	 */
	function GetUserJournalAllowedRoles($pUid, $pJournalId) {

		$lResult = array();
		//per-document roles
		if($this->CheckIfUserIsJournalAuthor($pUid, $pJournalId)){
			$lResult[] = AUTHOR_ROLE;
		}
		if($this->CheckIfUserIsJournalDedicatedReviewer($pUid, $pJournalId)){
			$lResult[] = DEDICATED_REVIEWER_ROLE;
		}
		$journal_wide_roles = array(SE_ROLE, LE_ROLE, CE_ROLE, JOURNAL_EDITOR_ROLE, JOURNAL_MANAGER_ROLE);
		foreach ($journal_wide_roles as $key => $role){
			if ($this->CheckIfUserIsJournalWide($role, $pUid, $pJournalId)){
				$lResult[] = $role;
			} 
		}
		return $lResult;
	}
	function CheckIfUserIsJournalWide($role, $pUid, $pJournalId) {
		$lSql = "
		SELECT id
		FROM pjs.journal_users
		WHERE uid = $pUid 
		  AND journal_id = $pJournalId  
		  AND role_id = $role";
		return $this->m_con->Execute($lSql) && $this->m_con->mRs['id'];
	}
	/**
	 * Returns whether the specified user is author in the specified journal or
	 * not
	 * (A user is considered author if he has at least 1 document for the
	 * specified journal)
	 *
	 * @param $pUid unknown_type
	 * @param $pJournalId unknown_type
	 * @return boolean
	 */
	function CheckIfUserIsJournalAuthor($pUid, $pJournalId) {
		// Checks if the user has any documents in the specified journal
		$lSql = "
		SELECT d.id
		FROM pjs.documents d JOIN
			 pjs.document_users du on du.document_id = d.id
		WHERE d.journal_id = $pJournalId 
		  AND du.uid = $pUid
		  AND du.role_id = " . AUTHOR_ROLE . "
		LIMIT 1";
		return $this->m_con->Execute($lSql) && $this->m_con->mRs['id'];			
	}

	/**
	 * Returns whether the specified user is a dedicated reviewer in the
	 * specified journal or not
	 * A user is considered dedicated reviewer if he has been
	 * added successfully (has confirmed) as dedicated reviewer for any document
	 * in the specified journal,
	 * or if he has been invited to be a dedicated reviewer for any document and
	 * hasnt declined/timeouted the invitation.
	 *
	 * @param $pUid unknown_type
	 * @param $pJournalId unknown_type
	 * @return boolean
	 */
	function CheckIfUserIsJournalDedicatedReviewer($pUid, $pJournalId) {
		// Checks if the user has been any added successfully as dedicated
		// reviewer for any document in the specified journal
		$lSql = 'SELECT d.id
		FROM pjs.documents d
		JOIN pjs.document_users du ON du.document_id = d.id
		WHERE d.journal_id = ' . (int) $pJournalId . ' AND du.uid = ' . (int) $pUid . ' AND du.role_id = ' . (int) DEDICATED_REVIEWER_ROLE . '
		LIMIT 1
		';
		if($this->m_con->Execute($lSql) && (int) $this->m_con->mRs['id']){
			return true;
		}
		$lSql = 'SELECT d.id
		FROM pjs.documents d
		JOIN pjs.document_user_invitations du ON du.document_id = d.id AND d.current_round_id = du.round_id
		WHERE d.journal_id = ' . (int) $pJournalId . ' AND du.uid = ' . (int) $pUid . ' AND du.role_id in (' . DEDICATED_REVIEWER_ROLE . ', ' . COMMUNITY_REVIEWER_ROLE . ' )
			AND d.state_id = ' . (int) DOCUMENT_IN_REVIEW_STATE . '
		LIMIT 1
		';
		return $this->m_con->Execute($lSql) && (int) $this->m_con->mRs['id']; 
	}
	/**
	 * Checks whether the specified user can view the specified document in the
	 * specified role.
	 *
	 * @param $pUid unknown_type
	 * @param $pDocumentId unknown_type
	 * @param $pViewRole unknown_type
	 * @param $pVersionId unknown_type
	 */
	function CheckIfUserCanViewDocument($pUid, $pDocumentId, $pViewRole) {
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		$lCon = $this->m_con;
		try{
			switch ((int) $pViewRole) {
				default :
					throw new Exception(getstr('pjs.thereIsNoSuchDocumentOrTheSpecifiedUserCannotOpenItInTheSpecifiedRole'));

				case (int) JOURNAL_EDITOR_ROLE :
					$lSql = "SELECT d.id
						FROM pjs.documents d
						JOIN pjs.journal_users u ON u.journal_id = d.journal_id
						WHERE d.id  = $pDocumentId
						  AND u.uid = $pUid 
						  AND u.role_id = $pViewRole";
					break;
				case AUTHOR_ROLE:
				case SE_ROLE:
					$lSql = "SELECT document_id as id
						FROM pjs.document_users
						WHERE document_id = $pDocumentId 
						  AND uid = $pUid
						  AND role_id IN ($pViewRole, " . JOURNAL_EDITOR_ROLE . ")
						UNION 
						SELECT d.id
						FROM pjs.documents d
						JOIN pjs.journal_users ju ON ju.journal_id = d.journal_id AND ju.uid = $pUid AND ju.role_id = " . JOURNAL_EDITOR_ROLE . "
						WHERE d.id = $pDocumentId";
					break;
				case LE_ROLE:
				case CE_ROLE:
					$lSql = "SELECT document_id as id
						FROM pjs.document_users
						WHERE document_id = $pDocumentId 
						  AND uid = $pUid
						  AND role_id = $pViewRole";
					break;
				case (int) COMMUNITY_REVIEWER_ROLE :
					$lSql = 'SELECT d.id 
						FROM pjs.documents d
						JOIN pjs.document_user_invitations i ON i.document_id = d.id AND role_id = ' . COMMUNITY_REVIEWER_ROLE . ' AND i.uid = ' . $pUid . '
						WHERE d.id = ' . (int)$pDocumentId;
					break;
				case (int) DEDICATED_REVIEWER_ROLE :
					$lSql = 'SELECT d.id
						FROM pjs.documents d
						LEFT JOIN pjs.document_users u ON u.uid = ' . (int) $pUid . ' AND u.document_id = d.id AND u.role_id IN (' . (int) DEDICATED_REVIEWER_ROLE . ', ' . COMMUNITY_REVIEWER_ROLE . ')
						LEFT JOIN pjs.document_user_invitations i ON i.uid = ' . (int) $pUid . ' AND i.role_id IN (' . (int) DEDICATED_REVIEWER_ROLE . ', ' . COMMUNITY_REVIEWER_ROLE . ')
						WHERE d.id = ' . (int) $pDocumentId . ' AND (u.id IS NOT NULL OR i.id IS NOT NULL) ';
					break;
			}
			if(! $lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			}
			if(! $lCon->mRs['id']){
				throw new Exception(getstr('pjs.thereIsNoSuchDocumentOrTheSpecifiedUserCannotOpenItInTheSpecifiedRole'));
			}
		}catch(Exception $pException){
			$lResult['err_cnt'] ++;
			$lResult['err_msgs'][] = array(
				'err_msg' => $pException->getMessage()
			);
		}
		return $lResult;
	}

	/**
	 * Returns the data about the reviewer invitation for the specified user
	 * for the specified document (if exists) and role
	 *
	 * @param $pDocumentId unknown_type
	 * @param $pUid unknown_type
	 */
	function GetDocumentCurrentRoundReviewerInvitationData($pDocumentId, $pUid, $pRoles = array(DEDICATED_REVIEWER_ROLE)) {
		$lSql = '
			SELECT i.*, rd.decision_id, rd.due_date as decision_due_date, rd.id as round_user_id,
				de.name as decision_name, rd.decision_notes, rd.decision_notes, rd.decision_date, drrus.id as review_user_state
			FROM pjs.documents d
			JOIN pjs.document_user_invitations i ON i.document_id = d.id AND i.round_id = d.current_round_id
			LEFT JOIN pjs.document_users du ON du.document_id = d.id AND du.uid = i.uid AND du.role_id = i.role_id
			LEFT JOIN pjs.document_review_round_users rd ON rd.round_id = i.round_id AND rd.document_user_id = du.id
			LEFT JOIN pjs.document_review_round_user_states drrus ON drrus.id = rd.state_id
			LEFT JOIN pjs.document_review_round_decisions de ON de.id = rd.decision_id
			WHERE i.uid = ' . (int) $pUid . ' AND d.id = ' . (int) $pDocumentId . ' AND i.role_id IN (' . implode(',', $pRoles) . ')
		';
		$this->m_con->Execute($lSql);
		// var_dump($lSql);
		return $this->m_con->mRs;
	}

	/**
	 * Returns the data about the layout editor round document user for the specified user
	 * for the specified document (if exists) and role
	 *
	 * @param $pDocumentId unknown_type
	 * @param $pUid unknown_type
	 */
	function GetDocumentCurrentRoundLEUserData($pDocumentId, $pUid) {
		$lSql = '
		SELECT rd.decision_id, rd.due_date as decision_due_date, rd.id as round_user_id,
		de.name as decision_name, rd.decision_notes, rd.decision_notes, rd.decision_date
		FROM pjs.documents d
		JOIN pjs.document_users du ON du.document_id = d.id
		JOIN pjs.document_review_round_users rd ON rd.round_id = d.current_round_id AND rd.document_user_id = du.id
		LEFT JOIN pjs.document_review_round_decisions de ON de.id = rd.decision_id
		WHERE d.id = ' . (int) $pDocumentId . 'AND du.uid = ' . (int)$pUid . ' AND du.role_id = ' . (int) LE_ROLE . '
		AND d.state_id = ' . (int)DOCUMENT_IN_LAYOUT_EDITING_STATE . '
		';

// 		var_dump($lSql);
		$this->m_con->Execute($lSql);
// 		var_dump($this->m_con->mRs);
		return $this->m_con->mRs;
	}

	/**
	 * Returns the data about the copy editor round document user for the specified user
	 * for the specified document (if exists) and role
	 *
	 * @param $pDocumentId unknown_type
	 * @param $pUid unknown_type
	 */
	function GetDocumentCurrentRoundCEUserData($pDocumentId, $pUid) {
		$lSql = '
		SELECT rd.decision_id, rd.due_date as decision_due_date, rd.id as round_user_id,
		de.name as decision_name, rd.decision_notes, rd.decision_notes, rd.decision_date
		FROM pjs.documents d
		JOIN pjs.document_users du ON du.document_id = d.id
		JOIN pjs.document_review_round_users rd ON rd.round_id = d.current_round_id AND rd.document_user_id = du.id
		LEFT JOIN pjs.document_review_round_decisions de ON de.id = rd.decision_id
		WHERE d.id = ' . (int) $pDocumentId . 'AND du.uid = ' . (int)$pUid . ' AND du.role_id = ' . (int) CE_ROLE . '
		AND d.state_id = ' . (int)DOCUMENT_IN_COPY_REVIEW_STATE . '
		';

		// 		var_dump($lSql);
		$this->m_con->Execute($lSql);
		return $this->m_con->mRs;
	}
	
	function GetProfileInformation($pUserId){
		$lSql = 'SELECT  coalesce(ut.name, \'\') || \' \' || coalesce(u.first_name, \'\') || \' \' || coalesce(u.last_name, \'\') as fullname, 
						u.affiliation, u.addr_street, u.addr_postcode, u.addr_city,  c.name as country, u.photo_id,
						u.website, u.uname, array_to_string(array_agg(DISTINCT pt.name), \', \') as product_types,
						array_to_string(array_agg(DISTINCT sc.name), \', \') as subject_categories,
						array_to_string(array_agg(DISTINCT tc.name), \', \') as taxon_categories,
						array_to_string(array_agg(DISTINCT cc.name), \', \') as chronological_categories,
						array_to_string(array_agg(DISTINCT gc.name), \', \') as geographical_categories,
						array_to_string(array_agg(DISTINCT j.name), \', \') as email_alerts_from_journals
				FROM usr u 
				LEFT JOIN usr_titles ut ON ut.id = u.usr_title_id
				LEFT JOIN countries c ON c.id = u.country_id
				LEFT JOIN product_types pt ON pt.id = ANY (u.product_types)
				LEFT JOIN subject_categories sc ON sc.id = ANY (u.subject_categories)
				LEFT JOIN taxon_categories tc ON tc.id = ANY (u.taxon_categories)
				LEFT JOIN chronological_categories cc ON cc.id = ANY (u.chronological_categories)
				LEFT JOIN geographical_categories gc ON gc.id = ANY (u.geographical_categories)
				LEFT JOIN journals j ON j.id = ANY (u.journals)
				WHERE u.id = ' . (int)$pUserId . '
				GROUP BY ut.name, u.first_name, u.last_name, u.affiliation, u.photo_id,
						 u.addr_street, u.addr_postcode, u.addr_city, c.name, u.website, u.uname';

		// 		var_dump($lSql);
		$this->m_con->SetFetchReturnType(PGSQL_ASSOC);
		
		$this->m_con->Execute($lSql);
		// 		var_dump($this->m_con->mRs);
		return $this->m_con->mRs;
	}
	
	function GetUser($pUid) {
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
			'id' => 0
		);
		$lCon = $this->m_con;
		$lSql = '
			SELECT 
				u.*, 
				first_name || \' \' || last_name as fullname,
				c.name as usr_country
			FROM usr u
			LEFT JOIN countries c ON c.id = u.country_id
			WHERE u.id = ' . (int) $pUid;

		if(! $lCon->Execute($lSql)){
			$lResult['err_cnt'] ++;
			$lResult['err_msgs'][] = array(
				'err_msg' => getstr($lCon->GetLastError())
			);
		}
		$lResult = array_merge($lResult, $lCon->mRs);

		// var_dump($lResult);
		return $lResult;
	}

	/**
	 * Returns true if the user is in the specific role/roles, else false
	 *
	 * @param $pDocumentId unknown_type
	 * @param $pUid unknown_type
	 * 
	 * @return boolean
	 */
	function CheckIfUserHasRole($pUserId, $pJournalId, $pUserRolesArr) {
		$lSql = "
			SELECT 
				id 
			FROM pjs.journal_users 
			WHERE journal_id = $pJournalId 
				AND uid = $pUserId
				AND role_id IN (" . implode(',', $pUserRolesArr) .")";

		$this->m_con->Execute($lSql);
		if((int)$this->m_con->mRs['id']){
			return TRUE;
		}
		return FALSE;
	}
}

?>