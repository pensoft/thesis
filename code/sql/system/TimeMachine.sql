CREATE OR REPLACE FUNCTION system."TimeMachine"(doc_id bigint, offset_in_days integer)
  RETURNS integer AS
$BODY$
DECLARE 
	current_round bigint;
	days interval = offset_in_days * INTERVAL '1 days';
	lCanProceedFlag boolean;
	lEnoughReviewers boolean;
	lCanTakeDecision boolean;
	lSEUsrId bigint;
	cNotEnoughReviewersEventType CONSTANT int := 39;
	cCanProceedEventType CONSTANT int := 38;
	cSERoleId int := 3;
	lCanInviteReviewers boolean;
	cNominatedReviewerType CONSTANT int := 5;
BEGIN

SELECT INTO current_round current_round_id FROM pjs.documents WHERE id = doc_id;

UPDATE pjs.documents 
SET public_duedate = public_duedate - days,
	panel_duedate = panel_duedate - days
WHERE id = doc_id;

UPDATE pjs.document_review_rounds r 
SET
	reviewers_assignment_duedate = reviewers_assignment_duedate - days,
	round_due_date = round_due_date - days
 WHERE r.id = current_round;
 
UPDATE pjs.document_review_round_users ru
SET	due_date = due_date - days
 WHERE ru.round_id = current_round;

SELECT INTO lCanProceedFlag can_proceed FROM pjs.document_review_rounds WHERE id = current_round;
		
-- check SE can take decision and enough reviewers assigned
SELECT INTO lCanTakeDecision result FROM pjs.spCheckIfSECanTakeADecision(doc_id);
--RAISE EXCEPTION 'SE Decision: %', lCanTakeDecision;
UPDATE pjs.document_review_rounds SET can_proceed = lCanTakeDecision WHERE id = current_round;
SELECT INTO lEnoughReviewers result FROM pjs.spCheckEnoughReviewrs(doc_id);
--RAISE EXCEPTION 'Enough Reviewers: %', lEnoughReviewers;
UPDATE pjs.document_review_rounds SET enough_reviewers = lEnoughReviewers WHERE id = current_round;

IF(lCanTakeDecision = TRUE AND lCanProceedFlag = FALSE) THEN
	SELECT INTO lSEUsrId dru.id 
	FROM pjs.document_users du
	JOIN pjs.document_review_round_users dru ON dru.document_user_id = du.id AND round_id = current_round
	WHERE du.role_id = cSERoleId AND du.document_id = doc_id;
	PERFORM pjs.spUpdateDueDates(1, doc_id, cCanProceedEventType, NULL, lSEUsrId);
END IF;

IF(lEnoughReviewers = FALSE) THEN
	PERFORM pjs.spUpdateDueDates(2, doc_id, cNotEnoughReviewersEventType, current_round, NULL);
END IF;

SELECT INTO lCanInviteReviewers result FROM pjs."spCheckCanInviteReviewer"(doc_id, current_round, cNominatedReviewerType);
UPDATE pjs.document_review_rounds SET review_lock = NOT lCanInviteReviewers WHERE document_id = doc_id AND id = current_round;

RETURN current_round;

END

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION system."TimeMachine"(bigint, integer)
  OWNER TO postgres;
