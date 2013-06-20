DROP TYPE IF EXISTS  pjs."ret_spCheckNonSubmitedUsersForRound" CASCADE;
CREATE TYPE pjs."ret_spCheckNonSubmitedUsersForRound" AS (
	non_submited_users int,
	non_submited_users_ids character varying
);

CREATE OR REPLACE FUNCTION pjs."spCheckNonSubmitedUsersForRound"(
	pDocumentId bigint,
	pRoundId int
)
  RETURNS pjs."ret_spCheckNonSubmitedUsersForRound"  AS
$BODY$
	DECLARE
		lRes pjs."ret_spCheckNonSubmitedUsersForRound" ;
		lReviewerRoleId int;
		lInvitedReviewerStateId int;
		lPublicReviewerRoleId int;
		lPanelReviewerRoleId int;
	BEGIN
		lReviewerRoleId = 5;
		lPublicReviewerRoleId = 6;
		lPanelReviewerRoleId = 7;
		
		lInvitedReviewerStateId = 1;
		
		SELECT INTO lRes
					count(*),
					aggr_concat_coma(du.id::text)
		FROM pjs.document_review_round_users dru
		JOIN pjs.document_users du ON du.id = dru.document_user_id
		JOIN pjs.document_user_invitations dui ON dui.round_id = dru.round_id AND dui.uid = du.uid
		WHERE 
			du.role_id IN (lReviewerRoleId, lPublicReviewerRoleId, lPanelReviewerRoleId) 
			and du.document_id = pDocumentId 
			and dru.round_id = pRoundId 
			and dru.decision_id IS NULL 
			and dru.state_id = lInvitedReviewerStateId 
			and dui.state_id IN (2,5)
		GROUP BY dru.round_id;

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION  pjs."spCheckNonSubmitedUsersForRound"(
	pDocumentId bigint,
	pRoundId int
) TO iusrpmt;

