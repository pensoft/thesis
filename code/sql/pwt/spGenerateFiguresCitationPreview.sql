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
		lCurrentPlateCitatedPics int[];
		
		lTempBigIntArr bigint[];
		
		lCurrentFigNum int;
		lCurrentFigIsPlage boolean;	
		
		lStartPos int;
		lEndPos int;
		lFigsIter int;
		lPlateId bigint;
		lFigId bigint;
		lFigPlateId bigint;
		lCurrentCitatedFig int;
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
			SELECT * 
			FROM pwt.media
			WHERE id = ANY(lRecord.object_ids)
			ORDER BY move_position ASC, position ASC
		LOOP			
			IF lRecord2.plate_id IS NULL THEN
				lWholeFigures = array_append(lWholeFigures, lRecord2.move_position);
			ELSE
				-- Гледаме дали целия плейт е вътре
				
				IF coalesce(in_array(lRecord2.move_position, lWholeFigures), false) = false AND coalesce(in_array(lRecord2.move_position, lPartPlates), false) = false THEN
					SELECT INTO lTempBigIntArr array_agg(id) 
					FROM pwt.media 
					WHERE document_id = lRecord.document_id AND plate_id = lRecord2.plate_id;
					
					IF lRecord.object_ids @> lTempBigIntArr THEN -- Цитиран е целия плейт
						lWholeFigures = array_append(lWholeFigures, lRecord2.move_position);
					ELSE -- Цитирана е само част от плейта
						lPartPlates = array_append(lPartPlates, lRecord2.move_position);
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
					lCurrentFigIsPlage = false;
				ELSE 
					lCurrentFigNum = lPartPlates[1];
					lCurrentFigIsPlage = true;
				END IF;
			ELSEIF coalesce(array_upper(lWholeFigures, 1), 0) > 0 THEN --Избираме 1вата останала фигура
				lCurrentFigNum = lWholeFigures[1];
				lCurrentFigIsPlage = false;
			ELSE -- Избираме 1я останал плейт
				lCurrentFigNum = lPartPlates[1];
				lCurrentFigIsPlage = true;
			END IF;
			
			-- Махаме текущия елемент от масивите
			lWholeFigures = array_pop(lWholeFigures, lCurrentFigNum);
			lPartPlates = array_pop(lPartPlates, lCurrentFigNum);
			
			IF lCurrentFigIsPlage = true THEN				
				lTemp = lTemp || lCurrentFigNum; -- № на плейта
				-- Работим с плейт - трябва да видим кои фигури от него са цитирани				
				SELECT INTO lPlateId 
					plate_id 
				FROM pwt.media
				WHERE id = ANY(lRecord.object_ids)
				AND move_position = lCurrentFigNum LIMIT 1;
				
				lXrefTemp = lXrefTemp || '<xref class="hide" isplate="1" fignumber="' || coalesce(lCurrentFigNum::varchar, '') || '" rid="' || coalesce(lPlateId::varchar, '') || '"></xref>';
				
				
				lCurrentPlateCitatedPics = ARRAY[]::int[];
				<<lPlateFiguresLoop>>
				FOR lRecord2 IN
					SELECT * 
					FROM pwt.media
					WHERE id = ANY(lRecord.object_ids)
					AND move_position = lCurrentFigNum
					ORDER BY position ASC
				LOOP
					lCurrentPlateCitatedPics = array_append(lCurrentPlateCitatedPics, lRecord2.position);
				END LOOP lPlateFiguresLoop;
				
				lIter = 1;
				<<lPlateFiguresInnerLoop>>
				WHILE coalesce(array_upper(lCurrentPlateCitatedPics, 1), 0) > 0 
				LOOP
					IF lIter > 1 THEN
						lTemp = lTemp || ', ';
					END IF;
					
					
					lCurrentCitatedFig = lCurrentPlateCitatedPics[1];
					lCurrentPlateCitatedPics = array_pop(lCurrentPlateCitatedPics, lCurrentPlateCitatedPics[1]);
					lStartPos = lCurrentCitatedFig;
					lEndPos = lCurrentCitatedFig;
					
					SELECT INTO lFigId
						id 
					FROM pwt.media
					WHERE id = ANY(lRecord.object_ids)
					AND move_position = lCurrentFigNum AND position = lCurrentCitatedFig
					LIMIT 1;
					
					lXrefTemp = lXrefTemp || '<xref class="hide" rid="' || coalesce(lFigId::varchar, '') || '" parentfig="' || coalesce(lPlateId::varchar, '') || '"></xref>';
					WHILE coalesce(array_upper(lCurrentPlateCitatedPics, 1), 0) > 0  AND lCurrentPlateCitatedPics[1] = lCurrentCitatedFig + 1
					LOOP
						lCurrentCitatedFig = lCurrentPlateCitatedPics[1];
						lCurrentPlateCitatedPics = array_pop(lCurrentPlateCitatedPics, lCurrentPlateCitatedPics[1]);
						lEndPos = lCurrentCitatedFig;
						
						SELECT INTO lFigId
							id 
						FROM pwt.media
						WHERE id = ANY(lRecord.object_ids)
						AND move_position = lCurrentFigNum AND position = lCurrentCitatedFig
						LIMIT 1;
						
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
				
				SELECT INTO lFigId, lFigPlateId 
					id, plate_id
				FROM pwt.media
				WHERE id = ANY(lRecord.object_ids)
				AND move_position = lCurrentFigNum LIMIT 1;
				
				IF lFigPlateId IS NULL THEN
					lXrefTemp = lXrefTemp || '<xref class="hide" rid="' || coalesce(lFigId::varchar, '') || '" fignumber="' || coalesce(lCurrentFigNum::varchar, '') || '"></xref>';
				ELSE
					lXrefTemp = lXrefTemp || '<xref class="hide" isplate="1" rid="' || coalesce(lFigPlateId::varchar, '') || '" fignumber="' || coalesce(lCurrentFigNum::varchar, '') || '"></xref>';
					FOR lRecord3 IN 
						SELECT id, plate_id
						FROM pwt.media
						WHERE document_id = lRecord.document_id AND move_position = lCurrentFigNum
					LOOP
						lXrefTemp = lXrefTemp || '<xref class="hide" parentfig="' || coalesce(lFigPlateId::varchar, '') || '" rid="' || coalesce(lRecord3.id::varchar, '') || '"></xref>';
					END LOOP;
				END IF;
				
				WHILE coalesce(array_upper(lWholeFigures, 1), 0) > 0  AND lWholeFigures[1] = lCurrentFigNum + 1
				LOOP
					lCurrentFigNum = lWholeFigures[1];
					lWholeFigures = array_pop(lWholeFigures, lWholeFigures[1]);
					lEndPos = lCurrentFigNum;
					
					SELECT INTO lFigId, lFigPlateId 
						id , plate_id
					FROM pwt.media
					WHERE id = ANY(lRecord.object_ids)
					AND move_position = lCurrentFigNum LIMIT 1;
					
					IF lFigPlateId IS NULL THEN
						lXrefTemp = lXrefTemp || '<xref class="hide" rid="' || coalesce(lFigId::varchar, '') || '" fignumber="' || coalesce(lCurrentFigNum::varchar, '') || '"></xref>';
					ELSE
						lXrefTemp = lXrefTemp || '<xref class="hide" isplate="1" rid="' || coalesce(lFigPlateId::varchar, '') || '" fignumber="' || coalesce(lCurrentFigNum::varchar, '') || '"></xref>';
						FOR lRecord3 IN 
							SELECT id, plate_id
							FROM pwt.media
							WHERE document_id = lRecord.document_id AND move_position = lCurrentFigNum
						LOOP
							lXrefTemp = lXrefTemp || '<xref class="hide" parentfig="' || coalesce(lFigPlateId::varchar, '') || '" rid="' || coalesce(lRecord3.id::varchar, '') || '"></xref>';
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