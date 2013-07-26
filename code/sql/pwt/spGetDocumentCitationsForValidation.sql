DROP TYPE IF EXISTS ret_spGetDocumentCitationsForValidation CASCADE;

CREATE TYPE ret_spGetDocumentCitationsForValidation AS (
	citation_id bigint,
	citation_type int,
	citation_mode int,
	field_id bigint,
	instance_id bigint,
	citation_objects bigint[],
	is_plate int,
	plate_id int
);

CREATE OR REPLACE FUNCTION spGetDocumentCitationsForValidation(
	pDocumentId bigint,
	pCitationTypes int[]
)
  RETURNS SETOF ret_spGetDocumentCitationsForValidation AS
$BODY$
	DECLARE
		lRes ret_spGetDocumentCitationsForValidation;		
		lRecord record;
		lRecord_figs record;
		cFiguresCitationType CONSTANT int := 1;
		cTablesCitationType CONSTANT int := 2;
		cReferencesCitationType CONSTANT int := 3;
	BEGIN		
		FOR lRecord IN
			SELECT id, citation_type, citation_mode, preview, object_ids, is_dirty, field_id, instance_id
			FROM pwt.citations
			WHERE document_id = pDocumentId AND citation_type = ANY (pCitationTypes)
		LOOP
			lRes.citation_id = lRecord.id;
			lRes.citation_type = lRecord.citation_type;
			lRes.citation_mode = lRecord.citation_mode;
			lRes.field_id = lRecord.field_id;
			lRes.instance_id = lRecord.instance_id;
			lRes.citation_objects = '{}';
			
			IF(lRecord.citation_type = cFiguresCitationType) THEN
				FOR lRecord_figs IN
					SELECT DISTINCT ON (doi.id) doi.id as id
					FROM pwt.document_object_instances doi
					JOIN pwt.document_object_instances doi2 ON doi.pos = substring(doi2.pos from 1 for 4) AND doi2.document_id = doi.document_id
					WHERE doi2.id = ANY (lRecord.object_ids)
						AND doi.document_id = pDocumentId
				LOOP
					lRes.citation_objects = array_append(lRes.citation_objects, lRecord_figs.id);
				END LOOP;
			END IF;
			
			IF (lRecord.citation_type = cTablesCitationType) THEN
				lRes.citation_objects = lRecord.object_ids;
			END IF;
			
			IF (lRecord.citation_type = cReferencesCitationType) THEN
				lRes.citation_objects = lRecord.object_ids;
			END IF;
			
			RETURN NEXT lRes;
		END LOOP;
		
		RETURN;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetDocumentCitationsForValidation(
	pDocumentId bigint,
	pCitationTypes int[]
) TO iusrpmt;
