DROP FUNCTION IF EXISTS pjs."spDisableInvitingUsers"(pRoundId bigint, pDocumentId bigint);
CREATE OR REPLACE FUNCTION pjs."spDisableInvitingUsers"(pRoundId bigint, pDocumentId bigint)
  RETURNS integer AS
$BODY$
	BEGIN		
		UPDATE pjs.document_review_rounds SET review_lock = true WHERE document_id = pDocumentId AND id = pRoundId;
	RETURN 1;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs."spDisableInvitingUsers"(pRoundId bigint, pDocumentId bigint) TO iusrpmt;
