DROP TYPE ret_spSaveTaxonCatalogueOfLifeBaseData CASCADE;
CREATE TYPE ret_spSaveTaxonCatalogueOfLifeBaseData AS (
	id bigint
);

CREATE OR REPLACE FUNCTION spSaveTaxonCatalogueOfLifeBaseData(
	pTaxonId bigint,	
	pCOLTaxonId varchar,
	pUrl varchar
)
  RETURNS ret_spSaveTaxonCatalogueOfLifeBaseData AS
$BODY$
	DECLARE		
		lRes ret_spSaveTaxonCatalogueOfLifeBaseData;			
	BEGIN				
		SELECT INTO lRes.id
			id
		FROM pjs.taxon_catalogue_of_life_data 
		WHERE taxon_id = pTaxonId;
		
		IF lRes.id IS NULL THEN
			INSERT INTO pjs.taxon_catalogue_of_life_data(taxon_id, col_taxon_id, url)
				VALUES(pTaxonId, pCOLTaxonId, pUrl);
			lRes.id = currval('pjs.taxon_catalogue_of_life_data_id_seq'::regclass);
		ELSE 
			UPDATE pjs.taxon_catalogue_of_life_data SET
				col_taxon_id = pCOLTaxonId,
				url = pUrl,
				lastmoddate = now()
			WHERE id = lRes.id;					
		END IF;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveTaxonCatalogueOfLifeBaseData(
	pTaxonId bigint,	
	pCOLTaxonId varchar,
	pUrl varchar
) TO iusrpmt;
