
DROP FUNCTION IF EXISTS spInsertPJSUserFromOldPjs(
	pPJScid int,
	pUname character varying,
	pUpass character varying,
	pFirstName character varying,
	pMiddleName character varying,
	pLastName character varying,
	pUsrTitle character varying,
	pClientType character varying,
	pAffiliation character varying,
	pDepartment character varying,
	pAddrStreet character varying,
	pAddrCity character varying,
	pAddrCountry character varying,
	pPhone character varying,
	pFax character varying,
	pVat character varying,
	pWeb character varying,
	pFirm character varying,
	pDime character varying,
	pDaddress character varying,
	pDCity character varying,
	pDZip character varying,
	pAlerts character varying,
	pSubjectCats character varying,
	pTaxonCats character varying,
	pGeoCats character varying,
	pChronoCats character varying,
	pJournals character varying
);

CREATE OR REPLACE FUNCTION spInsertPJSUserFromOldPjs(
	pPJScid int,
	pUname character varying,
	pUpass character varying,
	pFirstName character varying,
	pMiddleName character varying,
	pLastName character varying,
	pUsrTitle character varying,
	pClientType character varying,
	pAffiliation character varying,
	pDepartment character varying,
	pAddrStreet character varying,
	pAddrCity character varying,
	pZip character varying,
	pAddrCountry character varying,
	pPhone character varying,
	pFax character varying,
	pVat character varying,
	pWeb character varying,
	pFirm character varying,
	pDime character varying,
	pDaddress character varying,
	pDCity character varying,
	pDZip character varying,
	pAlerts character varying,
	pSubjectCats character varying,
	pTaxonCats character varying,
	pGeoCats character varying,
	pChronoCats character varying,
	pJournals character varying
)
 RETURNS int AS
$BODY$
DECLARE
	lUsrId int;
	lUsrTitleId int;
	lUsrClientTypeId int;
	lUsrCountryId int;
	lUsrDCountryId int;
	lUsrAlertsId int;
BEGIN
	
	SELECT INTO lUsrTitleId coalesce(id, 0) FROM usr_titles WHERE trim(lower(name)) = trim(lower(pUsrTitle));
	SELECT INTO lUsrClientTypeId coalesce(id, 0) FROM client_types WHERE trim(lower(name)) = trim(lower(pClientType));
	SELECT INTO lUsrCountryId coalesce(id, 0) FROM countries WHERE trim(lower(name)) = trim(lower(pAddrCountry));
	SELECT INTO lUsrDCountryId coalesce(id, 0) FROM countries WHERE trim(lower(name)) = trim(lower(pDCity));
	
	
	PERFORM spregusrstep1(NULL, 1, pUname, pUpass, pPJScid);
	lUsrId := currval('usr_id_seq');
	
	PERFORM spregusrstep2(lUsrId, 1, pFirstName, pMiddleName, pLastName, lUsrTitleId, lUsrClientTypeId, pAffiliation, pDepartment, pAddrStreet, pZip, pAddrCity, lUsrCountryId, pPhone, pFax, pVat, pWeb);
	
	UPDATE usr SET 	state = 1 WHERE id = lUsrId; -- ACTIVE 
	
	-- INSERT ADDRESSES
	INSERT INTO usr_addresses(
		uid, "name", firm, address, city, zip, country_id)
	VALUES (lUsrId, pDime, pFirm, pDaddress, pDCity, pDZip, lUsrDCountryId);
		
	RETURN 1;
	
END ;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spInsertPJSUserFromOldPjs(
	pPJScid int,
	pUname character varying,
	pUpass character varying,
	pFirstName character varying,
	pMiddleName character varying,
	pLastName character varying,
	pUsrTitle character varying,
	pClientType character varying,
	pAffiliation character varying,
	pDepartment character varying,
	pAddrStreet character varying,
	pAddrCity character varying,
	pZip character varying,
	pAddrCountry character varying,
	pPhone character varying,
	pFax character varying,
	pVat character varying,
	pWeb character varying,
	pFirm character varying,
	pDime character varying,
	pDaddress character varying,
	pDCity character varying,
	pDZip character varying,
	pAlerts character varying,
	pSubjectCats character varying,
	pTaxonCats character varying,
	pGeoCats character varying,
	pChronoCats character varying,
	pJournals character varying
) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spInsertPJSUserFromOldPjs(
	pPJScid int,
	pUname character varying,
	pUpass character varying,
	pFirstName character varying,
	pMiddleName character varying,
	pLastName character varying,
	pUsrTitle character varying,
	pClientType character varying,
	pAffiliation character varying,
	pDepartment character varying,
	pAddrStreet character varying,
	pAddrCity character varying,
	pZip character varying,
	pAddrCountry character varying,
	pPhone character varying,
	pFax character varying,
	pVat character varying,
	pWeb character varying,
	pFirm character varying,
	pDime character varying,
	pDaddress character varying,
	pDCity character varying,
	pDZip character varying,
	pAlerts character varying,
	pSubjectCats character varying,
	pTaxonCats character varying,
	pGeoCats character varying,
	pChronoCats character varying,
	pJournals character varying
) TO postgres;
GRANT EXECUTE ON FUNCTION spInsertPJSUserFromOldPjs(
	pPJScid int,
	pUname character varying,
	pUpass character varying,
	pFirstName character varying,
	pMiddleName character varying,
	pLastName character varying,
	pUsrTitle character varying,
	pClientType character varying,
	pAffiliation character varying,
	pDepartment character varying,
	pAddrStreet character varying,
	pAddrCity character varying,
	pZip character varying,
	pAddrCountry character varying,
	pPhone character varying,
	pFax character varying,
	pVat character varying,
	pWeb character varying,
	pFirm character varying,
	pDime character varying,
	pDaddress character varying,
	pDCity character varying,
	pDZip character varying,
	pAlerts character varying,
	pSubjectCats character varying,
	pTaxonCats character varying,
	pGeoCats character varying,
	pChronoCats character varying,
	pJournals character varying
) TO iusrpmt;