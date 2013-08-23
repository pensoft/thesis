DROP TYPE ret_spSaveInstanceFieldFromXml CASCADE;
CREATE TYPE ret_spSaveInstanceFieldFromXml AS (
	result int
);

CREATE OR REPLACE FUNCTION spSaveInstanceFieldFromXml(
	pInstanceId bigint,
	pFieldId bigint,
	pFieldXml xml,
	pUid int
)
  RETURNS ret_spSaveInstanceFieldFromXml AS
$BODY$
DECLARE
	lRes ret_spSaveInstanceFieldFromXml;
	lTemp xml[];
	lTempAttributes xml[];
	
	lFieldIntType int := 1;
	lFieldIntArrType int := 6;
	lFieldStrType int := 2;
	lFieldStrArrType int := 7;
	lFieldDateType int := 8;
	lFieldDateArrType int := 9;
	lFieldManyToStringType int := 3;
	lFieldManyToBitType int := 4;
	lFieldManyToBitOneBoxType int := 5;
	
	lRecord record;
	lTempRecord record;
	
	lValueInt int;
	lValueIntArr int[];
	lValueStr text;
	lValueStrArr text[];
	lValueDate date;
	lValueDateArr date[];
	
	lValueId int;
	
	lDefaultStrSeparator text := ',';
	lTempIntArray int[];
	lTempTextArray text[];
	lTempText text;
	lTempText2 text;
	lFieldIsHtml boolean;
	
	lDataSrcCursor refcursor;
	
	lIter int;
	lIter2 int;
	lSql text;
	lTmpQuery text;
	
	cTaxonClassificationControlType CONSTANT int := 22;
	cSubjectClassificationControlType CONSTANT int := 27;
	cChronologicalClassificationControlType CONSTANT int := 30;
	cGeographicalClassificationControlType CONSTANT int := 32;
	
	lDataSrcId int;
	
