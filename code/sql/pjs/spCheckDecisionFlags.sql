DROP TYPE IF EXISTS ret_spCheckDecisionFlags CASCADE;
CREATE TYPE ret_spCheckDecisionFlags AS (
	waitnominatedflag boolean,
	waitpanelflag boolean,
	caninvitenominatedflag boolean,
	reviews int
);

CREATE OR REPLACE FUNCTION pjs."spCheckDecisionFlags"(pDocumentId bigint, pCurrentRoundId bigint, pDocumentReviewType int, pPanelDuedate timestamp, pPublicDueDate timestamp)
  RETURNS ret_spCheckDecisionFlags AS
$BODY$
	DECLARE
		lRes ret_spCheckDecisionFlags;			
		lDedicatedReviewerRoleId int;
		lNewInvitationStateId int;
		lReviewerConfirmedStateId int := 1;
		cCommunityPeerReview int := 3;
		cPublicPeerReview int := 4;
		cPanelReviewerRoleId CONSTANT int := 7;
		cPublicReviewerRoleId CONSTANT int := 6;
		lCanInvitePanelReviewers boolean;
	BEGIN
		lDedicatedReviewerRoleId = 5;
		lNewInvitationStateId = 1;
		
		lRes.waitnominatedflag = FALSE;
		lRes.waitpanelflag = FALSE;
		lRes.caninvitenominatedflag = FALSE;
		
		/* waitnominatedflag checks START */
		-- Check if there are unresponded invitations
		IF EXISTS (
			SELECT * 
			FROM pjs.documents d
			JOIN pjs.document_user_invitations i ON i.document_id = d.id AND i.round_id = d.current_round_id
			WHERE i.role_id = lDedicatedReviewerRoleId AND i.state_id = lNewInvitationStateId AND d.id = pDocumentId
		) THEN
			lRes.waitnominatedflag = TRUE;
		END IF;
		
		-- Check that all reviewers that have accepted their invitations have taken a decision
		IF EXISTS (
			SELECT * 
			FROM pjs.documents d
			JOIN pjs.document_review_round_users r ON r.round_id = d.current_round_id
			JOIN pjs.document_users u ON u.id = r.document_user_id
			WHERE u.role_id IN (lDedicatedReviewerRoleId, cPanelReviewerRoleId, cPublicReviewerRoleId) AND r.decision_id IS NULL AND r.state_id = lReviewerConfirmedStateId AND d.id = pDocumentId
		) THEN
			lRes.waitnominatedflag = TRUE;
		END IF;
		/* waitnominatedflag checks END */
		
		-- checking for community_public_due_date is not due (the SE can not take decision)
		IF(pDocumentReviewType = cCommunityPeerReview) THEN
		
		/*
			IF(pPanelDuedate::date > now()::date OR pPanelDuedate IS NULL) THEN
				lRes.waitpanelflag = TRUE;
			END IF;
			
			-- check for can invite panels
			SELECT INTO lCanInvitePanelReviewers result FROM pjs."spCheckCanInviteReviewer"(pDocumentId, pCurrentRoundId, cPanelReviewerRoleId);
			IF(lCanInvitePanelReviewers = TRUE) THEN
				lRes.waitpanelflag = TRUE;
			END IF;
			*/
			
				-- if all panel reviewers has taken their decisions or the due_date has passed then TRUE
				IF(pPanelDuedate IS NOT NULL) THEN
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
						lRes.waitpanelflag = FALSE;
					ELSE
						IF(pPanelDuedate::date > now()::date) THEN
							lRes.waitpanelflag = TRUE;
						END IF;
						
						SELECT INTO lCanInvitePanelReviewers result FROM pjs."spCheckCanInviteReviewer"(pDocumentId, pCurrentRoundId, cPanelReviewerRoleId);
						IF(lCanInvitePanelReviewers = TRUE) THEN
							lRes.waitpanelflag = TRUE;
						END IF;
						
					END IF;
				ELSE
				
					-- check for can invite panels
					SELECT INTO lCanInvitePanelReviewers result FROM pjs."spCheckCanInviteReviewer"(pDocumentId, pCurrentRoundId, cPanelReviewerRoleId);
					IF(lCanInvitePanelReviewers = TRUE) THEN
						lRes.waitpanelflag = TRUE;
					END IF;
					
				END IF;
			
			
		END IF;
		
		IF(pDocumentReviewType = cPublicPeerReview) THEN
			
			IF(pPublicDueDate::date > now()::date OR pPublicDueDate IS NULL) THEN
				lRes.waitpanelflag = TRUE;
			END IF;
			
			-- check for can invite panels
			SELECT INTO lCanInvitePanelReviewers result FROM pjs."spCheckCanInviteReviewer"(pDocumentId, pCurrentRoundId, cPanelReviewerRoleId);
			IF(lCanInvitePanelReviewers = TRUE) THEN
				lRes.waitpanelflag = TRUE;
			END IF;
			
		END IF;
		
		--RAISE EXCEPTION 'pCurrentRoundId: %, lDedicatedReviewerRoleId: %', pCurrentRoundId, lDedicatedReviewerRoleId;
		-- at least 1 reviewer that took decision
		SELECT INTO lRes.reviews count(d.id) 
		FROM pjs.documents d
		JOIN pjs.document_review_round_users r ON r.round_id = d.current_round_id
		JOIN pjs.document_review_rounds dr ON dr.id = d.current_round_id
		JOIN pjs.document_users u ON u.id = r.document_user_id
		WHERE u.role_id IN (lDedicatedReviewerRoleId) AND r.decision_id IS NOT NULL AND r.state_id = lReviewerConfirmedStateId AND d.id = pDocumentId;

		-- check for can invite nominated reviewers
		-- RAISE EXCEPTION 'pDocumentId: %, pCurrentRoundId: %, lDedicatedReviewerRoleId: %', pDocumentId, pCurrentRoundId, lDedicatedReviewerRoleId;
		SELECT INTO lRes.caninvitenominatedflag result FROM pjs."spCheckCanInviteReviewer"(pDocumentId, pCurrentRoundId,lDedicatedReviewerRoleId);
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs."spCheckDecisionFlags"(
	pDocumentId bigint, 
	pCurrentRoundId bigint, 
	pDocumentReviewType int, 
	pPanelDuedate timestamp, 
	pPublicDueDate timestamp
) TO iusrpmt;
