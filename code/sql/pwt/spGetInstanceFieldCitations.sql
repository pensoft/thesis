DROP TYPE ret_spGetInstanceFieldCitations CASCADE;

CREATE TYPE ret_spGetInstanceFieldCitations AS (
	citation_id bigint,
	citation_mode int,
	preview varchar,
	citation_objects bigint[]
);

CREATE OR REPLACE FUNCTION spGetInstanceFieldCitations(
	pInstanceId bigint,
	pFieldId bigint,
	pCitationType int
)
  RETURNS SETOF ret_spGetInstanceFieldCitations AS
$BODY$
	DECLARE
		lRes ret_spGetInstanceFieldCitations;		
		lRecord record;
	BEGIN		
		FOR lRecord IN
			SELECT id, citation_mode, preview, object_ids, is_dirty
			FROM pwt.citations
			WHERE instance_id = pInstanceId AND field_id = pFieldId AND citation_type = pCitationType			
		LOOP
			lRes.citation_id = lRecord.id;
			lRes.citation_mode = lRecord.citation_mode;
			lRes.citation_objects = lRecord.object_ids;
			lRes.preview = lRecord.preview;
			IF lRecord.is_dirty = true THEN
				SELECT INTO lRes.preview preview FROM spGenerateCitationPreview(lRecord.id);
			END IF;
			
			RETURN NEXT lRes;
		END LOOP;
		
		RETURN;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetInstanceFieldCitations(
	pInstanceId bigint,
	pFieldId bigint,
	pCitationType int
) TO iusrpmt;
