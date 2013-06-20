DROP TYPE ret_spGenerateTaxonTreatmentSubTree CASCADE;
CREATE TYPE ret_spGenerateTaxonTreatmentSubTree AS (
	result int
);

CREATE OR REPLACE FUNCTION spGenerateTaxonTreatmentSubTree(
	pInstanceId bigint,
	pUid int
)
  RETURNS ret_spGenerateTaxonTreatmentSubTree AS
$BODY$
	DECLARE
		lRes ret_spGenerateTaxonTreatmentSubTree;	
		lTaxonNameObjectId bigint;
		lTaxonClassificationFieldId bigint;
		lDocTemplateObjectId bigint;
		lRecord record;
		lInstanceId bigint;
		lObjectId bigint;
		lTaxonRankFieldId bigint;
		lClassificationFieldId bigint;
		lRootClassificationFieldId bigint;
		lTaxonTreatmentTypeFieldId bigint;
		lTaxonRankValue int;
		lClassificationValue int;
		lTaxonNameFieldsWrapperInstanceId bigint;
		lTaxonNameFieldsWrapperObjectId bigint;
		
		lTaxonTreatmentNameCustomCreationId bigint;
		lTaxonTreatmentMainSubobjectCustomCreationRuleId bigint;
		
		lRootClassificationFieldValue int;
		lRankFieldValue int;
		lTreatmentTypeFieldValue int;
		lClassificationNomenclaturalCode int;
	BEGIN
		lTaxonNameObjectId = 42;
		lTaxonRankFieldId = 42;
		lTaxonClassificationFieldId = 41;
		lTaxonTreatmentTypeFieldId = 43;
		lClassificationFieldId = 41;
		lTaxonTreatmentNameCustomCreationId = 1;
		lTaxonNameFieldsWrapperObjectId = 45;
		lRootClassificationFieldId = 384;
		lTaxonTreatmentMainSubobjectCustomCreationRuleId = 10;
		
		SELECT INTO lTaxonRankValue value_int
		FROM pwt.instance_field_values 
		WHERE instance_id = pInstanceId AND field_id = lTaxonRankFieldId;
		
		SELECT INTO lClassificationValue value_arr_int[1]
		FROM pwt.instance_field_values 
		WHERE instance_id = pInstanceId AND field_id = lClassificationFieldId;
		
		--Сетваме полето за root-a на класификацията
		UPDATE pwt.instance_field_values fv SET
			value_int = CASE WHEN coalesce(c.rootnode, 0) = 0 THEN c.id ELSE c.rootnode END
		FROM taxon_categories c 
		WHERE fv.instance_id = pInstanceId AND fv.field_id = lRootClassificationFieldId  AND c.id = lClassificationValue;
		
		SELECT INTO lClassificationNomenclaturalCode
			"nomenclaturalCode"
		FROM taxon_categories c 
		WHERE c.id = lClassificationValue;
		
		--Сетваме полетата Rank, type of treatment и classification да станат readonly
		UPDATE pwt.instance_field_values SET
			is_read_only = true
		WHERE instance_id = pInstanceId AND (field_id = lTaxonRankFieldId OR field_id = lTaxonTreatmentTypeFieldId OR field_id = lTaxonClassificationFieldId);
		
		SELECT INTO lDocTemplateObjectId document_template_object_id 
		FROM pwt.document_object_instances 
		WHERE id = pInstanceId;
		
		
		SELECT INTO lRankFieldValue value_int
		FROM pwt.instance_field_values 
		WHERE instance_id = pInstanceId AND field_id = lTaxonRankFieldId;
		
		SELECT INTO lRootClassificationFieldValue value_int
		FROM pwt.instance_field_values 
		WHERE instance_id = pInstanceId AND field_id = lRootClassificationFieldId;
		
		
		SELECT INTO lTreatmentTypeFieldValue value_int
		FROM pwt.instance_field_values 
		WHERE instance_id = pInstanceId AND field_id = lTaxonTreatmentTypeFieldId;
		
		-- Добавяме правилния главен подобект
		SELECT INTO lObjectId result FROM spGetCustomCreateObject(lTaxonTreatmentMainSubobjectCustomCreationRuleId, ARRAY[lRankFieldValue, lClassificationNomenclaturalCode, lTreatmentTypeFieldValue]);
		IF lObjectId IS NOT NULL THEN
			PERFORM spCreateNewInstance(pInstanceId, lObjectId, pUid);
		END IF;
		/*
		-- Добавяме всички недобавени подобекти
		FOR lRecord IN 
			SELECT * FROM pwt.document_template_objects WHERE parent_id = lDocTemplateObjectId AND is_fake = true
			ORDER BY pos ASC
		LOOP
			SELECT INTO lInstanceId new_instance_id FROM spCreateNewInstance(pInstanceId, lRecord.object_id, pUid);			
		END LOOP;
		*/
		lRes.result = 1;

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGenerateTaxonTreatmentSubTree(	
	pInstanceId bigint,
	pUid int
) TO iusrpmt;
