-- Function: spSaveNewJournalUser(integer, bigint, character varying, integer, character varying, character varying, character varying, character varying, integer, integer[], character varying, character varying, character varying)

-- DROP FUNCTION spSaveNewJournalUser(integer, bigint, character varying, integer, character varying, character varying, character varying, character varying, integer, integer[], character varying, character varying, character varying);
/*
DROP TYPE pwt.ret_spcreateusrforauthor CASCADE;

CREATE TYPE pwt.ret_spcreateusrforauthor AS
   (user_exists integer,
    new_user_id integer,
    upass character varying,
	event_id integer);
ALTER TYPE pwt.ret_spcreateusrforauthor OWNER TO postgres;
*/

CREATE OR REPLACE FUNCTION spSaveNewJournalUser(
	pGuid integer,
	pJournalId bigint,
	pEmail character varying,
	pUsrTitleId integer, 
	pFirstName character varying,
	pLastName character varying,
	pAffiliation character varying,
	pCity character varying, 
	pCountryId integer, 
	pUserRoles integer[],
	pSubjectCategories character varying,
	pTaxonCategories character varying,
	pGeographicalCategories character varying,
	pMode int,
	pUid bigint,
	pRole int
)
  RETURNS pwt.ret_spcreateusrforauthor AS
$BODY$
DECLARE
	lRes pwt.ret_spcreateusrforauthor;
	
	cSERoleType CONSTANT int := 3;
	cReviewerRoleType CONSTANT int := 5;
	
	lEventTypeId int;
	lRole int;
BEGIN
	
	SELECT INTO lRes.user_exists, lRes.new_user_id, lRes.upass lResult.user_exists, lResult.new_user_id, lResult.upass FROM pwt.spcreateusrforauthor(pEmail, pUsrTitleId, pFirstName, NULL, pLastName, pAffiliation, pCity, pCountryId) as lResult;
	
	IF(pUserRoles IS NULL) THEN
		pUserRoles = ARRAY[pRole];
	END IF;
	
	IF lRes.user_exists IS NULL THEN -- nov user
		
		PERFORM spUpdateUserRoles( pJournalId, lRes.new_user_id, pUserRoles);
		IF ( ARRAY[cSERoleType] && pUserRoles ) THEN
			PERFORM spSaveUsrJournalExpertises(lRes.new_user_id, pJournalId, pSubjectCategories, pTaxonCategories, pGeographicalCategories);
		END IF;
		
		IF(pMode IS NOT NULL) THEN
			IF(ARRAY[cSERoleType] && pUserRoles) THEN
				lEventTypeId = 101;
				lRole = cSERoleType;
			ELSEIF (ARRAY[cReviewerRoleType] && pUserRoles) THEN
				lEventTypeId = 102;
				lRole = cSERoleType;
			END IF;
		ELSE
			lEventTypeId = 100;
			lRole = pUserRoles[1];
		END IF;
		--RAISE EXCEPTION 'test: %', lRes.new_user_id;
		SELECT INTO lRes.event_id event_id FROM spCreateEvent(lEventTypeId, null, pUid, pJournalId::int, lRes.new_user_id, lRole);
		
	ELSE -- usera syshtestvuva
		IF(pMode IS NOT NULL) THEN
			PERFORM spJustUpdateUserRoles( pJournalId, lRes.user_exists, pUserRoles);
		ELSE
			PERFORM spUpdateUserRoles( pJournalId, lRes.user_exists, pUserRoles);
		END IF;
		
		IF ( ARRAY[cSERoleType] && pUserRoles ) THEN
			PERFORM spSaveUsrJournalExpertises(lRes.user_exists, pJournalId, pSubjectCategories, pTaxonCategories, pGeographicalCategories);
		END IF;
		lRes.upass = NULL;
	END IF;
	
	RETURN lRes;
END ;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spSaveNewJournalUser(integer, bigint, character varying, integer, character varying, character varying, character varying, character varying, integer, integer[], character varying, character varying, character varying, integer, bigint, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spSaveNewJournalUser(integer, bigint, character varying, integer, character varying, character varying, character varying, character varying, integer, integer[], character varying, character varying, character varying, integer, bigint, integer) TO public;
GRANT EXECUTE ON FUNCTION spSaveNewJournalUser(integer, bigint, character varying, integer, character varying, character varying, character varying, character varying, integer, integer[], character varying, character varying, character varying, integer, bigint, integer) TO postgres;
GRANT EXECUTE ON FUNCTION spSaveNewJournalUser(integer, bigint, character varying, integer, character varying, character varying, character varying, character varying, integer, integer[], character varying, character varying, character varying, integer, bigint, integer) TO iusrpmt;
GRANT EXECUTE ON FUNCTION spSaveNewJournalUser(integer, bigint, character varying, integer, character varying, character varying, character varying, character varying, integer, integer[], character varying, character varying, character varying, integer, bigint, integer) TO pensoft;
