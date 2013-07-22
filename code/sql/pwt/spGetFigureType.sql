DROP TYPE ret_spGetFigureType CASCADE;
CREATE TYPE ret_spGetFigureType AS (
	figure_type int
);

CREATE OR REPLACE FUNCTION spGetFigureType(
	pFigureInstanceId bigint
)
  RETURNS ret_spGetFigureType AS
$BODY$
DECLARE
	lRes ret_spGetFigureType;
	lFigureTypeFieldId int = 488;		
BEGIN 	
	
	SELECT INTO lRes.figure_type
		value_int
	FROM  pwt.instance_field_values 
	WHERE instance_id = pFigureInstanceId AND field_id = lFigureTypeFieldId;
		
	RETURN lRes;
 
END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
GRANT EXECUTE ON FUNCTION spGetFigureType(
	pFigureInstanceId bigint
) TO iusrpmt;
