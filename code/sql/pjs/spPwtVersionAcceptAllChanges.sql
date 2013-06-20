DROP TYPE ret_spPwtVersionAcceptAllChanges CASCADE;
CREATE TYPE ret_spPwtVersionAcceptAllChanges AS (
	result int
);

CREATE OR REPLACE FUNCTION spPwtVersionAcceptAllChanges(
	pVersionId bigint,
	pUid int
)
  RETURNS ret_spPwtVersionAcceptAllChanges AS
$BODY$
	DECLARE
		lRes ret_spPwtVersionAcceptAllChanges;	
		lPwtVersionId bigint;		
		lUnprocessedUpdateChangeStateId int;
		lAcceptedChangeStateId int;
	BEGIN		
		lUnprocessedUpdateChangeStateId = 1;
		lAcceptedChangeStateId = 3;
		
		IF coalesce(spCheckIfUserCanEditPwtVersion(pVersionId, pUid), false) = false THEN
			RAISE EXCEPTION 'pjs.theSpecifiedVersionDoesNotBelongToTheSpecifiedAuthor';
		END IF;
		
		SELECT INTO lPwtVersionId pv.id
		FROM pjs.pwt_document_versions pv
		JOIN pjs.document_versions v ON v.id = pv.version_id 
		AND v.id = pVersionId AND v.uid = pUid;
		
		UPDATE pjs.pwt_document_version_changes SET
			state_id = lAcceptedChangeStateId
		WHERE pwt_document_version_id = lPwtVersionId AND (state_id = lUnprocessedUpdateChangeStateId OR before_processing_state_id = lUnprocessedUpdateChangeStateId);
		
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spPwtVersionAcceptAllChanges(
	pVersionId bigint,
	pUid int
) TO iusrpmt;
