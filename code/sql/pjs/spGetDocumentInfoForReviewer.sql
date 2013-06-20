DROP TYPE IF EXISTS pjs.ret_spGetDocumentInfoForReviewer CASCADE;
CREATE TYPE pjs.ret_spGetDocumentInfoForReviewer AS (
	document_id bigint,
	submitting_author_id bigint,
	current_round_id int

);

CREATE OR REPLACE FUNCTION pjs.spGetDocumentInfoForReviewer(
	pDocumentId bigint
)
  RETURNS pjs.ret_spGetDocumentInfoForReviewer AS
$BODY$
	DECLARE
		lRes pjs.ret_spGetDocumentInfoForReviewer;
		lAuthorVersionType int;
	BEGIN
		lAuthorVersionType = 1;
		
		SELECT INTO lRes 
			d.id, d.submitting_author_id, d.current_round_id
		FROM pjs.documents d
		JOIN usr u ON u.id = d.submitting_author_id
		WHERE d.id = pDocumentId;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs.spGetDocumentInfoForReviewer(
	pDocumentId bigint
) TO iusrpmt;
