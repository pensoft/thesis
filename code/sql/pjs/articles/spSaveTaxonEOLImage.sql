DROP TYPE ret_spSaveTaxonEOLImage CASCADE;
CREATE TYPE ret_spSaveTaxonEOLImage AS (
	id bigint
);

CREATE OR REPLACE FUNCTION spSaveTaxonEOLImage(
	pEOLDataId bigint,	
	pImageUrl varchar
)
  RETURNS ret_spSaveTaxonEOLImage AS
$BODY$
	DECLARE		
		lRes ret_spSaveTaxonEOLImage;			
	BEGIN				
		INSERT INTO pjs.taxon_eol_images(eol_data_id, url)
				VALUES(pEOLDataId, pImageUrl);
		lRes.id = currval('pjs.taxon_eol_images_id_seq'::regclass);
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveTaxonEOLImage(
	pEOLDataId bigint,	
	pImageUrl varchar
) TO iusrpmt;
