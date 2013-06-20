DROP TYPE ret_spRegularExpressions CASCADE;
CREATE TYPE ret_spRegularExpressions AS (
	id int,
	name varchar,	
	expression varchar,
	replacement varchar,
	groupsupdepth varchar
);

CREATE OR REPLACE FUNCTION spRegularExpressions(
	pOper int,
	pId int,
	pName varchar,
	pExpression varchar,
	pReplacement varchar,
	pGroupsUpDepth varchar
)
  RETURNS ret_spRegularExpressions AS
$BODY$
DECLARE
lRes ret_spRegularExpressions;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		INSERT INTO regular_expressions(name, expression, replacement, groupsupdepth) VALUES (pName, pExpression, pReplacement, pGroupsUpDepth);
		lId = currval('autotag_properties_id_seq');
	ELSE -- Update
		UPDATE regular_expressions SET
			name = pName,
			expression = pExpression,
			replacement = pReplacement,
			groupsupdepth = pGroupsUpDepth
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM regular_expressions WHERE id = lId;
ELSEIF pOper = 4 THEN --Copy
	INSERT INTO regular_expressions(name, expression, replacement, groupsupdepth) 
		SELECT coalesce(name, '') || '_copy', expression, replacement, groupsupdepth 
		FROM regular_expressions WHERE id = lId;
	lId = currval('autotag_properties_id_seq');
END IF;


SELECT INTO lRes id, name, expression, replacement, groupsupdepth FROM regular_expressions WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spRegularExpressions(
	pOper int,
	pId int,
	pName varchar,
	pExpression varchar,
	pReplacement varchar,
	pGroupsUpDepth varchar
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spRegularExpressions(
	pOper int,
	pId int,
	pName varchar,
	pExpression varchar,
	pReplacement varchar,
	pGroupsUpDepth varchar
) TO postgres84;
GRANT EXECUTE ON FUNCTION spRegularExpressions(
	pOper int,
	pId int,
	pName varchar,
	pExpression varchar,
	pReplacement varchar,
	pGroupsUpDepth varchar
) TO iusrpmt;
