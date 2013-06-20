DROP TYPE ret_spSaveTemplateObjectsOrder CASCADE;
CREATE TYPE ret_spSaveTemplateObjectsOrder AS (
	result int
);

/**
	Запазва подредбата на обектите от 1во ниво, за подадения темплейт
*/
CREATE OR REPLACE FUNCTION spSaveTemplateObjectsOrder(
	pTemplateId int,
	pTemplateObjectIds bigint[],
	pUid int
)
  RETURNS ret_spSaveTemplateObjectsOrder AS
$BODY$
DECLARE
lRes ret_spSaveTemplateObjectsOrder;
--lSid int;
lCurTime timestamp;
lId bigint;
lIter int;
lRecord record;
lPos varchar;
BEGIN
	
	CREATE TEMP TABLE template_objects_ord(
		template_object_id bigint,
		pos varchar,
		id serial
	);
	
	--Първо вкарваме подадените обекти в правилния ред
	FOR lIter IN 1 .. array_upper(pTemplateObjectIds, 1) LOOP
		INSERT INTO template_objects_ord(template_object_id) VALUES (pTemplateObjectIds[lIter]);
	END LOOP;
	
	-- Махаме тези, които не са на 1во ниво или не са от този темплейт
	DELETE FROM template_objects_ord o
	USING template_objects t 
	WHERE t.id = o.template_object_id AND t.id = ANY(pTemplateObjectIds) AND (t.template_id <> pTemplateId OR char_length(t.pos) > 2);
	
	-- Вкарваме ако има други обекти от 1во ниво, които по някакъв начин не са в подадения масив, в тяхния досегашен ред
	INSERT INTO template_objects_ord(template_object_id) 
		SELECT t.id 
		FROM template_objects t 
		WHERE char_length(t.pos) = 2 AND t.template_id = pTemplateId AND t.id <> ALL (pTemplateObjectIds)
		ORDER BY t.pos ASC;
	
	lPos = 'AA';
	FOR lRecord IN SELECT * FROM template_objects_ord ORDER BY id ASC LOOP
		UPDATE template_objects_ord SET pos = lPos WHERE id = lRecord.id;
		lPos = ForumGetNextOrd(lPos);
	END LOOP;
	
	-- Ъпдейтваме всички обекти от този темплейт
	-- като ги мачваме по обектите от 1во ниво и им слагаме новата позиция в началото
	UPDATE template_objects t SET
		pos = overlay(t.pos placing o.pos from 1 for 2)
	FROM template_objects t1 	
	JOIN template_objects_ord o ON o.template_object_id = t1.id
	WHERE substring(t.pos, 1, 2) = t1.pos AND t1.template_id = pTemplateId AND t.template_id = pTemplateId;
	
	DROP TABLE template_objects_ord;
	
	UPDATE templates SET
		lastmoduid = pUid,
		lastmoddate = now()
	WHERE id = pTemplateId;
	
	lRes.result = 1;
	RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveTemplateObjectsOrder(
	pTemplateId int,
	pTemplateObjectIds bigint[],
	pUid int
) TO iusrpmt;
