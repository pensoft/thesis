DROP TYPE ret_spImportPjsDocumentInstance CASCADE;
CREATE TYPE ret_spImportPjsDocumentInstance AS (
	result int
);

CREATE OR REPLACE FUNCTION spImportPjsDocumentInstance(
	pDocumentId int,
	pInstanceXml xml,	
	pUid int
)
  RETURNS ret_spImportPjsDocumentInstance AS
$BODY$
DECLARE
	lRes ret_spImportPjsDocumentInstance;
		
	lSubInstances xml[];
	lFields xml[];
	
	lIterInstances int;
	lIterFields int;
	
	lInstanceId bigint;
	lFieldId bigint;
	
	lCurrentInstance xml;
	lCurrentField xml;
	lTemp xml[];
	
	lPJSActionImportMode int = 3;
	lFieldsQuery text = './fields/*[@id > 0]';
	lInstanceQuery text = './*[@instance_id > 0]';
BEGIN
	lRes.result = 1;
	
	
	--Update all the fields of the current object
	lTemp = xpath('@instance_id', pInstanceXml);
	lInstanceId = lTemp[1]::text::bigint;		
	
	IF NOT EXISTS(SELECT * 
		FROM pwt.document_object_instances 
		WHERE id = lInstanceId AND document_id = pDocumentId
	) THEN
		RETURN lRes;
	END IF;
	
	lFields = xpath(lFieldsQuery, pInstanceXml);
	FOR lIterFields IN 
		1 .. coalesce(array_upper(lFields, 1), 0) 
	LOOP
		--lCurrentField = lFields[lIterFields];
		lCurrentField = xpath_nodeset(pInstanceXml::text, lFieldsQuery || '[' || lIterFields || ']');
		lTemp = xpath('@id', lCurrentField);
		lFieldId = lTemp[1]::text::int;	
		--RAISE NOTICE 'Instance % Field % Value %', lInstanceId, lFieldId, lCurrentField;
		PERFORM spSaveInstanceFieldFromXml(lInstanceId, lFieldId, lCurrentField, pUid);
		
	END LOOP;	
	-- After save actions
	PERFORM spPerformInstancesSqlSaveActions(pUid, ARRAY[lInstanceId]::int[], lPJSActionImportMode);
	
	--Update all the Sub instances
	lSubInstances = xpath(lInstanceQuery, pInstanceXml);	
	FOR lIterInstances IN 
		1 .. coalesce(array_upper(lSubInstances, 1), 0) 
	LOOP		
		-- lCurrentInstance = lSubInstances[lIterInstances];
		lCurrentInstance = xpath_nodeset(pInstanceXml::text, lInstanceQuery || '[' || lIterInstances || ']');
		PERFORM spImportPjsDocumentInstance(pDocumentId, lCurrentInstance, pUid);
	END LOOP;
	
	-- After save actions which are to be executed after all the subobjects have been updated. With/without propagation
	PERFORM spPerformInstancesSqlSaveActionsAfterSubobjWithProp(pUid, ARRAY[lInstanceId]::int[], lPJSActionImportMode);
	PERFORM spPerformInstancesSqlSaveActionsAfterSubobjWithoutProp(pUid, ARRAY[lInstanceId]::int[], lPJSActionImportMode);
	
	
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spImportPjsDocumentInstance(
	pDocumentId int,
	pInstanceXml xml,
	pUid int
) TO iusrpmt;
