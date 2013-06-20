DROP TYPE IF EXISTS pjs."ret_spDocumentSE" CASCADE;
CREATE TYPE pjs."ret_spDocumentSE" AS (
	result int,
	event_id bigint,
	event_id_sec bigint
);

CREATE OR REPLACE FUNCTION pjs."spDocumentSE"(
	pOper int,
	pDocumentId bigint,
	pSEId int,
	pUid int
)
  RETURNS pjs."ret_spDocumentSE" AS
$BODY$
	DECLARE
		lRes pjs."ret_spDocumentSE";	
				
		cJournalEditorRoleId CONSTANT int := 2;
		cSERoleId CONSTANT int := 3;
		cWaitingSEAssignmentState CONSTANT int := 2;
		cInReviewState CONSTANT int := 3;
		cReviewRoundStateId CONSTANT int := 1;
		cAuthorSubmittedVersionType CONSTANT int := 1;
		cSEVersionTypeId CONSTANT int := 3;
		cEditorRoleId CONSTANT int := 2;
		cEditorRoundType CONSTANT int := 4;
		cDocumentSEVersionType CONSTANT int := 3;
		cEDecisionId CONSTANT int := 1;
		cSEChangeEventType CONSTANT int := 4;
		cAssignedSEDocState CONSTANT int := 3;
		cAuthorRoleId CONSTANT int := 11;
		cNonPeerReviewType CONSTANT int := 1;	
		cNotEnoughReviewersEventType CONSTANT int := 39;
		cCanProceedEventType CONSTANT int := 38;
		cReviewRoundType CONSTANT int := 1;
			
		lRoundId bigint;
		lVersionId bigint;
		lSubmittedVersionId bigint;
		lRecord record;
		lDocumentUserId bigint;
		lDocumentRoundId bigint;
		lDocumentReviewRoundUserId bigint;
		lDocumentVersion bigint;
		lAuthorDocumentVersion bigint;
		lSEDuId bigint;
		lSEUid bigint;
		lSEAssignEventType int;
		lJournalId int;
		lDocumentReviewType int;
		lSEOldUid bigint;
		lEnoughReviewers boolean;
		lCanTakeDecision boolean;
		lCurrentRoundId bigint;
		lRoundType int;
		lCanProceed boolean;
		lSECommunityPublicEvent int;
	BEGIN		
		--lSEAssignEventType = 2;
		
		IF NOT EXISTS (
			SELECT ju.id
			FROM pjs.journal_users ju
			JOIN pjs.documents d ON d.journal_id = ju.journal_id
			WHERE ju.uid = pUid AND d.id = pDocumentId AND ju.role_id = cJournalEditorRoleId
		) THEN
			RAISE EXCEPTION 'pjs.onlyJournalManagersCanExecuteThisAction';
		END IF;
		
		IF NOT EXISTS (
			SELECT ju.id
			FROM pjs.journal_users ju
			JOIN pjs.documents d ON d.journal_id = ju.journal_id
			WHERE ju.uid = pSEId AND d.id = pDocumentId AND ju.role_id = cSERoleId
		) THEN
			RAISE EXCEPTION 'pjs.theSpecifiedUserIsNotSEForTheJournalOfTheDocument';
		END IF;
		/*
		IF EXISTS (
			SELECT du.id
			FROM pjs.document_users du
			WHERE document_id = pDocumentId
			  AND uid = pSEId
			  AND role_id = cAuthorRoleId
		) THEN
			RAISE EXCEPTION 'pjs.theSpecifiedUserIsAnAuthorOfTheDocumentCantBeSE';
		END IF;
		*/
		IF pOper = 1 THEN -- Add SE
		
			SELECT INTO 
				lDocumentReviewType, 
				lCurrentRoundId,
				lRoundType
				
				d.document_review_type_id, 
				d.current_round_id,
				dr.round_type_id
			FROM pjs.documents d
			JOIN pjs.document_review_rounds dr ON dr.id = d.current_round_id
			WHERE d.id = pDocumentId;
				--AND state_id IN (cWaitingSEAssignmentState, cAssignedSEDocState);
			
			-- creating SE assign event
			/* Document Review Types
				1;"Non-peer review"
				2;"Closed peer-review"
				3;"Community peer-review"
				4;"Public peer-review"
			*/
			IF(lDocumentReviewType = cNonPeerReviewType) THEN
				lSEAssignEventType = 2;
			ELSEIF(lDocumentReviewType = 2) THEN
				lSEAssignEventType = 20;
			ELSEIF(lDocumentReviewType = 3) THEN
				lSEAssignEventType = 21;
			ELSE
				lSEAssignEventType = 22;
			END IF;
			
			-- assign self as SE
			IF (pUid = pSEId) THEN 
				lSEAssignEventType = 99;
			END IF;
			
			SELECT INTO lAuthorDocumentVersion id 
			FROM pjs.document_versions 
			WHERE version_type_id = cAuthorSubmittedVersionType 
				AND document_id = pDocumentId
			ORDER BY id DESC
			LIMIT 1;
		
			IF NOT EXISTS (
				SELECT * 
				FROM pjs.document_users du
				WHERE document_id = pDocumentId AND role_id = cSERoleId
			) THEN
				-- ADD First SE
					
				IF (lDocumentReviewType IS NOT NULL) THEN
					
					-- >>> closing editor round
					IF NOT EXISTS 
						(SELECT id 
							FROM pjs.document_users 
							WHERE uid = pUid AND role_id = cEditorRoleId AND document_id = pDocumentId
						) THEN
						INSERT INTO pjs.document_users(document_id, uid, role_id) 
						VALUES (pDocumentId, pUid, cEditorRoleId);
						lDocumentUserId = currval('pjs.document_users_id_seq');
					ELSE
						SELECT INTO lDocumentUserId id 
						FROM pjs.document_users 
						WHERE uid = pUid AND role_id = cEditorRoleId AND document_id = pDocumentId;
					END IF;
					
					SELECT INTO lDocumentVersion id 
					FROM spCreateDocumentVersion(pDocumentId, pUid, cDocumentSEVersionType, lAuthorDocumentVersion);
					
					SELECT INTO lDocumentRoundId, lJournalId drr.id, d.journal_id
					FROM pjs.documents d
					JOIN pjs.document_review_rounds drr ON drr.id = d.current_round_id 
					WHERE d.id = pDocumentId AND drr.round_type_id = cEditorRoundType 
						AND drr.decision_id IS NULL 
					LIMIT 1;
					
					INSERT INTO pjs.document_review_round_users(round_id, decision_id, document_user_id, document_version_id, decision_date) 
						VALUES (lDocumentRoundId, cEDecisionId, lDocumentUserId, lDocumentVersion, CURRENT_TIMESTAMP);
					lDocumentReviewRoundUserId = currval('pjs.document_review_round_reviewers_id_seq');

					UPDATE pjs.document_review_rounds SET 
						decision_id = cEDecisionId, 
						decision_round_user_id = lDocumentReviewRoundUserId 
					WHERE id = lDocumentRoundId;
					-- <<< closing editor round
				
					INSERT INTO pjs.document_users(document_id, uid, role_id) 
						VALUES (pDocumentId, pSEId, cSERoleId);
					lDocumentUserId = currval('pjs.document_users_id_seq');
					
					SELECT INTO lDocumentVersion id 
					FROM spCreateDocumentVersion(pDocumentId, pSEId, cDocumentSEVersionType, lAuthorDocumentVersion);
					
					-- Update document state if the document is in the waiting se assignment state
					UPDATE pjs.documents 
					SET state_id = cInReviewState
					WHERE id = pDocumentId AND state_id = cWaitingSEAssignmentState;
					
					SELECT INTO lSubmittedVersionId id 
					FROM pjs.document_versions
					WHERE document_id = pDocumentId AND version_type_id = cAuthorSubmittedVersionType;
					
					-- This would be the first review round
					SELECT INTO lRoundId id 
					FROM spCreateDocumentRound(pDocumentId, cReviewRoundStateId);
					
					UPDATE pjs.document_review_rounds SET 
						create_from_version_id = lSubmittedVersionId 
					WHERE id = lRoundId;
					
					IF(lDocumentReviewType <> cNonPeerReviewType) THEN
						UPDATE pjs.document_review_rounds SET 
							enough_reviewers = FALSE, 
							can_proceed = FALSE 
						WHERE id = lRoundId;
					END IF;
					
					UPDATE pjs.documents SET
						current_round_id = lRoundId
					WHERE id = pDocumentId;
					
					UPDATE pjs.document_user_invitations SET 
						round_id = lRoundId 
					WHERE round_id = lCurrentRoundId AND document_id = pDocumentId;
					
					INSERT INTO pjs.document_review_round_users(round_id, document_user_id, document_version_id) 
						VALUES (lRoundId, lDocumentUserId, lDocumentVersion);
					lDocumentReviewRoundUserId = currval('pjs.document_review_round_reviewers_id_seq');

					UPDATE pjs.document_review_rounds SET 
						decision_round_user_id = lDocumentReviewRoundUserId,
						enough_reviewers = (lDocumentReviewType = cNonPeerReviewType),
						can_proceed = (lDocumentReviewType = cNonPeerReviewType)
					WHERE id = lRoundId;
					
					SELECT INTO lRes.event_id event_id 
					FROM spCreateEvent(lSEAssignEventType, pDocumentId, pUid, lJournalId, null, null);
					
					-- SET due dates for current round and users
					-- Updatating round due date
					PERFORM pjs.spUpdateDueDates(1, pDocumentId, lSEAssignEventType, lRoundId, NULL);
					
					IF(lDocumentReviewType = 4) THEN
						lSECommunityPublicEvent = 40;
						PERFORM pjs.spUpdateDueDates(4, pDocumentId, lSECommunityPublicEvent, NULL, NULL);
					ELSEIF(lDocumentReviewType = 3) THEN
						lSECommunityPublicEvent = 41;
						PERFORM pjs.spUpdateDueDates(4, pDocumentId, lSECommunityPublicEvent, NULL, NULL);
					END IF;
					
				END IF;
			ELSE 
				--Change se
				
				SELECT INTO lSEOldUid uid 
				FROM pjs.document_users 
				WHERE role_id = cSERoleId AND document_id = pDocumentId;
				
				UPDATE pjs.document_users SET 
					uid = pSEId 
				WHERE document_id = pDocumentId AND role_id = cSERoleId;
				
				SELECT INTO lRoundId current_round_id 
				FROM pjs.documents d
				WHERE id = pDocumentId;
				
				IF EXISTS (
					SELECT *
					FROM pjs.document_review_rounds
					WHERE id = lRoundId AND round_type_id = cReviewRoundStateId
				) THEN
					-- If the current round is review round - create a new version for the new SE and set it to the round user
					SELECT INTO lDocumentVersion id 
					FROM spCreateDocumentVersion(pDocumentId, pSEId, cDocumentSEVersionType, lAuthorDocumentVersion);
					
					UPDATE pjs.document_review_round_users ru SET
						document_version_id = lDocumentVersion
					FROM  pjs.document_users u					
					WHERE ru.round_id = lRoundId AND ru.document_user_id = u.id AND u.uid = pSEId AND u.role_id = cSERoleId;
					
					-- Set the lock flag so that a merge of the reviewer versions will be performed
					UPDATE pjs.document_review_rounds SET
						review_lock = false
					WHERE id = lRoundId;
				END IF;
				
				-- creating SE change event (and Assign Event)
				SELECT INTO lJournalId d.journal_id FROM pjs.documents d WHERE d.id = pDocumentId;
				SELECT INTO lRes.event_id event_id FROM spCreateEvent(cSEChangeEventType, pDocumentId, pUid, lJournalId, lSEOldUid, cSERoleId);
				SELECT INTO lRes.event_id_sec event_id FROM spCreateEvent(lSEAssignEventType, pDocumentId, pUid, lJournalId, null, null);
				
				IF(lRoundType = cReviewRoundType) THEN
					-- check SE can take decision and enough reviewers assigned
					SELECT INTO lCanTakeDecision result FROM pjs.spCheckIfSECanTakeADecision(pDocumentId);
					--RAISE EXCEPTION 'SE Decision: %', lCanTakeDecision;
					UPDATE pjs.document_review_rounds SET can_proceed = lCanTakeDecision WHERE id = lCurrentRoundId;
					SELECT INTO lEnoughReviewers result FROM pjs.spCheckEnoughReviewrs(pDocumentId);
					--RAISE EXCEPTION 'Enough Reviewers: %', lEnoughReviewers;
					UPDATE pjs.document_review_rounds SET enough_reviewers = lEnoughReviewers WHERE id = lCurrentRoundId;
				END IF;
			END IF;
		END IF;
	
		SELECT INTO lRoundId, lRoundType 
			d.current_round_id, dr.round_type_id 
		FROM pjs.documents d
		JOIN pjs.document_review_rounds dr ON dr.id = d.current_round_id
		WHERE d.id = pDocumentId;
		
		IF(lRoundType = cReviewRoundType) THEN
			SELECT INTO lEnoughReviewers, lCanProceed 
				enough_reviewers, can_proceed 
			FROM pjs.document_review_rounds 
			WHERE id = lRoundId;


			-- not enough reviewers
			IF (lEnoughReviewers = FALSE) THEN
				PERFORM pjs.spUpdateDueDates(2, pDocumentId, cNotEnoughReviewersEventType, lRoundId, NULL);
			END IF;
			-- not enough reviewers
			IF (lCanProceed = TRUE) THEN
				PERFORM pjs.spUpdateDueDates(1, pDocumentId, cCanProceedEventType, NULL, lDocumentReviewRoundUserId);
			ELSE
				PERFORM pjs.spUpdateDueDates(1, pDocumentId, lSEAssignEventType, NULL, lDocumentReviewRoundUserId);
			END IF;
		END IF;
	
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs."spDocumentSE"(
	pOper int,
	pDocumentId bigint,
	pSEId int,
	pUid int
) TO iusrpmt;
