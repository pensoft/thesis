DROP TABLE IF EXISTS pjs.poll;

CREATE TABLE pjs.poll (
  id serial PRIMARY KEY NOT NULL,
  label varchar NOT NULL,
  state int NOT NULL DEFAULT 0,
  ord int NOT NULL,
  journal_id int NOT NULL REFERENCES journals (id)
);

ALTER TABLE pjs.poll OWNER TO postgres;
GRANT ALL ON TABLE pjs.poll TO postgres;
GRANT ALL ON TABLE pjs.poll TO pensoft;
GRANT ALL ON TABLE pjs.poll TO public;