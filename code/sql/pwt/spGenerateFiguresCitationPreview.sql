-- Function: spgeneratefigurescitationpreview(bigint)

-- DROP FUNCTION spgeneratefigurescitationpreview(bigint);

CREATE OR REPLACE FUNCTION spgeneratefigurescitationpreview(pcitationid bigint)
  RETURNS ret_spgeneratefigurescitationpreview AS
$BODY$
	DECLARE
		lRes ret_spGenerateFiguresCitationPreview;		
		lRecord record;
		lCitatedFiguresCount int;
		lXrefTemp varchar;
		lCurFigure record;
		lSubPlatePositions text[];
		
		lTemp varchar;
		lIter int;
		lRecord2 record;
		lRecord3 record;
		
		lWholeFigures int[];
		lPartPlates int[];
		lCurrentPlateCitatedPics  bigint[];
		
		lTempBigIntArr bigint[];
		
		lCurrentFigNum int;
		lCurrentFigIsPlate boolean;	
		
		lStartPos int;
		lEndPos int;
		lFigsIter int;
		lPlateId bigint;
		lFigId bigint;
		lFigPlateId bigint;
		lCurrentCitatedFigNumber int;
		
		lFigObjectId bigint = 221;
		lFigTypeFieldId bigint = 488;
		lFigNumberFieldId bigint = 489;
		lFigPlateTypeId int = 2;
		lPlatePartObjectIds bigint[] = ARRAY[225, 226, 227, 228, 229, 230]::bigint[];
		lFigIsPlate int;
	BEGIN		
		
		lXrefTemp = '';
		lSubPlatePositions = ARRAY['a', 'b', 'c', 'd', 'e', 'f'];
		lTemp = '';
		
		SELECT INTO lRecord * 
		FROM pwt.citations 
		WHERE id = pCitationId AND citation_type = 1;
		
		IF lRecord.id IS NULL THEN
			RETURN lRes;
		END IF;
		
		lCitatedFiguresCount = array_upper(lRecord.object_ids, 1);
		lRes.citation_id = lRecord.id;
		
		lWholeFigures = ARRAY[]::int[];
		lPartPlates = ARRAY[]::int[];
		
		<<lCitatedPicsLoop>>
		FOR lRecord2 IN
			SELECT i.*, CASE WHEN f.value_int = lFigPlateTypeId THEN 1 ELSE 0 END as is_plate, f2.value_int as fignumber
			FROM pwt.document_object_instances i
			JOIN pwt.instance_field_values f ON f.instance_id = i.id AND f.field_id = lFigTypeFieldId
			JOIN pwt.instance_field_values f2 ON f2.instance_id = i.id AND f2.field_id = lFigNumberFieldId
			WHERE i.id = ANY(lRecord.object_ids) AND i.object_id = lFigObjectId AND i.is_confirmed = true
			ORDER BY i.pos
		LOOP			
			IF lRecord2.is_plate = 0 THEN
				lWholeFigures = array_append(lWholeFigures, lRecord2.fignumber);
			ELSE
				-- Гледаме дали целия плейт е вътре
				
				IF coalesce(in_array(lRecord2.fignumber, lWholeFigures), false) = false AND coalesce(in_array(lRecord2.fignumber, lPartPlates), false) = false THEN
					SELECT INTO lTempBigIntArr array_agg(i.id) 
					FROM pwt.document_object_instances i
					JOIN pwt.document_object_instances p ON  p.document_id = i.document_id AND p.pos = substring(i.pos, 1, char_length(p.pos))
					WHERE i.document_id = lRecord.document_id AND p.id = lRecord2.id AND i.object_id = ANY (lPlatePartObjectIds);
					
					RAISE NOTICE 'Fig %, subplates %', lRecord2.id, lTempBigIntArr;
					IF lRecord.object_ids @> lTempBigIntArr THEN -- Цитиран е целия плейт
						lWholeFigures = array_append(lWholeFigures, lRecord2.fignumber);
					ELSE -- Цитирана е само част от плейта
						lPartPlates = array_append(lPartPlates, lRecord2.fignumber);
					END IF;
				END IF;
			END IF;			
		END LOOP lCitatedPicsLoop;
		
		
		IF coalesce(array_upper(lWholeFigures, 1), 0) + coalesce(array_upper(lPartPlates, 1), 0) > 1 THEN
			lTemp = 'Figs ';
		ELSE 
			lTemp = 'Fig. ';
		END IF;
		
		lFigsIter = 1;
		WHILE coalesce(array_upper(lWholeFigures, 1), 0) > 0 OR coalesce(array_upper(lPartPlates, 1), 0) > 0 LOOP
			
			IF lFigsIter > 1 THEN
				lTemp = lTemp || ', ';
			END IF;
			-- Избираме минималния елемент от двата масива
			IF coalesce(array_upper(lWholeFigures, 1), 0) > 0 AND coalesce(array_upper(lPartPlates, 1), 0) > 0 THEN
				IF lWholeFigures[1] < lPartPlates[1] THEN
					lCurrentFigNum = lWholeFigures[1];
					lCurrentFigIsPlate = false;
				ELSE 
					lCurrentFigNum = lPartPlates[1];
					lCurrentFigIsPlate = true;
				END IF;
			ELSEIF coalesce(array_upper(lWholeFigures, 1), 0) > 0 THEN --Избираме 1вата останала фигура
				lCurrentFigNum = lWholeFigures[1];
				lCurrentFigIsPlate = false;
			ELSE -- Избираме 1я останал плейт
				lCurrentFigNum = lPartPlates[1];
				lCurrentFigIsPlate = true;
			END IF;
			
			-- Махаме текущия елемент от масивите
			lWholeFigures = array_pop(lWholeFigures, lCurrentFigNum);
			lPartPlates = array_pop(lPartPlates, lCurrentFigNum);
			
			IF lCurrentFigIsPlate = true THEN				
				lTemp = lTemp || lCurrentFigNum; -- № на плейта
				-- Работим с плейт - трябва да видим кои фигури от него са цитирани				
				SELECT INTO lPlateId 
					i.id
				FROM pwt.document_object_instances i
				JOIN pwt.instance_field_values f2 ON f2.instance_id = i.id AND f2.field_id = lFigNumberFieldId
				WHERE i.id = ANY(lRecord.object_ids) AND i.object_id = lFigObjectId AND i.is_confirmed = true AND f2.value_int = lCurrentFigNum
				LIMIT 1;
				
				lXrefTemp = lXrefTemp || '<xref class="hide" isplate="1" fignumber="' || coalesce(lCurrentFigNum::varchar, '') || '" rid="' || coalesce(lPlateId::varchar, '') || '"></xref>';
				
				
				lCurrentPlateCitatedPics = ARRAY[]::bigint[];
				<<lPlateFiguresLoop>>
				FOR lRecord2 IN
					SELECT i.*, spGetPlatePartNumber(i.id) as plate_num
					FROM pwt.document_object_instances i
					JOIN pwt.document_object_instances p ON  p.document_id = i.document_id AND p.pos = substring(i.pos, 1, char_length(p.pos))
					WHERE i.document_id = lRecord.document_id AND p.id = lPlateId AND i.object_id = ANY (lPlatePartObjectIds) AND i.id = ANY(lRecord.object_ids) 
					ORDER BY plate_num ASC
				LOOP
					lCurrentPlateCitatedPics = array_append(lCurrentPlateCitatedPics, lRecord2.id);
				END LOOP lPlateFiguresLoop;
				
				lIter = 1;
				<<lPlateFiguresInnerLoop>>
				WHILE coalesce(array_upper(lCurrentPlateCitatedPics, 1), 0) > 0 
				LOOP
					IF lIter > 1 THEN
						lTemp = lTemp || ', ';
					END IF;
					
					lFigId = lCurrentPlateCitatedPics[1];	
					lCurrentCitatedFigNumber = spGetPlatePartNumber(lCurrentPlateCitatedPics[1]);
					lCurrentPlateCitatedPics = array_pop(lCurrentPlateCitatedPics, lCurrentPlateCitatedPics[1]);
					lStartPos = lCurrentCitatedFigNumber;
					lEndPos = lCurrentCitatedFigNumber;
					
					--RAISE NOTICE 'PlateId %, Plate %, lCurrentNum %', lPlateId, lFigId, lCurrentCitatedFigNumber;
								
					
					lXrefTemp = lXrefTemp || '<xref class="hide" rid="' || coalesce(lFigId::varchar, '') || '" parentfig="' || coalesce(lPlateId::varchar, '') || '"></xref>';
					WHILE coalesce(array_upper(lCurrentPlateCitatedPics, 1), 0) > 0  AND spGetPlatePartNumber(lCurrentPlateCitatedPics[1]) = lCurrentCitatedFigNumber + 1
					LOOP
						lFigId = lCurrentPlateCitatedPics[1];	
						lCurrentCitatedFigNumber = spGetPlatePartNumber(lCurrentPlateCitatedPics[1]);
						lCurrentPlateCitatedPics = array_pop(lCurrentPlateCitatedPics, lCurrentPlateCitatedPics[1]);
						lEndPos = lCurrentCitatedFigNumber;
						
						lXrefTemp = lXrefTemp || '<xref class="hide" rid="' || coalesce(lFigId::varchar, '') || '" parentfig="' || coalesce(lPlateId::varchar, '') || '"></xref>';
					END LOOP;
					
					
					
					IF lStartPos = lEndPos THEN -- Цитиран е само 1 елемент (няма група)
						lTemp = lTemp || lSubPlatePositions[lStartPos];
					ELSE -- Цитирана е група от картинки
						IF lEndPos - lStartPos > 1 THEN
							lTemp = lTemp || lSubPlatePositions[lStartPos] || '-' || lSubPlatePositions[lEndPos];
						ELSE
							lTemp = lTemp || lSubPlatePositions[lStartPos] || ', ' || lSubPlatePositions[lEndPos];
						END IF;
												
					END IF;
					lIter = lIter + 1;
				END LOOP lPlateFiguresInnerLoop;
			ELSE 
				-- Работим с цяла фигура - трябва да гледаме дали имаме група
				lStartPos = lCurrentFigNum;
				lEndPos = lCurrentFigNum;
				
				SELECT INTO lFigId, lFigIsPlate 
					i.id, CASE WHEN f.value_int = lFigPlateTypeId THEN 1 ELSE 0 END as is_plate
				FROM pwt.document_object_instances i
				JOIN pwt.instance_field_values f ON f.instance_id = i.id AND f.field_id = lFigTypeFieldId
				JOIN pwt.instance_field_values f2 ON f2.instance_id = i.id AND f2.field_id = lFigNumberFieldId
				WHERE f2.value_int =  lCurrentFigNum AND i.object_id = lFigObjectId AND i.is_confirmed = true AND i.id = ANY(lRecord.object_ids) AND i.document_id = lRecord.document_id
				LIMIT 1;
				
				IF lFigIsPlate = 0 THEN
					lXrefTemp = lXrefTemp || '<xref class="hide" rid="' || coalesce(lFigId::varchar, '') || '" fignumber="' || coalesce(lCurrentFigNum::varchar, '') || '"></xref>';
				ELSE
					lXrefTemp = lXrefTemp || '<xref class="hide" isplate="1" rid="' || coalesce(lFigId::varchar, '') || '" fignumber="' || coalesce(lCurrentFigNum::varchar, '') || '"></xref>';
					FOR lRecord3 IN 
						SELECT i.*, spGetPlatePartNumber(i.id) as plate_num
						FROM pwt.document_object_instances i
						JOIN pwt.document_object_instances p ON  p.document_id = i.document_id AND p.pos = substring(i.pos, 1, char_length(p.pos))
						WHERE i.document_id = lRecord.document_id AND p.id = lFigId AND p.id = lFigId AND i.object_id = ANY (lPlatePartObjectIds)
						ORDER BY plate_num ASC
					LOOP
						lXrefTemp = lXrefTemp || '<xref class="hide" parentfig="' || coalesce(lFigId::varchar, '') || '" rid="' || coalesce(lRecord3.id::varchar, '') || '"></xref>';
					END LOOP;
				END IF;
				
				WHILE coalesce(array_upper(lWholeFigures, 1), 0) > 0  AND lWholeFigures[1] = lCurrentFigNum + 1
				LOOP
					lCurrentFigNum = lWholeFigures[1];
					lWholeFigures = array_pop(lWholeFigures, lWholeFigures[1]);
					lEndPos = lCurrentFigNum;
					
					SELECT INTO lFigId, lFigIsPlate 
						i.id, CASE WHEN f.value_int = lFigPlateTypeId THEN 1 ELSE 0 END as is_plate
					FROM pwt.document_object_instances i
					JOIN pwt.instance_field_values f ON f.instance_id = i.id AND f.field_id = lFigTypeFieldId
					JOIN pwt.instance_field_values f2 ON f2.instance_id = i.id AND f2.field_id = lFigNumberFieldId
					WHERE f2.value_int =  lCurrentFigNum AND i.object_id = lFigObjectId AND i.is_confirmed = true AND i.id = ANY(lRecord.object_ids) AND i.document_id = lRecord.document_id
					LIMIT 1;
					
					IF lFigIsPlate = 0 THEN
						lXrefTemp = lXrefTemp || '<xref class="hide" rid="' || coalesce(lFigId::varchar, '') || '" fignumber="' || coalesce(lCurrentFigNum::varchar, '') || '"></xref>';
					ELSE
						lXrefTemp = lXrefTemp || '<xref class="hide" isplate="1" rid="' || coalesce(lFigId::varchar, '') || '" fignumber="' || coalesce(lCurrentFigNum::varchar, '') || '"></xref>';
						FOR lRecord3 IN 
							SELECT i.*, spGetPlatePartNumber(i.id) as plate_num
							FROM pwt.document_object_instances i
							JOIN pwt.document_object_instances p ON  p.document_id = i.document_id AND p.pos = substring(i.pos, 1, char_length(p.pos))
							WHERE i.document_id = lRecord.document_id AND p.id = lFigId AND i.object_id = ANY (lPlatePartObjectIds)
							ORDER BY plate_num ASC
						LOOP
							lXrefTemp = lXrefTemp || '<xref class="hide" parentfig="' || coalesce(lFigId::varchar, '') || '" rid="' || coalesce(lRecord3.id::varchar, '') || '"></xref>';
						END LOOP;
					END IF;
				END LOOP;
				IF lStartPos = lEndPos THEN -- Цитиран е само 1 фигура (няма група)
					lTemp = lTemp || lStartPos;
				ELSE -- Цитирана е група от картинки
					IF lEndPos - lStartPos > 1 THEN
						lTemp = lTemp || lStartPos || '-' || lEndPos;
					ELSE
						lTemp = lTemp || lStartPos || ', ' || lEndPos;
					END IF;
				END IF;
			END IF;	

			lFigsIter = lFigsIter + 1;
		END LOOP;
		
		-- Накрая добавяме xref-овете
		lTemp = coalesce(lTemp, '') || lXrefTemp;
		lRes.preview = lTemp;
		RETURN lRes;		
	END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spgeneratefigurescitationpreview(bigint) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spgeneratefigurescitationpreview(bigint) TO public;
GRANT EXECUTE ON FUNCTION spgeneratefigurescitationpreview(bigint) TO postgres;
GRANT EXECUTE ON FUNCTION spgeneratefigurescitationpreview(bigint) TO iusrpmt;