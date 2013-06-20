DROP TYPE ret_spGenerateTableCitationPreview CASCADE;

CREATE TYPE ret_spGenerateTableCitationPreview AS (
	citation_id bigint,
	preview varchar
);

CREATE OR REPLACE FUNCTION spGenerateTableCitationPreview(
	pCitationId bigint
)
  RETURNS ret_spGenerateTableCitationPreview AS
$BODY$
	DECLARE
		lRes ret_spGenerateFiguresCitationPreview;		
		
		lXrefTemp varchar;				
		lTemp varchar;		
		
		lRecord record;		
		lRecord2 record;
		lRecord3 record;
		
		lCitatedTables int[];
		
		lCurrentTableNum int;		
		
		lStartPos int;
		lEndPos int;
		lTableIter int;		
		lTableId bigint;
		lTableCitationType int;
	BEGIN		
		
		lXrefTemp = '';		
		lTemp = '';
		
		lTableCitationType = 2;
		
		SELECT INTO lRecord * 
		FROM pwt.citations 
		WHERE id = pCitationId AND citation_type = lTableCitationType;
		
		IF lRecord.id IS NULL THEN
			RETURN lRes;
		END IF;
				
		lRes.citation_id = lRecord.id;
		
		lCitatedTables = ARRAY[]::int[];		
		
		<<lCitatedTablesLoop>>
		FOR lRecord2 IN
			SELECT * 
			FROM pwt.tables
			WHERE id = ANY(lRecord.object_ids)
			ORDER BY move_position ASC
		LOOP			
			lCitatedTables = array_append(lCitatedTables, lRecord2.move_position);		
		END LOOP lCitatedTablesLoop;
		
		RAISE NOTICE 'Citated %', lCitatedTables;
				
		IF coalesce(array_upper(lCitatedTables, 1), 0) > 1 THEN
			lTemp = 'Tables ';
		ELSE 
			lTemp = 'Table ';
		END IF;
		
		lTableIter = 1;
		WHILE coalesce(array_upper(lCitatedTables, 1), 0) > 0 LOOP
			
			IF lTableIter > 1 THEN
				lTemp = lTemp || ', ';
			END IF;
			lCurrentTableNum = lCitatedTables[1];
			lCitatedTables = array_pop(lCitatedTables, lCurrentTableNum);
			
			
			-- RAISE NOTICE 'Current %', lCurrentTableNum;
						
			-- Работим с цяла фигура - трябва да гледаме дали имаме група
			lStartPos = lCurrentTableNum;
			lEndPos = lCurrentTableNum;
			
			SELECT INTO lTableId
				id
			FROM pwt.tables
			WHERE id = ANY(lRecord.object_ids)
			AND move_position = lCurrentTableNum LIMIT 1;
			
			lXrefTemp = lXrefTemp || '<xref class="hide" tid="' || coalesce(lTableId::varchar, '') || '" tblnumber="' || coalesce(lCurrentTableNum::varchar, '') || '"></xref>';
			
			WHILE coalesce(array_upper(lCitatedTables, 1), 0) > 0  AND lCitatedTables[1] = lCurrentTableNum + 1
			LOOP
				lCurrentTableNum = lCitatedTables[1];
				lCitatedTables = array_pop(lCitatedTables, lCitatedTables[1]);
				lEndPos = lCurrentTableNum;
				
				SELECT INTO lTableId
					id
				FROM pwt.tables
				WHERE id = ANY(lRecord.object_ids)
				AND move_position = lCurrentTableNum LIMIT 1;
				
				lXrefTemp = lXrefTemp || '<xref class="hide" tid="' || coalesce(lTableId::varchar, '') || '" tblnumber="' || coalesce(lCurrentTableNum::varchar, '') || '"></xref>';
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

			lTableIter = lTableIter + 1;
		END LOOP;
		
		-- Накрая добавяме xref-овете
		lTemp = coalesce(lTemp, '') || lXrefTemp;
		lRes.preview = lTemp;
		RETURN lRes;		
	END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER;
GRANT EXECUTE ON FUNCTION spGenerateTableCitationPreview(
	pCitationId bigint
) TO iusrpmt;
