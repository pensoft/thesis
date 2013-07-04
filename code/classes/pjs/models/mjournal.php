<?php
/**
 * A model to handle journal requests
 * @author peterg
 *
 */
class mJournal extends emBase_Model {

	/**
	 * Returns a list of the subject editors for the specified journal.
	 * If a document id is passed - the section editors for the specified document
	 * will not be returned
	 * @param int $pJournalId
	 * @param int $pDocumentId
	 */
	function GetAvailableSEList($pJournalId, $pDocumentId, $pFilterSuggested = 0, $pFilterSearchByLetter = ''){
		if(!$pDocumentId){
			$lSql = "SELECT u.*
				FROM pjs.journal_users ju
				JOIN usr u ON u.id = ju.uid
				WHERE ju.journal_id = $pJournalId AND ju.role_id = " .  SE_ROLE;
		}else{
			
			if($pFilterSearchByLetter) {
				$lWhereAdd = ' AND lower(substring(u.first_name from 1 for 1)) = lower(\'' . $pFilterSearchByLetter . '\')';
			}
			
			if($pFilterSuggested === 1)
			{
				$fromFiltering = "
					(
						SELECT tc.id, tc.pos, SeTaxa.uid as se_id, '1' as priority 
						FROM pjs.documents d
						JOIN public.taxon_categories tc ON tc.id = ANY(d.taxon_categories)
						JOIN pjs.spGetSEtaxons($pJournalId) SeTaxa ON tc.id = SeTaxa.category_id
						WHERE d.id = $pDocumentId
					UNION
						SELECT sc.id, sc.pos, SeSubjects.uid as se_id, '2' as priority 
						FROM pjs.documents d
						JOIN public.subject_categories sc ON sc.id = ANY(d.subject_categories)
						LEFT JOIN pjs.spGetSEsubjects($pJournalId) SeSubjects ON sc.id = SeSubjects.category_id
						WHERE d.id = $pDocumentId
					) AS SeCategories JOIN";
				$joinFiltering = "on u.id = SeCategories.se_id";
				$tail = "
					GROUP BY u.id, jue.taxon_categories, jue.subject_categories
					ORDER BY max(length(SeCategories.pos)) desc";
				$duFiltering = 'max';
			}
			else
			{
				$tail = "WHERE ju.journal_id = $pJournalId
				  $lWhereAdd 
				  AND ju.role_id = " . SE_ROLE . "
				  ORDER BY first_name asc, last_name asc";
			}
			$lSql = "
			SELECT uname as email, u.id as id, first_name, last_name, 
				coalesce($duFiltering(du.id), 0) as assigned_se_uid,
				(SELECT string_agg(subj.name, '; ') FROM public.subject_categories subj WHERE subj.id  = ANY(jue.subject_categories)) as subjects,
				(SELECT string_agg(taxon.name, '; ') FROM public.taxon_categories taxon WHERE taxon.id = ANY(jue.taxon_categories)) as taxons
			FROM
			 $fromFiltering
			 public.usr u $joinFiltering LEFT JOIN 
					pjs.document_users du ON du.uid = u.id AND du.document_id = $pDocumentId AND du.role_id = " . SE_ROLE . " 
			JOIN pjs.journal_users  ju ON ju.uid = u.id JOIN 
					pjs.journal_users_expertises jue ON jue.journal_usr_id = ju.id
			$tail			
			";
			;
		}
 		//echo $lSql;
		$lResult = array();
		$this->m_con->Execute($lSql);
		while(!$this->m_con->Eof()){
			$lResult[] = $this->m_con->mRs;
			$this->m_con->MoveNext();
		}
// 		var_dump($lResult);
		return $lResult;
	}

