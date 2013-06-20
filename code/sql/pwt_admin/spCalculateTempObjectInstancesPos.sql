DROP TYPE ret_spCalculateTempObjectInstancesPos CASCADE;
CREATE TYPE ret_spCalculateTempObjectInstancesPos AS (
	result int
);

CREATE OR REPLACE FUNCTION spCalculateTempObjectInstancesPos(
	pTempTableName varchar
)
  RETURNS ret_spCalculateTempObjectInstancesPos AS
$BODY$
	DECLARE
		lRes ret_spCalculateTempObjectInstancesPos;		
		lRecord RECORD;
		lTempRecord RECORD;
		lPrevParentId bigint;
		lParentPos varchar;
		lCurrentPos varchar;
		lQuery varchar;
	BEGIN
		lPrevParentId = -1;
		lQuery = 'SELECT * FROM ' || pTempTableName || ' ORDER BY level ASC, parent_instance_id ASC, id ASC ';
		FOR lRecord IN EXECUTE lQuery LOOP
			IF lPrevParentId <> lRecord.parent_instance_id THEN
				lCurrentPos = 'AA';
				lParentPos = '';
				IF lRecord.level > 1 THEN
					EXECUTE 'SELECT pos FROM ' || pTempTableName || ' WHERE id = ' || lPrevParentId INTO lTempRecord;	
					lParentPos = lTempRecord.pos;
				END IF;
				EXECUTE 'UPDATE ' || pTempTableName || ' SET pos = ' || lParentPos || lCurrentPos || ' WHERE id = ' || lRecord.id;
			END IF;
		END LOOP;
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCalculateTempObjectInstancesPos(
	pTempTableName varchar
) TO iusrpmt;
