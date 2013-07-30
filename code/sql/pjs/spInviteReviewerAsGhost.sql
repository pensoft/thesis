-- Function: pjs."spInviteReviewerAsGhost"(bigint, bigint, integer)

-- DROP FUNCTION pjs."spInviteReviewerAsGhost"(bigint, bigint, integer);

CREATE OR REPLACE FUNCTION pjs."spInviteReviewerAsGhost"("pUid" bigint, "pDocumentId" bigint, "pCurrentRoundId" integer)
  RETURNS integer AS
$BODY$
DECLARE
	cUninvitedBySE CONSTANT int := 7;
	cAddedBySE CONSTANT int := 2;

BEGIN
	IF NOT EXISTS (	-- a record for this user for this round
		SELECT uid FROM pjs.document_user_invitations WHERE uid = "pUid" AND round_id = "pCurrentRoundId"
	) 
	THEN -- create one
		BEGIN 
			PERFORM pjs."spEnsureUserIsNotAuthorInDoc"("pUid", "pDocumentId", '%s (%s) is an author in this document => must not be a reviewer' );
		EXCEPTION  WHEN raise_exception THEN  
			RAISE EXCEPTION USING MESSAGE = SQLERRM;
		END;
	
		INSERT INTO pjs.document_user_invitations
				(  uid,   document_id,  round_id, 	 state_id, 		 added_by_type_id) 
		VALUES 	("pUid", "pDocumentId", "pCurrentRoundId", cUninvitedBySE, cAddedBySE);
		RETURN 1;
	END IF;
	RETURN 0;
END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;