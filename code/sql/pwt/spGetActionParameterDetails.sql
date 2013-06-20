DROP TYPE ret_spGetActionParameterDetails CASCADE;

CREATE TYPE ret_spGetActionParameterDetails AS (
	instance_id bigint,
	field_id bigint
);

CREATE OR REPLACE FUNCTION spGetActionParameterDetails(
	pInstanceId bigint,
	pParameterId bigint
)
  RETURNS ret_spGetActionParameterDetails AS
$BODY$
	DECLARE
		lRes ret_spGetActionParameterDetails;		
		lParameterSelector varchar;
	BEGIN
		
		SELECT INTO lParameterSelector parameter_selector FROM pwt.action_parameters WHERE id = pParameterId;
		
		SELECT INTO lRes.instance_id, lRes.field_id instance_id, field_id FROM spParseActionParameterSelector(pInstanceId, lParameterSelector);
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetActionParameterDetails(
	pInstanceId bigint,
	pParameterId bigint
) TO iusrpmt;
