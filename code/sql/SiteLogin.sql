-- Type: t_usr

DROP TYPE IF EXISTS t_usr CASCADE;

CREATE TYPE t_usr AS
   (id integer,
    uname character varying,
    fullname character varying,
    email character varying,
    usr_title_id integer,
    photo_id integer,
    utype integer,
    state integer,
	salutation character varying,
	staff integer,
    ip inet,
	admin boolean);
ALTER TYPE t_usr OWNER TO postgres;

-- Function: sitelogin(character varying, character varying, inet)

-- DROP FUNCTION sitelogin(character varying, character varying, inet);

CREATE OR REPLACE FUNCTION sitelogin(puname character varying, ppass character varying, pip inet)
  RETURNS t_usr AS
$BODY$
	DECLARE
		lResult t_usr;
	BEGIN
		SELECT INTO lResult
			u.id,
			u.uname, 
			coalesce(u.first_name || ' ' || u.last_name, u.uname) AS fullname, 
			u.uname, 
			u.usr_title_id, 
			u.photo_id, 
			u.utype, 
			u.state, 
			ut.name AS salutation,
			u.staff,
			pIP AS ip,
			u.admin
		FROM usr u
		LEFT JOIN public.usr_titles ut ON ut.id = u.usr_title_id
		WHERE trim(lower(u.uname)) = trim(lower(pUname))
			AND u.upass = md5(pPass)
			AND u.utype = 1;
		
		IF (lResult.id IS NOT NULL AND lResult.state = 1) THEN
			UPDATE usr
				SET access_date = CURRENT_TIMESTAMP,
					access_ip = pIP
			WHERE id = lResult.id;
		END IF;
		
		RETURN lResult;
	END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION sitelogin(character varying, character varying, inet) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION sitelogin(character varying, character varying, inet) TO postgres;
GRANT EXECUTE ON FUNCTION sitelogin(character varying, character varying, inet) TO iusrpmt;
