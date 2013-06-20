
CREATE TABLE eol_export
(
  id serial NOT NULL,
  title character varying,
  journal_id integer,
  issue_id integer,
  createdate timestamp without time zone,
  lastmod timestamp without time zone,
  createuid integer,
  "xml" character varying,
  appended_to_xml_file integer DEFAULT 0,
  time_appended_to_xml_file timestamp without time zone,
  CONSTRAINT eol_export_pkey PRIMARY KEY (id),
  CONSTRAINT eol_export_createuid_fkey FOREIGN KEY (createuid)
      REFERENCES usr (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
GRANT ALL ON TABLE eol_export TO iusrpmt;

CREATE TABLE journals(
	id serial NOT NULL,
	"name" character varying,
	pensoft_id integer NOT NULL,
	pensoft_title character varying NOT NULL,
	xml_file_name character varying,
	CONSTRAINT journals_pkey PRIMARY KEY (id)
)
GRANT ALL ON TABLE journals TO iusrpmt;