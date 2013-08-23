DROP TYPE ret_spSaveArticleLocality CASCADE;
CREATE TYPE ret_spSaveArticleLocality AS (
	id bigint
);

CREATE OR REPLACE FUNCTION spSaveArticleLocality(
	pArticleId bigint,	
	pLatitude float,
	pLongitude float,
	pInstanceIds bigint[]
)
  RETURNS ret_spSaveArticleLocality AS
$BODY$
	DECLARE		
		lRes ret_spSaveArticleLocality;		
		lArticleElementId bigint;
	BEGIN				
		SELECT INTO lArticleElementId
			id
		FROM pjs.article_localities 
		WHERE article_id = pArticleId AND latitude = pLatitude AND longitude = pLongitude;
		
		IF lArticleElementId IS NULL THEN
			INSERT INTO pjs.article_localities(article_id, latitude, longitude)
				VALUES (pArticleId, pLatitude, pLongitude);
			lArticleElementId = currval('pjs.article_localities_id_seq'::regclass);
		END IF;	
		
		DELETE FROM pjs.article_instance_localities 
		WHERE locality_id = lArticleElementId;
		
		FOR lIter IN 1 .. coalesce(array_upper(pInstanceIds, 1), 0) LOOP
			IF coalesce(pInstanceIds[lIter], 0) > 0 THEN
				INSERT INTO pjs.article_instance_localities(instance_id, locality_id) 
					VALUES (pInstanceIds[lIter], lArticleElementId);
			END IF;
		END LOOP;
		
		lRes.id = lArticleElementId;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveArticleLocality(
	pArticleId bigint,	
	pLatitude float,
	pLongitude float,
	pInstanceIds bigint[]
) TO iusrpmt;
