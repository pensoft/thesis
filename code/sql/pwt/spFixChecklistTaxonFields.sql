CREATE OR REPLACE FUNCTION pwt.spFixChecklistTaxonFields(
	pInstanceId bigint, -- InstanceId на Taxon-a
	pUid int
)
  RETURNS integer AS
$BODY$
	DECLARE
		lRankFieldId integer;
		lRankValue integer;
		lFieldIdsByRank int[];
	BEGIN
		lRankFieldId = 414;
		lFieldIdsByRank = ARRAY[48, 49, 417, 418, 419, 420, 421, 422, 423, 424, 425, 426, 427, 428, 429, 430, 431, 432, 433, 434, 435, 436];
		
		-- Взимаме Стойността на полето Rank за съответния Instance
		SELECT INTO lRankValue value_int FROM pwt.instance_field_values WHERE instance_id = pInstanceId AND field_id = lRankFieldId;
			
		-- Махаме Id-тата на полетата, които не трябва да зачистваме
		CASE lRankValue
			WHEN 1 THEN
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 419);
			WHEN 2 THEN
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 420);
			WHEN 3 THEN
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 421);
			WHEN 4 THEN
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 422);
			WHEN 5 THEN
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 423);
			WHEN 6 THEN
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 424);
			WHEN 7 THEN
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 425);
			WHEN 8 THEN
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 426);
			WHEN 9 THEN
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 427);
			WHEN 10 THEN
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 428);
			WHEN 11 THEN
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 429);
			WHEN 12 THEN
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 430);
			WHEN 13 THEN
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 431);
			WHEN 14 THEN
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 432);
			WHEN 15 THEN
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 433);
			WHEN 16 THEN
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 434);
			WHEN 17 THEN
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 48);
			WHEN 18 THEN
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 417);
			WHEN 19 THEN
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 48);
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 417);
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 49);
			WHEN 20 THEN
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 48);
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 417);
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 49);
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 418);
			WHEN 21 THEN
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 48);
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 417);
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 49);
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 435);
			WHEN 22 THEN
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 48);
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 417);
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 49);
				lFieldIdsByRank = array_pop(lFieldIdsByRank, 436);
		END CASE;
		
		-- Зачистваме полетата, които не са на избрания Rank
		UPDATE pwt.instance_field_values 
		SET value_str = ''
		WHERE instance_id = pInstanceId
			AND field_id = ANY(lFieldIdsByRank);

		RETURN 1;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

ALTER FUNCTION pwt.spFixChecklistTaxonFields(bigint, int) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spFixChecklistTaxonFields(bigint, int) TO public;
GRANT EXECUTE ON FUNCTION pwt.spFixChecklistTaxonFields(bigint, int) TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spFixChecklistTaxonFields(bigint, int) TO iusrpmt;