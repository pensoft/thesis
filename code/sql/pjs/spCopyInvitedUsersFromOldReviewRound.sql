DROP TYPE IF EXISTS pjs."ret_spCopyInvitedUsersFromOldReviewRound" CASCADE;
CREATE TYPE pjs."ret_spCopyInvitedUsersFromOldReviewRound" AS (
	result int
);

CREATE OR REPLACE FUNCTION pjs."spCopyInvitedUsersFromOldReviewRound"(
	pOldReviewRoundId bigint,
	pNewReviewRoundId bigint, 
	pDocumentId bigint
)
  RETURNS pjs."ret_spCopyInvitedUsersFromOldReviewRound" AS
$BODY$
	DECLARE
		lRes pjs."ret_spCopyInvitedUsersFromOldReviewRound";
		lRecord record;
		cSuggestedUserStateId CONSTANT int := 7;
	BEGIN		
		
		
		-- Insert Old review Round Users Into New Round with as suggested
		FOR lRecord IN
			SELECT * 
			FROM pjs.document_user_invitations
			WHERE document_id = pDocumentId AND round_id = pOldReviewRoundId
		LOOP
			INSERT INTO pjs.document_user_invitations(uid, document_id, round_id, state_id, date_invited, added_by_type_id)
						VALUES(lRecord.uid, lRecord.document_id, pNewReviewRoundId, cSuggestedUserStateId, CURRENT_TIMESTAMP, lRecord.added_by_type_id);
		END LOOP;
	
	
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs."spCopyInvitedUsersFromOldReviewRound"(
	pOldReviewRoundId bigint,
	pNewReviewRoundId bigint, 
	pDocumentId bigint
) TO iusrpmt;
