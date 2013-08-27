DROP TYPE ret_spSaveTaxonWikimediaCategory CASCADE;
CREATE TYPE ret_spSaveTaxonWikimediaCategory AS (
	id bigint
);

CREATE OR REPLACE FUNCTION spSaveTaxonWikimediaCategory(
	pTaxonId bigint,	
	pCategoryName varchar
)
  RETURNS ret_spSaveTaxonWikimediaCategory AS
$BODY$
	DECLARE		
		lRes ret_spSaveTaxonWikimediaCategory;			
	BEGIN				
		INSERT INTO pjs.taxon_wikimedia_categories(taxon_id, category_name)
				VALUES(pTaxonId, pCategoryName);
		lRes.id = currval('pjs.taxon_wikimedia_categories_id_seq'::regclass);
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveTaxonWikimediaCategory(
	pTaxonId bigint,	
	pCategoryName varchar
) TO iusrpmt;
