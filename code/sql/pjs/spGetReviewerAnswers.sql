DROP TYPE IF EXISTS ret_revieweranswers CASCADE;

CREATE type ret_revieweranswers AS (
	question int,
	answer int,
	count int
);
-- Function: spGetReviewerAnswer(integer)

-- DROP FUNCTION pjs.spGetReviewerAnswer(integer);

CREATE OR REPLACE FUNCTION pjs.spGetReviewerAnswers(
	pDocumentId integer,
	pRoundId bigint
)
  --~ RETURNS ret_revieweranswers AS
  RETURNS SETOF ret_revieweranswers AS
$BODY$
DECLARE
	lRecord integer;
	r ret_revieweranswers%rowtype;
BEGIN

	FOR r IN SELECT 1 as question, drr.question1, COUNT(drr.question1) as count FROM pjs.document_users du
		JOIN pjs.document_review_round_users dr ON (du.id = dr.document_user_id)
		LEFT JOIN pjs.document_review_round_users_form drr ON (drr.document_review_round_user_id = dr.id) AND drr.round_id = pRoundId
		WHERE role_id = 5 AND document_id = pDocumentId AND drr.question1 > 0
		GROUP BY question1
    LOOP
        RETURN NEXT r;
    END LOOP;
	
	FOR r IN SELECT 2 as question, drr.question2, COUNT(drr.question2) as count FROM pjs.document_users du
			JOIN pjs.document_review_round_users dr ON (du.id = dr.document_user_id)
			LEFT JOIN pjs.document_review_round_users_form drr ON (drr.document_review_round_user_id = dr.id) AND drr.round_id = pRoundId
			WHERE role_id = 5 AND document_id = pDocumentId AND drr.question2 > 0
			GROUP BY question2
    LOOP
        RETURN NEXT r;
    END LOOP;
	
	FOR r IN SELECT 3 as question, drr.question3, COUNT(drr.question3) as count FROM pjs.document_users du
		JOIN pjs.document_review_round_users dr ON (du.id = dr.document_user_id)
		LEFT JOIN pjs.document_review_round_users_form drr ON (drr.document_review_round_user_id = dr.id) AND drr.round_id = pRoundId
		WHERE role_id = 5 AND document_id = pDocumentId AND drr.question3 > 0
		GROUP BY question3
    LOOP
        RETURN NEXT r;
    END LOOP;
	
	FOR r IN SELECT 4 as question, drr.question4, COUNT(drr.question4) as count FROM pjs.document_users du
			JOIN pjs.document_review_round_users dr ON (du.id = dr.document_user_id)
			LEFT JOIN pjs.document_review_round_users_form drr ON (drr.document_review_round_user_id = dr.id) AND drr.round_id = pRoundId
			WHERE role_id = 5 AND document_id = pDocumentId AND drr.question4 > 0
			GROUP BY question4
    LOOP
        RETURN NEXT r;
    END LOOP;
	
	FOR r IN SELECT 5 as question, drr.question5, COUNT(drr.question5) as count FROM pjs.document_users du
		JOIN pjs.document_review_round_users dr ON (du.id = dr.document_user_id)
		LEFT JOIN pjs.document_review_round_users_form drr ON (drr.document_review_round_user_id = dr.id) AND drr.round_id = pRoundId
		WHERE role_id = 5 AND document_id = pDocumentId AND drr.question5 > 0
		GROUP BY question5
    LOOP
        RETURN NEXT r;
    END LOOP;
	
	FOR r IN SELECT 6 as question, drr.question6, COUNT(drr.question6) as count FROM pjs.document_users du
			JOIN pjs.document_review_round_users dr ON (du.id = dr.document_user_id)
			LEFT JOIN pjs.document_review_round_users_form drr ON (drr.document_review_round_user_id = dr.id) AND drr.round_id = pRoundId
			WHERE role_id = 5 AND document_id = pDocumentId AND drr.question6 > 0
			GROUP BY question6
    LOOP
        RETURN NEXT r;
    END LOOP;
	
	FOR r IN SELECT 7 as question, drr.question7, COUNT(drr.question7) as count FROM pjs.document_users du
			JOIN pjs.document_review_round_users dr ON (du.id = dr.document_user_id)
			LEFT JOIN pjs.document_review_round_users_form drr ON (drr.document_review_round_user_id = dr.id) AND drr.round_id = pRoundId
			WHERE role_id = 5 AND document_id = pDocumentId AND drr.question7 > 0
			GROUP BY question7
    LOOP
        RETURN NEXT r;
    END LOOP;
	
	FOR r IN SELECT 8 as question, drr.question8, COUNT(drr.question8) as count FROM pjs.document_users du
			JOIN pjs.document_review_round_users dr ON (du.id = dr.document_user_id)
			LEFT JOIN pjs.document_review_round_users_form drr ON (drr.document_review_round_user_id = dr.id) AND drr.round_id = pRoundId
			WHERE role_id = 5 AND document_id = pDocumentId AND drr.question8 > 0
			GROUP BY question8
    LOOP
        RETURN NEXT r;
    END LOOP;
	
	FOR r IN SELECT 9 as question, drr.question9, COUNT(drr.question9) as count FROM pjs.document_users du
			JOIN pjs.document_review_round_users dr ON (du.id = dr.document_user_id)
			LEFT JOIN pjs.document_review_round_users_form drr ON (drr.document_review_round_user_id = dr.id) AND drr.round_id = pRoundId
			WHERE role_id = 5 AND document_id = pDocumentId AND drr.question9 > 0
			GROUP BY question9
    LOOP
        RETURN NEXT r;
    END LOOP;
	
	FOR r IN SELECT 10 as question, drr.question10, COUNT(drr.question10) as count FROM pjs.document_users du
			JOIN pjs.document_review_round_users dr ON (du.id = dr.document_user_id)
			LEFT JOIN pjs.document_review_round_users_form drr ON (drr.document_review_round_user_id = dr.id) AND drr.round_id = pRoundId
			WHERE role_id = 5 AND document_id = pDocumentId AND drr.question10 > 0
			GROUP BY question10
    LOOP
        RETURN NEXT r;
    END LOOP;
	
	FOR r IN SELECT 11 as question, drr.question11, COUNT(drr.question11) as count FROM pjs.document_users du
			JOIN pjs.document_review_round_users dr ON (du.id = dr.document_user_id)
			LEFT JOIN pjs.document_review_round_users_form drr ON (drr.document_review_round_user_id = dr.id) AND drr.round_id = pRoundId
			WHERE role_id = 5 AND document_id = pDocumentId AND drr.question11 > 0
			GROUP BY question11
    LOOP
        RETURN NEXT r;
    END LOOP;
	
	FOR r IN SELECT 12 as question, drr.question12, COUNT(drr.question12) as count FROM pjs.document_users du
			JOIN pjs.document_review_round_users dr ON (du.id = dr.document_user_id)
			LEFT JOIN pjs.document_review_round_users_form drr ON (drr.document_review_round_user_id = dr.id) AND drr.round_id = pRoundId
			WHERE role_id = 5 AND document_id = pDocumentId AND drr.question12 > 0
			GROUP BY question12
    LOOP
        RETURN NEXT r;
    END LOOP;
	
	FOR r IN SELECT 13 as question, drr.question13, COUNT(drr.question13) as count FROM pjs.document_users du
			JOIN pjs.document_review_round_users dr ON (du.id = dr.document_user_id)
			LEFT JOIN pjs.document_review_round_users_form drr ON (drr.document_review_round_user_id = dr.id) AND drr.round_id = pRoundId
			WHERE role_id = 5 AND document_id = pDocumentId AND drr.question13 > 0
			GROUP BY question13
    LOOP
        RETURN NEXT r;
    END LOOP;

	FOR r IN SELECT 14 as question, drr.question14, COUNT(drr.question14) as count FROM pjs.document_users du
			JOIN pjs.document_review_round_users dr ON (du.id = dr.document_user_id)
			LEFT JOIN pjs.document_review_round_users_form drr ON (drr.document_review_round_user_id = dr.id) AND drr.round_id = pRoundId
			WHERE role_id = 5 AND document_id = pDocumentId AND drr.question14 > 0
			GROUP BY question14
    LOOP
        RETURN NEXT r;
    END LOOP;

    RETURN;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION pjs.spGetReviewerAnswers(integer, bigint) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pjs.spGetReviewerAnswers(integer, bigint) TO postgres;
GRANT EXECUTE ON FUNCTION pjs.spGetReviewerAnswers(integer, bigint) TO iusrpmt;
GRANT EXECUTE ON FUNCTION pjs.spGetReviewerAnswers(integer, bigint) TO pensoft;


