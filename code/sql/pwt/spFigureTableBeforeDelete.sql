DROP TYPE ret_spFigureTableBeforeDelete CASCADE;
CREATE TYPE ret_spFigureTableBeforeDelete AS (
	result int
);

CREATE OR REPLACE FUNCTION spFigureTableBeforeDelete(
	pInstanceId bigint,
	pUid integer
)
  RETURNS ret_spFigureTableBeforeDelete AS
$BODY$
DECLARE
	lRes ret_spFigureTableBeforeDelete;
	lFigureType int;
	lFigureNumFieldId bigint = 489;
	lCount int;
BEGIN 
	lRes.result = 1;
	
	
	UPDATE pwt.instance_field_values f SET
		value_int = value_int - 1
	FROM pwt.document_object_instances i
	JOIN pwt.document_object_instances s ON s.parent_id = i.parent_id AND s.pos < i.pos AND s.object_id = i.object_id
	WHERE f.instance_id = i.id AND field_id = lFigureNumFieldId AND s.id = pInstanceId;
	
	PERFORM spUpdateTableFigCitationsBeforeDelete(pInstanceId, pUid);
	
	RETURN lRes;
 
END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
GRANT EXECUTE ON FUNCTION spFigureTableBeforeDelete(
	pInstanceId bigint,
	pUid integer
) TO iusrpmt;
