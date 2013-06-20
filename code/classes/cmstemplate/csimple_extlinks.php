<?php
class csimple_extlinks extends cbase_cachedata {
	var $m_dontgetdata;
	function __construct($pFieldTempl) {
		$this->m_state = 0;
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];
	}
	
	function CheckVals() {
		if($this->m_state == 0) {
			$this->m_state++;
		} else {
			// NOTICE
		}
	}
	
	function GetData() {		
		$this->CheckVals();
		if ($this->m_state==1) {
			$this->m_state++;
			$lDefLinks = GetLinksArray($this->m_pubdata['taxon_name'], !$this->m_pubdata['content_is_static']);
			if($this->m_pubdata['content_is_static']){//Ako sydyrjanieto e statichno
				//Za vseki ot linkovete slagame propertyto dont_check_for_existence i slagame propertyto che sa namereni rezultati 
				foreach($lDefLinks as $lUrl => &$lUrlData){
					$lUrlData['dont_check_for_existence'] = true;
					$lUrlData['results_exist'] = true;
				}
			}
			foreach($this->m_pubdata['menus'] as $lMenuName => $lMenuData){
				$lMenu = new clinksmenu(array(
					'label' => $lMenuData['label'],
					'links' => $lMenuData['links'],
					'templs' => $this->m_pubdata['menus_templs'],
					'taxon_name' => $this->m_pubdata['taxon_name'],
					'ajax_link_template_type' => (int)$lMenuData['ajax_link_template_type'],
					'cache' => 'taxon_linksmenu',
					'cachetimeout' => CACHE_TIMEOUT_LENGTH,
					'def_links' => $lDefLinks,
				));
				$lMenu->SetParentLoggerObjectId($this->m_logger_object_id);
				$lMenu->GetDataC();//Slagame go za da moje da se vzima ot nego informaciq prez GetMenuLinkHref
				$this->m_pubdata[$lMenuName] = $lMenu;
			
			}
			$this->RegisterLoggerObjectEvent(PROFILE_LOG_FINISHED_RETRIEVING_DATA_EVENT);	
			$this->m_recordCount = 1;
		} else {
			// NOTICE
		}
		$this->m_dontgetdata = true;
	}
	
	function GetMenuLinkHref($pMenuName, $pLinkName){
		$lMenu = $this->m_pubdata[$pMenuName];
		if( !$lMenu instanceof clinksmenu ){
			return;
		}
		return $lMenu->GetLinkHref($pLinkName);
	}
	
	function Display() {
		if (!$this->m_dontgetdata)
			$this->GetDataC();
		
		if ($this->m_state < 2) {
			return;
		}
		
		//~ if (!$this->m_HTempl) {
			//~ trigger_error("There is no template", E_USER_WARNING);
			//~ return;
		//~ }
		
		//replacvame v templat-a
		
		return $this->ReplaceHtmlFields($this->getObjTemplate(G_DEFAULT));
	}
	
}
?>