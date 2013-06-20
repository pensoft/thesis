CREATE TABLE pjs.msg
(
  id serial PRIMARY KEY,
  document_id integer NOT NULL,
  author character varying(128),
  subject character varying NOT NULL,
  msg character varying NOT NULL,
  senderip inet,
  mdate timestamp without time zone NOT NULL DEFAULT ('now'::text)::timestamp(6) with time zone,
  rootid integer,
  ord character varying,
  usr_id integer REFERENCES usr (id),
  flags integer NOT NULL DEFAULT 0,
  replies integer DEFAULT 0,
  views integer DEFAULT 0,
  lastmoddate timestamp without time zone NOT NULL DEFAULT ('now'::text)::timestamp(6) with time zone,
  version_id bigint REFERENCES pjs.document_versions(id) NOT NULL,
  start_object_instances_id bigint,
  end_object_instances_id bigint,
  start_object_field_id bigint,
  end_object_field_id bigint,
  start_offset integer,
  end_offset integer
)
WITH (
  OIDS=TRUE
);
GRANT ALL ON TABLE pjs.msg TO iusrpmt;

ALTER TABLE pjs.msg ADD COLUMN original_id int REFERENCES pjs.msg(id);
UPDATE pjs.msg SET
	original_id = id;
ALTER TABLE pjs.msg ALTER COLUMN original_id SET NOT NULL;