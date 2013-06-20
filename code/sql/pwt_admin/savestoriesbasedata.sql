-- Function: savestoriesbasedata(integer, integer, character varying, character varying, character varying, character varying, timestamp without time zone, character varying, integer, character varying, integer, character varying, character varying, integer, integer, character varying, integer, integer, integer, integer, text)

-- DROP FUNCTION savestoriesbasedata(integer, integer, character varying, character varying, character varying, character varying, timestamp without time zone, character varying, integer, character varying, integer, character varying, character varying, integer, integer, character varying, integer, integer, integer, integer, text);

CREATE OR REPLACE FUNCTION savestoriesbasedata(pguid integer, pprimarysite integer, plang character varying, ptitle character varying, plink character varying, pdescription character varying, ppubdate timestamp without time zone, pauthor character varying, pcreateuid integer, pkeywords character varying, pstate integer, psubtitle character varying, pnadzaglavie character varying, pstorytype integer, pmainrubr integer, prubr character varying, ppriority integer, pindexer integer, pshowforum integer, pdscid integer, pbody text, pjournal_id integer)
  RETURNS integer AS
$BODY$
DECLARE
	lRubrArr text[];
	lArrSize integer;
	lArrIter integer;
	lRubr varchar;
	lGuid int;
	lRootID int;
	lKeyw text;
	lAllowedKwdArr text[];
	lKeyWords text[];
	lNewKeyWords text;

BEGIN
	
	IF (pRubr IS NOT NULL AND pMainRubr IS NULL) THEN
		RAISE EXCEPTION 'savestoriesbasedata.mustSelectMainRubr';
	END IF;
	
	-- Kluchovi dumi
	lNewKeyWords := '';
	lAllowedKwdArr := NULL;

	IF (pkeywords <> '') THEN 
		lKeyWords := string_to_array(pkeywords, ',');
				
		FOR i IN array_lower(lKeyWords, 1) .. array_upper(lKeyWords, 1) LOOP
			lKeyw := replace(replace(trim(translate(lKeyWords[i], E'\n\r\t', '   ')), '  ', ' '), '   ', ' ');
			
			IF (lAllowedKwdArr IS NULL) THEN
				lAllowedKwdArr := ARRAY[lKeyw];
			ELSE
				lAllowedKwdArr := lAllowedKwdArr || lKeyw;
			END IF;
			
			lNewKeyWords := array_to_string(lAllowedKwdArr, ', ');
		END LOOP;
	END IF;
	
	IF (trim(E'\n\r \t' from pauthor) <> '') THEN 
		IF NOT EXISTS (SELECT * from authors WHERE upper(authors_name) = upper(trim(E'\n\r \t' from pauthor))) THEN
			INSERT INTO authors VALUES (default,trim(E'\n\r \t' from pauthor));
		END IF;
	END IF;
	
	IF (pguid IS NULL) THEN
		INSERT INTO stories
			(lang, title, link, description, pubdate, author, createdate, lastmod, createuid, 
			 keywords, state, subtitle, primarysite, nadzaglavie, showforum, storytype)
			VALUES (pLang, ptitle, plink, pdescription, ppubdate, pauthor, current_timestamp, 
			current_timestamp, pcreateuid, lNewKeyWords, pstate, psubtitle, pprimarysite,
			pnadzaglavie, pshowforum, pStoryType);
		
		lGuid := currval('stories_guid_seq');
	ELSE
		UPDATE stories 
			SET lang = pLang, 
				title = ptitle, 
				link = plink, 
				description = pdescription, 
				pubdate = ppubdate, 
				author = pauthor, 
				lastmod = current_timestamp, 
				keywords = lNewKeyWords,
				state = pstate, 
				subtitle = psubtitle, 
				primarysite = pprimarysite, 
				nadzaglavie = pnadzaglavie, 
				showforum = pshowforum, 
				storytype = pStoryType
			WHERE guid = pguid;
		lGuid := pguid;
	END IF;
	
	SELECT INTO lRootID rootid
	FROM msg
	WHERE
		id = rootid
		AND itemid = lGuid
		AND dscid = pDscID;
	
	IF lRootID IS NULL THEN
		SELECT INTO lRootID forumaddfirstmsg FROM ForumAddFirstMsg(pDscID, lGuid, pcreateuid, null);
	ELSE
		IF pguid IS NOT NULL THEN
			UPDATE msg SET subject = ptitle WHERE id = rootid AND itemid = lGuid AND dscid = pDscID;
		END IF;
	END IF;
	
	IF EXISTS (SELECT * FROM sid1storyprops WHERE guid = lGuid) THEN
		UPDATE sid1storyprops SET priority = pPriority, journal_id = pjournal_id WHERE guid = lGuid;
	ELSE 
		INSERT INTO sid1storyprops (guid, priority, viewed, journal_id) 
		VALUES (lGuid, pPriority, 0, pjournal_id);
	END IF;
	
	PERFORM SaveStoriesRubriki(lGuid, pRubr, pMainRubr, pIndexer, pprimarysite);
	PERFORM StoriesIndexer(lGuid, pIndexer, pstate, pBody);
	
	INSERT INTO storychangelog (guid, modtime, userid, status, init) 
		VALUES (lGuid, current_timestamp, pcreateuid, pstate, (CASE WHEN pguid IS NULL THEN 1 ELSE 0 END));
	
	RETURN lGuid;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION savestoriesbasedata(integer, integer, character varying, character varying, character varying, character varying, timestamp without time zone, character varying, integer, character varying, integer, character varying, character varying, integer, integer, character varying, integer, integer, integer, integer, text) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION savestoriesbasedata(integer, integer, character varying, character varying, character varying, character varying, timestamp without time zone, character varying, integer, character varying, integer, character varying, character varying, integer, integer, character varying, integer, integer, integer, integer, text, integer) TO postgres;
GRANT EXECUTE ON FUNCTION savestoriesbasedata(integer, integer, character varying, character varying, character varying, character varying, timestamp without time zone, character varying, integer, character varying, integer, character varying, character varying, integer, integer, character varying, integer, integer, integer, integer, text, integer) TO iusrpmt;
GRANT EXECUTE ON FUNCTION savestoriesbasedata(integer, integer, character varying, character varying, character varying, character varying, timestamp without time zone, character varying, integer, character varying, integer, character varying, character varying, integer, integer, character varying, integer, integer, integer, integer, text, integer) TO pensoft;
