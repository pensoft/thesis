DROP TYPE ret_spCreateTTNSubseqCitationNameObject CASCADE;
CREATE TYPE ret_spCreateTTNSubseqCitationNameObject AS (
	result int
);

CREATE OR REPLACE FUNCTION spCreateTTNSubseqCitationNameObject(
	pInstanceId bigint, -- InstanceId на wrapper-a
	pUid int
)
  RETURNS ret_spCreateTTNSubseqCitationNameObject AS
$BODY$
	DECLARE
		lRes ret_spCreateTTNSubseqCitationNameObject;	
		
		
		lCreationRuleId bigint;
		
		lSynonymRankFieldId bigint;
		
		lSynonymRankValue int;
		
		lObjectId bigint;
		
		lNomenclatureObjectId bigint;
		lNomenclatureInstanceId bigint;
		
	BEGIN
		lSynonymRankFieldId = 239;
		lNomenclatureObjectId = 68;
		
		lCreationRuleId = 3;
		
		
		
		
		
		-- Трябва да разберем точно кой тип име да създадем
		-- За целта трябва да вземем полетo за rank на синонима
		SELECT INTO lNomenclatureInstanceId p.id 
			FROM pwt.document_object_instances i
			JOIN pwt.document_object_instances p ON
				p.document_id = i.document_id AND char_length(p.pos) < char_length(i.pos) AND 
				p.pos = substring(i.pos, 1, char_length(p.pos))				
			WHERE i.id = pInstanceId AND p.object_id = lNomenclatureObjectId;
		
		SELECT INTO lSynonymRankValue value_int
		FROM pwt.instance_field_values 
		WHERE instance_id = lNomenclatureInstanceId AND field_id = lSynonymRankFieldId;
		
		SELECT INTO lObjectId result FROM spGetCustomCreateObject(lCreationRuleId, ARRAY[lSynonymRankValue]);
		
		IF coalesce(lObjectId, 0) <> 0 THEN
			 PERFORM spCreateNewInstance(pInstanceId, lObjectId, pUid);
		END IF;
		
		
		lRes.result = 1;

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCreateTTNSubseqCitationNameObject(	
	pInstanceId bigint,
	pUid int
) TO iusrpmt;
