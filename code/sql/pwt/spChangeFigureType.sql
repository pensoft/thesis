DROP TYPE ret_spChangeFigureType CASCADE;
CREATE TYPE ret_spChangeFigureType AS (
	result int
);

CREATE OR REPLACE FUNCTION spChangeFigureType(
	pFigureInstanceId bigint, 
	pFigureType int,
	pUid integer
)
  RETURNS ret_spChangeFigureType AS
$BODY$
DECLARE
	lRes ret_spChangeFigureType;
	lFigureTypeFieldId int = 488;	
	lFigureCreationRuleId int = 11;
	lFigureTypeDataSrcId int;	
	lCheck boolean;
	lRecord record;
	lFigureObjectId int;
BEGIN 
	lRes.result = 1;
	
	SELECT INTO lFigureTypeDataSrcId
		data_src_id
	FROM  pwt.instance_field_values 
	WHERE instance_id = pFigureInstanceId AND field_id = lFigureTypeFieldId;
	
	SELECT INTO lCheck
		result
	FROM spCheckIfIdIsInDataSrc(pFigureType, lFigureTypeDataSrcId);
	
	IF coalesce(lCheck, false) <> true THEN
		RAISE EXCEPTION 'pwt.wrongFigureType';
	END IF;	
	
	UPDATE pwt.instance_field_values SET
		value_int = pFigureType
	WHERE instance_id = pFigureInstanceId AND field_id = lFigureTypeFieldId;
	
	SELECT INTO lFigureObjectId  
		result 
	FROM spGetCustomCreateObject(lFigureCreationRuleId, ARRAY[pFigureType]);
	
	IF EXISTS (SELECT * 
		FROM pwt.document_object_instances
		WHERE parent_id = pFigureInstanceId AND object_id = lFigureObjectId
	) THEN
		RETURN lRes;
	END IF;

	-- Remove all the other instances(i.e. plate, video, image)
	FOR lRecord IN
		SELECT i.id
		FROM pwt.document_object_instances i
		WHERE parent_id = pFigureInstanceId AND object_id IN (
				SELECT object_id 
				FROM pwt.custom_object_creation_combinations
				WHERE custom_object_creation_id = lFigureCreationRuleId 
			UNION
				SELECT default_object_id 
				FROM pwt.custom_object_creation
				WHERE id = lFigureCreationRuleId 
		)
	LOOP
		PERFORM spRemoveInstance(lRecord.id, pUid);
	END LOOP;
	RAISE NOTICE 'FigObj %', lFigureObjectId;
	PERFORM spCreateNewInstance(pFigureInstanceId, lFigureObjectId, pUid);
	
	RETURN lRes;
 
END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
GRANT EXECUTE ON FUNCTION spChangeFigureType(
	pFigureInstanceId bigint, 
	pFigureType int,
	pUid integer
) TO iusrpmt;
