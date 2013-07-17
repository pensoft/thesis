DROP TYPE IF EXISTS pjs.ret_spGetTemplateValuesForReplace CASCADE;

CREATE type pjs.ret_spGetTemplateValuesForReplace AS (
	first_name varchar,
	last_name varchar,
	usr_title varchar,
	document_title varchar,
	document_id bigint,
	author_list varchar,
	review_type_name varchar,
	due_date date,
	due_date_days int,
	autolog_hash varchar(128),
	journal_id int,
	journal_name varchar,
	journal_email varchar,
	journal_signature varchar,
	se_first_name varchar,
	se_last_name varchar,
	se_usr_title varchar,
	se_tax_expertize varchar,
	se_geo_expertize varchar,
	se_sub_expertize varchar,
	r_first_name varchar,
	r_last_name varchar,
	r_usr_title varchar
);
-- Function: spGetReviewerAnswer(integer)

-- DROP FUNCTION pjs."spGetTemplateValuesForReplace"(bigint, bigint);

CREATE OR REPLACE FUNCTION pjs."spGetTemplateValuesForReplace"(pUid bigint, pDocumentId bigint, pEventTypeId int, pUsrIdEventTo int, pUsrRoleEventTo int)
  RETURNS pjs.ret_spGetTemplateValuesForReplace AS
$BODY$
DECLARE
	lRes pjs.ret_spGetTemplateValuesForReplace;
	
	cAuthorRoleId CONSTANT int := 11;
	
	lJournalSectionId int;
	lJournalId int;
	lSectionId int;
	cSERoleId CONSTANT int := 3;
	lSEUID int;
	cNominatedReviewerRoleId CONSTANT int := 5;
	lJournalUserId int;
BEGIN

	lRes.document_id = pDocumentId;

	-- selecting information about user
	SELECT INTO 
		lRes.first_name,
		lRes.last_name,
		lRes.usr_title,
		lRes.autolog_hash
		
		u.first_name, 
		u.last_name,
		ut.name as usr_title,
		u.autolog_hash
	FROM usr u
	LEFT JOIN usr_titles ut ON ut.id = u.usr_title_id 
	WHERE u.id = pUid;
	
	-- get last SE data for the document
	SELECT INTO 
		lRes.se_first_name,
		lRes.se_last_name,
		lRes.se_usr_title,
		lSEUID
		
		u.first_name, 
		u.last_name,
		ut.name as usr_title,
		u.id
	FROM pjs.document_users du
	JOIN usr u ON u.id = du.uid
	LEFT JOIN usr_titles ut ON ut.id = u.usr_title_id 
	WHERE du.role_id = cSERoleId AND du.document_id = pDocumentId
	ORDER BY du.id DESC
	LIMIT 1;

	-- selecting document data
	SELECT INTO
	lRes.document_title,
	lRes.author_list,
	lRes.review_type_name
	
	d.name,
	a.author_list,
	drt.name
	FROM pjs.documents d
	JOIN pjs.document_review_types drt ON drt.id = d.document_review_type_id
	LEFT JOIN (
		SELECT aggr_concat_coma(u.first_name || ' ' || u.last_name) as author_list, du.document_id
		FROM pjs.document_users du
		JOIN usr u ON u.id = du.uid
		WHERE document_id = pDocumentId AND du.role_id = cAuthorRoleId
		GROUP BY du.document_id
	) a ON a.document_id = d.id
	WHERE d.id = pDocumentId;

	/* Get due date and due date days START*/
	-- get journal_section_id and some journal data
	SELECT INTO 
		lJournalSectionId, 
		lRes.journal_id,
		lRes.journal_name,
		lRes.journal_email,
		lRes.journal_signature
		
		d.journal_section_id, 
		d.journal_id,
		j.name,
		j.email,
		j.signature
	FROM pjs.documents d
	JOIN public.journals j ON j.id = d.journal_id
	WHERE d.id = pDocumentId;
	-- get pwt_paper_type_id
	SELECT INTO lSectionId pwt_paper_type_id FROM pjs.journal_sections WHERE id = lJournalSectionId;
	-- offset days
	SELECT INTO lRes.due_date_days "offset" FROM pjs.getEventOffset(pEventTypeId, lRes.journal_id, lSectionId);
	lRes.due_date = (now() + (lRes.due_date_days*INTERVAL '1 day'))::date;	
	/* Get due date and due date days END*/

	/* Get SE expertises START */
	SELECT INTO lJournalUserId ju.id FROM pjs.journal_users ju WHERE ju.uid = lSEUID AND ju.journal_id = lRes.journal_id AND role_id = cSERoleId;
	
	IF(lJournalUserId IS NOT NULL) THEN
	
		SELECT INTO lRes.se_tax_expertize aggr_concat_coma(name)
		FROM taxon_categories 
		WHERE id IN (SELECT unnest(taxon_categories) FROM pjs.journal_users_expertises WHERE journal_usr_id = lJournalUserId);
		
		SELECT INTO lRes.se_sub_expertize aggr_concat_coma(name)
		FROM subject_categories 
		WHERE id IN (SELECT unnest(subject_categories) FROM pjs.journal_users_expertises WHERE journal_usr_id = lJournalUserId);
		
		SELECT INTO lRes.se_geo_expertize aggr_concat_coma(name)
		FROM geographical_categories 
		WHERE id IN (SELECT unnest(geographical_categories) FROM pjs.journal_users_expertises WHERE journal_usr_id = lJournalUserId);
		
	END IF;
	
	/* Get SE expertises END */

	IF(pUsrIdEventTo > 0 AND pUsrRoleEventTo > 0) THEN
		IF(pUsrRoleEventTo = cNominatedReviewerRoleId) THEN
			SELECT INTO 
				lRes.r_first_name,
				lRes.r_last_name,
				lRes.r_usr_title
				
				u.first_name, 
				u.last_name,
				ut.name as usr_title
			FROM usr u 
			LEFT JOIN usr_titles ut ON ut.id = u.usr_title_id 
			WHERE u.id = pUsrIdEventTo;
		END IF;
	END IF;

    RETURN lRes;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION pjs."spGetTemplateValuesForReplace"(bigint, bigint, integer, integer, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pjs."spGetTemplateValuesForReplace"(bigint, bigint, integer, integer, integer) TO postgres;
GRANT EXECUTE ON FUNCTION pjs."spGetTemplateValuesForReplace"(bigint, bigint, integer, integer, integer) TO iusrpmt;
GRANT EXECUTE ON FUNCTION pjs."spGetTemplateValuesForReplace"(bigint, bigint, integer, integer, integer) TO pensoft;
