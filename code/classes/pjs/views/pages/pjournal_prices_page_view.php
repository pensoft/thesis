<?php

/**
 * The view class for the journals prices page
 */
class pJournal_Prices_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.dashboard'
		);
		
		$this->m_objectsMetadata['prices_list_templs'] = array(
			'templs' => array(
				G_HEADER => 'prices.head',
				G_STARTRS => 'prices.list_start',
				G_ROWTEMPL => 'prices.list_row',
				G_ENDRS => 'prices.list_end',
				G_FOOTER => 'prices.foot',
			)
		);
	}
}

?>