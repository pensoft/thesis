ALTER TABLE pwt.msg ALTER COLUMN msg DROP NOT NULL;
ALTER TABLE pjs.msg ALTER COLUMN msg DROP NOT NULL;

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
	BEGIN		
		IF pOper = 1 THEN
			SELECT INTO lOriginalId 
				original_id
			FROM pjs.msg
			WHERE id = pCommentId AND document_id = pDocumentId AND usr_id = pUid;
			
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

CREATE TYPE pwt.ret_spCommentEdit AS (
	comment_id int,
	msg varchar
);

CREATE OR REPLACE FUNCTION pwt.spCommentEdit(
	pOper int,
	pCommentId bigint,
	pDocumentId int, 
	pMsg varchar,
	pUid int
)
  RETURNS pwt.ret_spCommentEdit AS
$BODY$
	DECLARE
		lRes pwt.ret_spCommentEdit;			
	BEGIN		
		IF pOper = 1 THEN
			UPDATE pwt.msg SET
				msg = pMsg
			WHERE id = pCommentId AND document_id = pDocumentId AND usr_id = pUid;
		END IF;
		
		SELECT INTO lRes
			id, msg
		FROM pwt.msg 
		WHERE id = pCommentId AND document_id = pDocumentId AND usr_id = pUid;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pwt.spCommentEdit(
	pOper int,
	pCommentId bigint,
	pDocumentId int, 
	pMsg varchar,
	pUid int
) TO iusrpmt;