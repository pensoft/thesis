DROP TYPE ret_spFigureTableAfterMoveDown CASCADE;
CREATE TYPE ret_spFigureTableAfterMoveDown AS (
	result int
);

CREATE OR REPLACE FUNCTION spFigureTableAfterMoveDown(
	pInstanceId bigint,
	pUid integer
)
  RETURNS ret_spFigureTableAfterMoveDown AS
$BODY$
DECLARE
	lRes ret_spFigureTableAfterMoveDown;
	lFigureType int;
	lFigureNumFieldId bigint = 489;
	lCount int;
BEGIN 
	lRes.result = 1;
	
	SELECT INTO lCount count(*)
	FROM pwt.document_object_instances i
	JOIN pwt.document_object_instances f ON f.parent_id = i.parent_id AND f.object_id = i.object_id AND i.pos < f.pos
	WHERE f.id = pInstanceId AND i.is_confirmed = true;
	-- RAISE NOTICE 'Tbl %, cnt %', pInstanceId, lCount;
	UPDATE pwt.instance_field_values SET
		value_int = coalesce(lCount, 0) + 1
	WHERE instance_id = pInstanceId AND field_id = lFigureNumFieldId;
	
	PERFORM spUpdateTableFigCitations(pInstanceId, pUid);
	
	RETURN lRes;
 
END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
GRANT EXECUTE ON FUNCTION spFigureTableAfterMoveDown(
	pInstanceId bigint,
	pUid integer
) TO iusrpmt;
