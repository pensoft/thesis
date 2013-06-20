DROP VIEW IF EXISTS pjs.v_reviewers;
CREATE OR REPLACE VIEW pjs.v_reviewers AS 
SELECT 	rru.round_id, 
		du.uid, 
		rru.due_date::date, 
		role_id, 
		rru.decision_id,
	  (case when role_id = 5 and rru.decision_id is null      then 'pjs.dashboards.actions.submitReview'
			when role_id = 5 and rru.decision_id is not null  then 'pjs.dashboards.actions.reviewSubmitted' 
			when role_id = 3 and not r.enough_reviewers       then 'pjs.dashboards.actions.inviteReviewers'
			when role_id = 3 and r.can_proceed				  then 'pjs.dashboards.actions.takeDecision'
			end)::text as action
	FROM pjs.document_review_round_users rru 
			JOIN pjs.document_users du ON du.id = rru.document_user_id
			JOIN pjs.document_review_rounds r ON r.id = rru.round_id
				
	WHERE du.role_id IN (3, 5) --SE, DE

UNION ALL

SELECT 	round_id, uid, 
		due_date::date, 
		role_id as role_id, 
		NULL as decision_id,
		(case when role_id = 5 then 'pjs.dashboards.actions.respond2request'
			  when role_id = 7 then 'pjs.dashboards.actions.submitReview' end)::text as action
FROM pjs.document_user_invitations
WHERE state_id = 1

--order by act
;
GRANT ALL ON TABLE pjs.v_reviewers TO iusrpmt;