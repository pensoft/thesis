DROP TYPE ret_spGetDataSrcQuerySelectedId CASCADE;
CREATE TYPE ret_spGetDataSrcQuerySelectedId AS (
	id int
);

CREATE OR REPLACE FUNCTION spGetDataSrcQuerySelectedId(
	pSelectedValue text,
	pDataSrcId bigint
)
  RETURNS ret_spGetDataSrcQuerySelectedId AS
$BODY$
DECLARE
	lRes ret_spGetDataSrcQuerySelectedId;
	
	lRecord record;
	lTempRecord record;
	
	lTempText text;
	
	lDataSrcCursor refcursor;
	
	lIter int;
	lSql text;
	
BEGIN
	
	lTempText = lower(translate(pSelectedValue, ' ,.-*', ''));
	
	SELECT INTO lRecord  
		s.query, s.id
	FROM pwt.data_src s 
	WHERE s.id = pDataSrcId;
	
	IF lRecord.id IS NULL THEN
		RETURN lRes;
	END IF;
	
	
	IF coalesce(lTempText, '') <> '' THEN
		lSql = replace(lRecord.query, '{value}', quote_literal(pSelectedValue));
		lSql = 'SELECT a.*, 1 as is_equal  
			FROM (' || lSql || ')a 
			WHERE lower(translate(a.name::text, '' ,.-*'', '''')) = ' ||quote_literal(lTempText) || '
			';
		-- RAISE NOTICE 'SQL %, query %', lSql, lRecord.query;
		--OPEN lDataSrcCursor FOR EXECUTE replace(lRecord.query, '{value}', quote_literal(lTemp[1]::text));			
		OPEN lDataSrcCursor FOR EXECUTE lSql;			
		
		
		FETCH lDataSrcCursor INTO lTempRecord;
		
		-- RAISE NOTICE 'Query %, Record %, is not null %, text %', lRecord.query, lTempRecord, not(lTempRecord IS NULL), lTempText;
		WHILE NOT(lTempRecord IS NULL)
		LOOP
			lRes.id = lTempRecord.id;
			EXIT;
			FETCH lDataSrcCursor INTO lTempRecord;
		END LOOP;
		CLOSE lDataSrcCursor;			
		
	END IF;
	

	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetDataSrcQuerySelectedId(
	pSelectedValue text,
	pDataSrcId bigint
) TO iusrpmt;
