-- DROP FUNCTION spdeletejournalstory(integer, integer);

CREATE OR REPLACE FUNCTION spdeletejournalstory( pstoryid integer, pjournalid integer )
  RETURNS integer AS
$BODY$
DECLARE
	lJournalId integer;
	lPos character varying;
	lStoryId integer;
BEGIN
	SELECT INTO lJournalId, lPos, lStoryId journal_id, pos, guid FROM sid1storyprops WHERE guid = pstoryid AND journal_id = pjournalid;
	IF lStoryId IS NOT NULL THEN 
		FOR lStoryId IN 
			SELECT guid FROM sid1storyprops WHERE journal_id = lJournalId AND pos LIKE lPos || '%'
		LOOP
			PERFORM deletestory(lStoryId);
		END LOOP;
		return 1;
	END IF;
	return 0;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spdeletejournalstory(integer, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spdeletejournalstory(integer, integer) TO postgres;
GRANT EXECUTE ON FUNCTION spdeletejournalstory(integer, integer) TO iusrpmt;
GRANT EXECUTE ON FUNCTION spdeletejournalstory(integer, integer) TO pensoft;

