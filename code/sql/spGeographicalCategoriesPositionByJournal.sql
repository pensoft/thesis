DROP FUNCTION IF EXISTS spGeographicalCategoriesPositionByJournal();

CREATE FUNCTION spGeographicalCategoriesPositionByJournal() RETURNS integer
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
		  FROM geographical_categories
		) n;
		
		DELETE FROM geographical_categories_byjournal;
		
		FOR i IN array_lower(lJournals, 1) .. array_upper(lJournals, 1) LOOP
			
			PERFORM spInsertGeogrCategoriesByJournal(lJournals[i]);
			--RAISE NOTICE 'lJournals[i]: %', lJournals[i];
		END LOOP; 
		
		PERFORM spPregenerateGeographicalCategoriesPositionByJournal(0,0,'');
		
		RETURN 1;
	END
	$$;


ALTER FUNCTION public.spGeographicalCategoriesPositionByJournal() OWNER TO postgres;

--SELECT * FROM spGeographicalCategoriesPositionByJournal();