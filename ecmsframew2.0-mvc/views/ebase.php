<?php
abstract class ebase {
	protected $m_state; // state na obekt-a
	protected $m_pubdata; // stoinosti razni


	var $enablecache ;//da go priravniavame


	abstract public function ProcessData();
	abstract public function Display();


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

	public function ValExists($pKey) {
		return array_key_exists($pKey, $this->m_pubdata);
	}

	public function SetVal($pKey, $pVal) {
		return $this->m_pubdata[$pKey] = $pVal;
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
	/**
	 * Returns whether the cachefile is valid (recent enough) or not.
	 */
	function getCacheTimeout() {
		if ((int) $this->m_pubdata['cachetimeout']) {
			$lTimeOut = (int) $this->m_pubdata['cachetimeout'];
		} else {
			$lTimeOut = 60*60;
		}
			if ((mktime() - filemtime(PATH_CACHE . $this->cachefilename) > $lTimeOut)
				&& filemtime(PATH_CACHE . $this->cachefilename) != 1) {
				touch(PATH_CACHE . $this->cachefilename, 1);
				//~ echo 'timedout';
				return 0;
			}
			return 1;
	}

	function getCacheFn() {
		if (isset($this->m_pubdata['cache']) && !defined('DISABLE_CACHE')) {
			$this->cachefilename = $this->objname();
			return true;
		}
		return false;
	}

	/**
	 * Returns the name name of the cache file for the specified object
	 * @return string
	 */
	function objname() {
		// classname_subclass_0_1_2_3_4_5_6_7_8
		// cachegrp_class_parentclass_uniqid
		return $this->m_pubdata['cache'] . '_' . get_class($this) . '_' . get_parent_class($this) . '_' . sprintf("%x", $this->getPubdataHash());
	}

	/**
	 * returns a crc32 polynomial of the pubdata
	 */
	function getPubdataHash(){
		return crc32(serialize($this->m_pubdata));
	}

	/**
	 * Returns whether the cache file for this object exists or not
	 * @return boolean
	 */
	function getCacheExists() {
		return file_exists(PATH_CACHE . $this->cachefilename);
	}

	/**
	 * Returns the content of the cachefile for this object
	 */
	function getCacheContents() {
		return file_get_contents(PATH_CACHE . $this->cachefilename);
	}

	/**
	 * Writes the passed data in the cachefile for this object
	 * @param unknown_type $contents
	 */
	function saveCacheContents($contents) {
		file_put_contents(PATH_CACHE . $this->cachefilename, $contents);
	}

}
?>