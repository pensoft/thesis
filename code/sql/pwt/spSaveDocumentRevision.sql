--DROP FUNCTION pwt.spSaveDocumentRevision(bigint, integer);

CREATE OR REPLACE FUNCTION pwt.spSaveDocumentRevision(pDocumentId bigint, pUid integer)
  RETURNS bigint AS
$BODY$
DECLARE
	lRevisionId bigint;
	lLastRevisionUid integer;
	lDocumentId bigint;
	lDocName character varying;
	lTemplId integer;
	lCreateUsrId integer;
	lCreateDate timestamp without time zone;
	lLastModeDate timestamp without time zone;
	lCachedAuthors character varying;
	lState integer;
	lPaperTypeId integer;
	lJournalId integer;
	lDocXML xml;
	lDocHTML text;
	lPJSRevisionType int = 2;
	lRevisionType int;
BEGIN
	lRevisionId := 0;
	
	SELECT INTO lRevisionId, lLastRevisionUid, lRevisionType
		id, createuid, revision_type
	FROM pwt.document_revisions 
	WHERE document_id = pDocumentId
	ORDER BY createdate DESC
	LIMIT 1;
	
	SELECT INTO lDocumentId, lDocName, lTemplId, lCachedAuthors, lState, lPaperTypeId, lJournalId, lDocXML, lDocHTML
				id, name, template_id, cached_authors, state, papertype_id, journal_id, doc_xml, doc_html
	FROM pwt.documents
	WHERE id = pDocumentId;
	
	IF lDocumentId IS NOT NULL THEN
		IF lLastRevisionUid = pUid AND lRevisionType <> lPJSRevisionType THEN -- update revision
			UPDATE pwt.document_revisions 
			SET name = lDocName,
				template_id = lTemplId,
				lastmoddate = now(),
				cached_authors = lCachedAuthors,
				state = lState,
				papertype_id = lPaperTypeId,
				journal_id = lJournalId,
				doc_xml = lDocXML,
				doc_html = lDocHTML
			WHERE id = lRevisionId;
		ELSE 							-- add new revision
			INSERT INTO pwt.document_revisions 
				(document_id, name, template_id, createuid, cached_authors, state, papertype_id, journal_id, doc_xml, doc_html)
			VALUES (lDocumentId, lDocName, lTemplId, pUid, lCachedAuthors, lState, lPaperTypeId, lJournalId, lDocXML, lDocHTML);
			lRevisionId := currval('pwt.document_revisions_id_seq');
		END IF;
	END IF;
	
	RETURN lRevisionId;
END ;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION pwt.spSaveDocumentRevision(bigint, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spSaveDocumentRevision(bigint, integer) TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spSaveDocumentRevision(bigint, integer) TO iusrpmt;
GRANT EXECUTE ON FUNCTION pwt.spSaveDocumentRevision(bigint, integer) TO pensoft;
