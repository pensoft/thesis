DROP TYPE ret_spSaveCitation CASCADE;

CREATE TYPE ret_spSaveCitation AS (
	citation_id bigint,
	citation_mode int,
	preview varchar,
	citation_objects bigint[]
);

CREATE OR REPLACE FUNCTION spSaveCitation(
	pCitationId bigint,
	pInstanceId bigint,
	pFieldId bigint,
	pCitationType int,
	pCitationMode int,
	pCitationObjects int[],
	pUid int
)
  RETURNS ret_spSaveCitation AS
$BODY$
	DECLARE
		lRes ret_spSaveCitation;		
		lRecord record;
		lCitationId bigint;
		lDocumentId bigint;
	BEGIN	
		SELECT INTO lDocumentId document_id FROM pwt.document_object_instances WHERE id = pInstanceId;
		IF coalesce(pCitationId, 0) = 0 THEN
			INSERT INTO  pwt.citations(citation_type, object_ids, citation_mode, instance_id, field_id, document_id) VALUES (pCitationType, pCitationObjects, pCitationMode, pInstanceId, pFieldId, lDocumentId);
			lCitationId = currval('pwt.citations_id_seq');
		ELSE
			UPDATE pwt.citations SET
				object_ids = pCitationObjects, 
				citation_mode = pCitationMode,
				is_dirty = true
			WHERE id = pCitationId;
			lCitationId = pCitationId;
		END IF;
		
		
		FOR lRecord IN
			SELECT id, citation_mode, preview, object_ids, is_dirty, object_ids
			FROM pwt.citations
			WHERE id = lCitationId			
		LOOP
			lRes.citation_id = lRecord.id;
			lRes.citation_mode = lRecord.citation_mode;
			lRes.citation_objects = lRecord.object_ids;
			IF lRecord.is_dirty = true THEN
				SELECT INTO lRes.preview preview FROM spGenerateCitationPreview(lRecord.id);
			END IF;
						
		END LOOP;
		
		RETURN lRes;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveCitation(
	pCitationId bigint,
	pInstanceId bigint,
	pFieldId bigint,
	pCitationType int,
	pCitationMode int,
	pCitationObjects int[],
	pUid int
) TO iusrpmt;
