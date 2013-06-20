DROP TYPE ret_spImportPjsDocumentVersion CASCADE;
CREATE TYPE ret_spImportPjsDocumentVersion AS (
	result int
);

CREATE OR REPLACE FUNCTION spImportPjsDocumentVersion(
	pDocumentId int,
	pXml xml,	
	pCommentsXml xml,
	pUid int
)
  RETURNS ret_spImportPjsDocumentVersion AS
$BODY$
DECLARE
	lRes ret_spImportPjsDocumentVersion;
	
	lNewRevisionId bigint;
	lPjsRevisionTypeId int = 2;
	lReturnedFromPjsDocumentState int = 3;
	lPjsAuthorVersionType int = 1;
	
	lRevisionId bigint;
	lPjsAuthorVersionId bigint;
	lInstances xml[];
	lFields xml[];
	
	lIterObjects int;
	
	lCurrentInstance xml;	
	lTemp xml[];
	lImportIsAllowed boolean;
BEGIN
	SELECT INTO lImportIsAllowed is_allowed FROM spCheckIfUserCanImportPjsDocument(pDocumentId, pUid);
	
	IF coalesce(lImportIsAllowed, false) = false THEN
		RAISE EXCEPTION 'pwt.pjsImportThisUserCannotImportDocumentAtThisPoint';
	END IF;
	
	-- Insert a new pjs version
	INSERT INTO pwt.document_revisions(document_id, "name", template_id, createuid, cached_authors, state, papertype_id, journal_id, doc_xml, revision_type)
	SELECT pDocumentId, d.name, d.template_id, pUid, cached_authors, d.state, d.papertype_id, d.journal_id, pXml, lPjsRevisionTypeId
	FROM pwt.documents d 
	WHERE d.id = pDocumentId;
	
	lRevisionId = currval('pwt.document_revisions_id_seq');
	
	-- Get the version usr_ids from pjs
	SELECT INTO lPjsAuthorVersionId dv.id
	FROM pjs.document_versions dv
	JOIN pjs.pwt_documents pd ON pd.document_id = dv.document_id
	WHERE dv.version_type_id = lPjsAuthorVersionType AND pd.pwt_id = pDocumentId 
	ORDER BY dv.id DESC LIMIT 1;
	
	INSERT INTO pwt.pjs_revision_details(document_id, revision_id, change_user_ids)
	SELECT pDocumentId, lRevisionId, pdv.change_user_ids
	FROM pjs.pwt_document_versions pdv
	WHERE pdv.version_id = lPjsAuthorVersionId;
	
	PERFORM spImportRevisionComments(lRevisionId, pCommentsXml);
	
	--Update the document xml
	UPDATE pwt.documents SET
		doc_xml = pXml, 
		state = lReturnedFromPjsDocumentState,
		has_unprocessed_changes = true
	WHERE id = pDocumentId;
	
	--Update all the instances of the first lvl (the other are updated recursively)
	lInstances = xpath('//objects/*[@instance_id > 0]', pXml);	
	FOR lIterObjects IN 
		1 .. coalesce(array_upper(lInstances, 1), 0) 
	LOOP
		lCurrentInstance = lInstances[lIterObjects];	
		PERFORM spImportPjsDocumentInstance(pDocumentId, lCurrentInstance, pUid);
	END LOOP;
	
	lRes.result = 1;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spImportPjsDocumentVersion(
	pDocumentId int,
	pXml xml,	
	pCommentsXml xml,
	pUid int
) TO iusrpmt;
