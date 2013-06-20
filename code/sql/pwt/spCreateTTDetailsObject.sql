DROP TYPE ret_spCreateTTDetailsObject CASCADE;
CREATE TYPE ret_spCreateTTDetailsObject AS (
	result int
);

CREATE OR REPLACE FUNCTION spCreateTTDetailsObject(
	pInstanceId bigint, -- id на wrapper-a
	pUid int
)
  RETURNS ret_spCreateTTDetailsObject AS
$BODY$
	DECLARE
		lRes ret_spCreateTTDetailsObject;	
		lTaxonTreatmentObjectId bigint;
		lTaxonTreatmentInstanceId bigint;
		lObjectId bigint;
		
		lTaxonTreatmentTypeFieldId bigint;
		lTaxonTreatmentTypeValue int;
		
		lObjectCustomCreationId bigint;
		lRecord record;
	BEGIN
		lTaxonTreatmentObjectId = 41;
		lTaxonTreatmentTypeFieldId = 43;
		lObjectCustomCreationId = 4;
		
		-- Трябва да разберем точно кой тип име да създадем
		-- За целта трябва да вземем полетo за rank на синонима
		SELECT INTO lTaxonTreatmentInstanceId p.id 
			FROM pwt.document_object_instances i
			JOIN pwt.document_object_instances p ON
				p.document_id = i.document_id AND char_length(p.pos) < char_length(i.pos) AND 
				p.pos = substring(i.pos, 1, char_length(p.pos))				
			WHERE i.id = pInstanceId AND p.object_id = lTaxonTreatmentObjectId;
		
		SELECT INTO lTaxonTreatmentTypeValue value_int
		FROM pwt.instance_field_values 
		WHERE instance_id = lTaxonTreatmentInstanceId AND field_id = lTaxonTreatmentTypeFieldId;
		
		SELECT INTO lRecord * FROM pwt.document_object_instances i WHERE id = pInstanceId;
		
		SELECT INTO lObjectId result FROM spGetCustomCreateObject(lObjectCustomCreationId, ARRAY[lTaxonTreatmentTypeValue]);
		
		-- RAISE NOTICE 'ObjectId %, WrapperId % Rank %, WrapperObjectId %, WrapperTDO %', lObjectId, pInstanceId, lTaxonTreatmentTypeValue, lRecord.object_id, lRecord.document_template_object_id;
		
		IF coalesce(lObjectId, 0) <> 0 THEN
			 PERFORM spCreateNewInstance(pInstanceId, lObjectId, pUid);
		END IF;
		
		
		lRes.result = 1;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCreateTTDetailsObject(	
	pInstanceId bigint,
	pUid int
) TO iusrpmt;
