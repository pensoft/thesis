DROP TYPE IF EXISTS ret_spGetInstanceDisplayInTree CASCADE;
CREATE TYPE ret_spGetInstanceDisplayInTree AS (instance_id bigint);

DROP FUNCTION IF EXISTS pwt."spGetInstanceDisplayInTree"(pInstanceId bigint);

CREATE OR REPLACE FUNCTION pwt."spGetInstanceDisplayInTree"(pInstanceId bigint)
  RETURNS ret_spGetInstanceDisplayInTree AS
$BODY$
	DECLARE
		lRes ret_spGetInstanceDisplayInTree;
		lDisplayInTree boolean;
		lParentInstanceId bigint;
	BEGIN
		
		SELECT INTO lDisplayInTree display_in_tree FROM pwt.document_object_instances WHERE id = pInstanceId;
		
		IF(lDisplayInTree = TRUE) THEN
			lRes.instance_id = pInstanceId;
			RETURN lRes;
		ELSE
			SELECT INTO lParentInstanceId parent_id FROM pwt.document_object_instances WHERE id = pInstanceId;
			IF(lParentInstanceId IS NULL) THEN
				lRes.instance_id = pInstanceId;
				RETURN lRes;
			ELSE
				SELECT INTO lRes.instance_id instance_id FROM pwt."spGetInstanceDisplayInTree"(lParentInstanceId);
			END IF;
		END IF;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pwt."spGetInstanceDisplayInTree"(	
	pInstanceId bigint
) TO iusrpmt;
