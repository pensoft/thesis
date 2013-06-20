DROP TYPE ret_spEditorProceedDocumentToLayoutEditing CASCADE;
CREATE TYPE ret_spEditorProceedDocumentToLayoutEditing AS (
	result int
);

CREATE OR REPLACE FUNCTION spEditorProceedDocumentToLayoutEditing(
	pDocumentId bigint,	
	pUid int
)
  RETURNS ret_spEditorProceedDocumentToLayoutEditing AS
$BODY$
	DECLARE
		lRes ret_spEditorProceedDocumentToLayoutEditing;	
					
		lEditorRoleId int;
		lInCopyReviewDocumentState int;
		lWaitingAuthorToProceedToLayoutEditingDocumentStateId int;
		lReadyForCopyEditingState int;
		
	BEGIN		
				
		lEditorRoleId = 2;
		lInCopyReviewDocumentState = 8;	
		lReadyForCopyEditingState = 15;
		lWaitingAuthorToProceedToLayoutEditingDocumentStateId = 12;
		
						
		
		
		
		
		-- Check that the passed user is the journal editor
		-- Check also that the user is trying to make a decision for the current round of the document which is in review mode
		-- 
		IF NOT EXISTS (
			SELECT u.id
			FROM usr u			
			JOIN pjs.documents d ON d.id = pDocumentId
			JOIN pjs.journal_users ju ON ju.journal_id = d.journal_id AND ju.uid = u.id
			WHERE u.id = pUid AND ju.role_id = lEditorRoleId
			AND d.state_id IN (lInCopyReviewDocumentState, lReadyForCopyEditingState)
		) THEN
			RAISE EXCEPTION 'pjs.youCantPerformThisAction';
		END IF;
		
		
		
		-- Change document state		
		
		UPDATE pjs.documents SET
			state_id = lWaitingAuthorToProceedToLayoutEditingDocumentStateId
		WHERE id = pDocumentId;
				
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spEditorProceedDocumentToLayoutEditing(
	pDocumentId bigint,	
	pUid int
) TO iusrpmt;
