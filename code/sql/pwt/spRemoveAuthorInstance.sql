-- Function: pwt.spRemoveAuthorInstance(bigint,  integer)

-- DROP FUNCTION pwt.spRemoveAuthorInstance(bigint,  integer);

CREATE OR REPLACE FUNCTION pwt.spRemoveAuthorInstance(pinstanceid bigint, puid integer)
 RETURNS integer AS
$BODY$
	DECLARE
		lDocumentId bigint;
		lUsrId integer;
		lCurrentOrd int;
	BEGIN
		SELECT INTO lDocumentId document_id FROM pwt.document_object_instances WHERE id = pinstanceid;
		SELECT INTO lUsrId value_int FROM pwt.instance_field_values WHERE instance_id = pinstanceid AND field_id = 13; --13 e полето автор
		
		--След изтриване на инстанса трием и записа от document_users. 2 - user тип автор.
		SELECT INTO lCurrentOrd ord FROM pwt.document_users WHERE usr_id = lUsrId AND document_id = lDocumentId AND usr_type = 2;
		DELETE FROM pwt.document_users WHERE usr_id = lUsrId AND document_id = lDocumentId AND usr_type = 2;
		UPDATE pwt.document_users SET ord = ord - 1 WHERE document_id = lDocumentId AND usr_type = 2 AND ord > lCurrentOrd;
		
		RETURN 1;
	END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION pwt.spRemoveAuthorInstance(bigint,  integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spRemoveAuthorInstance(bigint,  integer) TO public;
GRANT EXECUTE ON FUNCTION pwt.spRemoveAuthorInstance(bigint,  integer) TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spRemoveAuthorInstance(bigint,  integer) TO iusrpmt;
