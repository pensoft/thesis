DROP TYPE ret_spSaveReferenceOrder CASCADE;

CREATE TYPE ret_spSaveReferenceOrder AS (
	result int
);

CREATE OR REPLACE FUNCTION spSaveReferenceOrder(
	pDocumentId bigint
)
  RETURNS ret_spSaveReferenceOrder AS
$BODY$
	DECLARE
		lRes ret_spSaveReferenceOrder;		
		lRecord record;
		lRecord2 record;
		lIter int;
		lReferenceHolderInstanceId bigint;
		lReferenceHolderObjectId bigint;
		lReferenceHolderPos varchar;
		lReferenceObjectId bigint;
		lReferencePosLength int;	
		lMaxPos int;
		lCurrentPos varchar;
	BEGIN
		lRes.result = 0;
		lReferenceHolderObjectId = 21;
		lReferenceObjectId = 95;
		lIter = 1;
		
		SELECT INTO lReferenceHolderInstanceId, lReferenceHolderPos id, pos
		FROM pwt.document_object_instances 
		WHERE document_id = pDocumentId AND object_id = lReferenceHolderObjectId LIMIT 1;
			
		
		IF lReferenceHolderInstanceId IS NULL THEN
			RETURN lRes;
		END IF;
		
		SELECT INTO lMaxPos max(pos) 
		FROM pwt.document_object_instances 
		WHERE parent_id = lReferenceHolderInstanceId AND object_id <> lReferenceObjectId;
		
		lReferencePosLength = char_length(lReferenceHolderPos) + 2;
		
		CREATE TEMP TABLE document_temp_references(
			ref_id bigint,
			pos varchar
		);
		
		IF lMaxPos IS NOT NULL THEN
			lCurrentPos = ForumGetNextOrd(lMaxPos);
		ELSE 
			lCurrentPos = 'AA';
		END IF;
		
		-- Insert the accepted references
		FOR lRecord IN 
			SELECT *
			FROM spGetDocumentReferences(pDocumentId) 
			ORDER BY is_website_citation ASC, first_author_combined_name ASC, authors_count ASC, authors_combined_names ASC, pubyear ASC	
		LOOP
			INSERT INTO document_temp_references(ref_id, pos)
				VALUES (lRecord.reference_instance_id, lReferenceHolderPos || lCurrentPos);
			
			lCurrentPos = ForumGetNextOrd(lCurrentPos);			
		END LOOP;
		-- Insert the unaccepted references
		FOR lRecord IN 
			SELECT *
			FROM pwt.document_object_instances
			WHERE is_confirmed = false AND document_id = pDocumentId AND object_id = lReferenceObjectId
			ORDER BY pos ASC
		LOOP
			INSERT INTO document_temp_references(ref_id, pos)
				VALUES (lRecord.id, lReferenceHolderPos || lCurrentPos);
			
			lCurrentPos = ForumGetNextOrd(lCurrentPos);			
		END LOOP;
		
		
		UPDATE pwt.document_object_instances i SET
			pos = overlay(i.pos placing o.pos FROM 1 FOR char_length(o.pos))
		FROM pwt.document_object_instances p
		JOIN document_temp_references o ON o.ref_id = p.id
		WHERE p.document_id = i.document_id AND p.pos = substring(i.pos, 1, char_length(p.pos)) AND i.pos >= p.pos;
		
		DROP TABLE document_temp_references;
		
		
		
		lRes.result = 1;
		RETURN lRes;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveReferenceOrder(
	pDocumentId bigint
) TO iusrpmt;
