DROP TYPE ret_spSaveTaxonUbioSiteResult CASCADE;
CREATE TYPE ret_spSaveTaxonUbioSiteResult AS (
	id bigint
);

CREATE OR REPLACE FUNCTION spSaveTaxonUbioSiteResult(
	pTaxonId bigint,	
	pSiteId int,
	pLink varchar
)
  RETURNS ret_spSaveTaxonUbioSiteResult AS
$BODY$
	DECLARE		
		lRes ret_spSaveTaxonUbioSiteResult;					
	BEGIN				
		SELECT INTO lRes.id 
			id
		FROM pjs.taxon_sites_results 
		WHERE taxon_id = pTaxonId AND site_id = pSiteId;
		
		IF lRes.id IS NULL THEN
			INSERT INTO pjs.taxon_sites_results(taxon_id, site_id, has_results, specific_link_url)
					VALUES(pTaxonId, pSiteId, true, pLink);
			lRes.id = currval('pjs.taxon_sites_results_id_seq'::regclass);
		ELSE
			UPDATE pjs.taxon_sites_results SET
				has_results = true,
				specific_link_url = pLink,
				lastmoddate = now()
			WHERE id = lRes.id;
			
		END IF;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveTaxonUbioSiteResult(
	pTaxonId bigint,	
	pSiteId int,
	pLink varchar
) TO iusrpmt;
