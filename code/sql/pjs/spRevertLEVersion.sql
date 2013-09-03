DROP TYPE IF EXISTS pjs."ret_spRevertLEVersion" CASCADE;
CREATE TYPE pjs."ret_spRevertLEVersion" AS (doc_xml xml);

CREATE OR REPLACE FUNCTION pjs."spRevertLEVersion"(pDocumentId bigint)
  RETURNS pjs."ret_spRevertLEVersion" AS
$BODY$
DECLARE
	lRes pjs."ret_spRevertLEVersion";
	cLEVersionType CONSTANT int := 5;
	cAuthorVersionType CONSTANT int := 1;
	lLastAuthorVersionId bigint;
	LEVersionTypeId bigint;
BEGIN

	SELECT INTO lRes.doc_xml pdv.xml
	FROM pjs.document_versions dv
	JOIN pjs.pwt_document_versions pdv ON pdv.version_id = dv.id
	WHERE dv.document_id = pDocumentId AND dv.version_type_id = cAuthorVersionType
	ORDER BY dv.id DESC
	LIMIT 1;
	
	SELECT INTO LEVersionTypeId id FROM pjs.document_versions WHERE document_id = pDocumentId AND version_type_id = cLEVersionType ORDER BY id DESC LIMIT 1;
	PERFORM pjs."spSaveLEXMLVersion"(LEVersionTypeId, lRes.doc_xml);
RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION pjs."spRevertLEVersion"(pDocumentId bigint) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pjs."spRevertLEVersion"(pDocumentId bigint) TO postgres;
GRANT EXECUTE ON FUNCTION pjs."spRevertLEVersion"(pDocumentId bigint) TO iusrpmt;
