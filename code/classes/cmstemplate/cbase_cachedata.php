<?php
$gObjectIdCounter = 0;
function our_spl_object_hash($pObj){//Vryshta unikalno id za obekta obj. Ne polzvame vgradenoto spl_object_hash poneje e bygavo
	global $gObjectIdCounter;
	if( !$pObj )
		return;
	if( $pObj->m_logger_object_id ){
		return $pObj->m_logger_object_id;
	}
	$gObjectIdCounter++;
	return $gObjectIdCounter;
}

abstract class cbase_cachedata extends cbase {	
	var $cachedatafilename;	
	var $m_got_data_from_cache;
	var $m_logger_object_id;
	var $m_parent_logger_object_id = 0;
	var $m_recordCount;
	var $m_AjaxRequiredObjectIds = array();
	
	function AddNewAjaxRequiredObjectId($pObjectId){
		$this->m_AjaxRequiredObjectIds[] = $pObjectId;
	}
	
	function SetParentLoggerObjectId($pObjectId){
		$this->m_parent_logger_object_id = $pObjectId;
	}
	
	function GetResultCount(){
		return (int) $this->m_recordCount;
	}
	
	function GetDataC() {//Razdelqme funkciqta na 3 chasti za da moje po-lesno da se nasledqva
		//~ trigger_error('##########' . get_class($this));		
		$lEnableCache = $this->GetDataCStepOne();
		
		$lDataGotFromCache = $this->GetDataCStepTwo($lEnableCache);
		if( $lDataGotFromCache )
			return;
		$this->GetDataCStepThree($lEnableCache);
	}
	
	function GetDataCStepOne(){//Registrira obekta v loggera i vryshta dali se keshira
		$this->RegisterLoggerObject();
		$this->m_got_data_from_cache = false;
		return $this->getCacheFn();
	}
	
	function GetDataCStepTwo($pEnableCache){//Opredelq dali se zimat dannite ot kesha - pri uspeh vryshta true inache false;
		if ($pEnableCache && $this->getDataCacheExists() && $this->getDataCacheTimeout()) {
			$lCacheData = unserialize($this->getDataCacheContents());
			/** 
				Ако има намерени резултати, или ако няма намерени резултати
				но кешът е прекалено нов - взимаме данните от кеша.
				
				Ако няма намерени резултати и кешът е по-стар от времето за кеш на обект без резултати
				(времето за кеш разделено на определена константа) - 
				генерираме кеша отново.
			*/
			if( (int) $lCacheData['recordCount'] || $this->getDataCacheNoResultTimeout()){
				$this->m_recordCount = $lCacheData['recordCount'];
				$this->m_pubdata = $lCacheData['pubdata'];
				$this->m_got_data_from_cache = true;
				$this->m_dontgetdata = true;
				$this->RegisterLoggerObjectEvent(PROFILE_LOG_FINISHED_RETRIEVING_DATA_EVENT);
				$this->RegisterLoggerObjectEvent(PROFILE_LOG_GOT_DATA_FROM_CACHE_EVENT, $this->m_got_data_from_cache);
				$this->RegisterLoggerObjectEvent(PROFILE_LOG_FINISHED_PARSING_DATA_EVENT);	
				$this->m_state = 2;
				//~ echo get_class($this) . 'DATA GOT FROM CACHE <br/>';
				return true;
			}
		}
		return false;
	}
	
	function GetDataCStepThree($pEnableCache){//Ako dannite ne se vzimat ot kesha - te se generirat i pri nujda se keshirat
		//~ echo get_class($this) . 'DATA GENERATED <br/>';
		
		$this->GetData();
		$this->RegisterLoggerObjectEvent(PROFILE_LOG_GOT_DATA_FROM_CACHE_EVENT, $this->m_got_data_from_cache, true);
		$this->RegisterLoggerObjectEvent(PROFILE_LOG_FINISHED_PARSING_DATA_EVENT);
		
		if ($pEnableCache) {
			//~ $this->getCacheFn();
			$lCacheData = array(
				'pubdata' => $this->m_pubdata,
				'recordCount' => $this->m_recordCount,
			);
			$this->saveDataCacheContents(serialize($lCacheData));
		}
	}
	
	function RegisterLoggerObject(){
		global $gTimeLogger;
		$this->m_logger_object_id = our_spl_object_hash($this);
		//~ trigger_error($this->m_logger_object_id . ' ' . get_class($this) );
		if(!$gTimeLogger instanceof ctime_logger ){		
			trigger_error('NO TIME LOGGER', E_USER_NOTICE);
			return;
		}
		$gTimeLogger->RegisterObject($this->m_logger_object_id, get_class($this), $this->m_parent_logger_object_id, $this->m_pubdata);		
	}
	
