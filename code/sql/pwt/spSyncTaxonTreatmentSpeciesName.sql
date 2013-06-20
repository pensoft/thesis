DROP TYPE ret_spSyncTaxonTreatmentSpeciesName CASCADE;
CREATE TYPE ret_spSyncTaxonTreatmentSpeciesName AS (
	result int
);

CREATE OR REPLACE FUNCTION spSyncTaxonTreatmentSpeciesName(
	pTaxonNameInstanceId bigint, -- InstanceId на таксон нейма
	pUid int
)
  RETURNS ret_spSyncTaxonTreatmentSpeciesName AS
$BODY$
	DECLARE
		lRes ret_spSyncTaxonTreatmentSpeciesName;	
		
		lTreatmentObjectId bigint;
		lTreatmentInstanceId bigint;
		
		lGenusNameFieldId bigint;
		lSpeciesNameFieldId bigint;
		
		lName varchar;
		lTempStr varchar;
	BEGIN
		lGenusNameFieldId = 48;
		lSpeciesNameFieldId = 49;
		lTreatmentObjectId = 41;
		
		-- Взимаме името - конкатенация на полетата genus и species
		SELECT INTO lTempStr value_str FROM pwt.instance_field_values WHERE instance_id = pTaxonNameInstanceId AND field_id = lGenusNameFieldId;
		
		lName = coalesce(lTempStr, '');
		
		SELECT INTO lTempStr value_str FROM pwt.instance_field_values WHERE instance_id = pTaxonNameInstanceId AND field_id = lSpeciesNameFieldId;
		
		IF char_length(lName) > 0 THEN
			lName = lName || ' ' || coalesce(lTempStr, '');
		ELSE
			lName = coalesce(lTempStr, '');
		END IF;
		
		-- Взимаме ид-то на treatment-a
		SELECT INTO lTreatmentInstanceId g.id 
		FROM pwt.document_object_instances i
		JOIN pwt.document_object_instances p ON p.id = i.parent_id
		JOIN pwt.document_object_instances g ON g.id = p.parent_id
		WHERE g.object_id = lTreatmentObjectId AND i.id = pTaxonNameInstanceId;
		
		-- RAISE NOTICE 'Tr %', lTreatmentInstanceId;
		
		-- Ъпдейтваме името му
		IF lTreatmentInstanceId IS NOT NULL THEN
			UPDATE pwt.document_object_instances SET
				display_name = lName 
			WHERE id = lTreatmentInstanceId;
		END IF;
		
		lRes.result = 1;		

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSyncTaxonTreatmentSpeciesName(	
	pTaxonNameInstanceId bigint,
	pUid int
) TO iusrpmt;
