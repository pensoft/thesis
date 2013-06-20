-- Function: spsavejournalstorydata(integer, integer, character varying, character varying, character varying, character varying, timestamp without time zone, character varying, integer, character varying, integer, character varying, character varying, integer, integer, character varying, integer, integer, integer, integer, text, integer)

-- DROP FUNCTION spsavejournalstorydata(integer, integer, character varying, character varying, character varying, character varying, timestamp without time zone, character varying, integer, character varying, integer, character varying, character varying, integer, integer, character varying, integer, integer, integer, integer, text, integer);


CREATE OR REPLACE FUNCTION spsavejournalstorydata(
	pguid integer, 
	pprimarysite integer, 
	plang character varying, 
	ptitle character varying, 
	plink character varying, 
	pdescription character varying, 
	ppubdate timestamp without time zone, 
	pauthor character varying, 
	pcreateuid integer, 
	pkeywords character varying, 
	pstate integer, 
	psubtitle character varying, 
	pnadzaglavie character varying, 
	pstorytype integer, 
	ppriority integer, 
	pindexer integer, 
	pshowforum integer, 
	pdscid integer, 
	pbody text, 
	pjournal_id integer,
	parent_id integer,
	pshow_in_sidebar integer
)
  RETURNS integer AS
$BODY$
DECLARE
	lStoryId integer;
	lRubrId integer;
	lOrd character varying;
	lParentOrd character varying;
	lRootId integer;
BEGIN
	IF pshow_in_sidebar IS NULL THEN
		pshow_in_sidebar := 0;
	END IF;
	IF pguid IS NOT NULL THEN
		SELECT INTO lStoryId guid FROM savestoriesbasedata(pguid, pprimarysite, plang, ptitle, plink, pdescription, ppubdate, 
						pauthor, pcreateuid, pkeywords, pstate, psubtitle, pnadzaglavie, pstorytype, 
						lRubrId, lRubrId::varchar, ppriority, pindexer, pshowforum, pdscid, pbody, pjournal_id) as guid;
		UPDATE sid1storyprops SET show_in_sidebar = pshow_in_sidebar WHERE guid = pguid;
	ELSE
		IF pjournal_id IS NOT NULL THEN
			SELECT INTO lRubrId pjs_rubr_id FROM journals WHERE id = pjournal_id;
			SELECT INTO lStoryId guid FROM savestoriesbasedata(pguid, pprimarysite, plang, ptitle, plink, pdescription, ppubdate, 
							pauthor, pcreateuid, pkeywords, pstate, psubtitle, pnadzaglavie, pstorytype, 
							lRubrId, lRubrId::varchar, ppriority, pindexer, pshowforum, pdscid, pbody, pjournal_id) as guid;
								
			IF parent_id IS NOT NULL THEN
				SELECT INTO lParentOrd, lRootId pos, rootnode FROM sid1storyprops WHERE guid = parent_id;
				SELECT INTO lOrd max(pos) FROM sid1storyprops WHERE rootnode = lRootId AND pos LIKE lParentOrd || '%' AND char_length(pos) = char_length(lParentOrd) + 2;
				lOrd := coalesce(ForumGetNextOrd(lOrd), 'AA');
				lOrd := lParentOrd || lOrd;
				UPDATE sid1storyprops SET rootnode = lRootId, pos = lOrd, show_in_sidebar = pshow_in_sidebar WHERE guid = lStoryId;
			ELSE
				SELECT INTO lOrd max(pos) FROM sid1storyprops WHERE journal_id = pjournal_id AND char_length(pos) = 2;
				lOrd := coalesce(ForumGetNextOrd(lOrd), 'AA');
				UPDATE sid1storyprops SET rootnode = lStoryId, pos = lOrd, show_in_sidebar = pshow_in_sidebar WHERE guid = lStoryId;
			END IF;
		END IF;
	END IF;
	RETURN lStoryId;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spsavejournalstorydata(integer, integer, character varying, character varying, character varying, character varying, timestamp without time zone, character varying, integer, character varying, integer, character varying, character varying, integer, integer, integer, integer, integer, text, integer, integer, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spsavejournalstorydata(integer, integer, character varying, character varying, character varying, character varying, timestamp without time zone, character varying, integer, character varying, integer, character varying, character varying, integer, integer, integer, integer, integer, text, integer, integer, integer) TO postgres;
GRANT EXECUTE ON FUNCTION spsavejournalstorydata(integer, integer, character varying, character varying, character varying, character varying, timestamp without time zone, character varying, integer, character varying, integer, character varying, character varying, integer, integer, integer, integer, integer, text, integer, integer, integer) TO iusrpmt;
GRANT EXECUTE ON FUNCTION spsavejournalstorydata(integer, integer, character varying, character varying, character varying, character varying, timestamp without time zone, character varying, integer, character varying, integer, character varying, character varying, integer, integer, integer, integer, integer, text, integer, integer, integer) TO pensoft;

