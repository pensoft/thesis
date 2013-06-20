DROP FUNCTION IF EXISTS spTaxonCategoriesPositionByJournal();

CREATE FUNCTION spTaxonCategoriesPositionByJournal() RETURNS integer
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
		  FROM taxon_categories
		) n;
		
		DELETE FROM taxon_categories_byjournal;
		
		FOR i IN array_lower(lJournals, 1) .. array_upper(lJournals, 1) LOOP
			
			PERFORM spInsertTaxonCategoriesByJournal(lJournals[i]);
			--RAISE NOTICE 'lJournals[i]: %', lJournals[i];
		END LOOP; 
		
		PERFORM spPregenerateTaxonCategoriesPositionByJournal(0,0,'');
		
		RETURN 1;
	END
	$$;


ALTER FUNCTION public.spTaxonCategoriesPositionByJournal() OWNER TO postgres;

--SELECT * FROM spTaxonCategoriesPositionByJournal();