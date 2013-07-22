DROP TYPE ret_spUpdateTableFigCitations CASCADE;

CREATE TYPE ret_spUpdateTableFigCitations AS (
	result int
);

CREATE OR REPLACE FUNCTION spUpdateTableFigCitations(
	pInstanceId bigint,
	pUid int
)
  RETURNS ret_spUpdateTableFigCitations AS
$BODY$
	DECLARE
		lRes ret_spUpdateTableFigCitations;		
		lRecord record;		
		lCitationType int;
	BEGIN					
		SELECT INTO lCitationType
			citation_type
		FROM spGetFigTableCitationType(pInstanceId);
		
		-- UPDATE-ваме всички цитации в които участва елемента
		UPDATE pwt.citations c SET
			is_dirty = true
		WHERE pInstanceId = ANY(c.object_ids) AND c.citation_type = lCitationType;
		
		lRes.result = 1;		
		RETURN lRes;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spUpdateTableFigCitations(
	pInstanceId bigint,
	pUid int
) TO iusrpmt;
