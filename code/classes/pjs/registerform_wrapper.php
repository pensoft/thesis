<?php

class RegisterForm_Wrapper extends eForm_Wrapper{
	/**
	 * A reference to the page controller
	 * @var cRegister_Controller
	 */
	var $m_pageControllerInstance;

	function __construct($pData){
		$this->m_pageControllerInstance = $pData['page_controller_instance'];
		parent::__construct($pData);

	}

	function CheckIfPasswordIsSecure($pPassword){
		return mb_strlen($pPassword) >= (int)MIN_ALLOWED_PASSWORD_LENGTH;
	}

	protected function PreActionProcessing(){
		if((int)$this->m_pageControllerInstance->GetUserId()){
			$this->m_formController->SetFieldValue('userid', (int)$this->m_pageControllerInstance->GetUserId() );
		}elseif(isset($_SESSION['tmpusrid'])) {
			$this->m_formController->SetFieldValue('userid', (int)$_SESSION['tmpusrid'] );
		}

		if( $this->m_formController->GetCurrentAction() == 'register') {

			/*
				CHECK IF USER ALREADY EXISTS IN OLD DB
				START
			*/

			if((int)$_SESSION['regstep'] == 1 && !(int)$this->m_formController->GetFieldValue('userid')) {
				$lCon = new DbCn(MYSQL_DBTYPE);
				$lCon->Open(MYSQL_DB_SRV, MYSQL_DB, MYSQL_USR, MYSQL_PASS);
				$lCon->Execute('SELECT CID FROM CLIENTS WHERE EMAIL = \'' . $this->m_formController->GetFieldValue('email') . '\'');
				$lCon->MoveFirst();
				if((int)$lCon->mRs['CID']) {
					$this->m_formController->SetError(getstr('This user already exists in our database!'), 'email');
				}
			}

			/*
				CHECK IF USER ALREADY EXISTS IN OLD DB
				END
			*/
			if (!$this->checkIfPasswordIsSecure($this->m_formController->GetFieldValue('password'))) {
				$this->m_formController->SetError(getstr('regprof.pass_not_secure'), 'password');
			}

			if( $this->m_formController->GetFieldValue('password') != $this->m_formController->GetFieldValue('password2') ) {
				$this->m_formController->SetError(getstr('regprof.pass_not_match'), 'password2');
			}
			if ($this->m_formController->GetFieldValue('editprofile') == 1) {
				if((int)$_SESSION['regstep'] == 1 && (int)$this->m_formController->GetFieldValue('userid') && !$this->m_formController->GetErrorCount()) {
					$cn = Con();
					$cn->Execute('SELECT oldpjs_cid FROM usr WHERE id = ' . (int)$this->m_formController->GetFieldValue('userid'));
					$cn->MoveFirst();
					$lOldPjsCid = (int)$cn->mRs['oldpjs_cid'];
					$cn->Close();

					$lCon = new DbCn(MYSQL_DBTYPE);
					$lCon->Open(MYSQL_DB_SRV, MYSQL_DB, MYSQL_USR, MYSQL_PASS);
					$lCon->Execute('CALL spRegUsrStep1(' . ((int)$lOldPjsCid ? (int)$lOldPjsCid : 'NULL') . ', 1, \'' . q($this->m_formController->GetFieldValue('email')) . '\', \'' . q($this->m_formController->GetFieldValue('password')) . '\')');
					$lCon->MoveFirst();
					$lCon->Close();
				}
				$this->m_formController->SetFieldProp('register', 'RedirUrl', '/editprofile.php?tAction=showedit&step=2&editprofile=1&success=1');
				$this->m_formController->SetFieldProp('register', 'ActionMask', ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW | ACTION_REDIRECT);
			}


			if((int)$_SESSION['regstep'] == 2 && (int)$this->m_formController->GetFieldValue('userid')) {

				$cn = Con();
				$cn->Execute('SELECT u.oldpjs_cid, ut.name as salut, ct.name as ctip, c.name as country
							FROM usr u
							LEFT JOIN usr_titles ut ON  ut.id = ' . (int)$this->m_formController->GetFieldValue('usrtitle') . '
							LEFT JOIN client_types ct ON ct.id = ' . (int)$this->m_formController->GetFieldValue('clienttype') . '
							LEFT JOIN countries c ON c.id = ' . (int)$this->m_formController->GetFieldValue('country') . '
							WHERE u.id = ' . (int)$this->m_formController->GetFieldValue('userid'));
				$cn->MoveFirst();


				/*
					INSERT NEW USER INTO OLD PENSOFT DATABASE FOR STEP 2
				*/
				$lCon = new DbCn(MYSQL_DBTYPE);
				$lCon->Open(MYSQL_DB_SRV, MYSQL_DB, MYSQL_USR, MYSQL_PASS);
				$lCon->Execute('CALL spRegUsrStep2(
													' . (int)$cn->mRs['oldpjs_cid'] . ',
													1,
													\'' . q($this->m_formController->GetFieldValue('firstname')) . '\',
													\'' . q($this->m_formController->GetFieldValue('middlename')) . '\',
													\'' . q($this->m_formController->GetFieldValue('lastname')) . '\',
													\'' . q($cn->mRs['salut']) . '\',
													\'' . q($cn->mRs['ctip']) . '\',
													\'' . q($this->m_formController->GetFieldValue('affiliation')) . '\',
													\'' . q($this->m_formController->GetFieldValue('department')) . '\',
													\'' . q($this->m_formController->GetFieldValue('addrstreet')) . '\',
													\'' . q($this->m_formController->GetFieldValue('postalcode')) . '\',
													\'' . q($this->m_formController->GetFieldValue('city')) . '\',
													\'' . q($cn->mRs['country']) . '\',
													\'' . q($this->m_formController->GetFieldValue('phone')) . '\',
													\'' . q($this->m_formController->GetFieldValue('fax')) . '\',
													\'' . q($this->m_formController->GetFieldValue('vatnumber')) . '\',
													\'' . q($this->m_formController->GetFieldValue('website')) . '\')');
				$lCon->MoveFirst();
				$lCon->Close();
				$cn->Close();

			} elseif((int)$_SESSION['regstep'] == 3 && (int)$this->m_formController->GetFieldValue('userid')) {
				/*
					INSERT NEW USER INTO OLD PENSOFT DATABASE FOR STEP 3
				*/

				$lProductTypes = $this->m_formController->GetFieldValue('producttypes');
				$cn = Con();
				$cn->Execute('SELECT u.oldpjs_cid, af.name as emnot
							FROM usr u
							LEFT JOIN usr_alerts_frequency af ON  af.id = ' . (int)$this->m_formController->GetFieldValue('alertsfreq') . '
							WHERE u.id = ' . (int)$this->m_formController->GetFieldValue('userid'));
				$cn->MoveFirst();
				$lEmNot = $cn->mRs['emnot'];
				/*
					INSERT NEW USER INTO OLD PENSOFT DATABASE FOR STEP 2
				*/
				$lCon = new DbCn(MYSQL_DBTYPE);
				$lCon->Open(MYSQL_DB_SRV, MYSQL_DB, MYSQL_USR, MYSQL_PASS);
				$lCon->Execute('CALL spRegUsrStep3(
													' . (int)$cn->mRs['oldpjs_cid'] . ',
													1,
													' . ((int)$lProductTypes[0] ? 1 : 0) . ',
													' . ((int)$lProductTypes[1] ? 1 : 0) . ',
													' . ((int)$lProductTypes[2] ? 1 : 0) . ',
													\'' . q($cn->mRs['emnot']) . '\')');
				$lCon->MoveFirst();
				$lCon->Close();
				$cn->Close();

			}


		}

		if($this->m_formController->GetCurrentAction() == 'showedit') {
			if ( $this->m_formController->GetFieldValue('password') != $this->m_formController->GetFieldValue('password2') ) {
				$this->m_formController->SetError(getstr('regprof.pass_not_match'), 'password2');
			}
			if((int)$this->m_formController->GetValueFromRequestWithoutChecks('showedit')) {
				$this->m_formController->SetFieldValue('editprofile', 1);
				$this->m_formController->SetFieldProp('email', 'AddTags', array(
						'readonly' => 'readonly',
						'id'  => 'P-RegFld-Email',
					)
				);
			}
		}
	}

	protected function PostActionProcessing(){
		$this->m_formController->SetFieldValue('editprofile', $this->m_formController->GetValueFromRequestWithoutChecks('editprofile'));

		if(!$this->m_formController->GetErrorCount() && $this->m_formController->GetCurrentAction() == 'register'){

			if($_SESSION['regstep'] == 1) {
				$lConfHash = GetConfHash( $this->m_formController->GetFieldValue('email'), $this->m_formController->GetFieldValue('email') );
				$_SESSION['confhash'] = $lConfHash;
				$_SESSION['usermail'] = $this->m_formController->GetFieldValue('email');

				$lRegModel = new mRegister_Model();
				$lRegModel->SetUserConfHash( $this->m_formController->GetFieldValue('email'), $lConfHash );
				$_SESSION['tmpusrid'] = (int)$this->m_formController->GetFieldValue('userid');

				/*
					INSERT NEW USER INTO OLD PENSOFT DATABASE FOR STEP 1
					START
				*/


				if(!(int)$this->m_formController->GetValueFromRequestWithoutChecks('editprofile')) {
					$lCon = new DbCn(MYSQL_DBTYPE);
					$lCon->Open(MYSQL_DB_SRV, MYSQL_DB, MYSQL_USR, MYSQL_PASS);
					$lCon->Execute('CALL spRegUsrStep1(NULL, 1, \'' . q($this->m_formController->GetFieldValue('email')) . '\', \'' . q($this->m_formController->GetFieldValue('password')) . '\')');
					$lCon->MoveFirst();
					$lOldPjsCid = (int)$lCon->mRs['CID'];

					$lCon->Close();
					$cn = Con();
					$cn->Execute('SELECT * FROM spSaveOldPJSId(\'' . q($this->m_formController->GetFieldValue('email')) . '\', ' . (int)$lOldPjsCid . ')');
					$cn->Close();

					/*
						INSERT NEW USER INTO OLD PENSOFT DATABASE FOR STEP 1
						END
					*/
				}

			}

			if((int)$this->m_pageControllerInstance->m_MyExpertise){
				echo '<script type="text/javascript">window.parent.location="/editprofile.php?success=1&my_expertise=1";</script>';
				exit;
			}

			if ($_SESSION['regstep'] == 3) {
				if(!(int)$this->m_pageControllerInstance->GetUserId()) {
					$lCon = new DbCn();
					$lCon->Open();
					$lCon->Execute('SELECT autolog_hash
									FROM public.usr u
									WHERE u.uname = \'' . q($_SESSION['usermail']) . '\'');
					$lAutoLogHash = $lCon->mRs['autolog_hash'];
					$lCon->Close();

					/* -- NE SE PRASHTA EMAIL
						$lCon = new DbCn();
						$lCon->Open();
						$lCon->Execute('SELECT coalesce(ut.name || \' \' || u.first_name || \' \' || u.last_name, u.uname) as fullname
										FROM public.usr u
										LEFT JOIN public.usr_titles ut ON ut.id = u.usr_title_id
										WHERE u.uname = \'' . q($_SESSION['usermail']) . '\'');
						$lUserFullName = $lCon->mRs['fullname'];
						$lCon->Close();

						$mespubdata = array(
							'confhash' => $_SESSION['confhash'],
							'siteurl' => SITE_URL,
							'user_fullname' => $lUserFullName,
							'mailsubject' => PENSOFT_MAILSUBJ_REGISTER,
							'mailto' => $_SESSION['usermail'],
							'charset' => 'UTF-8',
							'boundary' => '--_separator==_',
							'from' => array(
								'display' => PENSOFT_MAIL_DISPLAY,
								'email' => PENSOFT_MAIL_ADDR,
							),
							'templs' => array(
								G_DEFAULT => 'registerfrm.mailcontent',
							),
						);
						$msg = new cmessaging($mespubdata);
						$msg->Display();

					*/
				}

				if(isset($_SESSION['regstep'])) unset($_SESSION['regstep']);
				if(isset($_SESSION['tmpusrid'])) unset($_SESSION['tmpusrid']);
				if(isset($_SESSION['usermail'])) unset($_SESSION['usermail']);
				if(isset($_SESSION['confhash'])) unset($_SESSION['confhash']);

				//~ echo '<script type="text/javascript">window.parent.location="/login.php?regsuccess=1";</script>';
				echo '<script type="text/javascript">window.parent.location="/login.php?u_autolog_hash=' . $lAutoLogHash . '";</script>';
				exit;
			} else {
				if($_SESSION['regstep'] < 3){
					$_SESSION['regstep'] = $_SESSION['regstep'] + 1;
				}
				if(isset($_SESSION['tmpusrid']) && (int)$_SESSION['tmpusrid']) {
					echo '<script type="text/javascript">LayerProfEditFrm(\'P-Registration-Content\', 2, ' . $_SESSION['regstep'] . ');</script>';
					exit;
				} else {
					echo '<script type="text/javascript">LayerRegFrm(\'P-Registration-Content\', 1, 1);</script>';
					exit;
				}
			}
		}else{
			if((int)$this->m_pageControllerInstance->GetUserId()){
				$this->m_formController->SetFieldValue('userid', (int)$this->m_pageControllerInstance->GetUserId() );
			}elseif(isset($_SESSION['tmpusrid'])) {
				$this->m_formController->SetFieldValue('userid', (int)$_SESSION['tmpusrid'] );
			}
			$this->m_formController->SetFieldValue('password', '');
			$this->m_formController->SetFieldValue('password2', '');
		}
		if($_SESSION['regstep'] == 2){
			$this->m_formController->SetPubdataValue('photo_id', $this->m_formController->GetFieldValue('photoid'));
		}
		if($_SESSION['regstep'] == 3 || (int)$this->m_pageControllerInstance->m_MyExpertise){
			$this->m_pageControllerInstance->m_Categories_Controller->SetTreeSelectedVals('alerts_subject_cats', 'subject_categories', 'subject_selected_vals', $this->m_formController);
			$this->m_pageControllerInstance->m_Categories_Controller->SetTreeSelectedVals('alerts_chronical_cats', 'chronological_categories', 'chronological_selected_vals', $this->m_formController);
			$this->m_pageControllerInstance->m_Categories_Controller->SetTreeSelectedVals('alerts_taxon_cats', 'taxon_categories', 'taxon_selected_vals', $this->m_formController);
			$this->m_pageControllerInstance->m_Categories_Controller->SetTreeSelectedVals('alerts_geographical_cats', 'geographical_categories', 'geographical_selected_vals', $this->m_formController);
		}
	}
}

?>