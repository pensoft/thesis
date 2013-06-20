DROP TYPE ret_spPerformInstanceBeforeDeleteActions CASCADE;
CREATE TYPE ret_spPerformInstanceBeforeDeleteActions AS (
	result int
);

CREATE OR REPLACE FUNCTION spPerformInstanceBeforeDeleteActions(
	pInstanceId bigint,
	pUid int,
	pMode int DEFAULT 1
)
  RETURNS ret_spPerformInstanceBeforeDeleteActions AS
$BODY$
	DECLARE
		lRes ret_spPerformInstanceBeforeDeleteActions;					
		lReferenceTypeFieldId bigint;
		lRecord record;
		lActionBeforeDeletePos bigint;
		lObjectId bigint;		
	BEGIN
		lActionBeforeDeletePos = 6;
		
		SELECT INTO lObjectId object_id FROM pwt.document_object_instances WHERE id = pInstanceId;
		
		--Изпълняваме екшъните след създаване към този обект
		<<AfterCreationActions>>
		FOR lRecord IN
			SELECT a.eval_sql_function as function 
			FROM pwt.actions a
			JOIN pwt.object_actions oa ON oa.action_id = a.id
			WHERE oa.object_id = lObjectId AND oa.pos = lActionBeforeDeletePos AND a.eval_sql_function <> '' 
			ORDER BY oa.ord ASC 
		LOOP
			EXECUTE 'SELECT * FROM ' || lRecord.function || '(' || pInstanceId || ', ' || pUid || ');';
		END LOOP AfterCreationActions;
		
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spPerformInstanceBeforeDeleteActions(
	pInstanceId bigint,
	pUid int,
	pMode int
) TO iusrpmt;
