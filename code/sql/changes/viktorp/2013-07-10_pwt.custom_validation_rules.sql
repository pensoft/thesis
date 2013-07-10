-- template_id = 7 objects
select object_id, id, display_name, xml_node_name from pwt.template_objects where display_name = 'Contributor' and template_id = 7;
select object_id, id, display_name, xml_node_name from pwt.template_objects where display_name = 'Author' and xml_node_name = 'author' and template_id = 7;
select object_id, id, display_name, xml_node_name from pwt.template_objects where display_name = 'Authors' and template_id = 7;

INSERT INTO pwt.custom_validation_rules(name, function_name, ord, template_object_id, perform_in_modes) VALUES
('Contributor custom checks','CustomCheckSingleContributor',1,12508,ARRAY[1,2]),
('Single author duplication custom check','CustomCheckSingleAuthor',1,12502,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,12761,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,12721,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,12748,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,12740,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,12776,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,12728,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,12769,ARRAY[1,2]);

select c.*
from pwt.custom_validation_rules c 
join pwt.template_objects to1 on to1.id = c.template_object_id
where to1.template_id = 7;

-- template_id = 8 objects
select object_id, id, display_name, xml_node_name from pwt.template_objects where display_name = 'Contributor' and template_id = 8;
select object_id, id, display_name, xml_node_name from pwt.template_objects where display_name = 'Author' and xml_node_name = 'author' and template_id = 8;
select object_id, id, display_name, xml_node_name from pwt.template_objects where display_name = 'Authors' and template_id = 8;

INSERT INTO pwt.custom_validation_rules(name, function_name, ord, template_object_id, perform_in_modes) VALUES
('Contributor custom checks','CustomCheckSingleContributor',1,12628,ARRAY[1,2]),
('Single author duplication custom check','CustomCheckSingleAuthor',1,12624,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,12823,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,12844,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,12796,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,12836,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,12851,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,12803,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,12815,ARRAY[1,2]);

select c.*
from pwt.custom_validation_rules c 
join pwt.template_objects to1 on to1.id = c.template_object_id
where to1.template_id = 8;

-- template_id = 9 objects
select object_id, id, display_name, xml_node_name from pwt.template_objects where display_name = 'Contributor' and template_id = 9;
select object_id, id, display_name, xml_node_name from pwt.template_objects where display_name = 'Author' and xml_node_name = 'author' and template_id = 9;
select object_id, id, display_name, xml_node_name from pwt.template_objects where display_name = 'Authors' and template_id = 9;

INSERT INTO pwt.custom_validation_rules(name, function_name, ord, template_object_id, perform_in_modes) VALUES
('Contributor custom checks','CustomCheckSingleContributor',1,14010,ARRAY[1,2]),
('Single author duplication custom check','CustomCheckSingleAuthor',1,14005,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,14413,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,14453,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,14420,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,14432,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,14440,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,14468,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,14461,ARRAY[1,2]);

select c.*
from pwt.custom_validation_rules c 
join pwt.template_objects to1 on to1.id = c.template_object_id
where to1.template_id = 9;

-- template_id = 10 objects
select object_id, id, display_name, xml_node_name from pwt.template_objects where display_name = 'Contributor' and template_id = 10;
select object_id, id, display_name, xml_node_name from pwt.template_objects where display_name = 'Author' and xml_node_name = 'author' and template_id = 10;
select object_id, id, display_name, xml_node_name from pwt.template_objects where display_name = 'Authors' and template_id = 10;

INSERT INTO pwt.custom_validation_rules(name, function_name, ord, template_object_id, perform_in_modes) VALUES
('Contributor custom checks','CustomCheckSingleContributor',1,14540,ARRAY[1,2]),
('Single author duplication custom check','CustomCheckSingleAuthor',1,14535,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,14599,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,14607,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,14559,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,14586,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,14578,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,14566,ARRAY[1,2]),
('Reference authors custom checks','CustomCheckReferenceAuthors',1,14614,ARRAY[1,2]);

select c.*
from pwt.custom_validation_rules c 
join pwt.template_objects to1 on to1.id = c.template_object_id
where to1.template_id = 10;