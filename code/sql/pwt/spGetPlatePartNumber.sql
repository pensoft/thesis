CREATE OR REPLACE FUNCTION spGetPlatePartNumber(
	pPlatePartInstanceId bigint
)
  RETURNS int AS
$BODY$
	DECLARE
		lRes int;		
		lObjectId bigint;
	BEGIN				
		SELECT INTO lObjectId 
			object_id 
		FROM pwt.document_object_instances
		WHERE id = pPlatePartInstanceId;
		
		lRes = 0;
		
		IF lObjectId = 225 THEN
			lRes = 1;
		ELSEIF lObjectId = 226 THEN
			lRes = 2;
		ELSEIF lObjectId = 227 THEN
			lRes = 3;
		ELSEIF lObjectId = 228 THEN
			lRes = 4;
		ELSEIF lObjectId = 229 THEN
			lRes = 5;
		ELSEIF lObjectId = 230 THEN
			lRes = 6;
		END IF;		
		RETURN lRes;		
	END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER;
GRANT EXECUTE ON FUNCTION spGetPlatePartNumber(
	pPlatePartInstanceId bigint
) TO iusrpmt;
