-- DROP FUNCTION pjs.spcheckuserpermissionsfordecisionform(int, bigint, int);

CREATE OR REPLACE FUNCTION pjs.spCheckUserPermissionsForDecisionForm(
	pRoleId int,
	pDocumentId bigint,
	pUid int
)
  RETURNS int AS
$BODY$
	DECLARE
		JOURNAL_EDITOR_ROLE CONSTANT int := 2;
		SE_ROLE CONSTANT int := 3;
		DEDICATED_REVIEWER_ROLE CONSTANT int := 5;
		PANEL_REVIEWER_ROLE CONSTANT int := 7;
		lUserRole int;
		lRes int;
	BEGIN
		IF EXISTS (SELECT role_id FROM pjs.document_users WHERE uid = pUid AND document_id = pDocumentId AND role_id IN (JOURNAL_EDITOR_ROLE, SE_ROLE)) THEN
			RETURN 1;
		ELSEIF EXISTS (SELECT role_id FROM pjs.document_users WHERE uid = pUid AND document_id = pDocumentId AND role_id IN (DEDICATED_REVIEWER_ROLE, PANEL_REVIEWER_ROLE)) THEN
			IF pRoleId = DEDICATED_REVIEWER_ROLE OR pRoleId = PANEL_REVIEWER_ROLE THEN
				RETURN 1;
			END IF;
		END IF;
		
		RETURN 0;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs.spCheckUserPermissionsForDecisionForm(
	pRoleId int,
	pDocumentId bigint,
	pUid int
) TO iusrpmt;