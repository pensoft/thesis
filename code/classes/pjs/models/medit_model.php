<?php
/**
 * A model to edit journal articles
 *
 */
class mEdit_model extends emBase_Model {
	/**
	 * Return 1 if user have edit rights for this journal
	 * @param pUserId - id of the current user
	 */
	function CheckJournalRights($pUserId, $pJournalId){
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT count(*) as has_rights
					FROM pjs.journal_users
					WHERE uid = ' . (int)$pUserId . ' AND journal_id = ' . (int)$pJournalId . 'AND role_id IN ' . EDIT_JOURNAL_RIGHT_IDS;
// 		var_dump($lSql);
		$lCon->Execute($lSql);
		if( (int)$lCon->mRs['has_rights'] ){
			return 1;
		}
 		return 0;
	}
	
	function CheckJournalIssueRights($pUserId, $pJournalId){
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT count(*) as has_rights
					FROM pjs.journal_users
					WHERE uid = ' . (int)$pUserId . ' AND journal_id = ' . (int)$pJournalId . 'AND role_id IN ' . EDIT_JOURNAL_ISSUES_RIGHT_IDS;
// 		var_dump($lSql);
		$lCon->Execute($lSql);
		if( (int)$lCon->mRs['has_rights'] ){
			return 1;
		}
 		return 0;
	}
	
	function CheckUserIsJournalManager($pUserId, $pJournalId){
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT count(*) as has_rights
					FROM pjs.journal_users
					WHERE uid = ' . (int)$pUserId . ' AND journal_id = ' . (int)$pJournalId . 'AND role_id = ' . JOURNAL_MANAGER_ROLE;
// 		var_dump($lSql);
		$lCon->Execute($lSql);
		if( (int)$lCon->mRs['has_rights'] ){
			return 1;
		}
 		return 0;
	}
	
	function CheckUserRight($pUserId, $pJournalId, $pRightId){
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT count(*) as has_rights
					FROM pjs.journal_users
					WHERE uid = ' . (int)$pUserId . ' AND journal_id = ' . (int)$pJournalId . 'AND role_id = ' . (int)$pRightId;
// 		var_dump($lSql);
		$lCon->Execute($lSql);
		if( (int)$lCon->mRs['has_rights'] ){
			return 1;
		}
 		return 0;
	}
	
	function CheckJournalExist($pJournalId){
		$lCon = $this->m_con;
		$lSql = 'SELECT count(*) as journal_exist FROM journals WHERE id = ' . (int)$pJournalId;
		$lCon->Execute($lSql);
		if($lCon->mRs['journal_exist'])
			return 1;
		return 0;
	}
	
	// 
	function CheckDocumentAuthor($pDocumentId, $pUserId){
		$lCon = $this->m_con;
		$lSql = 'SELECT submitting_author_id authorid FROM pjs.documents WHERE id = ' . $pDocumentId . ' AND submitting_author_id = ' . $pUserId . '';
		$lCon->Execute($lSql);
		if($lCon->mRs['authorid'])
			return 1;
		return 0;
	}
	
	function GetStoryContent($pStoryId){
		return file_get_contents( PATH_STORIES . $pStoryId . '.html' );
	}
}
?>