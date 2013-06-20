DROP FUNCTION IF EXISTS spChronologicalCategoriesPositionByJournal();

CREATE FUNCTION spChronologicalCategoriesPositionByJournal() RETURNS integer
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
		  FROM chronological_categories
		) n;
		
		DELETE FROM chronological_categories_byjournal;
		
		FOR i IN array_lower(lJournals, 1) .. array_upper(lJournals, 1) LOOP
			
			PERFORM spInsertChronoCategoriesByJournal(lJournals[i]);
			--RAISE NOTICE 'lJournals[i]: %', lJournals[i];
		END LOOP; 
		
		PERFORM spPregenerateChronologicalCategoriesPositionByJournal(0,0,'');
		
		RETURN 1;
	END
	$$;


ALTER FUNCTION public.spChronologicalCategoriesPositionByJournal() OWNER TO postgres;

--SELECT * FROM spChronologicalCategoriesPositionByJournal();