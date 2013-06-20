DROP FUNCTION spsavejournalgroup(pOper int, pID int, pJournalId int, pName varchar, pDescription varchar, pParent int);
DROP TYPE ret_spsavejournalgroup;

CREATE TYPE ret_spsavejournalgroup AS (
	id int,
	name varchar,
	parentnode int,
	cval int,
	error int
);
								
CREATE OR REPLACE FUNCTION spsavejournalgroup(pOper int, pID int, pJournalId int, pName varchar, pDescription varchar, pParent int)
  RETURNS ret_spsavejournalgroup AS
$$
DECLARE
	lRes ret_spsavejournalgroup;
	lParentPos varchar;
	lMaxPos varchar;
	lParentRoot int;
	lID int;
	lTmpRoot int;
	lTmpPos varchar;
	lOldPos varchar;
BEGIN
	lRes.id := pID;
	
	IF pOper = 0 THEN -- GET
		SELECT INTO lRes.name, lParentRoot, lParentPos
			name, rootnode, substring(pos from 1 for (char_length(pos) - 2)) 
		FROM pjs.journal_user_groups WHERE id = pID;
		lRes.id = pID;
		
		SELECT INTO lRes.parentnode id FROM pjs.journal_user_groups WHERE rootnode = lParentRoot AND pos = lParentPos;
	
	ELSEIF pOper = 1 THEN --INSERT UPDATE
		IF pParent IS NULL THEN
			SELECT INTO lMaxPos pos FROM pjs.journal_user_groups WHERE char_length(pos) = 2 ORDER BY pos DESC LIMIT 1;
		ELSE
			SELECT INTO lParentRoot, lParentPos rootnode, pos FROM pjs.journal_user_groups WHERE id = pParent;
			SELECT INTO lOldPos pos FROM pjs.journal_user_groups WHERE id = pID;
			
			IF pParent = pID OR position(lOldPos in lParentPos) = 1 THEN
				RAISE EXCEPTION 'We can not move themselves into groups or renouncing its children!';
			END IF;
					
			SELECT INTO lMaxPos pos FROM pjs.journal_user_groups WHERE pos LIKE lParentPos || '%' ORDER BY pos DESC LIMIT 1;
		END IF;
		
		lMaxPos := ForumGetNextOrd(lMaxPos);
		IF pParent IS NOT NULL THEN
			lMaxPos := lParentPos || lMaxPos;
		END IF;
		
		lMaxPos := coalesce(lMaxPos, 'AA');
		IF NOT EXISTS (SELECT * FROM pjs.journal_user_groups WHERE id = pID) THEN -- INSERT
			
			if (pID is not NULL) then
				INSERT INTO pjs.journal_user_groups (id, journal_id, name, description, pos, rootnode)
				VALUES (pID, pJournalId, pName, pDescription, lMaxPos, 0);
				lID := pID;
			else
				INSERT INTO pjs.journal_user_groups (journal_id, name, description, pos, rootnode)
					VALUES (pJournalId, pName, pDescription, lMaxPos, 0);
				lID := currval('pjs.journal_user_groups_id_seq');
			end if;
			
			UPDATE pjs.journal_user_groups SET 
				rootnode = (case WHEN pParent IS NULL THEN lID ELSE pParent end) 
			WHERE id = lID;

		ELSE --UPDATE
			
			SELECT INTO lTmpPos pos FROM pjs.journal_user_groups WHERE id = pID;
			
			IF lTmpPos <> lMaxPos THEN 
				
				IF EXISTS (SELECT *
					FROM pjs.journal_user_groups r1
					JOIN pjs.journal_user_groups r2 ON r2.pos LIKE r1.pos || '%' AND r1.id <> r2.id
					WHERE r1.id = pID
				) THEN
					UPDATE pjs.journal_user_groups SET
						pos = overlay(pos placing lMaxPos from 1 for char_length(lTmpPos)),
						rootnode = coalesce(lParentRoot, pID)
					WHERE id = pID AND 
						pos LIKE lTmpPos || '%' AND char_length(pos) > char_length(lTmpPos);
				END IF;				
				
				UPDATE pjs.journal_user_groups SET 
					name = pName,
					description = pdescription,
					pos = lMaxPos,
					rootnode = coalesce(lParentRoot, id)
				WHERE id = pID;
				
			ELSE 
				UPDATE pjs.journal_user_groups
					SET name = pTitle, description = pdescription, rootnode = lID
				WHERE id = pID AND journal_id = pJournalId;
				lID := pGuid;
			END IF;
			
		END IF;
	ELSEIF pOper = 3 THEN -- DELETE
		IF EXISTS (SELECT *
			FROM pjs.journal_user_groups r1
			JOIN pjs.journal_user_groups r2 ON r2.pos LIKE r1.pos || '%' AND r1.id <> r2.id
			WHERE r1.id = pID
		) THEN
			--~ lRes.error = 1;
			--~ return lRes.error;
			RAISE EXCEPTION 'You can not delete group that has subgroups!';
		ELSE
			DELETE FROM pjs.journal_user_group_users WHERE journal_user_group_id = pID;
			DELETE FROM pjs.journal_user_groups WHERE id = pID AND journal_id = pJournalId;
		END IF;
		
	END IF;
	
	SELECT INTO lRes.id id FROM pjs.journal_user_groups WHERE id = pID ORDER BY id DESC LIMIT 1; 
	SELECT INTO lRes.parentnode rootnode FROM pjs.journal_user_groups WHERE id = lID ORDER BY id DESC LIMIT 1; 
	
	RETURN lRes;
END;
$$
  LANGUAGE 'plpgsql' SECURITY DEFINER;

ALTER FUNCTION spsavejournalgroup(pOper int, pID int, pJournalId int, pName varchar, pDescription varchar, pParent int) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spsavejournalgroup(pOper int, pID int, pJournalId int, pName varchar, pDescription varchar, pParent int) TO public;
GRANT EXECUTE ON FUNCTION spsavejournalgroup(pOper int, pID int, pJournalId int, pName varchar, pDescription varchar, pParent int) TO postgres;
GRANT EXECUTE ON FUNCTION spsavejournalgroup(pOper int, pID int, pJournalId int, pName varchar, pDescription varchar, pParent int) TO iusrpmt;
GRANT EXECUTE ON FUNCTION spsavejournalgroup(pOper int, pID int, pJournalId int, pName varchar, pDescription varchar, pParent int) TO pensoft;
