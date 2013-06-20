DROP FUNCTION pwt.spGetVersionRoleFilteredMsgRootIds(
	pRevisionId bigint
);

CREATE OR REPLACE FUNCTION pwt.spGetVersionRoleFilteredMsgRootIds(
	pRevisionId bigint
)
  RETURNS SETOF pwt.msg AS
$BODY$
	DECLARE
		lRes pwt.msg;		
		lMsgToSEFlagId int = 8;
	BEGIN		
		
		FOR lRes IN 
			SELECT DISTINCT m1.*				
			FROM pwt.msg m1
			WHERE m1.revision_id = pRevisionId AND m1.id = m1.rootid AND (m1.flags & lMsgToSEFlagId) = 0
		LOOP
			RETURN NEXT lRes;
		END LOOP;
		
		RETURN;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pwt.spGetVersionRoleFilteredMsgRootIds(
	pRevisionId bigint
) TO iusrpmt;
