DROP TYPE ret_spFixWrapperCustomObjectId CASCADE;
CREATE TYPE ret_spFixWrapperCustomObjectId AS (
	result int
);

/**
	В тази функция ще сменяме типа на подобекта от wrapper-a.
	T.e. ако wrapper-а има обект, който може да е тип1, тип2 и тип3 и в момента
	е от тип2, а трябва да го направим от тип3.
	За целта създаваме нов подобект от тип 2.
	Ъпдейтваме всичките общи field-ове на 2та типа със стойностите на първоначално съществуващия обект
	и накрая трием излишния обект.
	Ако двата типа съвпадат не правим нищо.
*/	
CREATE OR REPLACE FUNCTION spFixWrapperCustomObjectId(
	pInstanceId bigint, -- id-то на wrapper-а
	pObjectId bigint, -- новия тип на подобекта
	pUid int
)
  RETURNS ret_spFixWrapperCustomObjectId AS
$BODY$
	DECLARE
		lRes ret_spFixWrapperCustomObjectId;	
		lCurrentObjectInstanceId bigint;
		lCurrentObjectObjectId bigint;
		lNewInstanceId bigint;
		lRecord record;
	BEGIN		
		SELECT INTO lCurrentObjectInstanceId, lCurrentObjectObjectId id, object_id FROM pwt.document_object_instances WHERE parent_id = pInstanceId;
		
		lRes.result = 1;
		IF lCurrentObjectObjectId IS NOT NULL AND lCurrentObjectObjectId = pObjectId THEN -- Двата типа съвпадат - не трябва да правим нищо
			RETURN lRes;
		END IF;
		
		-- Създаваме новия подобект
		SELECT INTO lNewInstanceId new_instance_id FROM spCreateNewInstance(pInstanceId, pObjectId, pUid);
		
		
		-- Ако има стар обект
		IF lCurrentObjectInstanceId IS NOT NULL THEN
			-- Ъпдейтваме field-овете
			UPDATE pwt.instance_field_values v SET
				value_int = v1.value_int,
				value_str = v1.value_str,
				value_arr_int = v1.value_arr_int,
				value_arr_str = v1.value_arr_str,
				value_date = v1.value_date,
				value_arr_date = v1.value_arr_date
			FROM pwt.instance_field_values v1
			JOIN pwt.document_object_instances i1 ON i1.id = v1.instance_id
			JOIN pwt.document_object_instances p1 ON p1.id = lCurrentObjectInstanceId
				AND i1.document_id = p1.document_id AND p1.pos = substring(i1.pos, 1, char_length(p1.pos))
			JOIN pwt.document_object_instances i ON true
			JOIN pwt.document_object_instances p ON p.id = lNewInstanceId
				AND i.document_id = p.document_id AND p.pos = substring(i.pos, 1, char_length(p.pos))
			WHERE v1.field_id = v.field_id AND i.id = v.instance_id;
			
			-- Трием стария подобект
			PERFORM spRemoveInstance(lCurrentObjectInstanceId, pUid);
		END IF;
		
		
		

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spFixWrapperCustomObjectId(	
	pInstanceId bigint, -- id-то на wrapper-а
	pObjectId bigint, -- новия тип на подобекта
	pUid int
) TO iusrpmt;
