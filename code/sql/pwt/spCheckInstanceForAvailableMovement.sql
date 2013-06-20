DROP TYPE ret_spCheckInstanceForAvailableMovement CASCADE;
CREATE TYPE ret_spCheckInstanceForAvailableMovement AS (
	up int,
	down int
);

CREATE OR REPLACE FUNCTION spCheckInstanceForAvailableMovement(
	pInstanceId bigint
)
  RETURNS ret_spCheckInstanceForAvailableMovement AS
$BODY$
	DECLARE
		lRes ret_spCheckInstanceForAvailableMovement;	
		lPos varchar;
		lObjectId bigint;
		lDocumentId int;
		lPreviousId bigint;
		lNextId bigint;
		lAllowMove boolean;
		lParentId bigint;
		lParentIsConfirmed boolean;
	BEGIN
		lRes.up = 0;
		lRes.down = 0;
		
		SELECT INTO lParentId, lPos, lDocumentId, lObjectId, lAllowMove, lParentIsConfirmed
			i.parent_id, i.pos, i.document_id, i.object_id, dto.allow_movement, p.is_confirmed
		FROM pwt.document_object_instances i
		JOIN pwt.document_template_objects dto ON dto.id = i.document_template_object_id
		JOIN pwt.document_object_instances p ON p.id = i.parent_id
		WHERE i.id = pInstanceId;
		
		IF lAllowMove = false THEN
			RETURN lRes;
		END IF;
		
		SELECT INTO lPreviousId id 
		FROM pwt.document_object_instances
		WHERE document_id = lDocumentId AND object_id = lObjectId AND pos < lPos AND parent_id = lParentId AND (lParentIsConfirmed = false OR is_confirmed = true)
		ORDER BY pos DESC LIMIT 1;
		
		SELECT INTO lNextId id 
		FROM pwt.document_object_instances
		WHERE document_id = lDocumentId AND object_id = lObjectId AND pos > lPos AND parent_id = lParentId AND (lParentIsConfirmed = false OR is_confirmed = true)
		ORDER BY pos ASC LIMIT 1;
		
		IF lPreviousId IS NOT NULL THEN
			lRes.up = 1;
		END IF;
		
		IF lNextId IS NOT NULL THEN
			lRes.down = 1;
		END IF;

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCheckInstanceForAvailableMovement(
	pInstanceId bigint
) TO iusrpmt;
