DROP TYPE ret_spCreateNewReference CASCADE;
CREATE TYPE ret_spCreateNewReference AS (
	new_instance_id bigint,
	parent_instance_id bigint,
	display_in_tree int,
	container_id bigint
);
-- Създаваме нова референция
/**
	В общи линии създаваме обекта стандартно
	Попълваме му типа и му извикваме отново екшъните след създаване
*/
CREATE OR REPLACE FUNCTION spCreateNewReference(
	pParentInstanceId bigint,
	pObjectId bigint,
	pReferenceType int,
	pUid int
)
  RETURNS ret_spCreateNewReference AS
$BODY$
	DECLARE
		lRes ret_spCreateNewReference;					
		lReferenceTypeFieldId bigint;
	BEGIN
		lReferenceTypeFieldId = 269;
		
		SELECT INTO lRes
			new_instance_id, parent_instance_id, display_in_tree, container_id
		FROM spCreateNewInstance(pParentInstanceId, pObjectId, pUid);
		
		PERFORM pwt.spMarkInstanceAsUnconfirmed(lRes.new_instance_id, pUid);
		
		--Ъпдейтваме типа
		UPDATE pwt.instance_field_values SET
			value_int = pReferenceType,
			is_read_only = true
		WHERE field_id = lReferenceTypeFieldId AND instance_id = lRes.new_instance_id;
		
		
		--Изпълняваме екшъните след създаване към този обект
		PERFORM spPerformInstanceAfterCreationActions(lRes.new_instance_id, pUid);
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCreateNewReference(
	pParentInstanceId bigint,
	pObjectId bigint,
	pReferenceType int,
	pUid int
) TO iusrpmt;
