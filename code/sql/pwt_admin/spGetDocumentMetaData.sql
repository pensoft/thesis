DROP TYPE pwt.ret_spGetDocumentMetaData CASCADE;

CREATE TYPE pwt.ret_spGetDocumentMetaData  AS (
	journal_id int,
	papertype_id int
);

CREATE OR REPLACE FUNCTION pwt.spGetDocumentMetaData(
	pDocumentId int,
	pUid int
)
  RETURNS pwt.ret_spGetDocumentMetaData AS
$BODY$
	DECLARE lRes pwt.ret_spGetDocumentMetaData;
	BEGIN	
		
		SELECT INTO lRes.journal_id, lRes.papertype_id journal_id, papertype_id 
			FROM pwt.documents d
			JOIN pwt.document_users du on (d.id = du.document_id)
			WHERE 
				d.id = pDocumentId
				AND du.usr_id = pUid;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pwt.spGetDocumentMetaData(
	journal_id int,
	papertype_id int
) TO iusrpmt;
