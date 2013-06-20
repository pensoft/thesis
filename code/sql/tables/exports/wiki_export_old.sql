CREATE TABLE wiki_export_old
(
  id integer NOT NULL DEFAULT nextval('wiki_export_id_seq'::regclass),
  title character varying,
  article_id integer,
  createdate timestamp without time zone,
  lastmod timestamp without time zone,
  createuid integer,
  "xml" character varying,
  is_uploaded integer DEFAULT 0,
  upload_time timestamp without time zone,
  upload_has_errors integer DEFAULT 0,
  upload_msg text,
  wiki_username_id integer,
  CONSTRAINT wiki_export_old_pkey PRIMARY KEY (id),
  CONSTRAINT wiki_export_old_createuid_fkey FOREIGN KEY (createuid)
      REFERENCES usr (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT wiki_export_old_wiki_username_id_fkey FOREIGN KEY (wiki_username_id)
      REFERENCES wiki_login (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
ALTER TABLE wiki_export_old OWNER TO postgres;
GRANT ALL ON TABLE wiki_export_old TO postgres;
GRANT ALL ON TABLE wiki_export_old TO iusrpmt;