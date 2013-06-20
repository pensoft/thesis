DELIMITER //

DROP PROCEDURE IF EXISTS spRegUsrStep2 //

CREATE PROCEDURE spRegUsrStep2(
	pUid int,
	pOper int,
	pFirstName varchar(128),
	pMiddleName varchar(128),
	pLastName varchar(128),
	pSalut varchar(128),
	pCtip varchar(128),
	pFirma varchar(150),
	pDepartament varchar(128),
	pAddrstreet varchar(128),
	pZip varchar(50),
	pCity  varchar(128),
	pCountry varchar(128),
	pPhone varchar(128),
	pFax varchar(128),
	pVat varchar(128),
	pWebsite varchar(128)
)

LANGUAGE SQL
NOT DETERMINISTIC
READS SQL DATA
BEGIN
	
	IF (pOper = 1) THEN
		IF (pUid > 0) THEN
			
			UPDATE CLIENTS SET 
				ime = pFirstName, 
				prezime = pMiddleName,
				familia = pLastName,
				salut = pSalut,
				ctip = pCtip,
				firma = pFirma,
				depart = pDepartament,
				address = pAddrstreet,
				zip = pZip,
				city = pCity,
				country = pCountry,
				phone = pPhone,
				fax = pFax,
				dn = pVat,
				www = pWebsite
			WHERE cid = pUid;

			SELECT * FROM CLIENTS WHERE cid = pUid;
		END IF;
	
	
		/* ELSE IF OPER = 2 DELETE*/
		
		
	END IF;


END //
DELIMITER ;