DROP TYPE ret_spCheckIfIdIsInDataSrc CASCADE;
CREATE TYPE ret_spCheckIfIdIsInDataSrc AS (
	result boolean
);

CREATE OR REPLACE FUNCTION spCheckIfIdIsInDataSrc(
	pId bigint, 
	pDataSrcId int
)
  RETURNS ret_spCheckIfIdIsInDataSrc AS
$BODY$
DECLARE
	lRes ret_spCheckIfIdIsInDataSrc;	
	lQuery varchar;
	lSql varchar;
	lRecord record;
BEGIN 
	lRes.result = false;
	SELECT INTO lQuery 
		query 
	FROM pwt.data_src 
	WHERE id = pDataSrcId;
	
	IF lQuery IS NOT NULL THEN		
		lSql = 'SELECT a.*, 1 as is_equal  
			FROM (' || lQuery || ')a 
			WHERE id = ' || pId;
		FOR lRecord IN
			EXECUTE lSql 
		LOOP
			lRes.result = true;
			RETURN lRes;
		END LOOP;
	END IF;
		
	RETURN lRes;
 
END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
GRANT EXECUTE ON FUNCTION spCheckIfIdIsInDataSrc(
	pId bigint, 
	pDataSrcId int
) TO iusrpmt;
