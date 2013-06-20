-- Function: SecSitesRearrange(pParentId int4, pSiteIds _int4)

DROP FUNCTION SecSitesRearrange(pParentId int4, pSiteIds _int4);

CREATE OR REPLACE FUNCTION SecSitesRearrange(pParentId int4, pSiteIds _int4)
  RETURNS int4 AS
$BODY$
DECLARE
	arrSize int;
	arrIter int;
	lSid int;
	lSiteId int;
	lCount int;
BEGIN
	arrSize := array_upper(pSiteIds, 1);
	arrIter := 1;
	lCount :=1;
	WHILE arrIter <= arrSize 
		LOOP
			lSiteId := pSiteIds[arrIter];

			IF lSiteId > 0 THEN
				IF pParentId > 0 THEN 
					UPDATE secsites s SET
						ord = lCount
						FROM secsites s1 
					WHERE s1.id = pParentId AND ( (s.url ILIKE (s1.url || '%') AND s.cnt = s1.cnt + 1 AND s.type = 1) OR (s.type = 2 AND s.url = s1.url)) AND s.id = lSiteId;
					lCount := lCount + 1;
				ELSE
					UPDATE secsites s SET
						ord = lCount
					WHERE s.cnt <=2 AND s.id = lSiteId;
					lCount := lCount + 1;				
				END IF;
			END IF;
			arrIter := arrIter + 1;
		END LOOP;

	RETURN 1;
END;
$BODY$
  LANGUAGE 'plpgsql' SECURITY DEFINER;
ALTER FUNCTION SecSitesRearrange(pParentId int4, pSiteIds _int4) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION SecSitesRearrange(pParentId int4, pSiteIds _int4) TO postgres84;
GRANT EXECUTE ON FUNCTION SecSitesRearrange(pParentId int4, pSiteIds _int4) TO iusrpmt;
