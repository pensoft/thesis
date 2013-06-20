DROP FUNCTION IF EXISTS spInsertSubjectCategoriesByJournal(pJournalId integer);

CREATE OR REPLACE FUNCTION spInsertSubjectCategoriesByJournal(pJournalId integer)
  RETURNS integer AS
$BODY$
	DECLARE
		lRecord record;
		
		
	BEGIN
		
		FOR  lRecord IN  	SELECT DISTINCT ON (c.id) c.id, c.pos, coalesce(p.id, 0) as parent_id, c.name, p.name as parent_name, coalesce(p.rootnode,0) as rootnode
							FROM subject_categories c
							LEFT JOIN subject_categories p ON substr(c.pos, 1, char_length(p.pos)) = p.pos  AND pJournalId = any (p.journal_ids) AND p.id <> c.id
							WHERE pJournalId = any (c.journal_ids) -- and (c.rootnode = 6 or c.id = 6)
							ORDER BY c.id, p.pos DESC LOOP
			
			INSERT INTO subject_categories_byjournal(
						id, root, oldpos, journal_id
						) 
						VALUES (
							lRecord.id,
							lRecord.parent_id,
							lRecord.pos,
							pJournalId
						);
			
			--RAISE NOTICE 'ID:%, NodeName:%, Parent_id:%, ParentNodeName:%', lRecord.id, lRecord.name, lRecord.parent_id, lRecord.parent_name;
			
		END LOOP;
		
		RETURN 1;
	END
	$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spInsertSubjectCategoriesByJournal(pJournalId integer) OWNER TO postgres;
--select * from spInsertSubjectCategoriesByJournal(1);