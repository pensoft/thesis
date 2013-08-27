-- View: pwt.v_instance_fields_eml

-- DROP VIEW pwt.v_instance_fields_eml;

CREATE OR REPLACE VIEW pwt.v_instance_fields_eml AS 
 SELECT i.id AS instance_id, f.id AS field_id, f.name, f.type, of.label, 
    of.control_type, of.allow_nulls::integer AS allow_nulls, 
    of.has_help_label::integer AS has_help_label, of.help_label, fv.data_src_id, 
    s.query AS src_query, fv.value_str, fv.value_int, fv.value_arr_int, 
    fv.value_arr_str, fv.value_date, fv.value_arr_date, ft.value_column_name, 
    of.display_label::integer AS display_label, of.css_class, 
    of.has_example_label::integer AS has_example_label, of.example_label, 
    of.help_label_display_style, fv.is_read_only::integer AS is_read_only, 
    of.autocomplete_row_templ, of.autocomplete_onselect, 
    ft.is_array::integer AS is_array, hct.is_html::integer AS is_html, i.parent_id
   FROM pwt.document_object_instances i
   JOIN pwt.instance_field_values fv ON fv.instance_id = i.id
   JOIN pwt.object_fields of ON of.object_id = i.object_id AND of.field_id = fv.field_id
   JOIN pwt.fields f ON f.id = fv.field_id
   JOIN pwt.field_types ft ON ft.id = f.type
   JOIN pwt.html_control_types hct ON hct.id = of.control_type
   LEFT JOIN pwt.data_src s ON s.id = fv.data_src_id;

ALTER TABLE pwt.v_instance_fields_eml
  OWNER TO postgres;
GRANT ALL ON TABLE pwt.v_instance_fields_eml TO postgres;
GRANT ALL ON TABLE pwt.v_instance_fields_eml TO pensoft;
GRANT ALL ON TABLE pwt.v_instance_fields_eml TO iusrpmt;

