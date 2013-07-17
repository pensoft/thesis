CREATE OR REPLACE FUNCTION HtmlSpecialCharsDecode(
	pString text
)
  RETURNS text AS
$BODY$
DECLARE
	lRes text;	
BEGIN 	
	lRes = replace(pString, '&amp;', '&');
	lRes = replace(lRes, '&quot;', '"');
	lRes = replace(lRes, '&#039;', '\''');
	lRes = replace(lRes, '&lt;', '<');
	lRes = replace(lRes, '&gt;', '>');
		
	RETURN lRes;
 
END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
GRANT EXECUTE ON FUNCTION HtmlSpecialCharsDecode(
	pString text
) TO iusrpmt;