	function RegisterLoggerObjectEvent($pEvent, $pEventData = 0, $pDontPlaceDefaultDate = false){
		if( $pEventData == 0 && !$pDontPlaceDefaultDate )
			$pEventData = microtime(true);
		global $gTimeLogger;
		if( !($gTimeLogger instanceof ctime_logger ) )
			return;
		$gTimeLogger->RegisterObjectEvent($this->m_logger_object_id, $pEvent, $pEventData, true);
	}
	
	function getCacheTimeoutBase($pFile){
		$lTimeOut = $this->getCacheTimeoutPeriod();
		return $this->checkIfCacheIsValid($pFile, $lTimeOut);
	}
	
	/**
		Проверява дали кеша е валиден, т.е. създаден е преди по-малко от $pTimeout секунди.
		Ако кеша е невалиден трие съдържанието му.
	*/
	function checkIfCacheIsValid( $pFile, $pTimeout ){
		if ((mktime() - filemtime(PATH_CACHE . $pFile) > $pTimeout)
			&& filemtime(PATH_CACHE . $pFile) != 1) {
			touch(PATH_CACHE . $pFile, 1);
			//~ echo 'timedout';
			return 0;
		}
		return 1;
	}
	
	//Връща периода (в секунди), за който кеша е валиден. Ако няма зададен такъв параметър по подразбиране - 1 час (3600 сек).
	function getCacheTimeoutPeriod(){
		if ((int) $this->m_pubdata['cachetimeout']) {
			$lTimeOut = (int) $this->m_pubdata['cachetimeout'];
		} else $lTimeOut = 60*60;
		return $lTimeOut;
	}
	
	function getCacheTimeout() {
		return $this->getCacheTimeoutBase($this->cachefilename);
	}
	
	function getDataCacheTimeout() {		
		return $this->getCacheTimeoutBase($this->cachedatafilename);
	}
	
	/**
		Понеже искаме кеша на обектите, които не са намерили резултат да се генерира по-често, 
		разделяме периода на активност на кеша на определена константа.
		
		Ако константата е по-малка от едно - кеша се генерира по-често. Ако е по-голяма от 1 - кеша се генерира по-рядко. 
		Ако константата е неположителна - не правим нищо и си работим директно с параметъра
	*/
	function getDataCacheNoResultTimeout(){
		$lCacheFile = $this->cachedatafilename;
		$lTimeOut = $this->getCacheTimeoutPeriod();
		
		
		$lCacheDivisor = (float) NO_RESULT_CACHE_DIVISOR;
		if( $lCacheDivisor <= 0 )
			$lCacheDivisor = 1;
		$lTimeOut = (int) ($lTimeOut / $lCacheDivisor);
		
		return $this->checkIfCacheIsValid($lCacheFile, $lTimeOut);
	}
	
	function getCacheFn() {
		if ($this->m_pubdata['cache'] && (!defined('DISABLE_CACHE') || !(int)DISABLE_CACHE)) {
			$this->cachefilename = $this->objname();
			$this->cachedatafilename = $this->dataobjname();
			return true;
		}
		return false;
	}
	
	function objname() {
		// classname_subclass_0_1_2_3_4_5_6_7_8
		// cachegrp_class_parentclass_uniqid
		return $this->m_pubdata['cache'] . '_' . get_class($this) . '_' . get_parent_class($this) . '_' . sprintf("%x", crc32(serialize($this->m_pubdata)));
	}
	
	function dataobjname() {
		// classname_subclass_pubdata 0_1_2_3_4_5_6_7_8		
		//~ var_dump(serialize($this->m_pubdata));
		return $this->m_pubdata['cache'] . '_' . get_class($this) . '_' . get_parent_class($this) . '_pubdata_' . sprintf("%x", crc32(serialize($this->m_pubdata)));
	}
	
	function getCacheExistsBase($pFile) {
		return file_exists(PATH_CACHE . $pFile);
	}
	
	function getCacheExists() {
		return $this->getCacheExistsBase($this->cachefilename);
	}

	function getDataCacheExists(){
		return $this->getCacheExistsBase($this->cachedatafilename);
	}
	
	function getCacheContentsBase($pFile) {
		return file_get_contents(PATH_CACHE . $pFile);
	}
	
	function getCacheContents() {
		return $this->getCacheContentsBase($this->cachefilename);		
	}
	
	function getDataCacheContents(){
		return $this->getCacheContentsBase($this->cachedatafilename);
	}
	
	function saveCacheContentsBase($contents, $pFile) {
		file_put_contents(PATH_CACHE . $pFile, $contents);
	}
	
	function saveCacheContents($contents) {
		$this->saveCacheContentsBase($contents, $this->cachefilename);
	}
	
	function saveDataCacheContents($contents){
		$this->saveCacheContentsBase($contents, $this->cachedatafilename);
	}
}
?>