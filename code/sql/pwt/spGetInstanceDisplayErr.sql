DROP TYPE IF EXISTS ret_spGetInstanceDisplayErr CASCADE;
CREATE TYPE ret_spGetInstanceDisplayErr AS (instance_id bigint);

DROP FUNCTION IF EXISTS pwt."spGetInstanceDisplayErr"(pInstanceId bigint);

CREATE OR REPLACE FUNCTION pwt."spGetInstanceDisplayErr"(pInstanceId bigint)
  RETURNS ret_spGetInstanceDisplayErr AS
$BODY$
	DECLARE
		lRes ret_spGetInstanceDisplayErr;
		lDisplayErr boolean;
		lParentInstanceId bigint;
	BEGIN
		
		SELECT INTO lDisplayErr display_err FROM pwt.document_object_instances WHERE id = pInstanceId;
		
		IF(lDisplayErr = TRUE) THEN
			lRes.instance_id = pInstanceId;
			RETURN lRes;
		ELSE
			SELECT INTO lParentInstanceId parent_id FROM pwt.document_object_instances WHERE id = pInstanceId;
			IF(lParentInstanceId IS NULL) THEN
				lRes.instance_id = pInstanceId;
				RETURN lRes;
			ELSE
				SELECT INTO lRes.instance_id instance_id FROM pwt."spGetInstanceDisplayErr"(lParentInstanceId);
			END IF;
		END IF;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pwt."spGetInstanceDisplayErr"(	
	pInstanceId bigint
) TO iusrpmt;
