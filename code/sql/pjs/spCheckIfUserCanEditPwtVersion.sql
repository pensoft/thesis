CREATE OR REPLACE FUNCTION spCheckIfUserCanEditPwtVersion(
	pVersionId bigint,		
	pUid int
)
  RETURNS boolean AS
$BODY$
	DECLARE		
	BEGIN		
		
		IF NOT EXISTS(
			SELECT pv.id
			FROM pjs.pwt_document_versions pv
			JOIN pjs.document_versions v ON v.id = pv.version_id 
			AND v.id = pVersionId AND v.uid = pUid
		) THEN
			RETURN false;
		ELSE
			RETURN true;
		END IF;
		RETURN false;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCheckIfUserCanEditPwtVersion(
	pVersionId bigint,	
	pUid int
) TO iusrpmt;
