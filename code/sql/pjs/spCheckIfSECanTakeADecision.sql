DROP TYPE IF EXISTS ret_spCheckIfSECanTakeADecision CASCADE;
CREATE TYPE ret_spCheckIfSECanTakeADecision AS (result boolean);

CREATE OR REPLACE FUNCTION pjs.spCheckIfSECanTakeADecision(pDocumentId bigint)
  RETURNS ret_spCheckIfSECanTakeADecision AS
$BODY$
	DECLARE
		lRes ret_spCheckIfSECanTakeADecision;			
		lSuccessfullySubmittedDocState int;
		lDedicatedReviewerRoleId int;
		lNewInvitationStateId int;
		lReviewerConfirmedStateId int := 1;
		lRoundNumber int;
		cReviewRoundType int := 1;
		cClosedPeerReview int := 2;
		cCommunityPeerReview int := 3;
		cPublicPeerReview int := 4;
		cSeCanTakeDecisionEventId int := 1;
		lReviewType int;
		lCommunityPublicDueDate timestamp;
	BEGIN
		lDedicatedReviewerRoleId = 5;
		lNewInvitationStateId = 1;
		lRes.result = FALSE;
		
		-- get round number for closed-peer; community-peer; public review type if round_due_date > now
		SELECT INTO lRoundNumber, lReviewType, lCommunityPublicDueDate round_number, d.document_review_type_id, d.community_public_due_date
		FROM pjs.documents d 
		JOIN pjs.document_review_rounds dr ON dr.id = d.current_round_id AND dr.round_due_date::date > now()::date 
			-- if communite or public peer review
			AND (CASE WHEN d.document_review_type_id IN (cCommunityPeerReview, cPublicPeerReview) AND dr.round_number = 1 THEN d.community_public_due_date::date > now()::date ELSE TRUE END)
		WHERE d.id = pDocumentId AND d.document_review_type_id IN (cClosedPeerReview, cCommunityPeerReview, cPublicPeerReview);
		
		--RAISE EXCEPTION 'round_number: %', lRoundNumber;
		
		-- round 1
		IF (lRoundNumber = 1) THEN
		
			-- checking for community_public_due_date is not due (the SE can not take decision)
			IF(lReviewType = cCommunityPeerReview OR lReviewType = cPublicPeerReview) THEN
				IF(lCommunityPublicDueDate::date > now()::date) THEN
					RETURN lRes;
				END IF;
			END IF;
		
			-- Check if there are unresponded invitations
			IF EXISTS (
				SELECT * 
				FROM pjs.documents d
				JOIN pjs.document_user_invitations i ON i.document_id = d.id AND i.round_id = d.current_round_id
				WHERE i.role_id = lDedicatedReviewerRoleId AND i.state_id = lNewInvitationStateId AND d.id = pDocumentId
			) THEN
				RETURN lRes;
			END IF;
			
			-- Check that all reviewers that have accepted their invitations have taken a decision
			IF EXISTS (
				SELECT * 
				FROM pjs.documents d
				JOIN pjs.document_review_round_users r ON r.round_id = d.current_round_id
				JOIN pjs.document_users u ON u.id = r.document_user_id
				WHERE u.role_id = lDedicatedReviewerRoleId AND r.decision_id IS NULL AND r.state_id = lReviewerConfirmedStateId AND d.id = pDocumentId
			) THEN
				RETURN lRes;
			END IF;
		
			-- at least 1 reviewer that took decision
			IF EXISTS(
					SELECT * 
					FROM pjs.documents d
					JOIN pjs.document_review_round_users r ON r.round_id = d.current_round_id
					JOIN pjs.document_review_rounds dr ON dr.id = d.current_round_id
					JOIN pjs.document_users u ON u.id = r.document_user_id
					WHERE u.role_id = lDedicatedReviewerRoleId AND r.decision_id IS NOT NULL AND r.state_id = lReviewerConfirmedStateId AND d.id = pDocumentId
			) THEN
				lRes.result = TRUE;
			ELSE 
				lRes.result = FALSE;
			END IF;
			
		-- round 2
		ELSEIF(lRoundNumber = 2) THEN
		
			-- Check if there are unresponded invitations
			IF EXISTS (
				SELECT * 
				FROM pjs.documents d
				JOIN pjs.document_user_invitations i ON i.document_id = d.id AND i.round_id = d.current_round_id
				WHERE i.role_id = lDedicatedReviewerRoleId AND i.state_id = lNewInvitationStateId AND d.id = pDocumentId
			) THEN
				RETURN lRes;
			END IF;
			
			-- Check that all reviewers that have accepted their invitations have taken a decision
			IF EXISTS (
				SELECT * 
				FROM pjs.documents d
				JOIN pjs.document_review_round_users r ON r.round_id = d.current_round_id
				JOIN pjs.document_users u ON u.id = r.document_user_id
				WHERE u.role_id = lDedicatedReviewerRoleId AND r.decision_id IS NULL AND r.state_id = lReviewerConfirmedStateId AND d.id = pDocumentId
			) THEN
				RETURN lRes;
			END IF;
			
			lRes.result = TRUE;
			
		-- all other cases
		ELSE
			lRes.result = TRUE;
		END IF;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs.spCheckIfSECanTakeADecision(pDocumentId bigint) TO iusrpmt;
