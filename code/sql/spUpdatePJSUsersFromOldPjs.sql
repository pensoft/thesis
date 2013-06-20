
DROP FUNCTION IF EXISTS spUpdatePJSUsersFromOldPjs(
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

CREATE OR REPLACE FUNCTION spUpdatePJSUsersFromOldPjs(
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
	SELECT INTO lUsrAlertsId coalesce(id, 0) FROM usr_alerts_frequency WHERE trim(lower(name)) = trim(lower(pAlerts));
	
	UPDATE usr SET
		uname = pUname,
		upass = md5(pUpass),
		first_name = pFirstName,
		middle_name = pMiddleName,
		last_name = pLastName,
		usr_title_id = lUsrTitleId,
		client_type_id = lUsrClientTypeId,
		affiliation = pAffiliation,
		departament = pDepartment,
		addr_street = pAddrStreet,
		addr_city = pAddrCity,
		addr_postcode = pZip,
		country_id = lUsrCountryId,
		phone = pPhone,
		fax = pFax,
		vat = pVat,
		website = pWeb, 
		--state = 1,
		--utype = utype,
		--photo_id = photo_id,
		journals = 
		(CASE WHEN 
						(length(pJournals) > 1 AND length(pJournals) <> 2)  
			THEN array_sort_unique(regexp_split_to_array(regexp_replace(trim(both '|' from pJournals), '\|+', '|', 'g'), E'\\D'))::int[]
			ELSE NULL 
		END),
		usr_alerts_frequency_id = lUsrAlertsId,
		--product_types = ?,
		subject_categories = 
		(CASE WHEN 
						(length(pSubjectCats) > 1 AND length(pSubjectCats) <> 2)  
			THEN array_sort_unique(regexp_split_to_array(regexp_replace(trim(both '|' from pSubjectCats), '\|+', '|', 'g'), E'\\D'))::int[]
			ELSE NULL 
		END),
		taxon_categories = 
		(CASE WHEN 
						(length(pTaxonCats) > 1 AND length(pTaxonCats) <> 2)  
			THEN array_sort_unique(regexp_split_to_array(regexp_replace(trim(both '|' from pTaxonCats), '\|+', '|', 'g'), E'\\D'))::int[]
			ELSE NULL 
		END),
		chronological_categories = 
		(CASE WHEN 
						(length(pChronoCats) > 1 AND length(pChronoCats) <> 2)  
			THEN array_sort_unique(regexp_split_to_array(regexp_replace(trim(both '|' from pChronoCats), '\|+', '|', 'g'), E'\\D'))::int[]
			ELSE NULL 
		END),
		geographical_categories = 
		(CASE WHEN 
						(length(pGeoCats) > 1 AND length(pGeoCats) <> 2)  
			THEN array_sort_unique(regexp_split_to_array(regexp_replace(trim(both '|' from pGeoCats), '\|+', '|', 'g'), E'\\D'))::int[]
			ELSE NULL 
		END),
		--confhash = ?, 
		--create_date = ?, 
		--activate_date = ?, 
		modify_date = CURRENT_TIMESTAMP
		--access_date = ?, 
		--reg_ip = ?,
		--activate_ip = ?, 
		--access_ip = ?,
		--autolog_hash = autolog_hash
		--expertise_subject_categories = ?,
		--expertise_chronological_categories = ?,
		--expertise_taxon_categories = ?,
		--expertise_geographical_categories = ?

	WHERE oldpjs_cid = pPJScid;
	
	
	UPDATE usr_addresses AS ua -- UPDATE ADDRESSES
	SET 
		name = pDime,
		firm = pFirm,
		address = pDaddress,
		city = pDCity,
		zip = pDZip,
		country_id = lUsrCountryId
	FROM usr AS u
	WHERE ua.uid = u.id AND u.oldpjs_cid = pPJScid;
		
	RETURN 1;
	
END ;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spUpdatePJSUsersFromOldPjs(
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
GRANT EXECUTE ON FUNCTION spUpdatePJSUsersFromOldPjs(
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
GRANT EXECUTE ON FUNCTION spUpdatePJSUsersFromOldPjs(
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