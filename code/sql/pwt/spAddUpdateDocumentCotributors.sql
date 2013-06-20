-- Function: pwt.spAddUpdateDocumentCotributors(bigint, bigint)

-- DROP FUNCTION pwt.spAddUpdateDocumentCotributors(bigint, bigint);

CREATE OR REPLACE FUNCTION pwt.spAddUpdateDocumentCotributors(pdocumentid bigint, pinstanceid bigint)
  RETURNS pwt.ret_spgetdocumentauthors AS
$BODY$
	DECLARE
		lRes pwt.ret_spgetdocumentauthors;
	BEGIN
		SELECT INTO lRes.user_exists, lRes.new_user_id, lRes.instance_id, lRes.document_id, lRes.upass  * FROM pwt.spGetDocumentContributors(pDocumentId, pInstanceId);
		--PERFORM pwt.spGetDocumentAuthors(pDocumentId, pInstanceId);
		--PERFORM pwt.spGetDocumentTitle(pDocumentId, pInstanceId);
	RETURN lRes;
	END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION pwt.spAddUpdateDocumentCotributors(bigint, bigint) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spAddUpdateDocumentCotributors(bigint, bigint) TO public;
GRANT EXECUTE ON FUNCTION pwt.spAddUpdateDocumentCotributors(bigint, bigint) TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spAddUpdateDocumentCotributors(bigint, bigint) TO iusrpmt;