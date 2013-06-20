CREATE TABLE wiki_export
(
-- Inherited from table export_common:  id integer NOT NULL DEFAULT nextval('export_common_id_seq'::regclass),
-- Inherited from table export_common:  title character varying,
-- Inherited from table export_common:  article_id integer,
-- Inherited from table export_common:  createdate timestamp without time zone DEFAULT now(),
-- Inherited from table export_common:  lastmod timestamp without time zone DEFAULT now(),
-- Inherited from table export_common:  createuid integer,
-- Inherited from table export_common:  "xml" character varying,
-- Inherited from table export_common:  is_uploaded integer DEFAULT 0,
-- Inherited from table export_common:  upload_time timestamp without time zone,
-- Inherited from table export_common:  upload_has_errors integer DEFAULT 0,
-- Inherited from table export_common:  upload_msg text,
-- Inherited from table export_common:  type_id integer DEFAULT 2,
-- Inherited from table export_common:  is_generated integer DEFAULT 0,
-- Inherited from table export_common:  has_results integer DEFAULT 0,
  wiki_username_id integer,
  CONSTRAINT wiki_export_pkey PRIMARY KEY (id),
  CONSTRAINT wiki_export_createuid_fkey FOREIGN KEY (createuid)
      REFERENCES usr (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT wiki_export_wiki_username_id_fkey FOREIGN KEY (wiki_username_id)
      REFERENCES wiki_login (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
INHERITS (export_common)
WITH (
  OIDS=TRUE
);
ALTER TABLE wiki_export OWNER TO postgres;
GRANT ALL ON TABLE wiki_export TO postgres;
GRANT ALL ON TABLE wiki_export TO iusrpmt;