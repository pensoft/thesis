DROP TYPE IF EXISTS ret_spDeleteAllDocuments CASCADE;
CREATE TYPE ret_spDeleteAllDocuments AS (
	result int
);

CREATE OR REPLACE FUNCTION spDeleteAllDocuments(pDontDeleteDocIds bigint[])
  RETURNS ret_spDeleteAllDocuments AS
$BODY$
	DECLARE
		lRes ret_spDeleteAllDocuments;
		lDocs RECORD;
	BEGIN		
		
		FOR lDocs IN (SELECT id FROM pjs.documents WHERE id <> ALL(pDontDeleteDocIds)) LOOP
			PERFORM spDeleteDocument(lDocs.id);
		END LOOP;
		
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spDeleteAllDocuments(pDontDeleteDocIds bigint[]) TO iusrpmt;
