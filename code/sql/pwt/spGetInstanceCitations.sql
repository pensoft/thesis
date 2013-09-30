DROP TYPE ret_spGetInstanceCitations CASCADE;

CREATE TYPE ret_spGetInstanceCitations AS (
	citation_id bigint,
	citation_mode int,
	preview varchar,
	citation_type int,
	citation_objects bigint[],
	instance_id bigint,
	field_id bigint
);

CREATE OR REPLACE FUNCTION spGetInstanceCitations(
	pInstanceId bigint
)
  RETURNS SETOF ret_spGetInstanceCitations AS
$BODY$
	DECLARE
		lRes ret_spGetInstanceCitations;		
		lRecord record;
	BEGIN		
		FOR lRecord IN
			SELECT c.id, c.citation_mode, c.preview, c.object_ids, c.is_dirty, c.citation_type, c.instance_id, c.field_id
			FROM pwt.citations c
			JOIN pwt.document_object_instances p ON true
			JOIN pwt.document_object_instances ch ON ch.id = c.instance_id AND ch.document_id = p.document_id AND ch.pos ILIKE (p.pos || '%')
			WHERE p.id = pInstanceId
		LOOP
			lRes.citation_id = lRecord.id;
			lRes.citation_mode = lRecord.citation_mode;
			lRes.citation_objects = lRecord.object_ids;
			lRes.preview = lRecord.preview;
			lRes.citation_type = lRecord.citation_type;
			lRes.instance_id = lRecord.instance_id;
			lRes.field_id = lRecord.field_id;
			IF lRecord.is_dirty = true THEN
				SELECT INTO lRes.preview preview FROM spGenerateCitationPreview(lRecord.id);
			END IF;
			
			RETURN NEXT lRes;
		END LOOP;
		
		RETURN;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetInstanceCitations(
	pInstanceId bigint
) TO iusrpmt;
