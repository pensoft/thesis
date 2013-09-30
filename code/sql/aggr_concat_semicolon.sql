-- Function: concat_semicolon(text, text)

-- DROP FUNCTION concat_semicolon(text, text);

CREATE OR REPLACE FUNCTION concat_semicolon(text, text)
  RETURNS text AS
$BODY$
  DECLARE
    t text;
  BEGIN
    IF  character_length($1) > 0 THEN
      t = $1 ||'; '|| $2;
    ELSE
      t = $2;
    END IF;
    RETURN t;
  END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION concat_semicolon(text, text) OWNER TO postgres;


-- Aggregate: aggr_concat_coma

-- DROP AGGREGATE aggr_concat_coma(text);

CREATE AGGREGATE aggr_concat_semicolon(text) (
  SFUNC=concat_semicolon,
  STYPE=text
);
ALTER AGGREGATE aggr_concat_semicolon(text) OWNER TO postgres;
