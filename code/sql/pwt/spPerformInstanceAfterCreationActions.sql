DROP TYPE ret_spPerformInstanceAfterCreationActions CASCADE;
CREATE TYPE ret_spPerformInstanceAfterCreationActions AS (
	result int
);

CREATE OR REPLACE FUNCTION spPerformInstanceAfterCreationActions(
	pInstanceId bigint,
	pUid int,
	pMode int DEFAULT 1
)
  RETURNS ret_spPerformInstanceAfterCreationActions AS
$BODY$
	DECLARE
		lRes ret_spPerformInstanceAfterCreationActions;							
		lRecord record;
		lActionAfterCreationPos bigint;
		lObjectId bigint;		
	BEGIN
		lActionAfterCreationPos = 5;
		
		SELECT INTO lObjectId object_id FROM pwt.document_object_instances WHERE id = pInstanceId;
		
		--Изпълняваме екшъните след създаване към този обект
		<<AfterCreationActions>>
		FOR lRecord IN
			SELECT a.eval_sql_function as function 
			FROM pwt.actions a
			JOIN pwt.object_actions oa ON oa.action_id = a.id
			WHERE oa.object_id = lObjectId AND oa.pos = lActionAfterCreationPos AND a.eval_sql_function <> '' AND pMode = ANY (oa.execute_in_modes)
			ORDER BY oa.ord ASC 
		LOOP
			EXECUTE 'SELECT * FROM ' || lRecord.function || '(' || pInstanceId || ', ' || pUid || ');';
		END LOOP AfterCreationActions;
		
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spPerformInstanceAfterCreationActions(
	pInstanceId bigint,
	pUid int,
	pMode int
) TO iusrpmt;
