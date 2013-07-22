DROP TYPE ret_spUpdateTableFigCitationsBeforeDelete CASCADE;

CREATE TYPE ret_spUpdateTableFigCitationsBeforeDelete AS (
	result int
);

CREATE OR REPLACE FUNCTION spUpdateTableFigCitationsBeforeDelete(
	pInstanceId bigint,
	pUid int
)
  RETURNS ret_spUpdateTableFigCitationsBeforeDelete AS
$BODY$
	DECLARE
		lRes ret_spUpdateTableFigCitationsBeforeDelete;	
		lCitationType int;
		lRecord record;
	BEGIN			
			
		-- Първо маркираме цитациите като променени - на текущия и на всички файлове, които са след текущия (тяхната позиция се е променила).
		FOR lRecord IN
			SELECT i.id
			FROM pwt.document_object_instances m
			JOIN pwt.document_object_instances i ON i.document_id = m.document_id AND i.object_id = m.object_id AND i.parent_id = m.parent_id AND i.pos >= m.pos
			WHERE m.id = pInstanceId 
		LOOP
			PERFORM spUpdateTableFigCitations(lRecord.id, pUid);
		END LOOP;
		
		SELECT INTO lCitationType
			citation_type
		FROM spGetFigTableCitationType(pInstanceId);
		
		
		-- След това трием цитациите в които е цитирана само този елемент
		DELETE FROM pwt.citations c 
		WHERE pInstanceId = ANY(c.object_ids) AND array_upper(c.object_ids, 1) = 1 AND c.citation_type = lCitationType;
		
		-- След това махаме елемента от другите цитациите в които участва
		UPDATE pwt.citations c SET
			object_ids = array_pop(c.object_ids, pInstanceId)
		WHERE pInstanceId = ANY(c.object_ids) AND c.citation_type = lCitationType;
		
		
		lRes.result = 1;		
		RETURN lRes;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spUpdateTableFigCitationsBeforeDelete(
	pInstanceId bigint,
	pUid int
) TO iusrpmt;
