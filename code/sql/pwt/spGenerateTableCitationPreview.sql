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
		
		lXrefTemp varchar = '';				
		lTemp varchar = '';		
		
		lRecord record;		
		lRecord2 record;
		lRecord3 record;
		
		lCitatedTables bigint[];
		
		lCurrentTableNum int;		
		
		lStartPos int;
		lEndPos int;
		lTableIter int;		
		lTableId bigint;
		lTableCitationType int = 2;
		
		lItemNumFieldId bigint = 489;
		lGroupStartNum int = 0;
		lPreviousNum int = 0;
		lCurrentNum int = 0;
	BEGIN				
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
			FROM pwt.document_object_instances
			WHERE id = ANY(lRecord.object_ids)
			ORDER BY pos ASC
		LOOP			
			lCitatedTables = array_append(lCitatedTables, lRecord2.id);		
		END LOOP lCitatedTablesLoop;
		
		RAISE NOTICE 'Citated %', lCitatedTables;
		
		lTemp = 'Table';
		
		IF array_upper(lCitatedTables, 1) > 1 THEN -- plural form
			lTemp = lTemp || 's';
		END IF;
		lTemp = lTemp || ' ';
					
		lTableIter = 1;
		FOR lRecord2 IN
			SELECT i.*, f.value_int as idx
			FROM pwt.document_object_instances i
			JOIN  pwt.instance_field_values f ON f.instance_id = i.id AND f.field_id = lItemNumFieldId
			WHERE i.id = ANY(lRecord.object_ids) AND i.is_confirmed = true
			ORDER BY i.pos ASC
		LOOP
			lCurrentNum = lRecord2.idx;
			IF lGroupStartNum = 0 THEN
				lGroupStartNum = lCurrentNum;
			END IF;
			lXrefTemp = lXrefTemp || '<xref class="hide" tid="' || coalesce(lRecord2.id::varchar, '') || '" tblnumber="' || coalesce(lRecord2.idx::varchar, '') || '"></xref>';
			
			IF lPreviousNum > 0 AND lPreviousNum + 1 < lCurrentNum THEN -- End Of Group
				IF lTableIter > 1 THEN
					lTemp = lTemp || ', ';
				END IF;
				IF lGroupStartNum = lPreviousNum THEN -- Цитиран е само 1 фигура (няма група)
					lTemp = lTemp || lGroupStartNum;
				ELSE -- Цитирана е група от картинки
					IF lPreviousNum - lGroupStartNum > 1 THEN
						lTemp = lTemp || lGroupStartNum || '-' || lPreviousNum;
					ELSE
						lTemp = lTemp || lGroupStartNum || ', ' || lPreviousNum;
					END IF;
				END IF;
				lGroupStartNum = lCurrentNum;
				lTableIter = lTableIter + 1;
			END IF;		
			lPreviousNum = lRecord2.idx;
		END LOOP;
		IF lTableIter > 1 THEN
			lTemp = lTemp || ', ';
		END IF;
		IF lGroupStartNum = lPreviousNum THEN -- Цитиран е само 1 фигура (няма група)
			lTemp = lTemp || lGroupStartNum;
		ELSE -- Цитирана е група от картинки
			IF lPreviousNum - lGroupStartNum > 1 THEN
				lTemp = lTemp || lGroupStartNum || '-' || lPreviousNum;
			ELSE
				lTemp = lTemp || lGroupStartNum || ', ' || lPreviousNum;
			END IF;
		END IF;
		
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
