DROP TYPE IF EXISTS ret_spDocumentLE CASCADE;
CREATE TYPE ret_spDocumentLE AS (
	result int,
	event_id bigint,
	event_id_sec bigint
);

CREATE OR REPLACE FUNCTION spDocumentLE(
	pOper int,
	pDocumentId bigint,
	pLEId int,
	pUid int
)
  RETURNS ret_spDocumentLE AS
$BODY$
	DECLARE
		lRes ret_spDocumentLE;	
				
		lJournalEditorRoleId int;
		lLERoleId int;
		lReadyForLayoutState int;
		lInLayoutState int;
		lLayoutReviewRoundTypeId int;		
		lRoundId bigint;
		lAuthorSubmittedVersionType int;
		lLEVersionTypeId int;
		lRecord record;
		lDocumentUserId bigint;
		lERoundType int;
		lERoleId int;
		lCurrentAuthorVersionId bigint;
		lEDecisionId int;
		lDocumentReviewRoundUserId bigint;
		lDocumentRoundId bigint;
		lLERoundUsrId bigint;
		lLEAssignEventType int;
		lJournalId int;
		lLEChangeEventType int;
		lLEUid bigint;
		lRoundUsrId bigint;
		cReadyForCopyEditing CONSTANT int := 15;
		cLEVersionTypeId CONSTANT int := 5;
		lVersionId bigint;
		lXml xml;
	BEGIN		
		lJournalEditorRoleId = 3;
		lLERoleId = 8;
		
		lReadyForLayoutState = 13;
		lInLayoutState = 4;
		lLayoutReviewRoundTypeId = 3;
		lAuthorSubmittedVersionType = 1;
		lLEVersionTypeId = 5;
		lERoundType = 4;
		lERoleId = 2;
		lEDecisionId = 1;
		lLEAssignEventType = 16;
		lLEChangeEventType = 31;
		
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
			WHERE u.id = pLEId AND d.id = pDocumentId AND ju.role_id = lLERoleId
		) THEN
			RAISE EXCEPTION 'pjs.theSpecifiedUserIsNotLEForTheJournalOfTheDocument';
		END IF;
		
		SELECT INTO lCurrentAuthorVersionId id 
			FROM pjs.document_versions
			WHERE document_id = pDocumentId AND version_type_id = lAuthorSubmittedVersionType 
			ORDER BY id DESC LIMIT 1;
		
		IF pOper = 1 THEN -- Add LE
			SELECT INTO lJournalId d.journal_id FROM pjs.documents d WHERE d.id = pDocumentId;
			IF NOT EXISTS (
				SELECT * 
				FROM pjs.document_users du
				WHERE document_id = pDocumentId AND role_id = lLERoleId
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
				
				INSERT INTO pjs.document_users(document_id, uid, role_id) VALUES (pDocumentId, pLEId, lLERoleId);
				lDocumentUserId = currval('pjs.document_users_id_seq');
				
				-- Update document state if the document is in the waiting le assignment state
				IF EXISTS (
					SELECT *
					FROM pjs.documents
					WHERE id = pDocumentId AND state_id IN (lReadyForLayoutState, cReadyForCopyEditing)
				) THEN
					
					-- This would be the first layot review round
					SELECT INTO lRoundId id FROM spCreateDocumentRound(pDocumentId, lLayoutReviewRoundTypeId);
					UPDATE pjs.document_review_rounds SET create_from_version_id = lCurrentAuthorVersionId WHERE id = lRoundId;
					
					UPDATE pjs.documents SET
						state_id = lInLayoutState,
						current_round_id = lRoundId
					WHERE id = pDocumentId AND state_id IN (lReadyForLayoutState, cReadyForCopyEditing);
					
					-- Create the document revisions for the LE
					SELECT INTO lVersionId id FROM spCreateDocumentVersion(pDocumentId, pLEId, cLEVersionTypeId, lCurrentAuthorVersionId);
					
					INSERT INTO pjs.document_review_round_users(round_id, document_user_id, document_version_id) VALUES (lRoundId, lDocumentUserId, lVersionId);
					lLERoundUsrId = currval('pjs.document_review_round_reviewers_id_seq');
					
					SELECT INTO lXml xml FROM pjs.pwt_document_versions WHERE version_id = lVersionId;

					PERFORM spCreateArticle(pDocumentId,	lXml::varchar);
					
					UPDATE pjs.document_review_rounds SET decision_round_user_id = lLERoundUsrId WHERE id = lRoundId;
				ELSE 
					-- Create a revision for the document -if the document is in a review state
					IF EXISTS (
						SELECT *
						FROM pjs.documents
						WHERE id = pDocumentId AND state_id = lInLayoutState
					) THEN
						SELECT INTO lRoundId id
						FROM pjs.document_review_rounds
						WHERE document_id = pDocumentId AND round_type_id = lLayoutReviewRoundTypeId 
						ORDER BY id DESC 
						LIMIT 1;
						
						INSERT INTO pjs.document_review_round_users(round_id, document_user_id, document_version_id) VALUES (lRoundId, lDocumentUserId, lCurrentAuthorVersionId);
					END IF;
				END IF;
				
				SELECT INTO lRes.event_id event_id FROM spCreateEvent(lLEAssignEventType, pDocumentId, pUid, lJournalId, null, null);
			ELSE
				SELECT INTO lLEUid, lDocumentUserId uid, id FROM pjs.document_users WHERE role_id = lLERoleId AND document_id = pDocumentId;
				
				SELECT INTO lVersionId document_version_id 
				FROM pjs.document_review_round_users drrus
				JOIN pjs.documents d ON d.current_round_id = drrus.round_id AND d.id = pDocumentId
				WHERE drrus.document_user_id = lDocumentUserId;
				
				UPDATE pjs.document_users SET uid = pLEId WHERE document_id = pDocumentId AND role_id = lLERoleId;
				UPDATE pjs.document_versions SET uid = pLEId WHERE id = lVersionId;
				SELECT INTO lRes.event_id event_id FROM spCreateEvent(lLEChangeEventType, pDocumentId, pUid, lJournalId, lLEUid, lLERoleId);
				SELECT INTO lRes.event_id_sec event_id FROM spCreateEvent(lLEAssignEventType, pDocumentId, pUid, lJournalId, null, null);
			END IF;
			
			-- manage due dates
			SELECT INTO lRoundId current_round_id FROM pjs.documents WHERE id = pDocumentId;
			SELECT INTO lRoundUsrId dru.id 
			FROM pjs.document_users du
			JOIN pjs.document_review_round_users dru ON dru.document_user_id = du.id AND dru.round_id = lRoundId
			WHERE du.document_id = pDocumentId AND du.role_id = lLERoleId;
			
			PERFORM pjs.spUpdateDueDates(1, pDocumentId, lLEAssignEventType, lRoundId, lRoundUsrId);
		ELSE -- Remove
			SELECT INTO lDocumentUserId id 
			FROM pjs.document_users du
			WHERE document_id = pDocumentId AND uid = pLEId AND role_id = lLERoleId;			
			
			IF lDocumentUserId IS NOT NULL THEN
				-- Remove the records from document_review_round_users
				DELETE FROM pjs.document_review_round_users
				WHERE document_user_id = lDocumentUserId;
				
				DELETE FROM pjs.document_users du
				WHERE id = lDocumentUserId;
			END IF;
			
		END IF;
		
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spDocumentLE(
	pOper int,
	pDocumentId bigint,
	pLEId int,
	pUid int
) TO iusrpmt;
