DROP TYPE pwt.ret_spImportRevisionComments CASCADE;
CREATE TYPE pwt.ret_spImportRevisionComments AS (
	result int
);

CREATE OR REPLACE FUNCTION pwt.spImportRevisionComments(
	pRevisionId bigint,
	pCommentsXml xml
)
  RETURNS pwt.ret_spImportRevisionComments AS
$BODY$
DECLARE
	lRes pwt.ret_spImportRevisionComments;
	lRecord record;
	lComments xml[];
	lTemp xml[];
	
	lCurrentCommentXml xml;
	lIter int;
	
	lStartInstanceId bigint;
	lStartFieldId bigint;
	lStartOffset int;
	
	lEndInstanceId bigint;
	lEndFieldId bigint;
	lEndOffset int;
	
	lOrd varchar;
	lMdate timestamp;
	lFlags int;
	lUsrId int;
	lRootId int;
	lMsg varchar;
	lPreviousId int;
	lDocumentId bigint;
	lRootInstanceId bigint;
	lFirstInstanceId bigint;
	
	lMsgID int;
	lIsResolved int;
	lResolveUid int;
	lResolveTime timestamp;
	
	lIsDisclosed int;
	lUndisclosedUid int;
BEGIN
	lRes.result = 1;
	
	SELECT INTO lComments xpath('//comment', pCommentsXml);
	CREATE TEMP TABLE comments_import (
		LIKE pwt.msg,
		new_id int
	);
	
	SELECT INTO lDocumentId 
		document_id
	FROM pwt.document_revisions WHERE id = pRevisionId;
	
	IF lDocumentId IS NULL THEN
		RETURN lRes;
	END IF;
	
	SELECT INTO lFirstInstanceId id
	FROM pwt.document_object_instances doi
	WHERE document_id = lDocumentId 
	ORDER BY pos ASC 
	LIMIT 1;
	
	SET datestyle = "ISO, DMY";
	
	FOR lIter IN 1..coalesce(array_upper(lComments, 1), 0)
	LOOP
		lCurrentCommentXml = lComments[lIter];
		SELECT INTO lTemp xpath('@id', lCurrentCommentXml);
		lPreviousId = spConvertAnyToInt(lTemp[1]);
		
		SELECT INTO lTemp xpath('/comment/start_instance_id/text()', lCurrentCommentXml);
		lStartInstanceId = spConvertAnyToBigInt(lTemp[1]);
		SELECT INTO lTemp xpath('/comment/start_field_id/text()', lCurrentCommentXml);
		lStartFieldId = spConvertAnyToBigInt(lTemp[1]);
		SELECT INTO lTemp xpath('/comment/start_offset/text()', lCurrentCommentXml);
		lStartOffset = spConvertAnyToInt(lTemp[1]);
		
		SELECT INTO lTemp xpath('/comment/end_instance_id/text()', lCurrentCommentXml);
		lEndInstanceId = spConvertAnyToBigInt(lTemp[1]);
		SELECT INTO lTemp xpath('/comment/end_field_id/text()', lCurrentCommentXml);
		lEndFieldId = spConvertAnyToBigInt(lTemp[1]);
		SELECT INTO lTemp xpath('/comment/end_offset/text()', lCurrentCommentXml);
		lEndOffset = spConvertAnyToInt(lTemp[1]);
		
		SELECT INTO lTemp xpath('/comment/pos/text()', lCurrentCommentXml);
		lOrd = lTemp[1]::varchar;
		SELECT INTO lTemp xpath('/comment/msg/text()', lCurrentCommentXml);
		lMsg = lTemp[1]::varchar;
		
		SELECT INTO lTemp xpath('/comment/flags/text()', lCurrentCommentXml);
		lFlags = spConvertAnyToInt(lTemp[1]);
		SELECT INTO lTemp xpath('/comment/usr_id/text()', lCurrentCommentXml);
		lUsrId = spConvertAnyToInt(lTemp[1]);
		SELECT INTO lTemp xpath('/comment/rootid/text()', lCurrentCommentXml);
		lRootId = spConvertAnyToInt(lTemp[1]);
		SELECT INTO lTemp xpath('/comment/createdate/text()', lCurrentCommentXml);
		lMdate = spConvertAnyToTimestamp(lTemp[1]);
		
		SELECT INTO lTemp xpath('/comment/resolve_date/text()', lCurrentCommentXml);
		lResolveTime = spConvertAnyToTimestamp(lTemp[1]);
		SELECT INTO lTemp xpath('/comment/is_resolved/text()', lCurrentCommentXml);
		lIsResolved = spConvertAnyToInt(lTemp[1]);
		SELECT INTO lTemp xpath('/comment/resolve_uid/text()', lCurrentCommentXml);
		lResolveUid = spConvertAnyToInt(lTemp[1]);
		
		SELECT INTO lTemp xpath('/comment/is_disclosed/text()', lCurrentCommentXml);
		lIsDisclosed = spConvertAnyToInt(lTemp[1]);
		SELECT INTO lTemp xpath('/comment/undisclosed_usr_id/text()', lCurrentCommentXml);
		lUndisclosedUid = spConvertAnyToInt(lTemp[1]);
		
		/*
		RAISE NOTICE 'id %, usr_id %, ord %, msg %, flags %, rootid %, mdate %, start_object_instances_id %, start_object_field_id %, start_offset %,
			end_object_instances_id %, end_object_field_id %, end_offset %, document_id %, revision_id %',lPreviousId, lUsrId, lOrd, coalesce(lMsg, ''), lFlags, lRootId, lMdate, lStartInstanceId, lStartFieldId, lStartOffset,
			lEndInstanceId, lEndFieldId, lEndOffset, lDocumentId, pRevisionId;
		*/	
		
		INSERT INTO comments_import(id, usr_id, ord, msg, flags, rootid, mdate, start_object_instances_id, start_object_field_id, start_offset,
			end_object_instances_id, end_object_field_id, end_offset, document_id, revision_id,
			subject, lastmoddate, is_resolved, resolve_uid, resolve_date, is_disclosed, undisclosed_usr_id)
		VALUES(lPreviousId, lUsrId, lOrd, coalesce(lMsg, ''), lFlags, lRootId, lMdate, lStartInstanceId, lStartFieldId, lStartOffset,
			lEndInstanceId, lEndFieldId, lEndOffset, lDocumentId, pRevisionId,
			'', now(), lIsResolved::boolean, lResolveUid, lResolveTime, lIsDisclosed::boolean, lUndisclosedUid);
	END LOOP;
	
	FOR lRecord IN 
		SELECT * FROM comments_import
	LOOP
		lMsgID := nextval('pwt.msg_id_seq');
		IF coalesce(lRecord.start_object_instances_id, 0) > 0 THEN
			lRootInstanceId = lRecord.start_object_instances_id;
		ELSE
			lRootInstanceId = lFirstInstanceId;
		END IF;
		
		INSERT INTO pwt.msg(id, usr_id, ord, msg, flags, rootid, mdate, start_object_instances_id, 
			start_object_field_id, start_offset, root_object_instance_id, 
			end_object_instances_id, end_object_field_id, end_offset, document_id, revision_id,
			subject, is_resolved, resolve_uid, resolve_date, is_disclosed, undisclosed_usr_id)
		VALUES(lMsgID, lRecord.usr_id, lRecord.ord, lRecord.msg, lRecord.flags, lMsgID, lRecord.mdate, 
			CASE WHEN coalesce(lRecord.start_object_instances_id, 0) > 0 THEN lRecord.start_object_instances_id ELSE NULL END, lRecord.start_object_field_id, lRecord.start_offset, lRootInstanceId,
			CASE WHEN coalesce(lRecord.end_object_instances_id, 0) > 0 THEN lRecord.end_object_instances_id ELSE NULL END, lRecord.end_object_field_id, lRecord.end_offset, lDocumentId, pRevisionId, 
			lRecord.subject, lRecord.is_resolved, CASE WHEN coalesce(lRecord.resolve_uid, 0) > 0 THEN lRecord.resolve_uid ELSE NULL END, lRecord.resolve_date, lRecord.is_disclosed, CASE WHEN coalesce(lRecord.undisclosed_usr_id, 0) > 0 THEN lRecord.undisclosed_usr_id ELSE NULL END);
					
		
		UPDATE comments_import SET
			new_id = lMsgID
		WHERE id = lRecord.id;
	END LOOP;
	
	-- Update the rootid and the replies
	UPDATE pwt.msg m SET
		rootid = i1.new_id
	FROM comments_import i 
	JOIN comments_import i1 ON i1.id = i.rootid
	WHERE i.new_id = m.id;
	
	UPDATE pwt.msg m SET
		replies = i1.count - 1
	FROM comments_import i 
	JOIN (	
		SELECT count(*) as count, rootid 
		FROM comments_import 
		GROUP BY rootid
	)as i1 ON i1.rootid = i.id
	WHERE m.id = m.rootid AND i.new_id = m.id;
	
	DROP TABLE comments_import;
	
	SET datestyle = default;
	
	
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pwt.spImportRevisionComments(
	pRevisionId bigint,
	pCommentsXml xml
) TO iusrpmt;
