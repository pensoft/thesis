DROP FUNCTION IF EXISTS pjs."spDisableInvitingUsers"(pRoundId int, pDocumentId int);
CREATE OR REPLACE FUNCTION pjs."spDisableInvitingUsers"(pRoundId int, pDocumentId int)
  RETURNS integer AS
$BODY$
	BEGIN		
		UPDATE pjs.document_review_rounds SET review_lock = true WHERE document_id = pDocumentId AND id = pRoundId;
	RETURN 1;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs."spDisableInvitingUsers"(pRoundId int, pDocumentId int) TO iusrpmt;
