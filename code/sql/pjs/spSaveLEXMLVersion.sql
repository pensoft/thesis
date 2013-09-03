CREATE OR REPLACE FUNCTION pjs."spSaveLEXMLVersion"(pVersionId bigint, pXml xml)
  RETURNS int AS
$BODY$
	DECLARE		
		lArticleId int;
		lXmlAritcleType int := 1;
	BEGIN				
		UPDATE pjs.pwt_document_versions SET xml = pXml WHERE version_id = pVersionId;
		
		SELECT INTO lArticleId a.id
		FROM pjs.document_versions dv
		JOIN pjs.pwt_documents pd ON pd.document_id = dv.document_id
		JOIN pjs.articles a ON a.pwt_document_id = pd.pwt_id
		WHERE dv.id = pVersionId
		LIMIT 1;
		
		UPDATE pjs.article_cached_items SET cached_val = pXml::varchar WHERE article_id = lArticleId AND item_type = lXmlAritcleType;
		
		RETURN 1;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs."spSaveLEXMLVersion"(pVersionId bigint, pXml xml) TO iusrpmt;
