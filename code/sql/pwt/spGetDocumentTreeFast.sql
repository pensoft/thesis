DROP TYPE ret_spGetDocumentTreeFast CASCADE;
CREATE TYPE ret_spGetDocumentTreeFast AS (
	instance_id bigint,
	object_id integer,
	parent_object_id integer,
	object_name character varying,
	has_children integer,
	pos character varying,
	"level" integer,
	is_active integer,
	has_warning integer,
	parent_instance_id bigint,
	parent_instance_name character varying,
	num_children integer,
	documentstate integer,
	document_papertype character varying
);
ALTER TYPE ret_spGetDocumentTreeFast OWNER TO postgres;


CREATE OR REPLACE FUNCTION spGetDocumentTreeFast(
	pDocumentId integer, 
	pCurrentInstanceId bigint
) RETURNS SETOF ret_spGetDocumentTreeFast AS
	$BODY$
		DECLARE
			lRes ret_spGetDocumentTreeFast;
			lRecord RECORD;
			lCurrentInstancePos varchar;
		BEGIN			
			SELECT INTO lRes.documentstate, lRes.document_papertype  d.state, p.name
			FROM pwt.documents d
			JOIN pwt.papertypes p ON (d.papertype_id = p.id)
			WHERE d.id = pDocumentId;
			
			FOR lRecord IN 
				SELECT i.id, i.pos, i.object_id, i.display_name as object_name, i.parent_id, p.object_id as parent_object_id, p.display_name as parent_instance_name
				FROM pwt.document_object_instances i
				LEFT JOIN pwt.document_object_instances p ON p.id = i.parent_id
				WHERE i.document_id = pDocumentId AND i.display_in_tree = true
					AND i.is_confirmed = true
				ORDER BY pos ASC
			LOOP
				lRes.instance_id = lRecord.id;
				lRes.object_id = lRecord.object_id;
				lRes.object_name = lRecord.object_name;
				lRes.pos = lRecord.pos;
				lRes.level = char_length(lRecord.pos) / 2;
				lRes.has_warning = 0;
				lRes.parent_instance_id =  coalesce(lRecord.parent_id, 0);
				lRes.parent_object_id =  lRecord.parent_object_id;
				lRes.parent_instance_name = lRecord.parent_instance_name;
				
				lRes.is_active = 0;
				IF pCurrentInstanceId = lRes.instance_id THEN
					lRes.is_active = 1;
				END IF;			
				
				lRes.num_children = 0;
				lRes.has_children = 0;
				
				IF EXISTS (
					SELECT * 
					FROM pwt.document_object_instances 
					WHERE document_id = pDocumentId  
						AND parent_id = lRes.instance_id 
						AND is_confirmed = true AND display_in_tree = true 
					LIMIT 1
				) THEN
					lRes.num_children = 1;
					lRes.has_children = 1;
				END IF;

				
				/*
				IF lRes.num_children > 0 THEN
					IF EXISTS (
						SELECT *
						FROM pwt.document_object_instances 
						WHERE document_id = pDocumentId 		
							AND display_in_tree = true 
							AND pos like lRecord.pos || '__%'  				  
							AND is_confirmed = true
						LIMIT 1
					) THEN
						lRes.has_children = 1;
					END IF;				
				END IF;*/
				RETURN NEXT lRes;
			END LOOP;		

			RETURN;
		END
	$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER;
  
GRANT EXECUTE ON FUNCTION spGetDocumentTreeFast(
	pDocumentId integer, 
	pCurrentInstanceId bigint
) TO iusrpmt;
