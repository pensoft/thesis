DROP TYPE ret_spAutotagReVariables CASCADE;

CREATE TYPE ret_spAutotagReVariables AS (
	id int,
	source_id int,
	name varchar,
	variable_symbol varchar,
	variable_type int,
	expression varchar,
	concat_multiple int,
	concat_separator varchar
);

CREATE OR REPLACE FUNCTION spAutotagReVariables(
	pOper int,
	pId int,
	pSourceId int,
	pName varchar,
	pVariableSymbol varchar,
	pVariableType int,
	pExpression varchar,
	pConcatMultiple int,
	pConcatSeparator varchar
	
)
  RETURNS ret_spAutotagReVariables AS
$BODY$
DECLARE
lRes ret_spAutotagReVariables;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		INSERT INTO autotag_re_variables(source_id, name, variable_symbol, variable_type, expression, concat_multiple, concat_separator) 
			VALUES ( pSourceId, pName, pVariableSymbol, pVariableType, pExpression, pConcatMultiple, pConcatSeparator);
		lId = currval('autotag_re_variables_id_seq');
	ELSE -- Update
		UPDATE autotag_re_variables SET
			source_id = pSourceId,
			name = pName,
			variable_symbol = pVariableSymbol,
			variable_type = pVariableType,
			expression = pExpression,
			concat_multiple = pConcatMultiple,
			concat_separator = pConcatSeparator
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM autotag_re_variables WHERE id = lId;
ELSEIF pOper = 4 THEN --Copy
	
END IF;


SELECT INTO lRes id, source_id, name, variable_symbol, variable_type, expression, concat_multiple, concat_separator FROM autotag_re_variables WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spAutotagReVariables(
	pOper int,
	pId int,
	pSourceId int,
	pName varchar,
	pVariableSymbol varchar,
	pVariableType int,
	pExpression varchar,
	pConcatMultiple int,
	pConcatSeparator varchar
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spAutotagReVariables(
	pOper int,
	pId int,
	pSourceId int,
	pName varchar,
	pVariableSymbol varchar,
	pVariableType int,
	pExpression varchar,
	pConcatMultiple int,
	pConcatSeparator varchar
) TO postgres84;
GRANT EXECUTE ON FUNCTION spAutotagReVariables(
	pOper int,
	pId int,
	pSourceId int,
	pName varchar,
	pVariableSymbol varchar,
	pVariableType int,
	pExpression varchar,
	pConcatMultiple int,
	pConcatSeparator varchar
) TO iusrpmt;
