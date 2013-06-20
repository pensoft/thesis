DROP FUNCTION pjs.spGetVersionRoleFilteredMsgRootIds(
	pVersionId bigint
);

CREATE OR REPLACE FUNCTION pjs.spGetVersionRoleFilteredMsgRootIds(
	pVersionId bigint
)
  RETURNS SETOF pjs.msg AS
$BODY$
	DECLARE
		lRes pjs.msg;		
		lVersionUid int;
		lVersionRoleId int;
		lSERoleId int = 3;
		lDedicatedReviewerRoleId int = 5;
		lPublicReviewerRoleId int = 6;
		lCommunityReviewerRoleId int = 7;
		lAuthorRoleId int = 11;
		lMsgToSEFlagId int = 8;
		
		lRecord record;
		lRecord2 record;
		lCommentId bigint;
	BEGIN		
		SELECT INTO lVersionUid, lVersionRoleId
			du.uid, du.role_id
		FROM pjs.document_versions dv 
		JOIN pjs.document_review_round_users ru ON ru.document_version_id = dv.id
		JOIN pjs.document_users du ON du.id = ru.document_user_id
		WHERE dv.id = pVersionId;
		
		IF lVersionUid IS NULL THEN
			RETURN;
		END IF;
		
		IF lVersionRoleId = ANY (ARRAY[lDedicatedReviewerRoleId, lPublicReviewerRoleId, lCommunityReviewerRoleId]) THEN
			-- All the roots which have been created by an author, or 
			-- the roots in which the reviewer participates
			FOR lRes IN 
				SELECT DISTINCT m1.*				
				FROM pjs.msg m2
				JOIN pjs.msg m1 ON m1.id = m2.rootid
				LEFT JOIN pjs.document_users du ON du.document_id = m1.document_id AND du.uid = m1.usr_id AND du.role_id = lAuthorRoleId
				WHERE m2.version_id = pVersionId AND (m2.usr_id = lVersionUid OR du.id IS NOT NULL)
			LOOP
				RETURN NEXT lRes;
			END LOOP;
			
			
		ELSEIF lVersionRoleId = lAuthorRoleId THEN
			-- All Comments which are dont have the special flag to SE 
			FOR lRes IN 
				SELECT DISTINCT m1.*				
				FROM pjs.msg m1
				WHERE m1.version_id = pVersionId AND m1.id = m1.rootid AND (m1.flags & lMsgToSEFlagId) = 0
			LOOP
				RETURN NEXT lRes;
			END LOOP;
		ELSE 
			-- All root comments
			FOR lRes IN 
				SELECT DISTINCT m1.*				
				FROM pjs.msg m1
				WHERE m1.version_id = pVersionId AND m1.id = m1.rootid
			LOOP
				RETURN NEXT lRes;
			END LOOP;
		END IF;
		
		RETURN;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs.spGetVersionRoleFilteredMsgRootIds(
	pVersionId bigint
) TO iusrpmt;
