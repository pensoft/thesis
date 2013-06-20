-- Function: spJustUpdateUserRoles(bigint, integer, integer[])

-- DROP FUNCTION spJustUpdateUserRoles(bigint, integer, integer[]);


CREATE OR REPLACE FUNCTION spJustUpdateUserRoles(
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
	IF pRoles IS NOT NULL THEN
		lIter := 1;
		FOR lRecord IN 1 .. array_upper(pRoles, 1) LOOP
			IF pRoles[lIter] <> 0 THEN
				IF(NOT EXISTS(SELECT id FROM pjs.journal_users WHERE journal_id = pJournalId AND uid = pUid AND role_id = pRoles[lIter])) THEN
					INSERT INTO pjs.journal_users (journal_id, uid, role_id) VALUES (pJournalId, pUid, pRoles[lIter]);
				END IF;
			END IF;
			lIter := lIter + 1; 
		END LOOP;
	END IF;
	RETURN 1;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spJustUpdateUserRoles(bigint, integer, integer[]) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spJustUpdateUserRoles(bigint, integer, integer[]) TO postgres;
GRANT EXECUTE ON FUNCTION spJustUpdateUserRoles(bigint, integer, integer[]) TO iusrpmt;
GRANT EXECUTE ON FUNCTION spJustUpdateUserRoles(bigint, integer, integer[]) TO pensoft;

