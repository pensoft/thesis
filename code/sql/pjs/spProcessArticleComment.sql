DROP TYPE IF EXISTS ret_spProcessArticleComment CASCADE;
CREATE TYPE ret_spProcessArticleComment AS (
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
	BEGIN		
		IF(pOper = 1) THEN
			INSERT INTO pjs.article_forum(article_id, message, createuid) VALUES(pArticleId, pMessage, pUserId);
			SELECT INTO lRes.event_id event_id FROM spCreateEvent(cNewCommentEventType, pArticleId, pUserId, pJournalId, NULL, 1);
		ELSEIF(pOper = 2) THEN
			UPDATE pjs.article_forum 
				SET 
					state = 1, 
					approveuid = pUserId,
					approve_date = CURRENT_TIMESTAMP
			WHERE id = pId;
		ELSE
			UPDATE pjs.article_forum 
					SET 
						state = 2, 
						approveuid = pUserId,
						approve_date = CURRENT_TIMESTAMP
				WHERE id = pId;
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
