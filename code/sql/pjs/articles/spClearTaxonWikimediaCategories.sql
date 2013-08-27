DROP TYPE ret_spClearTaxonWikimediaCategories CASCADE;
CREATE TYPE ret_spClearTaxonWikimediaCategories AS (
	result int
);

CREATE OR REPLACE FUNCTION spClearTaxonWikimediaCategories(
	pTaxonId bigint
)
  RETURNS ret_spClearTaxonWikimediaCategories AS
$BODY$
	DECLARE		
		lRes ret_spClearTaxonWikimediaCategories;			
	BEGIN			
		DELETE FROM pjs.taxon_wikimedia_category_images i
		USING pjs.taxon_wikimedia_categories c
		WHERE c.taxon_id = pTaxonId AND c.id = i.category_id;
		
		DELETE FROM pjs.taxon_wikimedia_categories
		WHERE taxon_id = pTaxonId;
		
		lRes.result = 1;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spClearTaxonWikimediaCategories(
	pTaxonId bigint
) TO iusrpmt;
