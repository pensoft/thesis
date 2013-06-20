DROP TYPE ret_spSelectAuthor CASCADE;
CREATE TYPE ret_spSelectAuthor AS (
	result int,
	author_instance_id bigint
);

/**
	Тази функция ще се ползва за вкарване на контрибутор/автор от autoselect
*/
CREATE OR REPLACE FUNCTION spSelectAuthor(
	pInstanceId bigint, -- Това е ид-то на инстанса на author_search-a
	pAuthorId int,
	pUid int
)
  RETURNS ret_spSelectAuthor AS
$BODY$
	DECLARE
		lRes ret_spSelectAuthor;
		
		
		lAuthorDetailsObjectId bigint;
		lAuthorInstanceId bigint;
		
		lAuthorIdFieldId bigint;
		lAuthorEmailFieldId bigint;
		lAuthorSalutationFieldId bigint;
		lAuthorFirstNameFieldId bigint;
		lAuthorMiddleNameFieldId bigint;
		lAuthorLastNameFieldId bigint;
		
		lAuthorSecondaryAddressObjectId bigint;
		lAuthorSecondaryAddressInstanceId bigint;
		lAuthorAffiliationFieldId bigint;
		lAuthorCityFieldId bigint;
		lAuthorContryFieldId bigint;
		
		lAuthorObjectId bigint;
		lContributorObjectId bigint;
		
		lCurrentItemObjectId bigint;
		
		lRecord record;
		lRecord2 record;
	BEGIN				
		lAuthorObjectId = 8;
		lContributorObjectId = 12;
		
		lAuthorIdFieldId = 13;
		lAuthorEmailFieldId = 4;
		lAuthorSalutationFieldId = 5;
		lAuthorFirstNameFieldId = 6;
		lAuthorMiddleNameFieldId = 7;
		lAuthorLastNameFieldId = 8;
		
		lAuthorSecondaryAddressObjectId = 5;
		lAuthorAffiliationFieldId = 9;
		lAuthorCityFieldId = 10;
		lAuthorContryFieldId = 11;
		
		-- Автор обекта е директния родител
		SELECT INTO lAuthorInstanceId parent_id FROM pwt.document_object_instances WHERE id = pInstanceId;
		SELECT INTO lCurrentItemObjectId object_id FROM pwt.document_object_instances WHERE id = lAuthorInstanceId;
		
		IF lCurrentItemObjectId = lAuthorObjectId THEN -- Avtor
			lAuthorDetailsObjectId = 6;
		ELSE -- Contributor
			lAuthorDetailsObjectId = 10;
		END IF;
		
		--RAISE NOTICE 'InstId %, ObId %', lAuthorInstanceId, lAuthorDetailsObjectId;
		
		-- Трием name search-a
		-- PERFORM spRemoveInstance(pInstanceId, pUid);
		
		-- Създаваме author_details
		-- SELECT INTO lAuthorInstanceId new_instance_id FROM spCreateNewInstance(lAuthorInstanceId, lAuthorDetailsObjectId, pUid);
		
		-- Ако е подаден автор - трябва да му попълним данните
		SELECT INTO lRecord * FROM usr WHERE id = pAuthorId;
		
		IF lRecord.id IS NOT NULL THEN 
			/*
			-- Ако има няколко secondary адреса - трием ги
			FOR lRecord2 IN
				SELECT id FROM pwt.document_object_instances WHERE parent_id = lAuthorInstanceId AND object_id = lAuthorSecondaryAddressObjectId 
				ORDER BY pos ASC
				OFFSET 1
			LOOP
				PERFORM spRemoveInstance(lRecord2.id);
			END LOOP;
			
			*/
			SELECT INTO lAuthorSecondaryAddressInstanceId id FROM pwt.document_object_instances WHERE parent_id = lAuthorInstanceId AND object_id = lAuthorSecondaryAddressObjectId;
			-- Попълваме author_id
			UPDATE pwt.instance_field_values SET
				value_int = lRecord.id
			WHERE instance_id = lAuthorInstanceId AND field_id = lAuthorIdFieldId;
			
			-- Попълваме author_email
			UPDATE pwt.instance_field_values SET
				value_str = lRecord.uname
			WHERE instance_id = lAuthorInstanceId AND field_id = lAuthorEmailFieldId;
			
			-- Попълваме author_salutation
			UPDATE pwt.instance_field_values SET
				value_int = lRecord.usr_title_id
			WHERE instance_id = lAuthorInstanceId AND field_id = lAuthorSalutationFieldId;
			
			-- Попълваме author_firstname
			UPDATE pwt.instance_field_values SET
				value_str = lRecord.first_name
			WHERE instance_id = lAuthorInstanceId AND field_id = lAuthorFirstNameFieldId;
			
			-- Попълваме author_middlename
			UPDATE pwt.instance_field_values SET
				value_str = lRecord.middle_name
			WHERE instance_id = lAuthorInstanceId AND field_id = lAuthorMiddleNameFieldId;
			
			-- Попълваме author_lastname
			UPDATE pwt.instance_field_values SET
				value_str = lRecord.last_name
			WHERE instance_id = lAuthorInstanceId AND field_id = lAuthorLastNameFieldId;
			
			-- Попълваме author_affiliation
			UPDATE pwt.instance_field_values SET
				value_str = lRecord.affiliation
			WHERE instance_id = lAuthorSecondaryAddressInstanceId AND field_id = lAuthorAffiliationFieldId;
			
			-- Попълваме author_city
			UPDATE pwt.instance_field_values SET
				value_str = lRecord.addr_city
			WHERE instance_id = lAuthorSecondaryAddressInstanceId AND field_id = lAuthorCityFieldId;
			
			-- Попълваме author_country
			UPDATE pwt.instance_field_values SET
				value_int = lRecord.country_id
			WHERE instance_id = lAuthorSecondaryAddressInstanceId AND field_id = lAuthorContryFieldId;
			
		END IF;
		
		lRes.author_instance_id = lAuthorInstanceId;
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSelectAuthor(
	pInstanceId bigint,	
	pAuthorId int,
	pUid int
) TO iusrpmt;
