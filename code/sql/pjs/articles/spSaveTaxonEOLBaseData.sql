DROP TYPE ret_spSaveTaxonEOLBaseData CASCADE;
CREATE TYPE ret_spSaveTaxonEOLBaseData AS (
	id bigint
);

CREATE OR REPLACE FUNCTION spSaveTaxonEOLBaseData(
	pTaxonId bigint,	
	pEOLTaxonId varchar
)
  RETURNS ret_spSaveTaxonEOLBaseData AS
$BODY$
	DECLARE		
		lRes ret_spSaveTaxonEOLBaseData;			
	BEGIN				
		SELECT INTO lRes.id
			id
		FROM pjs.taxon_eol_data 
		WHERE taxon_id = pTaxonId;
		
		IF lRes.id IS NULL THEN
			INSERT INTO pjs.taxon_eol_data(taxon_id, eol_taxon_id)
				VALUES(pTaxonId, pEOLTaxonId);
			lRes.id = currval('pjs.taxon_eol_data_id_seq'::regclass);
		ELSE 
			UPDATE pjs.taxon_eol_data SET
				eol_taxon_id = pEOLTaxonId,				
				lastmoddate = now()
			WHERE id = lRes.id;
			
			DELETE FROM pjs.taxon_eol_images 
			WHERE eol_data_id = lRes.id;
		END IF;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveTaxonEOLBaseData(
	pTaxonId bigint,	
	pEOLTaxonId varchar
) TO iusrpmt;
