-- Function: pwt.spCheckContributorExists(bigint, integer)

-- DROP FUNCTION pwt.spCheckContributorExists(bigint, integer);

CREATE OR REPLACE FUNCTION pwt.spCheckContributorExists(pinstanceid bigint, puid integer)
  RETURNS integer AS
$BODY$
	DECLARE
		lDocumentId bigint;
		lUsrId integer;
	BEGIN
		SELECT INTO lDocumentId document_id FROM pwt.document_object_instances WHERE id = pInstanceId;
		SELECT INTO lUsrId value_int FROM pwt.instance_field_values WHERE instance_id = pinstanceid AND field_id = 13; --13 e полето автор
	
		--След изтриване на инстанса трием и записа от document_users. 4 - user тип contributor.
		DELETE FROM pwt.document_users WHERE usr_id = lUsrId AND document_id = lDocumentId AND usr_type = 4;
		
		RETURN 1;
	END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION pwt.spCheckContributorExists(bigint, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spCheckContributorExists(bigint, integer) TO public;
GRANT EXECUTE ON FUNCTION pwt.spCheckContributorExists(bigint, integer) TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spCheckContributorExists(bigint, integer) TO iusrpmt;
