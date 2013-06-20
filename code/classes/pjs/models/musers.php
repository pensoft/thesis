<?php 
class mUsers extends emBase_Model
{
	function get_users($name){
		$lCon = $this->m_con;
		$lCon->SetFetchReturnType(PGSQL_ASSOC);
			$lSql = "SELECT u.id, first_name || ' ' || last_name as name, uname as email,
						affiliation
					 FROM public.usr u
					 WHERE first_name || ' ' || last_name ILIKE '$name%'
					    OR uname ILIKE '$name%'
					    OR first_name ILIKE '$name%'
					    OR last_name ILIKE '$name%'
					 	";
		$lCon->Execute($lSql);
		$lResult = array();
		while(! $lCon->Eof()){
				$lResult[] = $lCon->mRs;
				$lCon->MoveNext();
			}
		return $lResult;
	}
	function get_users_mark_ses($name){
		$lCon = $this->m_con;
		$lCon->SetFetchReturnType(PGSQL_ASSOC);
		$lSql = "
			SELECT u.id, first_name || ' ' || last_name as name, uname as email,
				affiliation, ju.role_id
			 FROM public.usr u
			 LEFT JOIN (SELECT uid, role_id FROM pjs.journal_users WHERE role_id = ". SE_ROLE .") AS ju ON ju.uid = u.id  
			 WHERE first_name || ' ' || last_name ILIKE '$name%'
			    OR uname ILIKE '$name%'
			    OR first_name ILIKE '$name%'
			    OR last_name ILIKE '$name%'
			ORDER BY first_name, last_name";
				
		$lCon->Execute($lSql);
		$lResult = array();
		while(! $lCon->Eof()){
				$lResult[] = $lCon->mRs;
				$lCon->MoveNext();
			}
		return $lResult;
	}
	function is_se($usr, $journal)
	{
		$lSql = "SELECT * 
				 FROM pjs.journal_users 
				 WHERE uid = $usr
				   AND journal_id = $journal
				   AND role_id = " . SE_ROLE;
		return $this->m_con->Execute($lSql) && $this->m_con->mRs['id'];
	}
	function GetUsersByGroup($pGroupId, $pPage){
		$lCon = $this->m_con;
		//~ $lResult = array(
			//~ 'err_cnt' => 0,
			//~ 'err_msgs' => array(),
		//~ );
		$lSql = 'SELECT (u.first_name || \' \' || u.last_name) AS fullname, u.uname as email, u.addr_city, c.name as country,  u.uname as email, u.affiliation, photo_id as previewpicid, 
					journal_usr.journal_user_group_id as group_id, u.id, role as subtitle, ugrp.name grptitle, ugrp.description grpsubtitle
					FROM pjs.journal_user_group_users journal_usr 
					LEFT JOIN usr u ON u.id = journal_usr.uid 
					JOIN pjs.journal_user_groups ugrp ON journal_usr.journal_user_group_id = ugrp.id
					LEFT JOIN public.countries c ON c.id = u.country_id
					WHERE journal_user_group_id = '. $pGroupId . ' ORDER BY journal_usr.pos desc';
		if(!$lCon->Execute($lSql)){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $lCon->GetLastError());
		}else{
			$lCon->SetPage(DEFAULT_PAGE_SIZE, $pPage);
			while(! $lCon->Eof()){
				$lResultData[] = $lCon->mRs;
				$lCon->MoveNext();
			}
		}		
		$lResult = new emResults( array(
			'controller_data' => $lResultData,
			'pagesize' => DEFAULT_PAGE_SIZE, // not necessary, but easier this way
			'page_num' => $lCon->mPageNum,
			'record_count' => $lCon->RecordCount(),
		));
		return $lResult;
	}
	function getUsersByRole($pRoleiD){
		$lCon = $this->m_con;
		
		$lSql = 'SELECT (u.first_name || \' \' || u.last_name) AS fullname, u.uname as email, u.affiliation, photo_id as previewpicid, u.id
					FROM pjs.journal_users jusr
					left JOIN usr u ON jusr.uid = u.id
					WHERE role_id = ' . $pRoleiD . ' ORDER BY u.first_name ASC';
		if(!$lCon->Execute($lSql)){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $lCon->GetLastError());
		}else{
			while(! $lCon->Eof()){
				$lResult[] = $lCon->mRs;
				$lCon->MoveNext();
			}
		}
		return $lResult;
	}
	function InviteUserToGroup($pOper, $pUserId, $pJournal_User_Group_Id){
		$lCon = $this->m_con;
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
		);
		$lSql = 'SELECT * FROM spInviteUsersToGroup(' . $pOper . ', ' . $pUserId . ', ' . $pJournal_User_Group_Id . ')';
		//~ var_dump($lSql);
		if(!$lCon->Execute($lSql)){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $lCon->GetLastError());
		}else{
			$lResult += $lCon->mRs;
		}
		//~ print_r($lResult);
		return $lResult;
	}
	function RemoveUserFromGroup($pUid){
		$lCon = $this->m_con;
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
		);
		$lSql = 'SELECT * FROM pjs.spDocumentInviteReviewerByAuthor(' . (int)$pOper . ', ' . (int)$pInvitationId . ', ' . (int)$pDocumentId . ', ' . (int)$pReviewerId . ', ' . (int)$pUid . ', ' . (int)$pRoundId . ', ' . (int)$pAddedByType . ')';

		if(!$lCon->Execute($lSql)){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $lCon->GetLastError());
		}else{
			$lResult += $lCon->mRs;
		}
		return $lResult;
	}
	function GetUsersByCategories($pJournalId, $pPage, $pTaxon, $pSubject, $pGeographical) {
		$lResult = array();
		$lAnd = '';
		
		if(strlen($pTaxon) > 0){
			$lAnd .= ' AND pjs."spTaxonParents"(exp.taxon_categories) 				&& ARRAY[' . q(str_replace('t', '', $pTaxon)) . ']::integer[] ';
		}
		if(strlen($pSubject) > 0){
			$lAnd .= ' AND pjs."spSubjectParents"(exp.subject_categories) 			&& ARRAY[' . q(str_replace('s', '', $pSubject)) . ']::integer[] ';
		}
		if(strlen($pGeographical) > 0){
			$lAnd .= ' AND pjs."spGeographicalParents"(exp.geographical_categories) && ARRAY[' . q(str_replace('g', '', $pGeographical)) . ']::integer[] ';
		}
		
		//~ if(is_array($pSectionTypesArr) && count($pSectionTypesArr) > 0){
			//~ $lAnd .= ' AND d.journal_section_id IN (' . q(implode(",", $pSectionTypesArr)) . ') ';
		//~ }
		$lCon = $this->m_con;
		$lSql = "SELECT u.first_name || ' ' || u.last_name AS fullname, u.addr_city, c.name as country,
		 u.uname as email, u.affiliation, photo_id as previewpicid, u.id, jusr, 
		 	 (SELECT string_agg(name, '; ') FROM public.subject_categories      WHERE id = ANY(exp.subject_categories)) AS subject,
			 (SELECT string_agg(name, '; ') FROM public.taxon_categories        WHERE id = ANY(exp.taxon_categories)) AS taxon, 
			 (SELECT string_agg(name, '; ') FROM public.geographical_categories WHERE id = ANY(exp.geographical_categories)) AS geographical
					FROM pjs.journal_users jusr 
					JOIN pjs.journal_users_expertises exp ON exp.journal_usr_id = jusr.id
					LEFT JOIN usr u ON jusr.uid = u.id
					LEFT JOIN public.countries c ON c.id = u.country_id
					WHERE journal_id = $pJournalId
					$lAnd 
					
					ORDER BY u.first_name ASC";
		$lCon->Execute($lSql);
		$lCon->SetPage(DEFAULT_PAGE_SIZE, $pPage);
		
		while(!$lCon->Eof()){
			$lResultData[] = $lCon->mRs;
			$lCon->MoveNext();
		}
		
		$lResult = new emResults( array(
			'controller_data' => $lResultData,
			'pagesize' => DEFAULT_PAGE_SIZE, // not necessary, but easier this way
			'page_num' => $lCon->mPageNum,
			'record_count' => $lCon->RecordCount(),
		));
		
		return $lResult;
	}
	
}

?>