DROP TYPE IF EXISTS ret_spUndiscloseVersion CASCADE;
CREATE TYPE ret_spUndiscloseVersion AS (
	result boolean
);

CREATE OR REPLACE FUNCTION pjs.spUndiscloseVersion(
	pVersionId bigint,
	pRoleId bigint
)
	RETURNS ret_spUndiscloseVersion AS
$BODY$
	DECLARE
		lRes ret_spUndiscloseVersion;
		lUndisclosedUid int;
		lUid int;
		lVersionCreateDate timestamp;
		lDocumentId bigint;
	BEGIN		
		SELECT INTO lDocumentId, lUid, lVersionCreateDate
			document_id, uid, createdate
		FROM pjs.document_versions
		WHERE id = pVersionId;
		
		IF lDocumentId IS NULL THEN
			RETURN lRes;
		END IF;
		
		SELECT INTO lUndisclosedUid
			id 
		FROM pjs.spGetUndisclosedUid(lDocumentId, pRoleId, lUid);
		
		UPDATE pjs.document_versions SET
			is_disclosed = false,
			undisclosed_usr_id = lUndisclosedUid
		WHERE id = pVersionId;
		
		UPDATE pjs.msg SET
			is_disclosed = false,
			undisclosed_usr_id = lUndisclosedUid
		WHERE version_id = pVersionId AND usr_id = lUid AND id = original_id AND mdate > lVersionCreateDate;
		
		lRes.result = TRUE;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs.spUndiscloseVersion(
	pVersionId bigint,
	pRoleId bigint
) TO iusrpmt;
