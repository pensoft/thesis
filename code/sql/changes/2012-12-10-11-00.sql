-- Taxon treatment objects 
UPDATE pwt.template_objects SET
	display_object_in_xml = 3
WHERE object_id IN (179, 182, 184, 192, 196, 197);

-- Taxon treatment subsections
UPDATE pwt.template_objects SET
	display_object_in_xml = 3
WHERE object_id IN (185, 109, 140, 193, 198, 199);

-- Material wrappers
UPDATE pwt.template_objects SET
	display_object_in_xml = 4
WHERE object_id IN (84, 85, 86, 87, 88, 89, 25, 26, 27, 28, 29, 30, 31);

-- Reference fields
UPDATE pwt.template_objects SET
	display_object_in_xml = 4
WHERE object_id IN (97, 98, 99, 102, 103, 105, 106, 107, 108);

-- Reference authors and editors
UPDATE pwt.template_objects SET
	display_object_in_xml = 3
WHERE object_id IN (100, 92, 93, 101);

-- Reference conference fields
UPDATE pwt.template_objects SET
	display_object_in_xml = 4
WHERE object_id IN (104);

-- Dont display reference type in references object
UPDATE pwt.object_fields SET
	display_in_xml = 2
WHERE object_id = 21 AND field_id = 269;

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

ALTER TABLE pwt.data_src ADD COLUMN xml_node_name varchar;

UPDATE pwt.data_src dto SET
	xml_node_name = translate(lower(name), ' ', '_')
WHERE coalesce(xml_node_name, '') = '';

UPDATE pwt.data_src dto SET
	xml_node_name = translate(lower(xml_node_name), '()', '')
WHERE position('(' in xml_node_name) > 0 OR position(')' in xml_node_name) > 0;

UPDATE pwt.data_src dto SET
	xml_node_name = replace(lower(xml_node_name), '&', 'and')
WHERE position('&' in xml_node_name) > 0;

UPDATE pwt.data_src dto SET
	xml_node_name = replace(lower(xml_node_name), ',', '_')
WHERE position(',' in xml_node_name) > 0;

UPDATE pwt.data_src dto SET
	xml_node_name = replace(lower(xml_node_name), '/', '')
WHERE position('/' in xml_node_name) > 0;