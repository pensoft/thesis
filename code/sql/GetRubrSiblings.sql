DROP FUNCTION GetRubrSiblings(pRubrID int, pLangid int, pLangid int);

DROP TYPE retGetRubrSiblings;

CREATE TYPE retGetRubrSiblings AS (
id int,
name varchar,
ftype int
);

CREATE OR REPLACE FUNCTION GetRubrSiblings(pRubrID int, pSid int, pLangid int)
  RETURNS SETOF retGetRubrSiblings AS
$$

DECLARE
	lRes retGetRubrSiblings;
	lParentPos varchar;		
BEGIN
	if ((pRubrID is NULL) or (pRubrID=0)) then
		for lRes in select id, name[$3] as name,0 as ftype from rubr 
			where sid=pSid and (name[$3] is not null) and  (char_length(name[$3])>0) and (id=rootnode)
			order by pos
		loop
			return NEXT lRes ;
		end loop;
	else
		lParentPos=(select pos from rubr where id=pRubrID);
		for lRes in select id, name[$3] as name,(case when id=pRubrID then 1 when char_length(lParentPos)=char_length(pos) then 0 else 2 end) as ftype from rubr 
			where (name[$3] is not null) and  (char_length(name[$3])>0) 
					and ((pos like (lParentPos || '__')) or ( substring(pos from 1 for (char_length(pos)-2)) = substring(lParentPos from 1 for (char_length(lParentPos)-2)) ))
			order by pos
		loop
			return NEXT lRes ;
		end loop; 
	end if;
	return;
END
$$
  LANGUAGE 'plpgsql' SECURITY DEFINER;
ALTER FUNCTION GetRubrSiblings(pRubrID int, pSid int, pLangid int) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION GetRubrSiblings(pRubrID int, pSid int, pLangid int) TO iusrpmt;
