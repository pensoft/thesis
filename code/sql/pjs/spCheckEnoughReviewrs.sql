DROP TYPE IF EXISTS ret_spCheckEnoughReviewrs CASCADE;
CREATE TYPE ret_spCheckEnoughReviewrs AS (result boolean);

CREATE OR REPLACE FUNCTION pjs.spCheckEnoughReviewrs(pDocumentId bigint)
  RETURNS ret_spCheckEnoughReviewrs AS
$BODY$
	DECLARE
		lRes ret_spCheckIfSECanTakeADecision;			
		cDedicatedReviewerRoleId int := 5;
		lReviewerConfirmedStateId int := 1;
		cConfirmedInvitationStateId int := 2;
		cNewInvitationStateId int := 1;
		cReviewRoundType int := 1;
		cRoundOneNum int := 1;
		lRoundDueDate date;
		lReviewTypeId int;
		lRoundNum int;
		cNonPeerReviewTypeId int := 1;
		cConfirmedBySE int := 5;
	BEGIN
		lRes.result = FALSE;
		
		SELECT INTO 
			lRoundDueDate, 
			lReviewTypeId, 
			lRoundNum 
			dr.round_due_date::date, 
			d.document_review_type_id, 
			dr.round_number
		FROM pjs.documents d
		JOIN pjs.document_review_rounds dr ON dr.id = d.current_round_id
		WHERE d.id = pDocumentId;
		
		IF(
			lRoundDueDate < now()::date OR 
			lReviewTypeId = cNonPeerReviewTypeId OR 
			lRoundNum > 1
		) THEN
			--RAISE EXCEPTION 'Enough Reviewers %', lRoundDueDate;
			lRes.result = TRUE;
		ELSE
			IF EXISTS (
				SELECT * 
				FROM pjs.documents d
				JOIN pjs.document_review_rounds dr ON dr.id = d.current_round_id AND dr.round_type_id = cReviewRoundType
				JOIN pjs.document_user_invitations i ON i.document_id = d.id AND i.round_id = d.current_round_id AND i.state_id IN (cConfirmedInvitationStateId, cNewInvitationStateId, cConfirmedBySE)
				WHERE i.role_id = cDedicatedReviewerRoleId AND d.id = pDocumentId AND dr.round_number = cRoundOneNum
			) THEN
				--RAISE EXCEPTION 'Enough Reviewers - TRUE';
				lRes.result = TRUE;
			END IF;
		END IF;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs.spCheckEnoughReviewrs(pDocumentId bigint) TO iusrpmt;
