DELIMITER //

DROP PROCEDURE IF EXISTS spRegUsrStep3 //

CREATE PROCEDURE spRegUsrStep3(
	pUid int,
	pOper int,
	pBooksType int,
	pEBooksType int,
	pJournalsType int,
	pUsrAlertsFreq varchar(128)
)

LANGUAGE SQL
NOT DETERMINISTIC
READS SQL DATA
BEGIN
	
	IF (pOper = 1) THEN
		IF (pUid > 0) THEN
			
			UPDATE CLIENTS SET 
				AB1 = pBooksType,
				AB2 = pEBooksType,
				AB3 = pJournalsType,
				EMNOT = pUsrAlertsFreq,
				ACTIVE = 'Active'
			WHERE cid = pUid;

			SELECT * FROM CLIENTS WHERE cid = pUid;
		END IF;
	
	
		/* ELSE IF OPER = 2 DELETE*/
		
		
	END IF;


END //
DELIMITER ;