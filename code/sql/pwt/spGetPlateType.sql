DROP TYPE ret_spGetPlateType CASCADE;
CREATE TYPE ret_spGetPlateType AS (
	plate_type int
);

CREATE OR REPLACE FUNCTION spGetPlateType(
	pPlateInstanceId bigint
)
  RETURNS ret_spGetPlateType AS
$BODY$
DECLARE
	lRes ret_spGetPlateType;
	lPlateTypeFieldId int = 485;		
BEGIN 	
	
	SELECT INTO lRes.plate_type
		value_int
	FROM  pwt.instance_field_values 
	WHERE instance_id = pPlateInstanceId AND field_id = lPlateTypeFieldId;
		
	RETURN lRes;
 
END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
GRANT EXECUTE ON FUNCTION spGetPlateType(
	pPlateInstanceId bigint
) TO iusrpmt;
