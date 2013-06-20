CREATE OR REPLACE FUNCTION pwt.spfixchecklistlocalityfields(
	pInstanceId bigint, -- InstanceId на Locality
	pUid int
)
  RETURNS integer AS
$BODY$
	DECLARE
		lTypeFieldId integer;
		lTypeValue integer;
		lFieldIdsByType int[];
	BEGIN
		lTypeFieldId = 445;
		lFieldIdsByType = ARRAY[109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 129, 132, 133, 134, 446, 447, 448];
		
		-- Взимаме Стойността на полето Type за съответния Instance
		SELECT INTO lTypeValue value_int FROM pwt.instance_field_values WHERE instance_id = pInstanceId AND field_id = lTypeFieldId;
			
		-- Махаме Id-тата на полетата, които не трябва да зачистваме
		CASE lTypeValue
			WHEN 1 THEN
				lFieldIdsByType = array_pop(lFieldIdsByType, 109);
				lFieldIdsByType = array_pop(lFieldIdsByType, 110);
				lFieldIdsByType = array_pop(lFieldIdsByType, 111);
				lFieldIdsByType = array_pop(lFieldIdsByType, 112);
				lFieldIdsByType = array_pop(lFieldIdsByType, 113);
				lFieldIdsByType = array_pop(lFieldIdsByType, 114);
				lFieldIdsByType = array_pop(lFieldIdsByType, 115);
				lFieldIdsByType = array_pop(lFieldIdsByType, 116);
				lFieldIdsByType = array_pop(lFieldIdsByType, 117);
				lFieldIdsByType = array_pop(lFieldIdsByType, 118);
				lFieldIdsByType = array_pop(lFieldIdsByType, 119);
				lFieldIdsByType = array_pop(lFieldIdsByType, 120);
				lFieldIdsByType = array_pop(lFieldIdsByType, 121);
				lFieldIdsByType = array_pop(lFieldIdsByType, 122);
				lFieldIdsByType = array_pop(lFieldIdsByType, 123);
				lFieldIdsByType = array_pop(lFieldIdsByType, 124);
				lFieldIdsByType = array_pop(lFieldIdsByType, 125);
				lFieldIdsByType = array_pop(lFieldIdsByType, 126);
				lFieldIdsByType = array_pop(lFieldIdsByType, 129);
				lFieldIdsByType = array_pop(lFieldIdsByType, 132);
				lFieldIdsByType = array_pop(lFieldIdsByType, 134);
			WHEN 2 THEN
				lFieldIdsByType = array_pop(lFieldIdsByType, 446);
				lFieldIdsByType = array_pop(lFieldIdsByType, 447);
			WHEN 3 THEN
				lFieldIdsByType = array_pop(lFieldIdsByType, 448);
		END CASE;
		
		-- Зачистваме полетата, които не са на избрания Rank
		UPDATE pwt.instance_field_values 
		SET value_str = ''
		WHERE instance_id = pInstanceId
			AND field_id = ANY(lFieldIdsByType);

		RETURN 1;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

ALTER FUNCTION pwt.spfixchecklistlocalityfields(bigint, int) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spfixchecklistlocalityfields(bigint, int) TO public;
GRANT EXECUTE ON FUNCTION pwt.spfixchecklistlocalityfields(bigint, int) TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spfixchecklistlocalityfields(bigint, int) TO iusrpmt;