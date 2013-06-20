DROP TYPE ret_spGetDocumentDataByInstance CASCADE;
CREATE TYPE ret_spGetDocumentDataByInstance AS (
	document_id bigint,
	display_instance_in_tree int,
	root_instance_id int,
	document_name varchar,
	lock_usr_id bigint,
	is_locked int,
	xsl_dir_name varchar,
	document_is_readonly int,
	document_has_unprocessed_changes int,
	document_xml xml
);

CREATE OR REPLACE FUNCTION spGetDocumentDataByInstance(
	pInstanceId bigint
)
  RETURNS ret_spGetDocumentDataByInstance AS
$BODY$
	DECLARE
		lRes ret_spGetDocumentDataByInstance;		
	BEGIN
		SELECT INTO lRes i.document_id, CASE WHEN char_length(i.pos) > 2 THEN i.display_in_tree::int ELSE 1 END, null, d.name, d.lock_usr_id, d.is_locked::int, tem.xsl_dir_name
		FROM pwt.document_object_instances i
		JOIN pwt.documents d ON d.id = i.document_id
		LEFT JOIN pwt.document_template_objects dto ON dto.id = i.document_template_object_id
		LEFT JOIN pwt.templates tem ON tem.id = dto.template_id
		WHERE i.id = pInstanceId;
		
		SELECT INTO lRes.root_instance_id id 
		FROM pwt.document_object_instances
		WHERE document_id = lRes.document_id AND char_length(pos) = 2 
		ORDER BY pos ASC LIMIT 1;
		
		SELECT INTO lRes.document_is_readonly, lRes.document_has_unprocessed_changes, lRes.document_xml
			s.is_readonly::int, d.has_unprocessed_changes::int, d.doc_xml
		FROM pwt.document_states s
		JOIN pwt.documents d ON d.state = s.id
		WHERE d.id = lRes.document_id;

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetDocumentDataByInstance(
	pInstanceId bigint
) TO iusrpmt;
