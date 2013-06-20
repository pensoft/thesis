DROP TYPE IF EXISTS pjs.ret_spgetjournaluserdata CASCADE;

CREATE TYPE pjs.ret_spgetjournaluserdata AS ( 
   email varchar,
   guid bigint,
   usrtitle integer,
   firstname varchar,
   lastname varchar,
   affiliation varchar,
   city varchar,
   country integer,
   roles varchar[],
   user_roles varchar[],
   alerts_subject_cats integer[],
   alerts_taxon_cats integer[],
   alerts_geographical_cats integer[]
);
ALTER TYPE pwt.ret_spcreateusrforauthor OWNER TO postgres;

CREATE OR REPLACE FUNCTION pjs."spGetJournalUserData"(
	pJournalId bigint,
	pEmail varchar,
	pMode int,
	pRole int
)
  RETURNS pjs.ret_spgetjournaluserdata AS
$BODY$
DECLARE
	lRes pjs.ret_spgetjournaluserdata;
	
	lJournalUserId int;
	cSERoleId CONSTANT int := 3;
BEGIN
	SELECT INTO 
		lRes.email, 
		lRes.guid, 
		lRes.usrtitle, 
		lRes.firstname, 
		lRes.lastname, 
		lRes.affiliation, 
		lRes.city, 
		lRes.country, 
		lRes.roles,
		lRes.user_roles
		
		u.uname,
		u.id,
		u.usr_title_id,
		u.first_name,
		u.last_name,
		u.affiliation,
		u.addr_city,
		u.country_id,
		array_agg(ju.role_id),
		array_agg(ju.role_id)
	FROM usr u
	LEFT JOIN pjs.journal_users ju ON ju.uid = u.id AND ju.journal_id = pJournalId
	WHERE u.uname = pEmail
	GROUP BY u.id;
	
	IF(pMode = cSERoleId AND pRole = cSERoleId) THEN
		lRes.user_roles = ARRAY[cSERoleId];
	END IF;
	
	SELECT  INTO 
		lRes.alerts_subject_cats,
		lRes.alerts_taxon_cats,
		lRes.alerts_geographical_cats
		
		je.subject_categories,
		je.taxon_categories,
		je.geographical_categories
	FROM pjs.journal_users ju
	JOIN pjs.journal_users_expertises je ON je.journal_usr_id = ju.id
	WHERE ju.uid = lRes.guid AND ju.role_id = cSERoleId;
	
	IF(lRes.email IS NULL) THEN
		lRes.email = pEmail;
	END IF;
	
	RETURN lRes;
END ;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION pjs."spGetJournalUserData"(
	pJournalId bigint,
	pEmail varchar,
	pMode int,
	pRole int
) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pjs."spGetJournalUserData"(
	pJournalId bigint,
	pEmail varchar,
	pMode int,
	pRole int
) TO public;
GRANT EXECUTE ON FUNCTION pjs."spGetJournalUserData"(
	pJournalId bigint,
	pEmail varchar,
	pMode int,
	pRole int
) TO postgres;
GRANT EXECUTE ON FUNCTION pjs."spGetJournalUserData"(
	pJournalId bigint,
	pEmail varchar,
	pMode int,
	pRole int
) TO iusrpmt;
GRANT EXECUTE ON FUNCTION pjs."spGetJournalUserData"(
	pJournalId bigint,
	pEmail varchar,
	pMode int,
	pRole int
) TO pensoft;
