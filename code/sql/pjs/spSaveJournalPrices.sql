-- Function: spsavejournalstorydata(integer, integer[], integer[], numeric[])

-- DROP FUNCTION spsavejournalstorydata(integer, integer[], integer[], numeric[]);


CREATE OR REPLACE FUNCTION spsavejournalstorydata(
	pJournalId integer,
	pRangeStart integer[], 
	pRangeEnd integer[], 
	pPrice numeric[]
)
  RETURNS integer AS
$BODY$
DECLARE
	lRecord integer;
	lIter integer;
BEGIN
	DELETE FROM pjs.journal_prices WHERE journal_id = pJournalId;
	IF pRangeStart IS NOT NULL THEN
		lIter := 1;
		FOR lRecord IN 1 .. array_upper(pRangeStart, 1) LOOP
			INSERT INTO pjs.journal_prices (journal_id, range_start, range_end, price)
								VALUES (pJournalId, pRangeStart[lIter], pRangeEnd[lIter], pPrice[lIter]);
			lIter := lIter + 1; 
		END LOOP;
	END IF;
	RETURN 1;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spsavejournalstorydata(integer, integer[], integer[], numeric[]) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spsavejournalstorydata(integer, integer[], integer[], numeric[]) TO postgres;
GRANT EXECUTE ON FUNCTION spsavejournalstorydata(integer, integer[], integer[], numeric[]) TO iusrpmt;
GRANT EXECUTE ON FUNCTION spsavejournalstorydata(integer, integer[], integer[], numeric[]) TO pensoft;

