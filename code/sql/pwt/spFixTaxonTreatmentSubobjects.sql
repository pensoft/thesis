DROP TYPE ret_spFixTaxonTreatmentSubobjects CASCADE;
CREATE TYPE ret_spFixTaxonTreatmentSubobjects AS (
	result int
);

CREATE OR REPLACE FUNCTION spFixTaxonTreatmentSubobjects(
	pInstanceId bigint,
	pUid int
)
  RETURNS ret_spFixTaxonTreatmentSubobjects AS
$BODY$
	DECLARE
		lRes ret_spFixTaxonTreatmentSubobjects;	
		lTaxonRankFieldId bigint;
		lTaxonStatusTypeFieldId bigint;
		lTaxonHabitatFieldId bigint;
		
		lTaxonNameFieldsWrapperObjectId bigint;
		lTTMMaterialWrapperObjectId bigint;
		
		lTaxonTreatmentNameCustomCreationId bigint;
		lTaxonTreatmentMaterialCustomCreationId bigint;
		
		lObjectId bigint;
		lRecord record;
		
		lTaxonRankValue int;
		lTaxonStatusTypeValue int;
		lTaxonHabitatValue int;
		
		lTTMPriorityDCCustomCreationId bigint;
		lTTMExtendedDCCustomCreationId bigint;
		
		lTTMPriorityDCObjectId bigint;
		lTTMExtendedDCObjectId bigint;		
	BEGIN		
		lTaxonRankFieldId = 42;
		lTaxonStatusTypeFieldId = 44;
		lTaxonHabitatFieldId = 45;
		
		lTaxonNameFieldsWrapperObjectId = 45;
		lTTMMaterialWrapperObjectId = 44;
		
		lTaxonTreatmentNameCustomCreationId = 1;
		lTaxonTreatmentMaterialCustomCreationId = 2;
		
		lTTMPriorityDCCustomCreationId = 5;
		lTTMExtendedDCCustomCreationId = 6;
		
		lTTMPriorityDCObjectId = 83;
		lTTMExtendedDCObjectId = 84;
		
		
		-- Взимаме стойностите, които ни трябват 
		SELECT INTO lTaxonRankValue value_int
		FROM pwt.instance_field_values 
		WHERE instance_id = pInstanceId AND field_id = lTaxonRankFieldId;
		
		SELECT INTO lTaxonStatusTypeValue value_int
		FROM pwt.instance_field_values 
		WHERE instance_id = pInstanceId AND field_id = lTaxonStatusTypeFieldId;
		
		SELECT INTO lTaxonHabitatValue value_int
		FROM pwt.instance_field_values 
		WHERE instance_id = pInstanceId AND field_id = lTaxonHabitatFieldId;
		
		-- Първо оправяме подобекта на името на таксона
		SELECT INTO lObjectId result FROM spGetCustomCreateObject(lTaxonTreatmentNameCustomCreationId, ARRAY[lTaxonRankValue]);
		FOR lRecord IN 
			SELECT i.id 
			FROM pwt.document_object_instances i
			JOIN pwt.document_object_instances p ON p.document_id = i.document_id AND p.id = pInstanceId
				AND substring(i.pos, 1, char_length(p.pos)) = p.pos
			WHERE i.object_id = lTaxonNameFieldsWrapperObjectId
		LOOP
			PERFORM spFixWrapperCustomObjectId(lRecord.id, lObjectId, pUid);
		END LOOP;
		
		-- След това оправяме всички материали
		-- Първо коригираме Primary DC fields
		SELECT INTO lObjectId result FROM spGetCustomCreateObject(lTTMPriorityDCCustomCreationId, ARRAY[lTaxonStatusTypeValue, lTaxonHabitatValue]);
		FOR lRecord IN 
			SELECT i.id 
			FROM pwt.document_object_instances i
			JOIN pwt.document_object_instances p ON p.document_id = i.document_id AND p.id = pInstanceId
				AND substring(i.pos, 1, char_length(p.pos)) = p.pos
			WHERE i.object_id = lTTMPriorityDCObjectId
		LOOP
			PERFORM spFixWrapperCustomObjectId(lRecord.id, lObjectId, pUid);
		END LOOP;
		
		-- След това коригираме Extended DC fields
		SELECT INTO lObjectId result FROM spGetCustomCreateObject(lTTMExtendedDCCustomCreationId, ARRAY[lTaxonStatusTypeValue, lTaxonHabitatValue]);
		FOR lRecord IN 
			SELECT i.id 
			FROM pwt.document_object_instances i
			JOIN pwt.document_object_instances p ON p.document_id = i.document_id AND p.id = pInstanceId
				AND substring(i.pos, 1, char_length(p.pos)) = p.pos
			WHERE i.object_id = lTTMExtendedDCObjectId
		LOOP
			PERFORM spFixWrapperCustomObjectId(lRecord.id, lObjectId, pUid);
		END LOOP;
				
		
		lRes.result = 1;

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spFixTaxonTreatmentSubobjects(	
	pInstanceId bigint,
	pUid int
) TO iusrpmt;
