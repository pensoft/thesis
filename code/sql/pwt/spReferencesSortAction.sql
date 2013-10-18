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
		lReferenceIsConfirmed boolean;
		lReorderingChangeHasOcurred int;
	BEGIN
		
		SELECT INTO lDocumentId, lReferenceIsConfirmed
			document_id,is_confirmed
		FROM pwt.document_object_instances
		WHERE id = pInstanceId;
		
		SELECT INTO lReorderingChangeHasOcurred
			result
		FROM spCacheReferenceFields(pInstanceId);
		
		IF lReferenceIsConfirmed = true AND lReorderingChangeHasOcurred = 1 THEN
			PERFORM spSaveReferenceOrder(lDocumentId);
		END IF;
		lRes.result = 1;
		RETURN lRes;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spReferencesSortAction(
	pInstanceId bigint,
	pUid int
) TO iusrpmt;
