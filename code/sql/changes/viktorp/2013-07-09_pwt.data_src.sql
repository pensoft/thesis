--select * from pwt.data_src where id IN (44,45,46,47,50,51);

update pwt.data_src set xml_node_name = 'taxon_classification_src' where id = 44;
update pwt.data_src set xml_node_name = 'subject_classification_src' where id = 45;
update pwt.data_src set xml_node_name = 'chronological_classification_src' where id = 46;
update pwt.data_src set xml_node_name = 'geographical_classification_src' where id = 47;
update pwt.data_src set xml_node_name = 'typification_reasons_ICZN' where id = 50;
update pwt.data_src set xml_node_name = 'typification_reasons_ICN' where id = 51;