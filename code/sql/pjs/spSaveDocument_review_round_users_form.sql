DROP TYPE IF EXISTS retgetdocumentreview CASCADE;
CREATE TYPE retgetdocumentreview AS(
	document_review_round_user_id bigint,
	decision_id integer,
	question1 smallint,
	question2 smallint,
	question3 smallint,
	question4 smallint,
	question5 smallint,
	question6 smallint,
	question7 smallint,
	question8 smallint,
	question9 smallint,
	question10 smallint,
	question11 smallint,
	question12 smallint,
	question13 smallint,
	question14 smallint,
	notes_to_author character varying,
	notes_to_editor character varying,
	disclose_name smallint,
	publish_review smallint
);
ALTER TYPE retgetdocumentreview OWNER TO postgres;

CREATE OR REPLACE FUNCTION spSaveDocument_review_round_users_form(
	pOper integer,
	pGuid integer,
	pRoundId bigint,
	pDecision_id integer,
	pQuestion1 integer,
	pQuestion2 integer,
	pQuestion3 integer,
	pQuestion4 integer,
	pQuestion5 integer,
	pQuestion6 integer,
	pQuestion7 integer,
	pQuestion8 integer,
	pQuestion9 integer,
	pQuestion10 integer,
	pQuestion11 integer,
	pQuestion12 integer,
	pQuestion13 integer,
	pQuestion14 integer,
	pNotes_to_author character varying,
	pNotes_to_editor character varying,
	pDisclose_name integer,
	pPublish_review integer,
	pRole integer
)
RETURNS retgetdocumentreview AS
$BODY$
	DECLARE
		lResult retgetdocumentreview;
		lGuid int;
		lUsrId int;
		lCheckVersion int;
		lRoundId bigint;
		
		cSERoleId CONSTANT int := 3;
		cERoleId CONSTANT int := 2;
		cReviewRoundTypeId CONSTANT int := 1;
	BEGIN
		--~ pOper = 1 - create or update
		--~ pOper = 2 - select 
		--~ lResult := null;
	
		IF (pOper = 1) THEN
			
			SELECT INTO lUsrId, lRoundId usr.id, round_id FROM pjs.document_review_round_users usr WHERE usr.document_version_id = pGuid;

			IF(pRole = cSERoleId OR pRole = cERoleId) THEN 
				UPDATE pjs.document_review_rounds SET decision_notes = pNotes_to_author WHERE id = lRoundId;
			END IF;
		
			SELECT INTO lCheckVersion document_review_round_user_id FROM pjs.document_review_round_users_form WHERE document_review_round_user_id = lUsrId;
			IF (lCheckVersion IS NULL) THEN
					INSERT INTO pjs.document_review_round_users_form
					(document_review_round_user_id, decision_id, question1, question2, question3, question4, question5, question6, question7, question8, question9,
					 question10, question11, question12, question13, question14, notes_to_author, 
					 notes_to_editor, disclose_name, publish_review, round_id)
					VALUES (lUsrId, pDecision_id, pQuestion1, pQuestion2, pQuestion3, pQuestion4, pQuestion5, pQuestion6, pQuestion7, pQuestion8, 
					pQuestion9, pQuestion10, pQuestion11, pQuestion12, pQuestion13, pQuestion14, pNotes_to_author, pNotes_to_editor, coalesce(pDisclose_name, 0), pPublish_review, pRoundId);
				--~ lGuid := currval('document_review_round_users_form_pkey');
			ELSE
				UPDATE pjs.document_review_round_users_form SET
					decision_id = pDecision_id,
					question1 = pQuestion1,
					question2 = pQuestion2,
					question3 = pQuestion3,
					question4 = pQuestion4,
					question5 = pQuestion5,
					question6 = pQuestion6,
					question7 = pQuestion7,
					question8 = pQuestion8,
					question9 = pQuestion9,
					question10 = pQuestion10,
					question11 = pQuestion11,
					question12 = pQuestion12,
					question13 = pQuestion13,
					question14 = pQuestion14,
					notes_to_author = pNotes_to_author,
					notes_to_editor = pNotes_to_editor,
					disclose_name = coalesce(pDisclose_name, 0),
					publish_review = pPublish_review,
					round_id = pRoundId
				WHERE document_review_round_user_id = lUsrId;
				--~ lGuid := pGuid;
			END IF;
		ELSEIF (pOper = 2) THEN
			SELECT INTO lResult.document_review_round_user_id, lResult.decision_id, lResult.question1, lResult.question2, lResult.question3, lResult.question4, lResult.question5,
				lResult.question6, lResult.question7, lResult.question8, lResult.question9, lResult.question10, lResult.question11,
				lResult.question12, lResult.question13, lResult.question14,
				lResult.notes_to_author, lResult.notes_to_editor, lResult.disclose_name, lResult.publish_review
				f.document_review_round_user_id, COALESCE(usr.decision_id, f.decision_id), f.question1, f.question2, f.question3, f.question4, f.question5, f.question6, f.question7,
				f.question8, f.question9, f.question10, f.question11, f.question12, f.question13, f.question14, f.notes_to_author, f.notes_to_editor, f.disclose_name, f.publish_review
				FROM pjs.document_review_round_users usr
				left JOIN pjs.document_review_round_users_form f ON usr.id = f.document_review_round_user_id
				WHERE usr.document_version_id = pGuid;	
			IF lResult.document_review_round_user_id IS NOT NULL THEN
				lResult.disclose_name = coalesce(lResult.disclose_name, 0);
			END IF;

		END IF;
		RETURN lResult;
