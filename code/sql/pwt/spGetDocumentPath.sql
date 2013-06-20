DROP TYPE ret_spGetDocumentPath CASCADE;
CREATE TYPE ret_spGetDocumentPath AS (
	instance_id bigint,
	object_name varchar	
);

CREATE OR REPLACE FUNCTION spGetDocumentPath(
	pCurrentInstanceId bigint
)
  RETURNS SETOF ret_spGetDocumentPath AS
$BODY$
	DECLARE
		lRes ret_spGetDocumentPath;		
		lRecord RECORD;
		lCurrentInstancePos varchar;
		lCurrentPos varchar;
		lDocumentId int;
	BEGIN
		SELECT INTO lCurrentInstancePos, lDocumentId pos, document_id FROM pwt.document_object_instances WHERE id = pCurrentInstanceId;
		
		FOR lRes IN 
			SELECT i.id, i.display_name as name 
			FROM pwt.document_object_instances i
			JOIN pwt.objects o ON o.id = i.object_id
			WHERE i.document_id = lDocumentId AND char_length(i.pos) <= char_length(lCurrentInstancePos)
				AND i.pos = substring(lCurrentInstancePos, 1, char_length(i.pos)) AND (i.display_in_tree = true OR char_length(i.pos) = 2)
			ORDER BY pos ASC
		LOOP
			RETURN NEXT lRes;
		END LOOP;
			
		RETURN;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetDocumentPath(
	pCurrentInstanceId bigint
) TO iusrpmt;
