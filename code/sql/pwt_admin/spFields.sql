DROP TYPE ret_spFields CASCADE;
CREATE TYPE ret_spFields AS (
	id bigint,
	name varchar,
	type int,
	default_label varchar,
	default_control_type int,
	default_allow_nulls int,
	default_is_read_only int
);

CREATE OR REPLACE FUNCTION spFields(
	pOper int,
	pId bigint,
	pName varchar,
	pType int,
	pDefaultLabel varchar,
	pDefaultControlType int,
	pDefaultAllowNulls int,
	pDefaultIsReadOnly int,
	pUid int
)
  RETURNS ret_spFields AS
$BODY$
DECLARE
lRes ret_spFields;
--lSid int;
lCurTime timestamp;
lId bigint;
lFieldCanBeModified int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		INSERT INTO fields(name, type, default_control_type, default_label, default_allow_nulls, createuid, lastmoduid, default_is_read_only) 
			VALUES (pName, pType, pDefaultControlType, pDefaultLabel, pDefaultAllowNulls::boolean, pUid, pUid, pDefaultIsReadOnly::boolean);
		lId = currval('fields_id_seq');
	ELSE -- Update
		SELECT INTO lFieldCanBeModified result FROM spCheckIfFieldCanBeModified(pId);
		IF lFieldCanBeModified = 0 THEN
			RAISE EXCEPTION 'pwt_admin.fields.cantModifyThisField';
		END IF;
		
		UPDATE fields SET
			name = pName,
			default_control_type = pDefaultControlType,
			default_label = pDefaultLabel, 
			default_allow_nulls = pDefaultAllowNulls::boolean,
			default_is_read_only = pDefaultIsReadOnly::boolean,
			lastmoduid = pUid,
			lastmoddate = now()
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN -- Delete
	

END IF;


SELECT INTO lRes id, name, type, default_label, default_control_type, default_allow_nulls::int, default_is_read_only::int
FROM fields WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spFields(
	pOper int,
	pId bigint,
	pName varchar,
	pType int,
	pDefaultLabel varchar,
	pDefaultControlType int,
	pDefaultAllowNulls int,
	pDefaultIsReadOnly int,
	pUid int
) TO iusrpmt;
