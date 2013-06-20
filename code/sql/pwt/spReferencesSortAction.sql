DROP TYPE ret_spReferencesSortAction CASCADE;

CREATE TYPE ret_spReferencesSortAction AS (
	result int
);

CREATE OR REPLACE FUNCTION spReferencesSortAction(
	pInstanceId bigint,
	pUid int
)
  RETURNS ret_spReferencesSortAction AS
$BODY$
	DECLARE
		lRes ret_spReferencesSortAction;		
		lDocumentId bigint;
	BEGIN
		
		SELECT INTO lDocumentId document_id
		FROM pwt.document_object_instances
		WHERE id = pInstanceId;
		
		PERFORM spSaveReferenceOrder(lDocumentId);
		lRes.result = 1;
		RETURN lRes;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spReferencesSortAction(
	pInstanceId bigint,
	pUid int
) TO iusrpmt;
