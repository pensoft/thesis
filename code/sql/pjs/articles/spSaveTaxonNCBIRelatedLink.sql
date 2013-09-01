DROP TYPE ret_spSaveTaxonNCBIRelatedLink CASCADE;
CREATE TYPE ret_spSaveTaxonNCBIRelatedLink AS (
	id bigint
);

CREATE OR REPLACE FUNCTION spSaveTaxonNCBIRelatedLink(
	pNCBIDataId bigint,	
	pItemId varchar,
	pDbName varchar,
	pTitle varchar,
	pUrl varchar
)
  RETURNS ret_spSaveTaxonNCBIRelatedLink AS
$BODY$
	DECLARE		
		lRes ret_spSaveTaxonNCBIRelatedLink;			
	BEGIN				
		INSERT INTO pjs.taxon_ncbi_related_links(ncbi_data_id, db_name, title, item_id, url)
			VALUES (pNCBIDataId, pDbName, pTitle, pItemId, pUrl);
		lRes.id = currval('pjs.taxon_ncbi_related_links_id_seq');
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveTaxonNCBIRelatedLink(
	pNCBIDataId bigint,	
	pItemId varchar,
	pDbName varchar,
	pTitle varchar,
	pUrl varchar
) TO iusrpmt;
