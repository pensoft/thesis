DROP TYPE ret_spSaveTaxonBHLTitle CASCADE;
CREATE TYPE ret_spSaveTaxonBHLTitle AS (
	id bigint
);

CREATE OR REPLACE FUNCTION spSaveTaxonBHLTitle(
	pBHLDataId bigint,	
	pTitle varchar,
	pTitleUrl varchar
)
  RETURNS ret_spSaveTaxonBHLTitle AS
$BODY$
	DECLARE		
		lRes ret_spSaveTaxonBHLTitle;			
	BEGIN				
		INSERT INTO pjs.taxon_bhl_titles(taxon_bhl_data_id, title, title_url)
				VALUES(pBHLDataId, pTitle, pTitleUrl);
		lRes.id = currval('pjs.taxon_bhl_titles_id_seq'::regclass);
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveTaxonBHLTitle(
	pBHLDataId bigint,	
	pTitle varchar,
	pTitleUrl varchar
) TO iusrpmt;
