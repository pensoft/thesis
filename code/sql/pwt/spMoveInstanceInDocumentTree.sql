DROP TYPE ret_spMoveInstanceInDocumentTree CASCADE;

CREATE TYPE ret_spMoveInstanceInDocumentTree AS (
	result int,
	swap_instance_id bigint,
	original_available_move_up int,
	original_available_move_down int,
	swap_available_move_up int,
	swap_available_move_down int
);

CREATE OR REPLACE FUNCTION spMoveInstanceInDocumentTree(
	pOper int,
	pInstanceId bigint,	
	pUid int
)
  RETURNS ret_spMoveInstanceInDocumentTree AS
$BODY$
	DECLARE
		lRes ret_spMoveInstanceInDocumentTree;		
		lRecord record;
		lCurrentInstancePos varchar;
		lCurrentInstanceObjectId bigint;
		lRootInstancePos varchar;
		lCurrentPosLength int;
		lContainerItemObjectType int;
		lSwapInstanceId bigint;
		lDocumentId int;
		lCurrentDocumentUserID int;
		lCurrentDocumentUserOrd int;
		lUpInstanceId bigint;
		lDownInstanceId bigint;
		
		cAuthorObjectId CONSTANT bigint := 8;
		cAuthorIDFieldId CONSTANT bigint := 13;
		cAuthorUsrType CONSTANT int := 2;
	BEGIN
		lContainerItemObjectType = 2;
		
		SELECT INTO lCurrentInstancePos, lCurrentInstanceObjectId, lDocumentId pos, object_id, document_id FROM pwt.document_object_instances WHERE id = pInstanceId;
		lCurrentPosLength = char_length(lCurrentInstancePos);
		
		UPDATE pwt.documents SET xml_is_dirty = TRUE WHERE id = lDocumentId;
		
		IF (lCurrentInstanceObjectId = cAuthorObjectId) THEN
			SELECT INTO lCurrentDocumentUserID value_int FROM pwt.instance_field_values WHERE instance_id = pInstanceId AND document_id = lDocumentId AND field_id = cAuthorIDFieldId;
			SELECT INTO lCurrentDocumentUserOrd ord FROM pwt.document_users WHERE document_id = lDocumentId AND usr_id = lCurrentDocumentUserID;
		END IF;

		IF pOper = 1 THEN -- Down
			-- Избираме следващия инстанс
			SELECT INTO lRecord * FROM pwt.document_object_instances WHERE char_length(pos) = lCurrentPosLength
				AND document_id = lDocumentId AND pos > lCurrentInstancePos AND object_id = lCurrentInstanceObjectId
			ORDER BY pos ASC
			LIMIT 1;
			
			IF(lCurrentInstanceObjectId = cAuthorObjectId) THEN -- update document_users ORD
				UPDATE pwt.document_users SET ord = ord - 1 WHERE ord = (lCurrentDocumentUserOrd + 1) AND document_id = lDocumentId AND usr_type = cAuthorUsrType;
				UPDATE pwt.document_users SET ord = ord + 1 WHERE document_id = lDocumentId AND usr_id = lCurrentDocumentUserID AND usr_type = cAuthorUsrType;
			END IF;
			
		ELSEIF pOper = 2 THEN -- Up
			-- Избираме предходния инстанс			
			SELECT INTO lRecord * FROM pwt.document_object_instances WHERE char_length(pos) = lCurrentPosLength
				AND document_id = lDocumentId AND pos < lCurrentInstancePos AND object_id = lCurrentInstanceObjectId
			ORDER BY pos DESC
			LIMIT 1;
			
			IF(lCurrentInstanceObjectId = cAuthorObjectId) THEN -- update document_users ORD
				UPDATE pwt.document_users SET ord = ord + 1 WHERE ord = (lCurrentDocumentUserOrd - 1) AND document_id = lDocumentId AND usr_type = cAuthorUsrType;
				UPDATE pwt.document_users SET ord = ord - 1 WHERE document_id = lDocumentId AND usr_id = lCurrentDocumentUserID AND usr_type = cAuthorUsrType;
			END IF;
			
		END IF;
		
		lSwapInstanceId = lRecord.id;
		lRes.swap_instance_id = lSwapInstanceId;
		
		IF lSwapInstanceId IS NULL THEN
			RAISE EXCEPTION 'pwt.tree_instance_swap.no_instance_to_swap_with';
		END IF;
		
		IF pOper = 1 THEN -- Up
			lUpInstanceId = pInstanceId;					
			lDownInstanceId = lSwapInstanceId;
		ELSEIF pOper = 2 THEN --Down
			lUpInstanceId = lSwapInstanceId;
			lDownInstanceId = pInstanceId;								
		END IF;
		
		PERFORM spPerformInstanceBeforeSqlMoveUpActions(pUid, lUpInstanceId);
		PERFORM spPerformInstanceBeforeSqlMoveDownActions(pUid, lDownInstanceId);
		
		-- RAISE NOTICE 'instance_id %', lRecord.id;
		
		UPDATE pwt.document_object_instances i SET
			pos = overlay(pos placing 
				(CASE WHEN substring(pos, 1, lCurrentPosLength) = lCurrentInstancePos THEN lRecord.pos ELSE lCurrentInstancePos END ) 
			FROM 1 FOR lCurrentPosLength)		
		WHERE i.document_id = lDocumentId AND char_length(pos) >= lCurrentPosLength 
			AND (substring(pos, 1, lCurrentPosLength) = lRecord.pos OR substring(pos, 1, lCurrentPosLength) = lCurrentInstancePos );
		
		PERFORM spPerformInstanceAfterSqlMoveUpActions(pUid, lUpInstanceId);
		PERFORM spPerformInstanceAfterSqlMoveDownActions(pUid, lDownInstanceId);
		
		SELECT INTO lRecord * FROM spCheckInstanceForAvailableMovement(pInstanceId);
		
		lRes.original_available_move_up = lRecord.up;
		lRes.original_available_move_down = lRecord.down;
		
		SELECT INTO lRecord * FROM spCheckInstanceForAvailableMovement(lSwapInstanceId);
		
		lRes.swap_available_move_up = lRecord.up;
		lRes.swap_available_move_down = lRecord.down;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spMoveInstanceInDocumentTree(
	pOper int,	
	pInstanceId bigint,
	pUid int
) TO iusrpmt;
