DROP TYPE ret_spSaveTaxonWikimediaPhoto CASCADE;
CREATE TYPE ret_spSaveTaxonWikimediaPhoto AS (
	id bigint
);

CREATE OR REPLACE FUNCTION spSaveTaxonWikimediaPhoto(
	pCategoryId bigint,	
	pImgSrc varchar,
	pImgName varchar
)
  RETURNS ret_spSaveTaxonWikimediaPhoto AS
$BODY$
	DECLARE		
		lRes ret_spSaveTaxonWikimediaPhoto;			
	BEGIN				
		INSERT INTO pjs.taxon_wikimedia_category_images(category_id, image_name, url)
				VALUES(pCategoryId, pImgName, pImgSrc);
		lRes.id = currval('pjs.taxon_wikimedia_category_images_id_seq'::regclass);
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveTaxonWikimediaPhoto(
	pCategoryId bigint,	
	pImgSrc varchar,
	pImgName varchar
) TO iusrpmt;
