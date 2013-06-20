DROP TYPE ret_spGenerateTTMWrapperSubTree CASCADE;
CREATE TYPE ret_spGenerateTTMWrapperSubTree AS (
	result int
);

CREATE OR REPLACE FUNCTION spGenerateTTMWrapperSubTree(
	pInstanceId bigint, -- InstanceId на материала
	pUid int
)
  RETURNS ret_spGenerateTTMWrapperSubTree AS
$BODY$
	DECLARE
		lRes ret_spGenerateTTMWrapperSubTree;	
		
		
		lTTMPriorityDCCustomCreationId bigint;
		lTTMPriorityDCObjectId bigint;
		lTTMPriorityDCInstanceId bigint;
		lTTMPriorityDCPos varchar;
		
		lTTMExtendedDCCustomCreationId bigint;
		lTTMExtendedDCObjectId bigint;
		lTTMExtendedDCInstanceId bigint;
		lTTMExtendedDCPos varchar;
		
		
		lTaxonTreatmentObjectId bigint;
		lTaxonTreatmentInstanceId bigint;
		
		lTaxonStatusTypeFieldId bigint;
		lTaxonHabitatFieldId bigint;
		lDocumentId int;
		
		lTaxonStatusTypeValue int;
		lTaxonHabitatValue int;
		lObjectId bigint;
		
		lFileUploadObjectId bigint;
		lFileUploadInstanceId bigint;
		lChecklistInstanceId bigint;
		lChecklistObjectId bigint := 204;
		
		lStatusTypeExtantId int = 1;
		lHabitatTerrestrialId int = 1;
		
	BEGIN
		lTTMPriorityDCObjectId = 83;
		lTTMExtendedDCObjectId = 84;
		lTaxonTreatmentObjectId = 41;
		lFileUploadObjectId = 46;
		
		lTaxonStatusTypeFieldId = 44;
		lTaxonHabitatFieldId = 45;
		
		
		lTTMPriorityDCCustomCreationId = 5;
		lTTMExtendedDCCustomCreationId = 6;
		
		
		--Махаме file upload обекта
		SELECT INTO lFileUploadInstanceId id FROM pwt.document_object_instances WHERE parent_id = pInstanceId AND object_id = lFileUploadObjectId;
		
		IF lFileUploadInstanceId IS NOT NULL THEN
			PERFORM spRemoveInstance(lFileUploadInstanceId, pUid);
		END IF;
		
		SELECT INTO lTTMPriorityDCInstanceId, lTTMPriorityDCPos, lDocumentId id, pos, document_id 
			FROM pwt.document_object_instances 
			WHERE parent_id = pInstanceId AND object_id = lTTMPriorityDCObjectId;
			
		IF lTTMPriorityDCInstanceId IS NULL THEN -- Трябва да добавим обекта
			PERFORM spCreateNewInstance(pInstanceId, lTTMPriorityDCObjectId, pUid);
			
			SELECT INTO lTTMPriorityDCInstanceId, lTTMPriorityDCPos, lDocumentId id, pos, document_id 
			FROM pwt.document_object_instances 
			WHERE parent_id = pInstanceId AND object_id = lTTMPriorityDCObjectId;
		END IF;
		
			
		SELECT INTO lTTMExtendedDCInstanceId, lTTMExtendedDCPos, lDocumentId id, pos, document_id 
			FROM pwt.document_object_instances 
			WHERE parent_id = pInstanceId AND object_id = lTTMExtendedDCObjectId;
			
		IF lTTMExtendedDCInstanceId IS NULL THEN -- Трябва да добавим обекта
			PERFORM spCreateNewInstance(pInstanceId, lTTMExtendedDCObjectId, pUid);
			
			SELECT INTO lTTMExtendedDCInstanceId, lTTMExtendedDCPos, lDocumentId id, pos, document_id 
			FROM pwt.document_object_instances 
			WHERE parent_id = pInstanceId AND object_id = lTTMExtendedDCObjectId;
		END IF;
		
		-- Трябва да разберем точно кой тип материал да създадем
		-- За целта трябва да вземем полетата за type и habitat от таксон трийтмънта
		SELECT INTO lTaxonTreatmentInstanceId id 
			FROM pwt.document_object_instances
			WHERE document_id = lDocumentId AND char_length(pos) < char_length(lTTMPriorityDCPos)
				AND object_id = lTaxonTreatmentObjectId AND pos = substring(lTTMPriorityDCPos, 1, char_length(pos));
				
		SELECT INTO lChecklistInstanceId id 
			FROM pwt.document_object_instances
			WHERE document_id = lDocumentId AND char_length(pos) < char_length(lTTMPriorityDCPos)
				AND object_id = lChecklistObjectId AND pos = substring(lTTMPriorityDCPos, 1, char_length(pos));
		
		--RAISE NOTICE 'PriorityPos %, TreatmentObjId %, ', lTTMPriorityDCPos, lTaxonTreatmentObjectId;
		
		IF coalesce(lTaxonTreatmentInstanceId, 0) <> 0 THEN -- Treatment					
			SELECT INTO lTaxonStatusTypeValue value_int
			FROM pwt.instance_field_values 
			WHERE instance_id = lTaxonTreatmentInstanceId AND field_id = lTaxonStatusTypeFieldId;
			
			SELECT INTO lTaxonHabitatValue value_int
			FROM pwt.instance_field_values 
			WHERE instance_id = lTaxonTreatmentInstanceId AND field_id = lTaxonHabitatFieldId;
		ELSE	-- Checklist
			lTaxonStatusTypeValue = lStatusTypeExtantId;
			lTaxonHabitatValue = lHabitatTerrestrialId;			
		END IF;

		
		IF NOT EXISTS (SELECT * FROM pwt.document_object_instances WHERE parent_id = lTTMPriorityDCInstanceId) THEN -- Създаваме обект за priorityDarwinCore			
			SELECT INTO lObjectId result FROM spGetCustomCreateObject(lTTMPriorityDCCustomCreationId, ARRAY[lTaxonStatusTypeValue, lTaxonHabitatValue]);
			--RAISE NOTICE 'Rule %, Status %, Habitat %', lTTMPriorityDCCustomCreationId, lTaxonStatusTypeValue, lTaxonHabitatValue;
			--RAISE NOTICE 'InstId %, WrapperInstId %, ', pInstanceId, lTaxonTreatmentInstanceId;
			--RAISE NOTICE 'PriorityInstId %, ObjToCreateId %', lTTMPriorityDCInstanceId, lObjectId;
			IF coalesce(lObjectId, 0) <> 0 THEN
				 PERFORM spCreateNewInstance(lTTMPriorityDCInstanceId, lObjectId, pUid);
			END IF;
		END IF;
		
		IF NOT EXISTS (SELECT * FROM pwt.document_object_instances WHERE parent_id = lTTMExtendedDCInstanceId) THEN -- Създаваме обект за extendedDarwinCore			
			SELECT INTO lObjectId result FROM spGetCustomCreateObject(lTTMExtendedDCCustomCreationId, ARRAY[lTaxonStatusTypeValue, lTaxonHabitatValue]);
			--RAISE NOTICE 'Rule %, Status %, Habitat %', lTTMExtendedDCCustomCreationId, lTaxonStatusTypeValue, lTaxonHabitatValue;
			--RAISE NOTICE 'InstId %, WrapperObId %', pInstanceId, lTaxonTreatmentObjectId;
			--RAISE NOTICE 'ExtendedInstId %, ObjToCreateId %', lTTMExtendedDCInstanceId, lObjectId;
			IF coalesce(lObjectId, 0) <> 0 THEN
				 PERFORM spCreateNewInstance(lTTMExtendedDCInstanceId, lObjectId, pUid);
			END IF;
		END IF;
		
		
		
		
		
		
		
		
		lRes.result = 1;

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGenerateTTMWrapperSubTree(	
	pInstanceId bigint,
	pUid int
) TO iusrpmt;
