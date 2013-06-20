<?php
/**
 * A controller used to manage Reminders
 * @author viktorp
 *
 * @version bug-free version
 */
class cSubmission_Updater_Controller extends cBase_Controller {
	
	/**
	 * Submission updater model
	 * 
	 * @var object
	 * 
	 */
	var $m_SubmissionUpdaterModel;
	
	/**
	 * option (php argument)
	 * 
	 * @var int
	 */
	var $m_opt;
	
	function __construct($pOpt) {
		parent::__construct();
		$this->m_SubmissionUpdaterModel = new mSubmission_Updater_Model();
		$this->m_opt = (int)$pOpt;
	}

	/**
	 * Timeout user invitations
	 * 
	 */
	private function HandleTimeouts(){
		$this->m_SubmissionUpdaterModel->TimeOutInvitationUsers();
	}
	
	/**
	 * Check if document round round_due_date is late
	 * 
	 */
	private function HandleCanProceedAction(){
		$this->m_SubmissionUpdaterModel->HandleCanProceedAction();
	}
	
	
	/**
	 * Handles method call
	 * 
	 */
	function Display() {
		switch ($this->m_opt) {
			case 1:
				$this->HandleTimeouts();
				break;
			case 2:
				$this->HandleCanProceedAction();
				break;
			default:
				$this->HandleTimeouts();
				$this->HandleCanProceedAction();
				break;
		}
	}

}

?>