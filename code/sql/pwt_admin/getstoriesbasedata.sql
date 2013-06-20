-- Function: getstoriesbasedata(integer, integer)

-- DROP FUNCTION getstoriesbasedata(integer, integer);

CREATE OR REPLACE FUNCTION getstoriesbasedata(pguid integer, plangid integer)
  RETURNS SETOF retgetstoriesbasedata AS
$BODY$
DECLARE
	lResult retGetStoriesBaseData%ROWTYPE;
	lIcons varchar;
	lrubrids varchar;
	lrubrtxt varchar;
	lRubrRes RECORD;
BEGIN
	lIcons = '';
	lrubrids = '';
	lrubrtxt = '';

	FOR lResult IN SELECT s.guid, s.title, s.author, s.pubdate, s.state, s.description, s.keywords, s.lastmod, u.name, 
		s.subtitle, s.primarysite, s.link, s.nadzaglavie, s.showforum, s.storytype, s.lang, null, null, sp.valint, sp.valint2, sd.journal_id
		FROM stories s 
		JOIN sid1storyprops sd USING(guid) 
		LEFT JOIN storyproperties sp ON s.guid = sp.guid AND sp.propid = 4 
		LEFT JOIN usr u on (s.createuid = u.id)
		WHERE s.guid = pguid
	LOOP

		FOR lRubrRes IN
			SELECT sp.valint, r.name[pLangid]
			FROM storyproperties sp 
			JOIN rubr r ON sp.valint = r.id 
			WHERE sp.guid = pguid AND sp.propid = 1
		LOOP
			lrubrids := lrubrids || lRubrRes.valint::varchar || ',';
			lrubrtxt := lrubrtxt || lRubrRes.name::varchar || ', ';
		END LOOP;

		lResult.rubr := lrubrids;
		lResult.rubrstr := lrubrtxt;
		
		RETURN NEXT lResult;
	END LOOP;

	RETURN;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100
  ROWS 1000;
ALTER FUNCTION getstoriesbasedata(integer, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION getstoriesbasedata(integer, integer) TO postgres;
GRANT EXECUTE ON FUNCTION getstoriesbasedata(integer, integer) TO public;
GRANT EXECUTE ON FUNCTION getstoriesbasedata(integer, integer) TO iusrpmt;
GRANT EXECUTE ON FUNCTION getstoriesbasedata(integer, integer) TO pensoft;
