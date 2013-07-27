-- Function: pjs."spInviteReviewerAsGhost"(integer, integer, integer)

-- DROP FUNCTION pjs."spInviteReviewerAsGhost"(integer, integer, integer);

CREATE OR REPLACE FUNCTION pjs."spInviteReviewerAsGhost"(puid bigint, pdocumentid bigint, pcurrentroundid integer)
  RETURNS integer AS
$BODY$
DECLARE
	cUninvitedBySE CONSTANT int := 7;
	cAddedBySE CONSTANT int := 2;
BEGIN
	/*
		IF EXISTS (
			SELECT du.id
			FROM pjs.document_users du
			WHERE document_id = pDocumentId
			  AND uid = pUid
			  AND role_id = lAuthorRoleId
		) THEN
			RAISE EXCEPTION 'pjs.theSpecifiedUserIsAnAuthorOfTheDocumentCantBeReviewer';
		END IF;
	*/
	IF NOT EXISTS 
		( SELECT uid 
		FROM pjs.document_user_invitations 
		WHERE uid = pUid
		  AND document_id = pDocumentId 
		  AND round_id = pCurrentRoundId)
	THEN
		INSERT INTO pjs.document_user_invitations
				( uid, 	 document_id,  round_id, 	 state_id, 		 added_by_type_id) 
		VALUES 	(pUid, pDocumentId, pCurrentRoundId, cUninvitedBySE, cAddedBySE);
		RETURN 1;
	END IF;
	RETURN 0;
END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION pjs."spInviteReviewerAsGhost"(integer, integer, integer)
  OWNER TO pensoft;
