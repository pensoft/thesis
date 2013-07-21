DROP TYPE ret_spPlateAfterCreation CASCADE;
CREATE TYPE ret_spPlateAfterCreation AS (
	result int
);

CREATE OR REPLACE FUNCTION spPlateAfterCreation(
	pPlateInstanceId bigint,
	pUid integer
)
  RETURNS ret_spPlateAfterCreation AS
$BODY$
DECLARE
	lRes ret_spPlateAfterCreation;
	lPlateType int;
BEGIN 
	lRes.result = 1;
	
	SELECT INTO lPlateType 
		plate_type 
	FROM spGetPlateType(pPlateInstanceId);
	IF coalesce(lPlateType, 0) > 0 THEN
		PERFORM spChangePlateType(pPlateInstanceId, lPlateType, pUid);
	END IF;
	
	RETURN lRes;
 
END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
GRANT EXECUTE ON FUNCTION spPlateAfterCreation(
	pPlateInstanceId bigint,
	pUid integer
) TO iusrpmt;
