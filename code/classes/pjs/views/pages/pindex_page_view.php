<?php

/**
 * The view class for the stories browse and show pages
 *
 * @author peterg
 *
 */
class pIndex_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.index_page',
		);

		$this->m_objectsMetadata['index_story'] = array(
			'templs'=>array(/*
				G_DEFAULT => 'stories.show_index',
				G_GALLERY => 'global.empty',
				G_PHOTO => 'stories.showphoto',
				G_GALPHOTO => 'global.empty',
				G_RGALPHOTO => 'global.empty',
				G_GALPREV => 'global.empty',
				G_GALNAV => 'global.empty',
				G_GALNEXT => 'global.empty',
				G_RELGAL => 'global.empty',
				G_BIGPHOTO => 'stories.showphoto',
				G_RELINKHEADER => 'stories.relinkhead',
				G_RELINKROW => 'stories.rellinkrow',
				G_RELINKFOOTER => 'stories.relfoot',
				G_RELSTHEADER => 'stories.relsthead',
				G_RELSTROW => 'stories.relstrow',
				G_RELSTFOOTER => 'stories.relfoot',
				G_STORY_ATTACHMENTS => 'stories.showattrow',
				G_STORY_ATTACHMENTSMP3 => 'stories.attmp3',
				G_RELMEDIA_HEADER => 'stories.mediahead',
				G_RELMEDIA_FOOTER => 'stories.relfoot',
				G_STORY_ATTACHMENTS_HEADER => 'stories.attachmentshead',
				G_STORY_ATTACHMENTS_FOOTER => 'stories.relfoot',
				G_KEYHEADER => 'global.empty',
				G_KEYROW => 'global.empty',
				G_KEYFOOTER => 'global.empty',
				G_RESTRICTED => 'global.empty',
				G_NOSTORY => 'stories.nostory',*/
			),
		);
	}
}

?>