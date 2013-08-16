UPDATE pjs.event_reminders 
SET 
condition_sql = 'SELECT d.id as document_id, dui.uid as uid, dui.uid as uid_event_to, dui.role_id as uid_event_to_role_id, dui.id as invitation_id
FROM pjs.documents d 
JOIN pjs.document_review_rounds dru ON dru.id = d.current_round_id AND dru.round_number = 1
JOIN pjs.document_user_invitations dui ON dui.document_id = d.id AND dui.round_id = dru.id AND dui.role_id = 5 AND dui.state_id = 1
WHERE d.state_id = 3
	AND d.reminders_test_flag = 1
	AND (dui.due_date + {offset}*INTERVAL ''1 day'')::date = now()::date
	AND d.journal_id = {journal_id}'
WHERE id = 44;