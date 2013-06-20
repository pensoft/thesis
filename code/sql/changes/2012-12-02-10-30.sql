CREATE TABLE pwt.checklist_taxon_names(
	id serial PRIMARY KEY,
	rank_id int NOT NULL,
	rank_name varchar NOT NULL
);

CREATE TABLE pwt.checklist_taxon_names_details(
	id serial PRIMARY KEY,
	taxon_name_id int REFERENCES pwt.checklist_taxon_names(id) NOT NULL,
	field_id int NOT NULL,
	ord int DEFAULT 1 NOT NULL,
	pattern varchar DEFAULT '{value}',
	pattern_use int DEFAULT 1
);
COMMENT ON COLUMN pwt.checklist_taxon_names_details.pattern IS 'What pattern to use for the specific field (e.g. if the field should be wrappen in braces). The value expr to use in the pattern is {value}';
COMMENT ON COLUMN pwt.checklist_taxon_names_details.pattern_use IS 'Whether to use always the pattern (1) or to use the pattern when the field is not empty (0)';



INSERT INTO pwt.checklist_taxon_names(id, rank_id, rank_name) VALUES
(1, 1, 'kingdom'), (2, 2, 'subkingdom'), (3, 3, 'phylum'), (4, 4, 'subphylum'), (5, 5, 'superclass'), 
(6, 6, 'class'), (7, 7, 'subclass'), (8, 8, 'superorder'), (9, 9, 'order'), (10, 10, 'suborder'), 

(11, 11, 'infraorder'), (12, 12, 'superfamily'), (13, 13, 'family'), (14, 14, 'subfamily'), (15, 15, 'tribe'), 
(16, 16, 'subtribe'), (17, 17, 'genus'), (18, 18, 'subgenus'), (19, 19, 'species'), (20, 20, 'subspecies'), 
(21, 21, 'variety'), (22, 22, 'variety');

-- Single field ranks
INSERT INTO pwt.checklist_taxon_names_details(taxon_name_id, field_id) VALUES
(1, 419), (2, 420), (3, 421), (4, 422), (5, 423), (6, 424), (7, 425), (8, 426), (9, 427), (10, 428),
(11, 429), (12, 430), (13, 431), (14, 432), (15, 433), (16, 434), (17, 48), (18, 417);

-- Species
INSERT INTO pwt.checklist_taxon_names_details(taxon_name_id, field_id, ord, pattern, pattern_use) VALUES
(19, 48, 1, '{value}', 1), (19, 417, 2, '({value})', 0), (19, 49, 3, '{value}', 1);

-- Subspecies
INSERT INTO pwt.checklist_taxon_names_details(taxon_name_id, field_id, ord, pattern, pattern_use) VALUES
(20, 48, 1, '{value}', 1), (20, 417, 2, '({value})', 0), (20, 49, 3, '{value}', 1), (20, 418, 4, 'subsp. {value}', 0);

-- Variety
INSERT INTO pwt.checklist_taxon_names_details(taxon_name_id, field_id, ord, pattern, pattern_use) VALUES
(21, 48, 1, '{value}', 1), (21, 417, 2, '({value})', 0), (21, 49, 3, '{value}', 1), (21, 435, 4, 'var. {value}', 0);

-- Form
INSERT INTO pwt.checklist_taxon_names_details(taxon_name_id, field_id, ord, pattern, pattern_use) VALUES
(22, 48, 1, '{value}', 1), (22, 417, 2, '({value})', 0), (22, 49, 3, '{value}', 1), (22, 436, 4, 'form {value}', 0);

INSERT INTO pwt.actions(display_name, name, eval_sql_function) VALUES ('Save checklist taxon name after taxon save', 'Save checklist taxon name after taxon save', 'spSyncChecklistTaxonName');
-- 94;
INSERT INTO pwt.object_actions(object_id, action_id, ord, pos) VALUES (174, 94, 2, 4);

UPDATE pwt.objects SET
	default_limit_new_object_creation = false
WHERE id IN (8, 12);

UPDATE pwt.template_objects SET
	limit_new_object_creation = false
WHERE object_id IN (8, 12);

UPDATE pwt.document_template_objects SET
	limit_new_object_creation = false
WHERE object_id IN (8, 12);
