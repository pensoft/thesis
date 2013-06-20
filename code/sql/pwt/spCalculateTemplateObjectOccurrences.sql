DROP TYPE ret_spCalculateTemplateObjectOccurrences CASCADE;
CREATE TYPE ret_spCalculateTemplateObjectOccurrences AS (
	min_occurrence int,
	max_occurrence int
);

CREATE OR REPLACE FUNCTION spCalculateTemplateObjectOccurrences(
	pTemplateId int,	
	pObjectId bigint, -- that is the object id of the object
	pRealParentId bigint -- this is the id in template_objects of the parent
)
  RETURNS ret_spCalculateTemplateObjectOccurrences AS
$BODY$
DECLARE
	lRes ret_spCalculateTemplateObjectOccurrences;
	lRecord record;
	lRecord2 record;
	lChildTemplateObjectId bigint;
	lPreviousParentObjectId bigint;
BEGIN	
	lRes.min_occurrence = 1;
	lRes.max_occurrence = 1;
	
		
	SELECT INTO lChildTemplateObjectId o.id
	FROM pwt.template_objects o
	JOIN pwt.template_objects p ON p.id = pRealParentId AND p.template_id = o.template_id AND substring(o.pos, 1, char_length(p.pos)) = p.pos
	WHERE o.template_id = pTemplateId AND o.object_id = pObjectId
	LIMIT 1;
	
	lPreviousParentObjectId = pObjectId;
	
	FOR lRecord IN
		SELECT p.id, p.object_id
		FROM pwt.template_objects p		
		JOIN pwt.template_objects o ON o.id = lChildTemplateObjectId AND o.template_id = p.template_id AND substring(o.pos, 1, char_length(p.pos)) = p.pos
			AND p.id <> o.id
		ORDER BY p.pos DESC
	LOOP 
		SELECT INTO lRecord2 *
		FROM pwt.object_subobjects
		WHERE object_id = lRecord.object_id AND subobject_id = lPreviousParentObjectId;
		
		lRes.min_occurrence = lRes.min_occurrence * coalesce(lRecord2.api_min_occurrence, 0);
		lRes.max_occurrence = lRes.max_occurrence * coalesce(lRecord2.max_occurrence, 1);
		
		-- RAISE NOTICE 'Parent %, child %, min %, max %', lRecord2.object_id, lPreviousParentObjectId, lRecord2.api_min_occurrence, lRecord2.max_occurrence;
		
		IF lRecord.id = pRealParentId THEN
			RETURN lRes;
		END IF;		
		lPreviousParentObjectId = lRecord.object_id;
	END LOOP;
	
		
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCalculateTemplateObjectOccurrences(
	pTemplateId int,	
	pObjectId bigint,
	pRealParentId bigint
) TO iusrpmt;
