DROP TYPE ret_spSaveTaxonNCBIBaseData CASCADE;
CREATE TYPE ret_spSaveTaxonNCBIBaseData AS (
	id bigint
);

CREATE OR REPLACE FUNCTION spSaveTaxonNCBIBaseData(
	pTaxonId bigint,	
	pNCBITaxonId varchar,
	pRank varchar,
	pDivision varchar
)
  RETURNS ret_spSaveTaxonNCBIBaseData AS
$BODY$
	DECLARE		
		lRes ret_spSaveTaxonNCBIBaseData;			
	BEGIN				
		SELECT INTO lRes.id
			id
		FROM pjs.taxon_ncbi_data 
		WHERE taxon_id = pTaxonId;
		
		IF lRes.id IS NULL THEN
			INSERT INTO pjs.taxon_ncbi_data(taxon_id, ncbi_id, rank, division)
				VALUES(pTaxonId, pNCBITaxonId, pRank, pDivision);
			lRes.id = currval('pjs.taxon_ncbi_data_id_seq'::regclass);
		ELSE 
			UPDATE pjs.taxon_ncbi_data SET
				ncbi_id = pNCBITaxonId,
				rank = pRank,
				division = pDivision,
				lastmoddate = now()
			WHERE id = lRes.id;
			
			DELETE FROM pjs.taxon_ncbi_entrez_records
			WHERE ncbi_data_id = lRes.id;
			
			DELETE FROM pjs.taxon_ncbi_lineage
			WHERE ncbi_data_id = lRes.id;
			
			DELETE FROM pjs.taxon_ncbi_related_links
			WHERE ncbi_data_id = lRes.id;
		END IF;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveTaxonNCBIBaseData(
	pTaxonId bigint,	
	pNCBITaxonId varchar,
	pRank varchar,
	pDivision varchar
) TO iusrpmt;
