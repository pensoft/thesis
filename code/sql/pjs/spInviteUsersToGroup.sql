CREATE OR REPLACE FUNCTION spInviteUsersToGroup(	
	pOper int,
	pUid int,
	pJournal_User_Group_Id int
)
  RETURNS text AS
$BODY$
	DECLARE
		lPos int;
	BEGIN		
		IF pOper = 1 THEN
			-- insert user into pjs.journal_user_group_users
			SELECT INTO lPos max(pos) FROM pjs.journal_user_group_users;
			IF lPos IS NULL THEN
				lPos = 1;
			END IF;
			INSERT INTO pjs.journal_user_group_users(uid, journal_user_group_id, pos) VALUES(pUid, pJournal_User_Group_Id, (lPos+1));
		ELSEIF pOper = 2 THEN
			-- remove user from group
			DELETE FROM pjs.journal_user_group_users WHERE uid = pUid AND journal_user_group_id = pJournal_User_Group_Id;
		END IF;
		RETURN 1;
	END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spInviteUsersToGroup(integer, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spInviteUsersToGroup(integer, integer) TO postgres;
GRANT EXECUTE ON FUNCTION spInviteUsersToGroup(integer, integer) TO iusrpmt;
GRANT EXECUTE ON FUNCTION spInviteUsersToGroup(integer, integer) TO pensoft;