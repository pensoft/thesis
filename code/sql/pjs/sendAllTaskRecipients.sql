CREATE OR REPLACE FUNCTION pjs.sendAllTaskRecipients(pEvents varchar)
  RETURNS int AS
$BODY$
	DECLARE
		lEventsArr varchar[];
		lArrSize int;
		lIndex int;
		lReadyStateId int := 2;
	BEGIN		
		
		lEventsArr = string_to_array(pEvents, ',');
		lArrSize := ARRAY_UPPER(lEventsArr, 1);

		FOR lIndex IN 1..lArrSize LOOP
			UPDATE pjs.email_task_details SET state_id = lReadyStateId 
			WHERE id IN (
				SELECT etd.id
				FROM pjs.email_task_details etd
				JOIN pjs.email_tasks et ON et.id = etd.email_task_id
				JOIN pjs.email_task_definitions etdef ON etdef.id = et.task_definition_id AND is_automated = FALSE
				WHERE et.event_id = lEventsArr[lIndex]::bigint AND etd.state_id IN (1)
			);
		END LOOP;
		
		RETURN 1;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs.sendAllTaskRecipients(varchar) TO iusrpmt;
