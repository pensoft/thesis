CREATE TABLE profile_log(
	id serial PRIMARY KEY,
	taxon_name varchar,
	ip inet,
	date_logged timestamp,
	object_id varchar,
	object_classname varchar,
	object_parentobjectid varchar,
	object_params varchar,
	got_from_cache varchar,
	time_started timestamp,
	time_finished_retrieving_data timestamp,
	time_finished_parsing_data timestamp,
	seconds_retrieving float,
	seconds_parsing float	
	
);

GRANT ALL ON profile_log TO iusrpmt;

ALTER TABLE indesign_template_details ADD COLUMN special int DEFAULT 0;

DROP TYPE ret_spIndesignTemplateDetails CASCADE;


CREATE TYPE ret_spIndesignTemplateDetails AS (
	id int,
	template_id int,
	name varchar,	
	node_id int,
	style varchar,
	type int,
	parent_path varchar,
	new_parent int,
	change_before int,
	change_after int,
	special int
);

CREATE OR REPLACE FUNCTION spIndesignTemplateDetails(
	pOper int,
	pId int,
	pTemplateId int,
	pName varchar,
	pNodeId int,
	pStyle varchar,
	pType int,
	pParentPath varchar,
	pNewParent int,
	pChangeBefore int,
	pChangeAfter int,
	pSpecial int
)
  RETURNS ret_spIndesignTemplateDetails AS
$BODY$
DECLARE
lRes ret_spIndesignTemplateDetails;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		INSERT INTO indesign_template_details(indesign_templates_id, name, node_id, style, type, parent_path, new_parent, change_before, change_after, special) VALUES ( pTemplateId, pName, pNodeId, pStyle, pType, pParentPath, pNewParent, pChangeBefore, pChangeAfter, pSpecial);
		lId = currval('indesign_template_details_id_seq');
	ELSE -- Update
		UPDATE indesign_template_details SET
			name = pName,
			indesign_templates_id = pTemplateId, 
			node_id = pNodeId,
			style = pStyle,
			type = pType,
			parent_path = pParentPath,
			new_parent = pNewParent,
			change_before = pChangeBefore,
			change_after = pChangeAfter,
			special = pSpecial
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM indesign_template_details WHERE id = lId;
ELSEIF pOper = 4 THEN --Copy
	
END IF;


SELECT INTO lRes id, indesign_templates_id, name, node_id, style, type, parent_path, new_parent, change_before, change_after, special FROM indesign_template_details WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spIndesignTemplateDetails(
	pOper int,
	pId int,
	pTemplateId int,
	pName varchar,
	pNodeId int,
	pStyle varchar,
	pType int,
	pParentPath varchar,
	pNewParent int,
	pChangeBefore int,
	pChangeAfter int,
	pSpecial int
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spIndesignTemplateDetails(
	pOper int,
	pId int,
	pTemplateId int,
	pName varchar,
	pNodeId int,
	pStyle varchar,
	pType int,
	pParentPath varchar,
	pNewParent int,
	pChangeBefore int,
	pChangeAfter int,
	pSpecial int
) TO postgres84;
GRANT EXECUTE ON FUNCTION spIndesignTemplateDetails(
	pOper int,
	pId int,
	pTemplateId int,
	pName varchar,
	pNodeId int,
	pStyle varchar,
	pType int,
	pParentPath varchar,
	pNewParent int,
	pChangeBefore int,
	pChangeAfter int,
	pSpecial int
) TO iusrpmt;



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
