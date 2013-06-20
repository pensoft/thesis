<?php
class ctime_logger extends cbase_cachedata {
	var $m_Objects;//Тук пазим различните обекти - примерно лява колона, карта от гбиф и др.
	var $m_RegisteredEvents;//Това го ползваме за дебъг. Пази всички регистрирани ивенти.
	var $m_ObjectRegisteredEvents;//Тук ще пазим регистрираните ивенти за различните обекти за да може да се регистрира само първият ивент от всеки тип(при преизпълняване на ГетДата примерно)
	var $m_TaxonName;//Името на таксона, за който вадим информация
	var $m_LogFileHandle;//Handle към файла в който изваждаме лога
	function __construct($pFieldTempl) {
		$this->m_state = 0;
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);		
		$this->m_Objects = array();
		$this->m_ObjectRegisteredEvents = array();
	}
	
	function CheckVals() {		
	}
	
	function RegisterObject($pObjectId, $pObjectName, $pParentObjectId = 0, $pObjectParameters = array()){//Slagame nov obekt - primerno lqvata kolona sys saitovete		
		if( !isset($this->m_Objects[$pObjectId])){
			$this->m_Objects[$pObjectId] = array(
				'parameters' => $pObjectParameters,
				'object_name' => $pObjectName,
				'parent_objectid' => $pParentObjectId,
				'objectid' => $pObjectId,
				PROFILE_LOG_STARTED_EVENT => microtime(true),
				PROFILE_LOG_GOT_DATA_FROM_CACHE_EVENT => false,
				PROFILE_LOG_FINISHED_RETRIEVING_DATA_EVENT => '',
				PROFILE_LOG_FINISHED_PARSING_DATA_EVENT => '',
			);
			$this->m_ObjectRegisteredEvents[$pObjectId] = array(PROFILE_LOG_STARTED_EVENT);
		}
		$this->m_RegisteredEvents[] = array('id' => $pObjectId, 'ev' => PROFILE_LOG_STARTED_EVENT, 'time' => mktime());		
	}
	
	function RegisterObjectEvent($pObjectId, $pEvent, $pEventData = 0, $pDontPlaceDefaultDate = false){//Markirame nqkakyv event - primerno krai na vzimaneto na dannite
		if( $pEventData == 0 && !$pDontPlaceDefaultDate )
			$pEventData = microtime(true);
		if( isset($this->m_Objects[$pObjectId])){//Ako ima takyv obekt i ne sme markirali takova sybitie - markirame go
			/**Изрично гледаме дали не сме маркирали събитието, понеже е възможно ГетДата-та да се изпълни няколко пъти, от които само 1ят път е реалния.			
			*/
			if( !in_array( $pEvent, $this->m_ObjectRegisteredEvents[$pObjectId] ) ){
				$this->m_Objects[$pObjectId][$pEvent] = $pEventData;
				$this->m_ObjectRegisteredEvents[$pObjectId][] = $pEvent;				
			}
		}
		$this->m_RegisteredEvents[] = array('id' => $pObjectId, 'ev' => $pEvent, 'time' => microtime(true));
	}
	
	function GetDataC(){
	
	}
	
	function GetData() {		
	}
	
	function GetObjectLogMsg( $pObjectId ){//Vryshta syobshtenieto na obekta		
		if( !isset($this->m_Objects[$pObjectId]))
			return '';
		$pIp = $_SERVER['REMOTE_ADDR'];
		$lMsg = PROFILE_LOG_MSG_START_ESCAPE . 'IP: ' . PROFILE_LOG_DATA_VAL_ESCAPE . $pIp  . PROFILE_LOG_DATA_VAL_ESCAPE . ' ' 
		. PROFILE_LOG_OBJECTID_LABEL . ' ' . PROFILE_LOG_DATA_VAL_ESCAPE . $pObjectId . PROFILE_LOG_DATA_VAL_ESCAPE . ' ' 
		. PROFILE_LOG_OBJECT_NAME_LABEL . ' ' . PROFILE_LOG_DATA_VAL_ESCAPE . $this->m_Objects[$pObjectId]['object_name'] . PROFILE_LOG_DATA_VAL_ESCAPE  . ' ' 
		. PROFILE_LOG_OBJECT_PARENTID_LABEL . ' ' . PROFILE_LOG_DATA_VAL_ESCAPE . $this->m_Objects[$pObjectId]['parent_objectid'] . PROFILE_LOG_DATA_VAL_ESCAPE  . ' ' 
		. PROFILE_LOG_OBJECT_PARAMETERS_LABEL . ' ' . PROFILE_LOG_DATA_VAL_ESCAPE . var_export($this->m_Objects[$pObjectId]['parameters'], true) . PROFILE_LOG_DATA_VAL_ESCAPE . "\n";
		
		$lMsg .= "\t\t\t " . PROFILE_LOG_STARTED_LABEL . " " . PROFILE_LOG_DATA_VAL_ESCAPE . $this->parseDate($this->m_Objects[$pObjectId][PROFILE_LOG_STARTED_EVENT]) . PROFILE_LOG_DATA_VAL_ESCAPE . "\n";
		$lMsg .= "\t\t\t " . PROFILE_LOG_DATA_FROM_CACHE_LABEL . " " . PROFILE_LOG_DATA_VAL_ESCAPE . ($this->m_Objects[$pObjectId][PROFILE_LOG_GOT_DATA_FROM_CACHE_EVENT] ? 'Yes' : 'No') . PROFILE_LOG_DATA_VAL_ESCAPE . "\n";
		$lMsg .= "\t\t\t " . PROFILE_LOG_FINISHED_RETRIEVING_LABEL . " " . PROFILE_LOG_DATA_VAL_ESCAPE . $this->parseDate($this->m_Objects[$pObjectId][PROFILE_LOG_FINISHED_RETRIEVING_DATA_EVENT]) . PROFILE_LOG_DATA_VAL_ESCAPE . " - " . PROFILE_LOG_DATA_VAL_ESCAPE . number_format($this->m_Objects[$pObjectId][PROFILE_LOG_FINISHED_RETRIEVING_DATA_EVENT] - $this->m_Objects[$pObjectId][PROFILE_LOG_STARTED_EVENT], 10) . PROFILE_LOG_DATA_VAL_ESCAPE . " second(s) \n";
		$lMsg .= "\t\t\t " . PROFILE_LOG_FINISHED_PARSING_LABEL . " " . PROFILE_LOG_DATA_VAL_ESCAPE . $this->parseDate($this->m_Objects[$pObjectId][PROFILE_LOG_FINISHED_PARSING_DATA_EVENT]) . PROFILE_LOG_DATA_VAL_ESCAPE . " - " . PROFILE_LOG_DATA_VAL_ESCAPE . number_format($this->m_Objects[$pObjectId][PROFILE_LOG_FINISHED_PARSING_DATA_EVENT] - $this->m_Objects[$pObjectId][PROFILE_LOG_FINISHED_RETRIEVING_DATA_EVENT], 10). PROFILE_LOG_DATA_VAL_ESCAPE . " second(s) \n";
		$lMsg .= "\n\n" . PROFILE_LOG_MSG_END_ESCAPE;
		return $lMsg;
	}
	
	function RegisterTaxon($pTaxonName){
		$this->m_TaxonName = $pTaxonName;
	}
	
	function Display() {
		if( !(int) DEBUG_PROFILE_LOG )
			return;
		$lMsg = '';
		$lParsedObjects = array();		
		if( defined('PROFILE_LOG_FILE' ) ){
			$this->m_LogFileHandle = fopen(PROFILE_LOG_FILE, 'w');
		}
		$pIp = $_SERVER['REMOTE_ADDR'];
		$lCurrentDate = $this->ParseDate(microtime(true));
		
		$lWholeMsg = "\n\n" . PROFILE_LOG_START_ESCAPE . PROFILE_LOG_START_TAXON_LABEL . PROFILE_LOG_DATA_VAL_ESCAPE . $this->m_TaxonName . PROFILE_LOG_DATA_VAL_ESCAPE 
			. PROFILE_LOG_START_FROM_IP_LABEL . PROFILE_LOG_DATA_VAL_ESCAPE . $pIp . PROFILE_LOG_DATA_VAL_ESCAPE 
			. PROFILE_LOG_START_ON_DATE_LABEL . PROFILE_LOG_DATA_VAL_ESCAPE . $lCurrentDate . PROFILE_LOG_DATA_VAL_ESCAPE . PROFILE_LOG_START_ESCAPE . "\n\n";
			
		foreach( $this->m_Objects as $lObjectId => $lObjectData ){			
			$lMsg = $this->GetObjectLogMsg($lObjectId);
			foreach( $this->m_Objects as $lChildObjectId => $lChildObjectData ){//Parsvame sled obekta i decata mu
				if( in_array( $lChildObjectId, $lParsedObjects ) || $lChildObjectData['parent_objectid'] != $lObjectId)
					continue;
			}
			$lWholeMsg .= $lMsg;			
			foreach( $this->m_RegisteredEvents as $lEvent ){
				if( $lEvent['id'] == $lObjectId ){
					$lWholeMsg .= $lEvent['ev'] . ' ' . $this->ParseDate($lEvent['time']) . "\n";					
				}
			}
			
		}
		$lWholeMsg .= "\n\n" . PROFILE_LOG_END_ESCAPE . PROFILE_LOG_END_TAXON_LABEL . PROFILE_LOG_DATA_VAL_ESCAPE . $this->m_TaxonName . PROFILE_LOG_DATA_VAL_ESCAPE 
		. PROFILE_LOG_END_FROM_IP_LABEL . PROFILE_LOG_DATA_VAL_ESCAPE . $pIp . PROFILE_LOG_DATA_VAL_ESCAPE 
		. PROFILE_LOG_END_ON_DATE_LABEL . PROFILE_LOG_DATA_VAL_ESCAPE . $lCurrentDate . PROFILE_LOG_DATA_VAL_ESCAPE . PROFILE_LOG_END_ESCAPE . "\n\n";
		
		
		/**
			Изкарваме съобщенията наведнъж за да сме сигурни, че ще са последователни във файла (за всеки случай).
		*/
		$this->ReportMessage($lWholeMsg);
		if( $this->m_LogFileHandle ){
			fclose($this->m_LogFileHandle);
		}
		
	}
	
	function ParseDate($pDate){//Работим с DateTime обект за да може да изкараме микросекундите - date() работи само със цели секунди
		$lMicroSeconds = (int)(($pDate - floor($pDate)) * 1000000);
		$lDateTime = new DateTime( date('Y-m-d H:i:s.' . $lMicroSeconds, (int)$pDate) );
		return $lDateTime->format('d M Y H:i:s.u');
	}
	
	function ReportMessage($pMsg){
		trigger_error(str_replace("\t", '    ', $pMsg), E_USER_NOTICE);
		if( $this->m_LogFileHandle ){
			fwrite($this->m_LogFileHandle, $pMsg);
		}
	}

}