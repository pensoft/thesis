DROP TYPE IF EXISTS ret_spSaveZookeysDocumentFifthStep CASCADE;
CREATE TYPE ret_spSaveZookeysDocumentFifthStep AS (
	result int,
	event_id bigint
);

--DROP FUNCTION IF EXISTS spsavezookeysdocumentfifthstep(bigint, integer[], bigint);

CREATE OR REPLACE FUNCTION spsavezookeysdocumentfifthstep(pdocumentid bigint, puid bigint)
  RETURNS ret_spSaveZookeysDocumentFifthStep AS
$BODY$
	DECLARE
		lRes ret_spSaveZookeysDocumentFifthStep;			
		lSuccessfullySubmittedDocState int;
		
		cSubmissionEventType CONSTANT int := 1;
		lJournalId int;
	BEGIN
		
		lSuccessfullySubmittedDocState = 2;
		
		UPDATE pjs.documents d SET 
			state_id = lSuccessfullySubmittedDocState
		WHERE d.id = pDocumentId AND d.submitting_author_id = pUid AND d.state_id = 1;
		
		SELECT INTO lJournalId journal_id FROM pjs.documents WHERE id = pdocumentid;
		
		-- creating submission event
		SELECT INTO lRes.event_id event_id FROM spCreateEvent(cSubmissionEventType, pdocumentid, puid, lJournalId, null, null);
		
		lRes.result = 1;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spsavezookeysdocumentfifthstep(bigint, bigint) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spsavezookeysdocumentfifthstep(bigint, bigint) TO public;
GRANT EXECUTE ON FUNCTION spsavezookeysdocumentfifthstep(bigint, bigint) TO postgres;
GRANT EXECUTE ON FUNCTION spsavezookeysdocumentfifthstep(bigint, bigint) TO iusrpmt;
