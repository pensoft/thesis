<?php

/**
 * A model class to handle submission updates
 * @author viktorp
 *
 */
class mSubmission_Updater_Model extends emBase_Model {
	
	function TimeOutInvitationUsers() {
		$lCon = $this->m_con;
		$lSql = 'SELECT * FROM spHandleTimeouts()';
		$lCon->Execute($lSql);
		return 1;
	}
	
	function HandleCanProceedAction() {
		$lCon = $this->m_con;
		$lSql = 'SELECT * FROM spHandleCanProceedAction()';
		$lCon->Execute($lSql);
		return 1;
	}
	
}

?>