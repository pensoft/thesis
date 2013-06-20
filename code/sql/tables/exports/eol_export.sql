CREATE TABLE eol_export() INHERITS (export_common)
WITH (
  OIDS=TRUE
);
ALTER TABLE eol_export OWNER TO postgres;
GRANT ALL ON TABLE eol_export TO postgres;
GRANT ALL ON TABLE eol_export TO iusrpmt;

ALTER TABLE eol_export ALTER COLUMN type_id SET DEFAULT 1;