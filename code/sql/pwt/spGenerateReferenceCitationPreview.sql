DROP TYPE ret_spGenerateReferenceCitationPreview CASCADE;

CREATE TYPE ret_spGenerateReferenceCitationPreview AS (
	citation_id bigint,
	preview varchar
);


CREATE OR REPLACE FUNCTION spGenerateReferenceCitationPreview(
	pCitationId bigint
)
  RETURNS ret_spGenerateReferenceCitationPreview AS
$BODY$
	DECLARE
		lRes ret_spGenerateReferenceCitationPreview;		
		lRecord record;
		lTemp varchar;
		lIter int;
		lRecord2 record;
		lRecord3 record;
		lGroupItemsCount int;
		lReferenceFound boolean;
		lCharAsci int;		
		lCitatedReferencesCount int;
		lReferenceUrlFieldId bigint;
		lPubYearFieldId bigint;
		lAuthorLastNameFieldId bigint;
		lAuthorObjectId bigint;
		lXrefTemp varchar;
		lAuthorCombinedNameFieldId bigint = 250;
		lInstitutionalAuthorshipType int = 3;
		lReferenceAuthorshipType int;
		
		lReferenceThreeTypeAuthorshipObjectId bigint = 92;
		lReferenceTwoTypeAuthorshipObjectId bigint = 100;
		lReferenceOneTypeAuthorshipObjectId bigint = 101;
		
		lReferenceThreeTypeAuthorshipFieldId bigint = 265;
		lReferenceTwoTypeAuthorshipFieldId bigint = 281;
		lReferenceOneTypeAuthorshipFieldId bigint = 282;
	BEGIN		
		lReferenceUrlFieldId = 263;
		lAuthorLastNameFieldId = 252;		
		lAuthorObjectId = 90;
		lPubYearFieldId = 254;
		
		lXrefTemp = '';
		
		SELECT INTO lRecord * 
		FROM pwt.citations 
		WHERE id = pCitationId AND citation_type = 3;
		
		IF lRecord.id IS NULL THEN
			RETURN lRes;
		END IF;
		
		
		
		lCitatedReferencesCount = array_upper(lRecord.object_ids, 1);
		lRes.citation_id = lRecord.id;
				
		<<lReferenceLoop>>
		FOR lIter IN
			1 .. lCitatedReferencesCount
		LOOP
			
			--lXrefTemp = lXrefTemp || '<xref class="hide" rid="' || lRecord.object_ids[lIter] || '">';
			SELECT INTO lRecord2 
				* 
			FROM spGetDocumentReferences(lRecord.document_id) 
			WHERE reference_instance_id = lRecord.object_ids[lIter];
			
			SELECT INTO lReferenceAuthorshipType 
				coalesce(fv1.value_int, fv2.value_int, fv3.value_int)
			FROM pwt.document_object_instances p 
			LEFT JOIN pwt.document_object_instances i1 ON i1.document_id = p.document_id 
				AND substring(i1.pos, 1, char_length(p.pos)) = p.pos
				AND i1.object_id = lReferenceThreeTypeAuthorshipObjectId
			LEFT JOIN pwt.instance_field_values fv1 ON fv1.instance_id = i1.id AND fv1.field_id = lReferenceThreeTypeAuthorshipFieldId 			
			LEFT JOIN pwt.document_object_instances i2 ON i2.document_id = p.document_id 
				AND substring(i2.pos, 1, char_length(p.pos)) = p.pos
				AND i2.object_id = lReferenceTwoTypeAuthorshipObjectId
			LEFT JOIN pwt.instance_field_values fv2 ON fv2.instance_id = i2.id AND fv2.field_id = lReferenceTwoTypeAuthorshipFieldId 
			LEFT JOIN pwt.document_object_instances i3 ON i3.document_id = p.document_id 
				AND substring(i3.pos, 1, char_length(p.pos)) = p.pos
				AND i3.object_id = lReferenceOneTypeAuthorshipObjectId
			LEFT JOIN pwt.instance_field_values fv3 ON fv3.instance_id = i3.id AND fv3.field_id = lReferenceOneTypeAuthorshipFieldId 			
			WHERE p.id = lRecord2.reference_instance_id;
						
			lReferenceAuthorshipType = coalesce(lReferenceAuthorshipType, 0);
			
			IF lIter > 1 THEN
				IF lIter = lCitatedReferencesCount AND lRecord.citation_mode = 2 THEN
					lTemp = coalesce(lTemp, '') || '</xref> and ';
				ELSE
					lTemp = coalesce(lTemp, '') || '</xref>, ';
				END IF;
			END IF;
			lTemp = coalesce(lTemp, '') || '<xref class="hide" type="bibr" rid="' || lRecord.object_ids[lIter] || '">';
			
			IF lRecord2.is_website_citation = 1 THEN
				SELECT INTO lRecord3 fv.value_str 
				FROM pwt.instance_field_values fv
				JOIN pwt.document_object_instances p ON p.id = lRecord2.reference_instance_id
				JOIN pwt.document_object_instances i ON i.document_id = p.document_id
					AND substring(i.pos, 1, char_length(p.pos)) = p.pos AND i.id = fv.instance_id
				WHERE  fv.field_id = lReferenceUrlFieldId;
				lTemp = coalesce(lTemp, '') || coalesce(lRecord3.value_str, '');
				
				CONTINUE lReferenceLoop;
			END IF;
			
			
			lGroupItemsCount = 0;
			lReferenceFound = false;
			lCharAsci = ascii('a') - 1;
			
			/* RAISE NOTICE 'Ref id %, doc_id %, fac %, ac %,
				acn %, pubyear %, is_website_citation %', 
				lRecord.object_ids[lIter], lRecord.document_id, lRecord2.first_author_combined_name , lRecord2.authors_count,
				lRecord2.authors_combined_names, lRecord2.pubyear, lRecord2.is_website_citation;
			*/	
			<<lReferenceGroupLoop>>
			FOR lRecord3 IN
				SELECT * FROM spGetDocumentReferences(lRecord.document_id)
				WHERE trim(coalesce(first_author_combined_name, '')) = trim(coalesce(lRecord2.first_author_combined_name, ''))
					AND coalesce(authors_count, 0) = coalesce(lRecord2.authors_count, 0)
					AND trim(coalesce(authors_combined_names, '')) = trim(coalesce(lRecord2.authors_combined_names, ''))
					AND coalesce(pubyear, 0) = coalesce(lRecord2.pubyear, 0)
					AND is_website_citation = lRecord2.is_website_citation
				ORDER BY is_website_citation ASC, first_author_combined_name ASC, authors_count ASC, authors_combined_names ASC, pubyear ASC	
			LOOP				
				IF lReferenceFound = false THEN
					lCharAsci = lCharAsci + 1;
				END IF;
				lGroupItemsCount = lGroupItemsCount + 1;
				
				
				IF lRecord3.reference_instance_id = lRecord2.reference_instance_id THEN
					lReferenceFound = true;					
				END IF;
				
				IF lReferenceFound = true AND lGroupItemsCount > 1 THEN
					EXIT lReferenceGroupLoop;
				END IF;
			END LOOP lReferenceGroupLoop;
			
			-- Слагаме първо фамилиите на авторите
			IF lRecord2.authors_count > 2 OR lRecord2.authors_count = 1 THEN
				SELECT INTO lRecord3 
					fv.value_str, fv1.value_str as combined_name
				FROM pwt.instance_field_values fv
				JOIN pwt.instance_field_values fv1 ON fv1.instance_id = fv.instance_id
				JOIN pwt.document_object_instances p ON  p.id = lRecord2.reference_instance_id
				JOIN pwt.document_object_instances i ON i.document_id = p.document_id
					AND substring(i.pos, 1, char_length(p.pos)) = p.pos AND i.id = fv.instance_id
				WHERE fv.field_id = lAuthorLastNameFieldId AND fv1.field_id = lAuthorCombinedNameFieldId
					AND i.object_id = lAuthorObjectId
				ORDER BY i.pos ASC LIMIT 1;
				
				IF lReferenceAuthorshipType <> lInstitutionalAuthorshipType THEN
					lTemp = coalesce(lTemp, '') || coalesce(lRecord3.value_str, '');
				ELSE
					lTemp = coalesce(lTemp, '') || coalesce(lRecord3.combined_name, '');
				END IF;
				IF lRecord2.authors_count > 2 THEN
					lTemp = coalesce(lTemp, '') || ' et al.';
				END IF;
				
			ELSEIF lRecord2.authors_count = 2 THEN -- 2 автора 
				SELECT INTO lRecord3 
					fv.value_str, fv1.value_str as combined_name
				FROM pwt.instance_field_values fv
				JOIN pwt.instance_field_values fv1 ON fv1.instance_id = fv.instance_id				
				JOIN pwt.document_object_instances p ON  p.id = lRecord2.reference_instance_id
				JOIN pwt.document_object_instances i ON i.document_id = p.document_id
					AND substring(i.pos, 1, char_length(p.pos)) = p.pos AND i.id = fv.instance_id
				WHERE fv.field_id = lAuthorLastNameFieldId AND fv1.field_id = lAuthorCombinedNameFieldId
					AND i.object_id = lAuthorObjectId
				ORDER BY i.pos ASC LIMIT 1;
				
				IF lReferenceAuthorshipType <> lInstitutionalAuthorshipType THEN
					lTemp = coalesce(lTemp, '') || coalesce(lRecord3.value_str, '');
				ELSE
					lTemp = coalesce(lTemp, '') || coalesce(lRecord3.combined_name, '');
				END IF;
				lTemp = lTemp || ' and ';
				
				SELECT INTO lRecord3 
					fv.value_str, fv1.value_str as combined_name
				FROM pwt.instance_field_values fv
				JOIN pwt.instance_field_values fv1 ON fv1.instance_id = fv.instance_id				
				JOIN pwt.document_object_instances p ON  p.id = lRecord2.reference_instance_id
				JOIN pwt.document_object_instances i ON i.document_id = p.document_id
					AND substring(i.pos, 1, char_length(p.pos)) = p.pos AND i.id = fv.instance_id
				WHERE fv.field_id = lAuthorLastNameFieldId AND fv1.field_id = lAuthorCombinedNameFieldId
					AND i.object_id = lAuthorObjectId
				ORDER BY i.pos ASC LIMIT 1 OFFSET 1;
				
				IF lReferenceAuthorshipType <> lInstitutionalAuthorshipType THEN
					lTemp = coalesce(lTemp, '') || coalesce(lRecord3.value_str, '');
				ELSE
					lTemp = coalesce(lTemp, '') || coalesce(lRecord3.combined_name, '');
				END IF;
				
			ELSE -- 0 автора
			END IF;
			
			-- Pubyear
			SELECT INTO lRecord3 fv.value_int
			FROM pwt.instance_field_values fv
			JOIN pwt.document_object_instances p ON  p.id = lRecord2.reference_instance_id
			JOIN pwt.document_object_instances i ON i.document_id = p.document_id
				AND substring(i.pos, 1, char_length(p.pos)) = p.pos AND i.id = fv.instance_id
			WHERE  fv.field_id = lPubYearFieldId				
			ORDER BY i.pos ASC LIMIT 1;
			
			--RAISE NOTICE 'Pubyear %, field_id %, ref_id %', lRecord3.value_int, lPubYearFieldId, lRecord2.reference_instance_id;
			
			lTemp = coalesce(lTemp, '') || ' ';
			IF lRecord.citation_mode = 2 THEN
				lTemp = coalesce(lTemp, '') || '(';
			END IF;
			lTemp = coalesce(lTemp, '') || coalesce(lRecord3.value_int::varchar, '');
			
			-- RAISE NOTICE 'lGroupItemsCount %, char %', lGroupItemsCount, lCharAsci;
			IF lGroupItemsCount > 1 THEN
				lTemp = coalesce(lTemp, '') || coalesce(chr(lCharAsci)::varchar, '');
			END IF;
			
			IF lRecord.citation_mode = 2 THEN
				lTemp = coalesce(lTemp, '') || ')';
			END IF;
			
		
		END LOOP lReferenceLoop;
		-- Накрая добавяме xref-овете
		lTemp = replace(lTemp, '&', '&amp;');
		lTemp = coalesce(lTemp, '') || lXrefTemp;
		lTemp = coalesce(lTemp, '') || '</xref>';
		lRes.preview = lTemp;
		RETURN lRes;		
	END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER;
GRANT EXECUTE ON FUNCTION spGenerateReferenceCitationPreview(
	pCitationId bigint
) TO iusrpmt;
