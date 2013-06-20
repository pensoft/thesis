<?php

class FPass_Wrapper extends eForm_Wrapper{
	/**
	 * A reference to the page controller
	 * @var FPass_Wrapper
	 */
	var $m_pageControllerInstance;
	function __construct($pData){
		$this->m_pageControllerInstance = $pData['page_controller_instance'];
		parent::__construct($pData);	
	}

	protected function PreActionProcessing(){
		if ($this->m_formController->GetCurrentAction() == 'send') {
			if(!$this->m_formController->GetFieldValue('email')){
				$this->m_formController->SetError(getstr('regprof.errnodata'), 'email');
			} elseif (!CheckMail($this->m_formController->GetFieldValue('email'))) {
				$this->m_formController->SetError(getstr('regprof.email_not_valid'), 'email');
			}
		}
	}

	protected function PostActionProcessing(){
		if ($this->m_formController->GetCurrentAction() == 'send' && $this->m_formController->GetErrorCount() == 0) {
			
			$cn = Con();
			$sql = 'SELECT * FROM spUserFpass(\'' . q($this->m_formController->GetFieldValue('email')) . '\', null)';
			$cn->Execute($sql);
			$cn->MoveFirst();
			
			if (!$cn->mRs['uname']) {
				$this->m_formController->SetError(getstr('fpass_form.nosuchuser'));
			} else {
			
				/* UPDATE NA PASS V BAZATA NA PENSOFT */
				$lCon = new DbCn(MYSQL_DBTYPE);
				$lCon->Open(MYSQL_DB_SRV, MYSQL_DB, MYSQL_USR, MYSQL_PASS);
				$lCon->Execute('UPDATE CLIENTS SET PASS = \'' . q($cn->mRs['upass']) . '\' WHERE email LIKE \'' . $cn->mRs['uname'] . '\' AND CID = ' . (int)$cn->mRs['oldpjs_cid']);
				$lCon->MoveFirst();
				$lCon->Close();
				/* UPDATE NA PASS V BAZATA NA PENSOFT */
				
				$mespubdata = array(
					'uname' => $cn->mRs['uname'],
					'pass' => $cn->mRs['upass'],
					'fullname' => $cn->mRs['fullname'],
					'requestdate' => date('d/m/Y H:i'),
					'mailsubject' => MAILSUBJ_FPASS,
					'mailto' => $this->m_formController->GetFieldValue('email'),
					'charset' => 'UTF-8',
					'boundary' => '--_separator==_',
					'from' => array(
						'display' => MAIL_DISPLAY,
						'email' => MAIL_ADDR,
					),
					'templs' => array(
						G_DEFAULT => 'loginform.fpassmail',
					),
				);
				
				$msg = new cmessaging($mespubdata);
				$msg->Display();
				
				header('Location: /fpass.php?success=1');
				exit;
			}
	
		} 
	}


}

?>