--DROP TABLE metadata;

CREATE TABLE metadata
(
  id serial NOT NULL,
  title varchar,
  description varchar,
  keywords varchar,
  CONSTRAINT metadata_pkey PRIMARY KEY (id)
) 
WITHOUT OIDS;
ALTER TABLE metadata OWNER TO postgres84;
GRANT SELECT, UPDATE, INSERT, DELETE, REFERENCES, TRIGGER ON TABLE metadata TO postgres84;
GRANT SELECT, UPDATE ON TABLE metadata TO iusrnewetal;