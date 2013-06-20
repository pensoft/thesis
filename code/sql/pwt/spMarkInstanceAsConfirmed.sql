DROP FUNCTION pwt.spMarkInstanceAsConfirmed(
	pInstanceId bigint,
	pUid int
);

CREATE OR REPLACE FUNCTION pwt.spMarkInstanceAsConfirmed(
	pInstanceId bigint,
	pUid int
)
  RETURNS int AS
$BODY$
		DECLARE		
			lAllowedMinCount int;
			lAllowedMaxCount int;
			lCurrentInstanceCntOfThisType int;
			lParentInstanceId bigint;
			lParentObjectId bigint;
			lObjectId bigint;
			lIsConfirmed boolean;
			lParentIsConfirmed boolean;
		BEGIN	
			SELECT INTO lParentInstanceId, lObjectId, lParentObjectId, lIsConfirmed, lParentIsConfirmed
				i.parent_id, i.object_id, p.object_id, i.is_confirmed, p.is_confirmed
			FROM pwt.document_object_instances i
			JOIN pwt.document_object_instances p ON p.id = i.parent_id			
			WHERE i.id = pInstanceId;
			
			IF coalesce(lIsConfirmed, false) = true THEN
				-- If the instance is already confirmed - do nothing
				RETURN 1;
			END IF;
			
			SELECT INTO lCurrentInstanceCntOfThisType 
				count(*) 
			FROM pwt.document_object_instances 
			WHERE object_id = lObjectId AND parent_id = lParentInstanceId AND (lParentIsConfirmed = false OR is_confirmed = true);
			
			SELECT INTO lAllowedMinCount, lAllowedMaxCount 
				min_occurrence, max_occurrence
			FROM pwt.object_subobjects
			WHERE object_id = lParentObjectId AND subobject_id = lObjectId;
			
			-- Check if there arent too many instances of this type
			IF lAllowedMaxCount <= lCurrentInstanceCntOfThisType THEN
				RAISE EXCEPTION 'pwt.instance.thereAreTooManyInstancesOfThisType';
			END IF;
		
			-- Mark the instance and all subinstances down the tree.
			UPDATE pwt.document_object_instances i SET
				is_confirmed = true
			FROM pwt.document_object_instances p
			WHERE p.document_id = i.document_id AND p.id = pInstanceId AND substring(i.pos, 1, char_length(p.pos)) = p.pos;
			
			RETURN 1;
		END
	$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;

GRANT EXECUTE ON FUNCTION pwt.spMarkInstanceAsConfirmed(
	pInstanceId bigint,
	pUid int
) TO iusrpmt;