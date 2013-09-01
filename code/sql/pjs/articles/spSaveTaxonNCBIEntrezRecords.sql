DROP TYPE ret_spSaveTaxonNCBIEntrezRecords CASCADE;
CREATE TYPE ret_spSaveTaxonNCBIEntrezRecords AS (
	id bigint
);

CREATE OR REPLACE FUNCTION spSaveTaxonNCBIEntrezRecords(
	pNCBIDataId bigint,	
	pDBId int,
	pRecords int
)
  RETURNS ret_spSaveTaxonNCBIEntrezRecords AS
$BODY$
	DECLARE		
		lRes ret_spSaveTaxonNCBIEntrezRecords;			
	BEGIN				
		INSERT INTO pjs.taxon_ncbi_entrez_records(ncbi_data_id, db_id, records)
			VALUES (pNCBIDataId, pDBId, pRecords);
		lRes.id = currval('pjs.taxon_ncbi_entrez_records_id_seq');
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveTaxonNCBIEntrezRecords(
	pNCBIDataId bigint,	
	pDBId int,
	pRecords int
) TO iusrpmt;
