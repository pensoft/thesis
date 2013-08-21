DROP TYPE IF EXISTS pjs.ret_spGetDocumentInfoForPDF CASCADE;

CREATE type pjs.ret_spGetDocumentInfoForPDF AS (
	document_title varchar,
	document_id bigint,
	author_list varchar,
	document_type_name varchar
);
-- Function: spGetReviewerAnswer(integer)

-- DROP FUNCTION pjs."spGetDocumentInfoForPDF"(bigint);

CREATE OR REPLACE FUNCTION pjs."spGetDocumentInfoForPDF"(pDocumentId bigint)
  RETURNS pjs.ret_spGetDocumentInfoForPDF AS
$BODY$
DECLARE
	lRes pjs.ret_spGetDocumentInfoForPDF;
	
	cAuthorRoleId CONSTANT int := 11;
		
	lJournalSectionId int;
	lJournalId int;
	lSectionId int;
	cSERoleId CONSTANT int := 3;
	lSEUID int;
	cNominatedReviewerRoleId CONSTANT int := 5;
	cPanelReviewerRoleId CONSTANT int := 7;
	lJournalUserId int;
	lJournalUserIdOnCreateUsr int;
	cReviewerAcceptTakeDecisionEventTypeId CONSTANT int := 6;
	lPanelDueDate timestamp;
BEGIN
	
	lRes.document_id = pDocumentId;
	
	SELECT INTO
	lRes.document_title,
	lRes.author_list,
	lRes.document_type_name
	
	d.name,
	(SELECT aggr_concat_coma(a.author_name)
	FROM (
		SELECT (du.first_name || ' ' || du.last_name) as author_name 
		FROM pjs.document_users du
		WHERE du.document_id = pDocumentId AND du.role_id = cAuthorRoleId AND du.state_id = 1
		ORDER BY du.ord
	) a) as author_list,
	js.title
	FROM pjs.documents d
	JOIN pjs.document_review_types drt ON drt.id = d.document_review_type_id
	JOIN pjs.journal_sections js ON js.id = d.journal_section_id
	WHERE d.id = pDocumentId;
	
    RETURN lRes;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION pjs."spGetDocumentInfoForPDF"(bigint) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pjs."spGetDocumentInfoForPDF"(bigint) TO postgres;
GRANT EXECUTE ON FUNCTION pjs."spGetDocumentInfoForPDF"(bigint) TO iusrpmt;
GRANT EXECUTE ON FUNCTION pjs."spGetDocumentInfoForPDF"(bigint) TO pensoft;
