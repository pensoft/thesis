CREATE TABLE pauthors
(
  pauthors_id serial NOT NULL,
  pauthors_name text,
  CONSTRAINT pauthors_pkey PRIMARY KEY (pauthors_id)
)
WITH (OIDS=FALSE);
ALTER TABLE pauthors OWNER TO postgres84;
GRANT ALL ON TABLE pauthors TO postgres84;
GRANT SELECT ON TABLE pauthors TO iusrpmt;