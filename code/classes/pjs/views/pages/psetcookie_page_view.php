<?php

/**
 * The view class for the stories browse and show pages
 *
 * @author peterg
 *
 */
class pSetcookie_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.setcookie',
		);

		$this->m_objectsMetadata['setcookie'] = array(

			'templs'=>array(

				G_DEFAULT => 'registerfrm.setcookie',

			),

		);
	}
}

?>