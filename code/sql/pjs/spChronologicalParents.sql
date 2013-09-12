CREATE OR REPLACE FUNCTION pjs."spChronologicalParents"(pIds int[]) RETURNS int[] AS 
 $BODY$
	SELECT array_agg(id) FROM
		(SELECT DISTINCT c1.id
		 FROM (SELECT c.pos as pos
			   FROM  public.chronological_categories c 
			   WHERE c.id = ANY( $1 )
			  ) AS t1 
		 JOIN public.chronological_categories c1 ON (t1.pos like c1.pos || '%')
		) AS A
 $BODY$
LANGUAGE sql STABLE;