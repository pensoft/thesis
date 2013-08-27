DROP TYPE ret_spSaveTaxonBHLItem CASCADE;
CREATE TYPE ret_spSaveTaxonBHLItem AS (
	id bigint
);

CREATE OR REPLACE FUNCTION spSaveTaxonBHLItem(
	pTitleId bigint,	
	pVolume varchar,
	pPagesCount int
)
  RETURNS ret_spSaveTaxonBHLItem AS
$BODY$
	DECLARE		
		lRes ret_spSaveTaxonBHLItem;			
	BEGIN				
		INSERT INTO pjs.taxon_bhl_title_items(title_id, volume, pages_count)
				VALUES(pTitleId, pVolume, pPagesCount);
		lRes.id = currval('pjs.taxon_bhl_title_items_id_seq'::regclass);
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveTaxonBHLItem(
	pTitleId bigint,	
	pVolume varchar,
	pPagesCount int
) TO iusrpmt;
