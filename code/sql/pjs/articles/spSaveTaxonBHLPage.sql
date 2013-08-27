DROP TYPE ret_spSaveTaxonBHLPage CASCADE;
CREATE TYPE ret_spSaveTaxonBHLPage AS (
	id bigint
);

CREATE OR REPLACE FUNCTION spSaveTaxonBHLPage(
	pItemId bigint,	
	pUrl varchar,
	pThumbnailUrl varchar,
	pFullsizeImageUrl varchar,
	pNumber int
)
  RETURNS ret_spSaveTaxonBHLPage AS
$BODY$
	DECLARE		
		lRes ret_spSaveTaxonBHLPage;			
	BEGIN				
		INSERT INTO pjs.taxon_bhl_title_item_pages(item_id, "number", url, thumbnail_url, fullsize_image_url)
				VALUES(pItemId, pNumber, pUrl, pThumbnailUrl, pFullsizeImageUrl);
		lRes.id = currval('pjs.taxon_bhl_title_item_pages_id_seq'::regclass);
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveTaxonBHLPage(
	pItemId bigint,	
	pUrl varchar,
	pThumbnailUrl varchar,
	pFullsizeImageUrl varchar,
	pNumber int
) TO iusrpmt;
