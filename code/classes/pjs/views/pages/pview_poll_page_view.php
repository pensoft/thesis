<?php

/**
 * The view class for the browse journal issues page
 */
class pView_Poll_Page_View extends epPage_Json_View {
	function __construct($pData) {
		parent::__construct($pData);

		$this->m_Templs = array(
			G_DEFAULT => 'view_poll.page',
		);

		$this->m_objectsMetadata['aof_poll_answers'] = array(
			'templs' => array(
				G_HEADER => 'view_poll.aof_poll_view_head',
				G_FOOTER => 'view_poll.aof_poll_view_foot',
				G_STARTRS => 'view_poll.aof_poll_view_start',
				G_ENDRS => 'view_poll.aof_poll_view_end',
				G_NODATA => 'view_poll.aof_poll_view_nodata',
				G_ROWTEMPL => 'view_poll.aof_poll_view_row' 
			)
		);
		
	}
}

?>