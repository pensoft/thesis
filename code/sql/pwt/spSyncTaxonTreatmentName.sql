DROP TYPE ret_spSyncTaxonTreatmentName CASCADE;
CREATE TYPE ret_spSyncTaxonTreatmentName AS (
	result int
);

CREATE OR REPLACE FUNCTION spSyncTaxonTreatmentName(
	pTaxonNameInstanceId bigint, -- InstanceId на таксон нейма
	pUid int
)
  RETURNS ret_spSyncTaxonTreatmentName AS
$BODY$
	DECLARE
		lRes ret_spSyncTaxonTreatmentName;	
		
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

GRANT EXECUTE ON FUNCTION spSyncTaxonTreatmentName(	
	pTaxonNameInstanceId bigint,
	pUid int
) TO iusrpmt;
