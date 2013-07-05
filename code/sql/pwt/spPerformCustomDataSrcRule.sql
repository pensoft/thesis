DROP TYPE ret_spPerformCustomDataSrcRule CASCADE;
CREATE TYPE ret_spPerformCustomDataSrcRule AS (
	result int
);

CREATE OR REPLACE FUNCTION spPerformCustomDataSrcRule(
	pRuleId int,
	pParameters int[]
)
  RETURNS ret_spPerformCustomDataSrcRule AS
$BODY$
	DECLARE
		lRes ret_spPerformCustomDataSrcRule;	
		
		lCombination record;
		lCombinationParameters record;
	BEGIN
		<<CombinationLoop>>
		FOR lCombination IN 
			SELECT * FROM pwt.custom_data_src_rules_combinations 
			WHERE rule_id = pRuleId
			ORDER BY priority ASC
		LOOP
			-- RAISE NOTICE 'Combination %', lCombination.id;
			<<ParametersLoop>>
			FOR lCombinationParameters IN
				SELECT cd.value, p.ord
				FROM pwt.custom_data_src_rules_combinations_details cd
				JOIN pwt.custom_data_src_rules_parameters p ON p.id = cd.parameter_id
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
			lRes.result = lCombination.data_src_id;
				
			RETURN lRes;
		END LOOP CombinationLoop;
		
		SELECT INTO lRes.result 
			default_data_src_id 
		FROM  pwt.custom_data_src_rules 
		WHERE id = pRuleId;

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spPerformCustomDataSrcRule(
	pRuleId int,
	pParameters int[]
) TO iusrpmt;
