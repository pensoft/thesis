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
	BEGIN		
		FOR lRecord IN
			SELECT id, citation_type, citation_mode, preview, object_ids, is_dirty, field_id, instance_id
			FROM pwt.citations
			WHERE document_id = pDocumentId AND citation_type = ANY (pCitationTypes)
		LOOP
			lRes.citation_id = lRecord.id;
			lRes.citation_type = lRecord.citation_type;
			lRes.citation_mode = lRecord.citation_mode;
			lRes.citation_objects = lRecord.object_ids;
			lRes.field_id = lRecord.field_id;
			lRes.instance_id = lRecord.instance_id;
			SELECT INTO lRes.plate_id plate_id FROM pwt.media WHERE id = lRecord.object_ids[1];
			IF(lRes.plate_id IS NOT NULL) THEN
				lRes.is_plate = 1;
			ELSE
				lRes.is_plate = 0;
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
