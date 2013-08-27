DROP TYPE ret_spSaveTaxonSiteResult CASCADE;
CREATE TYPE ret_spSaveTaxonSiteResult AS (
	id bigint
);

CREATE OR REPLACE FUNCTION spSaveTaxonSiteResult(
	pTaxonId bigint,	
	pSiteId int,
	pHasResult int,
	pSpecificLink varchar
)
  RETURNS ret_spSaveTaxonSiteResult AS
$BODY$
	DECLARE		
		lRes ret_spSaveTaxonSiteResult;					
		lSpecificLink varchar;
	BEGIN				
		SELECT INTO lRes.id 
			id
		FROM pjs.taxon_sites_results 
		WHERE taxon_id = pTaxonId AND site_id = pSiteId;
		
		IF pHasResult > 0 THEN
			lSpecificLink = pSpecificLink;
		END IF;
		
		IF lRes.id IS NULL THEN
			INSERT INTO pjs.taxon_sites_results(taxon_id, site_id, has_results, specific_link_url)
					VALUES(pTaxonId, pSiteId, pHasResult::boolean, lSpecificLink);
			lRes.id = currval('pjs.taxon_sites_results_id_seq'::regclass);
		ELSE
			UPDATE pjs.taxon_sites_results SET				
				has_results = pHasResult::boolean,
				specific_link_url = lSpecificLink,
				lastmoddate = now()
			WHERE id = lRes.id;
			
		END IF;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveTaxonSiteResult(
	pTaxonId bigint,	
	pSiteId int,
	pHasResult int,
	pSpecificLink varchar
) TO iusrpmt;
