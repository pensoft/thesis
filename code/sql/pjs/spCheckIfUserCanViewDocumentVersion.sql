DROP TYPE IF EXISTS ret_spCheckIfUserCanViewDocumentVersion CASCADE;
CREATE TYPE ret_spCheckIfUserCanViewDocumentVersion AS (result int);

/*
* pjs."spCheckIfUserCanViewDocumentVersion"
*
* pDocumentId - document id (from pjs.documents)
* pVersionId - version id (from pjs.document_versions)
* pUid - user id (from public.usr)
*/
CREATE OR REPLACE FUNCTION pjs."spCheckIfUserCanViewDocumentVersion"(
	pDocumentId bigint,
	pVersionId bigint,
	pUid bigint
)
  RETURNS ret_spCheckIfUserCanViewDocumentVersion AS
$BODY$
	DECLARE
		-- variables
		lRes ret_spCheckIfUserCanViewDocumentVersion;	
		lDocumentState int;
		lRoundUserState int;
		lLastAuthorVersion bigint;
		lSubmittingAuthorId bigint;
		lSEDocumentUserId bigint;
		lRoundAuthorVersion bigint;
		lVersionType int;
		lJournalId int;
		lHadDecision int;
		
		-- user roles
		cEditorRoleId CONSTANT int := 2;
		cSERoleId CONSTANT int := 3;
		cDedicatedReviewerRoleId CONSTANT int := 5;
		cPanelReviewerRoleId CONSTANT int := 7;
		cPublicReviewerRoleId CONSTANT int := 6;
		cCERoleId CONSTANT int := 9;
		cLERoleId CONSTANT int := 8;
		
		-- waiting author document states
		cWaitingAuthorVersionAfterReviewRound CONSTANT int := 9;
		cWaitingAuthorVersionAfterLayoutRound CONSTANT int := 10;
		cWaitingAuthorToProceedToLayout CONSTANT int := 12;
		cWaitingAuthorToProceedToCopyEditing CONSTANT int := 14;
		cWaitingAuthorToProceedToLayoutAfterCopyEditing CONSTANT int := 17;
		
		-- some other document states
		cInLayoutState CONSTANT int := 4;
		cInCopyEditingState CONSTANT int := 8;
		cInApprovedForPublicationState CONSTANT int := 11;
		cReadyForLayoutState CONSTANT int := 13;
		cReadyForCopyEditingState CONSTANT int := 15;
		cCEDocumentVersionType CONSTANT int := 7;
		cLEDocumentVersionType CONSTANT int := 5;
		cPublicReviewVersionType CONSTANT int := 10;
		
		-- round types
		cReviewRoundType CONSTANT int := 1;
		cCERoundType CONSTANT int := 2;
		cLERoundType CONSTANT int := 3;
		
		-- version types
		cAuthorVersionType CONSTANT int := 1;
		cReviewerVersionType CONSTANT int := 2;
		cSEVersionType CONSTANT int := 3;
		cCEVersionType CONSTANT int := 7;
		
		-- invitation states
		cNewInvitationStateId CONSTANT int := 1;
		cConfirmedInvitationStateId CONSTANT int := 2;
		cConfirmedBySEInvitationStateId CONSTANT int := 5;
		
		
	BEGIN		
		lRes.result = 0;
	
		SELECT INTO lDocumentState, lSubmittingAuthorId, lJournalId state_id, submitting_author_id, journal_id FROM pjs.documents WHERE id = pDocumentId;
		
		-- if document is in waiting author state
		IF(
			lDocumentState = cWaitingAuthorVersionAfterReviewRound OR 
			lDocumentState = cWaitingAuthorVersionAfterLayoutRound OR 
			lDocumentState = cWaitingAuthorToProceedToLayout OR 
			lDocumentState = cWaitingAuthorToProceedToCopyEditing OR 
			lDocumentState = cWaitingAuthorToProceedToLayoutAfterCopyEditing
		) THEN
			-- if this version is the last author version then you cannot view it (it's in process)
			SELECT INTO lLastAuthorVersion id FROM pjs.document_versions WHERE document_id = pDocumentId AND version_type_id = cAuthorVersionType ORDER BY id DESC LIMIT 1;
			IF (lLastAuthorVersion = pVersionId) THEN 
				RETURN lRes;
			END IF;
		END IF;
		
		SELECT INTO lSEDocumentUserId id FROM pjs.document_users WHERE uid = pUid AND document_id = pDocumentId AND role_id = cSERoleId;
		SELECT INTO lRoundAuthorVersion create_from_version_id FROM pjs.document_review_rounds WHERE document_id = pDocumentId AND round_type_id = cReviewRoundType AND create_from_version_id = pVersionId;
		SELECT INTO lVersionType version_type_id FROM pjs.document_versions WHERE document_id = pDocumentId AND id = pVersionId; --AND version_type_id = cReviewerVersionType;
		
		IF(lVersionType = cPublicReviewVersionType) THEN
			lRes.result = 1;
			RETURN lRes;
		END IF;
		
		-- if this user is Editor it can see all versions
		IF EXISTS( SELECT id FROM pjs.journal_users WHERE uid = pUid AND role_id = cEditorRoleId AND journal_id = lJournalId ) THEN
			--RAISE EXCEPTION 'user is Editor';
			lRes.result = 1;
			RETURN lRes;
		-- if user view his own version
		ELSEIF EXISTS( SELECT id FROM pjs.document_versions WHERE document_id = pDocumentId AND uid = pUid AND id = pVersionId) THEN
			--RAISE EXCEPTION 'user want to see his own version';
			lRes.result = 1;
			RETURN lRes;
		-- if this user is SE (can view author version and reviewers versions)
		ELSEIF(lSEDocumentUserId IS NOT NULL) THEN
			IF(lRoundAuthorVersion IS NOT NULL OR lVersionType = cReviewerVersionType) THEN
				lRes.result = 1;
				RETURN lRes;
			END IF;
		-- other cases (reviewer, author, CE, LE)
		ELSE
			-- if this user is the submitting author and want to see SE/CE version
			IF (lSubmittingAuthorId = pUid AND (lVersionType = cSEVersionType OR lVersionType = cCEVersionType)) THEN
				SELECT INTO lHadDecision decision_id FROM pjs.document_review_round_users WHERE document_version_id = pVersionId;
				IF(lHadDecision IS NOT NULL) THEN
					lRes.result = 1;
					RETURN lRes;
				END IF;
			END IF;
			
			-- Copy Editor (and try to open author version for CE round)
			IF EXISTS (SELECT id FROM pjs.document_users WHERE document_id = pDocumentId AND uid = pUid AND role_id = cCERoleId) THEN
				IF EXISTS (SELECT id FROM pjs.document_review_rounds WHERE document_id = pDocumentId AND create_from_version_id = pVersionId AND round_type_id = cCERoundType) THEN
					lRes.result = 1;
					RETURN lRes;
				END IF;
			END IF;
			
			-- Layout Editor (and try to open author version for LE round)
			IF EXISTS (SELECT id FROM pjs.document_users WHERE document_id = pDocumentId AND uid = pUid AND role_id = cLERoleId) THEN
				IF EXISTS (SELECT id FROM pjs.document_review_rounds WHERE document_id = pDocumentId AND create_from_version_id = pVersionId AND round_type_id = cLERoundType) THEN
					lRes.result = 1;
					RETURN lRes;
				END IF;
			END IF;
			
			-- Start Reviewer Checks
			/*Reviewer (panel, nominated) - and want to view author version
				We must check if this user is added as reviewer for the current version round and then he can view this version
			*/
			-- this is for panel reviewer (only ivited)
			IF EXISTS(
				SELECT dui.id
				FROM pjs.document_user_invitations dui
				JOIN pjs.document_review_rounds drr ON drr.id = dui.round_id AND drr.document_id = pDocumentId AND create_from_version_id = pVersionId
				WHERE dui.uid = pUid 
					AND dui.role_id IN (cDedicatedReviewerRoleId, cPanelReviewerRoleId)
					AND (CASE WHEN dui.role_id = cDedicatedReviewerRoleId THEN dui.state_id IN (cNewInvitationStateId, cConfirmedInvitationStateId, cConfirmedBySEInvitationStateId) ELSE TRUE END)
			) THEN
				lRes.result = 1;
				RETURN lRes;
			END IF;

			-- this is for nominated/panel reviewer (added in pjs.document_users)
			IF EXISTS (
				SELECT drru.id 
				FROM pjs.document_review_round_users drru
				JOIN pjs.document_users du ON du.id = drru.document_user_id AND du.document_id = pDocumentId AND du.role_id IN (cDedicatedReviewerRoleId,cPanelReviewerRoleId)
				JOIN pjs.document_review_rounds drr ON drr.id = drru.round_id AND drr.document_id = pDocumentId AND create_from_version_id = pVersionId
				WHERE du.uid = pUid
			) THEN
				lRes.result = 1;
				RETURN lRes;
			END IF;
			-- END Reviewer Checks
			
		END IF;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs."spCheckIfUserCanViewDocumentVersion"(
	pDocumentId bigint,
	pVersionId bigint,
	pUid bigint
) TO iusrpmt;
