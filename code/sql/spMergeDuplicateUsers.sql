CREATE OR REPLACE FUNCTION spMergeDuplicateUsers(
	pUID int,
	pDuplicateUid int
)
RETURNS int AS
$BODY$
	DECLARE
	BEGIN
		
 UPDATE pwt.pjs_revision_details 
 SET change_user_ids = array_pop(change_user_ids, pDuplicateUid) || pUid 
 WHERE pDuplicateUid = ANY(change_user_ids) ;

 UPDATE pwt.msg 
 SET resolve_uid = pUid 
 WHERE resolve_uid = pDuplicateUid;

 UPDATE pjs.msg 
 SET usr_id = pUid 
 WHERE usr_id = pDuplicateUid;

 UPDATE pjs.msg 
 SET resolve_uid = pUid 
 WHERE resolve_uid = pDuplicateUid;

 UPDATE public.activity2 
 SET usr_id = pUid 
 WHERE usr_id = pDuplicateUid;

 UPDATE public.cross_site_logins 
 SET uid = pUid 
 WHERE uid = pDuplicateUid;

 UPDATE public.cross_site_logins 
 SET old_pjs_uid = pUid 
 WHERE old_pjs_uid = pDuplicateUid;

 UPDATE pwt.activity 
 SET usr_id = pUid 
 WHERE usr_id = pDuplicateUid;

 UPDATE pwt.api_import_log 
 SET uid = pUid 
 WHERE uid = pDuplicateUid;

 UPDATE pwt.document_revisions 
 SET createuid = pUid 
 WHERE createuid = pDuplicateUid;

 UPDATE pwt.document_users 
 SET usr_id = pUid 
 WHERE usr_id = pDuplicateUid;

 UPDATE pwt.documents 
 SET lock_usr_id = pUid 
 WHERE lock_usr_id = pDuplicateUid;

 UPDATE pwt.documents 
 SET createuid = pUid 
 WHERE createuid = pDuplicateUid;

 UPDATE pwt.documents 
 SET lastmoduid = pUid 
 WHERE lastmoduid = pDuplicateUid;

 UPDATE pwt.documents 
 SET import_api_uid = pUid 
 WHERE import_api_uid = pDuplicateUid;

 UPDATE pwt.fields 
 SET createuid = pUid 
 WHERE createuid = pDuplicateUid;

 UPDATE pwt.fields 
 SET lastmoduid = pUid 
 WHERE lastmoduid = pDuplicateUid;

 UPDATE pwt.lock_history 
 SET usr_id = pUid 
 WHERE usr_id = pDuplicateUid;

 UPDATE pwt.media 
 SET usr_id = pUid 
 WHERE usr_id = pDuplicateUid;

 UPDATE pwt.msg 
 SET usr_id = pUid 
 WHERE usr_id = pDuplicateUid;

 UPDATE pwt.objects 
 SET createuid = pUid 
 WHERE createuid = pDuplicateUid;

 UPDATE pwt.objects 
 SET lastmoduid = pUid 
 WHERE lastmoduid = pDuplicateUid;

 UPDATE pwt.plates 
 SET usr_id = pUid 
 WHERE usr_id = pDuplicateUid;

 UPDATE pwt.tables 
 SET usr_id = pUid 
 WHERE usr_id = pDuplicateUid;

 UPDATE pwt.templates 
 SET createuid = pUid 
 WHERE createuid = pDuplicateUid;

 UPDATE pwt.templates 
 SET lastmoduid = pUid 
 WHERE lastmoduid = pDuplicateUid;

 UPDATE public.undisclosed_users 
 SET uid = pUid 
 WHERE uid = pDuplicateUid;

 UPDATE pjs.article_authors 
 SET author_uid = pUid 
 WHERE author_uid = pDuplicateUid;

 UPDATE pjs.document_user_invitations 
 SET uid = pUid 
 WHERE uid = pDuplicateUid;

 UPDATE pjs.document_users 
 SET uid = pUid 
 WHERE uid = pDuplicateUid;

 UPDATE pjs.document_versions 
 SET uid = pUid 
 WHERE uid = pDuplicateUid;

 UPDATE pjs.email_task_details 
 SET uid = pUid 
 WHERE uid = pDuplicateUid;

 UPDATE pjs.journal_users 
 SET uid = pUid 
 WHERE uid = pDuplicateUid;

 UPDATE pjs.journal_user_group_users 
 SET uid = pUid 
 WHERE uid = pDuplicateUid;

 UPDATE pjs.pwt_document_versions 
 SET change_user_ids = array_pop(change_user_ids, pDuplicateUid) || pUid 
 WHERE pDuplicateUid = ANY(change_user_ids);

 UPDATE pjs.pwt_documents 
 SET createuid = pUid 
 WHERE createuid = pDuplicateUid;

 UPDATE pjs.pwt_documents 
 SET lastmoduid = pUid 
 WHERE lastmoduid = pDuplicateUid;

 UPDATE pjs.pwt_documents_msg 
 SET usr_id = pUid 
 WHERE usr_id = pDuplicateUid;

 UPDATE pjs.user_roles 
 SET uid = pUid 
 WHERE uid = pDuplicateUid;
		
	RETURN 1;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
GRANT EXECUTE ON FUNCTION spMergeDuplicateUsers(
	pUID int,
	pDuplicateUid int
) TO iusrpmt;