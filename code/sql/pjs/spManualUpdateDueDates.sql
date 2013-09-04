DROP TYPE IF EXISTS ret_spManualUpdateDueDates CASCADE;
CREATE TYPE ret_spManualUpdateDueDates AS (result boolean);

CREATE OR REPLACE FUNCTION pjs.spManualUpdateDueDates(
	pOper int,
	pDocumentId bigint,
	pRoundId bigint,
	pRoundUserId bigint,
	pDuedate timestamp,
	pEventId int
)
	RETURNS ret_spManualUpdateDueDates AS
$BODY$
	DECLARE
		lRes ret_spManualUpdateDueDates;
		lRoundDueDate timestamp;
		lDocumentId bigint;
		lJournalSectionId int;
		lJournalId int;
		lSectionId int;
		lOffsetDays int;
		lOffsetDaysEnd int;
	BEGIN		
		IF pDuedate < now() THEN
			RAISE EXCEPTION 'Invalid date';
		END IF;
		
		/* Get event id */
		SELECT INTO lDocumentId document_id FROM pjs.document_review_rounds WHERE id = pRoundId;
		-- get journal_section_id
		SELECT INTO lJournalSectionId, lJournalId journal_section_id, journal_id FROM pjs.documents WHERE id = lDocumentId;
		-- get pwt_paper_type_id
		SELECT INTO lSectionId pwt_paper_type_id FROM pjs.journal_sections WHERE id = lJournalSectionId;
		-- offset days
		SELECT INTO lOffsetDays, lOffsetDaysEnd "offset", offset_end FROM pjs.getEventOffset(pEventId, lJournalId, lSectionId);
		/* Get event id */
		
		IF pOper = 1 THEN 
			-- update round_due_date if there is pRoundId param
			/*IF(pRoundId IS NOT NULL) THEN
				UPDATE pjs.document_review_rounds 
				SET 
					round_due_date = pDueDate,
					deadline_date = pDueDate + (lOffsetDaysEnd*INTERVAL '1 day')
				WHERE id = pRoundId;
			END IF;*/
			
			-- update user decision due_date
			IF(pRoundUserId IS NOT NULL) THEN
					UPDATE pjs.document_review_round_users 
					SET 
						due_date = pDueDate,
						deadline_date = pDueDate + (lOffsetDaysEnd*INTERVAL '1 day')
					WHERE id = pRoundUserId;
			END IF;
		ELSEIF pOper = 2 THEN 
			IF(pRoundId IS NOT NULL) THEN
					UPDATE pjs.document_review_rounds 
					SET 
						reviewers_assignment_duedate = pDueDate,
						deadline_date = pDueDate + (lOffsetDaysEnd*INTERVAL '1 day')
					WHERE id = pRoundId;
			END IF;
		ELSEIF pOper = 3 THEN 
			
			IF(pRoundId IS NOT NULL AND pRoundUserId IS NOT NULL) THEN
				--RAISE EXCEPTION 'OFFSET: %', lOffsetDays;
				UPDATE pjs.document_user_invitations 
				SET 
					due_date = pDueDate,
					deadline_date = pDueDate + (lOffsetDaysEnd*INTERVAL '1 day')
				WHERE uid = pRoundUserId AND round_id = pRoundId;
			END IF;
			--RAISE EXCEPTION 'OFFSET1: %', lOffsetDays;
		END IF;
		
		lRes.result = TRUE;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
-- ALTER FUNCTION pjs.spManualUpdateDueDates(integer, integer, integer, integer, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pjs.spManualUpdateDueDates(
	pOper int,
	pDocumentId bigint,
	pRoundId bigint,
	pRoundUserId bigint,
	pDueDate timestamp,
	pEventId int
) TO iusrpmt;
