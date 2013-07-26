DROP FUNCTION IF EXISTS pjs.spDocumentInviteReviewerByAuthor(int, int, bigint, int, int, int, int);
CREATE OR REPLACE FUNCTION pjs.spDocumentInviteReviewerByAuthor(
	pOper int,
	pInvitationId int,
	pDocumentId bigint,
	pReviewerId int,
	pUid int,
	pRoundId int,
	pAddedByType int
)
  RETURNS ret_spDocumentInviteReviewer AS
$BODY$
	DECLARE
		lRes ret_spDocumentInviteReviewer;
		cStateId CONSTANT int := 7;
	BEGIN		
		-- Check that the current user is submitting_author
		IF NOT EXISTS (
			SELECT d.submitting_author_id
			FROM pjs.documents d
			WHERE d.submitting_author_id = pUid AND d.id = pDocumentId
		) THEN
			RAISE EXCEPTION 'pjs.onlySubmittingAuthorCanExecuteThisAction';
		END IF;
		
		IF pOper = 1 THEN -- INSERT
			--Invite the reviewer
			INSERT INTO pjs.document_user_invitations (uid, document_id, round_id, added_by_type_id, state_id)
				SELECT pReviewerId, pDocumentId, d.current_round_id, pAddedByType, cStateId
				FROM pjs.documents d
				WHERE d.id = pDocumentId;
		ELSEIF pOper = 2 THEN -- DELETE
			DELETE FROM pjs.document_user_invitations WHERE id = pInvitationId;
		END IF;
		
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs.spDocumentInviteReviewerByAuthor(
	pOper int,
	pInvitationId int,
	pDocumentId bigint,
	pReviewerId int,
	pUid int,
	pRoundId int,
	pAddedByType int
	
) TO iusrpmt;