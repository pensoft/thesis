-- Function: concat_coma(text, text)

-- DROP FUNCTION concat_coma(text, text);

CREATE OR REPLACE FUNCTION concat_coma(text, text)
  RETURNS text AS
$BODY$
  DECLARE
    t text;
  BEGIN
    IF  character_length($1) > 0 THEN
      t = $1 ||', '|| $2;
    ELSE
      t = $2;
    END IF;
    RETURN t;
  END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION concat_coma(text, text) OWNER TO postgres;


-- Aggregate: aggr_concat_coma

-- DROP AGGREGATE aggr_concat_coma(text);

CREATE AGGREGATE aggr_concat_coma(text) (
  SFUNC=concat_coma,
  STYPE=text
);
ALTER AGGREGATE aggr_concat_coma(text) OWNER TO postgres;
