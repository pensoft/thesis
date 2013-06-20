-- Function: spMoveGroupOrUserUpDown(integer, integer, integer)

-- DROP FUNCTION spMoveGroupOrUserUpDown(integer, integer);


CREATE OR REPLACE FUNCTION spMoveGroupOrUserUpDown(
	pguid integer,
	pOper integer,
	pjournalid integer,
	pdirection integer
)
  RETURNS integer AS
$BODY$
DECLARE
	lGroupId   integer;
	lJournalId integer;
	lPos       integer;
	lPosGrp    varchar;
	lIds	   integer[];
	lGId	   integer;

	lGroupId2   integer;
	lJournalId2 integer;
	lPos2       integer;
	lPosGrp2    varchar;
	lIds2	   integer[];
	
	lRes        integer;
BEGIN
	lRes:= 0;
	IF pOper = 1 THEN
		SELECT INTO lJournalId, lPosGrp, lGroupId journal_id, pos, id 
		FROM pjs.journal_user_groups 
		WHERE id = pguid AND journal_id = pjournalid;
		
		IF lGroupId IS NOT NULL THEN
			IF pdirection = 1 THEN -- Move Up
				SELECT INTO lJournalId2, lPosGrp2, lGroupId2 journal_id, pos, id 
				FROM pjs.journal_user_groups 
				WHERE pos < lPosGrp AND pos LIKE substring(lPosGrp, 0, char_length(lPosGrp)-1) || '%'
				AND char_length(pos) = char_length(lPosGrp) 
				AND journal_id = pjournalid 
				ORDER BY pos DESC
				LIMIT 1;
				-- WHERE pos > lPos AND journal_id = pjournalid ORDER BY pos ASC LIMIT 1;
				
				IF lGroupId2 IS NOT NULL THEN				
					SELECT INTO lIds array_agg(id) FROM pjs.journal_user_groups WHERE pos LIKE lPosGrp || '%' AND journal_id = pjournalid;
					SELECT INTO lIds2 array_agg(id) FROM pjs.journal_user_groups WHERE pos LIKE lPosGrp2 || '%' AND journal_id = pjournalid;
					
					UPDATE pjs.journal_user_groups SET pos = overlay(pos placing lPosGrp2 from 1 for char_length(lPosGrp2)) WHERE id = ANY (lIds);
					UPDATE pjs.journal_user_groups SET pos = overlay(pos placing lPosGrp from 1 for char_length(lPosGrp)) WHERE id = ANY (lIds2);
					lRes:= 1;
				END IF;
			ELSE -- Move Down
				SELECT INTO lJournalId2, lPosGrp2, lGroupId2 journal_id, pos, id 
				FROM pjs.journal_user_groups 
				WHERE pos > lPosGrp AND pos LIKE substring(lPosGrp, 0, char_length(lPosGrp)-1) || '%'
				AND char_length(pos) = char_length(lPosGrp) 
				AND journal_id = pjournalid
				ORDER BY pos ASC
				LIMIT 1;
				-- WHERE pos < lPos AND journal_id = pjournalid ORDER BY pos DESC LIMIT 1;
				
				IF lGroupId2 IS NOT NULL THEN
					
				SELECT INTO lIds array_agg(id) FROM pjs.journal_user_groups WHERE pos LIKE lPosGrp || '%' AND journal_id = pjournalid;
				SELECT INTO lIds2 array_agg(id) FROM pjs.journal_user_groups WHERE pos LIKE lPosGrp2 || '%' AND journal_id = pjournalid;
				
				UPDATE pjs.journal_user_groups SET pos = overlay(pos placing lPosGrp2 from 1 for char_length(lPosGrp2)) WHERE id = ANY (lIds);
				UPDATE pjs.journal_user_groups SET pos = overlay(pos placing lPosGrp from 1 for char_length(lPosGrp)) WHERE id = ANY (lIds2);
				
					lRes:= 1;
				END IF;
			END IF;
		END IF;
	ELSEIF pOper = 2 THEN
		SELECT INTO lPos, lGroupId, lGId pos, uid, journal_user_group_id 
		FROM pjs.journal_user_group_users
		WHERE uid = pguid;
		IF lGroupId IS NOT NULL THEN
			IF pdirection = 1 THEN -- Move Up
				SELECT INTO lPos2, lGroupId2 pos, uid
				FROM pjs.journal_user_group_users 
				WHERE pos > lPos AND journal_user_group_id = lGId ORDER BY pos ASC LIMIT 1;
				
				IF lGroupId2 IS NOT NULL THEN				
					UPDATE pjs.journal_user_group_users SET pos = lPos2 WHERE uid = lGroupId AND journal_user_group_id = lGId; 
					UPDATE pjs.journal_user_group_users SET pos = lPos WHERE uid = lGroupId2 AND journal_user_group_id = lGId; 
					lRes:= 1;
				END IF;
			ELSE -- Move Down
				SELECT INTO lPos2, lGroupId2 pos, uid 
				FROM pjs.journal_user_group_users 
				WHERE pos < lPos AND journal_user_group_id = lGId ORDER BY pos DESC LIMIT 1;
				
				IF lGroupId2 IS NOT NULL THEN
					
					UPDATE pjs.journal_user_group_users SET pos = lPos2 WHERE uid = lGroupId AND journal_user_group_id = lGId;
					UPDATE pjs.journal_user_group_users SET pos = lPos WHERE uid = lGroupId2 AND journal_user_group_id = lGId;
					lRes:= 1;
				END IF;
			END IF;
		END IF;
	END IF;
	RETURN lRes;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spMoveGroupOrUserUpDown(integer, integer, integer, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spMoveGroupOrUserUpDown(integer, integer, integer, integer) TO postgres;
GRANT EXECUTE ON FUNCTION spMoveGroupOrUserUpDown(integer, integer, integer, integer) TO iusrpmt;
GRANT EXECUTE ON FUNCTION spMoveGroupOrUserUpDown(integer, integer, integer, integer) TO pensoft;