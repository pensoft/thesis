-- Function: rubrikirearange(psiteid int4, pmodifiedarr _int4)

DROP FUNCTION rubrikirearange(psiteid int4, pmodifiedarr _int4);

CREATE OR REPLACE FUNCTION rubrikirearange(psiteid int4, pmodifiedarr _int4)
  RETURNS int4 AS
$BODY$
DECLARE
	arrSize int;
	arrIter int;
	lRubID int;
	lRNOld int;
	lPosLNew varchar;
	lPosLOld varchar;
	lSid int;
	lRubrs RECORD;
	lOverlayFrom int;
BEGIN
	lPosLNew := 'AA';

	arrSize := array_upper(pModifiedArr, 1);
	arrIter := 1;

	<< firstloop >>
	WHILE arrIter <= arrSize 
	LOOP
	lRubID := pModifiedArr[arrIter];

	IF lRubID > 0 THEN
	SELECT INTO lPosLOld, lRNOld pos, rootnode FROM rubr WHERE id = lRubID;

	lOverlayFrom := 1;
	IF char_length(lPosLOld) > 2 THEN
	lOverlayFrom := (char_length(lPosLOld) / 2);

	IF mod(lOverlayFrom, 2) = 0 THEN
	lOverlayFrom := lOverlayFrom + 1;
	ELSE
	lOverlayFrom := lOverlayFrom + 2;
	END IF;

	RAISE NOTICE 'posold - % --- posnew - % --- oo - %', lPosLOld, lPosLNew, lOverlayFrom;
	END IF;

	UPDATE rubr SET
	pos = overlay(pos placing lPosLNew from lOverlayFrom for 2)
	WHERE id = lRubID;

	<< secondloop >>
	FOR lRubrs IN SELECT * FROM rubr WHERE rootnode = lRNOld AND pos LIKE lPosLOld || '%' AND char_length(pos) > char_length(lPosLOld) ORDER BY pos
	LOOP
	RAISE NOTICE 'id - % ---- posold - % ---- posnew - %', lRubrs.id, lPosLOld, lPosLNew;
	UPDATE rubr SET
	pos = overlay(pos placing lPosLNew from lOverlayFrom for 2)
	WHERE id = lRubrs.id;

	END LOOP secondloop;

	lPosLNew := ForumGetNextOrd(lPosLNew);
	END IF;
	arrIter := arrIter + 1;
	END LOOP firstloop;

	RETURN 1;
END;
$BODY$
  LANGUAGE 'plpgsql' SECURITY DEFINER;
ALTER FUNCTION rubrikirearange(psiteid int4, pmodifiedarr _int4) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION rubrikirearange(psiteid int4, pmodifiedarr _int4) TO postgres84;
GRANT EXECUTE ON FUNCTION rubrikirearange(psiteid int4, pmodifiedarr _int4) TO iusrpmt;
