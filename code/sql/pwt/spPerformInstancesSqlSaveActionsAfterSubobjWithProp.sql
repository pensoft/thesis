DROP TYPE ret_spPerformInstancesSqlSaveActionsAfterSubobjWithProp CASCADE;
CREATE TYPE ret_spPerformInstancesSqlSaveActionsAfterSubobjWithProp AS (
	result int
);

CREATE OR REPLACE FUNCTION spPerformInstancesSqlSaveActionsAfterSubobjWithProp(
	pUid int,
	pInstanceIds bigint[],
	pMode int DEFAULT 1
)
  RETURNS ret_spPerformInstancesSqlSaveActionsAfterSubobjWithProp AS
$BODY$
	DECLARE
		lRes ret_spPerformInstancesSqlSaveActionsAfterSubobjWithProp;	
		
		lSqlSaveActionPos int;
		lRecord record;
	BEGIN
		lSqlSaveActionPos = 11;
		
		--Обработваме ги отгоре- надолу, т.е. първо се викат екшъните на подобектите и след това на самите обекти		
		--За всеки от променяните обекти викаме и after save event-ите на parent-ите
		FOR lRecord IN 
			SELECT i.id as instance_id, a.eval_sql_function as function
			FROM pwt.document_object_instances i
			JOIN pwt.document_object_instances c ON c.document_id = i.document_id AND i.pos = substring(c.pos, 1, char_length(i.pos))
			JOIN pwt.object_actions oa ON oa.object_id = i.object_id
			JOIN pwt.actions a ON a.id = oa.action_id
			WHERE c.id = ANY (pInstanceIds) AND oa.pos = lSqlSaveActionPos AND pMode = ANY(oa.execute_in_modes)
			ORDER BY i.pos DESC, oa.ord ASC
		LOOP
			EXECUTE 'SELECT * FROM ' || lRecord.function || '(' || lRecord.instance_id || ', ' || pUid || ');';
		END LOOP;
		
		
		lRes.result = 1;		

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spPerformInstancesSqlSaveActionsAfterSubobjWithProp(	
	pUid int,
	pInstanceIds bigint[],
	pMode int
) TO iusrpmt;
