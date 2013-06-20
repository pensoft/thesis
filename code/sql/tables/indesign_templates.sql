CREATE TABLE indesign_templates (
	id serial PRIMARY KEY,
	name varchar
);

GRANT ALL ON indesign_templates TO iusrpmt;
GRANT ALL ON indesign_templates_id_seq TO iusrpmt;

CREATE TABLE indesign_template_details
(
  id serial NOT NULL,
  indesign_templates_id integer,
  "name" character varying,
  node_id integer,
  style character varying,
  "type" integer,
  CONSTRAINT indesign_template_details_pkey PRIMARY KEY (id),
  CONSTRAINT indesign_template_details_indesign_templates_id_fkey FOREIGN KEY (indesign_templates_id)
      REFERENCES indesign_templates (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE,
  CONSTRAINT indesign_template_details_node_id_fkey FOREIGN KEY (node_id)
      REFERENCES xml_nodes (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
GRANT ALL ON TABLE indesign_template_details TO iusrpmt;
GRANT ALL ON indesign_template_details_id_seq TO iusrpmt;
