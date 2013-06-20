DROP FUNCTION pwt.spApiLogDocumentProcessing(
	pUid int,
	pXml text,
	pIsSuccessful int,
	pErrMsg text
);

CREATE OR REPLACE FUNCTION pwt.spApiLogDocumentProcessing(
	pUid int,
	pXml text,
	pIsSuccessful int,
	pErrMsg text
)
  RETURNS integer AS
$BODY$
		DECLARE
		BEGIN
			INSERT INTO pwt.api_import_log(uid, xml_content, is_successful, err_msg) VALUES (pUid, pXml, pIsSuccessful::boolean, pErrMsg);
			RETURN 1;
		END;
	$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
GRANT EXECUTE ON FUNCTION pwt.spApiLogDocumentProcessing(
	pUid int,
	pXml text,
	pIsSuccessful int,
	pErrMsg text
) TO iusrpmt;
