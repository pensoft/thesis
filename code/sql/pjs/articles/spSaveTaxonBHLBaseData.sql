DROP TYPE ret_spSaveTaxonBHLBaseData CASCADE;
CREATE TYPE ret_spSaveTaxonBHLBaseData AS (
	id bigint
);

CREATE OR REPLACE FUNCTION spSaveTaxonBHLBaseData(
	pTaxonId bigint,	
	pResultTakenSuccessfully int
)
  RETURNS ret_spSaveTaxonBHLBaseData AS
$BODY$
	DECLARE		
		lRes ret_spSaveTaxonBHLBaseData;			
	BEGIN				
		SELECT INTO lRes.id
			id
		FROM pjs.taxon_bhl_data 
		WHERE taxon_id = pTaxonId;
		
		IF lRes.id IS NULL THEN
			INSERT INTO pjs.taxon_bhl_data(taxon_id, result_taken_successfully)
				VALUES(pTaxonId, pResultTakenSuccessfully::boolean);
			lRes.id = currval('pjs.taxon_bhl_data_id_seq'::regclass);
		ELSE 
			UPDATE pjs.taxon_bhl_data SET
				result_taken_successfully = pResultTakenSuccessfully::boolean,				
				lastmoddate = now()
			WHERE id = lRes.id;
			
			DELETE FROM pjs.taxon_bhl_title_item_pages p
			USING pjs.taxon_bhl_title_items i
			JOIN pjs.taxon_bhl_titles t ON t.id = i.title_id
			WHERE i.id = p.item_id AND t.taxon_bhl_data_id = lRes.id;
			
			DELETE FROM pjs.taxon_bhl_title_items i
			USING pjs.taxon_bhl_titles t
			WHERE t.id = i.title_id AND t.taxon_bhl_data_id = lRes.id;
			
			DELETE FROM pjs.taxon_bhl_titles t
			WHERE t.taxon_bhl_data_id = lRes.id;
		END IF;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveTaxonBHLBaseData(
	pTaxonId bigint,	
	pResultTakenSuccessfully int
) TO iusrpmt;
