CREATE OR REPLACE FUNCTION spGetDocumentLatestCommentRevisionId(
	pDocumentId int,
	pReturnPJSRevisionId int
)
  RETURNS bigint AS
$BODY$
	DECLARE
		lRes bigint;	
		lPjsRevisionType int = 2;
		lLatestPjsRevisionId bigint;
	BEGIN								
		pReturnPJSRevisionId = coalesce(pReturnPJSRevisionId, 0);
		
		SELECT INTO lLatestPjsRevisionId
			id
		FROM pwt.document_revisions
		WHERE document_id = pDocumentId AND revision_type = lPjsRevisionType 
		ORDER BY createdate DESC 
		LIMIT 1;
		
		lRes = lLatestPjsRevisionId;
		IF pReturnPJSRevisionId = 0 AND lLatestPjsRevisionId IS NULL THEN 
			-- If there is no pjs revision - return the first revision of the document
			SELECT INTO lRes 
				id
			FROM pwt.document_revisions
			WHERE document_id = pDocumentId 
			ORDER BY createdate ASC 
			LIMIT 1;
		END IF;
		
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetDocumentLatestCommentRevisionId(
	pDocumentId int,
	pReturnPJSRevisionId int
) TO iusrpmt;
