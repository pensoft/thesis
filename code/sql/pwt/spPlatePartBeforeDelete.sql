DROP TYPE ret_spPlatePartBeforeDelete CASCADE;
CREATE TYPE ret_spPlatePartBeforeDelete AS (
	result int
);

CREATE OR REPLACE FUNCTION spPlatePartBeforeDelete(
	pInstanceId bigint,
	pUid integer
)
  RETURNS ret_spPlatePartBeforeDelete AS
$BODY$
DECLARE
	lRes ret_spPlatePartBeforeDelete;
	lCitationType int;	
BEGIN 
	lRes.result = 1;
	
	
	SELECT INTO lCitationType
		citation_type
	FROM spGetFigTableCitationType(pInstanceId);
	
	
	-- След това трием цитациите в които е цитирана само този елемент
	DELETE FROM pwt.citations c 
	WHERE pInstanceId = ANY(c.object_ids) AND array_upper(c.object_ids, 1) = 1 AND c.citation_type = lCitationType;
	
	-- След това махаме елемента от другите цитациите в които участва
	UPDATE pwt.citations c SET
		object_ids = array_pop(c.object_ids, pInstanceId)
	WHERE pInstanceId = ANY(c.object_ids) AND c.citation_type = lCitationType;
	
	RETURN lRes;
 
END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
GRANT EXECUTE ON FUNCTION spPlatePartBeforeDelete(
	pInstanceId bigint,
	pUid integer
) TO iusrpmt;
