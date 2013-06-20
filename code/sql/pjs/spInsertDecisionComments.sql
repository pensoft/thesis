DROP TYPE IF EXISTS pjs.ret_spInsertDecisionComments CASCADE;
CREATE TYPE pjs.ret_spInsertDecisionComments AS (
	result int
);

CREATE OR REPLACE FUNCTION pjs.spInsertDecisionComments(
	pRoundUserId bigint
)
  RETURNS pjs.ret_spInsertDecisionComments AS
$BODY$
	DECLARE
		lRes pjs.ret_spInsertDecisionComments;
		lUserRole int;
		lSERoleId int = 3;
		lDedicatedReviewerRoleId int = 5;
		lPublicReviewerRoleId int = 6;
		lCommunityReviewerRoleId int = 7;
		lMsgToAuthorFlagId int = 16;
		lMsgToSEFlagId int = 8;
		lMsgToAuthorAndSEFlagId int = 32;
		
		lVersionId bigint;
		lUserId int;
		
		lNotesToAuthor varchar;
		lNotesToEditor varchar;
		lNotesToEditorAndAuthor varchar;
		lMsgId int;
	BEGIN	
		SELECT INTO lNotesToAuthor, lNotesToEditor
			notes_to_author, notes_to_editor
		FROM pjs.document_review_round_users_form
		WHERE document_review_round_user_id = pRoundUserId;
	
		SELECT INTO lUserRole, lVersionId, lUserId
			du.role_id, ru.document_version_id, du.uid
		FROM pjs.document_review_round_users ru
		JOIN pjs.document_users du ON du.id = ru.document_user_id
		WHERE ru.id = pRoundUserId;
		
		IF lUserRole = lSERoleId THEN
			IF coalesce(lNotesToAuthor, '') <> '' THEN
				SELECT INTO lMsgId
					comment_id
				FROM pjs.spNewComment(lVersionId, lNotesToAuthor, null, null, null, null, null, null, null, lUserId, '');
				
				UPDATE pjs.msg SET
					flags = flags | lMsgToAuthorFlagId
				WHERE id = lMsgId;
			END IF;
		ELSEIF lUserRole = lDedicatedReviewerRoleId OR lUserRole = lPublicReviewerRoleId OR lUserRole = lCommunityReviewerRoleId THEN			
			IF coalesce(lNotesToAuthor, '') <> '' THEN
				SELECT INTO lMsgId
					comment_id
				FROM pjs.spNewComment(lVersionId, lNotesToAuthor, null, null, null, null, null, null, null, lUserId, '');
				UPDATE pjs.msg SET
					flags = flags | lMsgToAuthorAndSEFlagId
				WHERE id = lMsgId;
			END IF;
			IF coalesce(lNotesToEditor, '') <> '' THEN
				SELECT INTO lMsgId
					comment_id
				FROM pjs.spNewComment(lVersionId, lNotesToEditor, null, null, null, null, null, null, null, lUserId, '');
				UPDATE pjs.msg SET
					flags = flags | lMsgToSEFlagId
				WHERE id = lMsgId;
			END IF;
		END IF;
				
		lRes.result = 1;		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs.spInsertDecisionComments(
	pRoundUserId bigint
) TO iusrpmt;
