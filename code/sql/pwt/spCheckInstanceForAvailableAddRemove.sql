DROP TYPE ret_spCheckInstanceForAvailableAddRemove CASCADE;
CREATE TYPE ret_spCheckInstanceForAvailableAddRemove AS (
	allow_add int,
	allow_remove int
);

CREATE OR REPLACE FUNCTION spCheckInstanceForAvailableAddRemove(
	pInstanceId bigint
)
  RETURNS ret_spCheckInstanceForAvailableAddRemove AS
$BODY$
	DECLARE
		lRes ret_spCheckInstanceForAvailableAddRemove;	
		lObjectId bigint;
		lParentObjectId bigint;
		lPos varchar;
		lAllowedMinCount int;
		lAllowedMaxCount int;
		lCurrentInstanceCntOfThisType int;
		lDocumentId int;
		lAllowAdd boolean;
		lAllowRemove boolean;
		lParentInstanceId bigint;
		lParentIsConfirmed boolean;
	BEGIN
		lRes.allow_add = 0;
		lRes.allow_remove = 0;
		
		SELECT INTO lPos, lDocumentId, lObjectId, lParentObjectId, lAllowAdd, lAllowRemove, lParentInstanceId, lParentIsConfirmed
			i.pos, i.document_id, i.object_id, p.object_id, dto.allow_add, dto.allow_remove, p.id, p.is_confirmed
		FROM pwt.document_object_instances i
		JOIN pwt.document_object_instances p ON p.id = i.parent_id
		JOIN pwt.document_template_objects dto ON dto.id = i.document_template_object_id
		WHERE i.id = pInstanceId;
		
		IF lParentObjectId IS NULL THEN --Това е обект от 1во ниво - не може да добавяме/трием
			RETURN lRes;
		END IF;
		
		SELECT INTO lCurrentInstanceCntOfThisType count(*) 
		FROM pwt.document_object_instances 
		WHERE object_id = lObjectId AND parent_id = lParentInstanceId AND (lParentIsConfirmed = false OR is_confirmed = true);
		
		SELECT INTO lAllowedMinCount, lAllowedMaxCount min_occurrence, max_occurrence
		FROM pwt.object_subobjects
		WHERE object_id = lParentObjectId AND subobject_id = lObjectId;
		
		IF lAllowRemove = true AND lAllowedMinCount < lCurrentInstanceCntOfThisType THEN
			lRes.allow_remove = 1;
		END IF;
		
		IF lAllowAdd = true AND lAllowedMaxCount > lCurrentInstanceCntOfThisType THEN
			lRes.allow_add = 1;
		END IF;
		
		

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCheckInstanceForAvailableAddRemove(
	pInstanceId bigint
) TO iusrpmt;
