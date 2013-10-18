DROP TYPE ret_spGetDocumentReferences CASCADE;

CREATE TYPE ret_spGetDocumentReferences AS (
	reference_instance_id bigint,
	first_author_combined_name varchar,
	authors_count int,
	authors_combined_names varchar,
	pubyear int, 
	is_website_citation int,
	reference_pos varchar
);

CREATE OR REPLACE FUNCTION spGetDocumentReferences(
	pDocumentId bigint
)
  RETURNS SETOF ret_spGetDocumentReferences AS
$BODY$
	DECLARE
		lRes ret_spGetDocumentReferences;		
		lReferenceObjectId bigint = 95;
		
		lReferencePubyearFieldId int = 496;
		lReferenceIsWebsiteReferenceFieldId int = 497;
		lReferenceAllAuthorsCombinedNamesFieldId int = 495;
		lReferenceAuthorsCountFieldId int = 494;
		lReferenceFirstAuthorCombinedNameFieldId int = 493;
		
		lRecord record;		
	BEGIN
		lReferenceObjectId = 95;
		

		FOR lRes IN
			SELECT i.id, facn.value_str, ac.value_int, aacn.value_str, y.value_int, w.value_int, i.pos
			FROM pwt.document_object_instances i
			JOIN pwt.instance_field_values y ON y.instance_id = i.id AND y.field_id = lReferencePubyearFieldId
			JOIN pwt.instance_field_values w ON w.instance_id = i.id AND w.field_id = lReferenceIsWebsiteReferenceFieldId
			JOIN pwt.instance_field_values ac ON ac.instance_id = i.id AND ac.field_id = lReferenceAuthorsCountFieldId
			JOIN pwt.instance_field_values aacn ON aacn.instance_id = i.id AND aacn.field_id = lReferenceAllAuthorsCombinedNamesFieldId
			JOIN pwt.instance_field_values facn ON facn.instance_id = i.id AND facn.field_id = lReferenceFirstAuthorCombinedNameFieldId
						
			WHERE i.document_id = pDocumentId AND i.object_id = lReferenceObjectId AND i.is_confirmed = true
			
		LOOP			
			RETURN NEXT lRes;
		END LOOP;
		--RAISE EXCEPTION 'lInitialsArr: do tuk';
		RETURN;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetDocumentReferences(
	pDocumentId bigint
) TO iusrpmt;
