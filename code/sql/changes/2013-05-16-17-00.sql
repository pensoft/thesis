UPDATE pwt.document_template_objects t SET
	allow_add = false
FROM  pwt.document_template_objects p 
WHERE t.object_id = 5 AND p.id = t.parent_id AND p.object_id = 12;

UPDATE pwt.template_objects t SET
	allow_add = false
FROM  pwt.template_objects p 
WHERE t.object_id = 5 AND p.id = t.parent_id
AND p.template_id = t.template_id AND p.pos = substring(t.pos, 1, char_length(p.pos)) AND char_length(p.pos) + 2 = char_length(t.pos)
AND p.object_id = 12;

UPDATE pwt.fields SET
	"type" = 1
WHERE id IN (35, 38);