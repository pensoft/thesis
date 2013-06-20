-- Function: pwt.spupdatedocumenttitleauthors(bigint, bigint)

-- DROP FUNCTION pwt.spupdatedocumenttitleauthors(bigint, bigint);

CREATE OR REPLACE FUNCTION pwt.spupdatedocumenttitleauthors(pdocumentid bigint, pinstanceid bigint)
  RETURNS pwt.ret_spgetdocumentauthors AS
$BODY$
	DECLARE
		lRes pwt.ret_spgetdocumentauthors;
	BEGIN
		SELECT INTO lRes.user_exists, lRes.new_user_id, lRes.instance_id, lRes.document_id, lRes.upass  * FROM pwt.spGetDocumentAuthors(pDocumentId, pInstanceId);
		--PERFORM pwt.spGetDocumentAuthors(pDocumentId, pInstanceId);
		PERFORM pwt.spGetDocumentTitle(pDocumentId, pInstanceId);
	RETURN lRes;
	END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION pwt.spupdatedocumenttitleauthors(bigint, bigint) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spupdatedocumenttitleauthors(bigint, bigint) TO public;
GRANT EXECUTE ON FUNCTION pwt.spupdatedocumenttitleauthors(bigint, bigint) TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spupdatedocumenttitleauthors(bigint, bigint) TO iusrpmt;