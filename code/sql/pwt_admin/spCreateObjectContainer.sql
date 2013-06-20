DROP TYPE ret_spCreateObjectContainer CASCADE;

CREATE TYPE ret_spCreateObjectContainer AS (
	id bigint
);

CREATE OR REPLACE FUNCTION spCreateObjectContainer(
	pObjectId bigint,
	pContainerType int,
	pContainerName varchar
)
  RETURNS ret_spCreateObjectContainer AS
$BODY$
DECLARE
lRes ret_spCreateObjectContainer;
lPos int;
BEGIN

SELECT INTO lPos max(ord) FROM pwt.object_containers WHERE object_id = pObjectId;
lPos = coalesce(lPos, 0) + 1;

INSERT INTO pwt.object_containers(object_id, mode_id, ord, type, name) VALUES (pObjectId, 1, lPos, pContainerType, pContainerName);


lRes.id = currval('pwt.object_containers_id_seq'::regclass);


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCreateObjectContainer(
	pObjectId bigint,
	pContainerType int,
	pContainerName varchar
) TO iusrpmt;
