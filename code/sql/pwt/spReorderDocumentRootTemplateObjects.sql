DROP TYPE ret_spReorderDocumentRootTemplateObjects CASCADE;
CREATE TYPE ret_spReorderDocumentRootTemplateObjects AS (
	result int
);

CREATE OR REPLACE FUNCTION spReorderDocumentRootTemplateObjects(
	pObjectId bigint,
	pDocumentId bigint,
	pUid int
)
  RETURNS ret_spReorderDocumentRootTemplateObjects AS
$BODY$
DECLARE
	lRes ret_spReorderDocumentRootTemplateObjects;
	lRecord2 record;
	lTemplateId int;
	lNextChildPos varchar;	
BEGIN
	CREATE TEMP TABLE document_template_objects_ord(
		document_template_object_id bigint,
		pos varchar,
		id serial
	);
	CREATE TEMP TABLE document_objects_instances_ord(
		instance_id bigint,
		pos varchar,
		id serial
	);
	
	SELECT INTO lTemplateId
		template_id 
	FROM pwt.documents WHERE id = pDocumentId;
	
	INSERT INTO document_template_objects_ord(document_template_object_id)
	SELECT dto.id, i.pos as ipos, dto.pos as dpos, CASE WHEN i.id IS NULL THEN 0 ELSE 1 END as ord
		FROM pwt.document_template_objects dto
		LEFT JOIN pwt.template_objects i ON i.object_id = dto.object_id	AND char_length(i.pos) = char_length(dto.pos) AND i.template_id = lTemplateId		
		WHERE char_length(dto.pos) = 2 AND dto.document_id = pDocumentId		
		ORDER BY ord DESC, ipos ASC, dpos ASC;
		
		
		
	lNextChildPos = 'AA';
	<<lSubobjectsOrder>>
	FOR lRecord2 IN 
		SELECT * 
		FROM document_template_objects_ord 
		ORDER BY id ASC
	LOOP
		UPDATE document_template_objects_ord SET 
			pos = lNextChildPos 
		WHERE id = lRecord2.id;
		lNextChildPos = ForumGetNextOrd(lNextChildPos);
	END LOOP lSubobjectsOrder;
	
	UPDATE pwt.document_template_objects t SET
		pos = overlay(t.pos placing o.pos from 1 for char_length(o.pos))
	FROM pwt.document_template_objects p 	
	JOIN document_template_objects_ord o ON o.document_template_object_id = p.id
	WHERE substring(t.pos, 1, char_length(p.pos)) = p.pos AND p.document_id = pDocumentId AND t.document_id = pDocumentId;		

	
	-- Update the positions of the instances
	INSERT INTO document_objects_instances_ord(instance_id)
		SELECT i.id
		FROM pwt.document_object_instances i				
		JOIN pwt.document_template_objects dto ON dto.id = i.document_template_object_id
		WHERE char_length(i.pos) = 2
			AND i.document_id = pDocumentId
		ORDER BY dto.pos ASC, i.pos ASC;
	
	lNextChildPos = 'AA';
	 <<lSubInstancesOrder>>
	FOR lRecord2 IN 
		SELECT * 
		FROM document_objects_instances_ord 
		ORDER BY id ASC
	LOOP
		UPDATE document_objects_instances_ord SET 
			pos = lNextChildPos 
		WHERE id = lRecord2.id;
		lNextChildPos = ForumGetNextOrd(lNextChildPos);
	END LOOP lSubInstancesOrder;
	
		
	UPDATE pwt.document_object_instances t SET
		pos = overlay(t.pos placing o.pos from 1 for char_length(o.pos))
	FROM pwt.document_object_instances p 	
	JOIN document_objects_instances_ord o ON o.instance_id = p.id
	WHERE substring(t.pos, 1, char_length(p.pos)) = p.pos AND p.document_id = pDocumentId AND t.document_id = pDocumentId;
	
	
	lRes.result = 1;	
	RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spReorderDocumentRootTemplateObjects(
	pObjectId bigint,
	pDocumentId bigint,
	pUid int
) TO iusrpmt;
