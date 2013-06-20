DROP TYPE ret_spObjectFields CASCADE;
CREATE TYPE ret_spObjectFields AS (
	id bigint,
	object_id bigint,
	field_id bigint,
	label varchar,
	control_type int,
	allow_nulls int,
	is_read_only int
);

CREATE OR REPLACE FUNCTION spObjectFields(
	pOper int,
	pId bigint,
	pObjectId bigint,
	pFieldId bigint,
	pLabel varchar,
	pControlType int,
	pAllowNulls int,
	pIsReadOnly int,
	pUid int
)
  RETURNS ret_spObjectFields AS
$BODY$
DECLARE
lRes ret_spObjectFields;
--lSid int;
lCurTime timestamp;
lId bigint;
lObjectId bigint;
lObjectCanBeModified int;
BEGIN

	lId = pId;
	
	IF pOper = 1 THEN -- Insert/Update
		SELECT INTO lObjectCanBeModified result FROM spCheckIfObjectCanBeModified(pObjectId);
		IF lObjectCanBeModified = 0 THEN
			RAISE EXCEPTION 'pwt_admin.objects.cantModifyThisObject';
		END IF;
		
		
		IF lId IS NULL THEN --Insert
			INSERT INTO object_fields(object_id, field_id, label, control_type, allow_nulls, is_read_only) 
				VALUES (pObjectId, pFieldId, pLabel, pControlType, pAllowNulls::boolean, pIsReadOnly::boolean);
			lId = currval('object_fields_id_seq');
		ELSE -- Update
			-- Не променяме object_id-то и field_id ид-то
			UPDATE object_fields SET
				label = pLabel,
				control_type = pControlType,
				allow_nulls = pAllowNulls::boolean,
				is_read_only = pIsReadOnly::boolean
			WHERE id = pId;
		END IF;
		UPDATE objects SET
			lastmoduid = pUid,
			lastmoddate = now()
		WHERE id = pObjectId;
	ELSEIF pOper = 3 THEN -- Delete
		SELECT INTO lObjectId object_id FROM object_fields WHERE id = pId;
		
		SELECT INTO lObjectCanBeModified result FROM spCheckIfObjectCanBeModified(lObjectId);
		IF lObjectCanBeModified = 0 THEN
			RAISE EXCEPTION 'pwt_admin.objects.cantModifyThisObject';
		END IF;
		
		DELETE FROM object_fields WHERE id = pId;
		UPDATE objects SET
			lastmoduid = pUid,
			lastmoddate = now()
		WHERE id = lObjectId;

	END IF;


	SELECT INTO lRes id, object_id, field_id, label, control_type, allow_nulls::int, is_read_only::int
	FROM object_fields WHERE id = lId;


	RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spObjectFields(
	pOper int,
	pId bigint,
	pObjectId bigint,
	pFieldId bigint,
	pLabel varchar,
	pControlType int,
	pAllowNulls int,
	pIsReadOnly int,
	pUid int
) TO iusrpmt;
