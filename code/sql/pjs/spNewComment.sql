DROP TYPE pjs.ret_spNewComment CASCADE;
CREATE TYPE pjs.ret_spNewComment AS (
	comment_id int, 
	start_instance_id bigint,
	start_field_id bigint,
	start_offset int,
	end_instance_id bigint,
	end_field_id bigint,
	end_offset int,
	msg varchar
);

CREATE OR REPLACE FUNCTION pjs.spNewComment(
	pVersionId bigint, 
	pMsg character varying, 
	
	pStartInstanceId bigint, 
	pStartFieldId bigint, 
	pStartOffset int, 
	
	pEndInstanceId bigint, 
	pEndFieldId bigint, 
	pEndOffset int, 
	
	pSenderIp inet, 
		
	pUid integer,
	pAuthor character varying
)
  RETURNS pjs.ret_spNewComment AS
$BODY$
		DECLARE
			lRes pjs.ret_spNewComment;
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
			lVersionIsReadonly int;
		BEGIN
			lVersionIsReadonly = pjs.spCheckIfPjsVersionIsReadonly(pVersionId);
			IF coalesce(lVersionIsReadonly, 0) = 1 THEN
				RAISE EXCEPTION 'pjs.specifiedVersionIsReadonly';
			END IF;
				
			lCurTime := current_timestamp; --tva go polzvame za da insertnem i updatenem s edno i sushto vreme na vsiakude			
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
			
			IF (pVersionId IS NOT NULL) THEN
				SELECT INTO lDocumentId 
					document_id
				FROM pjs.document_versions
				WHERE id = pVersionId;
				
				lMsgID := nextval('pjs.msg_id_seq');
				INSERT INTO pjs.msg (id, version_id, document_id, author, subject, msg, senderip, usr_id, mdate, lastmoddate, flags, 
					start_object_instances_id, end_object_instances_id, start_object_field_id, end_object_field_id, start_offset, end_offset, 
					original_id) 
				VALUES (lMsgID, pVersionId, lDocumentId, pAuthor, '', pMsg, pSenderIp, pUid, lCurTime, lCurTime, 0,  
					lStartObjectInstancesId, lEndObjectInstancesId, lStartObjectFieldId, lEndObjectFieldId, pStartOffset, pEndOffset,
					lMsgID);
			
				
				
				IF lMsgID IS NOT NULL THEN
					UPDATE pjs.msg
					SET 
						rootid = lMsgID,
						ord = 'AA'
					WHERE 
						id = lMsgID;
				END IF;
				
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
  
GRANT EXECUTE ON FUNCTION pjs.spNewComment(
	pVersionId bigint, 
	pMsg character varying, 
	
	pStartInstanceId bigint, 
	pStartFieldId bigint, 
	pStartOffset int, 
	
	pEndInstanceId bigint, 
	pEndFieldId bigint, 
	pEndOffset int, 
	
	pSenderIp inet, 
		
	pUid integer,
	pAuthor character varying
) TO iusrpmt;
