DROP FUNCTION StoriesIndexer(pGuid int, pIndexer int, pState int, pBody text);

CREATE OR REPLACE FUNCTION StoriesIndexer(pGuid int, pIndexer int, pState int, pBody text) RETURNS int AS
$BODY$
DECLARE 
	lTitleV tsvector;
	lContentV tsvector;
	lBodyV tsvector;
BEGIN
	
	/*
	IF (pIndexer = 0) OR (pState NOT IN (3,4)) THEN
		DELETE FROM storiesft WHERE guid = pGuid;
		RETURN 0;
	END IF;
	*/
	
	SELECT INTO lTitleV, lContentV 
	setweight(to_tsvector('bg_utf8', lower(coalesce(title, ''))), 'A'),
	(
		setweight(to_tsvector('bg_utf8', lower(coalesce(title, ''))), 'A') || 
		setweight(to_tsvector('bg_utf8', lower(coalesce(keywords, ''))), 'A') || 
		setweight(to_tsvector('bg_utf8', lower(coalesce(subtitle, ''))), 'B') || 
		setweight(to_tsvector('bg_utf8', lower(coalesce(nadzaglavie, ''))), 'B') || 
		setweight(to_tsvector('bg_utf8', lower(coalesce(description, ''))), 'B') || 
		setweight(to_tsvector('bg_utf8', lower(coalesce(author, ''))), 'B')
	) 
	FROM stories WHERE guid = pGuid;
	
	lBodyV := coalesce(lContentV, '') || setweight(to_tsvector('bg_utf8', lower(coalesce(pBody, ''))), 'A');
	
	IF EXISTS (SELECT guid FROM storiesft WHERE guid = pGuid) THEN
		UPDATE storiesft SET
			newstext = pBody,
			title = lTitleV,
			content = lContentV,
			body = lBodyV 
		WHERE guid = pGuid;
	ELSE
		INSERT INTO storiesft (guid, newstext, title, content, body) 
			VALUES (pGuid, pBody, lTitleV, lContentV, lBodyV);
	END IF;
	
	RETURN 1;
END ;
$BODY$
  LANGUAGE 'plpgsql' SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION StoriesIndexer(pGuid int, pIndexer int, pState int, pBody text) TO iusrpmt;

REVOKE ALL ON FUNCTION StoriesIndexer(pGuid int, pIndexer int, pState int, pBody text) FROM public;

