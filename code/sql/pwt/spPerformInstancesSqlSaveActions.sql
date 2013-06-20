DROP TYPE ret_spPerformInstancesSqlSaveActions CASCADE;
CREATE TYPE ret_spPerformInstancesSqlSaveActions AS (
	result int
);

CREATE OR REPLACE FUNCTION spPerformInstancesSqlSaveActions(
	pUid int,
	pInstanceIds bigint[],
	pMode int DEFAULT 1
)
  RETURNS ret_spPerformInstancesSqlSaveActions AS
$BODY$
	DECLARE
		lRes ret_spPerformInstancesSqlSaveActions;	
		
		lSqlSaveActionPos int;
		lRecord record;
	BEGIN
		lSqlSaveActionPos = 4;
		-- Първо маркираме обектите (и родителите им нагоре), че вече не са нови
		FOR lRecord IN 
			SELECT id, i.pos, i.document_id 
			FROM pwt.document_object_instances i
			WHERE  i.id = ANY (pInstanceIds)  AND i.is_new = true
		LOOP	
			UPDATE pwt.document_object_instances SET
				is_new = false
			WHERE is_new = true AND document_id = lRecord.document_id AND pos = substring(lRecord.pos, 1, char_length(pos));
		END LOOP;
		
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

GRANT EXECUTE ON FUNCTION spPerformInstancesSqlSaveActions(	
	pUid int,
	pInstanceIds bigint[],
	pMode int
) TO iusrpmt;
