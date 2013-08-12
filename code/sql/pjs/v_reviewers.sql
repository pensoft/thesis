-- View: pjs.v_reviewers

-- DROP VIEW pjs.v_reviewers;

CREATE OR REPLACE VIEW pjs.v_reviewers AS 
                  SELECT rru.round_id, du.uid, rru.due_date::date AS due_date, 
            du.role_id, rru.decision_id, 
                CASE
                    WHEN (du.role_id = ANY (ARRAY[5, 7])) AND rru.decision_id IS NULL THEN 'pjs.dashboards.actions.submitReview'::text
                    WHEN (du.role_id = ANY (ARRAY[5, 7])) AND rru.decision_id IS NOT NULL THEN 'pjs.dashboards.actions.reviewSubmitted'::text
                    WHEN du.role_id = 3 AND NOT r.enough_reviewers THEN 'pjs.dashboards.actions.inviteReviewers'::text
                    WHEN du.role_id = 3 AND r.can_proceed THEN 'pjs.dashboards.actions.takeDecision'::text
                    ELSE NULL::text
                END AS action
           FROM pjs.document_review_round_users rru
      JOIN pjs.document_users du ON du.id = rru.document_user_id
   JOIN pjs.document_review_rounds r ON r.id = rru.round_id
  WHERE du.role_id = ANY (ARRAY[3, 5, 7, 11])
UNION ALL 
         SELECT document_user_invitations.round_id, 
            document_user_invitations.uid, 
            document_user_invitations.due_date::date AS due_date, 
            document_user_invitations.role_id, NULL::integer AS decision_id, 
                CASE
                    WHEN document_user_invitations.role_id = 5 THEN 'pjs.dashboards.actions.respond2request'::text
                    WHEN document_user_invitations.role_id = 7 THEN 'pjs.dashboards.actions.submitReview'::text
                    ELSE NULL::text
                END AS action
           FROM pjs.document_user_invitations
          WHERE document_user_invitations.state_id = 1;

GRANT ALL ON TABLE pjs.v_reviewers TO iusrpmt;

