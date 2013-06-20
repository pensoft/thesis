DROP TYPE ret_spParseReferenceAuthor CASCADE;
CREATE TYPE ret_spParseReferenceAuthor AS (
	result bigint
);

CREATE OR REPLACE FUNCTION spParseReferenceAuthor(
	pReferenceAuthorInstanceId bigint,
	pUid bigint
)
  RETURNS ret_spParseReferenceAuthor AS
$BODY$
	DECLARE
		lRes ret_spParseReferenceAuthor;			
		lCombinedNameFieldId bigint;
		lFirstNameFieldId bigint;
		lMiddleNameFieldId bigint;
		lLastNameFieldId bigint;
		
		lFirstName varchar;
		lLastName varchar;
		lMiddleName varchar;
		
		lCombinedName varchar;		
		
		lComaPosition int;
		lDotPosition int;
		lSpacePosition int;
		lLastSpacePosition int;
		lFirstPart varchar;
	BEGIN
		lCombinedNameFieldId = 250;
		lFirstNameFieldId = 251;
		lMiddleNameFieldId = 253;
		lLastNameFieldId = 252;
		
		SELECT INTO lCombinedName trim(value_str) FROM
		pwt.instance_field_values i
		WHERE instance_id = pReferenceAuthorInstanceId AND field_id = lCombinedNameFieldId;
		
		
		lComaPosition = strpos(lCombinedName, ',');
		
		IF lComaPosition > 0 THEN
			-- Smith, John K => John (K) Smith
			-- Всичко преди запетайката е фамилия
			lLastName = substring(lCombinedName, 1, lComaPosition - 1);
			lFirstPart = substring(lCombinedName, lComaPosition + 1);-- Всичко след запетайката без самата запетайка
		ELSE
			--Тук всичко след последния интервал е фамилия
			lLastSpacePosition = length(lCombinedName) - position(' ' in reverse(lCombinedName)) + 1;
			IF lLastSpacePosition < length(lCombinedName) THEN -- имаме спейс
				lFirstPart = substring(lCombinedName, 1, lLastSpacePosition - 1);-- Всичко преди интервала
				lLastName = substring(lCombinedName, lLastSpacePosition + 1);-- Всичко след интервала без самия интервал
			ELSE
				lFirstPart = '';
				lLastName = lCombinedName;
			END IF;
		END IF;
				
		lFirstPart = trim(lFirstPart);
		-- Сега трябва да нацепим първото и средното име
		lDotPosition = strpos(lFirstPart, '.');
		IF lDotPosition > 0 THEN -- Ако имаме точка
			-- Всичко преди 1вата точка е 1во име (включително точката)
			lFirstName = substring(lFirstPart, 1, lDotPosition);
			-- Всичко след точката е средно име
			lMiddleName = substring(lFirstPart, lDotPosition + 1);
		ELSE 
			lSpacePosition = strpos(lFirstPart, ' ');
			IF lSpacePosition > 0 THEN -- Всичко преди спейса е 1во име
				lFirstName = substring(lFirstPart, 1, lSpacePosition - 1);
				lMiddleName = substring(lFirstPart, lSpacePosition + 1);
			ELSE -- Всичко е само 1во име
				lFirstName = lFirstPart;
				lMiddleName = '';
			END IF;		
		END IF;
		
		lFirstName = trim(lFirstName);
		lMiddleName = trim(lMiddleName);
		lLastName = trim(lLastName);
		
		UPDATE pwt.instance_field_values SET
			value_str = lFirstName
		WHERE instance_id = pReferenceAuthorInstanceId AND field_id = lFirstNameFieldId;	
		UPDATE pwt.instance_field_values SET
			value_str = lMiddleName
		WHERE instance_id = pReferenceAuthorInstanceId AND field_id = lMiddleNameFieldId;	
		UPDATE pwt.instance_field_values SET
			value_str = lLastName
		WHERE instance_id = pReferenceAuthorInstanceId AND field_id = lLastNameFieldId;	
		
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spParseReferenceAuthor(
	pReferenceAuthorInstanceId bigint,
	pUid bigint
) TO iusrpmt;
