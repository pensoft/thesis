DROP TYPE ret_spGetDocumentCitations CASCADE;

CREATE TYPE ret_spGetDocumentCitations AS (
	citation_id bigint,
	citation_mode int,
	preview varchar,
	field_id bigint,
	instance_id bigint,
	citation_objects bigint[]
);

CREATE OR REPLACE FUNCTION spGetDocumentCitations(
	pDocumentId bigint,
	pCitationType int,
	pInstanceId bigint
)
  RETURNS SETOF ret_spGetDocumentCitations AS
$BODY$
	DECLARE
		lRes ret_spGetDocumentCitations;		
		lRecord record;
		lRootInstancePos varchar;
	BEGIN		
		
		IF coalesce(pInstanceId, 0) <> 0 THEN
			SELECT INTO lRootInstancePos pos FROM pwt.document_object_instances WHERE id = pInstanceId;
		END IF;
	
		FOR lRecord IN
			SELECT c.id, c.citation_mode, c.preview, c.object_ids, c.is_dirty, c.field_id, c.instance_id
			FROM pwt.citations c
			JOIN pwt.document_object_instances doi ON c.instance_id = doi.id AND (CASE WHEN lRootInstancePos IS NOT NULL THEN substring(doi.pos from 1 for 2) = lRootInstancePos ELSE TRUE END)
			WHERE c.document_id = pDocumentId AND c.citation_type = pCitationType			
		LOOP
			lRes.citation_id = lRecord.id;
			lRes.citation_mode = lRecord.citation_mode;
			lRes.citation_objects = lRecord.object_ids;
			lRes.preview = lRecord.preview;
			lRes.field_id = lRecord.field_id;
			lRes.instance_id = lRecord.instance_id;
			IF lRecord.is_dirty = true THEN
				SELECT INTO lRes.preview preview FROM spGenerateCitationPreview(lRecord.id);
			END IF;
			
			RETURN NEXT lRes;
		END LOOP;
		
		RETURN;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetDocumentCitations(
	pDocumentId bigint,
	pCitationType int,
	pInstanceId bigint
) TO iusrpmt;
