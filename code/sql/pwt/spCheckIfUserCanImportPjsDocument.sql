DROP TYPE ret_spCheckIfUserCanImportPjsDocument CASCADE;
CREATE TYPE ret_spCheckIfUserCanImportPjsDocument AS (
	is_allowed boolean
);

CREATE OR REPLACE FUNCTION spCheckIfUserCanImportPjsDocument(
	pDocumentId int,
	pUid int
)
  RETURNS ret_spCheckIfUserCanImportPjsDocument AS
$BODY$
DECLARE
	lRes ret_spCheckIfUserCanImportPjsDocument;	
	lSubmittedToPjsDocumentState int = 2;	
BEGIN
	lRes.is_allowed = true;	
	IF NOT EXISTS (
		SELECT * 
		FROM pwt.documents
		WHERE id = pDocumentId AND state = lSubmittedToPjsDocumentState
	) THEN 
		lRes.is_allowed = false;
	END IF;
	
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCheckIfUserCanImportPjsDocument(
	pDocumentId int,
	pUid int
) TO iusrpmt;
