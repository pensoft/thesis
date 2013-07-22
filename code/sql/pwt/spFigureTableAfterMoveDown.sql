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
	
	UPDATE pwt.instance_field_values SET
		value_int = value_int + 1
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
