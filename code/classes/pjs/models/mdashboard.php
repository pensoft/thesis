<?php

/**
 * A model class to handle dashboard requests
 * @author peterg
 *
 */
class mDashboard extends emBase_Model {
	
	var $m_rejectedStates   = array(100 =>	DOCUMENT_REJECTED_STATE, DOCUMENT_ARCHIVED_STATE, DOCUMENT_REJECTED_BUT_RESUBMISSION);
	
	var $m_inReviewStates   = array(200 =>	DOCUMENT_IN_REVIEW_STATE, DOCUMENT_REVISIONS_AFTER_REVIEW_STATE);									  
	var $m_inCopyEditStates = array(300 =>	DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_COPY_EDITING_STATE,
											DOCUMENT_READY_FOR_COPY_REVIEW_STATE,
											DOCUMENT_IN_COPY_REVIEW_STATE);
	var $m_inLayoutStates   = array(400 =>	DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_LAYOUT_EDITING_STATE,
											DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_LAYOUT_EDITING_AFTER_COPY_EDITING_STATE,
											DOCUMENT_READY_FOR_LAYOUT_STATE,
											DOCUMENT_IN_LAYOUT_EDITING_STATE,
											DOCUMENT_REVISIONS_AFTER_LAYOUT_STATE);
	var $m_inProductionStates;
	var $m_activeStates;
	function __construct() {
		parent::__construct();
		$this->m_inProductionStates =		$this->m_inCopyEditStates + 
											$this->m_inLayoutStates +
											array(500 => DOCUMENT_APPROVED_FOR_PUBLISH);
											
		$this->m_activeStates = 			array(600 => DOCUMENT_WAITING_SE_ASSIGNMENT_STATE) + 
											$this->m_inReviewStates + 
										  	$this->m_inProductionStates;
		$this->rejectedStates   = '(' . implode(', ', $this->m_rejectedStates    ) . ')';
		$this->inReviewStates   = '(' . implode(', ', $this->m_inReviewStates    ) . ')';
		$this->inCopyEditStates = '(' . implode(', ', $this->m_inCopyEditStates    ) . ')';
		$this->inLayoutStates   = '(' . implode(', ', $this->m_inLayoutStates    ) . ')';
		$this->inProduction     = '(' . implode(', ', $this->m_inProductionStates) . ')';
		$this->activeStates     = '(' . implode(', ', $this->m_activeStates      ) . ')';
		
													
	}
	function schedule($days_remaining = "rru.due_date::date - current_date") {
		return $this->scheduling($days_remaining) . " as schedule, " .
			   $this->late($days_remaining) 	  . " as late, " .
			   $this->days_remaining($days_remaining) 	  . " as days";
	}
	function scheduling($days_remaining) {
		return "(case when $days_remaining <  0 then 'pjs.dashboard.schedule.late'
					  when $days_remaining  = 0 then 'pjs.dashboard.schedule.duetoday'
						  					    else 'pjs.dashboard.schedule.fine' end)";
	}
	
	function days_remaining($days){ return "(case when $days = 0 then 'pjs.dashboard.schedule.today'::text else abs($days)::text end)"; }
	function late($days_remaining){ return "(case when $days_remaining <= 0 then 'late' else '' end)"; }
	
	function roler($dict){
		return "(case when rv.action = 'pjs.dashboards.actions.inviteReviewers' then ".$dict['pjs.dashboards.actions.inviteReviewers']."
					  when rv.action = 'pjs.dashboards.actions.respond2request' then ".$dict['pjs.dashboards.actions.respond2request']."
					  when rv.action = 'pjs.dashboards.actions.takeDecision' 	then ".$dict['pjs.dashboards.actions.takeDecision']."
					  when rv.action = 'pjs.dashboards.actions.submitReview'    then ".$dict['pjs.dashboards.actions.submitReview']."
					  when rv.action = 'pjs.dashboards.actions.reviewSubmitted' then ".$dict['pjs.dashboards.actions.reviewSubmitted']."
				 			end)";
	}
									  
	/**
	 * Returns an array of the documents for the specified dashboard viewmode
	 * for the specified user
	 *
	 * @param $pUid int
	 * @param $pViewingMode int
	 * @param $pJournalId int
	 * @return
	 *
	 */
	function HandleDashboardRequest($pUid, $pViewingRole, $pViewingMode, $pJournalId) {
		//error_reporting(E_ALL);
		$lResult = array();
		$lCon = $this->m_con;
		$lCon->SetFetchReturnType(PGSQL_ASSOC);
		$daysToAssignSE = 7;
		$days2AssignSE = "submitted_date::date + $daysToAssignSE - current_date";
		$days2AcceptRequest = "due_date::date - current_date";
		$days2reviewPaper = "rv.due_date - current_date";
		$days2layoutpaper = "due_date::date - current_date";
		$days2copyedit = "due_date::date - current_date";
		$days2inviteReviewers = "r.reviewers_assignment_duedate::date - current_date";
		$days2closeRound = "r.round_due_date::date - current_date";
		$lSql = '';
		$myDocs  = '(SELECT document_id FROM pjs.document_users WHERE role_id = ' . AUTHOR_ROLE . ' AND uid = ' . $pUid . ')';
		$mySE    = '(SELECT document_id FROM pjs.document_users WHERE role_id = ' . SE_ROLE . ' AND uid = ' . $pUid . ')';
		$myR     = '(SELECT document_id FROM pjs.document_users WHERE role_id = ' . DEDICATED_REVIEWER_ROLE . ' AND uid = ' . $pUid . ')';
		$myL     = '(SELECT document_id FROM pjs.document_users WHERE role_id = ' . LE_ROLE . ' AND uid = ' . $pUid . ')';
		$idTitleAuthors = 'v.doc_id as id, v.title as title, v.authors as authors, v.submitter_name as submitter_name, v.submitter_email as submitter_email';
		
		
		$schedule = array(
						'pjs.dashboards.actions.inviteReviewers' => $this->scheduling($days2inviteReviewers),
						'pjs.dashboards.actions.respond2request' => $this->scheduling($days2reviewPaper),
						'pjs.dashboards.actions.submitReview'    => $this->scheduling($days2reviewPaper),
				    	'pjs.dashboards.actions.reviewSubmitted' => "'pjs.dashboard.status.completed'",
				    	'pjs.dashboards.actions.takeDecision'	 => $this->scheduling($days2reviewPaper),
						);
		$days = array(
						'pjs.dashboards.actions.inviteReviewers' => $this->days_remaining($days2inviteReviewers),
					 	'pjs.dashboards.actions.respond2request' => $this->days_remaining($days2reviewPaper),
					 	'pjs.dashboards.actions.submitReview'    => $this->days_remaining($days2reviewPaper),
					 	'pjs.dashboards.actions.takeDecision'    => $this->days_remaining($days2reviewPaper),
					 	'pjs.dashboards.actions.reviewSubmitted' => "'pjs.dashboard.dash'::text",
					    );
		$late = array(
						'pjs.dashboards.actions.inviteReviewers' => $this->late($days2inviteReviewers),
					 	'pjs.dashboards.actions.respond2request' => $this->late($days2reviewPaper),
					 	'pjs.dashboards.actions.submitReview'    => $this->late($days2reviewPaper),
					 	'pjs.dashboards.actions.takeDecision'    => $this->late($days2reviewPaper),
					 	'pjs.dashboards.actions.reviewSubmitted' => "'pjs.dashboard.dash'::text",
					    );
		
		switch ($pViewingMode) {
			case DASHBOARD_YOUR_TASKS_VIEWMODE:
				$lSql = "
				SELECT $idTitleAuthors,
					role.name as yourrole,
					rv.role_id,
					rv.action,
					". $this->roler($schedule). " as schedule,
					". $this->roler($late)    . " as late,
					". $this->roler($days)    . " as days
				FROM pjs.v_getdocumentsandauthors v  
					JOIN pjs.document_review_rounds r on v.current_round = r.id 
					 	LEFT JOIN pjs.v_reviewers rv on v.current_round = rv.round_id 
					JOIN pjs.user_role_types role ON rv.role_id = role.id 
				WHERE v.journal = $pJournalId 
				  AND uid = $pUid
				  AND rv.decision_id is NULL 
				  AND v.state in $this->activeStates
				ORDER BY v.doc_id asc"; break;
			
			case DASHBOARD_AUTHOR_PENDING_VIEWMODE:
				$lSql = "
				SELECT $idTitleAuthors, 
					 ". $this->schedule($days2closeRound) .",
					 v.state as action,
					 " . AUTHOR_ROLE  . " as role_id
				FROM pjs.v_getdocumentsandauthors v 
				LEFT JOIN pjs.document_review_rounds r ON  r.id = v.current_round
				--LEFT JOIN pjs.document_review_round_users rru ON v.current_round = rru.round_id
				WHERE v.journal = $pJournalId AND v.doc_id in $myDocs
				  AND v.state in $this->activeStates
				  --AND rru.due_date is not null
				ORDER BY v.doc_id asc"; break;				
				
			case DASHBOARD_AUTHOR_PUBLISHED_VIEWMODE:
				$lSql = "
				SELECT $idTitleAuthors, 
					js.title as articletype,
					v.pub_date as publicationdate,
					v.doi, 
					i.number as issuenumber,
					i.name as issuetype, 
					" . AUTHOR_ROLE . " as role_id
				FROM pjs.v_getdocumentsandauthors v JOIN 
					 	  pjs.journal_sections js ON v.journal_section = js.id LEFT JOIN
					 	  pjs.journal_issues i ON v.issue_id = i.id
				WHERE v.journal = $pJournalId 
				  AND v.doc_id in $myDocs
				  AND v.state = " . DOCUMENT_PUBLISHED_STATE . "
				ORDER BY v.doc_id asc"; break;	
				
			case DASHBOARD_AUTHOR_REJECTED_VIEWMODE:
				$lSql = "
				SELECT $idTitleAuthors, 
					 v.mdate as date, 
					 s.name as editorialdecision, 
					 " . AUTHOR_ROLE . " as role_id
				FROM pjs.v_getdocumentsandauthors v JOIN 
					 	 pjs.document_states s ON v.state = s.id
				WHERE v.journal = $pJournalId 
				  AND v.doc_id in $myDocs
				  AND v.state in $this->rejectedStates
				ORDER BY v.doc_id asc"; break;

			case DASHBOARD_AUTHOR_INCOMPLETE_VIEWMODE:
				$lSql = "
				SELECT $idTitleAuthors, 
					v.mdate as date, 
					" . AUTHOR_ROLE . " as role_id
				FROM pjs.v_getdocumentsandauthors v
				WHERE v.journal = $pJournalId 
				  AND v.submitter_id = $pUid 
				  AND v.state = " . DOCUMENT_INCOMPLETE_STATE . "
			    ORDER BY v.doc_id asc"; break;
				
			case DASHBOARD_SE_IN_PRODUCTION_VIEWMODE:
				$role = SE_ROLE;
				$lSql = "
				SELECT $idTitleAuthors, v.editor_notes,
				s.name as status, $role as role_id
				FROM pjs.v_getdocumentsandauthors v JOIN
					 pjs.document_states s ON v.state = s.id
				WHERE v.journal = $pJournalId AND v.doc_id in $mySE 
					AND v.state in $this->inProduction
				ORDER BY v.doc_id asc"; break;
				
			case DASHBOARD_SE_PUBLISHED_VIEWMODE:
				$role = SE_ROLE;
				$lSql = "
				SELECT $idTitleAuthors, 
					js.title as articletype, v.editor_notes,
					v.pub_date as publicationdate,
					v.doi,
					issues.number as issuenumber,
					issues.name as issuetype, $role as role_id
				FROM pjs.v_getdocumentsandauthors v JOIN 
					 pjs.journal_sections js ON v.journal_section = js.id LEFT JOIN
 					 pjs.journal_issues issues on v.issue_id = issues.id
				WHERE v.journal = $pJournalId AND v.doc_id in $mySE 
					AND v.state = " . DOCUMENT_PUBLISHED_STATE . "
				ORDER BY v.doc_id asc"; break;
				
			case DASHBOARD_SE_REJECTED_VIEWMODE:
				$role = SE_ROLE;
				$lSql = "
				SELECT $idTitleAuthors, v.editor_notes,
					v.mdate as date, 
					s.name as editorialdecision, $role as role_id
				FROM pjs.v_getdocumentsandauthors v JOIN 
					 pjs.document_states s ON v.state = s.id  
				WHERE v.journal = $pJournalId AND v.doc_id in $mySE 
					AND v.state in $this->rejectedStates
				ORDER BY v.doc_id asc"; break;
				
			case DASHBOARD_EDITOR_PENDING_ALL_VIEWMODE:
				$role = JOURNAL_EDITOR_ROLE;
				$lSql = "
				SELECT $idTitleAuthors,
					 (case when v.state = 3 then 'review round ' || r.round_number::text
					       else s.name end) as status,
					 v.editor_notes,
					 v.review_type as review_num, rt.name as review_type,
					 r.round_number as reviewround, $role as role_id,
					 (case when v.state = " . DOCUMENT_APPROVED_FOR_PUBLISH . " then 'pjs.actions.askTeo'
					       else rv.action end) as action,
					 u.first_name || '&nbsp;' || u.last_name::text as who, 
 					 ". $this->roler($schedule)." as schedule,
					 ". $this->roler($late)." as late,   
					 ". $this->roler($days)." as days 
				FROM pjs.v_getdocumentsandauthors v
					JOIN pjs.document_review_rounds r ON  r.id = v.current_round
						LEFT JOIN pjs.v_reviewers rv ON rv.round_id = r.id
							LEFT JOIN public.usr u ON rv.uid = u.id
					JOIN pjs.document_review_types rt on rt.id = v.review_type
				JOIN pjs.document_states s ON v.state = s.id   
							  
				WHERE v.journal = $pJournalId 
				  AND v.state in $this->activeStates
				ORDER BY v.doc_id asc";
				
				$group = array('action', 'who', 'schedule', 'days', 'late');
				$verbatim = 11; 	
				break;			
			case DASHBOARD_EDITOR_PENDING_UNASSIGNED_VIEWMODE:
				$role = JOURNAL_EDITOR_ROLE;
				$lSql = "
				SELECT $idTitleAuthors,
					v.editor_notes,
					v.review_type as review_num, rt.name as review_type,
					" . $this->schedule($days2AssignSE) .", 
					$role as role_id
				FROM pjs.v_getdocumentsandauthors v JOIN 
					 pjs.document_review_types rt on rt.id = v.review_type
				WHERE v.journal = $pJournalId
					AND v.state = " . DOCUMENT_WAITING_SE_ASSIGNMENT_STATE . "
				ORDER BY v.doc_id asc"; break;
			
			case DASHBOARD_SE_IN_REVIEW_VIEWMODE:
				$lSql = "AND v.doc_id in $mySE
				"; //no break is required
			case DASHBOARD_EDITOR_PENDING_IN_REVIEW_VIEWMODE:
				$lSql = "
				SELECT $idTitleAuthors,
					 v.editor_notes,
					 v.review_type as review_num, rt.name as review_type,
					 r.round_number as reviewround, " . $pViewingMode % 10 . " as role_id,
					 rv.action,
					 u.first_name || '&nbsp;' || u.last_name::text as who, 
 					 ". $this->roler($schedule)." as schedule,
					 ". $this->roler($late)." as late,   
					 ". $this->roler($days)." as days 
				FROM pjs.v_getdocumentsandauthors v
					JOIN pjs.document_review_rounds r ON  r.id = v.current_round
						LEFT JOIN pjs.v_reviewers rv ON rv.round_id = r.id
							 LEFT JOIN public.usr u ON rv.uid = u.id
					JOIN pjs.document_review_types rt on rt.id = v.review_type
				WHERE v.journal = $pJournalId 
				  AND v.state in $this->inReviewStates 
				  AND (NOT r.enough_reviewers
				  		OR r.can_proceed
				  		OR rv.role_id = ".DEDICATED_REVIEWER_ROLE. ")
				  " . $lSql . "ORDER BY v.doc_id asc";  
				$group = array('action', 'who', 'schedule', 'days', 'late');
				$verbatim = 10; 
				break;
				
			case DASHBOARD_EDITOR_PENDING_IN_COPY_EDIT_VIEWMODE:
				$role = JOURNAL_EDITOR_ROLE;
				$lSql = "
				SELECT $idTitleAuthors, 
					state as action, 
					v.editor_notes, 
					$role as role_id,
					" . $this->schedule() . "
				FROM pjs.v_getdocumentsandauthors v 
				JOIN pjs.document_review_round_users rru ON rru.round_id = v.current_round
				JOIN pjs.document_users du ON du.uid = rru.document_user_id
				WHERE v.journal = $pJournalId 
				  AND v.state in  $this->inCopyEditStates
				  AND du.role_id = " . CE_ROLE . "
				ORDER BY v.doc_id asc"; break;
					
			case DASHBOARD_EDITOR_PENDING_IN_LAYOUT_VIEWMODE:
				$role = JOURNAL_EDITOR_ROLE;
				$lSql = "
				SELECT $idTitleAuthors, 
					 state as action, 
					 issues.name as forpublicationin,
				 	 v.editor_notes,
				 	 " . $this->schedule(). ",
				 	 $role as role_id
				FROM pjs.v_getdocumentsandauthors v 
				JOIN pjs.document_review_round_users rru on rru.round_id = v.current_round
				LEFT JOIN pjs.journal_issues issues on v.issue_id = issues.id
				WHERE v.journal = $pJournalId 
					AND v.state in $this->inLayoutStates
				ORDER BY v.doc_id asc"; break;
				
			case DASHBOARD_EDITOR_PENDING_READY_FOR_PUBLISHING_VIEWMODE:
				$role = JOURNAL_EDITOR_ROLE;
				$lSql = "
				SELECT $idTitleAuthors, 
					js.title as articletype,
					v.approve_date as dateapproved,
					pages as pages,
					v.editor_notes,
					issues.number as issuenumber,
					issues.name as issuetype, $role as role_id
				FROM pjs.v_getdocumentsandauthors v JOIN 
					 pjs.journal_sections js ON v.journal_section = js.id LEFT JOIN
 					 pjs.journal_issues issues on v.issue_id = issues.id
				WHERE v.journal = $pJournalId 
					AND v.state = " . DOCUMENT_APPROVED_FOR_PUBLISH . "
				ORDER BY v.doc_id asc"; break;
				
			case DASHBOARD_EDITOR_PUBLISHED_VIEWMODE:
				$role = JOURNAL_EDITOR_ROLE;
				$lSql = "
				SELECT $idTitleAuthors,
					v.editor_notes,
					js.title as articletype,
					v.pub_date as publicationdate,
					v.doi, 
					issues.number as issuenumber,
					issues.name as issuetype, $role as role_id
				FROM pjs.v_getdocumentsandauthors v JOIN 
					 pjs.journal_sections js ON v.journal_section = js.id LEFT JOIN
 					 pjs.journal_issues issues on v.issue_id = issues.id
				WHERE v.journal = $pJournalId 
					AND v.state = " . DOCUMENT_PUBLISHED_STATE . "
				ORDER BY v.doc_id asc"; break;
				
			case DASHBOARD_EDITOR_REJECTED_VIEWMODE:
				$role = JOURNAL_EDITOR_ROLE;
				$lSql = "
				SELECT $idTitleAuthors, 
					v.editor_notes,
					v.mdate as date, 
					s.name as editorialdecision, $role as role_id
				FROM pjs.v_getdocumentsandauthors v JOIN 
					 pjs.document_states s ON v.state = s.id 
				WHERE v.journal = $pJournalId 
					AND v.state in $this->rejectedStates
				ORDER BY v.doc_id asc"; break;
				
			case DASHBOARD_DEDICATED_REVIEWER_PENDING_VIEWMODE:
				$lSql = "
				SELECT $idTitleAuthors, 
					 rv.action,  
					 role.name as reviewertype, 
					 role.id as role_id,
					 r.round_number as reviewround, " .
					 $this->schedule($days2reviewPaper) . "
				FROM pjs.v_getdocumentsandauthors v
					JOIN pjs.document_review_rounds r ON r.document_id = v.doc_id and r.round_type_id = 1
						JOIN pjs.v_reviewers rv ON rv.round_id = r.id
							JOIN public.usr u ON u.id = rv.uid  
						    JOIN pjs.user_role_types role ON role.id = rv.role_id
				WHERE v.journal = $pJournalId
				  AND rv.uid = $pUid
				  AND rv.action <> 'pjs.dashboards.actions.reviewSubmitted'
				  AND role_id = " . DEDICATED_REVIEWER_ROLE . "
				ORDER BY v.doc_id asc"; break;
				
			case DASHBOARD_DEDICATED_REVIEWER_PENDING_ARCHIVED_VIEWMODE:
				$lSql = "
				SELECT $idTitleAuthors, 
					 rv.action,  
					 role.name as reviewertype, 
					 role.id as role_id,
					 r.round_number as reviewround, 
					 decision.name as yousaid,
					 eddecision.name as editorialdecision,
					 (case when v.state not in $this->inReviewStates then eddecision.name end) as finaldecision
				FROM pjs.v_getdocumentsandauthors v
					JOIN pjs.document_review_rounds r ON r.document_id = v.doc_id and r.round_type_id = 1
						JOIN pjs.v_reviewers rv ON rv.round_id = r.id
							JOIN public.usr u ON u.id = rv.uid 
					 		JOIN pjs.user_role_types role ON role.id = rv.role_id
					 	JOIN pjs.document_review_round_decisions decision on decision.id = rv.decision_id
					 	LEFT JOIN pjs.document_review_round_decisions eddecision on eddecision.id = r.decision_id
				WHERE v.journal = $pJournalId
				  AND rv.uid = $pUid
				  AND rv.action = 'pjs.dashboards.actions.reviewSubmitted' 
				ORDER BY v.doc_id asc"; break;
				
			case DASHBOARD_COPY_EDITOR_PENDING_VIEWMODE:
				$lSql = "
				SELECT $idTitleAuthors,
					 v.state as action,
					 v.role_id,
					 ". $this->schedule($days2layoutpaper) ."
				FROM pjs.v_getdocumentsandreviewers v
				WHERE v.role_id = ". CE_ROLE ." 
					 AND v.state = " . DOCUMENT_IN_COPY_REVIEW_STATE . "
				ORDER BY v.doc_id asc"; break;
				
			case DASHBOARD_COPY_EDITOR_ARCHIVED_VIEWMODE:
				$lSql = "
				SELECT $idTitleAuthors,
					 (case when v.state = " . DOCUMENT_PUBLISHED_STATE . " then 'pjs.dashboard.status.published'
																  		   else 'pjs.dashboard.status.completed' end) as status,
					 v.doi as doi,
					 du.role_id
				FROM pjs.v_getdocumentsandauthors v JOIN
					 pjs.document_users du on du.document_id = v.doc_id
				WHERE du.role_id = ". CE_ROLE ." 
					 AND v.state <> " . DOCUMENT_IN_COPY_REVIEW_STATE . "
				ORDER BY v.doc_id asc"; break;

			case DASHBOARD_LAYOUT_PENDING_VIEWMODE:
				$role = LE_ROLE;
				$lSql = "
				SELECT $idTitleAuthors,
					v.state as action,
					j.short_name as journal_short,
					j.name as journal_full,
					issues.number as issuenumber,
					issues.name as issuetype, $role as role_id, ".
					$this->schedule()."
				FROM pjs.v_getdocumentsandauthors v
				JOIN pjs.document_review_round_users rru ON rru.round_id = v.current_round
				 LEFT JOIN
 					 pjs.journal_issues issues on v.issue_id = issues.id JOIN
 					 public.journals j on j.id = v.journal
 				WHERE v.state in $this->inLayoutStates
 				  AND v.doc_id in $myL 
 				ORDER BY v.doc_id asc"; break;
				
			case DASHBOARD_LAYOUT_READY_VIEWMODE:
				$role = LE_ROLE;
				$lSql = "
				SELECT $idTitleAuthors,
				js.title as articletype,
					j.short_name as journal_short,
					j.name as journal_full,
					v.approve_date as dateapproved,
					pages as pages,
					issues.number as issuenumber,
					issues.name as issuetype, $role as role_id
				FROM pjs.v_getdocumentsandauthors v JOIN 
					 pjs.journal_sections js ON v.journal_section = js.id LEFT JOIN
 					 pjs.journal_issues issues on v.issue_id = issues.id JOIN
 					 public.journals j on j.id = v.journal
 				WHERE doc_id in $myL
 				  AND v.state = " . DOCUMENT_APPROVED_FOR_PUBLISH . "
 				ORDER BY v.doc_id asc"; break;
 				  
			case DASHBOARD_LAYOUT_PUBLISHED_VIEWMODE:
				$role = LE_ROLE;
				$lSql = "
				SELECT $idTitleAuthors,
					 js.title as articletype,
					 v.pub_date as publicationdate,
					 v.doi,
					 j.short_name as journal_short,
					j.name as journal_full, 
					 issues.number as issuenumber,
					 issues.name as issuetype, $role as role_id
				FROM pjs.v_getdocumentsandauthors v JOIN 
					 pjs.journal_sections js ON v.journal_section = js.id LEFT JOIN
 					 pjs.journal_issues issues on v.issue_id = issues.id JOIN
 					 public.journals j on j.id = v.journal
				WHERE v.doc_id in $myL
				  AND v.state = " . DOCUMENT_PUBLISHED_STATE . "
				ORDER BY v.doc_id asc"; break;
				  
			case DASHBOARD_LAYOUT_STATISTICS_VIEWMODE:
				$lSql = "
				SELECT max(j.name) as journal_full,
					max(j.short_name) as journal_short,
					sum(case when state_id = ".DOCUMENT_APPROVED_FOR_PUBLISH." then 1
							else 0 end)::int as articles_laidout,
					
					sum(case when state_id = ".DOCUMENT_APPROVED_FOR_PUBLISH." then d.number_of_pages
							 else 0 end) as pages_laidout,
					
					sum(case when state_id = ".DOCUMENT_PUBLISHED_STATE." then 1
							 else 0 end) as articles_published,
								    
					sum(case when state_id = ".DOCUMENT_PUBLISHED_STATE." then d.number_of_pages
							 else 0 end) as pages_published
				FROM public.journals j 
				JOIN pjs.documents   d  ON j.id =  d.journal_id
				WHERE
				  d.state_id in (".DOCUMENT_PUBLISHED_STATE.", ".DOCUMENT_APPROVED_FOR_PUBLISH.")
				  AND d.id in (SELECT document_id FROM pjs.document_users WHERE role_id = " . LE_ROLE . " AND uid = $pUid)
				GROUP BY j.id
				"; break;
				
		}
		//if(defined('DanchoDebug'))
		//trigger_error('<textarea style="position: fixed; bottom: 0; left: 0; width: 500px; height: 200px" rows="12" cols="40">' . str_replace("\t\t\t\t", "", $lSql)   . "</textarea>\n");		
		
		$lCon->Execute($lSql);
		
		switch ($pViewingMode)
		 {
	 		case DASHBOARD_SE_IN_REVIEW_VIEWMODE:
			case DASHBOARD_EDITOR_PENDING_ALL_VIEWMODE:
		 	case DASHBOARD_EDITOR_PENDING_IN_REVIEW_VIEWMODE:	

				while(! $lCon->Eof()){
					$record = $lCon->mRs; $id = $record['id'];
					 
					if (empty($lResult[$id]))					
						 $lResult[$id] = array_slice($record, 0, $verbatim); 
				
					foreach ($group as $key) 
						$lResult[$id][$key][] = $record[$key];
					
					$lCon->MoveNext();
				}
			break;
		 	
			default:
			while(! $lCon->Eof()){
				$lResult[] = $lCon->mRs;
				$lCon->MoveNext();
			}
			//if(isset($role) && count($lResult) == 1)
			//	header("location: " . SITE_URL . "view_document?view_role=$role&id=" . $lResult[0]['id']);
		 }
		
		 //var_dump($lResult);
		 //exit();
		 
		//var_dump($lCon->GetLastError());
		//var_dump($lCon->mRs);
		
		//print_r($lResult);
		return $lResult;
	}
	
	function GetViewModeCounts($pJournalId, $pRolesArr, $pUid){
		$lResultArray = array();
		if(!is_array($pRolesArr) || !count($pRolesArr)){
			return $lResultArray;
		}
		$lSql = 'SELECT 
			max(role_id) as role_id, 
			viewmode_id,
			sum(count) as count
 			FROM pjs.spGetDashboardViewmodeCount(' . $pJournalId . ', 
		  ARRAY[' . implode(',', $pRolesArr) . ']::int[], ' . $pUid . ')
		  GROUP BY viewmode_id;
		  ';
		//echo $lSql;
		$this->m_con->Execute($lSql);
		while(!$this->m_con->Eof()){
			/*$lRole = $this->m_con->mRs['role_id'];
			if(!array_key_exists($lRole, $lResultArray)){
				$lResultArray[$lRole] = array();
			}*/
			//$lResultArray[$lRole][$this->m_con->mRs['viewmode_id']] = $this->m_con->mRs['count'];
			$lResultArray[$this->m_con->mRs['viewmode_id']] = $this->m_con->mRs['count'];
			$this->m_con->MoveNext();
		}
		return $lResultArray;
	}
}

?>