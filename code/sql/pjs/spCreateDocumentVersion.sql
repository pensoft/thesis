DROP TYPE ret_spCreateDocumentVersion CASCADE;
CREATE TYPE ret_spCreateDocumentVersion AS (
	id int
);

CREATE OR REPLACE FUNCTION spCreateDocumentVersion(
	pDocumentId bigint,
	pUid int,
	pVersionType int,
	pVersionToCopyFromId bigint
)
  RETURNS ret_spCreateDocumentVersion AS
$BODY$
	DECLARE		
		lRes ret_spCreateDocumentVersion;
		lAuthorVersionType int;
		lVersNum int;
	BEGIN				
		lAuthorVersionType = 1;
		
		SELECT INTO lVersNum max(version_num)
		FROM pjs.document_versions
		WHERE document_id = pDocumentId AND version_type_id = lAuthorVersionType;
		
		IF(pVersionType = lAuthorVersionType) THEN
			lVersNum = coalesce(lVersNum, 0) + 1;
		END IF;
	
		INSERT INTO pjs.document_versions(uid, version_type_id, document_id, version_num) VALUES (pUid, pVersionType, pDocumentId, lVersNum);
		lRes.id = currval('pjs.document_versions_id_seq');
		
		INSERT INTO pjs.pwt_document_versions (version_id, xml)  
		SELECT lRes.id, xml FROM pjs.pwt_document_versions WHERE version_id = pVersionToCopyFromId;
		
		IF pVersionToCopyFromId IS NOT NULL THEN
			PERFORM pjs.spCopyVersionComments(pVersionToCopyFromId, lRes.id);
		END IF;
	
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCreateDocumentVersion(
	pDocumentId bigint,
	pUid int,
	pVersionType int,
	pVersionToCopyFromId bigint
) TO iusrpmt;
