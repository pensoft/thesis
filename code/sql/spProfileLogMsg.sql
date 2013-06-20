DROP TYPE ret_spProfileLogMsg CASCADE;
CREATE TYPE ret_spProfileLogMsg AS (
	id int,
	ip inet,
	taxon_name varchar,	
	date_logged timestamp,
	object_id varchar,
	object_classname varchar,
	object_parentobjectid varchar,
	object_params varchar,
	time_started timestamp,
	got_from_cache varchar,	
	time_finished_retrieving_data timestamp,
	seconds_retrieving float,
	time_finished_parsing_data timestamp,	
	seconds_parsing float
	
	
);

CREATE OR REPLACE FUNCTION spProfileLogMsg(
	pOper int,
	pId int,
	pIp inet, 
	pTaxonName varchar,
	pDateLogged timestamp,
	pObjectId varchar,
	pObjectName varchar,
	pObjectParentObjectId varchar,
	pObjectParams varchar,
	pTimeStarted timestamp,
	pGotFromCache varchar,
	pTimeFinishedRetrievingData timestamp,
	pSecondsRetrieving float,
	pTimeFinishedParsingData timestamp,
	pSecondsParsing float
	
)
  RETURNS ret_spProfileLogMsg AS
$BODY$
DECLARE
lRes ret_spProfileLogMsg;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		SELECT INTO lId id FROM profile_log WHERE taxon_name = pTaxonName AND date_logged = pDateLogged AND ip = pIp AND object_id = pObjectId;
		IF lId IS NULL THEN
			INSERT INTO profile_log(
				ip, taxon_name, date_logged, object_id, object_classname, object_parentobjectid, object_params,
				time_started, got_from_cache, time_finished_retrieving_data, seconds_retrieving, time_finished_parsing_data, seconds_parsing
			) VALUES (
				pIp, pTaxonName, pDateLogged, pObjectId, pObjectName, pObjectParentObjectId, pObjectParams,
				pTimeStarted, pGotFromCache, pTimeFinishedRetrievingData, pSecondsRetrieving, pTimeFinishedParsingData, pSecondsParsing
			);
			lId = currval('profile_log_id_seq');
		END IF;
	ELSE -- Update
		UPDATE profile_log SET
			ip = pIp,
			taxon_name = pTaxonName,
			date_logged = pDateLogged,
			object_id = pObjectId,
			object_classname = pObjectName,
			object_parentobjectid = pObjectParentObjectId,
			object_params = pObjectParams,
			time_started = pTimeStarted,
			got_from_cache = pGotFromCache,
			time_finished_retrieving_data = pTimeFinishedRetrievingData,
			seconds_retrieving = pSecondsRetrieving,
			time_finished_parsing_data = pTimeFinishedParsingData,
			seconds_parsing = pSecondsParsing
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM profile_log WHERE id = lId;

END IF;


SELECT INTO lRes id, ip, taxon_name, date_logged, object_id, object_classname, object_parentobjectid, object_params, time_started, got_from_cache, time_finished_retrieving_data, 
	seconds_retrieving, time_finished_parsing_data, seconds_parsing
FROM profile_log WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spProfileLogMsg(
	pOper int,
	pId int,
	pIp inet, 
	pTaxonName varchar,
	pDateLogged timestamp,
	pObjectId varchar,
	pObjectName varchar,
	pObjectParentObjectId varchar,
	pObjectParams varchar,
	pTimeStarted timestamp,
	pGotFromCache varchar,
	pTimeFinishedRetrievingData timestamp,
	pSecondsRetrieving float,
	pTimeFinishedParsingData timestamp,
	pSecondsParsing float
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spProfileLogMsg(
	pOper int,
	pId int,
	pIp inet, 
	pTaxonName varchar,
	pDateLogged timestamp,
	pObjectId varchar,
	pObjectName varchar,
	pObjectParentObjectId varchar,
	pObjectParams varchar,
	pTimeStarted timestamp,
	pGotFromCache varchar,
	pTimeFinishedRetrievingData timestamp,
	pSecondsRetrieving float,
	pTimeFinishedParsingData timestamp,
	pSecondsParsing float
) TO postgres84;
GRANT EXECUTE ON FUNCTION spProfileLogMsg(
	pOper int,
	pId int,
	pIp inet, 
	pTaxonName varchar,
	pDateLogged timestamp,
	pObjectId varchar,
	pObjectName varchar,
	pObjectParentObjectId varchar,
	pObjectParams varchar,
	pTimeStarted timestamp,
	pGotFromCache varchar,
	pTimeFinishedRetrievingData timestamp,
	pSecondsRetrieving float,
	pTimeFinishedParsingData timestamp,
	pSecondsParsing float
) TO iusrpmt;
