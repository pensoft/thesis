DROP TYPE ret_spParseActionParameterSelector CASCADE;

CREATE TYPE ret_spParseActionParameterSelector AS (
	instance_id bigint,
	field_id bigint
);

/**
 * Тук ще връщаме id-то на инстанса и на field-a, които отговарят
 * на подадения параметър селектор за подадения инстанс
 * 
 * Пример:
 * 		instance_id:10
 * 		pSelectorString:12[2]/@13
 * 
 * Това значи да се върне field-а с id 13 на 2-я подинстанс, чийто object_id е 12 
 */
CREATE OR REPLACE FUNCTION spParseActionParameterSelector(
	pInstanceId bigint,
	pSelectorString varchar
)
  RETURNS ret_spParseActionParameterSelector AS
$BODY$
	DECLARE
		lRes ret_spParseActionParameterSelector;		
		
		lIter int;
		lMatches varchar[];
		lCurrentMatch varchar;
		lNextInstanceId bigint;
		lCurrentInstanceId bigint;
		lCurrentMatchObjectId bigint;
		lCurrentMatchIdx int;
		lSubMatch varchar[];
		lFirstChar character;
	BEGIN
		lNextInstanceId = pInstanceId;
		lMatches = regexp_split_to_array(pSelectorString, E'\/');
		
		FOR lIter IN 1 .. array_upper(lMatches, 1)
		LOOP
			lCurrentMatch = lMatches[lIter];			
			lFirstChar = substr(lCurrentMatch, 1, 1);
			IF lFirstChar = '@' THEN -- Стигнали сме до field-a - return-ваме
				lRes.instance_id = lNextInstanceId;
				lRes.field_id = substr(lCurrentMatch, 2)::bigint;
				RETURN lRes;
			END IF;
			IF lFirstChar = '$' THEN -- Взимаме parent-a
				lCurrentInstanceId = lNextInstanceId;
				SELECT INTO lNextInstanceId i.parent_id 
				FROM pwt.document_object_instances i
				WHERE i.id = lCurrentInstanceId;
				-- RAISE NOTICE 'Parent %, current %', lNextInstanceId, lCurrentInstanceId;
				IF lNextInstanceId IS NULL THEN -- няма такъв обект - край
					RETURN lRes;
				END IF;
				CONTINUE;
			END IF;
			lSubMatch = regexp_matches(lCurrentMatch, '(\d+)(\[(\d+)\])?');
			lCurrentMatchObjectId = lSubMatch[1]::bigint;
			IF lSubMatch[3] IS NOT NULL THEN
				-- Слагаме -1 понеже OFFSET-а е 0 базиран
				lCurrentMatchIdx = lSubMatch[3]::int - 1;				
			ELSE 
				lCurrentMatchIdx = 0;
			END IF;
			
			-- АКо по някаква причина не сме намерили текущия обект - продължаваме нататък
			IF coalesce(lCurrentMatchObjectId, 0) = 0 THEN
				CONTINUE;
			END IF;
			
			lCurrentInstanceId = lNextInstanceId;
			
			SELECT INTO lNextInstanceId i.id 
			FROM pwt.document_object_instances i
			JOIN pwt.document_object_instances p ON p.id = i.parent_id
			WHERE p.id = lCurrentInstanceId AND i.object_id = lCurrentMatchObjectId
			ORDER BY i.pos
			LIMIT 1 OFFSET lCurrentMatchIdx;
			
			IF lNextInstanceId IS NULL THEN -- няма такъв обект - край
				RETURN lRes;
			END IF;
		END LOOP;
		
		
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spParseActionParameterSelector(
	pInstanceId bigint,
	pSelectorString varchar
) TO iusrpmt;
