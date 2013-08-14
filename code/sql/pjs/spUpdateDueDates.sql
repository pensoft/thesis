DROP TYPE IF EXISTS ret_spUpdateDueDates CASCADE;
CREATE TYPE ret_spUpdateDueDates AS (result boolean);

CREATE OR REPLACE FUNCTION pjs.spUpdateDueDates(
	pOper int,
	pDocumentId bigint,
	pEventTypeId int,
	pRoundId bigint,
	pRoundUserId bigint
)
	RETURNS ret_spUpdateDueDates AS
$BODY$
	DECLARE
		lRes ret_spUpdateDueDates;
		lJournalSectionId int;
		lJournalId int;
		lSectionId int;
		lOffsetDays int;
		lOffsetDaysEnd int;
	BEGIN		
		
		-- get journal_section_id
		SELECT INTO lJournalSectionId, lJournalId journal_section_id, journal_id FROM pjs.documents WHERE id = pDocumentId;
		
		-- get pwt_paper_type_id
		SELECT INTO lSectionId pwt_paper_type_id FROM pjs.journal_sections WHERE id = lJournalSectionId;
		
		-- offset days
		SELECT INTO lOffsetDays, lOffsetDaysEnd "offset", offset_end FROM pjs.getEventOffset(pEventTypeId, lJournalId, lSectionId);

		IF pOper = 1 THEN 
			-- update deadline_date if there is pRoundId param
			IF(pRoundId IS NOT NULL) THEN
				UPDATE pjs.document_review_rounds SET deadline_date = now() + ((lOffsetDays + lOffsetDaysEnd)*INTERVAL '1 day') WHERE id = pRoundId;
			END IF;
			
			-- update user decision due_date
			IF(pRoundUserId IS NOT NULL) THEN
					UPDATE pjs.document_review_round_users 
					SET 
						due_date = now() + (lOffsetDays*INTERVAL '1 day'),
						deadline_date = now() + ((lOffsetDays + lOffsetDaysEnd)*INTERVAL '1 day')
					WHERE id = pRoundUserId;
			END IF;
		ELSEIF pOper = 2 THEN 
			IF(pRoundId IS NOT NULL) THEN
					UPDATE pjs.document_review_rounds 
					SET 
						reviewers_assignment_duedate = now() + (lOffsetDays*INTERVAL '1 day'),
						deadline_date = now() + ((lOffsetDays + lOffsetDaysEnd)*INTERVAL '1 day')
					WHERE id = pRoundId;
			END IF;
		ELSEIF pOper = 3 THEN 
			
			IF(pRoundId IS NOT NULL AND pRoundUserId IS NOT NULL) THEN
				--RAISE EXCEPTION 'OFFSET: %', lOffsetDays;
				UPDATE pjs.document_user_invitations 
				SET 
					due_date = now() + (lOffsetDays*INTERVAL '1 day'),
					deadline_date = now() + ((lOffsetDays + lOffsetDaysEnd)*INTERVAL '1 day')
				WHERE uid = pRoundUserId 
					AND round_id = pRoundId;
			END IF;
			--RAISE EXCEPTION 'OFFSET1: %', lOffsetDays;
		ELSEIF pOper = 4 THEN 
			
			IF(pDocumentId IS NOT NULL) THEN
				--RAISE EXCEPTION 'OFFSET: %', lOffsetDays;
				UPDATE pjs.documents SET panel_duedate = now() + (lOffsetDays*INTERVAL '1 day') WHERE id = pDocumentId;
			END IF;
			--RAISE EXCEPTION 'OFFSET1: %', lOffsetDays;
		ELSEIF pOper = 5 THEN 
			
			IF(pDocumentId IS NOT NULL) THEN
				--RAISE EXCEPTION 'OFFSET: %', lOffsetDays;
				UPDATE pjs.documents SET public_duedate = now() + (lOffsetDays*INTERVAL '1 day') WHERE id = pDocumentId;
			END IF;
			--RAISE EXCEPTION 'OFFSET1: %', lOffsetDays;
		END IF;
		
		lRes.result = TRUE;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs.spUpdateDueDates(
	pOper int,
	pDocumentId bigint,
	pEventTypeId int,
	pRoundId bigint,
	pRoundUserId bigint
) TO iusrpmt;
