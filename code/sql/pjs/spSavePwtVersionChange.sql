DROP TYPE ret_spSavePwtVersionChange CASCADE;
CREATE TYPE ret_spSavePwtVersionChange AS (
	result int
);

CREATE OR REPLACE FUNCTION spSavePwtVersionChange(
	pVersionId bigint,	
	pFieldId bigint, 
	pInstanceId bigint,
	pContent varchar,
	pUid int
)
  RETURNS ret_spSavePwtVersionChange AS
$BODY$
	DECLARE
		lRes ret_spSavePwtVersionChange;	
		lPwtVersionId bigint;
		lChangeId bigint;
		lUnprocessedChangeStateId int;
	BEGIN		
		lUnprocessedChangeStateId = 1;
		
		
		
		IF coalesce(spCheckIfUserCanEditPwtVersion(pVersionId, pUid), false) = false THEN
			RAISE EXCEPTION 'pjs.theSpecifiedVersionDoesNotBelongToTheSpecifiedAuthor';
		END IF;
		
		
		SELECT INTO lPwtVersionId pv.id
		FROM pjs.pwt_document_versions pv
		JOIN pjs.document_versions v ON v.id = pv.version_id 
		AND v.id = pVersionId AND v.uid = pUid;
		
		IF coalesce(lPwtVersionId, 0) = 0 THEN
			RAISE EXCEPTION 'pjs.theSpecifiedVersionDoesNotBelongToTheSpecifiedAuthor';
		END IF;
		
		SELECT INTO lChangeId id 
		FROM pjs.pwt_document_version_changes 
		WHERE field_id = pFieldId AND instance_id = pInstanceId AND pwt_document_version_id = lPwtVersionId;
		
		IF coalesce(lChangeId, 0) = 0 THEN
			INSERT INTO pjs.pwt_document_version_changes(pwt_document_version_id, field_id, instance_id, state_id, "value") 
			VALUES (lPwtVersionId, pFieldId, pInstanceId, lUnprocessedChangeStateId, pContent);
		ELSE
			UPDATE pjs.pwt_document_version_changes SET
				"value" = pContent, 
				state_id = lUnprocessedChangeStateId
			WHERE id = lChangeId;
		END IF;
	
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSavePwtVersionChange(
	pVersionId bigint,	
	pFieldId bigint, 
	pInstanceId bigint,
	pContent varchar,
	pUid int
) TO iusrpmt;
