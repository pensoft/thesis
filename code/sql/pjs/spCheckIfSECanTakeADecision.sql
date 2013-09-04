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
		lPanelDueDate timestamp;
		lPublicDueDate timestamp;
		lCanInviteDedicatedReviewers boolean;
		lCurrentRoundId bigint;
		cPanelReviewerRoleId CONSTANT int := 7;
		cPublicReviewerRoleId CONSTANT int := 6;
		lCanInvitePanelReviewers boolean;
		lPanelFlag int := 0;
	BEGIN
		lDedicatedReviewerRoleId = 5;
		lNewInvitationStateId = 1;
		lRes.result = FALSE;
		
		-- get round number for closed-peer; community-peer; public review type if round_due_date > now
		SELECT INTO lRoundNumber, lReviewType, lPanelDueDate, lPublicDueDate, lCurrentRoundId round_number, d.document_review_type_id, d.panel_duedate, d.public_duedate, d.current_round_id
		FROM pjs.documents d 
		JOIN pjs.document_review_rounds dr ON dr.id = d.current_round_id --AND dr.round_due_date::date > now()::date 
			-- if communite or public peer review
			AND (CASE WHEN d.document_review_type_id = cCommunityPeerReview AND dr.round_number = 1 AND d.panel_duedate IS NOT NULL THEN d.panel_duedate::date > now()::date ELSE TRUE END)
			AND (CASE WHEN d.document_review_type_id = cPublicPeerReview AND dr.round_number = 1 AND d.public_duedate IS NOT NULL THEN d.public_duedate::date > now()::date ELSE TRUE END)
		WHERE d.id = pDocumentId AND d.document_review_type_id IN (cClosedPeerReview, cCommunityPeerReview, cPublicPeerReview);
		
		--RAISE EXCEPTION 'round_number: %', lRoundNumber;
		
		-- round 1
		IF (lRoundNumber = 1) THEN
		
			-- checking for community_public_due_date is not due (the SE can not take decision)
			IF(lReviewType = cCommunityPeerReview) THEN
				
				-- if all panel reviewers has taken their decisions or the due_date has passed then TRUE
				IF(lPanelDueDate IS NOT NULL) THEN
				--RAISE NOTICE 'panel check';
					IF NOT EXISTS (
						SELECT dui.*
						FROM pjs.document_user_invitations dui
						JOIN pjs.document_users u ON u.uid = dui.uid AND u.role_id = cPanelReviewerRoleId AND u.document_id = pDocumentId
						JOIN pjs.document_review_round_users r ON r.document_user_id = u.id
						WHERE dui.document_id = pDocumentId AND r.decision_id IS NULL AND dui.role_id IN (cPanelReviewerRoleId)
						UNION
						SELECT dui.*
						FROM pjs.document_user_invitations dui
						LEFT JOIN pjs.document_users u ON u.uid = dui.uid AND u.role_id IN (cPanelReviewerRoleId) AND u.document_id = pDocumentId
						WHERE dui.document_id = pDocumentId AND dui.role_id IN (cPanelReviewerRoleId) AND u.id IS NULL
					) THEN
						lRes.result = TRUE;
						lPanelFlag = 1;
					ELSE
						IF(lPanelDueDate::date > now()::date) THEN
							lRes.result = FALSE;
							RETURN lRes;
						END IF;
						
						SELECT INTO lCanInvitePanelReviewers result FROM pjs."spCheckCanInviteReviewer"(pDocumentId, lCurrentRoundId, cPanelReviewerRoleId);
						IF(lCanInvitePanelReviewers = TRUE) THEN
							lRes.result = FALSE;
							RETURN lRes;
						END IF;
						
					END IF;
				ELSE
				
					-- check for can invite panels
					SELECT INTO lCanInvitePanelReviewers result FROM pjs."spCheckCanInviteReviewer"(pDocumentId, lCurrentRoundId, cPanelReviewerRoleId);
					IF(lCanInvitePanelReviewers = TRUE) THEN
						lRes.result = FALSE;
						RETURN lRes;
					END IF;
					
				END IF;
				
			END IF;
			
			IF(lReviewType = cPublicPeerReview) THEN
				
				IF(lPublicDueDate::date > now()::date OR lPublicDueDate IS NULL) THEN
					lRes.result = FALSE;
					RETURN lRes;
				END IF;
				
				-- check for can invite panels
				SELECT INTO lCanInvitePanelReviewers result FROM pjs."spCheckCanInviteReviewer"(pDocumentId, lCurrentRoundId, cPanelReviewerRoleId);
				IF(lCanInvitePanelReviewers = TRUE) THEN
					lRes.result = FALSE;
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
				lRes.result = FALSE;
				RETURN lRes;
			END IF;
			
			-- Check that all reviewers that have accepted their invitations have taken a decision
			IF EXISTS (
				SELECT * 
				FROM pjs.documents d
				JOIN pjs.document_review_round_users r ON r.round_id = d.current_round_id
				JOIN pjs.document_users u ON u.id = r.document_user_id
				WHERE u.role_id IN (lDedicatedReviewerRoleId) AND r.decision_id IS NULL AND r.state_id = lReviewerConfirmedStateId AND d.id = pDocumentId
			) THEN
				lRes.result = FALSE;
				RETURN lRes;
			END IF;
			--RAISE EXCEPTION 'lCurrentRoundId: %, lDedicatedReviewerRoleId: %', lCurrentRoundId, lDedicatedReviewerRoleId;
			-- at least 1 reviewer that took decision
			IF EXISTS(
					SELECT * 
					FROM pjs.documents d
					JOIN pjs.document_review_round_users r ON r.round_id = d.current_round_id
					JOIN pjs.document_review_rounds dr ON dr.id = d.current_round_id
					JOIN pjs.document_users u ON u.id = r.document_user_id
					WHERE u.role_id = lDedicatedReviewerRoleId AND r.decision_id IS NOT NULL AND r.state_id = lReviewerConfirmedStateId AND d.id = pDocumentId
			) THEN
				--RAISE EXCEPTION '333';
				IF(lPanelFlag = 1) THEN
					lRes.result = TRUE;
					RETURN lRes;
				ELSE 
					lRes.result = FALSE;
					RETURN lRes;
				END IF;
				lRes.result = TRUE;
			ELSE 
				-- check for can invite nominated reviewers
				SELECT INTO lCanInviteDedicatedReviewers result FROM pjs."spCheckCanInviteReviewer"(pDocumentId, lCurrentRoundId,lDedicatedReviewerRoleId);
				IF(lCanInviteDedicatedReviewers = TRUE) THEN
					lRes.result = FALSE;
				ELSE
					IF(lPanelFlag = 1) THEN
						lRes.result = TRUE;
						RETURN lRes;
					ELSE 
						lRes.result = FALSE;
						RETURN lRes;
					END IF;
				END IF;
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
