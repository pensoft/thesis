DROP VIEW pwt.v_instance_fields;
CREATE OR REPLACE VIEW pwt.v_instance_fields AS 
 SELECT i.id as instance_id, f.id as field_id, f.name, f.type, of.label, of.control_type, of.allow_nulls::integer AS allow_nulls, of.has_help_label::int as has_help_label, of.help_label, fv.data_src_id, s.query AS src_query,
	fv.value_str, fv.value_int, fv.value_arr_int, fv.value_arr_str, fv.value_date, fv.value_arr_date, ft.value_column_name, of.display_label::int, of.css_class,
	of.has_example_label::int as has_example_label, of.example_label, of.help_label_display_style,
	fv.is_read_only::int as is_read_only,
	of.autocomplete_row_templ, of.autocomplete_onselect, ft.is_array::int as is_array, hct.is_html::int as is_html
   FROM pwt.document_object_instances i
   JOIN pwt.instance_field_values fv ON fv.instance_id = i.id
   JOIN pwt.object_fields of ON of.object_id = i.object_id AND of.field_id = fv.field_id
   JOIN pwt.fields f ON f.id = fv.field_id
   JOIN pwt.field_types ft ON ft.id = f.type
   JOIN pwt.html_control_types hct ON hct.id = of.control_type
   LEFT JOIN pwt.data_src s ON s.id = fv.data_src_id;
   
   
GRANT ALL ON TABLE pwt.v_instance_fields TO iusrpmt;
