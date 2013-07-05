-- Function: spperformttmaftercreation(bigint, integer)

-- DROP FUNCTION spperformttmaftercreation(bigint, integer);

DROP TYPE ret_spPerformTTMAfterCreation CASCADE;
CREATE TYPE ret_spPerformTTMAfterCreation AS (
	result int
);

CREATE OR REPLACE FUNCTION spperformttmaftercreation(pmaterialinstanceid bigint, puid integer)
  RETURNS ret_spperformttmaftercreation AS
$BODY$
DECLARE
	lRes ret_spPerformTTMAfterCreation;
	lTreatmentObjectId bigint;
	lTreatmentInstanceId bigint;

	lChecklistInstanceId bigint;
	lChecklistObjectId bigint := 204;
	lLocalityInstanceId bigint;
	lLocalityObjectId bigint := 211;

	lRankFieldId bigint;
	lRankFieldValue int;
	lNomenclatureFieldId bigint := 41;
	lNomenclatureRootFieldId bigint;
	lNomenclatureRootFieldValue int;
	lTreatmentTypeFieldId bigint;
	lTreatmentTypeFieldValue int;
	lMaterialTypeFieldId bigint;

	lNewTaxonTreatmentType int;
	lSpeciesTreatmentRank int;
	lPhytokeysNomenclatureRoots int[];
	lPhytoKeysMaterialTypeSrcId bigint;

	lICZNMaterialTypeSrcId bigint := 48;
	lICNMaterialTypeSrcId bigint := 49;
	lChecklistNomenclaturalCode int;
	lChecklistNomenclaturalCodeICZN int = 1;
	lChecklistNomenclaturalCodeICN int = 2;
	lDataSrcId int;
	lTreatmentCustomDataSrcRuleId int = 1;
BEGIN 
	lTreatmentObjectId = 41;
	lRankFieldId = 42;
	lTreatmentTypeFieldId = 43;
	lMaterialTypeFieldId = 209;
	lNomenclatureRootFieldId  = 384;

	lNewTaxonTreatmentType = 1;
	lSpeciesTreatmentRank = 1;
	lPhytokeysNomenclatureRoots = ARRAY[6, 7, 364];

	lPhytoKeysMaterialTypeSrcId = 36;
 
	SELECT INTO lTreatmentInstanceId p.id
	FROM pwt.document_object_instances i
	JOIN pwt.document_object_instances p ON p.document_id = i.document_id
		AND p.object_id = lTreatmentObjectId AND substring(i.pos, 1, char_length(p.pos)) = p.pos
	WHERE i.id = pMaterialInstanceId;


	SELECT INTO lChecklistInstanceId p.id
	FROM pwt.document_object_instances i
	JOIN pwt.document_object_instances p ON p.document_id = i.document_id
		AND p.object_id = lChecklistObjectId AND substring(i.pos, 1, char_length(p.pos)) = p.pos
	WHERE i.id = pMaterialInstanceId;

	SELECT INTO lLocalityInstanceId p.id
	FROM pwt.document_object_instances i
	JOIN pwt.document_object_instances p ON p.document_id = i.document_id
		AND p.object_id = lLocalityObjectId AND substring(i.pos, 1, char_length(p.pos)) = p.pos
	WHERE i.id = pMaterialInstanceId;

	

	IF coalesce(lTreatmentInstanceId, 0) = 0 AND coalesce(lChecklistInstanceId, 0) = 0 AND coalesce(lLocalityInstanceId, 0) = 0  THEN
		RETURN lRes;
	END IF;
 
	IF coalesce(lTreatmentInstanceId, 0) <> 0 THEN -- Treatment case
		SELECT INTO lNomenclatureRootFieldValue value_int
		FROM pwt.instance_field_values 
		WHERE instance_id = lTreatmentInstanceId AND field_id = lNomenclatureRootFieldId;
		
		SELECT INTO lChecklistNomenclaturalCode
			"nomenclaturalCode" 
		FROM public.taxon_categories c
		WHERE c.id = lNomenclatureRootFieldValue;
		
		SELECT INTO lRankFieldValue
			value_int
		FROM pwt.instance_field_values 
		WHERE instance_id = lTreatmentInstanceId AND field_id = lRankFieldId; 
		
		SELECT INTO lTreatmentTypeFieldValue
			value_int
		FROM pwt.instance_field_values 
		WHERE instance_id = lTreatmentInstanceId AND field_id = lTreatmentTypeFieldId;
		
		SELECT INTO lDataSrcId
			result
		FROM spPerformCustomDataSrcRule(lTreatmentCustomDataSrcRuleId, ARRAY[lChecklistNomenclaturalCode, lTreatmentTypeFieldValue, lRankFieldValue]::int[])	;
	ELSE -- Checklist taxon case
		SELECT INTO lChecklistNomenclaturalCode
		"nomenclaturalCode" 
		FROM public.taxon_categories c
		JOIN pwt.instance_field_values i ON c.id = ANY(i.value_arr_int)
		WHERE i.instance_id in  (lChecklistInstanceId, lLocalityInstanceId) AND i.field_id = lNomenclatureFieldId;

		IF lChecklistNomenclaturalCode = lChecklistNomenclaturalCodeICZN THEN
			lDataSrcId = lICZNMaterialTypeSrcId;
		ELSEIF lChecklistNomenclaturalCode = lChecklistNomenclaturalCodeICN THEN
			lDataSrcId = lICNMaterialTypeSrcId;
		END IF;		
	END IF;
	IF lDataSrcId IS NOT NULL THEN
		UPDATE pwt.instance_field_values  SET
			data_src_id = lDataSrcId
		WHERE instance_id = pMaterialInstanceId AND field_id = lMaterialTypeFieldId;
	END IF;

	lRes.result = 1;
	RETURN lRes;
 
END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spperformttmaftercreation(bigint, integer)
  OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spperformttmaftercreation(bigint, integer) TO public;
GRANT EXECUTE ON FUNCTION spperformttmaftercreation(bigint, integer) TO postgres;
GRANT EXECUTE ON FUNCTION spperformttmaftercreation(bigint, integer) TO iusrpmt;
