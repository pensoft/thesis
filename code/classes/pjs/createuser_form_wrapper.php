<?php

class createUser_Form_Wrapper extends eForm_Wrapper{

	var $m_pageControllerInstance;
	var $m_journalId;
	var $m_mode;
	var $m_DocumentId;
	var $m_RoundId;
	var $m_uid; // new user id
	var $m_role;
	
	function __construct($pData){
		$this->m_pageControllerInstance = $pData['page_controller_instance'];
		parent::__construct($pData);

	}

	protected function PreActionProcessing(){
		$this->m_journalId = $this->m_formController->GetFieldValue('journal_id');
		$this->m_DocumentId = (int)$this->m_formController->GetFieldValue('documentid');
		$this->m_RoundId = (int)$this->m_formController->GetFieldValue('roundid');
		$this->m_mode = (int)$this->m_formController->GetFieldValue('mode');
		$this->m_role = (int)$this->m_formController->GetFieldValue('role');
	}
	protected function PostActionProcessing(){
		if( !$this->m_formController->GetErrorCount() && $this->m_formController->GetCurrentAction() == 'save'){
			$this->m_uid = $this->m_formController->GetFieldValue('guid');
			/* INSERT V BAZATA NA PENSOFT START */
			$lCon = new DbCn(MYSQL_DBTYPE);
			$lCon->Open(MYSQL_DB_SRV, MYSQL_DB, MYSQL_USR, MYSQL_PASS);
			$lCon->Execute('SELECT CID FROM CLIENTS WHERE EMAIL = \'' . $this->m_formController->GetFieldValue('email') . '\'');
			$lCon->MoveFirst();
			if(!(int)$lCon->mRs['CID']) {
				
				$cn = Con();
				$cn->Execute('SELECT u.*, ut.name as salut, ct.name as ctip, c.name as country
							FROM usr u
							LEFT JOIN usr_titles ut ON  ut.id = ' . (int)$this->m_formController->GetFieldValue('usrtitle') . '
							LEFT JOIN client_types ct ON ct.id = ' . (int)$this->m_formController->GetFieldValue('clienttype') . '
							LEFT JOIN countries c ON c.id = ' . (int)$this->m_formController->GetFieldValue('country') . '
							WHERE trim(lower(u.uname)) = trim(lower(\'' . q($this->m_formController->GetFieldValue('email')) . '\'))');
							
				$cn->MoveFirst();
				if($cn->mRs['plain_upass']) {
					$lCon = new DbCn(MYSQL_DBTYPE);
					$lCon->Open(MYSQL_DB_SRV, MYSQL_DB, MYSQL_USR, MYSQL_PASS);
					$lCon->Execute('CALL spRegUsrStep1(NULL, 1, \'' . q($this->m_formController->GetFieldValue('email')) . '\', \'' . q($cn->mRs['plain_upass']) . '\')');
					$lCon->MoveFirst();
					$lOldPjsCid = (int)$lCon->mRs['CID'];	
				}
				if((int)$lOldPjsCid) {
					$lCon->Close();
					$lCon = new DbCn(MYSQL_DBTYPE);
					$lCon->Open(MYSQL_DB_SRV, MYSQL_DB, MYSQL_USR, MYSQL_PASS);
					$lCon->Execute('CALL spRegUsrStep2(
														' . (int)$lOldPjsCid . ',
														1,
														\'' . q($cn->mRs['first_name']) . '\',
														\'' . q($cn->mRs['middle_name']) . '\',
														\'' . q($cn->mRs['last_name']) . '\',
														\'' . q($cn->mRs['salut']) . '\',
														\'' . q($cn->mRs['ctip']) . '\',
														\'' . q($cn->mRs['affiliation']) . '\',
														\'' . q($cn->mRs['department']) . '\',
														\'' . q($cn->mRs['addr_street']) . '\',
														\'' . q($cn->mRs['addr_postcode']) . '\',
														\'' . q($cn->mRs['addr_city']) . '\',
														\'' . q($cn->mRs['country']) . '\',
														\'' . q($cn->mRs['phone']) . '\',
														\'' . q($cn->mRs['fax']) . '\',
														\'' . q($cn->mRs['vat']) . '\',
														\'' . q($cn->mRs['webiste']) . '\')');
					$lCon->MoveFirst();
					$lCon->Close();
					
					$cn = new DbCn();
					$cn->Open();
					$cn->Execute('SELECT * FROM spSaveOldPJSId(\'' . q($this->m_formController->GetFieldValue('email')) . '\', ' . (int)$lOldPjsCid . ')');
					$cn->Close();
				}
				
			}
			
			if((int)$this->m_formController->GetFieldValue('event_id')) {
				//trigger_error('!!!CREATE USER NOTICE!!!', E_USER_NOTICE);
				//trigger_error('upass: ' . $this->m_formController->GetFieldValue('upass'), E_USER_NOTICE);
				//trigger_error('journal_id: ' . $this->m_formController->GetFieldValue('journal_id'), E_USER_NOTICE);
				/**
				 * Manage event task (submitting new document)
				 */
				$lCreateUserTaskObj = new cTask_Manager(array(
					'event_id' => (int)$this->m_formController->GetFieldValue('event_id'),
					'upass' => $this->m_formController->GetFieldValue('upass'),
					'journal_id' => $this->m_formController->GetFieldValue('journal_id'),
				));
				$lCreateUserTaskObj->Display();
			}
			
			/* INSERT V BAZATA NA PENSOFT END */
			if ($this->m_mode) {
				global $user;
				
				switch ($this->m_role){
					case SE_ROLE: 
						$lDocumentsModel = new mDocuments_Model();
						$lRes = array();
						$lRes = $lDocumentsModel->AddRemoveSE($this->m_DocumentId, $this->m_uid, $user->id);
						
						// creating tasks
						foreach ($lRes['event_id'] as $key => $value) {
							/**
							 * Manage event task (submitting new document)
							 */
							$lTaskObj = new cTask_Manager(array(
								'event_id' => (int)$value,
							));
							$lTaskObj->Display();
						}
						
						// redirect
						$lEvents = 'event_id[]=' . implode("&event_id[]=", $lRes['event_id']) . '&e_redirect=1';
						header('Location: /view_document.php?id=' . $this->m_DocumentId . '&view_role=' . E_ROLE . '&mode=1&suggested=1&' . $lEvents);
						exit;
					break;
					case DEDICATED_REVIEWER_ROLE:
						if ($this->m_mode == SE_ROLE){ // add reviwer as SE
							$lDocumentsModel = new mDocuments_Model();
							$lDocumentsModel->InviteReviewerAsGhost($this->m_uid, $this->m_DocumentId, $this->m_RoundId);
							header('Location: /view_document.php?id=' . $this->m_DocumentId . '&view_role=' . $this->m_mode . '&mode=' . REVIEWER_INVITATION_NEW_STATE);
							exit;
						} else { // add reviwer as Author
							
							$lDocumentsModel = new mDocuments_Model();
							$lRoundData = $lDocumentsModel->GetDocumentCurrentReviewRoundId($this->m_DocumentId);
							$lDocumentsModel->InviteDocumentReviewerByAuthor(
								1, 
								0, 
								$this->m_DocumentId, 
								$this->m_uid, 
								$user->id, 
								(int)$lRoundData['round_id'], 
								1
							);
							header('Location: /document_bdj_submission.php?document_id=' . $this->m_DocumentId);
							exit;
							//$lDocumentsModel->InviteDocumentReviewerByAuthor(1, $this->m_DocumentId, $this->m_uid, $user->id, $lRoundData['round_id'], AUTHOR_ROLE);
							//~ header('Location: /view_document.php?id=' . $this->m_DocumentId . '&view_role=' . $this->m_mode . '&mode=' . REVIEWER_INVITATION_NEW_STATE);
						}
					break;
					default:
						header('Location: /manage_journal_users.php?journal_id=' . $this->m_journalId);
						exit;
					break;
				}
			}
			header('Location: /manage_journal_users.php?journal_id=' . $this->m_journalId);
			exit;
		}

		if($this->m_formController->GetFieldValue('guid')){
			$this->m_formController->SetFieldProp('usrtitle', 'AddTags', array('disabled' => 'disabled'));
			$this->m_formController->SetFieldProp('firstname', 'AddTags', array('disabled' => 'disabled'));
			$this->m_formController->SetFieldProp('lastname', 'AddTags', array('disabled' => 'disabled'));
			$this->m_formController->SetFieldProp('affiliation', 'AddTags', array('disabled' => 'disabled'));
			$this->m_formController->SetFieldProp('city', 'AddTags', array('disabled' => 'disabled'));
			$this->m_formController->SetFieldProp('country', 'AddTags', array('disabled' => 'disabled'));
		}
		
		$this->m_pageControllerInstance->m_Categories_Controller->SetTreeSelectedVals('alerts_subject_cats', 'subject_categories', 'subject_selected_vals', $this->m_formController);
		$this->m_pageControllerInstance->m_Categories_Controller->SetTreeSelectedVals('alerts_taxon_cats', 'taxon_categories', 'taxon_selected_vals', $this->m_formController);
		$this->m_pageControllerInstance->m_Categories_Controller->SetTreeSelectedVals('alerts_geographical_cats', 'geographical_categories', 'geographical_selected_vals', $this->m_formController);
	}
}

?>