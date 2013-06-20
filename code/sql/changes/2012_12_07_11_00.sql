ALTER TABLE pwt.fields ADD COLUMN api_allow_null boolean DEFAULT true;
ALTER TABLE pwt.object_fields ADD COLUMN api_allow_null boolean DEFAULT true;

ALTER TABLE pwt.object_subobjects ADD COLUMN api_min_occurrence int DEFAULT 0;

-- Article metadata required 1 author
UPDATE pwt.object_subobjects SET 
	api_min_occurrence = 1
WHERE object_id = 14 AND subobject_id = 9;

UPDATE pwt.object_subobjects SET 
	api_min_occurrence = 1
WHERE object_id = 9 AND subobject_id = 8;

-- Article title - required
UPDATE pwt.object_fields SET
	api_allow_null = false
WHERE field_id = 3;

UPDATE pwt.template_objects o SET
	parent_id = p.id
FROM pwt.template_objects p 
WHERE p.parent_id IS NULL AND
	p.template_id = o.template_id AND char_length(p.pos) + 2 = char_length(o.pos) AND substring(o.pos, 1, char_length(p.pos)) = p.pos;
--Author/Contributor name search
UPDATE pwt.template_objects SET
	display_object_in_xml = 2
WHERE object_id IN (11, 7);

--Author
UPDATE pwt.template_objects SET
	xml_node_name = 'author'
WHERE object_id = 8;

--Author address
UPDATE pwt.template_objects SET
	xml_node_name = 'address'
WHERE object_id = 5;

--Add subsections to other taxon sections
SET search_path TO public,pwt;
SELECT * FROM spObjectSubobject(1, null, 78, 50, 0, 99999999, 0, 8);
SELECT * FROM spObjectSubobject(1, null, 77, 50, 0, 99999999, 0, 8);
SELECT * FROM spObjectSubobject(1, null, 80, 50, 0, 99999999, 0, 8);
SELECT * FROM spObjectSubobject(1, null, 79, 50, 0, 99999999, 0, 8);
SELECT * FROM spObjectSubobject(1, null, 75, 50, 0, 99999999, 0, 8);

UPDATE pwt.template_objects dto SET
	xml_node_name = translate(lower(display_name), ' -', '_')
WHERE coalesce(xml_node_name, '') = '';

UPDATE pwt.template_objects dto SET
	xml_node_name = translate(lower(xml_node_name), '()', '')
WHERE position('(' in xml_node_name) > 0 OR position(')' in xml_node_name) > 0;

UPDATE pwt.template_objects dto SET
	xml_node_name = replace(lower(xml_node_name), '&', 'and')
WHERE position('&' in xml_node_name) > 0;

UPDATE pwt.template_objects dto SET
	xml_node_name = replace(lower(xml_node_name), '/', '')
WHERE position('/' in xml_node_name) > 0;

UPDATE pwt.object_fields SET
	api_allow_null = false
WHERE object_id = 8 AND field_id IN (6, 4, 8, 248, 14);

UPDATE pwt.object_fields SET
	api_allow_null = false
WHERE object_id = 41 AND field_id IN (41, 42, 43);

-- Article metadata Editorial.. authors
UPDATE pwt.object_subobjects SET 
	api_min_occurrence = 1
WHERE object_id = 152 AND subobject_id = 153;

UPDATE pwt.object_subobjects SET 
	api_min_occurrence = 1
WHERE object_id = 153 AND subobject_id = 8;

--TTM Priority DC - disable in api
UPDATE pwt.template_objects SET
	display_object_in_xml = 2 
WHERE object_id = 83;

-- TTM Extended DC - disable search field
UPDATE pwt.object_fields SET
	display_in_xml = 2
WHERE object_id = 84 AND field_id = 249;

--TTM Extended DC type status 
UPDATE pwt.object_fields SET
	api_allow_null = false
WHERE object_id = 84 AND field_id = 209;


--Reference - ref type 
UPDATE pwt.object_fields SET
	api_allow_null = false
WHERE object_id = 95 AND field_id = 269;

--Checklist taxon - rank
UPDATE pwt.object_fields SET
	api_allow_null = false
WHERE object_id = 174 AND field_id = 414;

--Reference parse/ext db search
UPDATE pwt.template_objects SET
	display_object_in_xml = 2 
WHERE object_id IN (94, 96);

--Reference author
UPDATE pwt.template_objects SET
	xml_node_name = 'reference_author' 
WHERE object_id = 90;
--Reference editor
UPDATE pwt.template_objects SET
	xml_node_name = 'reference_editor' 
WHERE object_id = 91;

--Taxon name species
UPDATE pwt.template_objects SET
	xml_node_name = 'taxon_name_species'
WHERE object_id = 180;

--Taxon name genus
UPDATE pwt.template_objects SET
	xml_node_name = 'taxon_name_genus'
WHERE object_id = 181;

-- Reference citations
UPDATE pwt.template_objects SET
	display_object_in_xml = 2 
WHERE object_id IN (187);

--Taxon discussion section 
UPDATE pwt.template_objects SET
	xml_node_name = 'taxon_discussion'
WHERE object_id = 75;

-- Reference authorship 
UPDATE pwt.template_objects SET
	xml_node_name = 'authors_reference'
WHERE object_id = 100;

UPDATE pwt.template_objects SET
	xml_node_name = 'authors_reference_biblio'
WHERE object_id = 101;