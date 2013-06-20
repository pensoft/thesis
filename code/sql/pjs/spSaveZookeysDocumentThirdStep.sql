CREATE OR REPLACE FUNCTION spSaveZookeysDocumentThirdStep(
	pDocumentId bigint,
	pIntendedIssue int,
	pUid bigint
)
  RETURNS ret_spSaveZookeysDocumentSecondStep AS
$BODY$
	DECLARE
		lRes ret_spSaveZookeysDocumentSecondStep;
	BEGIN
		UPDATE pjs.documents d SET 
			intended_issue_id = pIntendedIssue,
			creation_step = 4,
			submitted_date = now()
		WHERE d.id = pDocumentId AND d.submitting_author_id = pUid AND d.state_id = 1 ;
		lRes.result = 1;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveZookeysDocumentThirdStep(
	pDocumentId bigint,
	pIntendedIssue int,
	pUid bigint
) TO iusrpmt;
