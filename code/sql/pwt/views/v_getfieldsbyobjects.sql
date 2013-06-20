CREATE OR REPLACE VIEW pwt.v_getfieldsbyobjects AS 
	SELECT i1.id as root_instance_id, i1.object_id, v.instance_id as field_instance_id, i1.document_id, v.field_id, v.value_str, v.value_int, v.value_arr_int, v.value_arr_str, v.value_date, v.value_arr_date
		FROM pwt.document_object_instances i1
		JOIN pwt.document_object_instances i2 ON (i1.document_id = i2.document_id AND i2.pos LIKE i1.pos || '%')
		JOIN pwt.instance_field_values v ON (i2.id = v.instance_id)
		ORDER BY i1.id, i2.pos;

ALTER TABLE pwt.v_getfieldsbyobjects OWNER TO postgres;
GRANT ALL ON TABLE pwt.v_getfieldsbyobjects TO postgres;
GRANT ALL ON TABLE pwt.v_getfieldsbyobjects TO iusrpmt;