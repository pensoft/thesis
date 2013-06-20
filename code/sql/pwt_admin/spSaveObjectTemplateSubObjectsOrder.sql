DROP TYPE ret_spSaveObjectTemplateSubObjectsOrder CASCADE;
CREATE TYPE ret_spSaveObjectTemplateSubObjectsOrder AS (
	result int
);

/**
	Запазва подредбата на обектите от 1во ниво, за подадения темплейт
*/
CREATE OR REPLACE FUNCTION spSaveObjectTemplateSubObjectsOrder(
	pTemplateId int,
	pParentTemplateObjectId bigint,
	pTemplateSubObjectIds bigint[],
	pUid int
)
  RETURNS ret_spSaveObjectTemplateSubObjectsOrder AS
$BODY$
DECLARE
	lRes ret_spSaveObjectTemplateSubObjectsOrder;
	--lSid int;
	lCurTime timestamp;
	lId bigint;
	lIter int;
	lRecord record;
	lPos varchar;
	lParentPos varchar;
	lChildPosLength int;
BEGIN
	SELECT INTO lParentPos 
		pos
	FROM pwt.template_objects 
	WHERE id = pParentTemplateObjectId;
	
	lChildPosLength = char_length(lParentPos) + 2;
	
	CREATE TEMP TABLE template_objects_ord(
		template_object_id bigint,
		pos varchar,
		id serial
	);
	
	--Първо вкарваме подадените обекти в правилния ред
	FOR lIter IN 1 .. array_upper(pTemplateSubObjectIds, 1) LOOP
		INSERT INTO template_objects_ord(template_object_id) VALUES (pTemplateSubObjectIds[lIter]);
	END LOOP;
	
	DELETE FROM template_objects_ord o 
	WHERE template_object_id NOT IN ( 
		SELECT id 
		FROM pwt.template_objects 
		WHERE parent_id = pParentTemplateObjectId
	);
	
	INSERT INTO template_objects_ord(template_object_id) 
		SELECT o.id 
		FROM pwt.template_objects o
		WHERE o.parent_id = pParentTemplateObjectId
		AND o.id NOT IN (SELECT template_object_id FROM template_objects_ord)
		ORDER BY o.pos ASC
	;	
	
	lPos = 'AA';
	FOR lRecord IN 
		SELECT * 
		FROM template_objects_ord 
		ORDER BY id ASC
	LOOP
		UPDATE template_objects_ord SET 
			pos = lParentPos || lPos 
		WHERE id = lRecord.id;
		lPos = ForumGetNextOrd(lPos);
	END LOOP;
	
	-- Ъпдейтваме всички обекти от този темплейт
	-- като ги мачваме по обектите от 1во ниво и им слагаме новата позиция в началото
	UPDATE pwt.template_objects t SET
		pos = overlay(t.pos placing o.pos from 1 for lChildPosLength)
	FROM pwt.template_objects p 	
	JOIN template_objects_ord o ON o.template_object_id = p.id
	WHERE substring(t.pos, 1, char_length(p.pos)) = p.pos AND p.template_id = pTemplateId AND t.template_id = pTemplateId;
	
	DROP TABLE template_objects_ord;
	
	UPDATE pwt.templates SET
		lastmoduid = pUid,
		lastmoddate = now()
	WHERE id = pTemplateId;
	
	lRes.result = 1;
	RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveObjectTemplateSubObjectsOrder(
	pTemplateId int,
	pParentTemplateObjectId bigint,
	pTemplateSubObjectIds bigint[],
	pUid int
) TO iusrpmt;
