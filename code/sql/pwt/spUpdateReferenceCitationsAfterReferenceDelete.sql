DROP TYPE ret_spUpdateReferenceCitationsAfterReferenceDelete CASCADE;

CREATE TYPE ret_spUpdateReferenceCitationsAfterReferenceDelete AS (
	result int
);

CREATE OR REPLACE FUNCTION spUpdateReferenceCitationsAfterReferenceDelete(
	pReferenceId bigint,
	pUid int
)
  RETURNS ret_spUpdateReferenceCitationsAfterReferenceDelete AS
$BODY$
	DECLARE
		lRes ret_spUpdateReferenceCitationsAfterReferenceDelete;	
		lReferenceCitationType int;
	BEGIN	
		lReferenceCitationType = 3;
			
		-- Първо маркираме цитациите като променени
		PERFORM spUpdateReferenceCitations(pReferenceId, pUid);
		
		
		-- След това трием цитациите в които е цитирана само тази референция
		DELETE FROM pwt.citations c 
		WHERE pReferenceId = ANY(c.object_ids) AND array_upper(c.object_ids, 1) = 1 AND c.citation_type = lReferenceCitationType;
		
		-- След това махаме референцията от другите цитациите в които участва
		UPDATE pwt.citations c SET
			object_ids = array_pop(c.object_ids, pReferenceId)
		WHERE pReferenceId = ANY(c.object_ids) AND c.citation_type = lReferenceCitationType;
		
		
		lRes.result = 1;		
		RETURN lRes;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spUpdateReferenceCitationsAfterReferenceDelete(
	pReferenceId bigint,
	pUid int
) TO iusrpmt;
