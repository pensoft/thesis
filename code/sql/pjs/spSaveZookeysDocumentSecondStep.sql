DROP TYPE IF EXISTS ret_spSaveZookeysDocumentSecondStep CASCADE;
CREATE TYPE ret_spSaveZookeysDocumentSecondStep AS (
	result int
);

CREATE OR REPLACE FUNCTION spSaveZookeysDocumentSecondStep(
	pDocumentId bigint,
	pComments varchar,
	pUid bigint
)
  RETURNS ret_spSaveZookeysDocumentSecondStep AS
$BODY$
	DECLARE
		lRes ret_spSaveZookeysDocumentSecondStep;			
		lSuccessfullySubmittedDocState int;
	BEGIN
		lSuccessfullySubmittedDocState = 2;
		UPDATE pjs.documents d SET 
			notes_to_editor = pComments,
			creation_step = 3,
			submitted_date = now()
		WHERE d.id = pDocumentId AND d.submitting_author_id = pUid AND d.state_id = 1 ;
		lRes.result = 1;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveZookeysDocumentSecondStep(
	pDocumentId bigint,
	pComments varchar,
	pUid bigint
) TO iusrpmt;
