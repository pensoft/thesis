<?php
abstract class cbase {
	protected $m_state; // state na obekt-a
	protected $m_pubdata; // stoinosti razni
	protected $m_keyvnames; // 
	protected $m_formerrflag = 0; //tva e da pokazva koga ima greshka vuv validaciata na formata
	protected $m_templfile = "tcpage.php";
	var $m_antets_arr;//da go priravniavame 
	var $enablecache ;//da go priravniavame 
	
	abstract public function CheckVals();
	abstract public function GetData();
	abstract public function Display();
	
	function CheckVal($pVal, $pErrMsg, $pValType) {
		switch ($pValType) {
			case "date" :
				if (!ckdt($pVal)) {
					$this->m_pubdata['formerr'] .= $pErrMsg;
					$this->m_formerrflag++;
				}
				break;
			case "uname" :
				if ($pVal == "" || !$pVal) {
					$this->m_pubdata['formerr'] .= $pErrMsg;
					$this->m_formerrflag++;
				}
				else {
					if (preg_match('/[^\w\d\_]/', $pVal)) {
						$this->m_pubdata['formerr'] .= $pErrMsg;
						$this->m_formerrflag++;
					}
				}
				break;
			case "email" :
				if (!CheckMail($pVal)) {
					$this->m_pubdata['formerr'] .= $pErrMsg;
					$this->m_formerrflag++;
				}
				break;
			default :
				if ($pVal == "" || !$pVal) {
					$this->m_pubdata['formerr'] .= $pErrMsg;
					$this->m_formerrflag++;
				}
		}
	}
	
	public function EncodeUrl($url, $pTranslate = false) {
		$url = preg_replace("/\[(.*?)\]/e", "\$this->HtmlPrepare('\\1')", $url);
		if (!defined('ENABLE_MOD_REWRITE') || (int)ENABLE_MOD_REWRITE == 0) {
			return $url;
		}
		if ($this->m_pubdata['_rewrite_'] instanceof crewrite) {
			return $this->m_pubdata['_rewrite_']->EncodeUrl($url, $pTranslate);
		}
		return $url;
	}
	
	public function GetVal($pKey) {
		return $this->m_pubdata[$pKey];
	}
	
	public function SetVal($pKey, $pVal) {
		return $this->m_pubdata[$pKey] = $pVal;
	}

	protected function ReplaceHtmlFields($pStr) {
		return preg_replace("/\{(.*?)\}/e", "\$this->HtmlPrepare('\\1')", $pStr);
	}
	
	function DisplayTemplate($pTemplId) {
		$lmas = $this->getTemplate($pTemplId);
		return $this->ReplaceHtmlFields($lmas);
	}

	function EvalHtmlTemplateFunction($pName) {
		if (!preg_match('/^\_(.*)\((.*)\)$/', $pName, $lMas)) {
			return '';
		}
		$lFuncname = $lMas[1];
		$lFuncParams = split(',', stripslashes($lMas[2]));
		
		$lStrFuncParams = '';
		
		foreach ($lFuncParams as $k => $v) {
			$l = trim($v);
			
			if (is_array($this->m_currentRecord) && array_key_exists($l, $this->m_currentRecord)) {
				$lStrFuncParams .= var_export($this->m_currentRecord[$l], true) . ',';
			} else if (is_array($this->m_pubdata) && array_key_exists($l, $this->m_pubdata)) {
				$lStrFuncParams .= var_export($this->m_pubdata[$l], true) . ',';
			} else {
				$lStrFuncParams .= var_export($l, true) . ',';
			}
		}
		if ($lStrFuncParams) {
			$lStrFuncParams = substr($lStrFuncParams, 0, -1);
		}
		$evalstr = 'return ' . $lFuncname . '(' . $lStrFuncParams . ');';
		return eval($evalstr);
	}
	
	function EvalHtmlTemplateMethod($pName) {
		if (!preg_match('/^\$(.*)\((.*)\)$/', $pName, $lMas)) {
			return '';
		}
		$lFuncname = $lMas[1];
		$lFuncParams = split(',', stripslashes($lMas[2]));
		$lStrFuncParams = '';
		
		foreach ($lFuncParams as $k => $v) {
			$l = trim($v);
			
			if (is_array($this->m_currentRecord) && array_key_exists($l, $this->m_currentRecord)) {
				$lStrFuncParams .= var_export($this->m_currentRecord[$l], true) . ',';
			} else if (is_array($this->m_pubdata) && array_key_exists($l, $this->m_pubdata)) {
				$lStrFuncParams .= var_export($this->m_pubdata[$l], true) . ',';
			} else {
				$lStrFuncParams .= var_export($l, true) . ',';
			}
		}
		if ($lStrFuncParams) {
			$lStrFuncParams = substr($lStrFuncParams, 0, -1);
		}
		$evalstr = 'return $this->' . $lFuncname . '(' . $lStrFuncParams . ');';
		return eval($evalstr);
	}

