DROP TYPE pjs.ret_spNewCommentReply CASCADE;
CREATE TYPE pjs.ret_spNewCommentReply AS (
	comment_id int, 
	start_instance_id bigint,
	start_field_id bigint,
	start_offset int,
	end_instance_id bigint,
	end_field_id bigint,
	end_offset int,
	msg varchar
);

CREATE OR REPLACE FUNCTION pjs.spNewCommentReply(
	pRootId bigint, 
	pMsg character varying, 
	
	pSenderIp inet, 
		
	pUid integer,
	pAuthor character varying
)
  RETURNS pjs.ret_spNewCommentReply AS
$BODY$
		DECLARE
			lRes pjs.ret_spNewCommentReply;
			lMsgID int;
			lNewOrd varchar;
			lReplyOrd varchar;
			lRootID int;
			lLastID int;
			lCurTime timestamp;
			lTopicFlags int;
			lStartObjectInstancesId int;
			lEndObjectInstancesId int;
			lStartObjectFieldId int;
			lEndObjectFieldId int;
			lStartOffset int;
			lEndOffset int;
			lFlag int;
			lDocumentId bigint;
			lVersionId bigint;
		BEGIN
			lCurTime := current_timestamp; --tva go polzvame za da insertnem i updatenem s edno i sushto vreme na vsiakude			
			SELECT INTO lTopicFlags, lStartObjectInstancesId, lEndObjectInstancesId, lStartObjectFieldId, lEndObjectFieldId, lStartOffset, lEndOffset, lDocumentId, lVersionId
				flags, start_object_instances_id, end_object_instances_id, start_object_field_id, end_object_field_id,  start_offset, end_offset, document_id, version_id
			FROM pjs.msg
			WHERE
				id = pRootId;
			
			lMsgID := nextval('pjs.msg_id_seq');
			INSERT INTO pjs.msg (id, version_id, document_id, author, subject, msg, senderip, rootid, usr_id, mdate, lastmoddate, flags,  
				start_object_instances_id, end_object_instances_id, start_object_field_id, end_object_field_id, start_offset, end_offset,
				original_id) 
			VALUES (lMsgID, lVersionId, lDocumentId, pAuthor, '', pMsg, pSenderIp, pRootId, pUid, lCurTime, lCurTime, lTopicFlags, 
				null, null, null, null, null, null, 
				lMsgID);
			
			
			
			IF lMsgID IS NOT NULL THEN --uveli4avame replies na root-a
				UPDATE pjs.msg SET 
					replies = replies + 1, 
					lastmoddate = lCurTime 
				WHERE id = pRootId;
			
				-- update na ord-a
				SELECT INTO lReplyOrd ord
				FROM pjs.msg
				WHERE id = pRootId;
					
				SELECT INTO lNewOrd, lLastID 
					max(ord),
					max (
						CASE
							WHEN flags & 1 = 1 THEN id
							ELSE NULL
						END
					)
				FROM pjs.msg 
				WHERE
				id = pRootId
				AND ord LIKE COALESCE(lReplyOrd,'');
				
				--RAISE NOTICE 'Ord: %', lNewOrd;
				IF lNewOrd IS NULL THEN
					lNewOrd := 'AA';
				ELSE
					lNewOrd := ForumGetNextOrd(substring(lNewOrd from char_length(lNewOrd)-1));
				END IF;
				
				--RAISE NOTICE 'Ord: %', lNewOrd;
				UPDATE pjs.msg SET 
					ord = lNewOrd 
				WHERE id = lMsgID;
				
			END IF;
				
				
						
			SELECT INTO lRes
				lMsgID, start_object_instances_id, start_object_field_id, start_offset,
				end_object_instances_id, end_object_field_id, end_offset, msg
			FROM pjs.msg
			WHERE id = lMsgID;
			
			RETURN lRes;
		END;
	$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
  
GRANT EXECUTE ON FUNCTION pjs.spNewCommentReply(
	pRootId bigint, 
	pMsg character varying, 
	
	pSenderIp inet, 
		
	pUid integer,
	pAuthor character varying
) TO iusrpmt;
