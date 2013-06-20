DROP TYPE IF EXISTS  pjs."ret_spCheckInvitedReviewersForRound" CASCADE;
CREATE TYPE pjs."ret_spCheckInvitedReviewersForRound" AS (
	invited_users int,
	invited_users_ids character varying
);

CREATE OR REPLACE FUNCTION pjs."spCheckInvitedReviewersForRound"(
	pDocumentId bigint,
	pRoundId int
)
  RETURNS pjs."ret_spCheckInvitedReviewersForRound"  AS
$BODY$
	DECLARE
		lRes pjs."ret_spCheckInvitedReviewersForRound" ;
		lReviewerRoleId int;
		lInvitedReviewerStateId int;
	BEGIN
		lReviewerRoleId = 5;
		lInvitedReviewerStateId = 1;
		
		SELECT INTO lRes
			count(dui.id), 
			aggr_concat_coma(dui.uid::text)
		FROM pjs.document_user_invitations dui
		JOIN pjs.document_review_rounds drr ON drr.id = dui.round_id AND drr.can_proceed = TRUE
		WHERE 
			dui.role_id = lReviewerRoleId 
			AND dui.round_id = pRoundId 
			AND dui.state_id = lInvitedReviewerStateId 
			AND dui.document_id = pDocumentId
		GROUP BY drr.id;
	
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION  pjs."spCheckInvitedReviewersForRound"(
	pDocumentId bigint,
	pRoundId int
) TO iusrpmt;

