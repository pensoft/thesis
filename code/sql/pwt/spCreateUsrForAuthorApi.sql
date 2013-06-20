DROP TYPE pwt.ret_spCreateUsrForAuthorApi CASCADE;

CREATE TYPE pwt.ret_spCreateUsrForAuthorApi AS (
	user_exists integer,
	uid integer,
	email varchar,
	upass varchar,
	fullname varchar
);

CREATE OR REPLACE FUNCTION pwt.spCreateUsrForAuthorApi(
	pEmail varchar, 
	pSalutation varchar, 
	pFName varchar, 
	pMName varchar, 
	pLName varchar, 
	pAff varchar, 
	pCity varchar, 
	pCountry varchar
)
  RETURNS pwt.ret_spCreateUsrForAuthorApi AS
$BODY$
	DECLARE
		lRes pwt.ret_spCreateUsrForAuthorApi;
		lSalutationId int;
		lCountryId int;
	BEGIN
		SELECT INTO lSalutationId id 
		FROM usr_titles
		WHERE name ILIKE trim(pSalutation);
		
		SELECT INTO lCountryId id 
		FROM  countries
		WHERE name ILIKE trim(pCountry);
		
		lRes.email = trim(lower(pEmail));
		
		SELECT INTO lRes.uid id 
		FROM public.usr 
		WHERE uname = trim(lower(pEmail)) AND state = 1;
		
		lRes.user_exists = 1;
		IF lRes.uid IS NULL THEN
			lRes.user_exists = 0;
			-- RAISE NOTICE 'ASD1 %', lRes.uid;
			SELECT INTO lRes.uid userid FROM public.spregusrstep1(NULL, 1, pEmail, 'tmppass') AS uid;
			-- RAISE NOTICE 'ASD2 %', lRes.uid;
			IF (lRes.uid IS NOT NULL) THEN
				-- RAISE NOTICE 'ASD %', lRes.uid;				
				SELECT INTO lRes.upass upass FROM public.userfpass(pEmail, lRes.uid) AS upass;
				PERFORM public.spregusrstep2(lRes.uid, 1, pFName, pMName, pLName, lSalutationId, 1/*client type*/, pAff, NULL, NULL, NULL, pCity, lCountryId, NULL, NULL, NULL, NULL);
				--Activate user
				UPDATE public.usr SET state = 1 WHERE id = lRes.uid;
			END IF;
		END IF;
		
		SELECT INTO lRes.fullname coalesce(ut.name || ' ' || u.first_name || ' ' || u.last_name, u.uname) as fullname,
				u.uname as user_email, u.autolog_hash as autolog_hash
		FROM public.usr u
		LEFT JOIN public.usr_titles ut ON ut.id = u.usr_title_id
		WHERE u.id = lRes.uid;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;

GRANT EXECUTE ON FUNCTION pwt.spCreateUsrForAuthorApi(
	pEmail varchar, 
	pSalutation varchar, 
	pFName varchar, 
	pMName varchar, 
	pLName varchar, 
	pAff varchar, 
	pCity varchar, 
	pCountry varchar
) TO iusrpmt;

