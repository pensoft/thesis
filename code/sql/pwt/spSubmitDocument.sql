DROP TYPE IF EXISTS pwt.ret_spSubmitDocument CASCADE;

CREATE TYPE pwt.ret_spSubmitDocument AS (
	result int
);

CREATE OR REPLACE FUNCTION pwt.spSubmitDocument(
	pDocumentId bigint,
	pUid int
)
  RETURNS pwt.ret_spSubmitDocument AS
$BODY$
	DECLARE
		lRes pwt.ret_spSubmitDocument;		
		lDocumentId bigint;
		
		lReadyToSubmitDocumentState int = 6;
		lSubmittedDocumentState int = 2;
		lReturnedFromPjsDocumentState int = 3;
	BEGIN
		IF NOT EXISTS (
			SELECT * FROM pwt.documents 
			WHERE id = pDocumentId AND createuid = pUid
		) THEN
			RAISE EXCEPTION 'pwt.submitYouAreNotTheAuthorOfTheDocument';
		END IF;
		
		IF NOT EXISTS (
			SELECT * FROM pwt.documents 
			WHERE id = pDocumentId AND state IN (lReadyToSubmitDocumentState, lReturnedFromPjsDocumentState)
		) THEN
			RAISE EXCEPTION 'pwt.submitTheDocumentIsNotInStateWhichAllowsSubmitting';
		END IF;
		
		UPDATE pwt.documents SET
			state = lSubmittedDocumentState
		WHERE id = pDocumentId;
		
		lRes.result = 1;
		RETURN lRes;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pwt.spSubmitDocument(
	pDocumentId bigint,
	pUid int
) TO iusrpmt;
