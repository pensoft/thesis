DELIMITER //

DROP PROCEDURE IF EXISTS spRegUsrStep1 //

CREATE PROCEDURE spRegUsrStep1(
	pUid int,
	pOper int,
	pEmail varchar(128),
	pUpass varchar(128)
)

LANGUAGE SQL
NOT DETERMINISTIC
READS SQL DATA
BEGIN
	DECLARE lId int;
	
	IF (pOper = 1) THEN
		IF (pUid is null) THEN
			INSERT INTO CLIENTS (email, pass) VALUES(trim(pEmail), pUpass);
		
			SET lId = LAST_INSERT_ID();
			SELECT * FROM CLIENTS WHERE cid = lId;
		ELSE
			UPDATE CLIENTS SET 
				pass = pUPass, 
				email = trim(pEmail) 
			WHERE cid = pUid;

			SELECT * FROM CLIENTS WHERE cid = pUid;
		END IF;
	
	
		/* ELSE IF OPER = 2 DELETE*/
		
		
	END IF;


END //
DELIMITER ;