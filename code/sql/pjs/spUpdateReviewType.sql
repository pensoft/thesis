CREATE OR REPLACE FUNCTION pjs.spUpdateReviewType(
	pDocumentId int,
	pReviewTypeId int
)
	RETURNS int AS
$BODY$
	DECLARE
		lRes int;
		lRoundDueDate timestamp;
	BEGIN
		IF pDocumentId IS NOT NULL AND pReviewTypeId IS NOT NULL THEN
			update pjs.documents SET document_review_type_id = pReviewTypeId WHERE id = pDocumentId;
			lRes = 1;
		END IF;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
-- ALTER FUNCTION pjs.spUpdateReviewType(integer, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pjs.spUpdateReviewType(
	pDocumentId int,
	pReviewType int
) TO iusrpmt;
