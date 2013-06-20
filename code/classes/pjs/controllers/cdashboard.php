<?php
/**
 * A controller used to display the dashboard page for a specific journal
 * @author peterg
 *
 */
class cDashboard extends cBase_Controller {
	
	function __construct() {
		parent::__construct();
		$this->RedirectIfNotLogged();
		$this->InitViewingModeData();

		$pViewPageObjectsDataArray = array();

		$lDashboardModel = new mDashboard();
		$this->m_models['dashboard'] = $lDashboardModel;
		$lDashboardData = $lDashboardModel->HandleDashboardRequest($this->GetUserId(), $this->m_viewingRole, $this->m_viewingMode, $this->m_journalId);
		//var_dump($lDashboardData);
		$pViewPageObjectsDataArray['contents'] = new evList_Display(array(
			'name_in_viewobject' => 'dashboard_story_list' . $this->m_viewingMode,
			'controller_data' => $lDashboardData,
			'viewmode' => $this->m_viewingMode,
			'viewmode_title' => 'pjs.dashboards.' . $this->m_viewingMode,
			'viewmode_no_data' => 'pjs.dashboards.nodata.' . $this->m_viewingMode,
			'view_role' => (int) $this->m_viewingRole
		));
		$pViewPageObjectsDataArray['pagetitle'] = getstr('pjs.dashboards.'. $this->m_viewingMode);
		$pViewPageObjectsDataArray['title.suffix'] = getstr('pjs.editorial');
		
		$this->AddJournalObjects($this->m_journalId);
		$this->m_pageView = new pDashboard(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
		$this->InitLeftcolObjects();
	}
	function head_CSS_files(){return array('def');}
	function head_JS_files(){return array('js/def');}
}
?>