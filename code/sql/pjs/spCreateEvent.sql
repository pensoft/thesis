DROP TYPE IF EXISTS ret_spCreateEvent CASCADE;
CREATE TYPE ret_spCreateEvent AS (
	event_id bigint
);

CREATE OR REPLACE FUNCTION spCreateEvent(
	pEventType int,
	pDocumentId bigint,
	pUserId bigint,
	pJournalId int,
	pUsrEventTo bigint,
	pUsrRoleId int
)
  RETURNS ret_spCreateEvent AS
$BODY$
	DECLARE
		lRes ret_spCreateEvent;	
		lDocumentIdDataTypeId int;
		lUserIdDataTypeId int;
		lUserEventToIdDataType int;
		lUserEventRoleIdDataType int;
		lJournalIdDataType int;
	BEGIN		
		lDocumentIdDataTypeId = 2;
		lUserIdDataTypeId = 3;
		lUserEventToIdDataType = 4;
		lUserEventRoleIdDataType = 6;
		lJournalIdDataType = 7;
		
		INSERT INTO pjs.event_log(event_type_id, journal_id, eventdate) VALUES(pEventType, pJournalId, now());
		lRes.event_id = currval('pjs.event_log_id_seq');
		
		-- inserting event data (document_id, user_id)
		IF (pDocumentId IS NOT NULL) THEN
			INSERT INTO pjs.event_data(event_id, value_int, event_data_type_id) VALUES(lRes.event_id, pDocumentId, lDocumentIdDataTypeId);
		END IF;
		
		IF (pJournalId IS NOT NULL) THEN
			INSERT INTO pjs.event_data(event_id, value_int, event_data_type_id) VALUES(lRes.event_id, pJournalId, lJournalIdDataType);
		END IF;
		
		INSERT INTO pjs.event_data(event_id, value_int, event_data_type_id) VALUES(lRes.event_id, pUserId, lUserIdDataTypeId);
		
		IF (pUsrEventTo IS NOT NULL) THEN
			INSERT INTO pjs.event_data(event_id, value_int, event_data_type_id) VALUES(lRes.event_id, pUsrEventTo, lUserEventToIdDataType);
		END IF;
		
		IF (pUsrRoleId IS NOT NULL) THEN
			INSERT INTO pjs.event_data(event_id, value_int, event_data_type_id) VALUES(lRes.event_id, pUsrRoleId, lUserEventRoleIdDataType);
		END IF;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCreateEvent(
	pEventType int,
	pDocumentId bigint,
	pUserId bigint,
	pJournalId int,
	pUsrEventTo bigint,
	pUsrRoleId int
) TO iusrpmt;
