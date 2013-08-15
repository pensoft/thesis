DROP TYPE ret_spGenerateSupFileCitationPreview CASCADE;

CREATE TYPE ret_spGenerateSupFileCitationPreview AS (
	citation_id bigint,
	preview varchar
);

CREATE OR REPLACE FUNCTION spGenerateSupFileCitationPreview(
	pCitationId bigint
)
  RETURNS ret_spGenerateSupFileCitationPreview AS
$BODY$
	DECLARE
		lRes ret_spGenerateFiguresCitationPreview;		
		
		lXrefTemp varchar;				
		lTemp varchar;		
		
		lRecord record;		
		lRecord2 record;
		lRecord3 record;
		
		lCitatedFiles bigint[];
		
		lIter int;		
		lSupFileCitationType int = 4;
		lTitleFieldId bigint = 214;
		lDocumentId bigint;
		lSupFileObjectId bigint = 55;
	BEGIN		
		
		lXrefTemp = '';		
		lTemp = '';
		
		SELECT INTO lDocumentId 
			document_id
		FROM pwt.citations
		WHERE id = pCitationId;
				
		
		SELECT INTO lRecord * 
		FROM pwt.citations 
		WHERE id = pCitationId AND citation_type = lSupFileCitationType;
		
		IF lRecord.id IS NULL THEN
			RETURN lRes;
		END IF;
				
		lRes.citation_id = lRecord.id;
				
		
		<<lCitatedFilesLoop>>
		FOR lRecord2 IN
			SELECT * 
			FROM pwt.document_object_instances
			WHERE id = ANY(lRecord.object_ids)
			ORDER BY pos ASC
		LOOP			
			lCitatedFiles = array_append(lCitatedFiles, lRecord2.id);		
		END LOOP lCitatedFilesLoop;
		
		RAISE NOTICE 'Citated %', lCitatedFiles;
				
		lTemp = 'Suppl. material';
		
		IF array_upper(lCitatedFiles, 1) > 1 THEN -- plural form
			lTemp = lTemp || 's';
		END IF;
		lTemp = lTemp || ' ';
		
		lIter = 1;
		
		RAISE NOTICE 'SELECT i.*, ic.idx
			FROM pwt.document_object_instances i
			JOIN (
				SELECT *, row_number() over (order by pos ASC nulls last) as idx
				FROM pwt.document_object_instances
				WHERE document_id = % AND object_id = %
			) ic ON ic.id = i.id
			WHERE i.id = ANY(%) AND i.is_confirmed = true
			ORDER BY i.pos ASC', lDocumentId, lSupFileObjectId, lRecord.object_ids;
		FOR lRecord2 IN
			SELECT i.*, ic.idx
			FROM pwt.document_object_instances i
			JOIN (
				SELECT *, row_number() over (order by pos ASC nulls last) as idx
				FROM pwt.document_object_instances
				WHERE document_id = lDocumentId AND object_id = lSupFileObjectId AND is_confirmed = true
			) ic ON ic.id = i.id
			WHERE i.id = ANY(lRecord.object_ids) AND i.is_confirmed = true
			ORDER BY i.pos ASC
		LOOP
			IF lIter > 1 THEN
				lTemp = lTemp || ', ';
			END IF;
			lTemp = lTemp || '<xref class="hide" type="suppl" rid="' || coalesce(lRecord2.id::varchar, '') || '" tblnumber="' || coalesce(lRecord2.idx::varchar, '') || '">' ||
						coalesce(lRecord2.idx::varchar, '') ||
					 '</xref>';
			lIter = lIter + 1;
		END LOOP;
		
				
		
		-- Накрая добавяме xref-овете
		lTemp = coalesce(lTemp, '') || lXrefTemp;
		lRes.preview = lTemp;
		RETURN lRes;		
	END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER;
GRANT EXECUTE ON FUNCTION spGenerateSupFileCitationPreview(
	pCitationId bigint
) TO iusrpmt;
