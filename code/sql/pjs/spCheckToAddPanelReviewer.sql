DROP TYPE IF EXISTS ret_spCheckToAddPanelReviewer CASCADE;
CREATE TYPE ret_spCheckToAddPanelReviewer AS (
	invitation_id bigint,
	round_user_id bigint,
	version_id bigint
);

CREATE OR REPLACE FUNCTION pjs."spCheckToAddPanelReviewer"(
	pDocumentId bigint,
	pUid int
)
  RETURNS ret_spCheckToAddPanelReviewer AS
$BODY$
	DECLARE
		lRes ret_spCheckToAddPanelReviewer;	
		cPanelReviewerRoleId CONSTANT int := 7;
	BEGIN		

		IF NOT EXISTS(SELECT * FROM pjs.document_users WHERE document_id = pDocumentId AND uid = pUid AND role_id = cPanelReviewerRoleId) THEN
			-- selecting invitation id
			SELECT INTO lRes.invitation_id id
			FROM pjs.document_user_invitations 
			WHERE document_id = pDocumentId 
				AND uid = pUid AND role_id = cPanelReviewerRoleId;
			
			-- Confirm reviewer invitation and add the user to document users
			PERFORM spCancelConfirmReviewerInvitation(1, lRes.invitation_id, pUid, pDocumentId);
			
		END IF;
		
		SELECT INTO lRes.round_user_id, lRes.version_id drru.id, drru.document_version_id
		FROM pjs.documents d
		JOIN pjs.document_users du ON du.document_id = d.id AND du.uid = pUid AND du.role_id = cPanelReviewerRoleId
		JOIN pjs.document_review_round_users drru ON drru.round_id = d.current_round_id AND drru.document_user_id = du.id
		WHERE d.id = pDocumentId;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs."spCheckToAddPanelReviewer"(
	pDocumentId bigint,
	pUid int
) TO iusrpmt;
