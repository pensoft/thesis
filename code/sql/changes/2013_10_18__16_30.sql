UPDATE pwt.object_fields dto SET
 xml_node_name = translate(lower(label), ' ', '_')
WHERE coalesce(xml_node_name, '') = '';

UPDATE pwt.object_fields dto SET
 xml_node_name = translate(lower(xml_node_name), '()', '')
WHERE position('(' in xml_node_name) > 0 OR position(')' in xml_node_name) > 0;

UPDATE pwt.object_fields dto SET
 xml_node_name = replace(lower(xml_node_name), '&', 'and')
WHERE position('&' in xml_node_name) > 0;

UPDATE pwt.object_fields dto SET
 xml_node_name = replace(lower(xml_node_name), ',', '_')
WHERE position(',' in xml_node_name) > 0;

UPDATE pwt.object_fields dto SET
 xml_node_name = replace(lower(xml_node_name), '''', '')
WHERE position('''' in xml_node_name) > 0;

UPDATE pwt.object_fields dto SET
 xml_node_name = replace(lower(xml_node_name), '/', '')
WHERE position('/' in xml_node_name) > 0;

SELECT
	spSyncDocumentObjectFields(95,
	id,
	8
)

FROM pwt.documents;

SELECT
	spCacheReferenceFields(id)

FROM pwt.document_object_instances
WHERE object_id = 95;

INSERT INTO pwt.actions(display_name, name, eval_sql_function)
VALUES ('Reference after confirm - reorder all references', 'Reference after confirm - reorder all references', 'spReferenceAfterConfirm');

INSERT INTO pwt.object_actions(object_id, action_id, ord, pos)
	VALUES (95, 132, 1, 17);
/*
	Modified sps
	spCacheReferenceFields
	spReferenceAfterConfirm
	spReferencesSortAction
	spGetDocumentReferences
*/