	/**
	 * Returns a list of the dedicated reviewers for the specified journal.
	 * If a document id is passed - the dedicated reviewers who have been invited for the current round of the specified document
	 * will not be returned
	 * @param int $pJournalId
	 * @param int $pDocumentId
	 */
	function GetAvailableDedicatedReviewersList($pJournalId, $pDocumentId, $pSearchFilterValue = ''){
		
		//error_reporting(E_ERROR | E_WARNING | E_PARSE);
		//assert(is_int($pJournalId));
		assert(is_int($pDocumentId));
		$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 7;
		$lSql = "SELECT
				u.id,
				u.first_name, 
				u.last_name, 
				u.uname as email
				, dui.added_by_type_id as added,
		
				(SELECT string_agg((CASE WHEN c.id = any(d.taxon_categories) THEN '<b>' || c.name || '</b>'
										 ELSE c.name END) , '; ') 
				 FROM public.taxon_categories c 
				 WHERE c.id = ANY(u.expertise_taxon_categories)) as taxa,
				
				(SELECT string_agg((CASE WHEN c.id = any(d.subject_categories) THEN '<b>' || c.name || '</b>'
										 ELSE c.name END) , '; ') 
				 FROM public.subject_categories c 
				 WHERE c.id = ANY(u.expertise_subject_categories)) as subjects,
				 
				(SELECT string_agg((CASE WHEN c.id = any(d.geographical_categories) THEN '<b>' || c.name || '</b>'
										 ELSE c.name END) , '; ')
				 FROM public.geographical_categories c 
				 WHERE c.id = ANY(u.expertise_geographical_categories))  as geo,  
				 dui.role_id,
				 dui.date_invited
				FROM pjs.document_user_invitations dui
				JOIN pjs.documents d ON d.id = dui.document_id
				JOIN public.usr u ON u.id = dui.uid
				WHERE document_id = $pDocumentId
				  AND dui.round_id = d.current_round_id
				  AND ((dui.role_id <> 5 AND dui.role_id <> 11 AND dui.role_id <> 3) OR dui.role_id IS NULL)
				
				UNION
		
				(SELECT id, first_name, last_name, email, added,
					taxa, subjects, geo, role_id, date_invited
				FROM(
				SELECT row_number() OVER(ORDER BY rating DESC) AS position, * FROM
				
				(SELECT 
					u.id
					, u.first_name, u.last_name, u.uname AS email
					, t1.pos AS tax_pos, t2.pos AS sub_pos, t3.pos AS geo_pos

					, ( coalesce(1 - tax_rating::double precision / (SELECT max(length(pos)) FROM public.taxon_categories)        , 0) * 6 + 
						coalesce(1 - sub_rating::double precision / (SELECT max(length(pos)) FROM public.subject_categories)      , 0) * 3 + 
						coalesce(1 - geo_rating::double precision / (SELECT max(length(pos)) FROM public.geographical_categories) , 0) * 1)
				
					  as rating, 3 AS added,
					  
					(SELECT string_agg((CASE WHEN c.pos = any(t1.pos) THEN '<b>' || c.name || '</b>'
											 ELSE c.name END) , '; ') 
					 FROM public.taxon_categories c 
					 WHERE c.id = ANY(u.expertise_taxon_categories)) as taxa,
					
					(SELECT string_agg((CASE WHEN c.pos = any(t2.pos) THEN '<b>' || c.name || '</b>'
											 ELSE c.name END) , '; ') 
					 FROM public.subject_categories c 
					 WHERE c.id = ANY(u.expertise_subject_categories)) as subjects,
					 
					(SELECT string_agg((CASE WHEN c.pos = any(t3.pos) THEN '<b>' || c.name || '</b>'
											 ELSE c.name END) , '; ') 
					 FROM public.geographical_categories c 
					 WHERE c.id = ANY(u.expertise_geographical_categories))  as geo,
					 0 AS role_id,
					 NULL::timestamp as date_invited 
				FROM
				usr u 
				LEFT join(
					SELECT t1.usr_id, array_agg(t1.pos) as pos, max((length(c1.pos) - length(t1.pos)) / 2) as tax_rating
					FROM pjs.documents d
					JOIN public.taxon_categories c1 on (c1.id = ANY(d.taxon_categories))
					JOIN (
						SELECT c.pos as pos, c.name AS name, u.id AS usr_id
							FROM usr u
							JOIN public.taxon_categories c ON (c.id = ANY(u.expertise_taxon_categories))
					) AS t1 ON (c1.pos like t1.pos || '%')
					WHERE 
						d.id = $pDocumentId
					group by t1.usr_id
				) as t1 on (u.id = t1.usr_id)
				LEFT join(
					SELECT t1.usr_id, array_agg(t1.pos) as pos, max((length(c1.pos) - length(t1.pos)) / 2) as sub_rating
					FROM pjs.documents d
					JOIN public.subject_categories c1 on (c1.id = ANY(d.subject_categories))
					JOIN (
						select c.pos as pos, c.name as name, u.id as usr_id
							from usr u
							JOIN public.subject_categories c on (c.id = ANY(u.expertise_subject_categories))
					) as t1 on (c1.pos like t1.pos || '%')
					WHERE 
						d.id = $pDocumentId
					group by t1.usr_id
				) as t2 on (u.id = t2.usr_id)
				
				LEFT join(
					SELECT t1.usr_id, array_agg(t1.pos) as pos, max((length(c1.pos) - length(t1.pos)) / 2) as geo_rating
					FROM pjs.documents d
					JOIN public.geographical_categories c1 on (c1.id = ANY(d.geographical_categories))
					JOIN (
						select c.pos as pos, c.name as name, u.id as usr_id
							from usr u
							JOIN public.geographical_categories c on (c.id = ANY(u.expertise_geographical_categories))
					) as t1 on (c1.pos like t1.pos || '%')
					WHERE 
						d.id = $pDocumentId
					GROUP BY t1.usr_id
				) AS t3 ON (u.id = t3.usr_id)	
				WHERE t1.usr_id is not null OR t2.usr_id is not null or t3.usr_id is not null
				ORDER BY rating DESC
				) AS oaeuaoeouaeouoeu
				) AS aouoeuoeuoe
				WHERE rating >= $limit
				OR position <= 2
							)
							
			
			ORDER BY added, date_invited
			";
		$lResult = array();
		$this->m_con->Execute($lSql);
		while(!$this->m_con->Eof()){
			$lResult[] = $this->m_con->mRs;
			$this->m_con->MoveNext();
		}
		return $lResult;
	}

