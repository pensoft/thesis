DROP TYPE ret_spObjectSubobject CASCADE;
CREATE TYPE ret_spObjectSubobject AS (
	id bigint,
	object_id bigint,
	subobject_id bigint,	
	min_occurrence int,
	initial_occurrence int,
	max_occurrence int
);

CREATE OR REPLACE FUNCTION spObjectSubobject(
	pOper int,
	pId bigint,
	pObjectId bigint,
	pSubobjectId bigint,
	pMinOccurrence int,
	pMaxOccurrence int,
	pInitialOccurrence int,
	pUid int
)
  RETURNS ret_spObjectSubobject AS
$BODY$
DECLARE
lRes ret_spObjectSubobject;
--lSid int;
lCurTime timestamp;
lId bigint;
lSubObjectId bigint;
lObjectId bigint;
lObjectCanBeModified int;
lHierarchyDependanceExists int;
BEGIN

	lId = pId;
	IF pOper = 1 THEN -- Insert/Update
		SELECT INTO lObjectCanBeModified result FROM spCheckIfObjectCanBeModified(pObjectId);
		IF lObjectCanBeModified = 0 THEN
			RAISE EXCEPTION 'pwt_admin.objects.cantModifyThisObject';
		END IF;
			
		
		IF lId IS NULL THEN --Insert
			IF pObjectId = pSubobjectId THEN
				RAISE EXCEPTION 'pwt_admin.objects.objectsCantBeSubobjectsOfThemselves';
			END IF;
			
			SELECT INTO lHierarchyDependanceExists result FROM spCheckForObjectHierarchyDependance(pObjectId, pSubobjectId);
			
			IF lHierarchyDependanceExists = 1 THEN
				RAISE EXCEPTION 'pwt_admin.objects.subobjectCyclesAreNotAllowed';
			END IF;
		
			INSERT INTO object_subobjects(object_id, subobject_id, min_occurrence, initial_occurrence, max_occurrence) VALUES (pObjectId, pSubobjectId, pMinOccurrence, pInitialOccurrence, pMaxOccurrence);
			lId = currval('object_subobjects_id_seq');
			PERFORM spFixTemplateObjects(1, pObjectId, pSubobjectId);
		ELSE -- Update
			-- Не променяме object_id-то и subobject_id ид-то
			UPDATE object_subobjects SET
				min_occurrence = pMinOccurrence,
				initial_occurrence = pInitialOccurrence,
				max_occurrence = pMaxOccurrence
			WHERE id = pId;
			PERFORM spFixTemplateObjects(2, pObjectId, pSubobjectId);
		END IF;
		UPDATE objects SET
			lastmoduid = pUid,
			lastmoddate = now()
		WHERE id = pObjectId;
	ELSEIF pOper = 3 THEN -- Delete
		SELECT INTO lObjectId, lSubObjectId object_id, subobject_id FROM object_subobjects WHERE id = pId;
		SELECT INTO lObjectCanBeModified result FROM spCheckIfObjectCanBeModified(lObjectId);
		
		IF lObjectCanBeModified = 0 THEN
			RAISE EXCEPTION 'pwt_admin.objects.cantModifyThisObject';
		END IF;
		
		DELETE FROM object_subobjects WHERE id = pId; 
		PERFORM spFixTemplateObjects(3, lObjectId, lSubobjectId);
	END IF;


	SELECT INTO lRes id, object_id, subobject_id, min_occurrence, initial_occurrence, max_occurrence
	FROM object_subobjects WHERE id = lId;


	RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spObjectSubobject(
	pOper int,
	pId bigint,
	pObjectId bigint,
	pSubobjectId bigint,
	pMinOccurrence int,
	pInitialOccurrence int,
	pMaxOccurrence int,
	pUid int
) TO iusrpmt;
