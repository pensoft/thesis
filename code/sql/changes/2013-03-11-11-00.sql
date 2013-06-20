/*
object_id bigint,
field_id bigint,
control_type integer,
label character varying,
allow_nulls boolean NOT NULL DEFAULT false,
id bigserial NOT NULL,
data_src_id integer,
has_help_label boolean,
help_label character varying,
display_label boolean NOT NULL DEFAULT true,
css_class character varying,
autocomplete_row_templ character varying,
default_value_id bigint,
is_read_only boolean NOT NULL DEFAULT false,
xml_node_name character varying,
display_in_xml integer NOT NULL DEFAULT 1,
help_label_display_style integer NOT NULL DEFAULT 1,
has_example_label boolean NOT NULL DEFAULT false,
example_label character varying,
autocomplete_onselect character varying,
dont_save_value boolean NOT NULL DEFAULT false,
api_allow_null boolean DEFAULT true,

188;51;2;"Year";t;949;;;"";t;"";"";;f;"year";1;1;f;"";"";f;t
174;51;2;"Year";f;797;;;"";t;"";"";;f;"year";1;1;f;"";"";f;t
180;51;2;"Year";f;877;;;"";t;"";"";;f;"year";1;1;f;"";"";f;t
181;51;2;"Year";f;880;;;"";t;"";"";;f;"year";1;1;f;"";"";f;t

*/

SELECT * FROM spObjectFields(3, 797, null, null, null, null, null, null, null);
SELECT * FROM spObjectFields(3, 877, null, null, null, null, null, null, null);
SELECT * FROM spObjectFields(3, 880, null, null, null, null, null, null, null);
SELECT * FROM spObjectFields(3, 949, null, null, null, null, null, null, null);

UPDATE pwt.object_fields SET 
	label = 'Taxon Author(s) and year',
	xml_node_name = 'taxon_authors_and_year' 
	
WHERE field_id = 50;