<?php

/**
 * The view class for the register page
 */
class pTasksPopUp_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		
		switch ($pData['act_templ']) {
			// form
			case 'getform':
			case 'skip_refresh_form':
				$this->m_Templs = array(
					G_DEFAULT => 'taskspopup.form_only'
				);
				break;
			// list
			case 'getlist':
				$this->m_Templs = array(
					G_DEFAULT => 'taskspopup.list_only'
				);
				break;
			// all
			default:
				$this->m_Templs = array(
					G_DEFAULT => 'global.taskspopuppage'
				);
				break;
		}

		$this->m_objectsMetadata['taskfrm'] = array(
			'templs' => array(
				G_FORM_TEMPLATE => 'taskspopup.form',
			)
		);
		
		$this->m_objectsMetadata['taskfrmview'] = array(
			'templs' => array(
				G_FORM_TEMPLATE => 'taskspopup.formview',
			)
		);
		
		$this->m_objectsMetadata['list'] = array(
			'templs' => array(
				G_HEADER => 'taskspopup.listheader',
				G_ROWTEMPL => 'taskspopup.listrow',
				G_FOOTER => 'taskspopup.listfoot',
			)
		);
	}

}

?>