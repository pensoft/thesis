CREATE TABLE export_types(
	id serial PRIMARY KEY,
	name character varying
);

ALTER TABLE export_types OWNER TO postgres;
GRANT ALL ON TABLE export_types TO postgres;
GRANT ALL ON TABLE export_types TO iusrpmt;

INSERT INTO export_types(name) VALUES ('eol'), ('wiki'), ('keys');

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
  type_id integer REFERENCES export_types(id),
  CONSTRAINT export_common_pkey PRIMARY KEY (id),
  CONSTRAINT export_common_createuid_fkey FOREIGN KEY (createuid)
      REFERENCES usr (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=TRUE
);
ALTER TABLE export_common OWNER TO postgres;
GRANT ALL ON TABLE export_common TO postgres;
GRANT ALL ON TABLE export_common TO iusrpmt;

CREATE TABLE keys_export() INHERITS (export_common)
WITH (
  OIDS=TRUE
);
ALTER TABLE keys_export OWNER TO postgres;
GRANT ALL ON TABLE keys_export TO postgres;
GRANT ALL ON TABLE keys_export TO iusrpmt;

ALTER TABLE keys_export ALTER COLUMN type_id SET DEFAULT 3;

ALTER TABLE keys_export
  ADD CONSTRAINT keys_export_pkey PRIMARY KEY(id);

INSERT INTO secsites(id, url, name, cnt, ord, type) VALUES (56, '/resources/exports/', 'Exports', 3, 6, 1);
INSERT INTO secsites(id, url, name, cnt, ord, type) VALUES (57, '/resources/exports/keys_export/', 'Keys Export', 3, 1, 1);
ALTER SEQUENCE secsites_id_seq RESTART WITH 58;


INSERT INTO secgrpacc(gid, sid, type) VALUES (2, 56, 6);
INSERT INTO secgrpacc(gid, sid, type) VALUES (2, 57, 6);