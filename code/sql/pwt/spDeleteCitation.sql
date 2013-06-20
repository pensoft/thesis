DROP TYPE ret_spDeleteCitation CASCADE;

CREATE TYPE ret_spDeleteCitation AS (
	result int
);

CREATE OR REPLACE FUNCTION spDeleteCitation(
	pCitationId bigint,
	pUid int
)
  RETURNS ret_spDeleteCitation AS
$BODY$
	DECLARE
		lRes ret_spDeleteCitation;				
	BEGIN
		DELETE FROM pwt.citations WHERE id = pCitationId;
		
		lRes.result = 1;
		RETURN lRes;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spDeleteCitation(
	pCitationId bigint,
	pUid int
) TO iusrpmt;
