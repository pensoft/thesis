DROP TYPE ret_spSaveTaxonGBIFBaseData CASCADE;
CREATE TYPE ret_spSaveTaxonGBIFBaseData AS (
	id bigint
);

CREATE OR REPLACE FUNCTION spSaveTaxonGBIFBaseData(
	pTaxonId bigint,	
	pMapIframeSrc varchar,
	pGBIFTaxonId varchar
)
  RETURNS ret_spSaveTaxonGBIFBaseData AS
$BODY$
	DECLARE		
		lRes ret_spSaveTaxonGBIFBaseData;			
	BEGIN				
		SELECT INTO lRes.id
			id
		FROM pjs.taxon_gbif_data 
		WHERE taxon_id = pTaxonId;
		
		IF lRes.id IS NULL THEN
			INSERT INTO pjs.taxon_gbif_data(taxon_id, map_iframe_src, gbif_taxon_id)
				VALUES(pTaxonId, pMapIframeSrc, pGBIFTaxonId);
			lRes.id = currval('pjs.taxon_gbif_data_id_seq'::regclass);
		ELSE 
			UPDATE pjs.taxon_gbif_data SET
				gbif_taxon_id = pGBIFTaxonId,
				map_iframe_src = pMapIframeSrc,
				lastmoddate = now()
			WHERE id = lRes.id;
		END IF;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveTaxonGBIFBaseData(
	pTaxonId bigint,	
	pMapIframeSrc varchar,
	pGBIFTaxonId varchar
) TO iusrpmt;
