CREATE OR REPLACE FUNCTION pwt.spAutoLockDocument(
	pDocumentId int,	
	pLockTimeoutInterval int,
	pUid int
)
RETURNS int AS
$BODY$
	DECLARE		
		lCurrentDocumentIsLocked boolean;
		lCurrentDocumentLockUid int;
		lLastLockTs timestamp;
		lCurrTime TIMESTAMP;
	BEGIN
		lCurrTime = CURRENT_TIMESTAMP;
		
		SELECT INTO lCurrentDocumentIsLocked, lCurrentDocumentLockUid, lLastLockTs d.is_locked, d.lock_usr_id, d.last_lock_date
			FROM pwt.documents d
			JOIN pwt.document_users du on (d.id = du.document_id)
			WHERE id = pDocumentId;
		--RAISE NOTICE 'Time %, Locked %', lCurrTime - (pLockTimeoutInterval::text || ' seconds')::interval, lLastLockTs;
		-- IF lCurrentDocumentIsLocked = false THEN
			-- RAISE EXCEPTION 'pwt.documentIsUnlocked';
		-- END IF;
		
		IF lCurrentDocumentIsLocked = true AND lCurrentDocumentLockUid <> pUid AND lCurrTime - (pLockTimeoutInterval::text || ' seconds')::interval < lLastLockTs THEN
			RAISE EXCEPTION 'pwt.documentHasBeenLockedByAnotherUser';
		END IF;
		
		UPDATE pwt.documents SET 			
			last_lock_date = lCurrTime,
			is_locked = true,
			lock_usr_id = pUid
		WHERE
			id = pDocumentId;
		
		RETURN 1;
		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pwt.spAutoLockDocument(
	pDocumentId int,	
	pLockTimeoutInterval int,
	pUid int
) TO iusrpmt;
