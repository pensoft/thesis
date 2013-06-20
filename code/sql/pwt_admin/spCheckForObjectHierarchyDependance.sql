DROP TYPE ret_spCheckForObjectHierarchyDependance CASCADE;
CREATE TYPE ret_spCheckForObjectHierarchyDependance AS (
	result int
);

/**
	Връща 1 ако между двата обекта има йерархична зависимост (единия е подобект на другия)
	и 0 в противен случай.
	Ако вече има зависимост 2та обекта не мога да навлизат в друга зависимост (иначе ще се получи цикъл);
*/
CREATE OR REPLACE FUNCTION spCheckForObjectHierarchyDependance(
	pObjectId bigint,
	pObject2Id bigint
)
  RETURNS ret_spCheckForObjectHierarchyDependance AS
$BODY$
DECLARE
lRes ret_spCheckForObjectHierarchyDependance;
--lSid int;
lCurTime timestamp;
lId bigint;
BEGIN
	lRes.result = 0;
	

	RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCheckForObjectHierarchyDependance(
	pObjectId bigint,
	pObject2Id bigint
) TO iusrpmt;
