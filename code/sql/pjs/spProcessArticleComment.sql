DROP TYPE IF EXISTS ret_spProcessArticleComment CASCADE;
CREATE TYPE ret_spProcessArticleComment AS (
	message text,
	id int,
	event_id bigint
);

DROP FUNCTION IF EXISTS pjs."spProcessArticleComment"(
	pOper int,
	pId int,
	pUserId int,
	pArticleId bigint,
	pJournalId int,
	pMessage text
);

CREATE OR REPLACE FUNCTION pjs."spProcessArticleComment"(
	pOper int,
	pId int,
	pUserId int,
	pArticleId bigint,
	pJournalId int,
	pMessage text
)
  RETURNS ret_spProcessArticleComment AS
$BODY$
	DECLARE
		lRes ret_spProcessArticleComment;
		cNewCommentEventType int := 105;
		cAOFCommentPollElementType CONSTANT int:= 2;
	BEGIN		
		
	
		IF(pOper = 1) THEN -- new
			
			SELECT INTO lRes.id, lRes.message id, message FROM pjs.article_forum WHERE article_id = pArticleId AND createuid = pUserId AND state = 0;
			
			IF(lRes.id IS NULL) THEN
				INSERT INTO pjs.article_forum(article_id, message, createuid) VALUES(pArticleId, pMessage, pUserId);
				lRes.id := currval('pjs.article_forum_id_seq');
				INSERT INTO pjs.poll_answers(rel_element_id, poll_id, rel_element_type) SELECT lRes.id, id, cAOFCommentPollElementType FROM pjs.poll WHERE state = 1 AND journal_id = pJournalId;
			END IF;
			--SELECT INTO lRes.event_id event_id FROM spCreateEvent(cNewCommentEventType, pArticleId, pUserId, pJournalId, NULL, 1);
		ELSEIF(pOper = 2) THEN -- approve
			UPDATE pjs.article_forum 
				SET 
					state = 1, 
					approveuid = pUserId,
					approve_date = CURRENT_TIMESTAMP
			WHERE id = pId;
		ELSEIF(pOper = 3) THEN -- reject
			UPDATE pjs.article_forum 
					SET 
						state = 2, 
						approveuid = pUserId,
						approve_date = CURRENT_TIMESTAMP
				WHERE id = pId;
		ELSEIF(pOper = 4) THEN -- comment
			UPDATE pjs.article_forum 
				SET 
					state = 1, 
					approveuid = pUserId,
					approve_date = CURRENT_TIMESTAMP,
					message = pMessage
			WHERE id = pId;
			SELECT INTO lRes.event_id event_id FROM spCreateEvent(cNewCommentEventType, pArticleId, pUserId, pJournalId, NULL, 1);
			--RAISE EXCEPTION 'spCreateEvent(%, %, %, %, NULL, 1)', cNewCommentEventType, pArticleId, pUserId, pJournalId;
		ELSEIF(pOper = 5) THEN -- save
			UPDATE pjs.article_forum 
				SET 
					message = pMessage
			WHERE id = pId;
		ELSE -- (6) - delete
			-- delete from pjs.poll_answers 
			DELETE FROM pjs.poll_answers  WHERE rel_element_type = cAOFCommentPollElementType AND rel_element_id = pId;
			DELETE FROM pjs.article_forum WHERE id = pId;
		END IF;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs."spProcessArticleComment"(
	pOper int,
	pId int,
	pUserId int,
	pArticleId bigint,
	pJournalId int,
	pMessage text
) TO iusrpmt;
