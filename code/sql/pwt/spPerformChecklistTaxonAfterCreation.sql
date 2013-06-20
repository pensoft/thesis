DROP TYPE ret_spPerformChecklistTaxonAfterCreation CASCADE;
CREATE TYPE ret_spPerformChecklistTaxonAfterCreation AS (
	result int
);

CREATE OR REPLACE FUNCTION spPerformChecklistTaxonAfterCreation(
 pTaxonInstanceId bigint,
 pUid int
)
  RETURNS ret_spPerformChecklistTaxonAfterCreation AS
$BODY$
DECLARE
	lRes ret_spPerformChecklistTaxonAfterCreation;
	lChecklistInstanceId bigint;
	lChecklistObjectId bigint := 204;

	lNomenclatureFieldId bigint := 41;
	lNomenclatureRootFieldValue int;
 
BEGIN 
	SELECT INTO lChecklistInstanceId p.id
	FROM pwt.document_object_instances i
	JOIN pwt.document_object_instances p ON p.document_id = i.document_id
		AND p.object_id = lChecklistObjectId AND substring(i.pos, 1, char_length(p.pos)) = p.pos
	WHERE i.id = pTaxonInstanceId;
 
	IF coalesce(lChecklistInstanceId, 0) = 0 THEN
		RETURN lRes;
	END IF;
 
	SELECT INTO lNomenclatureRootFieldValue 
		CASE WHEN rootnode = 0 THEN id ELSE rootnode END
	FROM public.taxon_categories c
	JOIN pwt.instance_field_values i ON c.id = ANY(i.value_arr_int)
	WHERE i.instance_id = lChecklistInstanceId AND i.field_id = lNomenclatureFieldId;
	
	IF coalesce(lNomenclatureRootFieldValue, 0) = 0 THEN
		RAISE EXCEPTION USING MESSAGE = 'pjs.youHaveToSelectChecklistNomenclatureFirst';
	END IF;

	lRes.result = 1;
	RETURN lRes;
 
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spPerformChecklistTaxonAfterCreation(
	pTaxonInstanceId bigint,
	pUid int
) TO iusrpmt;
