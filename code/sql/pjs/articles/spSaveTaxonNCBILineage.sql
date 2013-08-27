DROP TYPE ret_spSaveTaxonNCBILineage CASCADE;
CREATE TYPE ret_spSaveTaxonNCBILineage AS (
	id bigint
);

CREATE OR REPLACE FUNCTION spSaveTaxonNCBILineage(
	pNCBIDataId bigint,	
	pTaxId varchar,
	pScientificName varchar
)
  RETURNS ret_spSaveTaxonNCBILineage AS
$BODY$
	DECLARE		
		lRes ret_spSaveTaxonNCBILineage;			
	BEGIN				
		INSERT INTO pjs.taxon_ncbi_lineage(ncbi_data_id, scientific_name, tax_id)
				VALUES(pNCBIDataId, pScientificName, pTaxId);
		lRes.id = currval('pjs.taxon_ncbi_lineage_id_seq'::regclass);
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveTaxonNCBILineage(
	pNCBIDataId bigint,
	pTaxId varchar,
	pScientificName varchar
) TO iusrpmt;
