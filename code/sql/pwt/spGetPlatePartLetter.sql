CREATE OR REPLACE FUNCTION spGetPlatePartLetter(
	pPlatePartInstanceId bigint
)
  RETURNS varchar AS
$BODY$
	DECLARE
		lRes varchar;		
		lPlateNumber int;		
		lSubPlatePositions varchar[] = ARRAY['a', 'b', 'c', 'd', 'e', 'f'];
	BEGIN				
		lPlateNumber = spGetPlatePartNumber(pPlatePartInstanceId);
		
		lRes = lSubPlatePositions[lPlateNumber];		
		RETURN lRes;		
	END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER;
GRANT EXECUTE ON FUNCTION spGetPlatePartLetter(
	pPlatePartInstanceId bigint
) TO iusrpmt;
