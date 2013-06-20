DROP TYPE ret_spUpdateReferenceCitations CASCADE;

CREATE TYPE ret_spUpdateReferenceCitations AS (
	result int
);

CREATE OR REPLACE FUNCTION spUpdateReferenceCitations(
	pReferenceId bigint,
	pUid int
)
  RETURNS ret_spUpdateReferenceCitations AS
$BODY$
	DECLARE
		lRes ret_spUpdateReferenceCitations;		
		lRecord record;		
		lRecord2 record;
		lDocumentId bigint;
		lReferenceCitationType int;
	BEGIN	
		lReferenceCitationType = 3;
		
		
		SELECT INTO lDocumentId document_id FROM pwt.document_object_instances WHERE id = pReferenceId;
		SELECT INTO lRecord * FROM spGetDocumentReferences(lDocumentId) WHERE reference_instance_id = pReferenceId;
		
		-- UPDATE-ваме всички цитации от същата група
		FOR lRecord2 IN
			SELECT * FROM spGetDocumentReferences(lDocumentId)
			WHERE first_author_combined_name = lRecord.first_author_combined_name 
				AND authors_count = lRecord.authors_count
				AND authors_combined_names = lRecord.authors_combined_names
				AND pubyear = lRecord.pubyear
				AND is_website_citation = lRecord.is_website_citation				
			
		LOOP
			UPDATE pwt.citations c SET
				is_dirty = true
			WHERE lRecord2.reference_instance_id = ANY(c.object_ids) AND c.citation_type = lReferenceCitationType;
		END LOOP;
		
		lRes.result = 1;		
		RETURN lRes;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spUpdateReferenceCitations(
	pReferenceId bigint,
	pUid int
) TO iusrpmt;
