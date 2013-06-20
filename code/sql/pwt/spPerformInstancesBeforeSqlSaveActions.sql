DROP TYPE ret_spPerformInstancesBeforeSqlSaveActions CASCADE;
CREATE TYPE ret_spPerformInstancesBeforeSqlSaveActions AS (
	result int
);

CREATE OR REPLACE FUNCTION spPerformInstancesBeforeSqlSaveActions(
	pUid int,
	pInstanceIds bigint[],
	pMode int DEFAULT 1
)
  RETURNS ret_spPerformInstancesBeforeSqlSaveActions AS
$BODY$
	DECLARE
		lRes ret_spPerformInstancesBeforeSqlSaveActions;	
		
		lSqlBeforeSaveActionPos int;
		lRecord record;
	BEGIN
		lSqlBeforeSaveActionPos = 9;
		
		--Обработваме ги отгоре- надолу, т.е. първо се викат екшъните на подобектите и след това на самите обекти		
		FOR lRecord IN 
			SELECT i.id as instance_id, a.eval_sql_function as function
			FROM pwt.document_object_instances i
			JOIN pwt.object_actions oa ON oa.object_id = i.object_id
			JOIN pwt.actions a ON a.id = oa.action_id
			WHERE i.id = ANY (pInstanceIds) AND oa.pos = lSqlBeforeSaveActionPos AND pMode = ANY(oa.execute_in_modes)
			ORDER BY i.pos DESC, oa.ord ASC
		LOOP
			EXECUTE 'SELECT * FROM ' || lRecord.function || '(' || lRecord.instance_id || ', ' || pUid || ');';
		END LOOP;
		
		
		lRes.result = 1;		

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spPerformInstancesBeforeSqlSaveActions(	
	pUid int,
	pInstanceIds bigint[],
	pMode int
) TO iusrpmt;
