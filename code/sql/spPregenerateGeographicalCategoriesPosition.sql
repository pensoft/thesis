DROP FUNCTION IF EXISTS spPregenerateGeographicalCategoriesPosition(pparent bigint, prootnode bigint, pposition character varying);

CREATE FUNCTION spPregenerateGeographicalCategoriesPosition(pparent bigint, prootnode bigint, pposition character varying) RETURNS integer
    LANGUAGE plpgsql SECURITY DEFINER
    AS $$
	DECLARE
		lRecord record;
		lPos varchar;
	BEGIN
		
		lPos := pPosition || 'AA';
		
		IF pParent = 0 THEN
			FOR lRecord IN SELECT * FROM geographical_categories WHERE parentnode = pParent ORDER BY name ASC LOOP
				--RAISE NOTICE 'lPos: % , Id: %', lPos, lRecord.id;
				UPDATE geographical_categories SET pos = lPos WHERE id = lRecord.id;
				PERFORM spPregenerateGeographicalCategoriesPosition(lRecord.id, lRecord.id, lPos);
				
				lPos := pPosition || ForumGetNextOrd(lPos);
				
			END LOOP;
		ELSE
			FOR lRecord IN SELECT * FROM geographical_categories WHERE parentnode = pParent ORDER BY name ASC LOOP
				--RAISE NOTICE '2lPos: % , Id: %', lPos, lRecord.id;
				UPDATE geographical_categories SET pos = lPos WHERE id = lRecord.id;
				PERFORM spPregenerateGeographicalCategoriesPosition(lRecord.id, pRootNode, lPos);
				
				lPos := pPosition || ForumGetNextOrd(lPos);
			
			END LOOP;
			
		END IF;
		
		RETURN 1;
	END
	$$;


ALTER FUNCTION public.spPregenerateGeographicalCategoriesPosition(pparent bigint, prootnode bigint, pposition character varying) OWNER TO postgres;

--SELECT * FROM spPregenerateGeographicalCategoriesPosition(7,7,'');