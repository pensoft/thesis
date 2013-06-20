-- Function: spInbox(integer, integer, integer, character varying, character varying, character varying, integer)

-- DROP FUNCTION spInbox(integer, integer, integer, character varying, character varying, character varying, integer);

CREATE OR REPLACE FUNCTION spInbox(poper integer, pmsgid integer, puid integer, precipient character varying, psubject character varying, pmsg character varying, ptype integer)
  RETURNS integer AS
$BODY$
DECLARE
	lRecIdArr text[];
	lArrSize int;
	lArrIter int;
	lRecId int;
	lRootID int;
	lCurMsgId int;
BEGIN
	
	IF pOper = 0 THEN
		lRecIdArr := string_to_array(pRecipient, ',');
		lArrSize := array_upper(lRecIdArr, 1);
		lArrIter := 1;

		IF (pMsgid IS NULL) THEN
			--RAISE NOTICE 'arr: %', lRecIdArr;
			--RAISE NOTICE 'arrsize: %', lArrSize;
			
			WHILE (lArrIter <= lArrSize) LOOP
				lRecId := lRecIdArr[lArrIter]::int;
				--RAISE NOTICE 'arr: %', lRecId;
				INSERT INTO inbox(sender_id, recipient_id, msg, subject, sender_state, recipient_state, type)
					VALUES(pUid, lRecId, pMsg, pSubject, 2, 1, pType);
				--UPDATE rootid
				lCurMsgId = currval('inbox_id_seq');
				--IF lArrIter = 1 THEN
					lRootID := lCurMsgId;
				--END IF;
				UPDATE inbox SET rootid = lRootID WHERE id = lCurMsgId;
				
				lArrIter := lArrIter + 1;
			END LOOP;
		--RAISE EXCEPTION 'arrsize: %', lArrSize;
		ELSE
			WHILE (lArrIter < lArrSize) LOOP
				lRecId := lRecIdArr[lArrIter]::int;
				
				IF EXISTS (SELECT id FROM usr WHERE id = lRecId) THEN
					INSERT INTO inbox(sender_id, recipient_id, msg, subject, sender_state, recipient_state, rootid, type)
						VALUES(pUid, lRecId, pMsg, pSubject, 2, 1, pMsgid, pType);
				END IF;
					
				lArrIter := lArrIter + 1;
			END LOOP;
		END IF;
		
	ELSEIF pOper = 3 THEN
		
		IF EXISTS (SELECT id FROM inbox WHERE rootid = pMsgid AND sender_id = pUid AND sender_state < 3) THEN
			UPDATE inbox SET sender_state = 3 WHERE rootid = pMsgid;
		END IF;
		
		IF EXISTS (SELECT id FROM inbox WHERE rootid = pMsgid AND recipient_id = pUid AND recipient_state < 3) THEN
			UPDATE inbox SET recipient_state = 3 WHERE rootid = pMsgid;
		END IF;
		
	END IF;
	
	RETURN 1;
END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spInbox(integer, integer, integer, character varying, character varying, character varying, integer)
  OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spInbox(integer, integer, integer, character varying, character varying, character varying, integer) TO postgres;
GRANT EXECUTE ON FUNCTION spInbox(integer, integer, integer, character varying, character varying, character varying, integer) TO iusrpmt;
