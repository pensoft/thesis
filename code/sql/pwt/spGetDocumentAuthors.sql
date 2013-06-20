--DROP TYPE pwt.ret_spgetdocumentauthors CASCADE;
/*
CREATE TYPE pwt.ret_spgetdocumentauthors AS(
	user_exists integer,
	new_user_id integer,
	instance_id integer,
	document_id integer,
	upass varchar
);
*/
-- Dont drop the type because other functions depend on it
DROP FUNCTION pwt.spgetdocumentauthors(
	pDocumentid bigint, 
	pAuthorInstanceId bigint
);

CREATE OR REPLACE FUNCTION pwt.spgetdocumentauthors(
	pDocumentid bigint, 
	pAuthorInstanceId bigint
)
  RETURNS pwt.ret_spgetdocumentauthors AS
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
		lCoAuthorValID bigint;
		lRightsValID bigint;
		lSendMailValID bigint;
		
		lContributorObjectId int;
		lContributorInstanceForDeleteId bigint;
		
		lUsrIdInstanceId bigint;
		
		lAuthorObjectId bigint;
		
		lUserAuthorTypeId int;
		
		lRec RECORD;
		lAuthorInstanceRecord record;
		
		lRes pwt.ret_spgetdocumentauthors;
		
		cAuthorUsrType CONSTANT int := 2;
		lMaxAuthorsOrd int;
	BEGIN
	
		IF (pDocumentId IS NULL) THEN
			SELECT INTO pDocumentId document_id 
			FROM pwt.document_object_instances 
			WHERE id = pAuthorInstanceId;
			
			lRes.document_id = pDocumentId;
		ELSE
			lRes.document_id = pDocumentId;
		END IF;
		
		
		lAuthorObjectId = 8;
		lContributorObjectId = 12;
		lUserAuthorTypeId = 2;
		
		lUsrIdID = 13;
		lEmailValID = 4;
		lSalutationValID = 5;
		lFirstNameValID = 6;
		lMiddleNameValID = 7;
		lLastNameValID = 8;
		lAffValID = 9;
		lCityValID = 10;
		lCountryValID = 11;
		lCoAuthorValID = 15;
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
		DELETE FROM pwt.document_users 
		WHERE document_id = pDocumentId AND usr_type = lUserAuthorTypeId;
		
		lMaxAuthorsOrd = 1;
		
		--Get authors data from document
		
		FOR lAuthorInstanceRecord IN 
			SELECT * 
			FROM pwt.document_object_instances i
			WHERE i.document_id = pDocumentId AND object_id = lAuthorObjectId AND is_confirmed = true
			ORDER BY pos ASC	
		LOOP
			-- Loop through all authors 
			
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
			
			FOR lRec IN 
				SELECT *, root_instance_id as author_instance_id
				FROM pwt.v_getfieldsbyobjects 
				WHERE document_id = pDocumentId AND root_instance_id = lAuthorInstanceRecord.id			
			LOOP
				-- Fill the author details				
				CASE lRec.field_id
					-- WHEN lSendMailValID THEN lRes.instance_id = lRec.field_instance_id;
					WHEN lUsrIdID THEN lUsrIdVal = lRec.value_int;
					WHEN lEmailValID THEN lEmailVal = lRec.value_str;
					WHEN lSalutationValID THEN lSalutationVal = lRec.value_int;
					WHEN lFirstNameValID THEN lFirstNameVal = lRec.value_str;
					WHEN lMiddleNameValID THEN lMiddleNameVal = lRec.value_str;
					WHEN lLastNameValID THEN lLastNameVal = lRec.value_str;
					WHEN lAffValID THEN lAffVal = (CASE WHEN lAffVal IS NULL THEN lRec.value_str ELSE lAffVal END);--only first occurrence
					WHEN lCityValID THEN lCityVal = (CASE WHEN lCityVal IS NULL THEN lRec.value_str ELSE lCityVal END);--only first occurrence
					WHEN lCountryValID THEN lCountryVal = (CASE WHEN lCountryVal IS NULL THEN lRec.value_int ELSE lCountryVal END);--only first occurrence
					WHEN lCoAuthorValID THEN lCoAuthorVal = lRec.value_arr_int;
					WHEN lRightsValID THEN lRightsVal = lRec.value_arr_int;
					ELSE
						--do nothing
				END CASE;
				
			END LOOP;
					
			SELECT INTO lUsrIdExistsVal, lUsrIdNewVal, lUsrPass 
				user_exists, new_user_id, upass
			FROM pwt.spCreateUsrForAuthor(lEmailVal, lSalutationVal, lFirstNameVal, lMiddleNameVal, lLastNameVal, lAffVal, lCityVal, lCountryVal) as uid;
			
			IF lAuthorInstanceRecord.id = pAuthorInstanceId THEN
				lRes.user_exists :=  lUsrIdExistsVal;
				lRes.new_user_id :=  lUsrIdNewVal;
				lRes.upass :=  lUsrPass;
				lRes.instance_id = pAuthorInstanceId;
			END IF;
			
			IF (lUsrIdExistsVal IS NULL) THEN
				lUsrIdVal :=  lUsrIdNewVal;
			ELSE
				lUsrIdVal :=  lUsrIdExistsVal;				
			END IF;
			
			-- RAISE NOTICE 'Uname %, Id %', lEmailVal, lUsrIdVal;
			
			-- Проверяваме дали Автора е Контрибутор и ако е взимаме ид-то на инстанса
			SELECT INTO lContributorInstanceForDeleteId instance_id 
			FROM pwt.instance_field_values ifv
			JOIN pwt.document_object_instances doi ON ifv.instance_id = doi.id AND doi.object_id = lContributorObjectId
			WHERE ifv.field_id = lUsrIdID AND ifv.document_id = pDocumentId AND ifv.value_int = lUsrIdVal;
			-- След което го трием
			IF lContributorInstanceForDeleteId IS NOT NULL THEN
				PERFORM spRemoveInstance( lContributorInstanceForDeleteId, UsrIdVal );
			END IF;
			
			--Reverse update usr_id in document data
			UPDATE pwt.instance_field_values SET 
				value_int = lUsrIdVal
			WHERE document_id = pDocumentId
				AND instance_id = lAuthorInstanceRecord.id
				AND field_id = lUsrIdID;
			--END IF;
			
			--SELECT INTO lMaxAuthorsOrd max(ord) as ord FROM pwt.document_users WHERE document_id = pDocumentId AND usr_type = cAuthorUsrType;
			
			--lMaxAuthorsOrd := coalesce(lMaxAuthorsOrd, 0);
			
			--RAISE EXCEPTION 'lMaxAuthorsOrd: %, documentID: %, cAuthorUsrType: %, lUserAuthorTypeId: %', lMaxAuthorsOrd, pDocumentId, cAuthorUsrType, lUserAuthorTypeId;
			--deal with authors' data in pwt.document_users
			INSERT INTO pwt.document_users (document_id, usr_id, first_name, middle_name, last_name, usr_type, ord) 
				VALUES (pDocumentId, lUsrIdVal, lFirstNameVal, lMiddleNameVal, lLastNameVal, lUserAuthorTypeId, lMaxAuthorsOrd);
				
			IF(lUserAuthorTypeId = cAuthorUsrType) THEN
				lMaxAuthorsOrd = lMaxAuthorsOrd + 1;
			END IF;
			
		END LOOP;
		
		-- Накрая викаме процедурата за контрибутори за да зачисти останалото освен контрибутор инстанса (например записите в pwt.document_users)
		PERFORM pwt.spGetDocumentContributors(pDocumentId, 0);
			
		RETURN lRes;
	END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
GRANT EXECUTE ON FUNCTION pwt.spgetdocumentauthors(
	pDocumentid bigint, 
	pAuthorInstanceId bigint
) TO iusrpmt;
