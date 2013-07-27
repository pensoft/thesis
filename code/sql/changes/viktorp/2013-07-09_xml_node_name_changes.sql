-- select * from pwt.template_objects where object_id = 90;

-- pwt.template_objects
update pwt.template_objects set xml_node_name = 'reference_author' where object_id = 90;
update pwt.template_objects set xml_node_name = 'synonyms' where object_id = 200;
update pwt.template_objects set xml_node_name = 'genus_name' where object_id = 181;
update pwt.template_objects set xml_node_name = 'species_name' where object_id = 180;

update pwt.template_objects set xml_node_name = 'type_species_ICZN_redescription' where object_id = 215;
update pwt.template_objects set xml_node_name = 'type_species_ICN_redescription' where object_id = 217;
update pwt.template_objects set xml_node_name = 'type_species_ICZN_new' where object_id = 186;
update pwt.template_objects set xml_node_name = 'type_species_ICN_new' where object_id = 195;

update pwt.template_objects set xml_node_name = 'taxon_notes' where object_id = 206;
update pwt.template_objects set xml_node_name = 'taxon_distribution' where object_id = 208;
update pwt.template_objects set xml_node_name = 'reference_single_citation' where object_id = 201;

update pwt.template_objects set xml_node_name = 'authors_editors_institutions' where object_id = 92;
update pwt.template_objects set xml_node_name = 'authors_institutions' where object_id = 100;
update pwt.template_objects set xml_node_name = 'authors' where object_id = 101;

-- pwt.document_template_objects
update pwt.document_template_objects set xml_node_name = 'reference_author' where object_id = 90;
update pwt.document_template_objects set xml_node_name = 'synonyms' where object_id = 200;
update pwt.document_template_objects set xml_node_name = 'genus_name' where object_id = 181;
update pwt.document_template_objects set xml_node_name = 'species_name' where object_id = 180;

update pwt.document_template_objects set xml_node_name = 'type_species_ICZN_redescription' where object_id = 215;
update pwt.document_template_objects set xml_node_name = 'type_species_ICN_redescription' where object_id = 217;
update pwt.document_template_objects set xml_node_name = 'type_species_ICZN_new' where object_id = 186;
update pwt.document_template_objects set xml_node_name = 'type_species_ICN_new' where object_id = 195;

update pwt.document_template_objects set xml_node_name = 'taxon_notes' where object_id = 206;
update pwt.document_template_objects set xml_node_name = 'taxon_distribution' where object_id = 208;
update pwt.document_template_objects set xml_node_name = 'reference_single_citation' where object_id = 201;

update pwt.document_template_objects set xml_node_name = 'authors_editors_institutions' where object_id = 92;
update pwt.document_template_objects set xml_node_name = 'authors_institutions' where object_id = 100;
update pwt.document_template_objects set xml_node_name = 'authors' where object_id = 101;

-- da pitam dan4o...!!!
update pwt.document_template_objects set xml_node_name = 'treatment1' where object_id = 216;
update pwt.template_objects set xml_node_name = 'treatment1' where object_id = 216;