DROP TYPE ret_spGetTaxonClassificationSelectedRootId CASCADE;
CREATE TYPE ret_spGetTaxonClassificationSelectedRootId AS (
	id int,
	nomenclature_code int
);

CREATE OR REPLACE FUNCTION spGetTaxonClassificationSelectedRootId(
	pSelectedValue text
)
  RETURNS ret_spGetTaxonClassificationSelectedRootId AS
$BODY$
DECLARE
	lRes ret_spGetTaxonClassificationSelectedRootId;
	
	lRecord record;
	lTempRecord record;
	
	lTempText text;
	
	lDataSrcCursor refcursor;
	
	lIter int;
	lSql text;
	
BEGIN
	
	lTempText = lower(translate(pSelectedValue, ' ,.-*', ''));
	
	
	
	IF coalesce(lTempText, '') <> '' THEN		
		lSql = 'SELECT a.*
			FROM (SELECT * FROM taxon_categories)a 
			WHERE lower(translate(a.name::text, '' ,.-*'', '''')) = ' ||quote_literal(lTempText) || '
			';
		-- RAISE NOTICE 'SQL %, query %', lSql, lRecord.query;
		--OPEN lDataSrcCursor FOR EXECUTE replace(lRecord.query, '{value}', quote_literal(lTemp[1]::text));			
		OPEN lDataSrcCursor FOR EXECUTE lSql;			
		
		
		FETCH lDataSrcCursor INTO lTempRecord;
		
		-- RAISE NOTICE 'Query %, Record %, is not null %, text %', lRecord.query, lTempRecord, not(lTempRecord IS NULL), lTempText;
		WHILE NOT(lTempRecord IS NULL)
		LOOP
			lRes.nomenclature_code = lTempRecord."nomenclaturalCode";
			IF coalesce(lTempRecord.rootnode, 0) = 0 THEN 
				lRes.id = lTempRecord.id;
			ELSE 
				lRes.id = lTempRecord.rootnode;
			END IF;
			
			EXIT;
			FETCH lDataSrcCursor INTO lTempRecord;
		END LOOP;
		CLOSE lDataSrcCursor;			
		
	END IF;
	

	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetTaxonClassificationSelectedRootId(
	pSelectedValue text
) TO iusrpmt;
