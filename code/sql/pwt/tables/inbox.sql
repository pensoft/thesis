-- Table: inbox

-- DROP TABLE inbox;

CREATE TABLE inbox
(
  id serial NOT NULL,
  sender_id integer NOT NULL,
  recipient_id integer NOT NULL,
  msg character varying,
  sender_state integer,
  subject character varying,
  createdate timestamp without time zone NOT NULL DEFAULT now(),
  recipient_state integer,
  rootid integer,
  type integer,
  CONSTRAINT inbox_recipient_id_fkey FOREIGN KEY (recipient_id)
      REFERENCES usr (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE NO ACTION
)
WITH (
  OIDS=TRUE
);
ALTER TABLE inbox
  OWNER TO postgres84;
GRANT ALL ON TABLE inbox TO postgres;
GRANT ALL ON TABLE inbox TO iusrpmt;
