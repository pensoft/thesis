CREATE TABLE menus (
    id serial NOT NULL,
    name character varying[] NOT NULL,
    sid integer DEFAULT 1,
    parentid integer,
    "type" integer NOT NULL,
    active integer NOT NULL DEFAULT 1,
    ord integer DEFAULT 1 NOT NULL,
    href character varying[],
    img character varying[]
);

ALTER TABLE menus
add CONSTRAINT menus_pkey PRIMARY KEY(ID);