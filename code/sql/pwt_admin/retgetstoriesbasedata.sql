-- Type: retgetstoriesbasedata

-- DROP TYPE retgetstoriesbasedata;

CREATE TYPE retgetstoriesbasedata AS
   (guid integer,
    title character varying,
    author character varying,
    pubdate timestamp without time zone,
    state integer,
    description character varying,
    keywords character varying,
    lastmod timestamp without time zone,
    createuid character varying,
    subtitle character varying,
    primarysite integer,
    link character varying,
    nadzaglavie character varying,
    showforum integer,
    storytype integer,
    "language" character varying(3),
    rubr character varying,
    rubrstr character varying,
    mainrubr integer,
    indexer integer,
	journal_id integer);
ALTER TYPE retgetstoriesbasedata OWNER TO postgres;
