DROP TYPE ret_spUpdateSupFileCitations CASCADE;

CREATE TYPE ret_spUpdateSupFileCitations AS (
	result int
);

CREATE OR REPLACE FUNCTION spUpdateSupFileCitations(
	pSupFileId bigint,
	pUid int
)
  RETURNS ret_spUpdateSupFileCitations AS
$BODY$
	DECLARE
		lRes ret_spUpdateSupFileCitations;		
		lRecord record;		
		lCitationType int = 4;
	BEGIN					
		-- UPDATE-ваме всички цитации в които участва елемента
		UPDATE pwt.citations c SET
			is_dirty = true
		WHERE pSupFileId = ANY(c.object_ids) AND c.citation_type = lCitationType;
		
		lRes.result = 1;		
		RETURN lRes;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spUpdateSupFileCitations(
	pSupFileId bigint,
	pUid int
) TO iusrpmt;
