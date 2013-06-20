DROP TYPE IF EXISTS ret_spManualUpdateDueDates CASCADE;
CREATE TYPE ret_spManualUpdateDueDates AS (result boolean);

CREATE OR REPLACE FUNCTION pjs.spManualUpdateDueDates(
	pOper int,
	pDocumentId bigint,
	pRoundId bigint,
	pRoundUserId bigint,
	pDuedate timestamp
)
	RETURNS ret_spManualUpdateDueDates AS
$BODY$
	DECLARE
		lRes ret_spManualUpdateDueDates;
		-- lOffsetDays int;
		lRoundDueDate timestamp;
	BEGIN		
		IF pDuedate < now() THEN
			RAISE EXCEPTION 'Invalid date';
		END IF;
		IF pOper = 1 THEN 
			-- update round_due_date if there is pRoundId param
			IF(pRoundId IS NOT NULL) THEN
				UPDATE pjs.document_review_rounds SET round_due_date = pDueDate WHERE id = pRoundId;
			END IF;
			
			-- update user decision due_date
			IF(pRoundUserId IS NOT NULL) THEN
			
				/*SELECT INTO lRoundDueDate dr.round_due_date 
				FROM pjs.document_review_rounds dr
				JOIN pjs.document_review_round_users dru ON dru.round_id = dr.id
				WHERE dru.id = pRoundUserId;*/
				
				/*IF(lRoundDueDate::date < (pDueDate)::date) THEN
					UPDATE pjs.document_review_round_users SET due_date = lRoundDueDate WHERE id = pRoundUserId;
				ELSE*/
					UPDATE pjs.document_review_round_users SET due_date = pDueDate WHERE id = pRoundUserId;
				--END IF;
				
			END IF;
		ELSEIF pOper = 2 THEN 
			IF(pRoundId IS NOT NULL) THEN
				
				--SELECT INTO lRoundDueDate round_due_date FROM pjs.document_review_rounds WHERE id = pRoundId;
				/*IF(lRoundDueDate::date < (pDueDate)::date) THEN
					UPDATE pjs.document_review_rounds SET reviewers_assignment_duedate = lRoundDueDate WHERE id = pRoundId;
				ELSE*/
					UPDATE pjs.document_review_rounds SET reviewers_assignment_duedate = pDueDate WHERE id = pRoundId;
				--END IF;
				
			END IF;
		ELSEIF pOper = 3 THEN 
			
			IF(pRoundId IS NOT NULL AND pRoundUserId IS NOT NULL) THEN
				--RAISE EXCEPTION 'OFFSET: %', lOffsetDays;
				UPDATE pjs.document_user_invitations SET due_date = pDueDate WHERE uid = pRoundUserId AND round_id = pRoundId;
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
	pDueDate timestamp
) TO iusrpmt;
