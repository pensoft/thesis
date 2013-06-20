DROP TYPE ret_spSaveTableData CASCADE;

CREATE TYPE ret_spSaveTableData AS (
	tableid bigint,
	move_position int
);

-- Function: pwt.spsavetabledata(integer, integer, integer, character varying, character varying, integer)
-- DROP FUNCTION pwt.spsavetabledata(integer, integer, integer, character varying, character varying, integer);

CREATE OR REPLACE FUNCTION pwt.spsavetabledata(poper integer, ptableid integer, pdocid integer, ptitle character varying, pdesc character varying, pcreateuid integer)
  RETURNS ret_spSaveTableData AS
$BODY$
DECLARE
	DECLARE lRes ret_spSaveTableData;
	lPosition int;
BEGIN
	lRes.tableid := 0;
	lPosition := 0;

	IF (pOper = 1) THEN -- INSERT
		IF pDocId > 0 THEN
			SELECT INTO lPosition max(pwt.tables.move_position)
			FROM pwt.tables
			WHERE pwt.tables.document_id = pdocid;
			IF(lPosition > 0) THEN 
				lPosition := lPosition + 1;
			ELSE
				lPosition := 1;
			END IF;

			INSERT INTO pwt.tables (document_id, title, description, usr_id, move_position) 
				VALUES (pDocId, pTitle, pDesc, pCreateUid, lPosition);
				lRes.tableid := currval('pwt.tables_id_seq');
				lRes.move_position := lPosition;
		END IF;
	ELSIF pOper = 2 THEN -- UPDATE
		IF pTableId > 0 THEN
			UPDATE pwt.tables SET 
						title = pTitle,
						description = pDesc,
						lastmod = CURRENT_TIMESTAMP
			WHERE id = pTableId AND document_id = pDocId;
				lRes.tableid := pTableId;
			SELECT INTO lRes.move_position move_position FROM pwt.tables WHERE id = pTableId AND document_id = pDocId;
		END IF;
	ELSIF pOper = 3 THEN -- DELETE
	
	END IF;
	RETURN lRes;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
GRANT EXECUTE ON FUNCTION pwt.spsavetabledata(integer, integer, integer, character varying, character varying, integer) TO iusrpmt;