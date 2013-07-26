CREATE OR REPLACE FUNCTION system."Proceed"(doc_id bigint)
  RETURNS integer AS
$BODY$BEGIN
UPDATE pjs.document_review_rounds r
SET can_proceed = true 
WHERE r.id = (SELECT current_round_id FROM pjs.documents WHERE id = doc_id);
RETURN 1;
END$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION system."Proceed"(bigint)
  OWNER TO pensoft;
