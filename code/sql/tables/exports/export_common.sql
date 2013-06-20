CREATE TABLE export_types(
	id serial PRIMARY KEY,
	name character varying
);

ALTER TABLE export_types OWNER TO postgres;
GRANT ALL ON TABLE export_types TO postgres;
GRANT ALL ON TABLE export_types TO iusrpmt;

CREATE TABLE export_common
(
  id serial NOT NULL,
  title character varying,
  article_id integer,
  createdate timestamp without time zone DEFAULT now(),
  lastmod timestamp without time zone DEFAULT now(),
  createuid integer,
  "xml" character varying,
  is_uploaded integer DEFAULT 0,
  upload_time timestamp without time zone,
  upload_has_errors integer DEFAULT 0,
  upload_msg text,
  type_id integer,
  is_generated integer DEFAULT 0,
  has_results integer DEFAULT 0,
  CONSTRAINT export_common_pkey PRIMARY KEY (id),
  CONSTRAINT export_common_createuid_fkey FOREIGN KEY (createuid)
      REFERENCES usr (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT export_common_type_id_fkey FOREIGN KEY (type_id)
      REFERENCES export_types (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=TRUE
);
ALTER TABLE export_common OWNER TO postgres
GRANT ALL ON TABLE export_common TO postgres;
GRANT ALL ON TABLE export_common TO iusrpmt;