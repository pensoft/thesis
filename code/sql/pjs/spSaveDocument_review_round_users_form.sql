DROP TYPE IF EXISTS retgetdocumentreview CASCADE;
CREATE TYPE retgetdocumentreview AS(
	document_review_round_user_id bigint,
	decision_id integer,
	notes_to_author character varying,
	notes_to_editor character varying,
	disclose_name smallint,
	publish_review smallint,
	id bigint
);
ALTER TYPE retgetdocumentreview OWNER TO postgres;

CREATE OR REPLACE FUNCTION spSaveDocument_review_round_users_form(
	pOper integer,
	pGuid integer,
	pRoundId bigint,
	pDecision_id integer,
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
		lDocumentReviewRoundUsersFormId bigint;
		lJournalId int;
		
		cSERoleId CONSTANT int := 3;
		cERoleId CONSTANT int := 2;
		cReviewRoundTypeId CONSTANT int := 1;
		cReviewerPollElementType CONSTANT int := 1;
	BEGIN
		--~ pOper = 1 - create or update
		--~ pOper = 2 - select 
		--~ lResult := null;
	
		IF (pOper = 1) THEN
			
			SELECT INTO lUsrId, lRoundId, lJournalId usr.id, round_id, d.journal_id
			FROM pjs.document_review_round_users usr 
			JOIN pjs.document_users du ON du.id = usr.document_user_id
			JOIN pjs.documents d ON d.id = du.document_id
			WHERE usr.document_version_id = pGuid;

			IF(pRole = cSERoleId OR pRole = cERoleId) THEN 
				UPDATE pjs.document_review_rounds SET decision_notes = pNotes_to_author WHERE id = lRoundId;
			END IF;
		
			SELECT INTO lCheckVersion document_review_round_user_id FROM pjs.document_review_round_users_form WHERE document_review_round_user_id = lUsrId;
			
			IF (lCheckVersion IS NULL) THEN
					INSERT INTO pjs.document_review_round_users_form (document_review_round_user_id, decision_id,  notes_to_author, notes_to_editor, disclose_name, publish_review, round_id)
					VALUES (lUsrId, pDecision_id, pNotes_to_author, pNotes_to_editor, coalesce(pDisclose_name, 0), pPublish_review, pRoundId);
					
					lDocumentReviewRoundUsersFormId := currval('pjs.document_review_round_users_form_id_seq');
					
					INSERT INTO pjs.poll_answers(rel_element_id, poll_id, rel_element_type) SELECT lDocumentReviewRoundUsersFormId, id, cReviewerPollElementType FROM pjs.poll WHERE state = 1 AND journal_id = lJournalId;
				--~ lGuid := currval('document_review_round_users_form_pkey');
			ELSE
				UPDATE pjs.document_review_round_users_form SET
					decision_id = pDecision_id,
					notes_to_author = pNotes_to_author,
					notes_to_editor = pNotes_to_editor,
					disclose_name = coalesce(pDisclose_name, 0),
					publish_review = pPublish_review,
					round_id = pRoundId
				WHERE document_review_round_user_id = lUsrId;
				--~ lGuid := pGuid;
			END IF;
		ELSEIF (pOper = 2) THEN
			SELECT INTO 
				lResult.document_review_round_user_id, 
				lResult.decision_id, 
				lResult.notes_to_author, 
				lResult.notes_to_editor, 
				lResult.disclose_name, 
				lResult.publish_review,
				lResult.id
				
				f.document_review_round_user_id, 
				COALESCE(usr.decision_id, f.decision_id), 
				f.notes_to_author, 
				f.notes_to_editor, 
				f.disclose_name, 
				f.publish_review,
				f.id
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
	pNotes_to_author character varying,
	pNotes_to_editor character varying,
	pDisclose_name integer,
	pPublish_review integer,
	pRole integer
) TO postgres;