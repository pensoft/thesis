DROP TYPE ret_spRemoveContainer CASCADE;
CREATE TYPE ret_spRemoveContainer AS (
	result int
);

CREATE OR REPLACE FUNCTION spRemoveContainer(
	pContainerId bigint,	
	pUid int
)
  RETURNS ret_spRemoveContainer AS
$BODY$
	DECLARE
		lRes ret_spRemoveContainer;					
	BEGIN
		
		
		DELETE FROM pwt.object_container_details WHERE container_id = pContainerId;
		DELETE FROM pwt.object_containers WHERE id = pContainerId;
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spRemoveContainer(
	pContainerId bigint,	
	pUid int
) TO iusrpmt;
