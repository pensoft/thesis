<?php

class cgetatt extends cbase {
	var $file_id;
	var $file_code;
	var $filename;
	var $mimetype;
	private $filetitle;
	var $allowedExts;
	var $filesize;
		
	function __construct($pFieldTempl) {
		$this->m_state = 0;
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];
		
		$this->file_id = $pFieldTempl['fileid'];
		$this->file_code = $_POST['code'];
		$this->m_pubdata['fileid'] = $this->file_id;
	}
		
	function CheckVals() {
		if($this->m_state == 0) {
			$this->m_state++;
		} else {
			// NOTICE
		}
	}

	function GetData($pCode = 'null') {
		$this->CheckVals();
		
		$cn = Con();
		$lSqlStr = 'SELECT * from GetAttachment(' . $this->file_id . ', '. ($pCode ? '\''. $pCode .'\'' : 'null') .', null)';
		$cn->Execute($lSqlStr);
		$cn->MoveFirst();
		
		$accessArr = array();
		
		if (!$cn->Eof()) {
			
			$this->filename = 'oo_'. $cn->mRs['filename'];
			if ($cn->mRs['filetitle']) {
				$this->filetitle = $cn->mRs['filetitle'];
			} else {
				$this->filetitle = $this->filename;
			}	
			
			if ($cn->mRs['mediasize']) {
				$this->filesize = $cn->mRs['mediasize'];
			}
			
			$this->mimetype = $cn->mRs['mimetype'];

			$accessArr['access'] = $cn->mRs['access'];
			$accessArr['accesstype'] = $cn->mRs['accesstype'];
		}
		
		return $accessArr;
	}
	
	function GetRights($pAccess, $pAccessType) {
		// Access Type : 0 = svoboden, 1 = s kod, 2 = samo za abonati
		global $usr_obj;

		$abon_user = (int)$usr_obj->uid && (int)$usr_obj->grpid;
		
		if ($pAccessType == 0 || ($pAccessType == 1 && $pAccess) || ($pAccessType == 2 && $abon_user)) {
			$restrictType = 0;
		} elseif ($pAccessType == 1 && !$this->file_code) {
			$restrictType = 1;
		} elseif ($pAccessType == 1 && $this->file_code && !$pAccess) {
			$restrictType = 3;
		} else {
			$restrictType = 2;
		}
		
		return $restrictType; 	
		// return value: 0 = da, 1 = da (s kod), 2 = ne (no rights), 3 = ne (greshen kod)
	}
	
	function sendheaders($pType) {
		if ($pType == 'def') {
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			
			if (trim($this->mimetype)) {
				header('Content-type: ' . $this->mimetype);
			} else {
				header('Content-type: application/force-download');
			}
			if (trim($this->filesize)) {
				header('Content-Length: '. $this->filesize);
			}
			header('Content-Disposition: attachment; filename="'. $this->filetitle .'"');
			readfile(PATH_DL . $this->filename);
		} elseif ($pType == 'msg') {
			header('Content-type: text/html; charset=UTF-8');
		}
	}

	function Display() {
		$accessArr = $this->GetData($this->file_code);
		$restr = $this->GetRights($accessArr['access'], $accessArr['accesstype']);
		
		if (!$restr) {
			$this->sendheaders('def');
		} else {
			$this->sendheaders('msg');
			if ($restr == 1) {
				echo $this->ReplaceHtmlFields($this->getObjTemplate(G_ENTERCODE));
			} elseif ($restr == 2) {
				echo $this->ReplaceHtmlFields($this->getObjTemplate(G_ABONACCESS));
			} elseif ($restr == 3) {
				echo $this->ReplaceHtmlFields($this->getObjTemplate(G_WRONGCODE));
			}
		}
	}

}

?>