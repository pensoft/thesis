<?php
class cmessaging extends cbase {
	function __construct($pFieldTempl) {
		$this->m_state = 0;
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];
		
		$this->con = new DBCn;
		$this->con->Open();
		if (!is_array($this->m_pubdata['from'])) {
			$this->m_pubdata['from']['email'] = "karieri@karieri.bg";//ideiata e che v konkretnite klasovete mojesh da si replace-nesh tezi stoinosti
			$this->m_pubdata['from']['display'] = "KARIERI.BG";
		}
		$this->m_templfile = "tmessaging.php";
	}
	
	function CheckVals() {
	}
	
	function GetData() {
		$this->m_state = 2;
	}
	
	function SaveData() {
		$lSqlStr = "SELECT * FROM AddToMessaging(" . sqlnull($this->m_pubdata['from']['display'], 1) . ", " . sqlnull($this->m_pubdata['mailto'], 1) . ", " . sqlnull($this->m_pubdata['mailsubject'], 1) . ", current_timestamp::timestamp) as mid";
		//echo $lSqlStr;
		$this->con->execute($lSqlStr);
		$this->con->MoveFirst();
		if (!$this->con->Eof()) {
			$this->m_pubdata['mid'] = $this->con->mRs['mid'];
			$this->m_state = 3;
		}
	}
	
	function SaveMailfile($pContents) {
		//tuka nekvi dopulnitelni raboti shte ni trebat za generirane na samia mail
		$preferences = array(
			"input-charset" => "UTF-8",
			"output-charset" => "UTF-8",
			"line-break-chars" => "\n"
		);
		
		$lEncodedSubject = iconv_mime_encode("Subject", $this->m_pubdata['mailsubject'], $preferences);
		$lHeaders = "To: " . $this->m_pubdata['mailto'] . "\n";
		$lHeaders .= "From: " . $this->m_pubdata['from']['display'] . " <" . $this->m_pubdata['from']['email'] . ">" . "\n";
		$lHeaders .= ($this->m_pubdata['replyto'] != '' ? "Reply-To: " . $this->m_pubdata['replyto'] . "\n" : '');
		$lHeaders .= $lEncodedSubject . "\n";
		if ($this->m_pubdata['bcc'])
		$lHeaders .= "Bcc: " . $this->m_pubdata['bcc'] . "\n";
		
		if ($this->m_pubdata['cc']){
			$lHeaders .= "Cc: " . $this->m_pubdata['cc'] . "\n";	
		}
		$lHeaders .= "MIME-Version: 1.0\n";
				
		if (is_array($this->m_pubdata['attachment'])) {
		
			$lBoundary = "--_separator==_";
			$lHeaders .= "Content-type: multipart/mixed;boundary=\"$lBoundary\"\n\n";
			$lHeaders .= "If you are reading this, then your e-mail client does not support MIME.\n";
			$lHeaders .= "\n--$lBoundary\n";
			$lHeaders .= "Content-Type: text/html; charset=\"UTF-8\" \n";
			$lHeaders .= "Content-Transfer-Encoding: base64\n\n";
			$lHeaders .= chunk_split(base64_encode($pContents));
			$lHeaders .= "\n--$lBoundary\n";
			
			
			if($this->m_pubdata['attachment']['file']) { //ako ima file, go loadvame i mu vzimame content type-a 
				if( is_array( $this->m_pubdata['attachment']['file'] ) ) { 
					foreach( $this->m_pubdata['attachment']['file'] as $file ){ 
						$lExecRet = exec("file -i " . $file['file'], $lRetArr);
						$lContentType = substr($lExecRet, strlen($file['file']) + 2);
						$lContent = file_get_contents($file['file']);
						$lAttContentType = ($lContentType ? $lContentType : "text/html");

						$lHeaders .= $this->stickAtt( $lAttContentType, $file['filename'], $lContent, $lBoundary );
					}
				} else {
					$lExecRet = exec("file -i " . $this->m_pubdata['attachment']['file'], $lRetArr);
					$this->m_pubdata['attachment']['contenttype'] = substr($lExecRet, strlen($this->m_pubdata['attachment']['file']) + 2);
					$this->m_pubdata['attachment']['contents'] = file_get_contents($this->m_pubdata['attachment']['file']);
					$lAttContentType = ($this->m_pubdata['attachment']['contenttype'] ? $this->m_pubdata['attachment']['contenttype'] : "text/html");
					$lHeaders .= $this->stickAtt( $lAttContentType, $this->m_pubdata['attachment']['filename'], $this->m_pubdata['attachment']['contents'], $lBoundary );
				}

			}
			
		} else {
			$lHeaders .= "Content-Type: text/html; charset=\"UTF-8\"\n";
			$lHeaders .= "Content-Transfer-Encoding: base64\n";
			$lHeaders .= "\n";
			$lHeaders .= chunk_split(base64_encode($pContents));
		}
		//$lFRet = file_put_contents(PATH_MESSAGING . $this->m_pubdata['mid'] . ".txt", $lHeaders . chunk_split(base64_encode($pContents)));
		$lFRet = file_put_contents(PATH_MESSAGING . $this->m_pubdata['mid'] . ".txt", $lHeaders);
		
		if ($lFRet) 
			$this->con->execute("UPDATE messaging SET state = 0 WHERE id = " . $this->m_pubdata['mid']);
	}
	
	function stickAtt( $pAttContentType, $pFilename, $pContent, $lBoundary ){
		$lHeaders = '';
			$lHeaders .= "Content-Type: " . $pAttContentType . "\n";
			$lHeaders .= "Content-Transfer-Encoding: base64\n";
			$lHeaders .= "Content-Disposition: attachment;filename=\"" . $pFilename . "\"\n\n";
			$lHeaders .= chunk_split(base64_encode( $pContent ));
			$lHeaders .= "\n--$lBoundary\n";
		return $lHeaders;
	}
	
	function Display() {
		$this->GetData();
		
		if ($this->m_state != 2) {
			return 0;
		}
		
		$this->SaveData();
		
		if ($this->m_state != 3) {
			return 0;
		}		
		
		$lRet = $this->ReplaceHtmlFields($this->getObjTemplate(G_DEFAULT));
		
		$this->SaveMailfile($lRet);
		return 1;
	}
	
	function SetPubData($pArr) { 
		foreach ($pArr as $k => $v) {
			$this->m_pubdata[$k] = $v;
		}
	}
}
?>