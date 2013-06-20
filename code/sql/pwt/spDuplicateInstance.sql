DROP TYPE ret_spDuplicateInstance CASCADE;
CREATE TYPE ret_spDuplicateInstance AS (
	new_instance_id bigint,
	parent_instance_id bigint,
	display_in_tree int,
	container_id bigint
);

CREATE OR REPLACE FUNCTION spDuplicateInstance(
	pInstanceId bigint,
	pUid int	
)
  RETURNS ret_spDuplicateInstance AS
$BODY$
	DECLARE
		lRes ret_spDuplicateInstance;			
		lParentInstanceId bigint;
		lObjectId bigint;
		lDocumentId int;
	BEGIN
		SELECT INTO lParentInstanceId, lObjectId, lDocumentId
			parent_id, object_id, document_id
		FROM pwt.document_object_instances 
		WHERE id = pInstanceId;
		
		IF lParentInstanceId IS NULL THEN
			RAISE EXCEPTION 'pwt.cannotDuplicateRootInstances';
		END IF;
		
		BEGIN
			SELECT INTO lRes
				new_instance_id, parent_instance_id, display_in_tree, container_id
			FROM spCreateNewInstance(lParentInstanceId, lObjectId, pUid);			
			
			PERFORM spEquateInstances(pInstanceId, lRes.new_instance_id, pUid);
			PERFORM pwt."XmlIsDirty"(1, lDocumentId, lParentInstanceId);
			EXCEPTION WHEN raise_exception THEN			
				RAISE EXCEPTION USING MESSAGE = SQLERRM;
		END;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spDuplicateInstance(
	pInstanceId bigint,
	pUid int
) TO iusrpmt;
