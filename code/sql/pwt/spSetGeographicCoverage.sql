CREATE OR REPLACE FUNCTION pwt.spSetGeographicCoverage(
	pInstanceId bigint,
	pUid int
)
  RETURNS integer AS
$BODY$
	DECLARE
		lGlobalCoverageValue int;
		lGlobalCoverageFieldId bigint;
		lEastId int;
		lWestId int;
		lSouthId int;
		lNorthId int;
	BEGIN
		lGlobalCoverageFieldId = 321;
		lEastId = 318;
		lWestId = 317;
		lSouthId = 319;
		lNorthId = 320;
		
		SELECT INTO lGlobalCoverageValue array_upper(value_arr_int, 1) FROM pwt.instance_field_values WHERE instance_id = pInstanceId AND field_id = lGlobalCoverageFieldId;
		
		IF lGlobalCoverageValue = 1 THEN
			
			UPDATE pwt.instance_field_values SET value_str = '180'
			WHERE instance_id = pInstanceId AND field_id = lEastId;
			
			UPDATE pwt.instance_field_values SET value_str = '-180'
			WHERE instance_id = pInstanceId AND field_id = lWestId;
			
			UPDATE pwt.instance_field_values SET value_str = '-90'
			WHERE instance_id = pInstanceId AND field_id = lSouthId;
			
			UPDATE pwt.instance_field_values SET value_str = '90'
			WHERE instance_id = pInstanceId AND field_id = lNorthId;
		END IF;
		RETURN 1;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

ALTER FUNCTION pwt.spSetGeographicCoverage(bigint, int) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spSetGeographicCoverage(bigint, int) TO public;
GRANT EXECUTE ON FUNCTION pwt.spSetGeographicCoverage(bigint, int) TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spSetGeographicCoverage(bigint, int) TO iusrpmt;