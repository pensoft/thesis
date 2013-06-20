<?php

class pStory_Childrens_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.story_childrens',
		);

		$this->m_objectsMetadata['tree_list_templs'] = array(
			'templs' => array(
				G_HEADER   => 'global.empty',
				G_ROWTEMPL => 'browse.left_row_show',
				G_FOOTER   => 'global.empty',
				G_NODATA   => 'global.empty'
			)
		);
	}
}

?>