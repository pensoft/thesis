-- Function: spregusrstep2(integer, integer, character varying, character varying, character varying, integer, integer, character varying, character varying, character varying, character varying, character varying, integer, character varying, character varying, character varying, character varying)

-- DROP FUNCTION spregusrstep2(integer, integer, character varying, character varying, character varying, integer, integer, character varying, character varying, character varying, character varying, character varying, integer, character varying, character varying, character varying, character varying);

CREATE OR REPLACE FUNCTION spregusrstep2(pid integer, pop integer, pfirstname character varying, pmiddlename character varying, plastname character varying, pusrtitleid integer, pclienttype integer, paffiliation character varying, pdepartment character varying, paddrstreet character varying, paddrpostcode character varying, paddrcity character varying, paddrcountryid integer, pphone character varying, pfax character varying, pvat character varying, pwebsite character varying)
  RETURNS integer AS
$BODY$
DECLARE
	lResult int;
BEGIN

	IF (pOp = 1) THEN
		
		IF (pId is not null) THEN
			IF NOT EXISTS (SELECT * FROM usr WHERE id = pId) THEN
				--RAISE EXCEPTION 'This user don not exists!';
			END IF;
			
			--Insert za Step 2
			UPDATE usr SET 	first_name = pFirstName, 
					middle_name = pMiddleName, 
					last_name = pLastName, 
					usr_title_id = pUsrTitleId, 
					client_type_id = pClientType, 
					affiliation = pAffiliation, 
					departament = pDepartment, 
					addr_street = pAddrStreet, 
					addr_postcode = pAddrPostCode, 
					addr_city = pAddrCity, 
					country_id = pAddrCountryId, 
					phone = pPhone, 
					fax = pFax, 
					vat = pVat, 
					website = pWebsite
			WHERE id = pId;
			SELECT INTO lResult id FROM usr WHERE id = pId;
		ELSE
			
			IF NOT EXISTS (SELECT * FROM usr WHERE id = pId) THEN
				--RAISE EXCEPTION 'This user don not exists!';
			END IF;
			
			
		END IF;
	END IF;
	RETURN lResult;

END ;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spregusrstep2(integer, integer, character varying, character varying, character varying, integer, integer, character varying, character varying, character varying, character varying, character varying, integer, character varying, character varying, character varying, character varying) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spregusrstep2(integer, integer, character varying, character varying, character varying, integer, integer, character varying, character varying, character varying, character varying, character varying, integer, character varying, character varying, character varying, character varying) TO postgres;
GRANT EXECUTE ON FUNCTION spregusrstep2(integer, integer, character varying, character varying, character varying, integer, integer, character varying, character varying, character varying, character varying, character varying, integer, character varying, character varying, character varying, character varying) TO iusrpmt;
