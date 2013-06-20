DROP TYPE ret_spSaveAuthorLayoutDecision CASCADE;
CREATE TYPE ret_spSaveAuthorLayoutDecision AS (
	result int
);

CREATE OR REPLACE FUNCTION spSaveAuthorLayoutDecision(
	pDocumentId bigint,
	pDecisionId int,
	pUid int
)
  RETURNS ret_spSaveAuthorLayoutDecision AS
$BODY$
	DECLARE
		lRes ret_spSaveAuthorLayoutDecision;	
						
		lLERoleId int;		
		lInLayoutReviewDocumentState int;
		lAcceptDecisionId int;
		lReturnToLayoutDecisionId int;
		lDocumentId bigint;
		
		lNewDocumentStateId int;
		lApprovedForPublishDocumentStateId int;
		lWaitingForAuthorVersionAfterLayoutDocumentStateId int;
		
		lDocumentVersionAfterLayoutTypeId int;
		lAuthorSubmittedVersionTypeId int;
		lCurrentVersionId bigint;
		
		lLEVersionTypeId int;
		lNewVersionId bigint;
		
		lRoundId bigint;
		lLayoutRoundTypeId int;	
		lRecord record;
		lVersionId bigint;
	BEGIN		
		
		lLERoleId = 8;		
		lInLayoutReviewDocumentState = 4;	
		
		lAcceptDecisionId = 6;
		lReturnToLayoutDecisionId = 7;	
		
		lApprovedForPublishDocumentStateId = 11;
		lWaitingForAuthorVersionAfterLayoutDocumentStateId = 10;
		
		
		lDocumentVersionAfterLayoutTypeId = 6;
		lAuthorSubmittedVersionTypeId = 1;
		lLEVersionTypeId = 5;
		lLayoutRoundTypeId = 3;
		
		
		
		-- Check that the passed user is the author of the specified document
		-- Check also that the user is trying to make a decision for the for a document which is in the appropriate state
		-- 
		IF NOT EXISTS (
			SELECT u.id
			FROM usr u			
			JOIN pjs.documents d ON d.submitting_author_id = u.id 
			WHERE u.id = pUid AND d.id = pDocumentId
			AND d.state_id = lWaitingForAuthorVersionAfterLayoutDocumentStateId
		) THEN
			RAISE EXCEPTION 'pjs.youCantPerformThisAction';
		END IF;
		
		-- Change document state
		IF pDecisionId = lAcceptDecisionId THEN
			lNewDocumentStateId = lApprovedForPublishDocumentStateId;			
		ELSEIF pDecisionId = lReturnToLayoutDecisionId THEN			
			lNewDocumentStateId = lInLayoutReviewDocumentState;
		END IF;
		
		UPDATE pjs.documents SET
			state_id = lNewDocumentStateId
		WHERE id = pDocumentId;
		
		SELECT INTO lCurrentVersionId id 
		FROM pjs.document_versions 
		WHERE document_id = pDocumentId AND version_type_id = lDocumentVersionAfterLayoutTypeId
		ORDER BY id DESC LIMIT 1;
		
		SELECT INTO lNewVersionId id FROM spCreateDocumentVersion(pDocumentId, pUid, lAuthorSubmittedVersionTypeId, lCurrentVersionId);
		
		IF pDecisionId = lReturnToLayoutDecisionId THEN
			-- Create new round
			SELECT INTO lRoundId id FROM spCreateDocumentRound(pDocumentId, lLayoutRoundTypeId);
			-- Add the layout editors 
			FOR lRecord IN
				SELECT uid, du.id
				FROM pjs.document_users du
				WHERE document_id = pDocumentId AND role_id = lLERoleId
			LOOP
				SELECT INTO lVersionId id FROM spCreateDocumentVersion(pDocumentId, lRecord.uid, lLEVersionTypeId, lNewVersionId);
				INSERT INTO pjs.document_review_round_users(round_id, document_user_id, document_version_id) VALUES (lRoundId, lRecord.id, lVersionId);
			END LOOP;
			
		END IF;
	
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveAuthorLayoutDecision(
	pRoundUserId bigint,
	pDecisionId int,
	pUid int
) TO iusrpmt;
