DROP TYPE ret_spSyncTaxonTreatmentFamilyName CASCADE;
CREATE TYPE ret_spSyncTaxonTreatmentFamilyName AS (
	result int
);

CREATE OR REPLACE FUNCTION spSyncTaxonTreatmentFamilyName(
	pTaxonNameInstanceId bigint, -- InstanceId на таксон нейма
	pUid int
)
  RETURNS ret_spSyncTaxonTreatmentFamilyName AS
$BODY$
	DECLARE
		lRes ret_spSyncTaxonTreatmentFamilyName;	
		
		lTreatmentObjectId bigint;
		lTreatmentInstanceId bigint;
		
		lFamilyFieldId bigint;
		
		
		lName varchar;
		lTempStr varchar;
	BEGIN
		lFamilyFieldId = 241;
		
		lTreatmentObjectId = 41;
		
		-- Взимаме името - полето family
		SELECT INTO lTempStr value_str FROM pwt.instance_field_values WHERE instance_id = pTaxonNameInstanceId AND field_id = lFamilyFieldId;
		
		lName = coalesce(lTempStr, '');
				
		
		-- Взимаме ид-то на treatment-a
		SELECT INTO lTreatmentInstanceId i.id 
		FROM pwt.document_object_instances i
		JOIN pwt.document_object_instances n ON n.document_id = i.document_id AND i.pos = substring(n.pos, 1, char_length(i.pos))
		WHERE i.object_id = lTreatmentObjectId AND n.id = pTaxonNameInstanceId;
		
		-- Ъпдейтваме името му
		UPDATE pwt.document_object_instances SET
			display_name = lName 
		WHERE id = lTreatmentInstanceId;
		
		
		lRes.result = 1;		

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSyncTaxonTreatmentFamilyName(	
	pTaxonNameInstanceId bigint,
	pUid int
) TO iusrpmt;
