-- Function: spUpdateUserRoles(bigint, integer, integer[])

-- DROP FUNCTION spUpdateUserRoles(bigint, integer, integer[]);


CREATE OR REPLACE FUNCTION spUpdateUserRoles(
	pJournalId bigint,
	pUid integer, 
	pRoles integer[]
)
  RETURNS integer AS
$BODY$
DECLARE
	lRecord integer;
	lIter integer;
BEGIN
	DELETE FROM pjs.journal_users 
	WHERE journal_id = pJournalId 
		AND uid = pUid
		AND role_id <> 11 -- Author Role
		AND role_id <> 5;  -- Dedicated Reviewer
	
	IF pRoles IS NOT NULL THEN
		lIter := 1;
		FOR lRecord IN 1 .. array_upper(pRoles, 1) LOOP
			IF pRoles[lIter] <> 0 THEN
				INSERT INTO pjs.journal_users (journal_id, uid, role_id)
									VALUES (pJournalId, pUid, pRoles[lIter]);
			END IF;
			lIter := lIter + 1; 
		END LOOP;
	END IF;
	RETURN 1;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spUpdateUserRoles(bigint, integer, integer[]) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spUpdateUserRoles(bigint, integer, integer[]) TO postgres;
GRANT EXECUTE ON FUNCTION spUpdateUserRoles(bigint, integer, integer[]) TO iusrpmt;
GRANT EXECUTE ON FUNCTION spUpdateUserRoles(bigint, integer, integer[]) TO pensoft;

