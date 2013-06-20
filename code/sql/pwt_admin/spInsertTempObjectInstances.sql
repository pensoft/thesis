DROP TYPE ret_spInsertTempObjectInstances CASCADE;
CREATE TYPE ret_spInsertTempObjectInstances AS (
	result int
);


CREATE OR REPLACE FUNCTION spInsertTempObjectInstances(
	pTempTableName varchar
)
  RETURNS ret_spInsertTempObjectInstances AS
$BODY$
	DECLARE
		lRes ret_spInsertTempObjectInstances;		
		lRecord RECORD;
		lTempRecord RECORD;
		lRealId bigint;
		lQuery varchar;
	BEGIN		
		lQuery = 'SELECT * FROM ' || pTempTableName;
		FOR lRecord IN EXECUTE lQuery LOOP
			SELECT INTO lRealId nextval('pwt.document_object_instances_id_seq'::regclass);
			INSERT INTO pwt.document_object_instances(id, document_id, pos, object_id, display_in_tree)
				VALUES(lRealId, lRecord.document_id, lRecord.pos, lRecord.object_id, lRecord.display_in_tree);
				
			EXECUTE 'UPDATE ' || pTempTableName || ' SET real_id = ' || lRealId || ' WHERE id = ' || lRecord.id;			
		END LOOP;
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spInsertTempObjectInstances(
	pTempTableName varchar
) TO iusrpmt;
