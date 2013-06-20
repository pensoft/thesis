<?php
class pDashboard extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			'G_DEFAULT' => 'global.dashboard',
		);
		
		$templates = array(
			DASHBOARD_YOUR_TASKS_VIEWMODE => 'YourTasks',
			DASHBOARD_AUTHOR_INCOMPLETE_VIEWMODE => 'AuthorIncomplete',
			DASHBOARD_AUTHOR_PENDING_VIEWMODE => 'AuthorPending',
			DASHBOARD_AUTHOR_PUBLISHED_VIEWMODE => 'AuthorPublished',
			DASHBOARD_AUTHOR_REJECTED_VIEWMODE => 'AuthorRejected',
			DASHBOARD_SE_IN_REVIEW_VIEWMODE => 'SubjectEditorInReview',
			DASHBOARD_SE_IN_PRODUCTION_VIEWMODE => 'SubjectEditorInProduction',
			DASHBOARD_SE_PUBLISHED_VIEWMODE => 'SubjectEditorPublished',
			DASHBOARD_SE_REJECTED_VIEWMODE => 'SubjectEditorRejected',
			DASHBOARD_EDITOR_PENDING_ALL_VIEWMODE => 'EditorPending',
			DASHBOARD_EDITOR_PENDING_UNASSIGNED_VIEWMODE => 'EditorUnassigned',
			DASHBOARD_EDITOR_PENDING_IN_REVIEW_VIEWMODE => 'EditorInReview',
			DASHBOARD_EDITOR_PENDING_IN_COPY_EDIT_VIEWMODE => 'EditorInCopyEdit',
			DASHBOARD_EDITOR_PENDING_IN_LAYOUT_VIEWMODE => 'EditorInLayout',
			DASHBOARD_EDITOR_PENDING_READY_FOR_PUBLISHING_VIEWMODE => 'EditorReadyForPublishing',
			DASHBOARD_EDITOR_PUBLISHED_VIEWMODE => 'EditorPublished',
			DASHBOARD_EDITOR_REJECTED_VIEWMODE => 'EditorRejected',
			//DASHBOARD_DEDICATED_REVIEWER_REQUESTS_VIEWMODE => 'ReviewerRequests',
			DASHBOARD_DEDICATED_REVIEWER_PENDING_VIEWMODE => 'ReviewerPending',
			DASHBOARD_DEDICATED_REVIEWER_PENDING_ARCHIVED_VIEWMODE => 'ReviewerArchived',
			DASHBOARD_COPY_EDITOR_PENDING_VIEWMODE => 'CopyEditorPending',
			DASHBOARD_COPY_EDITOR_ARCHIVED_VIEWMODE => 'CopyEditorArchived',
			DASHBOARD_LAYOUT_PENDING_VIEWMODE => 'LayoutEditorPending',
			DASHBOARD_LAYOUT_READY_VIEWMODE => 'LayoutEditorReadyForPublishing',
			DASHBOARD_LAYOUT_PUBLISHED_VIEWMODE => 'LayoutEditorPublished',
			DASHBOARD_LAYOUT_STATISTICS_VIEWMODE => 'LayoutEditorStatistics',
		);

		$prefix = 'dashboard_story_list';
		foreach ($templates as $mode => $template){
			$this->m_objectsMetadata[$prefix . $mode] =  array(
				'templs' => array(
						    G_NODATA   => "dashboard.NODATA",
						    G_HEADER   => "dashboard.HEADER",
						    G_STARTRS  => "dashboard.$template.STARTRS",
						    G_ROWTEMPL => "dashboard.$template.ROWTEMPL",
						    G_ENDRS    => "dashboard.endtable",
						   )
		    		);
		}
	}
}
?>