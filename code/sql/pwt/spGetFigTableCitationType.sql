DROP TYPE ret_spGetFigTableCitationType CASCADE;

CREATE TYPE ret_spGetFigTableCitationType AS (
	citation_type int
);

CREATE OR REPLACE FUNCTION spGetFigTableCitationType(
	pInstanceId bigint
)
  RETURNS ret_spGetFigTableCitationType AS
$BODY$
	DECLARE
		lRes ret_spGetFigTableCitationType;				
		lTableObjectId int = 238;
		lFigObjectId int = 221;
		lPlateObjectIds int[] = ARRAY[225, 226, 227, 228, 229, 230];
		lFigCitationType int = 1;
		lTableCitationType int = 2;
		lObjectId bigint;
	BEGIN			
		SELECT INTO lObjectId
			object_id
		FROM pwt.document_object_instances 
		WHERE id = pInstanceId;
		
		IF lObjectId = lTableObjectId THEN
			lRes.citation_type = lTableCitationType;
		ELSEIF lObjectId = lFigObjectId THEN
			lRes.citation_type = lFigCitationType;
		ELSEIF lObjectId = ANY (lPlateObjectIds) THEN
			lRes.citation_type = lFigCitationType;
		END IF;
		RETURN lRes;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetFigTableCitationType(
	pInstanceId bigint
) TO iusrpmt;
