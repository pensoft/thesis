DROP TYPE ret_spPerformTTExternalLinkAfterCreation CASCADE;
CREATE TYPE ret_spPerformTTExternalLinkAfterCreation AS (
	result int
);

CREATE OR REPLACE FUNCTION spPerformTTExternalLinkAfterCreation(
	pExternalLinkInstanceId bigint,
	pUid int
)
  RETURNS ret_spPerformTTExternalLinkAfterCreation AS
$BODY$
DECLARE
	lRes ret_spPerformTTExternalLinkAfterCreation;
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
	lLinkTypeFieldId bigint;
	
	lNewTaxonTreatmentType int;
	lSpeciesTreatmentRank int;
	lPhytokeysNomenclatureRoots int[];
	lPhytoKeysExternalLinkTypeSrcId bigint;
BEGIN	
	lTreatmentObjectId = 41;
	lRankFieldId = 42;
	lTreatmentTypeFieldId = 43;
	lLinkTypeFieldId = 52;
	lNomenclatureRootFieldId  = 384;
	
	lNewTaxonTreatmentType = 1;
	lSpeciesTreatmentRank = 1;
	lPhytokeysNomenclatureRoots = ARRAY[6, 7, 364];
	
	lPhytoKeysExternalLinkTypeSrcId = 37;
	
	SELECT INTO lTreatmentInstanceId p.id
	FROM pwt.document_object_instances i
	JOIN pwt.document_object_instances p ON p.document_id = i.document_id
		AND p.object_id = lTreatmentObjectId AND substring(i.pos, 1, char_length(p.pos)) = p.pos
	WHERE i.id = pExternalLinkInstanceId;
	
	SELECT INTO lChecklistInstanceId p.id
	FROM pwt.document_object_instances i
	JOIN pwt.document_object_instances p ON p.document_id = i.document_id
		AND p.object_id = lChecklistObjectId AND substring(i.pos, 1, char_length(p.pos)) = p.pos
	WHERE i.id = pExternalLinkInstanceId;

	SELECT INTO lLocalityInstanceId p.id
	FROM pwt.document_object_instances i
	JOIN pwt.document_object_instances p ON p.document_id = i.document_id
		AND p.object_id = lLocalityObjectId AND substring(i.pos, 1, char_length(p.pos)) = p.pos
	WHERE i.id = pExternalLinkInstanceId;
	
	IF coalesce(lTreatmentInstanceId, 0) = 0 AND coalesce(lChecklistInstanceId, 0) = 0 AND coalesce(lLocalityInstanceId, 0) = 0 THEN
		RETURN lRes;
	END IF;
	
	IF coalesce(lTreatmentInstanceId, 0) > 0 THEN -- Treatment
		
		SELECT INTO lNomenclatureRootFieldValue value_int
		FROM pwt.instance_field_values 
		WHERE instance_id = lTreatmentInstanceId AND field_id = lNomenclatureRootFieldId;
	ELSE -- Checklist
		SELECT INTO lNomenclatureRootFieldValue 
			CASE WHEN rootnode = 0 THEN id ELSE rootnode END
		FROM public.taxon_categories c
		JOIN pwt.instance_field_values i ON c.id = ANY(i.value_arr_int)
		WHERE i.instance_id in (lChecklistInstanceId, lLocalityInstanceId) AND i.field_id = lNomenclatureFieldId;
	END IF;
	
	/**
		Ще сменим възможните стойности на полето за тип на материал
		в зависимост от това какъв тип е триитмънта
	*/
	IF lNomenclatureRootFieldValue = ANY(lPhytokeysNomenclatureRoots) THEN
		UPDATE pwt.instance_field_values  SET
			data_src_id = lPhytoKeysExternalLinkTypeSrcId
		WHERE instance_id = pExternalLinkInstanceId AND field_id = lLinkTypeFieldId;
	END IF;
	
	lRes.result = 1;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spPerformTTExternalLinkAfterCreation(
	pExternalLinkInstanceId bigint,
	pUid int
) TO iusrpmt;
