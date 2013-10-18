DROP TYPE IF EXISTS ret_spCacheReferenceFields CASCADE;

CREATE TYPE ret_spCacheReferenceFields AS (
	result int
);

CREATE OR REPLACE FUNCTION spCacheReferenceFields(
	pReferenceInstanceId bigint
)
  RETURNS ret_spCacheReferenceFields AS
$BODY$
	DECLARE
		lRes ret_spCacheReferenceFields;		
		lAuthorObjectId bigint;
		lReferenceObjectId bigint;
		lWebsiteReferenceObjectId bigint;
		
		lFirstAuthorCombinedName varchar;
		lPubyear int;
		lIsWebsiteReference int;
		
		lAuthorFirstNameFieldId bigint = 251;
		lAuthorLastNameFieldId bigint = 252;
		lAuthorCombinedNameFieldId bigint = 250;
		lAuthorOtherFirstNamesFieldId bigint = 253;
		lPubYearFieldId bigint = 254;
		
		lReferencePubyearFieldId int = 496;
		lReferenceIsWebsiteReferenceFieldId int = 497;
		lReferenceAllAuthorsCombinedNamesFieldId int = 495;
		lReferenceAuthorsCountFieldId int = 494;
		lReferenceFirstAuthorCombinedNameFieldId int = 493;
		
		lPreviousFirstAuthorCombinedName varchar;
		lPreviousAuthorsCount int;
		lPreviousAllAuthorsCombinedNames varchar;
		lPreviousPubYear int;
		lPreviousIsWebsiteReference int;
		
		lRecord record;
		lCombinedAuthorNames varchar;
		lAuthorsCount int;
		lInitials varchar;
		lCurrentAuthorCombinedNames varchar;
		lAuthorsAuthorshipType int = 1;
		lEditorsAuthorshipType int = 2;
		lAuthorshipFieldIds bigint[] = ARRAY[281, 282, 265];
		lInitialsArr varchar[];
		lIter int;
		lInitialsArrCount int;
	BEGIN
		lAuthorObjectId = 90;
		lReferenceObjectId = 95;
		lWebsiteReferenceObjectId = 108;
		
		SELECT INTO lPubyear
			py.value_int
		FROM pwt.document_object_instances i
		JOIN pwt.document_object_instances c ON c.document_id = i.document_id AND substring(c.pos, 1, char_length(i.pos)) = i.pos 
		JOIN pwt.instance_field_values py ON py.instance_id = c.id AND py.field_id = lPubYearFieldId
		WHERE i.id = pReferenceInstanceId;
		
		lIsWebsiteReference = 0;
		
		IF EXISTS (
			SELECT i.id, null, null, null, null, 1, i.pos 
			FROM pwt.document_object_instances i
			JOIN pwt.document_object_instances c ON c.document_id = i.document_id AND substring(c.pos, 1, char_length(i.pos)) = i.pos AND c.object_id = lWebsiteReferenceObjectId
			WHERE i.id = pReferenceInstanceId
		) THEN
			lIsWebsiteReference = 1;
		END IF;
		
		
		lAuthorsCount = 0;
		lCombinedAuthorNames = '';
		FOR lRecord IN 
			SELECT fn.value_str as first_name, ln.value_str as last_name, cn.value_str as combined_name, at.value_int as authorship_type, fn2.value_str as other_first_names
			FROM pwt.document_object_instances i
			JOIN pwt.document_object_instances p ON p.id = pReferenceInstanceId AND substring(i.pos, 1, char_length(p.pos)) = p.pos
			JOIN pwt.instance_field_values fn ON fn.instance_id = i.id AND fn.field_id = lAuthorFirstNameFieldId
			JOIN pwt.instance_field_values fn2 ON fn2.instance_id = i.id AND fn2.field_id = lAuthorOtherFirstNamesFieldId
			JOIN pwt.instance_field_values ln ON ln.instance_id = i.id AND ln.field_id = lAuthorLastNameFieldId
			JOIN pwt.instance_field_values cn ON cn.instance_id = i.id AND cn.field_id = lAuthorCombinedNameFieldId
			JOIN pwt.document_object_instances ai ON substring(ai.pos, 1, char_length(p.pos)) = p.pos
			JOIN pwt.instance_field_values at ON at.instance_id = ai.id AND at.field_id = ANY (lAuthorshipFieldIds)
			
			WHERE i.document_id = p.document_id AND i.object_id = lAuthorObjectId AND ai.document_id =  p.document_id  
			ORDER BY i.pos ASC
		LOOP
			lAuthorsCount = lAuthorsCount + 1;
			lInitials = substring(lRecord.first_name, 1, 1);
			
			lInitialsArr = regexp_split_to_array(lRecord.other_first_names, E'\\s+');
			lInitialsArrCount = coalesce(array_upper(lInitialsArr, 1), 0);
			IF(lInitialsArrCount > 0) THEN
				FOR lIter IN 
					1 .. lInitialsArrCount
				LOOP
					lInitials = lInitials || substring(lInitialsArr[lIter], 1, 1);
				END LOOP;
			END IF;
							
			IF lRecord.authorship_type IN (lAuthorsAuthorshipType, lEditorsAuthorshipType) THEN 
				lCurrentAuthorCombinedNames = trim(coalesce(lRecord.last_name || ' ', '') || coalesce(lInitials, ''));
			ELSE
				lCurrentAuthorCombinedNames = trim(coalesce(lRecord.combined_name, ''));
			END IF;

			IF lAuthorsCount = 1 THEN					
				lFirstAuthorCombinedName = lCurrentAuthorCombinedNames;
				lCombinedAuthorNames = lCurrentAuthorCombinedNames;
			ELSE
				lCombinedAuthorNames = lCombinedAuthorNames || ' ' || lCurrentAuthorCombinedNames;
			END IF;			
			
		END LOOP;
		
		SELECT INTO
			lPreviousFirstAuthorCombinedName, lPreviousAuthorsCount, lPreviousAllAuthorsCombinedNames, lPreviousPubYear, lPreviousIsWebsiteReference
			facn.value_str, ac.value_int, aacn.value_str, y.value_int, w.value_int
		FROM pwt.document_object_instances i
		JOIN pwt.instance_field_values y ON y.instance_id = i.id AND y.field_id = lReferencePubyearFieldId
		JOIN pwt.instance_field_values w ON w.instance_id = i.id AND w.field_id = lReferenceIsWebsiteReferenceFieldId
		JOIN pwt.instance_field_values ac ON ac.instance_id = i.id AND ac.field_id = lReferenceAuthorsCountFieldId
		JOIN pwt.instance_field_values aacn ON aacn.instance_id = i.id AND aacn.field_id = lReferenceAllAuthorsCombinedNamesFieldId
		JOIN pwt.instance_field_values facn ON facn.instance_id = i.id AND facn.field_id = lReferenceFirstAuthorCombinedNameFieldId
		WHERE i.id = pReferenceInstanceId;
		
		lRes.result = 0;
		
		IF lPreviousFirstAuthorCombinedName IS DISTINCT FROM  lFirstAuthorCombinedName OR
			lPreviousAuthorsCount IS DISTINCT FROM  lAuthorsCount OR
			lPreviousAllAuthorsCombinedNames IS DISTINCT FROM  lCombinedAuthorNames OR
			lPreviousPubYear IS DISTINCT FROM  lPubyear OR
			lPreviousIsWebsiteReference IS DISTINCT FROM  lIsWebsiteReference
		THEN
			lRes.result = 1;
		ELSE
			RETURN lRes;		
		END IF;
				
		
		UPDATE pwt.instance_field_values SET
			value_int = lAuthorsCount
		WHERE instance_id = pReferenceInstanceId AND field_id = lReferenceAuthorsCountFieldId;
		
		UPDATE pwt.instance_field_values SET
			value_int = lIsWebsiteReference
		WHERE instance_id = pReferenceInstanceId AND field_id = lReferenceIsWebsiteReferenceFieldId;
		
		UPDATE pwt.instance_field_values SET
			value_int = lPubyear
		WHERE instance_id = pReferenceInstanceId AND field_id = lReferencePubyearFieldId;
		
		UPDATE pwt.instance_field_values SET
			value_str = lCombinedAuthorNames
		WHERE instance_id = pReferenceInstanceId AND field_id = lReferenceAllAuthorsCombinedNamesFieldId;
		
		UPDATE pwt.instance_field_values SET
			value_str = lFirstAuthorCombinedName
		WHERE instance_id = pReferenceInstanceId AND field_id = lReferenceFirstAuthorCombinedNameFieldId;
		
		
		
		RETURN lRes;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCacheReferenceFields(
	pReferenceInstanceId bigint
) TO iusrpmt;