END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

ALTER FUNCTION spSaveDocument_review_round_users_form(
	pOper integer,
	pGuid integer,
	pRoundId bigint,
	pDecision_id integer,
	pQuestion1 integer,
	pQuestion2 integer,
	pQuestion3 integer,
	pQuestion4 integer,
	pQuestion5 integer,
	pQuestion6 integer,
	pQuestion7 integer,
	pQuestion8 integer,
	pQuestion9 integer,
	pQuestion10 integer,
	pQuestion11 integer,
	pQuestion12 integer,
	pQuestion13 integer,
	pQuestion14 integer,
	pNotes_to_author character varying,
	pNotes_to_editor character varying,
	pDisclose_name integer,
	pPublish_review integer,
	pRole int
) OWNER TO postgres;

GRANT EXECUTE ON FUNCTION spSaveDocument_review_round_users_form(
	pOper integer,
	pGuid integer,
	pRoundId bigint,
	pDecision_id integer,
	pQuestion1 integer,
	pQuestion2 integer,
	pQuestion3 integer,
	pQuestion4 integer,
	pQuestion5 integer,
	pQuestion6 integer,
	pQuestion7 integer,
	pQuestion8 integer,
	pQuestion9 integer,
	pQuestion10 integer,
	pQuestion11 integer,
	pQuestion12 integer,
	pQuestion13 integer,
	pQuestion14 integer,
	pNotes_to_author character varying,
	pNotes_to_editor character varying,
	pDisclose_name integer,
	pPublish_review integer,
	pRole integer
) TO public;
GRANT EXECUTE ON FUNCTION spSaveDocument_review_round_users_form(
	pOper integer,
	pGuid integer,
	pRoundId bigint,
	pDecision_id integer,
	pQuestion1 integer,
	pQuestion2 integer,
	pQuestion3 integer,
	pQuestion4 integer,
	pQuestion5 integer,
	pQuestion6 integer,
	pQuestion7 integer,
	pQuestion8 integer,
	pQuestion9 integer,
	pQuestion10 integer,
	pQuestion11 integer,
	pQuestion12 integer,
	pQuestion13 integer,
	pQuestion14 integer,
	pNotes_to_author character varying,
	pNotes_to_editor character varying,
	pDisclose_name integer,
	pPublish_review integer,
	pRole integer
) TO pensoft;
GRANT EXECUTE ON FUNCTION spSaveDocument_review_round_users_form(
	pOper integer,
	pGuid integer,
	pRoundId bigint,
	pDecision_id integer,
	pQuestion1 integer,
	pQuestion2 integer,
	pQuestion3 integer,
	pQuestion4 integer,
	pQuestion5 integer,
	pQuestion6 integer,
	pQuestion7 integer,
	pQuestion8 integer,
	pQuestion9 integer,
	pQuestion10 integer,
	pQuestion11 integer,
	pQuestion12 integer,
	pQuestion13 integer,
	pQuestion14 integer,
	pNotes_to_author character varying,
	pNotes_to_editor character varying,
	pDisclose_name integer,
	pPublish_review integer,
	pRole integer
) TO iusrpmt;
GRANT EXECUTE ON FUNCTION spSaveDocument_review_round_users_form(
	pOper integer,
	pGuid integer,
	pRoundId bigint,
	pDecision_id integer,
	pQuestion1 integer,
	pQuestion2 integer,
	pQuestion3 integer,
	pQuestion4 integer,
	pQuestion5 integer,
	pQuestion6 integer,
	pQuestion7 integer,
	pQuestion8 integer,
	pQuestion9 integer,
	pQuestion10 integer,
	pQuestion11 integer,
	pQuestion12 integer,
	pQuestion13 integer,
	pQuestion14 integer,
	pNotes_to_author character varying,
	pNotes_to_editor character varying,
	pDisclose_name integer,
	pPublish_review integer,
	pRole integer
) TO postgres;