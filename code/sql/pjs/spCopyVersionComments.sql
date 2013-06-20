DROP TYPE pjs.ret_spCopyVersionComments CASCADE;
CREATE TYPE pjs.ret_spCopyVersionComments AS (
	result int
);

CREATE OR REPLACE FUNCTION pjs.spCopyVersionComments(
	pVersionFromId bigint,
	pVersionToId bigint
)
  RETURNS pjs.ret_spCopyVersionComments AS
$BODY$
DECLARE
	lRes pjs.ret_spCopyVersionComments;
	lRecord record;	
BEGIN
	lRes.result = 1;
		
	
	INSERT INTO pjs.msg(
		document_id, subject, msg, senderip, mdate, rootid, ord, usr_id, flags, replies, 
		start_object_instances_id, end_object_instances_id, start_object_field_id, end_object_field_id, start_offset, end_offset, 
		version_id, original_id, is_resolved, resolve_uid, resolve_date, is_disclosed, undisclosed_usr_id
	) SELECT document_id, subject, msg, senderip, mdate, rootid, ord, usr_id, flags, replies, 
		start_object_instances_id, end_object_instances_id, start_object_field_id, end_object_field_id, start_offset, end_offset, 
		pVersionToId, original_id, is_resolved, resolve_uid, resolve_date, is_disclosed, undisclosed_usr_id
	FROM pjs.msg
	WHERE version_id = pVersionFromId;
	
	-- Update the rootids
	UPDATE pjs.msg m SET
		rootid = m1.id
	FROM (SELECT DISTINCT ON (rootid) rootid, id
		FROM pjs.msg
		WHERE version_id = pVersionToId		
		ORDER BY rootid ASC, ord ASC
	) m1
	WHERE m.version_id = pVersionToId AND m1.rootid = m.rootid;
	
	
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs.spCopyVersionComments(
	pVersionFromId bigint,
	pVersionToId bigint
) TO iusrpmt;
