DROP TYPE ret_spFixTaxonNomenclatureOriginalCitations CASCADE;
CREATE TYPE ret_spFixTaxonNomenclatureOriginalCitations AS (
	result int,
	citation_wrapper_instances bigint[]
);

CREATE OR REPLACE FUNCTION spFixTaxonNomenclatureOriginalCitations(
	pInstanceId bigint, -- InstanceId на номенкатурата
	pNewRankValue int,
	pUid int
)
  RETURNS ret_spFixTaxonNomenclatureOriginalCitations AS
$BODY$
	DECLARE
		lRes ret_spFixTaxonNomenclatureOriginalCitations;	
		
		
		lCreationRuleId bigint;
		
		lSynonymRankFieldId bigint;
		
		lPreviousRankValue int;
		
		lObjectId bigint;
				
		lResInstancesArr bigint[];
		lCitationWrapperObjectId bigint;
		lRecord record;
		lRecord2 record;
	BEGIN
		lSynonymRankFieldId = 239;	
		lCitationWrapperObjectId = 64;
		
		lCreationRuleId = 3;
	
		
		SELECT INTO lPreviousRankValue value_int
		FROM pwt.instance_field_values 
		WHERE instance_id = pInstanceId AND field_id = lSynonymRankFieldId;
		
		IF lPreviousRankValue <> pNewRankValue THEN
			UPDATE pwt.instance_field_values SET 
				value_int = pNewRankValue
			WHERE instance_id = pInstanceId AND field_id = lSynonymRankFieldId;
			
			SELECT INTO lObjectId result FROM spGetCustomCreateObject(lCreationRuleId, ARRAY[pNewRankValue]);
			
			FOR lRecord IN
				SELECT i.id
				FROM pwt.document_object_instances i
				JOIN pwt.document_object_instances p ON p.document_id = i.document_id AND p.pos = substring(i.pos, 1, char_length(p.pos)) AND p.id = pInstanceId
				WHERE i.object_id = lCitationWrapperObjectId
			LOOP	
				SELECT INTO lRecord2 * FROM pwt.document_object_instances WHERE parent_id = lRecord.id;
				
				IF lRecord2.id IS NOT NULL AND lRecord2.object_id = lObjectId THEN -- По някаква причина този wrapper има от правилния тип обекти - продължаваме към следващия wrapper
					CONTINUE;
				END IF;
				
				IF lRecord2.id IS NOT NULL THEN -- Трябва да изтрием стария обект
					PERFORM spRemoveInstance(lRecord2.id, pUid);
				END IF;
				
				-- Правим правилния обект
				PERFORM spCreateNewInstance(lRecord.id, lObjectId, pUid);
				
				-- Вкарваме wrapper-а в резултата, за да може да го рефрешнем
				lResInstancesArr = array_append(lResInstancesArr, lRecord.id);
			END LOOP;
		END IF;
		
		lRes.result = 1;
		lRes.citation_wrapper_instances = lResInstancesArr;

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spFixTaxonNomenclatureOriginalCitations(	
	pInstanceId bigint,
	pNewRankValue int,
	pUid int
) TO iusrpmt;
