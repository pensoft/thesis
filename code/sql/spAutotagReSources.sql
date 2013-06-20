DROP TYPE ret_spAutotagReSources CASCADE;
CREATE TYPE ret_spAutotagReSources AS (
	id int,
	name varchar,	
	source_xpath varchar
);

CREATE OR REPLACE FUNCTION spAutotagReSources(
	pOper int,
	pId int,
	pName varchar,
	pSourceXPath varchar
)
  RETURNS ret_spAutotagReSources AS
$BODY$
DECLARE
lRes ret_spAutotagReSources;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		INSERT INTO autotag_re_sources(name, source_xpath) VALUES (pName, pSourceXPath);
		lId = currval('autotag_properties_id_seq');
	ELSE -- Update
		UPDATE autotag_re_sources SET
			name = pName,
			source_xpath = pSourceXPath
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM autotag_re_variables WHERE source_id = lId;
	DELETE FROM autotag_re_sources WHERE id = lId;
ELSEIF pOper = 4 THEN --Copy
	INSERT INTO autotag_re_sources(name, source_xpath) 
		SELECT coalesce(name, '') || '_copy', source_xpath
		FROM autotag_re_sources WHERE id = lId;
	lId = currval('autotag_properties_id_seq');
		
	--Kopirame i prikachenite variable-i
	INSERT INTO autotag_re_variables(name, variable_symbol, source_id, variable_type, expression, concat_multiple, concat_separator)
	SELECT name, variable_symbol, lId, variable_type, expression, concat_multiple, concat_separator
	FROM autotag_re_variables WHERE source_id = pId;
	
END IF;


SELECT INTO lRes id, name, source_xpath FROM autotag_re_sources WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spAutotagReSources(
	pOper int,
	pId int,
	pName varchar,
	pSourceXPath varchar
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spAutotagReSources(
	pOper int,
	pId int,
	pName varchar,
	pSourceXPath varchar
) TO postgres84;
GRANT EXECUTE ON FUNCTION spAutotagReSources(
	pOper int,
	pId int,
	pName varchar,
	pSourceXPath varchar
) TO iusrpmt;
