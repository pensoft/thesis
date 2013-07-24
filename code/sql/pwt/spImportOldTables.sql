DROP TYPE ret_spImportOldTables CASCADE;
CREATE TYPE ret_spImportOldTables AS (
	result int
);

CREATE OR REPLACE FUNCTION spImportOldTables(
	pDocumentId int
)
  RETURNS ret_spImportOldTables AS
$BODY$
DECLARE
	lRes ret_spImportOldTables;
	
	lTableObjectId bigint = 238;
	lTableWrapperObjectId bigint = 237;	
	lWrapperInstanceId bigint;
	
	lTableId bigint;
	lTableRecord record;
	lCitationsRecord record;
	lUid int;
		
	lCaptionFieldId bigint = 482;
	lContentFieldId bigint = 490;
	
	lTableCitationType int = 2;
	lCitationOldObjectIds bigint[];
	lCitationNewObjectIds bigint[];	
BEGIN
	SELECT INTO lWrapperInstanceId 
		id
	FROM pwt.document_object_instances 
	WHERE document_id = pDocumentId AND object_id = lTableWrapperObjectId;
	
	IF lWrapperInstanceId IS NULL THEN
		RAISE EXCEPTION 'pwt.noTableWrapperForTheSelectedDocument';
	END IF;
	
	CREATE TEMP TABLE tables_import (
		old_id bigint,
		new_id bigint		
	);
	
	CREATE TEMP TABLE citations_import(
		citation_id bigint,
		old_object_ids bigint[],
		new_object_ids bigint[]
	);
	
	INSERT INTO citations_import(citation_id, old_object_ids)
		SELECT id, object_ids
	FROM pwt.citations
	WHERE document_id = pDocumentId AND citation_type = lTableCitationType;
	
	<<lTableLoop>>
	FOR lTableRecord IN
		SELECT t.id,
			t.title as table_title,
			t.description as table_desc,
			t.move_position as position,
			t.lastmod,
			t.usr_id
		FROM pwt.tables t
		WHERE t.document_id = pDocumentId
		ORDER BY t.move_position ASC
	LOOP 
		lUid = lTableRecord.usr_id;
		SELECT INTO lTableId 
			new_instance_id
		FROM spCreateNewInstance(lWrapperInstanceId, lTableObjectId, lUid);
		
		PERFORM pwt.spMarkInstanceAsUnconfirmed(lTableId, lUid);
		PERFORM pwt.spMarkInstanceAsConfirmed(lTableId, lUid);
		
		INSERT INTO tables_import(old_id, new_id) 
			VALUES (lTableRecord.id, lTableId);
			
		
		--Update the table caption
		UPDATE pwt.instance_field_values SET
			value_str = lTableRecord.table_title
		WHERE instance_id = lTableId AND field_id = lCaptionFieldId;
		
		--Update the table caption
		UPDATE pwt.instance_field_values SET
			value_str = lTableRecord.table_desc
		WHERE instance_id = lTableId AND field_id = lContentFieldId;
					
	END LOOP lTableLoop;
		
	FOR lCitationsRecord IN
		SELECT *
		FROM citations_import
	LOOP
		SELECT INTO lCitationNewObjectIds
			array_agg(new_id)
		FROM tables_import 
		WHERE old_id = ANY(lCitationsRecord.old_object_ids);		
		
		UPDATE citations_import SET
			new_object_ids = lCitationNewObjectIds
		WHERE citation_id = lCitationsRecord.citation_id;		
	END LOOP;
	
	UPDATE pwt.citations c SET
		object_ids = t.new_object_ids,
		is_dirty = true
	FROM citations_import t
	WHERE t.citation_id = c.id;
	
	
	
	DROP TABLE tables_import;
	DROP TABLE citations_import;
	
	lRes.result = 1;
	RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spImportOldTables(
	pDocumentId int
) TO iusrpmt;
