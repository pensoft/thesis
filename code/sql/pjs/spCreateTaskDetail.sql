DROP FUNCTION spCreateTaskDetail(
	pTemplates varchar[],
	pUids int[],
	pURoles int[],
	pTaskId bigint,
	pManual boolean,
	pSubject varchar[]
);

CREATE OR REPLACE FUNCTION spCreateTaskDetail(
	pTemplates varchar[],
	pUids int[],
	pURoles int[],
	pTaskId bigint,
	pManual boolean,
	pSubject varchar[],
	pCC varchar
)
  RETURNS int AS
$BODY$
	DECLARE
		i int;
		lURole int := NULL;
		lTaskDetailStateId int := 2;
	BEGIN
		IF(pManual = FALSE) THEN
			lTaskDetailStateId = 1;
		END IF;
		
		FOR i IN array_lower(pUids, 1) .. array_upper(pUids, 1) LOOP
			
			IF(pURoles[i] > 0) THEN
				lURole = pURoles[i];
			END IF;
			
			INSERT INTO pjs.email_task_details(email_task_id, uid, state_id, template, role_id, subject, cc) VALUES(pTaskId, pUids[i], lTaskDetailStateId, pTemplates[i], lURole, pSubject[i], pCC);
		END LOOP; 
		
		RETURN 1;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spCreateTaskDetail(varchar[], int[], int[], bigint, boolean, varchar[], varchar) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spCreateTaskDetail(varchar[], int[], int[], bigint, boolean, varchar[], varchar) TO postgres;
GRANT EXECUTE ON FUNCTION spCreateTaskDetail(varchar[], int[], int[], bigint, boolean, varchar[], varchar) TO iusrpmt;
GRANT EXECUTE ON FUNCTION spCreateTaskDetail(varchar[], int[], int[], bigint, boolean, varchar[], varchar) TO pensoft;
