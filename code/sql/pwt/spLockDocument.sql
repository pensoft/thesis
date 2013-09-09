DROP FUNCTION pwt.spLockDocument(
	pDocumentId int,
	pLockOperationId int,
	pLockTimeoutInterval int,	
	pUid int
);

CREATE OR REPLACE FUNCTION pwt.spLockDocument(
	pDocumentId int,
	pLockOperationId int,
	pLockTimeoutInterval int,
	pAutoUnlockDuration bigint,
	pUid int
)
RETURNS int AS
$BODY$
	DECLARE
		lLockDocument boolean;
		lDocumentIsLocked boolean;
		lCurrentDocumentLockUid int;
		lLastLockTs timestamp;
		lCurrTime TIMESTAMP;
		lDocumentId bigint;
		lIsAdmin int;
		lRecord record;
	BEGIN
		lCurrTime = CURRENT_TIMESTAMP;
		SELECT INTO lLockDocument lock_operation_code FROM pwt.lock_operations WHERE id = pLockOperationId;
		IF (lLockDocument IS NOT NULL) THEN
			-- check if admin
			SELECT INTO lIsAdmin id FROM public.usr WHERE id = pUid AND "admin" = TRUE;
			
			SELECT INTO lDocumentId, lDocumentIsLocked, lCurrentDocumentLockUid, lLastLockTs
				d.id, d.is_locked, d.lock_usr_id, d.last_lock_date
			FROM pwt.documents d
			JOIN pwt.document_users du ON (d.id = du.document_id) AND (CASE WHEN lIsAdmin IS NULL THEN du.usr_id = pUid ELSE TRUE END)
			WHERE id = pDocumentId;
			
			IF (
				-- Ако не сме автори - ще гръмне тук
				coalesce(lDocumentId, 0) = 0 OR (
					lDocumentIsLocked = true 				
					AND lCurrentDocumentLockUid <> pUid 
					AND lCurrTime - (pLockTimeoutInterval::text || ' seconds')::interval < lLastLockTs
				)
			) THEN -- Ако някой го е локнал последните X секунди - край
				RETURN 0;
			ELSE
				IF (lDocumentIsLocked <> lLockDocument ) THEN
					UPDATE pwt.documents SET 
						is_locked = lLockDocument, 
						lock_usr_id = CASE WHEN NOT lLockDocument THEN NULL ELSE pUid END,
						last_lock_date = CASE WHEN NOT lLockDocument THEN last_lock_date ELSE lCurrTime END,
						lock_primary_date = CASE 
							WHEN NOT lLockDocument THEN NULL --Unlock							
							ELSE lCurrTime END -- New lock
					WHERE
						id = pDocumentId;
						
					INSERT INTO pwt.lock_history(document_id, lock_operation_id, usr_id, ts)
						VALUES (pDocumentId, pLockOperationId, pUid, lCurrTime);
					RETURN 1;
				ELSE
					UPDATE pwt.documents SET 						
						lock_usr_id = CASE WHEN NOT lLockDocument THEN NULL ELSE pUid END,
						last_lock_date = CASE WHEN NOT lLockDocument THEN last_lock_date ELSE lCurrTime END,
						lock_primary_date = CASE 
							WHEN NOT lLockDocument THEN NULL --Unlock
							WHEN lCurrTime - (pLockTimeoutInterval::text || ' seconds')::interval < lLastLockTs THEN lock_primary_date -- Auto lock 
							ELSE lCurrTime END -- New lock
					WHERE
						id = pDocumentId;
					
					IF lLockDocument AND lCurrTime - (pLockTimeoutInterval::text || ' seconds')::interval < lLastLockTs THEN --Perform auto lock
						-- Check for auto unlock 
						SELECT INTO lRecord *
						FROM spAutoUnlockDocument(pDocumentId, pAutoUnlockDuration, pUid);
						IF lRecord.result = 1 THEN -- Auto unlocked has occurred
							RETURN 0;
						END IF;
					END IF;
						
					RETURN 2;
				END IF;
			END IF;
		ELSE
			RETURN 0;
		END IF;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pwt.spLockDocument(
	pDocumentId int,
	pLockOperationId int,
	pLockTimeoutInterval int,
	pAutoUnlockDuration bigint,
	pUid int
) TO iusrpmt;
