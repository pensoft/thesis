DROP TYPE ret_spAddItemToContainer CASCADE;

CREATE TYPE ret_spAddItemToContainer AS (
	result int
);

CREATE OR REPLACE FUNCTION spAddItemToContainer(
	pContainerId bigint,
	pItemId bigint,
	pItemType int,
	pCssClass varchar
)
  RETURNS ret_spAddItemToContainer AS
$BODY$
DECLARE
lRes ret_spAddItemToContainer;
lPos int;
BEGIN


SELECT INTO lPos max(ord) FROM pwt.object_container_details WHERE container_id = pContainerId;
lPos = coalesce(lPos, 0) + 1;

IF NOT EXISTS (SELECT * FROM pwt.object_container_details WHERE container_id = pContainerId AND item_id = pItemId AND item_type = pItemType) THEN
	INSERT INTO pwt.object_container_details(container_id, item_id, item_type, css_class, ord) VALUES (pContainerId, pItemId, pItemType, pCssClass, lPos);
END IF;


lRes.result = 1;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spAddItemToContainer(
	pContainerId bigint,
	pItemId bigint,
	pItemType int,
	pCssClass varchar
) TO iusrpmt;
