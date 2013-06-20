-- Type: pwt.ret_spgetdocumencontributors

--DROP TYPE pwt.ret_spgetdocumencontributors CASCADE;

CREATE TYPE pwt.ret_spgetdocumencontributors AS
   (user_exists integer,
    new_user_id integer,
    instance_id integer,
    document_id integer,
	upass varchar);
ALTER TYPE pwt.ret_spgetdocumencontributors OWNER TO postgres;

-- Function: pwt.spGetDocumentContributors(bigint, bigint)

-- DROP FUNCTION pwt.spGetDocumentContributors(bigint, bigint);

CREATE OR REPLACE FUNCTION pwt.spGetDocumentContributors(pdocumentid bigint, pinstanceid bigint)
  RETURNS pwt.ret_spgetdocumencontributors AS
$BODY$
	DECLARE
		lPrevInstanceId bigint;
		
		--fields values
		lUsrIdVal int;
		lUsrIdExistsVal int;
		lUsrIdNewVal int;
		lUsrPass varchar;
		lEmailVal varchar;
		lSalutationVal int;
		lFirstNameVal varchar;
		lMiddleNameVal varchar;
		lLastNameVal varchar;
		lAffVal varchar;
		lCityVal varchar;
		lCountryVal int;
		lCoAuthorVal int[];
		lRightsVal int[];
		lSendMailVal int;
		
		lUserContributorTypeId int;
		
		--fields IDs
		lUsrIdID bigint;
		lEmailValID bigint;
		lSalutationValID bigint;
		lFirstNameValID bigint;
		lMiddleNameValID bigint;
		lLastNameValID bigint;
		lAffValID bigint;
		lCityValID bigint;
		lCountryValID bigint;
		lRightsValID bigint;
		lSendMailValID bigint;
		
		lUsrIdInstanceId bigint;
		
		lContributorObjectId bigint;
		
		lRec RECORD;
		
		lRes pwt.ret_spgetdocumencontributors;
	BEGIN
	
	IF (pDocumentId IS NULL) THEN
		SELECT INTO pDocumentId document_id FROM pwt.document_object_instances WHERE id = pInstanceId;
		lRes.document_id = pDocumentId;
	ELSE
		lRes.document_id = pDocumentId;
	END IF;
	
	
	lContributorObjectId = 12;
	
	lUserContributorTypeId = 4;
	
	lUsrIdID = 13;
	lEmailValID = 4;
	lSalutationValID = 5;
	lFirstNameValID = 6;
	lMiddleNameValID = 7;
	lLastNameValID = 8;
	lAffValID = 9;
	lCityValID = 10;
	lCountryValID = 11;
	lRightsValID = 14;
	lSendMailValID = 288;
	
	lUsrIdVal = NULL;
	lUsrIdExistsVal = NULL;
	lUsrIdNewVal = NULL;
	lUsrPass = NULL;
	lEmailVal = NULL;
	lSalutationVal = NULL;
	lFirstNameVal = NULL;
	lMiddleNameVal = NULL;
	lLastNameVal = NULL;
	lAffVal = NULL;
	lCityVal = NULL;
	lCountryVal = NULL;
	lCoAuthorVal = NULL;
	lRightsVal = NULL;
	lSendMailVal = NULL;
	lUsrIdInstanceId = NULL;
	
	lPrevInstanceId = NULL;
	
	--Delete all document_users data
	DELETE FROM pwt.document_users WHERE document_id = pDocumentId AND usr_type = lUserContributorTypeId;
	
	--Get authors data from document
	
	FOR lRec IN 
		SELECT * FROM pwt.v_getfieldsbyobjects WHERE document_id = pDocumentId and object_id = lContributorObjectId
		/*SELECT i1.id as author_instance_id, v.*
		FROM pwt.document_object_instances i1
		JOIN pwt.document_object_instances i2 ON (i1.document_id = i2.document_id AND i2.pos LIKE i1.pos || '%' AND i1.pos <> i2.pos)
		JOIN pwt.instance_field_values v ON (i2.id = v.instance_id)
		WHERE 
			i1.document_id = pDocumentId
			AND i1.object_id = lContributorObjectId
		ORDER BY i1.id, i2.pos		
		*/
		
	
	LOOP
		
		IF (lPrevInstanceId IS NULL) THEN
			lPrevInstanceId = lRec.root_instance_id;
		END IF;
		
		IF (lPrevInstanceId <> lRec.root_instance_id) THEN
			--Create records in public.usr for new authors
			--IF (lUsrIdExistsVal IS NULL) THEN
			SELECT INTO lUsrIdExistsVal, lUsrIdNewVal, lUsrPass * FROM pwt.spCreateUsrForAuthor(lEmailVal, lSalutationVal, lFirstNameVal, lMiddleNameVal, lLastNameVal, lAffVal, lCityVal, lCountryVal) as uid;
			IF (lUsrIdExistsVal = 0) THEN
				RETURN lRes;
			END IF;

			IF (lUsrIdExistsVal IS NULL) THEN
				lUsrIdVal :=  lUsrIdNewVal;
				lRes.new_user_id :=  lUsrIdNewVal;
				lRes.upass :=  lUsrPass;
			ELSE
				lUsrIdVal :=  lUsrIdExistsVal;
				lRes.user_exists :=  lUsrIdExistsVal;
			END IF;
			
			--Reverse update usr_id in document data
			UPDATE pwt.instance_field_values 
				SET value_int = lUsrIdVal
				WHERE
					document_id = pDocumentId
					AND instance_id = lUsrIdInstanceId
					AND field_id = lUsrIdID;
			--END IF;
			
			--deal with authors' data in pwt.document_users
			INSERT INTO pwt.document_users (document_id, usr_id, first_name, middle_name, last_name, usr_type) VALUES (pDocumentId, lUsrIdVal, lFirstNameVal, lMiddleNameVal, lLastNameVal, lUserContributorTypeId);
			
			lUsrIdExistsVal = NULL;
			lUsrIdNewVal = NULL;
			lEmailVal = NULL;
			lSalutationVal = NULL;
			lFirstNameVal = NULL;
			lMiddleNameVal = NULL;
			lLastNameVal = NULL;
			lAffVal = NULL;
			lCityVal = NULL;
			lCountryVal = NULL;
			lCoAuthorVal = NULL;
			lRightsVal = NULL;
			lSendMailVal = NULL;
			lUsrIdInstanceId = NULL;
			
			lPrevInstanceId = lRec.root_instance_id;
		END IF;
		
		CASE lRec.field_id
			WHEN lSendMailValID THEN lRes.instance_id = lRec.field_instance_id;
			WHEN lUsrIdID THEN lUsrIdVal = lRec.value_int; lUsrIdInstanceId = lRec.field_instance_id;
			WHEN lEmailValID THEN lEmailVal = lRec.value_str;
			WHEN lSalutationValID THEN lSalutationVal = lRec.value_int;
			WHEN lFirstNameValID THEN lFirstNameVal = lRec.value_str;
			WHEN lMiddleNameValID THEN lMiddleNameVal = lRec.value_str;
			WHEN lLastNameValID THEN lLastNameVal = lRec.value_str;
			WHEN lAffValID THEN lAffVal = (CASE WHEN lAffVal IS NULL THEN lRec.value_str ELSE lAffVal END);--only first occurrence
			WHEN lCityValID THEN lCityVal = (CASE WHEN lCityVal IS NULL THEN lRec.value_str ELSE lCityVal END);--only first occurrence
			WHEN lCountryValID THEN lCountryVal = (CASE WHEN lCountryVal IS NULL THEN lRec.value_int ELSE lCountryVal END);--only first occurrence
			WHEN lRightsValID THEN lRightsVal = lRec.value_arr_int;
			ELSE
				--do nothing
		END CASE;
		
	END LOOP;
	
	--loop patch
	IF (lPrevInstanceId IS NOT NULL) THEN
		--Create records in public.usr for new authors
		--IF (lUsrIdVal IS NULL) THEN
		SELECT INTO lUsrIdExistsVal, lUsrIdNewVal, lUsrPass * FROM pwt.spCreateUsrForAuthor(lEmailVal, lSalutationVal, lFirstNameVal, lMiddleNameVal, lLastNameVal, lAffVal, lCityVal, lCountryVal) as uid;
		IF (lUsrIdExistsVal = 0) THEN
				RETURN lRes;
		END IF;

		IF (lUsrIdExistsVal IS NULL) THEN
			lUsrIdVal :=  lUsrIdNewVal;
			lRes.new_user_id :=  lUsrIdNewVal;
			lRes.upass :=  lUsrPass;
		ELSE
			lUsrIdVal :=  lUsrIdExistsVal;
			lRes.user_exists :=  lUsrIdExistsVal;
		END IF;
		
		--Reverse update usr_id in document data
		UPDATE pwt.instance_field_values 
			SET value_int = lUsrIdVal
			WHERE
				document_id = pDocumentId
				AND instance_id = lUsrIdInstanceId
				AND field_id = lUsrIdID;
		--END IF;

		--deal with authors' data in pwt.document_users
		INSERT INTO pwt.document_users (document_id, usr_id, first_name, middle_name, last_name, usr_type) VALUES (pDocumentId, lUsrIdVal, lFirstNameVal, lMiddleNameVal, lLastNameVal, lUserContributorTypeId);
	END IF;

	RETURN lRes;
	END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION pwt.spGetDocumentContributors(bigint, bigint) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spGetDocumentContributors(bigint, bigint) TO public;
GRANT EXECUTE ON FUNCTION pwt.spGetDocumentContributors(bigint, bigint) TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spGetDocumentContributors(bigint, bigint) TO iusrpmt;
