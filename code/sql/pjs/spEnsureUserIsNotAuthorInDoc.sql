-- Function: pjs."spEnsureUserIsNotAuthorInDoc"(bigint, bigint, text)

-- DROP FUNCTION pjs."spEnsureUserIsNotAuthorInDoc"(bigint, bigint, text);

CREATE OR REPLACE FUNCTION pjs."spEnsureUserIsNotAuthorInDoc"("pUid" bigint, "pDocumentId" bigint, "pMessage" text)
  RETURNS boolean AS
$BODY$
DECLARE
	cAuthorRoleId CONSTANT int := 11;
	lAuthorEmail text;
	lAuthorName text;
	lStaff int;
BEGIN 
	IF EXISTS ( -- an author of the manuscript with specified id
		SELECT du.id FROM pjs.document_users du WHERE document_id = "pDocumentId" AND uid = "pUid" AND role_id = cAuthorRoleId
	) THEN -- abort operation
		SELECT INTO lAuthorEmail, 	lStaff,	lAuthorName
						uname, 		staff,	first_name || ' ' || last_name FROM usr WHERE id = "pUid";

		IF lStaff < 1 THEN
			RAISE EXCEPTION USING MESSAGE = format("pMessage", lAuthorName, lAuthorEmail);
		END IF;
	END IF;
	RETURN TRUE;
END
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION pjs."spEnsureUserIsNotAuthorInDoc"(bigint, bigint, text)
  OWNER TO postgres;
