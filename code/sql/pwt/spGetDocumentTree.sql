DROP TYPE ret_spGetDocumentTree CASCADE;
CREATE TYPE ret_spGetDocumentTree AS (
	instance_id bigint,
	object_id int,
	parent_object_id int,
	object_name varchar,
	has_children int,
	pos varchar,
	level int,
	is_active int,
	has_warning int,
	parent_instance_id bigint,
	parent_instance_name varchar,
	num_children int,
	documentstate int,
	document_papertype varchar
);

CREATE OR REPLACE FUNCTION spGetDocumentTree(
	pDocumentId int,
	pCurrentInstanceId bigint
)
  RETURNS SETOF ret_spGetDocumentTree AS
$BODY$
	DECLARE
		lRes ret_spGetDocumentTree;
		lRecord RECORD;
		lCurrentInstancePos varchar;
	BEGIN
		SELECT INTO lCurrentInstancePos pos FROM pwt.document_object_instances WHERE id = pCurrentInstanceId;
		
		SELECT INTO lRes.documentstate, lRes.document_papertype  d.state, p.name
			FROM pwt.documents d
			JOIN pwt.papertypes p on (d.papertype_id = p.id)
			WHERE d.id = pDocumentId;
		
		FOR lRecord IN 
			SELECT i.*, i.display_name as object_name
			FROM pwt.document_object_instances i
			JOIN pwt.objects o ON o.id = i.object_id			
			WHERE i.document_id = pDocumentId AND (i.display_in_tree = true OR char_length(i.pos) = 2) AND i.is_confirmed = true
			ORDER BY pos ASC
		LOOP
			lRes.instance_id = lRecord.id;
			lRes.object_id = lRecord.object_id;
			lRes.object_name = lRecord.object_name;
			lRes.pos = lRecord.pos;
			lRes.level = char_length(lRecord.pos) / 2;
			lRes.has_warning = 0;
			
			SELECT INTO lRes.parent_instance_id, lRes.parent_instance_name, lRes.parent_object_id id , display_name, object_id
			FROM pwt.document_object_instances i
			WHERE document_id = pDocumentId AND char_length(pos) < char_length(lRes.pos) AND substring(lRes.pos, 1, char_length(pos)) = pos AND display_in_tree = true
			ORDER BY pos DESC LIMIT 1;
			lRes.parent_instance_id = coalesce(lRes.parent_instance_id, 0);
			
			lRes.is_active = 0;			
			
			IF lRes.level <= char_length(lCurrentInstancePos) THEN
				--IF substring(lCurrentInstancePos, 1, char_length(lRes.pos) ) = lRes.pos THEN
				IF lCurrentInstancePos = lRes.pos THEN
					lRes.is_active = 1;
				END IF;
			END IF;
			
			SELECT INTO lRes.has_children count(*) 
			FROM pwt.document_object_instances 
			WHERE document_id = pDocumentId AND display_in_tree = true AND char_length(pos) > char_length(lRecord.pos) AND substring(pos, 1, char_length(lRecord.pos) ) = lRecord.pos  AND is_confirmed = true;
			
			SELECT INTO lRes.num_children count(*) 
			FROM pwt.document_object_instances 
			WHERE document_id = pDocumentId AND parent_id = lRes.instance_id AND is_confirmed = true;
			
			IF lRes.has_children > 0 THEN
				lRes.has_children = 1;
			END IF;
			RETURN NEXT lRes;
		END LOOP;		

		RETURN;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetDocumentTree(
	pDocumentId int,
	pCurrentInstanceId bigint
) TO iusrpmt;
