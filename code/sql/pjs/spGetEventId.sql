DROP TYPE IF EXISTS ret_spGetEventId CASCADE;
CREATE TYPE ret_spGetEventId AS (
	event_id int
);

CREATE OR REPLACE FUNCTION pjs."spGetEventId"(
	pOper int,
	pRoundId bigint
)
  RETURNS ret_spGetEventId AS
$BODY$
	DECLARE
		lRes ret_spGetEventId;
	BEGIN
		
		IF (pOper = 1) THEN
			lRes.event_id = 6;
		ELSEIF (pOper = 2) THEN
			lRes.event_id = 39;
		ELSEIF(pOper = 3) THEN
			lRes.event_id = 35;
		ELSEIF(pOper = 4) THEN
			lRes.event_id = 38;
		END IF;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs."spGetEventId"(
	pOper int,
	pRoundId bigint
) TO iusrpmt;
