DROP TYPE ret_spPerformInstancesAfterSqlConfirmActions CASCADE;
CREATE TYPE ret_spPerformInstancesAfterSqlConfirmActions AS (
	result int
);

CREATE OR REPLACE FUNCTION spPerformInstancesAfterSqlConfirmActions(
	pUid int,
	pInstanceIds bigint[],
	pMode int DEFAULT 1
)
  RETURNS ret_spPerformInstancesAfterSqlConfirmActions AS
$BODY$
	DECLARE
		lRes ret_spPerformInstancesAfterSqlConfirmActions;	
		
		lActionPos int;
		lRecord record;
	BEGIN
		lActionPos = 17;
		
		--Обработваме ги отгоре- надолу, т.е. първо се викат екшъните на подобектите и след това на самите обекти		
		FOR lRecord IN 
			SELECT i.id as instance_id, a.eval_sql_function as function
			FROM pwt.document_object_instances i
			JOIN pwt.object_actions oa ON oa.object_id = i.object_id
			JOIN pwt.actions a ON a.id = oa.action_id
			WHERE  i.id = ANY (pInstanceIds)  AND oa.pos = lActionPos AND pMode = ANY(oa.execute_in_modes)
			ORDER BY i.pos DESC, oa.ord ASC
		LOOP
			EXECUTE 'SELECT * FROM ' || lRecord.function || '(' || lRecord.instance_id || ', ' || pUid || ');';
		END LOOP;
		
		
		lRes.result = 1;		

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spPerformInstancesAfterSqlConfirmActions(	
	pUid int,
	pInstanceIds bigint[],
	pMode int
) TO iusrpmt;
