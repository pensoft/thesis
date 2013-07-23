CREATE OR REPLACE FUNCTION system."TimeMachine"(doc_id bigint, offset_in_days integer)
  RETURNS integer AS
$BODY$
DECLARE 
	current_round bigint;
	days interval = offset_in_days * INTERVAL '1 days';
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

RETURN current_round;

END

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION system."TimeMachine"(bigint, integer)
  OWNER TO postgres;
