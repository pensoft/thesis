DROP TYPE ret_spSaveTaxonLiasDetail CASCADE;
CREATE TYPE ret_spSaveTaxonLiasDetail AS (
	id bigint
);

CREATE OR REPLACE FUNCTION spSaveTaxonLiasDetail(
	pLiasDataId bigint,	
	pDetailId varchar,
	pDetailName varchar
)
  RETURNS ret_spSaveTaxonLiasDetail AS
$BODY$
	DECLARE		
		lRes ret_spSaveTaxonLiasDetail;			
	BEGIN				
		INSERT INTO pjs.taxon_lias_data_details(data_id, detail_id, detail_name)
				VALUES(pLiasDataId, pDetailId, pDetailName);
		lRes.id = currval('pjs.taxon_lias_data_details_id_seq'::regclass);
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveTaxonLiasDetail(
	pLiasDataId bigint,	
	pDetailId varchar,
	pDetailName varchar
) TO iusrpmt;
