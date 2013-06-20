<?php
/**
 * A page view class which displays its arguments through json_encode
 * @author peterg
 *
 */
class epPage_Json_View extends epPage_View {
	public function Display() {
		$this->SetPageContentType('application/json');
		return json_encode($this->m_pubdata);
	}
	
	public function SetPubData($pPubdata){
		$this->m_pubdata = $pPubdata;
	}
}
?>