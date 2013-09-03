DROP TYPE IF EXISTS ret_spDocumentCE CASCADE;
CREATE TYPE ret_spDocumentCE AS (
	result int,
	event_id bigint,
	event_id_sec bigint
);

CREATE OR REPLACE FUNCTION spDocumentCE(
	pOper int,
	pDocumentId bigint,
	pRoundId bigint,
	pCEId int,
	pUid int
)
  RETURNS ret_spDocumentCE AS
$BODY$
	DECLARE
		lRes ret_spDocumentCE;	
				
		lJournalEditorRoleId int;
		lCERoleId int;
		lReadyForCopyEditingState int;
		lInCopyEditState int;
		lCopyReviewRoundTypeId int;		
		lRoundId bigint;
		lVersionId bigint;
		lAuthorSubmittedVersionType int;
		lCEVersionTypeId int;
		lRecord record;
		lDocumentUserId bigint;
		lERoundType int;
		lERoleId int;
		lCurrentAuthorVersionId bigint;
		lEDecisionId int;
		lDocumentReviewRoundUserId bigint;
		lDocumentRoundId bigint;
		lCERoundUsrId bigint;
		lCEAssignEventType int;
		lJournalId int;
		lCEChangeEventType int;
		lOldCEUsrId bigint;
		lRoundUsrId bigint;
		lCEDocumentUserId bigint;
		lCEVersionId bigint;
	BEGIN		
		lJournalEditorRoleId = 2;
		lCERoleId = 9;
		
		lReadyForCopyEditingState = 15;
		lInCopyEditState = 8;
		lCopyReviewRoundTypeId = 2;
		lAuthorSubmittedVersionType = 1;
		lCEVersionTypeId = 7;
		lERoundType = 4;
		lERoleId = 2;
		lEDecisionId = 1;
		lCEAssignEventType = 17;
		lCEChangeEventType = 34;
		
		IF NOT EXISTS (
			SELECT u.id
			FROM usr u
			JOIN  pjs.journal_users ju ON ju.uid = u.id
			JOIN pjs.documents d ON d.journal_id = ju.journal_id
			WHERE u.id = pUid AND d.id = pDocumentId AND ju.role_id = lJournalEditorRoleId
		) THEN
			RAISE EXCEPTION 'pjs.onlyJournalManagersCanExecuteThisAction';
		END IF;
		
		IF NOT EXISTS (
			SELECT u.id
			FROM usr u
			JOIN  pjs.journal_users ju ON ju.uid = u.id
			JOIN pjs.documents d ON d.journal_id = ju.journal_id
			WHERE u.id = pCEId AND d.id = pDocumentId AND ju.role_id = lCERoleId
		) THEN
			RAISE EXCEPTION 'pjs.theSpecifiedUserIsNotCEForTheJournalOfTheDocument';
		END IF;
		
		SELECT INTO lCurrentAuthorVersionId id 
			FROM pjs.document_versions
			WHERE document_id = pDocumentId AND version_type_id = lAuthorSubmittedVersionType 
			ORDER BY id DESC LIMIT 1;
		
		IF pOper = 1 THEN -- Add CE
			SELECT INTO lJournalId d.journal_id FROM pjs.documents d WHERE d.id = pDocumentId;
			
			IF NOT EXISTS (
				SELECT du.* 
				FROM pjs.documents d
				JOIN pjs.document_review_round_users drrus ON drrus.round_id = d.current_round_id
				JOIN pjs.document_users du ON du.id = drrus.document_user_id AND du.role_id = lCERoleId
				WHERE d.id = pDocumentId
			) THEN
				
				-- >>> closing editor round
				SELECT INTO lDocumentUserId id FROM pjs.document_users WHERE role_id = lERoleId AND document_id = pDocumentId;
				
				SELECT INTO lDocumentRoundId drr.id 
				FROM pjs.documents d
				JOIN pjs.document_review_rounds drr ON drr.id = d.current_round_id 
				WHERE d.id = pDocumentId AND drr.round_type_id = lERoundType AND drr.decision_id IS NULL 
				LIMIT 1;
				
				UPDATE pjs.document_review_round_users SET decision_id = lEDecisionId WHERE round_id = lDocumentRoundId AND decision_id IS NULL;
				UPDATE pjs.document_review_rounds SET decision_id = lEDecisionId WHERE id = lDocumentRoundId;
				-- <<< closing editor round
				
				INSERT INTO pjs.document_users(document_id, uid, role_id) VALUES (pDocumentId, pCEId, lCERoleId);
				lDocumentUserId = currval('pjs.document_users_id_seq');
				
				-- Update document state if the document is in the waiting ce assignment state
				IF EXISTS (
					SELECT *
					FROM pjs.documents
					WHERE id = pDocumentId AND state_id = lReadyForCopyEditingState
				) THEN

					-- This would be the first layot review round
					SELECT INTO lRoundId id FROM spCreateDocumentRound(pDocumentId, lCopyReviewRoundTypeId);
					UPDATE pjs.document_review_rounds SET create_from_version_id = lCurrentAuthorVersionId WHERE id = lRoundId;
					-- RAISE NOTICE 'Roundid %', lRoundId;
					
					UPDATE pjs.documents SET
						state_id = lInCopyEditState,
						current_round_id = lRoundId
					WHERE id = pDocumentId AND state_id = lReadyForCopyEditingState;
				
					SELECT INTO lVersionId id FROM spCreateDocumentVersion(pDocumentId, pCEId, lCEVersionTypeId, lCurrentAuthorVersionId);
					INSERT INTO pjs.document_review_round_users(round_id, document_user_id, document_version_id) VALUES (lRoundId, lDocumentUserId, lVersionId);
					lCERoundUsrId = currval('pjs.document_review_round_reviewers_id_seq');
					UPDATE pjs.document_review_rounds SET decision_round_user_id = lCERoundUsrId WHERE id = lRoundId;
				ELSE 
					-- Create a revision for the document -if the document is in a review state
					IF EXISTS (
						SELECT *
						FROM pjs.documents
						WHERE id = pDocumentId AND state_id = lInCopyEditState
					) THEN
						SELECT INTO lRoundId id
						FROM pjs.document_review_rounds
						WHERE document_id = pDocumentId AND round_type_id = lCopyReviewRoundTypeId 
						ORDER BY id DESC 
						LIMIT 1;
						
						SELECT INTO lVersionId id FROM spCreateDocumentVersion(pDocumentId, pCEId, lCEVersionTypeId, lCurrentAuthorVersionId);
						INSERT INTO pjs.document_review_round_users(round_id, document_user_id, document_version_id) VALUES (lRoundId, lDocumentUserId, lVersionId);
					END IF;
				END IF;
				
				SELECT INTO lRes.event_id event_id FROM spCreateEvent(lCEAssignEventType, pDocumentId, pUid, lJournalId, null, null);
				
			ELSE
				SELECT INTO lOldCEUsrId, lCEDocumentUserId, lCEVersionId du.uid, du.id, drrus.document_version_id
				FROM pjs.documents d
				JOIN pjs.document_review_round_users drrus ON drrus.round_id = d.current_round_id
				JOIN pjs.document_users du ON du.id = drrus.document_user_id AND du.role_id = lCERoleId
				WHERE d.id = pDocumentId;
				
				SELECT INTO lRes.event_id_sec event_id FROM spCreateEvent(lCEChangeEventType, pDocumentId, pUid, lJournalId, lOldCEUsrId, lCERoleId);
				UPDATE pjs.document_users SET uid = pCEId WHERE document_id = pDocumentId AND role_id = lCERoleId AND id = lCEDocumentUserId;
				UPDATE pjs.document_versions SET uid = pCEId WHERE id = lCEVersionId;
				SELECT INTO lRes.event_id event_id FROM spCreateEvent(lCEAssignEventType, pDocumentId, pUid, lJournalId, null, null);
			END IF;
		END IF;
	
		-- manage due dates
		SELECT INTO lRoundId current_round_id FROM pjs.documents WHERE id = pDocumentId;
		SELECT INTO lRoundUsrId dru.id 
		FROM pjs.document_users du
		JOIN pjs.document_review_round_users dru ON dru.document_user_id = du.id AND dru.round_id = lRoundId
		WHERE du.document_id = pDocumentId AND du.role_id = lCERoleId;
		
		PERFORM pjs.spUpdateDueDates(1, pDocumentId, lCEAssignEventType, lRoundId, lRoundUsrId);
	
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spDocumentCE(
	pOper int,
	pDocumentId bigint,
	pRoundId bigint,
	pCEId int,
	pUid int
) TO iusrpmt;
