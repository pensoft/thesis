DROP TYPE ret_spSaveTaxonLiasBaseData CASCADE;
CREATE TYPE ret_spSaveTaxonLiasBaseData AS (
	id bigint
);

CREATE OR REPLACE FUNCTION spSaveTaxonLiasBaseData(
	pTaxonId bigint,	
	pResults int
)
  RETURNS ret_spSaveTaxonLiasBaseData AS
$BODY$
	DECLARE		
		lRes ret_spSaveTaxonLiasBaseData;			
	BEGIN				
		SELECT INTO lRes.id
			id
		FROM pjs.taxon_lias_data 
		WHERE taxon_id = pTaxonId;
		
		IF lRes.id IS NULL THEN
			INSERT INTO pjs.taxon_lias_data(taxon_id, results)
				VALUES(pTaxonId, pResults);
			lRes.id = currval('pjs.taxon_lias_data_id_seq'::regclass);
		ELSE 
			UPDATE pjs.taxon_lias_data SET
				results = pResults,				
				lastmoddate = now()
			WHERE id = lRes.id;			
			
			DELETE FROM pjs.taxon_lias_data_details t
			WHERE t.data_id = lRes.id;
		END IF;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveTaxonLiasBaseData(
	pTaxonId bigint,	
	pResults int
) TO iusrpmt;
