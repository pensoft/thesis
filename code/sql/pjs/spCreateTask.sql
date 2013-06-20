DROP TYPE IF EXISTS ret_spCreateTask CASCADE;
CREATE TYPE ret_spCreateTask AS (
	id int
);

CREATE OR REPLACE FUNCTION spCreateTask(
	pEventId bigint,
	pTaskDefinitionId int
)
  RETURNS ret_spCreateTask AS
$BODY$
	DECLARE
		lRes ret_spCreateTask;	
	BEGIN
		INSERT INTO pjs.email_tasks(task_definition_id, event_id, state_id, createdate) VALUES(pTaskDefinitionId, pEventId, 1, now());
		lRes.id = currval('pjs.email_tasks_id_seq');
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spCreateTask(bigint, int) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spCreateTask(bigint, int) TO postgres;
GRANT EXECUTE ON FUNCTION spCreateTask(bigint, int) TO iusrpmt;
GRANT EXECUTE ON FUNCTION spCreateTask(bigint, int) TO pensoft;
