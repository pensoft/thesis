-- Function: pjs."spGenerateNLMXML"(xml, bigint);

-- DROP FUNCTION pjs."spGenerateNLMXML"(xml, bigint);

CREATE OR REPLACE FUNCTION pjs."spGenerateNLMXML"(pXml xml, pDocumentId bigint)
  RETURNS integer AS
$BODY$
DECLARE
	cNLMXMLItemType int := 19;
	cXMLMetricsItemType int := 7;
BEGIN
		IF NOT EXISTS (SELECT * FROM pjs.article_cached_items WHERE article_id = pDocumentId AND item_type = cNLMXMLItemType) THEN
			INSERT INTO pjs.article_cached_items(cached_val, item_type, article_id) VALUES(pXml, cNLMXMLItemType, pDocumentId);
			PERFORM spCreateArticleMetric(pDocumentId, cXMLMetricsItemType);
		ELSE
			UPDATE pjs.article_cached_items
				SET cached_val = pXml
			WHERE article_id = pDocumentId AND item_type = cNLMXMLItemType;
		END IF;
	
	RETURN 1;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION pjs."spGenerateNLMXML"(xml, bigint) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pjs."spGenerateNLMXML"(xml, bigint) TO postgres;
GRANT EXECUTE ON FUNCTION pjs."spGenerateNLMXML"(xml, bigint) TO iusrpmt;
GRANT EXECUTE ON FUNCTION pjs."spGenerateNLMXML"(xml, bigint) TO pensoft;

