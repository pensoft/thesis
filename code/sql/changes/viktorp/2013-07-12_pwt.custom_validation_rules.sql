-- CustomCheckTaxonPaperTaxonomicClassification (Taxonomic paper, species inventory)
INSERT INTO pwt.custom_validation_rules(name, function_name, ord, template_object_id, perform_in_modes) VALUES('Taxon classification custom check', 'CustomCheckTaxonPaperTaxonomicClassification', 1, 14013, ARRAY[1,2]);
INSERT INTO pwt.custom_validation_rules(name, function_name, ord, template_object_id, perform_in_modes) VALUES('Taxon classification custom check', 'CustomCheckTaxonPaperTaxonomicClassification', 1, 14543, ARRAY[1,2]);


-- CustomCheckSubjectClassification (All templates)
INSERT INTO pwt.custom_validation_rules(name, function_name, ord, template_object_id, perform_in_modes) VALUES('Subject classification custom check', 'CustomCheckSubjectClassification', 1, 1675, ARRAY[1,2]);
INSERT INTO pwt.custom_validation_rules(name, function_name, ord, template_object_id, perform_in_modes) VALUES('Subject classification custom check', 'CustomCheckSubjectClassification', 1, 1842, ARRAY[1,2]);
INSERT INTO pwt.custom_validation_rules(name, function_name, ord, template_object_id, perform_in_modes) VALUES('Subject classification custom check', 'CustomCheckSubjectClassification', 1, 1936, ARRAY[1,2]);
INSERT INTO pwt.custom_validation_rules(name, function_name, ord, template_object_id, perform_in_modes) VALUES('Subject classification custom check', 'CustomCheckSubjectClassification', 1, 12511, ARRAY[1,2]);
INSERT INTO pwt.custom_validation_rules(name, function_name, ord, template_object_id, perform_in_modes) VALUES('Subject classification custom check', 'CustomCheckSubjectClassification', 1, 12631, ARRAY[1,2]);
INSERT INTO pwt.custom_validation_rules(name, function_name, ord, template_object_id, perform_in_modes) VALUES('Subject classification custom check', 'CustomCheckSubjectClassification', 1, 14013, ARRAY[1,2]);
INSERT INTO pwt.custom_validation_rules(name, function_name, ord, template_object_id, perform_in_modes) VALUES('Subject classification custom check', 'CustomCheckSubjectClassification', 1, 14543, ARRAY[1,2]);

-- CustomCheckForChecklistTaxonAtLeastOne (Taxonomic paper)
INSERT INTO pwt.custom_validation_rules(name, function_name, ord, template_object_id, perform_in_modes) VALUES('Checklist - taxon check at least one custom check', 'CustomCheckForChecklistTaxonAtLeastOne', 1, 14479, ARRAY[1,2]);

-- CustomCheckIdentificationKeyCouplet (Taxonomic paper)
INSERT INTO pwt.custom_validation_rules(name, function_name, ord, template_object_id, perform_in_modes) VALUES('Identification key - key couplet at least one custom check', 'CustomCheckIdentificationKeyCouplet', 1, 14399, ARRAY[1,2]);

-- CustomCheckWebLocationFieldsNotEmpty (Software description)
INSERT INTO pwt.custom_validation_rules(name, function_name, ord, template_object_id, perform_in_modes) VALUES('Web location - homepage, wiki, download page at least one field required custom check', 'CustomCheckWebLocationFieldsNotEmpty', 1, 1685, ARRAY[1,2]);

-- Software description - Usage rights - IP rights notes
UPDATE pwt.object_fields SET allow_nulls = TRUE WHERE field_id = 312;
INSERT INTO pwt.custom_validation_rules(name, function_name, ord, template_object_id, perform_in_modes) VALUES('Usage rights - if use license is other then ip rights notes is required', 'CustomCheckUsageRightsIPRightsNotesFieldsNotEmpty', 1, 1688, ARRAY[1,2]);

-- CustomCheckProjectDescriptionFieldsNotEmpty (Interactive key)
INSERT INTO pwt.custom_validation_rules(name, function_name, ord, template_object_id, perform_in_modes) VALUES('Project description - Study area description or Design description is required', 'CustomCheckProjectDescriptionFieldsNotEmpty', 1, 1851, ARRAY[1,2]);
