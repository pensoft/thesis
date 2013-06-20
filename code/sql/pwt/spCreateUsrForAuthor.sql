-- Type: pwt.ret_spcreateusrforauthor

--DROP TYPE pwt.ret_spcreateusrforauthor CASCADE;
--DROP TYPE pwt.ret_spcreateusrforauthor CASCADE;

CREATE TYPE pwt.ret_spcreateusrforauthor AS
   (user_exists integer,
    new_user_id integer,
    upass character varying,
	event_id integer);
ALTER TYPE pwt.ret_spcreateusrforauthor OWNER TO postgres;

DROP FUNCTION IF EXISTS pwt.spcreateusrforauthor(
	pemailval character varying, 
	psalutationval integer, 
	pfirstnameval character varying, 
	pmiddlenameval character varying, 
	plastnameval character varying, 
	paffval character varying, 
	pcityval character varying, 
	pcountryval integer
);

CREATE OR REPLACE FUNCTION pwt.spcreateusrforauthor(
	pemailval character varying, 
	psalutationval integer, 
	pfirstnameval character varying, 
	pmiddlenameval character varying, 
	plastnameval character varying, 
	paffval character varying, 
	pcityval character varying, 
	pcountryval integer
)
  RETURNS pwt.ret_spcreateusrforauthor AS
$BODY$
	DECLARE
		lRes pwt.ret_spCreateUsrForAuthor;
	BEGIN
		IF (pEmailVal IS NULL) THEN
			RETURN lRes;
		END IF;
		
		SELECT INTO lRes.user_exists id 
		FROM public.usr 
		WHERE uname = trim(lower(pemailval)) AND state = 1;
		
		IF lRes.user_exists IS NULL THEN

			SELECT INTO lRes.new_user_id userid 
			FROM public.spregusrstep1(NULL, 1, pemailval, 'tmppass') AS userid;
	
			IF (lRes.new_user_id IS NOT NULL) THEN
				--PERFORM public.userfpass(pEmailVal, lRes.new_user_id);
				SELECT INTO lRes.upass upass 
				FROM public.spUserFpass(pEmailVal, lRes.new_user_id) AS upass;
				
				PERFORM public.spregusrstep2(lRes.new_user_id, 1, pFirstNameVal, pMiddleNameVal, pLastNameVal, pSalutationVal, 1/*client type*/, pAffVal, NULL, NULL, NULL, pCityVal, pCountryVal, NULL, NULL, NULL, NULL);
				
				--Activate user
				UPDATE public.usr SET 
					state = 1 
				WHERE id = lRes.new_user_id;
			END IF;
		END IF;
		RETURN lRes;
	END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;



GRANT EXECUTE ON FUNCTION pwt.spcreateusrforauthor(
	pemailval character varying, 
	psalutationval integer, 
	pfirstnameval character varying, 
	pmiddlenameval character varying, 
	plastnameval character varying, 
	paffval character varying, 
	pcityval character varying, 
	pcountryval integer
) TO iusrpmt;
