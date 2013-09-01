DROP TYPE ret_spGetArticleContentsInstances CASCADE;
CREATE TYPE ret_spGetArticleContentsInstances AS (
	instance_id bigint,
	display_name varchar,
	level int,
	pos varchar,
	parent_instance_id bigint,
	has_children int
);

CREATE OR REPLACE FUNCTION spGetArticleContentsInstances(
	pArticleId bigint
)
  RETURNS SETOF ret_spGetArticleContentsInstances AS
$BODY$
	DECLARE		
		lRes ret_spGetArticleContentsInstances;		
		lRecord record;
		lRecord2 record;
		lChecklistWrapperObjectId bigint = 203;
		lTreatmentsWrapperObjectId bigint = 54;
		lIdentificationKeysWrapperObjectId bigint = 24;
		
		lChecklistObjectId bigint = 204;
		lTreatmentsObjectId bigint = 41;
		lIdentificationKeysObjectId bigint = 23;
		
		lFiguresWrapperObjectId bigint = 236;
		lTablesWrapperObjectId bigint = 237;
		
		lSubSectionObjectId bigint = 50;
		lChildrenCount int = 0;
		cSubsectionTitleFieldId CONSTANT int = 211;
	BEGIN		
		<<lMainInstancesLoop>>
		FOR lRecord IN 
			SELECT i.*
			FROM pwt.document_object_instances i
			JOIN pjs.articles a ON a.pwt_document_id = i.document_id
			WHERE char_length(i.pos) = 2 AND a.id = pArticleId AND i.object_id NOT IN (lFiguresWrapperObjectId , lTablesWrapperObjectId)
			ORDER BY i.pos ASC
		LOOP
			IF NOT EXISTS (
				SELECT *  
				FROM pwt.document_object_instances i1 				
				WHERE i1.parent_id = lRecord.id AND i1.is_confirmed = true
			) AND NOT EXISTS (
				SELECT *  
				FROM pwt.instance_field_values f 
					WHERE f.instance_id = lRecord.id AND (
						f.value_str <> '' OR
						value_int IS NOT NULL OR
						array_upper(f.value_arr_int, 1) IS NOT NULL OR
						array_upper(f.value_arr_str, 1) IS NOT NULL OR
						value_date IS NOT NULL OR
						array_upper(f.value_arr_date, 1) IS NOT NULL
					)
			)THEN -- Empty instance
				CONTINUE lMainInstancesLoop;
			END IF;
			
			IF lRecord.object_id IN (lChecklistWrapperObjectId, lTreatmentsWrapperObjectId, lIdentificationKeysWrapperObjectId) THEN
				SELECT INTO lChildrenCount 
					count(*)
				FROM pwt.document_object_instances i
				WHERE parent_id = lRecord.id AND object_id IN (lChecklistObjectId, lIdentificationKeysObjectId) AND i.is_confirmed = true;
				
				IF lChildrenCount <> 1 THEN
					lRes.instance_id = lRecord.id;
					lRes.display_name = lRecord.display_name;
					lRes.pos = lRecord.pos;
					lRes.level = char_length(lRecord.pos) / 2;		
					lRes.parent_instance_id = null;
					lRes.has_children = 1;
					RETURN NEXT lRes;
				END IF;
				
				<<ChildrenLoop>>
				FOR lRecord2 IN
					SELECT *
					FROM pwt.document_object_instances i
					WHERE parent_id = lRecord.id AND object_id IN (lChecklistObjectId, lTreatmentsObjectId, lIdentificationKeysObjectId) AND i.is_confirmed = true
					ORDER BY pos ASC
				LOOP
					lRes.instance_id = lRecord2.id;
					lRes.display_name = lRecord2.display_name;
					lRes.has_children = 0;
					IF lChildrenCount > 1 or lRecord2.object_id = lTreatmentsObjectId THEN 
						lRes.pos = lRecord2.pos;
						lRes.level = char_length(lRecord2.pos) / 2;		
						lRes.parent_instance_id = lRecord.id;					
					ELSE 
						lRes.pos = lRecord.pos;
						lRes.level = char_length(lRecord.pos) / 2;		
						lRes.parent_instance_id = null;
					END IF;
					RETURN NEXT lRes;
				END LOOP ChildrenLoop;
				
			ELSE
				SELECT INTO lChildrenCount
					count(*)
				FROM  pwt.document_object_instances i
				WHERE parent_id = lRecord.id AND object_id IN (lSubSectionObjectId) AND i.is_confirmed = true;
			
				lRes.instance_id = lRecord.id;
				lRes.display_name = lRecord.display_name;
				lRes.pos = lRecord.pos;
				lRes.level = char_length(lRecord.pos) / 2;		
				lRes.parent_instance_id = null;
				
				IF lChildrenCount > 0 THEN
					lRes.has_children = 1;
				ELSE
					lRes.has_children = 0;
				END IF;
				RETURN NEXT lRes;
				
				--Check if there are subsections
				<<lSubsectionsLoop>>
				FOR lRecord2 IN
					SELECT *
					FROM pwt.document_object_instances i
					JOIN pwt.instance_field_values ifv on i.id = ifv.instance_id
					WHERE parent_id = lRecord.id AND object_id IN (lSubSectionObjectId) AND i.is_confirmed = true AND field_id = cSubsectionTitleFieldId
					ORDER BY pos ASC
				LOOP
					lRes.instance_id = lRecord2.id;
					lRes.display_name = lRecord2.value_str;
					lRes.pos = lRecord2.pos;
					lRes.level = char_length(lRecord2.pos) / 2;		
					lRes.parent_instance_id = lRecord.id;		
					lRes.has_children = 0;
					RETURN NEXT lRes;
				END LOOP lSubsectionsLoop;
			END IF;
			
		END LOOP lMainInstancesLoop;
		
		RETURN;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetArticleContentsInstances(
	pArticleId bigint
) TO iusrpmt;
