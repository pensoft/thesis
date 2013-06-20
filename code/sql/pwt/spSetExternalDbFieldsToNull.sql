CREATE OR REPLACE FUNCTION spSetExternalDbFieldsToNull(	
	pInstanceId bigint,	
	pUID int
)
RETURNS int AS
$BODY$
	DECLARE
	lCrossrefFieldId bigint;
	lPubmedFieldId bigint;
	BEGIN
		lCrossrefFieldId = 266;
		lCrossrefFieldId = 267;
		
		UPDATE pwt.instance_field_values SET
			value_str = null
		WHERE instance_id = pInstanceId AND field_id IN(lCrossrefFieldId, lPubmedFieldId);		
		
	RETURN 1;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSetExternalDbFieldsToNull(
	pInstanceId bigint,	
	pUID int
) TO iusrpmt;
