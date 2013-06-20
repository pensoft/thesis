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
		lAuthorObjectId bigint;
		lReferenceObjectId bigint;
		lWebsiteReferenceObjectId bigint;
		
		lAuthorFirstNameFieldId bigint;
		lAuthorLastNameFieldId bigint;
		
		lPubYearFieldId bigint;
		
		lRecord record;
		lCombinedAuthorNames varchar;
		lAuthorsCount int;
		lInitials varchar;
		lCurrentAuthorCombinedNames varchar;
	BEGIN
		lAuthorObjectId = 90;
		lReferenceObjectId = 95;
		lWebsiteReferenceObjectId = 108;
		
		lAuthorFirstNameFieldId = 251;
		lAuthorLastNameFieldId = 252;
		
		lPubYearFieldId = 254;
		
		FOR lRes IN
			SELECT i.id, null, null, null, py.value_int, 0, i.pos
			FROM pwt.document_object_instances i
			JOIN pwt.document_object_instances c ON c.document_id = i.document_id AND substring(c.pos, 1, char_length(i.pos)) = i.pos 
			JOIN pwt.instance_field_values py ON py.instance_id = c.id AND py.field_id = lPubYearFieldId
			WHERE i.document_id = pDocumentId AND i.object_id = lReferenceObjectId AND i.is_confirmed = true
			UNION
			SELECT i.id, null, null, null, null, 1, i.pos 
			FROM pwt.document_object_instances i
			JOIN pwt.document_object_instances c ON c.document_id = i.document_id AND substring(c.pos, 1, char_length(i.pos)) = i.pos AND c.object_id = lWebsiteReferenceObjectId
			WHERE i.document_id = pDocumentId AND i.object_id = lReferenceObjectId AND i.is_confirmed = true
		LOOP
			lAuthorsCount = 0;
			lCombinedAuthorNames = '';
			FOR lRecord IN 
				SELECT fn.value_str as first_name, ln.value_str as last_name
				FROM pwt.document_object_instances i
				JOIN pwt.document_object_instances p ON p.id = lRes.reference_instance_id AND substring(i.pos, 1, char_length(p.pos)) = p.pos
				JOIN pwt.instance_field_values fn ON fn.instance_id = i.id AND fn.field_id = lAuthorFirstNameFieldId
				JOIN pwt.instance_field_values ln ON ln.instance_id = i.id AND ln.field_id = lAuthorLastNameFieldId
				WHERE i.document_id = pDocumentId AND i.object_id = lAuthorObjectId
				ORDER BY i.pos ASC
			LOOP
				lAuthorsCount = lAuthorsCount + 1;
				lInitials = substring(lRecord.first_name, 1, 1);
				lCurrentAuthorCombinedNames = trim(coalesce(lRecord.last_name || ' ', '') || coalesce(lInitials, ''));
				IF lAuthorsCount = 1 THEN					
					lRes.first_author_combined_name = lCurrentAuthorCombinedNames;
					lCombinedAuthorNames = lCurrentAuthorCombinedNames;
				ELSE
					lCombinedAuthorNames = lCombinedAuthorNames || ' ' || lCurrentAuthorCombinedNames;
				END IF;			
			END LOOP;
			
			lRes.authors_count = lAuthorsCount;
			lRes.authors_combined_names = lCombinedAuthorNames;
		
			RETURN NEXT lRes;
		END LOOP;
		
		RETURN;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetDocumentReferences(
	pDocumentId bigint
) TO iusrpmt;
