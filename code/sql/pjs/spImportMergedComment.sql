DROP TYPE IF EXISTS ret_spImportMergedComment CASCADE;
CREATE TYPE ret_spImportMergedComment AS (
	result int,
	id int
);

CREATE OR REPLACE FUNCTION spImportMergedComment(
	pVersionId bigint,
	pCommentId int,
	pOriginalId int,
	pRootId int,
	pNewStartOffset int,
	pNewEndOffset int
)
  RETURNS ret_spImportMergedComment AS
$BODY$
	DECLARE
		lRes ret_spImportMergedComment;
		lMsgID int;
		lRootId int;
		lPos varchar;
		lCurTime timestamp = current_timestamp;		
		lRootPos varchar;
	BEGIN		
		
		IF EXISTS (
			SELECT *
			FROM pjs.msg 
			WHERE version_id = pVersionId AND id = pCommentId
		) THEN
			-- Update the offset
			UPDATE pjs.msg SET
				start_offset = pNewStartOffset,
				end_offset = pNewEndOffset
			WHERE id = pCommentId;
			lRes.id = pCommentId;
		ELSE
			lMsgID := nextval('pjs.msg_id_seq');
			IF pCommentId = pRootId THEN
				lRootId = lMsgID;
				lPos = 'AA';
			ELSE 
				lRootId = pRootId;
				-- update na ord-a
				SELECT INTO lRootPos ord
				FROM pjs.msg
				WHERE id = pRootId;
					
				SELECT INTO lPos
					max(ord)
				FROM pjs.msg 
				WHERE id = pRootId AND ord LIKE COALESCE(lRootPos,'');
				
				--RAISE NOTICE 'Ord: %', lNewOrd;
				IF lPos IS NULL THEN
					lPos := 'AA';
				ELSE
					lPos := ForumGetNextOrd(substring(lPos from char_length(lPos)-1));
				END IF;
				
			END IF;
			
			INSERT INTO pjs.msg(id, version_id, document_id, author, subject, msg, senderip, usr_id, mdate, lastmoddate, flags, rootid,
				start_object_instances_id, end_object_instances_id, start_object_field_id, end_object_field_id, start_offset, end_offset, 
				original_id, ord, is_disclosed, undisclosed_usr_id, is_resolved, resolve_uid, resolve_date) 
			SELECT lMsgID, pVersionId, document_id, author, subject, msg, senderip, usr_id, mdate, lastmoddate, flags, lRootId,
				start_object_instances_id, end_object_instances_id, start_object_field_id, end_object_field_id, pNewStartOffset, pNewEndOffset, 
				original_id, lPos, is_disclosed, undisclosed_usr_id, is_resolved, resolve_uid, resolve_date
			FROM pjs.msg
			WHERE id = pCommentId;
			
			IF lRootId = pRootId THEN
				UPDATE pjs.msg SET 
					replies = replies + 1, 
					lastmoddate = lCurTime 
				WHERE id = pRootId;
			END IF;
			
			lRes.id = lMsgID;
			
		END IF;
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spImportMergedComment(
	pVersionId bigint,
	pCommentId int,
	pOriginalId int,
	pRootId int,
	pNewStartOffset int,
	pNewEndOffset int
) TO iusrpmt;
