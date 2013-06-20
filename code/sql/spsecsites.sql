-- Function: spsecsites(poper int4, pid int4, pname "varchar", purl "varchar", pord int4, pcnt int4)

-- DROP FUNCTION spsecsites(poper int4, pid int4, pname "varchar", purl "varchar", pord int4, pcnt int4);
DROP FUNCTION spsecsites(poper int4, pid int4, pname "varchar", purl "varchar", pord int4, pcnt int4);
CREATE OR REPLACE FUNCTION spsecsites(poper int4, pid int4, pname "varchar", purl "varchar", pord int4, ptype int4, pcnt int4)
  RETURNS secsites AS
$BODY$

DECLARE
lResult secsites;
BEGIN

lResult.id := pID;

IF pOper = 0 THEN
-- GET
SELECT INTO lResult * 
FROM secsites
WHERE
id = pID;

ELSIF pOper = 1 then
IF pID IS NULL THEN
-- INSERT
INSERT INTO secsites (name, url, ord, type, cnt) VALUES (pName, pUrl, pOrd, pType, pCnt);
lResult.id := currval('secsites_id_seq');

ELSE
--UPDATE
UPDATE secsites
SET
name = pName,
url = pUrl,
ord = pOrd,
cnt = pCnt,
type = pType
WHERE
id = pID;
END IF;

ELSIF pOper = 3 THEN
-- DELETE

DELETE FROM secsites WHERE id = pID;
END IF;

RETURN lResult ;
END ;

$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spsecsites(poper int4, pid int4, pname "varchar", purl "varchar", pord int4, ptype int4, pcnt int4) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spsecsites(poper int4, pid int4, pname "varchar", purl "varchar", pord int4, ptype int4, pcnt int4) TO postgres84;
GRANT EXECUTE ON FUNCTION spsecsites(poper int4, pid int4, pname "varchar", purl "varchar", pord int4, ptype int4, pcnt int4) TO iusrcmstemplate;
