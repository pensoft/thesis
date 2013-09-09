DROP TYPE ret_spAutoUnlockDocument CASCADE;
CREATE TYPE ret_spAutoUnlockDocument AS (
	result int
);

CREATE OR REPLACE FUNCTION spAutoUnlockDocument(
	pDocumentId int,
	pAutoUnlockDuration bigint,
	pUid integer
)
  RETURNS ret_spAutoUnlockDocument AS
$BODY$
DECLARE
	lRes ret_spAutoUnlockDocument;
BEGIN 
	lRes.result = 0;
	
	IF EXISTS (
		SELECT * 
		FROM pwt.documents 
		WHERE id = pDocumentId AND is_locked = true AND lock_usr_id = pUid AND last_content_change + (pAutoUnlockDuration || ' seconds')::interval < now() AND lock_primary_date + (pAutoUnlockDuration || ' seconds')::interval < now()
	) THEN 
		UPDATE pwt.documents SET
			is_locked = false,
			lock_usr_id = null
		WHERE id = pDocumentId;
		lRes.result = 1;
	END IF;
	
	RETURN lRes;
 
END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
GRANT EXECUTE ON FUNCTION spAutoUnlockDocument(
	pDocumentId int,
	pAutoUnlockDuration bigint,
	pUid integer
) TO iusrpmt;
