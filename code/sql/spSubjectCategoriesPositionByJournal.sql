DROP FUNCTION IF EXISTS spSubjectCategoriesPositionByJournal();

CREATE FUNCTION spSubjectCategoriesPositionByJournal() RETURNS integer
    LANGUAGE plpgsql SECURITY DEFINER
    AS $$
	DECLARE
		lRecord record;
		lPos varchar;
		lJournals int[];
	BEGIN
		
		SELECT INTO lJournals array_agg(distinct v)
		FROM (
		  SELECT unnest(journal_ids) v
		  FROM subject_categories
		) n;
		
		DELETE FROM subject_categories_byjournal;
		
		FOR i IN array_lower(lJournals, 1) .. array_upper(lJournals, 1) LOOP
			
			PERFORM spInsertSubjectCategoriesByJournal(lJournals[i]);
			--RAISE NOTICE 'lJournals[i]: %', lJournals[i];
		END LOOP; 
		
		PERFORM spPregenerateSubjectCategoriesPositionByJournal(0,0,'');
		
		RETURN 1;
	END
	$$;


ALTER FUNCTION public.spSubjectCategoriesPositionByJournal() OWNER TO postgres;
