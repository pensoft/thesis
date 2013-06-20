DROP FUNCTION sp_secgrp(
	pOp int, 
	pID int, 
	pName varchar
);

CREATE OR REPLACE FUNCTION sp_secgrp(
	pOp int, 
	pID int, 
	pName varchar
) RETURNS secgrp AS
$BODY$
DECLARE
	lResult secgrp;
BEGIN

	IF pOp = 1 THEN
		IF pID IS NULL THEN
			-- INSERT
			INSERT INTO secgrp (name) 
				VALUES (pName);
			lResult.id := currval('secgrp_id_seq');
			
		ELSE
			--UPDATE
			UPDATE secgrp SET
				name = pName
			WHERE id = pID;
			
		END IF;
	END IF;
	
	SELECT INTO lResult * 
	FROM secgrp
	WHERE id = pID;

	RETURN lResult;

END ;
$BODY$
  LANGUAGE 'plpgsql' SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION sp_secgrp(
	pOp int, 
	pID int, 
	pName varchar
) TO iusrpmt;

REVOKE ALL ON FUNCTION sp_secgrp(
	pOp int, 
	pID int, 
	pName varchar
) FROM public;