	/**
	 * Returns a list of the layout editors for the specified journal.
	 * If a document id is passed - the layout editors for the specified document
	 * will not be returned
	 * @param int $pJournalId
	 * @param int $pDocumentId
	 */
	function GetAvailableLEList($pJournalId, $pDocumentId){
		if(!$pDocumentId){
			$lSql = 'SELECT u.*
			FROM pjs.journal_users ju
			JOIN usr u ON u.id = ju.uid
			WHERE ju.journal_id = ' . (int)$pJournalId . ' AND ju.role_id = ' . (int) LE_ROLE . '
			';
		}else{
			$lSql = 'SELECT u.*, coalesce(du.id, 0) as assigned_le_uid
			FROM pjs.journal_users ju
			JOIN usr u ON u.id = ju.uid
			LEFT JOIN pjs.document_users du ON du.uid = u.id AND du.document_id = ' . (int)$pDocumentId . ' AND du.role_id = ' . (int) LE_ROLE . '
			WHERE ju.journal_id = ' . (int)$pJournalId . ' AND ju.role_id = ' . (int) LE_ROLE . '
			';
		}
		// 		var_dump($lSql);
		$lResult = array();
		$this->m_con->Execute($lSql);
		while(!$this->m_con->Eof()){
			$lResult[] = $this->m_con->mRs;
			$this->m_con->MoveNext();
		}
		// 		var_dump($lResult);
		return $lResult;
	}

	/**
	 * Returns a list of the copy editors for the specified journal.
	 * If a document id is passed - the copy editors for the specified document
	 * will not be returned
	 * @param int $pJournalId
	 * @param int $pDocumentId
	 */
	function GetAvailableCEList($pJournalId, $pDocumentId, $pCurrentRoundId){
		if(!$pDocumentId){
			$lSql = 'SELECT u.*
			FROM pjs.journal_users ju
			JOIN usr u ON u.id = ju.uid
			WHERE ju.journal_id = ' . (int)$pJournalId . ' AND ju.role_id = ' . (int) CE_ROLE . '
			';
		}else{
			$lSql = ' 
				SELECT 
					u.*,
					(
						SELECT du.uid as uid
						FROM pjs.documents d
						JOIN pjs.document_review_round_users drrus ON drrus.round_id = d.current_round_id
						JOIN pjs.document_users du ON du.id = drrus.document_user_id AND du.role_id = ' . (int) CE_ROLE . ' 
						WHERE d.id = ' . (int)$pDocumentId . '
						ORDER BY du.id DESC
						LIMIT 1
					) as assigned_ce_uid
				FROM pjs.journal_users ju 
				JOIN usr u ON u.id = ju.uid 
				WHERE ju.journal_id = ' . (int)$pJournalId . ' AND ju.role_id = ' . (int) CE_ROLE;
			
		}
		 // var_dump($lSql);
		$lResult = array();
		$this->m_con->Execute($lSql);
		while(!$this->m_con->Eof()){
			$lResult[] = $this->m_con->mRs;
			$this->m_con->MoveNext();
		}
		// 		var_dump($lResult);
		return $lResult;
	}
	
	function GetJournals($pTableName, $pRootNode = false, $pKey = 0) {
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT * FROM journals';
		
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			 $lResult[$lCon->mRs['id']] = array(
				'id' => (int)$lCon->mRs['id'],
				'name' => $lCon->mRs['name'],
				'description' => $lCon->mRs['description'],
			); 
			$lCon->MoveNext();
		}
		return $lResult;
	}
	
	function GetLastJournalDocuments($pJournalId) {
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT * FROM journals';
		
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			 $lResult[] = $lCon->mRs;
			$lCon->MoveNext();
		}
		return $lResult;
	}

	function GetDocumentLayoutData($pDocumentId) {
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = '
			SELECT 
				du.*,
				drrus.document_version_id as le_version_id
			FROM pjs.document_users du 
			JOIN pjs.document_review_round_users drrus ON drrus.document_user_id = du.id
			WHERE du.document_id = ' . (int)$pDocumentId . ' 
				AND du.role_id = ' . (int)LE_ROLE . ' 
			ORDER BY du.id DESC 
			LIMIT 1
		';
		
		$lCon->Execute($lSql);
		return $lCon->mRs;
	}
}
?>