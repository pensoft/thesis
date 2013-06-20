DROP TYPE ret_spPwtVersionRejectAllChanges CASCADE;
CREATE TYPE ret_spPwtVersionRejectAllChanges AS (
	result int
);

CREATE OR REPLACE FUNCTION spPwtVersionRejectAllChanges(
	pVersionId bigint,
	pUid int
)
  RETURNS ret_spPwtVersionRejectAllChanges AS
$BODY$
	DECLARE
		lRes ret_spPwtVersionRejectAllChanges;	
		lPwtVersionId bigint;		
		lUnprocessedUpdateChangeStateId int;
		lRejectedChangeStateId int;
	BEGIN		
		lUnprocessedUpdateChangeStateId = 1;
		lRejectedChangeStateId = 4;
		
		IF coalesce(spCheckIfUserCanEditPwtVersion(pVersionId, pUid), false) = false THEN
			RAISE EXCEPTION 'pjs.theSpecifiedVersionDoesNotBelongToTheSpecifiedAuthor';
		END IF;
		
		SELECT INTO lPwtVersionId pv.id
		FROM pjs.pwt_document_versions pv
		JOIN pjs.document_versions v ON v.id = pv.version_id 
		AND v.id = pVersionId AND v.uid = pUid;
		
		UPDATE pjs.pwt_document_version_changes SET
			state_id = lRejectedChangeStateId
		WHERE pwt_document_version_id = lPwtVersionId AND (state_id = lUnprocessedUpdateChangeStateId OR before_processing_state_id = lUnprocessedUpdateChangeStateId);
		
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spPwtVersionRejectAllChanges(
	pVersionId bigint,
	pUid int
) TO iusrpmt;
