DROP FUNCTION pwt.spCommentAdd(
	pRootId integer, 
	pInstanceId bigint, 
	pDocumentId integer, 
	pAuthor character varying, 
	pSubject character varying, 
	pMsg character varying, 
	pSenderIp inet, 
	pUid integer, 
	pStartInstanceId bigint, 
	pEndInstanceId bigint, 
	pStartFieldId bigint, 
	pEndFieldId bigint, 
	pStartOffset integer, 
	pEndOffset integer
);

CREATE OR REPLACE FUNCTION pwt.spCommentAdd(
	pRootId integer, 
	pInstanceId bigint, 
	pDocumentId integer, 
	pAuthor character varying, 
	pSubject character varying, 
	pMsg character varying, 
	pSenderIp inet, 
	pUid integer, 
	pStartInstanceId bigint, 
	pEndInstanceId bigint, 
	pStartFieldId bigint, 
	pEndFieldId bigint, 
	pStartOffset integer, 
	pEndOffset integer
)
  RETURNS integer AS
$BODY$
		DECLARE
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
			lRevisionId bigint;
		BEGIN
			lCurTime := current_timestamp; --tva go polzvame za da insertnem i updatenem s edno i sushto vreme na vsiakude
			IF pRootId IS NOT NULL THEN -- ako imame rootid imame otgovor
				--vzemame nqkoi danni ot root message-a
				SELECT INTO lTopicFlags, lStartObjectInstancesId, lEndObjectInstancesId, lStartObjectFieldId, lEndObjectFieldId, lStartOffset, lEndOffset, lRevisionId  
					flags, start_object_instances_id, end_object_instances_id, start_object_field_id, end_object_field_id,  start_offset, end_offset, revision_id
				FROM pwt.msg
				WHERE
					id = pRootId;
				
				IF (pInstanceId IS NOT NULL AND pDocumentId IS NOT NULL) THEN -- ako eventualno instance i document_id ne sa prazni insert-vame stoinostite
					INSERT INTO pwt.msg (document_id, author, subject, msg, senderip, rootid, usr_id, mdate, lastmoddate, flags, root_object_instance_id, revision_id,
						start_object_instances_id, end_object_instances_id, start_object_field_id, end_object_field_id, start_offset, end_offset) 
					VALUES (pDocumentId, pAuthor, pSubject, pMsg, pSenderIp, pRootId, pUid, lCurTime, lCurTime, lTopicFlags, pInstanceId, lRevisionId,
						null, null, null, null, null, null);
				
					lMsgID := currval('pwt.msg_id_seq');
				END IF;
				
				IF lMsgID IS NOT NULL THEN --uveli4avame replies na root-a
					UPDATE pwt.msg SET replies = replies + 1, lastmoddate = lCurTime WHERE id = pRootId;
				
					-- update na ord-a
					SELECT INTO lReplyOrd ord
						FROM pwt.msg
						WHERE id = pRootId;
						
					SELECT INTO lNewOrd, lLastID 
						max(ord),
						max (
							CASE
								WHEN flags & 1 = 1 THEN id
								ELSE NULL
							END
						)
					FROM pwt.msg 
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
					UPDATE pwt.msg SET ord = lNewOrd WHERE id = lMsgID;
					
				END IF;
				
			ELSE -- ako nqmame rootid suzdavame nov root message
				SELECT INTO lRevisionId 
					spGetDocumentLatestCommentRevisionId(pDocumentId, 0);
				
				lStartObjectInstancesId = pStartInstanceId;
				IF coalesce(lStartObjectInstancesId, 0) = 0 THEN
					lStartObjectInstancesId = null;
				END IF;
				
				lEndObjectInstancesId = pEndInstanceId;
				IF coalesce(lEndObjectInstancesId, 0) = 0 THEN
					lEndObjectInstancesId = null;
				END IF;
				
				lStartObjectFieldId = pStartFieldId;
				IF coalesce(lStartObjectFieldId, 0) = 0 THEN
					lStartObjectFieldId = null;
				END IF;
				
				lEndObjectFieldId = pEndFieldId;
				IF coalesce(lEndObjectFieldId, 0) = 0 THEN
					lEndObjectFieldId = null;
				END IF;
				
				IF (pInstanceId IS NOT NULL AND pDocumentId IS NOT NULL) THEN
					INSERT INTO pwt.msg (document_id, author, subject, msg, senderip, usr_id, mdate, lastmoddate, flags, root_object_instance_id, revision_id,
						start_object_instances_id, end_object_instances_id, start_object_field_id, end_object_field_id, start_offset, end_offset) 
					VALUES (pDocumentId, pAuthor, pSubject, pMsg, pSenderIp, pUid, lCurTime, lCurTime, 0, pInstanceId, lRevisionId, 
						lStartObjectInstancesId, lEndObjectInstancesId, lStartObjectFieldId, lEndObjectFieldId, pStartOffset, pEndOffset);
				
					lMsgID := currval('pwt.msg_id_seq');
					
					IF lMsgID IS NOT NULL THEN
						UPDATE pwt.msg
						SET 
							rootid = lMsgID,
							ord = 'AA'
						WHERE 
							id = lMsgID;
					END IF;
					
				END IF;
			END IF;
			INSERT INTO pwt.activity (usr_id, document_id, action_type, action_time) VALUES(pUid, pDocumentId, 2, lCurTime);
			RETURN lMsgID;
		END;
	$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
GRANT EXECUTE ON FUNCTION pwt.spCommentAdd(
	pRootId integer, 
	pInstanceId bigint, 
	pDocumentId integer, 
	pAuthor character varying, 
	pSubject character varying, 
	pMsg character varying, 
	pSenderIp inet, 
	pUid integer, 
	pStartInstanceId bigint, 
	pEndInstanceId bigint, 
	pStartFieldId bigint, 
	pEndFieldId bigint, 
	pStartOffset integer, 
	pEndOffset integer
) TO iusrpmt;