BEGIN
	
	
	RAISE NOTICE 'UPDATE FIELD InstanceId %, FieldId %, Xml %', pInstanceId, pFieldId, pFieldXml;
	
	SELECT INTO lRecord f.type, ofi.control_type, ofi.data_src_id, s.query, ct.is_html
	FROM pwt.document_object_instances i
	JOIN pwt.object_fields ofi ON ofi.object_id = i.object_id
	JOIN pwt.html_control_types ct ON ct.id = ofi.control_type
	JOIN pwt.fields f ON f.id = ofi.field_id
	LEFT JOIN pwt.data_src s ON s.id = ofi.data_src_id
	WHERE i.id = pInstanceId AND f.id = pFieldId;
	
	lFieldIsHtml = lRecord.is_html;
	
	lTemp = xpath('/*/value/text()', pFieldXml);
	lTempAttributes = xpath('/*/value/@value_id', pFieldXml);
	
	
	IF lRecord.type = lFieldIntType THEN
		IF lRecord.data_src_id IS NOT NULL THEN
			lTempText2 = HtmlSpecialCharsDecode(lTemp[1]::text);
			lTempText = lower(translate(lTempText2, ' ,.-*', ''));
			SELECT INTO lValueId * FROM spConvertAnyToInt(lTempAttributes[1]);
			
			IF lValueId IS NOT NULL THEN
				lSql = replace(lRecord.query, '{value}', quote_literal(lTempText2));
				lSql = 'SELECT a.*, 1 as is_equal  
					FROM (' || lSql || ')a 
					WHERE a.id = ' ||quote_literal(lValueId) || '
					';
				OPEN lDataSrcCursor FOR EXECUTE lSql;								
				FETCH lDataSrcCursor INTO lTempRecord;
				
				-- RAISE NOTICE 'Query %, Record %, is not null %, text %', lRecord.query, lTempRecord, not(lTempRecord IS NULL), lTempText;
				WHILE NOT(lTempRecord IS NULL)
				LOOP
					/*RAISE NOTICE 'name %, xml_name %', lTempRecord.name::character varying, lTempText;
					IF lower(translate(lTempRecord.name, ' ,.-*', '')) = lTempText THEN
						lValueInt = lTempRecord.id;
						-- RAISE NOTICE 'Valint %', lValueInt;
						EXIT;
					END IF;*/
					IF lTempRecord.is_equal = 1 THEN
						lValueInt = lTempRecord.id;
						EXIT;
					END IF;
					FETCH lDataSrcCursor INTO lTempRecord;
				END LOOP;
				CLOSE lDataSrcCursor;	
			END IF;
			
			IF lValueInt IS NULL THEN
				IF coalesce(lTempText, '') <> '' THEN
					lSql = replace(lRecord.query, '{value}', quote_literal(lTempText2));
					lSql = 'SELECT a.*, 1 as is_equal  
						FROM (' || lSql || ')a 
						WHERE lower(translate(a.name::text, '' ,.-*'', '''')) = ' ||quote_literal(lTempText) || '
						';					
					-- RAISE NOTICE 'SQL %, query %', lSql, lRecord.query;
					--OPEN lDataSrcCursor FOR EXECUTE replace(lRecord.query, '{value}', quote_literal(lTemp[1]::text));			
					OPEN lDataSrcCursor FOR EXECUTE lSql;			
					
					
					FETCH lDataSrcCursor INTO lTempRecord;
					
					-- RAISE NOTICE 'Query %, Record %, is not null %, text %', lRecord.query, lTempRecord, not(lTempRecord IS NULL), lTempText;
					WHILE NOT(lTempRecord IS NULL)
					LOOP
						/*RAISE NOTICE 'name %, xml_name %', lTempRecord.name::character varying, lTempText;
						IF lower(translate(lTempRecord.name, ' ,.-*', '')) = lTempText THEN
							lValueInt = lTempRecord.id;
							-- RAISE NOTICE 'Valint %', lValueInt;
							EXIT;
						END IF;*/
						IF lTempRecord.is_equal = 1 THEN
							lValueInt = lTempRecord.id;
							EXIT;
						END IF;
						FETCH lDataSrcCursor INTO lTempRecord;
					END LOOP;
					CLOSE lDataSrcCursor;			
				END IF;
			END IF;
		ELSE
			SELECT INTO lValueInt * FROM spConvertAnyToInt(lTemp[1]);
		END IF;
	ELSEIF lRecord.type = lFieldIntArrType THEN
		IF(
			lRecord.control_type = cTaxonClassificationControlType OR 
			lRecord.control_type = cSubjectClassificationControlType OR 
			lRecord.control_type = cChronologicalClassificationControlType OR 
			lRecord.control_type = cGeographicalClassificationControlType
		) THEN
			IF(lRecord.control_type = cTaxonClassificationControlType) THEN
				lDataSrcId = 44;
			ELSEIF (lRecord.control_type = cSubjectClassificationControlType) THEN
				lDataSrcId = 45;
			ELSEIF (lRecord.control_type = cChronologicalClassificationControlType) THEN
				lDataSrcId = 46;
			ELSE
				lDataSrcId = 47;
			END IF;
			
			SELECT INTO lRecord.data_src_id, lRecord.query id, query FROM pwt.data_src WHERE id = lDataSrcId;
		END IF;
	
		IF NOT (lRecord.data_src_id IS NULL) AND array_upper(lTemp, 1) > 0 THEN
			lTempTextArray = ARRAY[]::text[];			
			lTempIntArray = ARRAY[]::int[];
			lSql = '';
			FOR lIter2 IN
				1 .. array_upper(lTemp, 1)
			LOOP
				IF lIter2 > 1 THEN
					lSql = lSql || ' UNION ';
				END IF;
				
				lTempText2 = HtmlSpecialCharsDecode(lTemp[lIter2]::text);
				lTmpQuery = replace(lRecord.query, '{value}', lTempText2);
				lSql = lSql || '(' || lTmpQuery || ')';
			END LOOP;
			
			lSql = 'SELECT a.*, 1 as is_equal
			FROM (' || coalesce(lSql, '') || ')a 
			WHERE lower(translate(a.name::text, '' ,.-*'', '''')) = ANY(ARRAY[';
			FOR lIter IN
				1 .. array_upper(lTemp, 1)
			LOOP
				IF lIter > 1 THEN
					lSql = lSql || ', ';
				END IF;
				lTempText2 = HtmlSpecialCharsDecode(lTemp[lIter]::text);
				
				lSql = lSql || quote_literal(coalesce(lower(translate(lTempText2, ' ,.-*', '')), ''));
				--lTempTextArray = array_append(lTempTextArray, lower(translate(lTemp[lIter]::text, ' ,.-*', '')));
			END LOOP;
			lSql = lSql || ']);';
			
			--OPEN lDataSrcCursor FOR EXECUTE lRecord.query;
			OPEN lDataSrcCursor FOR EXECUTE lSql;
			FETCH lDataSrcCursor INTO lTempRecord;
			WHILE NOT(lTempRecord IS NULL)
			LOOP
				/*RAISE NOTICE 'Record %, textArray %, lTemp %', lRecord, lTempTextArray, lTemp;
				IF lower(translate(lRecord.name, ' ,.-*', '')) = ANY (lTempTextArray) THEN
					lTempIntArray = array_append(lTempIntArray, lRecord.id);
				END IF;*/
				
				IF lTempRecord.is_equal = 1 THEN
					lTempIntArray = array_append(lTempIntArray, lTempRecord.id);
				END IF;
				FETCH lDataSrcCursor INTO lTempRecord;
			END LOOP;
			CLOSE lDataSrcCursor;
			
			lValueIntArr = lTempIntArray;
		ELSE
			SELECT INTO lValueIntArr * FROM spConvertAnyArrayToIntArray(lTemp);
		END IF;
	
		
	ELSEIF lRecord.type = lFieldStrType THEN
		--SELECT INTO lValueStr spXmlConcatArr(xpath('/*/value/node()', pFieldXml))::text;
		/*Here we avoid the previous approach because
		if the value contained direct html entities (e.g. &gt;) it decoded them automatically to their respective symbols
		*/
		SELECT INTO lTempText xpath_nodeset(pFieldXml::text, '/*/value');
		
		IF(lTempText <> '<value/>') THEN
			lValueStr = regexp_replace(lTempText, '^<value(\s[^>]*)?>(.*)</value>', '\2');
		END IF;
	ELSEIF lRecord.type = lFieldStrArrType THEN
		lValueStrArr = lTemp::text[];
	ELSEIF lRecord.type = lFieldDateType THEN
		SELECT INTO lValueDate * FROM spConvertAnyToDate(lTemp[1]);
	ELSEIF lRecord.type = lFieldDateArrType THEN
		SELECT INTO lValueDateArr * FROM spConvertAnyArrayToIntArray(lTemp);
	ELSEIF lRecord.type = lFieldManyToStringType THEN		
		lValueStr = array_to_string(lTemp::text[], lDefaultStrSeparator, '');
	ELSEIF lRecord.type = lFieldManyToBitType THEN		
		SELECT INTO lTempIntArray * FROM spConvertAnyArrayToIntArray(lTemp);
		lValueInt = 0;
		FOR lIter IN
			1 .. coalesce(array_upper(lTempIntArray, 1), 0)
		LOOP
			lValueInt = lValueInt + coalesce(lTempIntArray[lIter], 0);
		END LOOP;
		
		IF lValueInt = 0 THEN
			lValueInt = null;
		END IF;
	ELSEIF lRecord.type = lFieldManyToBitOneBoxType THEN
		SELECT INTO lValueInt * FROM spConvertAnyToInt(lTemp[1]);
	END IF;
	
	-- RAISE NOTICE 'SetValTo %, InstanceId %, field_id %', lValueInt, pInstanceId, pFieldId;
	-- RAISE NOTICE 'ValInt %, ValStr %', lValueInt, lValueStr;
	IF lFieldIsHtml = false THEN
		lValueStr = HtmlSpecialCharsDecode(lValueStr);
	END IF;
	
	UPDATE pwt.instance_field_values SET
		value_int = lValueInt,
		value_arr_int = lValueIntArr,
		value_str = lValueStr,
		value_arr_str = lValueStrArr,
		value_date = lValueDate,
		value_arr_date = lValueDateArr
	WHERE instance_id = pInstanceId AND field_id = pFieldId;
	
	
	lRes.result = 1;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveInstanceFieldFromXml(
	pInstanceId bigint,
	pFieldId bigint,
	pFieldXml xml,
	pUid int
) TO iusrpmt;
