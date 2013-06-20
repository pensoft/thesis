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
