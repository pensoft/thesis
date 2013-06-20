DROP TYPE ret_spSyncChecklistTaxonName CASCADE;
CREATE TYPE ret_spSyncChecklistTaxonName AS (
	result int
);

CREATE OR REPLACE FUNCTION spSyncChecklistTaxonName(
	pTaxonInstanceId bigint, -- InstanceId на таксон нейма
	pUid int
)
  RETURNS ret_spSyncChecklistTaxonName AS
$BODY$
	DECLARE
		lRes ret_spSyncChecklistTaxonName;	
		
		lRankFieldId bigint := 414;		
		lRankValue int;
		lName varchar;
		lRecord record;
		lFieldValue varchar;
		lPatternValue varchar;
		lSeparator varchar := ' ';
	BEGIN
		
		lRes.result = 0;
		
		SELECT INTO lRankValue value_int
		FROM pwt.instance_field_values 
		WHERE instance_id = pTaxonInstanceId AND field_id = lRankFieldId;
		
		
		IF coalesce(lRankValue, 0) = 0 THEN
			RETURN lRes;
		END IF;
		lRes.result = 1;
		
		RAISE NOTICE 'Rank %', lRankValue;
		
		lName = '';
		FOR lRecord IN
			SELECT * 
			FROM pwt.checklist_taxon_names_details d
			JOIN pwt.checklist_taxon_names t ON t.id = d.taxon_name_id
			WHERE t.rank_id = lRankValue
			ORDER BY d.ord ASC
		LOOP
			SELECT INTO lFieldValue value_str
			FROM pwt.instance_field_values
			WHERE instance_id = pTaxonInstanceId AND field_id = lRecord.field_id;
			
			IF coalesce(lFieldValue, '') <> '' OR lRecord.pattern_use = 1 THEN
				lPatternValue = coalesce(replace(lRecord.pattern, '{value}', coalesce(lFieldValue, '')), '');
				IF lPatternValue <> '' AND lName <> '' THEN
					lName = lName || lSeparator;
				END IF;
				lName = lName || lPatternValue;

			END IF;
		END LOOP;
		
		--RAISE NOTICE 'Name %', lName;
		IF lName = '' THEN
			lName = 'Taxon';
		END IF;
		
		UPDATE pwt.document_object_instances SET
			display_name = lName
		WHERE id = pTaxonInstanceId;
		
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSyncChecklistTaxonName(	
	pTaxonInstanceId bigint,
	pUid int
) TO iusrpmt;
