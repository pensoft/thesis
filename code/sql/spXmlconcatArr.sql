CREATE OR REPLACE FUNCTION spXmlConcatArr(
	pXmlArr xml[]
)
  RETURNS xml AS
$BODY$
DECLARE
	lRes xml;	
	lIter int;
BEGIN
	
	FOR lIter IN 
		1 .. coalesce(array_upper(pXmlArr, 1), 0) 
	LOOP
		lRes = xmlconcat(lRes, pXmlArr[lIter]);			
	END LOOP;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spXmlConcatArr(
	pXmlArr xml[]
) TO iusrpmt;