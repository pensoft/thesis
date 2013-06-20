DROP FUNCTION IF EXISTS spPregenerateSubjectCategoriesPositionByJournal(pparent bigint, prootnode bigint, pposition character varying);

CREATE FUNCTION spPregenerateSubjectCategoriesPositionByJournal(pparent bigint, prootnode bigint, pposition character varying) RETURNS integer
    LANGUAGE plpgsql SECURITY DEFINER
    AS $$
	DECLARE
		lRecord record;
		lPos varchar;
	BEGIN
		
		lPos := pPosition || 'AA';
		
		IF pParent = 0 THEN
			FOR lRecord IN SELECT * FROM subject_categories_byjournal WHERE root = pParent ORDER BY oldpos ASC LOOP
				--RAISE NOTICE 'lPos: % , Id: %', lPos, lRecord.id;
				UPDATE subject_categories_byjournal SET pos = lPos WHERE id = lRecord.id;
				PERFORM spPregenerateSubjectCategoriesPositionByJournal(lRecord.id, lRecord.id, lPos);
				
				lPos := pPosition || ForumGetNextOrd(lPos);
				
			END LOOP;
		ELSE
			FOR lRecord IN SELECT * FROM subject_categories_byjournal WHERE root = pParent ORDER BY oldpos ASC LOOP
				--RAISE NOTICE '2lPos: % , Id: %', lPos, lRecord.id;
				UPDATE subject_categories_byjournal SET pos = lPos WHERE id = lRecord.id;
				PERFORM spPregenerateSubjectCategoriesPositionByJournal(lRecord.id, pRootNode, lPos);
				
				lPos := pPosition || ForumGetNextOrd(lPos);
			
			END LOOP;
			
		END IF;
		
		RETURN 1;
	END
	$$;


ALTER FUNCTION public.spPregenerateSubjectCategoriesPositionByJournal(pparent bigint, prootnode bigint, pposition character varying) OWNER TO postgres;
