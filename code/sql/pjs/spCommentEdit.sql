DROP TYPE pjs.ret_spCommentEdit CASCADE;
CREATE TYPE pjs.ret_spCommentEdit AS (
	comment_id int,
	msg varchar
);

CREATE OR REPLACE FUNCTION pjs.spCommentEdit(
	pOper int,
	pCommentId bigint,
	pDocumentId int, 
	pMsg varchar,
	pUid int
)
  RETURNS pjs.ret_spCommentEdit AS
$BODY$
	DECLARE
		lRes pjs.ret_spCommentEdit;	
		lOriginalId int;
		lVersionId bigint;
		lVersionIsReadonly int;
	BEGIN		
		IF pOper = 1 THEN
			SELECT INTO lOriginalId, lVersionId
				original_id, version_id
			FROM pjs.msg
			WHERE id = pCommentId AND document_id = pDocumentId AND usr_id = pUid;
			
			lVersionIsReadonly = pjs.spCheckIfPjsVersionIsReadonly(lVersionId);
			IF coalesce(lVersionIsReadonly, 0) = 1 THEN
				RAISE EXCEPTION 'pjs.specifiedVersionIsReadonly';
			END IF;
			
			UPDATE pjs.msg SET
				msg = pMsg
			WHERE original_id = lOriginalId AND document_id = pDocumentId AND usr_id = pUid;
		END IF;
		
		SELECT INTO lRes
			id, msg
		FROM pjs.msg 
		WHERE id = pCommentId AND document_id = pDocumentId AND usr_id = pUid;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs.spCommentEdit(
	pOper int,
	pCommentId bigint,
	pDocumentId int, 
	pMsg varchar,
	pUid int
) TO iusrpmt;
