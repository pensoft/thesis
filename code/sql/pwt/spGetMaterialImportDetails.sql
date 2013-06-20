DROP TYPE IF EXISTS pwt.ret_spGetMaterialImportDetails CASCADE;

CREATE TYPE pwt.ret_spGetMaterialImportDetails AS (
	status_type int,
	habitat_type int
);

CREATE OR REPLACE FUNCTION pwt.spGetMaterialImportDetails(
	pParentInstanceId bigint
)
  RETURNS pwt.ret_spGetMaterialImportDetails AS
$BODY$
	DECLARE
		lRes pwt.ret_spGetMaterialImportDetails;		
		lRecord record;
		lParentTreatmentInstanceId bigint;
		lTreatmentObjectId bigint = 41;
		lChecklistObjectId bigint = 204;
		lChecklistStatusType int = 1;
		lChecklistHabitatType int = 1;
	BEGIN		
		SELECT INTO lParentTreatmentInstanceId 
			p.object_id
		FROM pwt.document_object_instances i 
		JOIN pwt.document_object_instances p ON p.document_id = i.document_id AND p.object_id = lTreatmentObjectId 
			AND p.pos = substring(i.pos, 1, char_length(p.pos))
		WHERE i.id = pParentInstanceId;
				
		IF lParentTreatmentInstanceId IS NOT NULL THEN
			SELECT INTO lRes.status_type, lRes.habitat_type
				status_type, habitat_type 
			FROM spGetTTDetails(pParentInstanceId);
		ELSE 
			lRes.status_type = lChecklistStatusType;
			lRes.habitat_type = lChecklistHabitatType;
		END IF;		
		
		RETURN lRes;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pwt.spGetMaterialImportDetails(
	pParentInstanceId bigint
) TO iusrpmt;

--select * from spGetMaterialImportDetails(89369);