DROP TYPE IF EXISTS ret_getEventOffset CASCADE;
CREATE TYPE ret_getEventOffset AS ("offset" int, offset_end int);

CREATE OR REPLACE FUNCTION pjs.getEventOffset(
	pEventTypeId int,
	pJournalId int,
	pSectionId int
)
	RETURNS ret_getEventOffset AS
$BODY$
	DECLARE
		lRes ret_getEventOffset;
	BEGIN		
		SELECT INTO lRes.offset, lRes.offset_end "offset", offset_end FROM pjs.event_offset WHERE event_type_id = pEventTypeId AND journal_id = pJournalId AND section_id = pSectionId;
		
		IF (lRes.offset IS NULL) THEN
			SELECT INTO lRes.offset, lRes.offset_end "offset", offset_end FROM pjs.event_offset WHERE event_type_id = pEventTypeId;
		END IF;

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs.getEventOffset(
	pEventTypeId int,
	pJournalId int,
	pSectionId int
) TO iusrpmt;
