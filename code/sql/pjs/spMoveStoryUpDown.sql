-- Function: spmovestoryupdown(integer, integer, integer)

-- DROP FUNCTION spmovestoryupdown(integer, integer);


CREATE OR REPLACE FUNCTION spmovestoryupdown(
	pguid integer,
	pjournalid integer,
	pdirection integer
)
  RETURNS integer AS
$BODY$
DECLARE
	lStoryId   integer;
	lJournalId integer;
	lPos       character varying;
	lIds	   integer[];
	
	lStoryId2   integer;
	lJournalId2 integer;
	lPos2       character varying;
	lIds2	    integer[];
	
	lRes        integer;
BEGIN
	lRes:= 0;

	SELECT INTO lJournalId, lPos, lStoryId journal_id, pos, guid 
	FROM sid1storyprops 
	WHERE guid = pguid AND journal_id = pjournalid;
	
	IF lStoryId IS NOT NULL THEN
		IF pdirection = 1 THEN -- Move Up
			SELECT INTO lJournalId2, lPos2, lStoryId2 journal_id, pos, guid 
			FROM sid1storyprops 
			WHERE pos < lPos AND pos LIKE substring(lPos, 0, char_length(lPos)-1) || '%'
				AND char_length(pos) = char_length(lPos) 
				AND journal_id = pjournalid 
			ORDER BY pos DESC
			LIMIT 1;
			
			IF lStoryId2 IS NOT NULL THEN
				SELECT INTO lIds array_agg(guid) FROM sid1storyprops WHERE pos LIKE lPos || '%' AND journal_id = pjournalid;
				SELECT INTO lIds2 array_agg(guid) FROM sid1storyprops WHERE pos LIKE lPos2 || '%' AND journal_id = pjournalid;
				
				UPDATE sid1storyprops SET pos = overlay(pos placing lPos2 from 1 for char_length(lPos2)) WHERE guid = ANY (lIds);
				UPDATE sid1storyprops SET pos = overlay(pos placing lPos from 1 for char_length(lPos)) WHERE guid = ANY (lIds2);
				lRes:= 1;
			END IF;
		ELSE -- Move Down
			SELECT INTO lJournalId2, lPos2, lStoryId2 journal_id, pos, guid 
			FROM sid1storyprops 
			WHERE pos > lPos AND pos LIKE substring(lPos, 0, char_length(lPos)-1) || '%'
				AND char_length(pos) = char_length(lPos) 
				AND journal_id = pjournalid
			ORDER BY pos ASC
			LIMIT 1;
			
			IF lStoryId2 IS NOT NULL THEN
				SELECT INTO lIds array_agg(guid) FROM sid1storyprops WHERE pos LIKE lPos || '%' AND journal_id = pjournalid;
				SELECT INTO lIds2 array_agg(guid) FROM sid1storyprops WHERE pos LIKE lPos2 || '%' AND journal_id = pjournalid;
				
				UPDATE sid1storyprops SET pos = overlay(pos placing lPos2 from 1 for char_length(lPos2)) WHERE guid = ANY (lIds);
				UPDATE sid1storyprops SET pos = overlay(pos placing lPos from 1 for char_length(lPos)) WHERE guid = ANY (lIds2);
				lRes:= 1;
			END IF;
		END IF;
	END IF;
	RETURN lRes;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spmovestoryupdown(integer, integer, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spmovestoryupdown(integer, integer, integer) TO postgres;
GRANT EXECUTE ON FUNCTION spmovestoryupdown(integer, integer, integer) TO iusrpmt;
GRANT EXECUTE ON FUNCTION spmovestoryupdown(integer, integer, integer) TO pensoft;