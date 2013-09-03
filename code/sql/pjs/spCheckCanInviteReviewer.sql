DROP TYPE IF EXISTS pjs."ret_spCheckCanInviteReviewer" CASCADE;
CREATE TYPE pjs."ret_spCheckCanInviteReviewer" AS (
	result boolean
);

CREATE OR REPLACE FUNCTION pjs."spCheckCanInviteReviewer"(	
	pDocumentId bigint,
	pRoundId bigint,
	pReviewerType int
)
  RETURNS pjs."ret_spCheckCanInviteReviewer" AS
$BODY$
	DECLARE
		lRes pjs."ret_spCheckCanInviteReviewer";
		lDeadlineDate timestamp;
		cNominatedReviewerType CONSTANT int := 5;
		lSEAssignEventType int;
		lJournalSectionId int;
		lJournalId int;
		lSectionId int;
		lOffsetDays int;
		lAcceptReviewOffsetDays int;
		lNominatedOffsetDays int;
		lCanTakeDecisionOffsetDays int;
		cReviewerInvitationEventType CONSTANT int := 3;
		cReviewAcceptedEventType int := 6;
		cCanProceedEventType CONSTANT int := 38;
		lDocumentReviewType int;
		cPanelReviewerRoleId CONSTANT int := 7;
		cPanelReviewerInvitationEventType CONSTANT int := 36;
	BEGIN
		lRes.result = TRUE;
	
		-- selecting round due date
		SELECT INTO lDeadlineDate deadline_date FROM pjs.document_review_rounds WHERE id = pRoundId;
		/*-- selecting document review type
		SELECT INTO lDocumentReviewType, lJournalSectionId, lJournalId journal_section_id, journal_id FROM pjs.documents where id = pDocumentId;
		
		-- get journal_section_id
		SELECT INTO lJournalSectionId, lJournalId journal_section_id, journal_id FROM pjs.documents WHERE id = pDocumentId;
		
		-- get pwt_paper_type_id
		SELECT INTO lSectionId pwt_paper_type_id FROM pjs.journal_sections WHERE id = lJournalSectionId;

		-- get journal_section_id
		SELECT INTO lJournalSectionId, lJournalId journal_section_id, journal_id FROM pjs.documents WHERE id = pDocumentId;
		
		-- get pwt_paper_type_id
		SELECT INTO lSectionId pwt_paper_type_id FROM pjs.journal_sections WHERE id = lJournalSectionId;
	*/
		IF (pReviewerType = cNominatedReviewerType) THEN
		/*
			-- offset days (accept_review_days)
			SELECT INTO lAcceptReviewOffsetDays "offset" FROM pjs.getEventOffset(cReviewerInvitationEventType, lJournalId, lSectionId);
			-- offset days (nominated_days)
			SELECT INTO lNominatedOffsetDays "offset" FROM pjs.getEventOffset(cReviewAcceptedEventType, lJournalId, lSectionId);
			-- offset days (can take decision)
			SELECT INTO lCanTakeDecisionOffsetDays "offset" FROM pjs.getEventOffset(cCanProceedEventType, lJournalId, lSectionId);
			
			--RAISE EXCEPTION 'lNominatedOffsetDays: %, lAcceptReviewOffsetDays: %, lCanTakeDecisionOffsetDays: %', lNominatedOffsetDays, lAcceptReviewOffsetDays, lCanTakeDecisionOffsetDays;
			*/
			IF(now() < lDeadlineDate) THEN
				lRes.result = TRUE;
			ELSE
				lRes.result = FALSE;
			END IF;
			
		ELSEIF (pReviewerType = cPanelReviewerRoleId) THEN
			/*
			-- offset days (accept_review_days)
			SELECT INTO lAcceptReviewOffsetDays "offset" FROM pjs.getEventOffset(cPanelReviewerInvitationEventType, lJournalId, lSectionId);
			
			-- offset days (nominated_days)
			SELECT INTO lNominatedOffsetDays "offset" FROM pjs.getEventOffset(cReviewAcceptedEventType, lJournalId, lSectionId);
			
			-- offset days (can take decision)
			SELECT INTO lCanTakeDecisionOffsetDays "offset" FROM pjs.getEventOffset(cCanProceedEventType, lJournalId, lSectionId);
			*/
			IF(now() < lDeadlineDate) THEN
				lRes.result = TRUE;
			ELSE
				lRes.result = FALSE;
			END IF;
			
		END IF;
		
		RETURN lRes;
		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs."spCheckCanInviteReviewer"(
	pDocumentId bigint,
	pRoundId bigint,
	pReviewerType int
) TO iusrpmt;