	protected function HtmlPrepare($pName) {
		global $gAntetsArr;//tva e nai-globalnia masiv s antetkite
		$lRetStr = '';
		if ($pName[0] == '_') {
			$lRetStr = $this->EvalHtmlTemplateFunction($pName);
		} elseif ($pName[0] == '$') {
			$lRetStr = $this->EvalHtmlTemplateMethod($pName);
		} else if ($pName[0] == '*') {
			$lRetStr = $this->DisplayTemplate(substr($pName, 1));
		} else if ($pName[0] == '%') {//antetka
			if (!$_SESSION['glang']) $_SESSION['glang'] = 1;//ako se polzva ot drug site(naprimer ot administraciata) da ne predavame ezik
			$lRetStr = $gAntetsArr[substr($pName, 1)];
		} else if (isset($this->m_pubdata[$pName])) {
			if (is_object($this->m_pubdata[$pName])) {
				// AKO E OBEKT
				$lRetStr = $this->m_pubdata[$pName]->DisplayC();
			} elseif (is_array($this->m_pubdata[$pName])) {
				// AKO E MASIV - se edno e definiciq za obekt
				if (!$this->m_pubdata[$pName]['ob'])
					$this->m_pubdata[$pName]['ob'] = new $this->m_pubdata[$pName]['ctype']($this->m_pubdata[$pName]);
				$lRetStr = $this->m_pubdata[$pName]['ob']->DisplayC();
				
			} elseif (is_scalar($this->m_pubdata[$pName])) {
				// AKO E SKALAR
				$lRetStr = $this->m_pubdata[$pName];
				
			} else {
				// AKO E DRUGO ? kvo drugo ima - ima nqkvi laina - trebe moje bi da praim warning
				
				$lRetStr = '';
			}
		}
		
		return $lRetStr;
	}
	
	function getTemplate($pTemplName) {
		global $gTemplatesArray;
		if ($gTemplatesArray) {
			if (array_key_exists($pTemplName, $gTemplatesArray)) {
				return $gTemplatesArray[$pTemplName];
			}
		}
		
		$lTmp = '';
		$lArr = array();
		if (strstr($pTemplName,'.')) {
			$lArr = explode('.', $pTemplName);
		}
		
		$lExtSite = '';
		
		if (count($lArr) == 3) {
			
			$lSiteName = $lArr[0];
			$lTmp = $lArr[1];
			$lExtSite = $lSiteName . '.';
			
		} elseif (count($lArr) == 2) {
			
			$lSiteName = SITE_NAME;
			$lTmp = $lArr[0];
			
		} else {
			$lSiteName = SITE_NAME;
			$lTmp = 'tcpage';
		}
		
		$lFileName = PATH_CLASSES . $lSiteName . '/templates/' . $lTmp . '.php';
		
		if (file_exists($lFileName)) {
			require_once($lFileName);
		} elseif (file_exists(PATH_CLASSES . '/templates/' . $lTmp . '.php')) {
			require_once(PATH_CLASSES . '/templates/' . $lTmp . '.php');
		} else {
			trigger_error("The file <b>\"$lFileName\"</b> does not exist [$pTemplName] !!!" . "\n", E_USER_NOTICE);
		}
		
		if (!is_array($gTemplArr)) {
			$gTemplArr = array();
		}
		
		foreach ($gTemplArr as $k => $v) {
			$gTemplatesArray[$lExtSite . $k] = $v;
		}
		
		if (!array_key_exists($pTemplName, $gTemplatesArray)) {
			trigger_error("<b>\"$pTemplName\"</b> not found !!!" . "\n", E_USER_NOTICE);
		}
		
		return $gTemplatesArray[$pTemplName];	
	}
	
	function getObjTemplate($pTemplId, $templadd="") {
		if (!is_array($this->m_Templs) || !array_key_exists($pTemplId, $this->m_Templs)) {
			if (is_array($this->m_defTempls)) {
				if (!array_key_exists($pTemplId, $this->m_defTempls)) {
					trigger_error("Template " . $pTemplId . " does not exist in the object.");//TUKA TREBE DA SE VZIMAT DEFAULTNI TEMPLS
				}
				else {
					return $this->getTemplate($this->m_defTempls[$pTemplId].$templadd);
				}
			}
		}
		else {
			return $this->getTemplate($this->m_Templs[$pTemplId].$templadd);
		}
	}
	
	function DisplayC() {
		$enablecache = $this->getCacheFn();
		if ($enablecache && $this->getCacheExists() && $this->getCacheTimeout()) {
			return $this->getCacheContents();
		}
		
		$lRet = $this->Display();
		
		if ($enablecache) {
			$this->saveCacheContents($lRet);
		}
		
		return $lRet;
	}
	
	function getCacheTimeout() {
		if ((int) $this->m_pubdata['cachetimeout']) {
			$lTimeOut = (int) $this->m_pubdata['cachetimeout'];
		} else $lTimeOut = 60*60;
		if ((mktime() - filemtime(PATH_CACHE . $this->cachefilename) > $lTimeOut)
			&& filemtime(PATH_CACHE . $this->cachefilename) != 1) {
			touch(PATH_CACHE . $this->cachefilename, 1);
			//~ echo 'timedout';
			return 0;
		}
		return 1;
	}
	
	function getCacheFn() {
		if ($this->m_pubdata['cache'] && !defined('DISABLE_CACHE')) {
			$this->cachefilename = $this->objname();
			return true;
		}
		return false;
	}
	
	function objname() {
		// classname_subclass_0_1_2_3_4_5_6_7_8
		// cachegrp_class_parentclass_uniqid
		return $this->m_pubdata['cache'] . '_' . get_class($this) . '_' . get_parent_class($this) . '_' . sprintf("%x", crc32(serialize($this->m_pubdata)));
	}
	
	
	function getCacheExists() {
		return file_exists(PATH_CACHE . $this->cachefilename);
	}
	
	function getCacheContents() {
		return file_get_contents(PATH_CACHE . $this->cachefilename);
	}
	
	function saveCacheContents($contents) {
		file_put_contents(PATH_CACHE . $this->cachefilename, $contents);
	}
	
}
?>