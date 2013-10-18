DROP TYPE IF EXISTS ret_spReferenceAfterConfirm CASCADE;

CREATE TYPE ret_spReferenceAfterConfirm AS (
	result int
);

CREATE OR REPLACE FUNCTION spReferenceAfterConfirm(
	pInstanceId bigint,
	pUid int
)
  RETURNS ret_spReferenceAfterConfirm AS
$BODY$
	DECLARE
		lRes ret_spReferenceAfterConfirm;		
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

GRANT EXECUTE ON FUNCTION spReferenceAfterConfirm(
	pInstanceId bigint,
	pUid int
) TO iusrpmt;
