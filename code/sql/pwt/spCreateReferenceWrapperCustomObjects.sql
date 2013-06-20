DROP TYPE ret_spCreateReferenceWrapperCustomObjects CASCADE;
CREATE TYPE ret_spCreateReferenceWrapperCustomObjects AS (
	result int
);

CREATE OR REPLACE FUNCTION spCreateReferenceWrapperCustomObjects(
	pInstanceId bigint, -- id на reference-a
	pUid int
)
  RETURNS ret_spCreateReferenceWrapperCustomObjects AS
$BODY$
	DECLARE
		lRes ret_spCreateReferenceWrapperCustomObjects;	
		lReferenceWrapperObjectId bigint;
		lReferenceWrapperInstanceId bigint;
		lObjectId bigint;
		
		lReferenceTypeFieldId bigint;
		lReferenceTypeValue int;
		
		lObjectCustomCreationId bigint;
		lRecord record;
	BEGIN
		lReferenceWrapperObjectId = 97;
		lReferenceTypeFieldId = 269;
		lObjectCustomCreationId = 7;
				
		-- Трябва да разберем точно кой тип референция да създадем и да го добавим на wrappera
		SELECT INTO lReferenceWrapperInstanceId i.id 
			FROM pwt.document_object_instances i
			JOIN pwt.document_object_instances p ON
				p.document_id = i.document_id AND char_length(p.pos) < char_length(i.pos) AND 
				p.pos = substring(i.pos, 1, char_length(p.pos))				
			WHERE p.id = pInstanceId AND i.object_id = lReferenceWrapperObjectId;
		
		SELECT INTO lReferenceTypeValue value_int
		FROM pwt.instance_field_values 
		WHERE instance_id = pInstanceId AND field_id = lReferenceTypeFieldId;
		
		
		SELECT INTO lObjectId result FROM spGetCustomCreateObject(lObjectCustomCreationId, ARRAY[lReferenceTypeValue]);
		
		-- SELECT INTO lRecord * FROM pwt.document_object_instances i WHERE id = pInstanceId
		-- RAISE NOTICE 'ObjectId %, WrapperId % Rank %, WrapperObjectId %, WrapperTDO %', lObjectId, pInstanceId, lReferenceTypeValue, lRecord.object_id, lRecord.document_template_object_id;
		
		IF coalesce(lObjectId, 0) <> 0 THEN
			 PERFORM spCreateNewInstance(lReferenceWrapperInstanceId, lObjectId, pUid);
		END IF;
		
		
		lRes.result = 1;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCreateReferenceWrapperCustomObjects(	
	pInstanceId bigint,
	pUid int
) TO iusrpmt;
