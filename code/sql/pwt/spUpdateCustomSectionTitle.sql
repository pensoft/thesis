DROP TYPE ret_spUpdateCustomSectionTitle CASCADE;
CREATE TYPE ret_spUpdateCustomSectionTitle AS (
	result int
);

CREATE OR REPLACE FUNCTION spUpdateCustomSectionTitle(
	pSectionInstanceId bigint,
	pTitle varchar,
	pUid int
)
  RETURNS ret_spUpdateCustomSectionTitle AS
$BODY$
	DECLARE
		lRes ret_spUpdateCustomSectionTitle;			
		
		lSectionTitleFieldId bigint;
	BEGIN
		lSectionTitleFieldId = 211;		
		
		UPDATE pwt.instance_field_values SET
			value_str = pTitle
		WHERE instance_id = pSectionInstanceId AND field_id = lSectionTitleFieldId;
		
		lRes.result = 1;

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spUpdateCustomSectionTitle(
	pSectionInstanceId bigint,
	pTitle varchar,
	pUid int
) TO iusrpmt;
