DROP TYPE ret_spInsertTempFields CASCADE;
CREATE TYPE ret_spInsertTempFields AS (
	result int
);


CREATE OR REPLACE FUNCTION spInsertTempFields(
	pTempObjectsTableName varchar,
	pTempFieldsTableName varchar
)
  RETURNS ret_spInsertTempFields AS
$BODY$
	DECLARE
		lRes ret_spInsertTempFields;		
		lRecord RECORD;
		lTempRecord RECORD;
		lRealId bigint;
		lQuery varchar;
	BEGIN		
		lQuery = '
		INSERT INTO instance_field_values(instance_id, field_id, value_str, document_id) SELECT
			to.real_id, tf.field_id, tf.value, tf.document_id
		FROM ' || pTempObjectsTableName || ' to
		JOIN ' || pTempFieldsTableName || ' tf ON tf.instance_id = to.id';
		
		EXECUTE lQuery;
		
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spInsertTempFields(
	pTempObjectsTableName varchar,
	pTempFieldsTableName varchar
) TO iusrpmt;
