DROP TYPE ret_spFigureAfterCreation CASCADE;
CREATE TYPE ret_spFigureAfterCreation AS (
	result int
);

CREATE OR REPLACE FUNCTION spFigureAfterCreation(
	pFigureInstanceId bigint,
	pUid integer
)
  RETURNS ret_spFigureAfterCreation AS
$BODY$
DECLARE
	lRes ret_spFigureAfterCreation;
	lFigureType int;
	lFigureNumFieldId bigint = 489;
	lCount int;
BEGIN 
	lRes.result = 1;
	
	SELECT INTO lFigureType 
		figure_type 
	FROM spGetFigureType(pFigureInstanceId);
	
	PERFORM spChangeFigureType(pFigureInstanceId, lFigureType, pUid);
	
	SELECT INTO lCount count(*)
	FROM pwt.document_object_instances i
	JOIN pwt.document_object_instances f ON f.parent_id = i.parent_id AND f.object_id = i.object_id AND f.id <> i.id
	WHERE f.id = pFigureInstanceId AND i.is_confirmed = true;
	
	UPDATE pwt.instance_field_values SET
		value_int = coalesce(lCount, 0) + 1
	WHERE instance_id = pFigureInstanceId AND field_id = lFigureNumFieldId;
	
	RETURN lRes;
 
END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
GRANT EXECUTE ON FUNCTION spFigureAfterCreation(
	pFigureInstanceId bigint,
	pUid integer
) TO iusrpmt;
