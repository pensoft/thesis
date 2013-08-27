DROP TYPE ret_spSaveTaxonPreview CASCADE;
CREATE TYPE ret_spSaveTaxonPreview AS (
	id bigint,
	cache_id bigint
);

CREATE OR REPLACE FUNCTION spSaveTaxonPreview(
	pTaxonId bigint,	
	pPreview varchar
)
  RETURNS ret_spSaveTaxonPreview AS
$BODY$
	DECLARE		
		lRes ret_spSaveTaxonPreview;	
		lElementCacheTypeId int = 7;				
	BEGIN				
		SELECT INTO lRes.id, lRes.cache_id 
			id, cache_id
		FROM pjs.taxons 
		WHERE id = pTaxonId;
		
		IF lRes.id IS NULL THEN
			RAISE EXCEPTION 'pjs.noSuchTaxon';
		END IF;
		
		IF lRes.cache_id  IS NULL THEN
			INSERT INTO pjs.article_cached_items(cached_val, item_type)
				VALUES (pPreview, lElementCacheTypeId);
				
			lRes.cache_id  = currval('pjs.article_cached_items_id_seq'::regclass);
			UPDATE pjs.taxons SET
				cache_id = lRes.cache_id
			WHERE id = pTaxonId;
		ELSE
			UPDATE pjs.article_cached_items SET
				cached_val = pPreview,
				lastmoddate = now()
			WHERE id = lRes.cache_id ;
		END IF;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveTaxonPreview(
	pTaxonId bigint,	
	pPreview varchar
) TO iusrpmt;
