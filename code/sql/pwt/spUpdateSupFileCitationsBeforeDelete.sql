DROP TYPE ret_spUpdateSupFileCitationsBeforeDelete CASCADE;

CREATE TYPE ret_spUpdateSupFileCitationsBeforeDelete AS (
	result int
);

CREATE OR REPLACE FUNCTION spUpdateSupFileCitationsBeforeDelete(
	pSupFileId bigint,
	pUid int
)
  RETURNS ret_spUpdateSupFileCitationsBeforeDelete AS
$BODY$
	DECLARE
		lRes ret_spUpdateSupFileCitationsBeforeDelete;	
		lCitationType int = 4;
		lRecord record;
	BEGIN			
			
		-- Първо маркираме цитациите като променени - на текущия и на всички файлове, които са след текущия (тяхната позиция се е променила).
		FOR lRecord IN
			SELECT i.id
			FROM pwt.document_object_instances m
			JOIN pwt.document_object_instances i ON i.document_id = m.document_id AND i.object_id = m.object_id AND i.parent_id = m.parent_id AND i.pos >= m.pos
			WHERE m.id = pSupFileId 
		LOOP
			PERFORM spUpdateSupFileCitations(lRecord.id, pUid);
		END LOOP;
		
		
		
		
		-- След това трием цитациите в които е цитирана само този елемент
		DELETE FROM pwt.citations c 
		WHERE pSupFileId = ANY(c.object_ids) AND array_upper(c.object_ids, 1) = 1 AND c.citation_type = lCitationType;
		
		-- След това махаме елемента от другите цитациите в които участва
		UPDATE pwt.citations c SET
			object_ids = array_pop(c.object_ids, pSupFileId)
		WHERE pSupFileId = ANY(c.object_ids) AND c.citation_type = lCitationType;
		
		
		lRes.result = 1;		
		RETURN lRes;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spUpdateSupFileCitationsBeforeDelete(
	pSupFileId bigint,
	pUid int
) TO iusrpmt;
