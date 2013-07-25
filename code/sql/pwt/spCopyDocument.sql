DROP TYPE ret_spCopyDocument CASCADE;
CREATE TYPE ret_spCopyDocument AS (
	document_id int
);

/**
	This stored procedure will copy a document.
	The copied tables are:
		pwt.citations
		pwt.document_object_instances
		pwt.document_revisions
		pwt.document_users
		pwt.documents
		pwt.instance_field_values
		pwt.media
		pwt.msg
		pwt.plates
		pwt.tables
		pwt.document_template_objects
	The following tables will not be copied		
		pwt.lock_history
*/
CREATE OR REPLACE FUNCTION spCopyDocument(
	pDocumentId int
)
  RETURNS ret_spCopyDocument AS
$BODY$
	DECLARE
		lNewDocumentId int;
		lRes ret_spCopyDocument;	
		lRecord record;
		lCitationItemsRecord record;
		lDocumentTemplateObjectsRow pwt.document_template_objects%ROWTYPE;
		lDocumentObjectInstanceRow pwt.document_object_instances%ROWTYPE;
		lMsgRow pwt.msg%ROWTYPE;
		lDocumentRevisionRow pwt.document_revisions%ROWTYPE;
		lMediaRow pwt.media%ROWTYPE;
		lPlateRow pwt.plates%ROWTYPE;
		lTableRow pwt.tables%ROWTYPE;		
		lCitationRow pwt.citations%ROWTYPE;
		
		lStatement text;
		lTempId bigint;
		lPreviousId bigint;		
		
		lFigCitationType int = 1;
		lTableCitationType int = 2;
		lReferenceCitationType int = 3;
		lSupFileCitationType int = 4;
		lCitationObjectIds bigint[];
	BEGIN
		-- Create a new document
		SELECT INTO lNewDocumentId 
			nextval('pwt.documents_id_seq'::regclass);
			
			
		INSERT INTO pwt.documents(id, name, template_id, createuid, lastmoduid, state, papertype_id, journal_id) 
			SELECT lNewDocumentId, name || '_Copy', template_id, createuid, lastmoduid, state, papertype_id, journal_id
			FROM pwt.documents 
			WHERE id = pDocumentId;		
		
		-- Copy document_template_objects first
		-- Copy document_template_objects START
		CREATE TEMP TABLE document_template_objects_temp (
			LIKE pwt.document_template_objects,
			new_id bigint
		);
		
		INSERT INTO document_template_objects_temp
			SELECT * 
			FROM pwt.document_template_objects
			WHERE document_id = pDocumentId;
		
		SELECT INTO lStatement
			'SELECT ' || array_to_string(ARRAY(SELECT 'o' || '.' || c.column_name
				FROM information_schema.columns As c
				    WHERE table_name = 'document_template_objects' AND table_schema='pwt'
				    AND  c.column_name NOT IN('new_id')
				    ORDER BY ordinal_position ASC
			    ), ',') 
			|| ' FROM document_template_objects_temp AS o';
		<<lDocumentTemplateObjectsLoop>>
		FOR lDocumentTemplateObjectsRow IN 
			EXECUTE lStatement			
		LOOP
			SELECT INTO lTempId 
				nextval('pwt.document_template_objects_id_seq'::regclass);
			
			lPreviousId = lDocumentTemplateObjectsRow.id;
			lDocumentTemplateObjectsRow.id = lTempId;
			lDocumentTemplateObjectsRow.parent_id = null;
			lDocumentTemplateObjectsRow.document_id = lNewDocumentId;
			
			
			INSERT INTO pwt.document_template_objects 
				SELECT lDocumentTemplateObjectsRow.*;
			
			UPDATE document_template_objects_temp SET
				new_id = lTempId
			WHERE id = lPreviousId;			
		END LOOP lDocumentTemplateObjectsLoop;
		
		-- Update the parent_ids 
		UPDATE 	pwt.document_template_objects o SET
				parent_id = p.id
		FROM pwt.document_template_objects p
		WHERE o.document_id = lNewDocumentId AND p.document_id = o.document_id AND char_length(o.pos) = char_length(p.pos) + 2 
			AND substring(o.pos, 1, char_length(p.pos)) = p.pos;
		
		-- Copy document_template_objects END
		
		-- Copy document_object_instances START	
		CREATE TEMP TABLE document_object_instances_temp (
			LIKE pwt.document_object_instances,
			new_id bigint
		);
		
		INSERT INTO document_object_instances_temp
			SELECT * 
			FROM pwt.document_object_instances
			WHERE document_id = pDocumentId;
		
		SELECT INTO lStatement
			'SELECT ' || array_to_string(ARRAY(SELECT 'o' || '.' || c.column_name
				FROM information_schema.columns As c
				    WHERE table_name = 'document_object_instances' AND table_schema='pwt'
				    AND  c.column_name NOT IN('new_id')
				    ORDER BY ordinal_position ASC
			    ), ',') 
			|| ' FROM document_object_instances_temp AS o';
		
		<<lDocumentObjectInstancesLoop>>
		FOR lDocumentObjectInstanceRow IN 
			EXECUTE lStatement			
		LOOP
			SELECT INTO lTempId 
				nextval('pwt.document_object_instances_id_seq'::regclass);
			
			lPreviousId = lDocumentObjectInstanceRow.id;
			lDocumentObjectInstanceRow.id = lTempId;
			lDocumentObjectInstanceRow.parent_id = null;
			lDocumentObjectInstanceRow.is_modified = true;
			lDocumentObjectInstanceRow.cached_xml = null;
			lDocumentObjectInstanceRow.document_id = lNewDocumentId;
			
			
			INSERT INTO pwt.document_object_instances 
				SELECT lDocumentObjectInstanceRow.*;
			
			UPDATE document_object_instances_temp SET
				new_id = lTempId
			WHERE id = lPreviousId;			
		END LOOP lDocumentObjectInstancesLoop;
		
		-- Update the parent_id
		UPDATE pwt.document_object_instances i SET
			parent_id = p.new_id
		FROM document_object_instances_temp t
		JOIN document_object_instances_temp p ON p.id = t.parent_id
		WHERE i.document_id = lNewDocumentId AND t.new_id = i.id;
		
		--Update the document_template_object_id
		UPDATE pwt.document_object_instances i SET
			document_template_object_id = t.new_id
		FROM document_template_objects_temp t
		WHERE i.document_id = lNewDocumentId AND t.id = i.document_template_object_id;
		
		-- Copy document_object_instances END	
		
		-- Copy instance_field_values START	
		CREATE TEMP TABLE instance_field_values_temp (
			LIKE pwt.instance_field_values
		);
		
		INSERT INTO instance_field_values_temp 
			SELECT * 
			FROM pwt.instance_field_values
			WHERE document_id = pDocumentId;
			
		
		UPDATE instance_field_values_temp t SET
			document_id = lNewDocumentId,
			instance_id  = t1.new_id
		FROM document_object_instances_temp t1
		WHERE t.instance_id = t1.id;
		
		INSERT INTO pwt.instance_field_values 
			SELECT *
			FROM instance_field_values_temp;		
		-- Copy instance_field_values END	
		
		-- Copy document_users START	
		CREATE TEMP TABLE document_users_temp (
			LIKE pwt.document_users
		);
		INSERT INTO document_users_temp 
			SELECT * 
			FROM pwt.document_users
			WHERE document_id = pDocumentId;
		
		UPDATE document_users_temp t SET
			document_id = lNewDocumentId
		;
		
		INSERT INTO pwt.document_users 
			SELECT *
			FROM document_users_temp;		
		-- Copy document_users END	
		
		-- Copy document_revisions START	
		CREATE TEMP TABLE document_revisions_temp (
			LIKE pwt.document_revisions,
			new_id bigint
		);
		
		INSERT INTO document_revisions_temp
			SELECT * 
			FROM pwt.document_revisions
			WHERE document_id = pDocumentId;
		
		SELECT INTO lStatement
			'SELECT ' || array_to_string(ARRAY(SELECT 'o' || '.' || c.column_name
				FROM information_schema.columns As c
				    WHERE table_name = 'document_revisions' AND table_schema='pwt'
				    AND  c.column_name NOT IN('new_id')
				    ORDER BY ordinal_position ASC
			    ), ',') 
			|| ' FROM document_revisions_temp AS o';
		
		<<lDocumentRevisionsLoop>>
		FOR lDocumentRevisionRow IN 
			EXECUTE lStatement			
		LOOP
			SELECT INTO lTempId 
				nextval('pwt.document_revisions_id_seq'::regclass);
			
			lPreviousId = lDocumentRevisionRow.id;
			lDocumentRevisionRow.id = lTempId;			
			lDocumentRevisionRow.document_id = lNewDocumentId;
			
			
			INSERT INTO pwt.document_revisions 
				SELECT lDocumentRevisionRow.*;
			
			UPDATE document_revisions_temp SET
				new_id = lTempId
			WHERE id = lPreviousId;			
		END LOOP lDocumentRevisionsLoop;		
		-- Copy document_revisions END	
		
		-- Copy msg START	
		CREATE TEMP TABLE msg_temp (
			LIKE pwt.msg,
			new_id bigint
		);
		
		INSERT INTO msg_temp
			SELECT * 
			FROM pwt.msg
			WHERE document_id = pDocumentId;
		
		SELECT INTO lStatement
			'SELECT ' || array_to_string(ARRAY(SELECT 'o' || '.' || c.column_name
				FROM information_schema.columns As c
				    WHERE table_name = 'msg' AND table_schema='pwt'
				    AND  c.column_name NOT IN('new_id')
				    ORDER BY ordinal_position ASC
			    ), ',') 
			|| ' FROM msg_temp AS o';
		
		<<lMsgLoop>>
		FOR lMsgRow IN 
			EXECUTE lStatement			
		LOOP
			SELECT INTO lTempId 
				nextval('pwt.msg_id_seq'::regclass);
			
			lPreviousId = lMsgRow.id;
			lMsgRow.id = lTempId;			
			lMsgRow.document_id = lNewDocumentId;
			
			
			INSERT INTO pwt.msg 
				SELECT lMsgRow.*;
			
			UPDATE msg_temp SET
				new_id = lTempId
			WHERE id = lPreviousId;			
		END LOOP lMsgLoop;
		
		-- Update the rootid
		UPDATE pwt.msg i SET
			rootid = t.new_id
		FROM msg_temp t
		WHERE i.document_id = lNewDocumentId AND t.id = i.rootid;
		
		-- Update the root_object_instance_id
		UPDATE pwt.msg i SET
			root_object_instance_id = t.new_id
		FROM document_object_instances_temp t
		WHERE i.document_id = lNewDocumentId AND t.id = i.root_object_instance_id;
		
		-- Update the start_object_instances_id
		UPDATE pwt.msg i SET
			start_object_instances_id = t.new_id
		FROM document_object_instances_temp t
		WHERE i.document_id = lNewDocumentId AND t.id = i.start_object_instances_id;
		
		-- Update the end_object_instances_id
		UPDATE pwt.msg i SET
			end_object_instances_id = t.new_id
		FROM document_object_instances_temp t
		WHERE i.document_id = lNewDocumentId AND t.id = i.end_object_instances_id;
		
		--Update the revision_id
		UPDATE pwt.msg i SET
			revision_id = t.new_id
		FROM document_revisions_temp t
		WHERE i.document_id = lNewDocumentId AND t.id = i.revision_id;		
		-- Copy msg END	
		
		-- Copy plates START	
		CREATE TEMP TABLE plates_temp (
			LIKE pwt.plates,
			new_id bigint
		);
		
		INSERT INTO plates_temp
			SELECT * 
			FROM pwt.plates
			WHERE document_id = pDocumentId;
		
		SELECT INTO lStatement
			'SELECT ' || array_to_string(ARRAY(SELECT 'o' || '.' || c.column_name
				FROM information_schema.columns As c
				    WHERE table_name = 'plates' AND table_schema='pwt'
				    AND  c.column_name NOT IN('new_id')
				    ORDER BY ordinal_position ASC
			    ), ',') 
			|| ' FROM plates_temp AS o';
		
		<<lPlatesLoop>>
		FOR lPlateRow IN 
			EXECUTE lStatement			
		LOOP
			SELECT INTO lTempId 
				nextval('pwt.plates_id_seq'::regclass);
			
			lPreviousId = lPlateRow.id;
			lPlateRow.id = lTempId;			
			lPlateRow.document_id = lNewDocumentId;
			
			
			INSERT INTO pwt.plates 
				SELECT lPlateRow.*;
			
			UPDATE plates_temp SET
				new_id = lTempId
			WHERE id = lPreviousId;			
		END LOOP lPlatesLoop;
				
		-- Copy plates END	
		
		-- Copy tables START	
		CREATE TEMP TABLE tables_temp (
			LIKE pwt.tables,
			new_id bigint
		);
		
		INSERT INTO tables_temp
			SELECT * 
			FROM pwt.tables
			WHERE document_id = pDocumentId;
		
		SELECT INTO lStatement
			'SELECT ' || array_to_string(ARRAY(SELECT 'o' || '.' || c.column_name
				FROM information_schema.columns As c
				    WHERE table_name = 'tables' AND table_schema='pwt'
				    AND  c.column_name NOT IN('new_id')
				    ORDER BY ordinal_position ASC
			    ), ',') 
			|| ' FROM tables_temp AS o';
		
		<<lTablesLoop>>
		FOR lTableRow IN 
			EXECUTE lStatement			
		LOOP
			SELECT INTO lTempId 
				nextval('pwt.tables_id_seq'::regclass);
			
			lPreviousId = lTableRow.id;
			lTableRow.id = lTempId;			
			lTableRow.document_id = lNewDocumentId;
			
			
			INSERT INTO pwt.tables 
				SELECT lTableRow.*;
			
			UPDATE tables_temp SET
				new_id = lTempId
			WHERE id = lPreviousId;			
		END LOOP lTablesLoop;
		
		-- Copy tables END	
		
		-- Copy media START	
		CREATE TEMP TABLE media_temp (
			LIKE pwt.media,
			new_id bigint
		);
		
		INSERT INTO media_temp
			SELECT * 
			FROM pwt.media
			WHERE document_id = pDocumentId;
		
		SELECT INTO lStatement
			'SELECT ' || array_to_string(ARRAY(SELECT 'o' || '.' || c.column_name
				FROM information_schema.columns As c
				    WHERE table_name = 'media' AND table_schema='pwt'
				    AND  c.column_name NOT IN('new_id')
				    ORDER BY ordinal_position ASC
			    ), ',') 
			|| ' FROM media_temp AS o';
		
		<<lMediaLoop>>
		FOR lMediaRow IN 
			EXECUTE lStatement			
		LOOP
			SELECT INTO lTempId 
				nextval('stories_guid_seq'::regclass);
			
			lPreviousId = lMediaRow.id;
			lMediaRow.id = lTempId;			
			lMediaRow.document_id = lNewDocumentId;
			
			
			INSERT INTO pwt.media 
				SELECT lMediaRow.*;
			
			UPDATE media_temp SET
				new_id = lTempId
			WHERE id = lPreviousId;			
		END LOOP lMediaLoop;
		
		-- Update the plate_id
		UPDATE pwt.media i SET
			plate_id = t.new_id
		FROM plates_temp t
		WHERE i.document_id = lNewDocumentId AND t.id = i.plate_id;	
		-- Copy media END	
		
		-- Copy citations START	
		CREATE TEMP TABLE citations_temp (
			LIKE pwt.citations,
			new_id bigint
		);
		
		INSERT INTO citations_temp
			SELECT * 
			FROM pwt.citations
			WHERE document_id = pDocumentId;
		
		SELECT INTO lStatement
			'SELECT ' || array_to_string(ARRAY(SELECT 'o' || '.' || c.column_name
				FROM information_schema.columns As c
				    WHERE table_name = 'citations' AND table_schema='pwt'
				    AND  c.column_name NOT IN('new_id')
				    ORDER BY ordinal_position ASC
			    ), ',') 
			|| ' FROM citations_temp AS o';
		
		<<lCitationsLoop>>
		FOR lCitationRow IN 
			EXECUTE lStatement			
		LOOP
			SELECT INTO lTempId 
				nextval('pwt.citations_id_seq'::regclass);
			
			lPreviousId = lCitationRow.id;
			lCitationRow.id = lTempId;			
			lCitationRow.document_id = lNewDocumentId;
			
			
			INSERT INTO pwt.citations 
				SELECT lCitationRow.*;
			
			UPDATE citations_temp SET
				new_id = lTempId
			WHERE id = lPreviousId;			
		END LOOP lCitationsLoop;
		
		-- Update the instance_id
		UPDATE pwt.citations i SET
			instance_id = t.new_id
		FROM document_object_instances_temp t
		WHERE i.document_id = lNewDocumentId AND t.id = i.instance_id;		
		-- Copy citations END	
		
		-- Fix the citation ids in the text
		<<CitationsIdFixLoop>>
		FOR lRecord IN 
			SELECT * 
			FROM pwt.citations
			WHERE document_id = lNewDocumentId
		LOOP
			SELECT INTO lPreviousId 
				id
			FROM citations_temp 
			WHERE new_id = lRecord.id;
				
			UPDATE pwt.instance_field_values SET
				value_str = replace(value_str, 'citation_id="' || lPreviousId || '"', 'citation_id="' || lRecord.id || '"')
			WHERE instance_id = lRecord.instance_id AND field_id = lRecord.field_id;	
			
			lCitationObjectIds = ARRAY[]::bigint[];
			IF lRecord.citation_type IN (lReferenceCitationType, lSupFileCitationType, lFigCitationType, lTableCitationType) THEN
				<<lCitationInstanceItemsLoop>>
				FOR lCitationItemsRecord IN
					SELECT * 
					FROM document_object_instances_temp
					WHERE id = ANY (lRecord.object_ids)
				LOOP
					lCitationObjectIds = array_append(lCitationObjectIds, lCitationItemsRecord.new_id);
				END LOOP lCitationInstanceItemsLoop;
			/*ELSEIF lRecord.citation_type = lFigCitationType THEN
				<<lCitationFigItemsLoop>>
				FOR lCitationItemsRecord IN
					SELECT * 
					FROM media_temp
					WHERE id = ANY (lRecord.object_ids)
				LOOP
					lCitationObjectIds = array_append(lCitationObjectIds, lCitationItemsRecord.new_id);
				END LOOP lCitationFigItemsLoop;
			ELSEIF lRecord.citation_type = lTableCitationType THEN
				<<lCitationTableItemsLoop>>
				FOR lCitationItemsRecord IN
					SELECT * 
					FROM tables_temp
					WHERE id = ANY (lRecord.object_ids)
				LOOP
					lCitationObjectIds = array_append(lCitationObjectIds, lCitationItemsRecord.new_id);
				END LOOP lCitationTableItemsLoop;
			*/
			END IF;
			UPDATE pwt.citations SET
				object_ids = lCitationObjectIds,
				is_dirty = true
			WHERE id = lRecord.id AND document_id = lNewDocumentId;
			
			
		END LOOP CitationsIdFixLoop;
					
		lRes.document_id = lNewDocumentId;
		
		-- We wont drop the temp tables here because we need them in the php part of the copy.
		-- We will drop them there
		-- We will change their owner so that they can be dropped
		ALTER TABLE document_template_objects_temp OWNER TO iusrpmt;
		ALTER TABLE document_object_instances_temp OWNER TO iusrpmt;
		ALTER TABLE instance_field_values_temp OWNER TO iusrpmt;
		ALTER TABLE document_revisions_temp OWNER TO iusrpmt;
		ALTER TABLE msg_temp OWNER TO iusrpmt;
		ALTER TABLE document_users_temp OWNER TO iusrpmt;
		ALTER TABLE plates_temp OWNER TO iusrpmt;
		ALTER TABLE media_temp OWNER TO iusrpmt;
		ALTER TABLE tables_temp OWNER TO iusrpmt;
		ALTER TABLE citations_temp OWNER TO iusrpmt; 
		
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCopyDocument(	
	pDocumentId int
) TO iusrpmt;
