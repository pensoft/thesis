select of.object_id, of.label, of.field_id, t1.id 
from pwt.template_objects t
LEFT JOIN pwt.template_objects t1 ON t1.pos = substring(t.pos from 1 for length(t.pos)-2) AND t1.template_id = t.template_id
LEFT JOIN pwt.object_fields of ON of.object_id = t1.object_id and of.allow_nulls = false
where t.object_id = 50 and t.template_id = 8 and of.field_id is not null;

--template_id = 9
INSERT INTO pwt.custom_validation_rules(name, function_name, ord, template_object_id, perform_in_modes) VALUES('Field Subsection custom check', 'FieldSubsectionCustomCheck', 1, 14015, ARRAY[1,2]);
update pwt.object_fields set allow_nulls = true where object_id = 16 and field_id = 20;

--template_id = 7
INSERT INTO pwt.custom_validation_rules(name, function_name, ord, template_object_id, perform_in_modes) VALUES('Field Subsection custom check', 'FieldSubsectionCustomCheck', 1, 12702, ARRAY[1,2]);
INSERT INTO pwt.custom_validation_rules(name, function_name, ord, template_object_id, perform_in_modes) VALUES('Field Subsection custom check', 'FieldSubsectionCustomCheck', 1, 12709, ARRAY[1,2]);
INSERT INTO pwt.custom_validation_rules(name, function_name, ord, template_object_id, perform_in_modes) VALUES('Field Subsection custom check', 'FieldSubsectionCustomCheck', 1, 12711, ARRAY[1,2]);
update pwt.object_fields set allow_nulls = true where object_id = 166 and field_id = 20;
update pwt.object_fields set allow_nulls = true where object_id = 170 and field_id = 23;
update pwt.object_fields set allow_nulls = true where object_id = 171 and field_id = 224;

-- template_id = 8
INSERT INTO pwt.custom_validation_rules(name, function_name, ord, template_object_id, perform_in_modes) VALUES('Field Subsection custom check', 'FieldSubsectionCustomCheck', 1, 12633, ARRAY[1,2]);
update pwt.object_fields set allow_nulls = true where object_id = 165 and field_id = 412;


--template_id = 7,8,9,10,4,5,3