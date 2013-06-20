DROP TYPE ret_spGetCustomCreateObject CASCADE;
CREATE TYPE ret_spGetCustomCreateObject AS (
	result bigint
);

CREATE OR REPLACE FUNCTION spGetCustomCreateObject(
	pCustomCreateObjectId bigint,
	pParameters int[]
)
  RETURNS ret_spGetCustomCreateObject AS
$BODY$
	DECLARE
		lRes ret_spGetCustomCreateObject;	
		
		lCombination record;
		lCombinationParameters record;
	BEGIN
		<<CombinationLoop>>
		FOR lCombination IN 
			SELECT * FROM pwt.custom_object_creation_combinations 
			WHERE custom_object_creation_id = pCustomCreateObjectId
			ORDER BY priority ASC
		LOOP
			-- RAISE NOTICE 'Combination %', lCombination.id;
			<<ParametersLoop>>
			FOR lCombinationParameters IN
				SELECT cd.value, p.ord
				FROM pwt.custom_object_creation_combinations_details cd
				JOIN pwt.custom_object_creation_parameters p ON p.id = cd.parameter_id
				WHERE cd.combination_id = lCombination.id				
			LOOP
				IF pParameters IS NULL OR array_upper(pParameters, 1) < lCombinationParameters.ord THEN
					CONTINUE CombinationLoop;
				END IF;
				-- RAISE  NOTICE 'parm %, val %', pParameters[lCombinationParameters.ord], lCombinationParameters.value;
				IF pParameters[lCombinationParameters.ord] IS NULL OR pParameters[lCombinationParameters.ord] <> lCombinationParameters.value THEN
					CONTINUE CombinationLoop;
				END IF;
				
				--RAISE NOTICE 'ArrSize %, Ord %, ArrValue %, Value %', array_upper(pParameters, 1), lCombinationParameters.ord, pParameters[lCombinationParameters.ord], lCombinationParameters.value;
								
			END LOOP ParametersLoop;		
			-- Всички параметри съвпадат - това е нашата комбинация
			lRes.result = lCombination.object_id;
				
			RETURN lRes;
		END LOOP CombinationLoop;
		
		SELECT INTO lRes.result default_object_id FROM  pwt.custom_object_creation WHERE id = pCustomCreateObjectId;

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetCustomCreateObject(
	pCustomCreateObjectId bigint,
	pParameters int[]
) TO iusrpmt;
