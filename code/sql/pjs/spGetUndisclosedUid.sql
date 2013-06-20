DROP TYPE IF EXISTS ret_spGetUndisclosedUid CASCADE;
CREATE TYPE ret_spGetUndisclosedUid AS (
	id int
);

CREATE OR REPLACE FUNCTION pjs.spGetUndisclosedUid(
	pDocumentId bigint,
	pRoleId bigint,
	pUid int
)
	RETURNS ret_spGetUndisclosedUid AS
$BODY$
	DECLARE
		lRes ret_spGetUndisclosedUid;
		lPwtDocumentId bigint;
		lRoleName varchar;
		lDocumentRoleUndisclosedUsersCount int;
	BEGIN		
		SELECT INTO lRes.id
			id
		FROM public.undisclosed_users
		WHERE pjs_document_id = pDocumentId AND pjs_user_role_id = pRoleId AND uid = pUid;
		
		IF lRes.id IS NULL THEN
			SELECT INTO lPwtDocumentId
				pwt_id
			FROM pjs.pwt_documents
			WHERE document_id = pDocumentId;
			
			SELECT INTO lRoleName
				name
			FROM pjs.user_role_types
			WHERE id = pRoleId;
			
			SELECT INTO lDocumentRoleUndisclosedUsersCount
				count(*)
			FROM public.undisclosed_users
			WHERE pjs_user_role_id = pRoleId AND pjs_document_id = pDocumentId;
			
			INSERT INTO public.undisclosed_users(pwt_document_id, pjs_document_id, pjs_user_role_id, uid, name)
			VALUES (lPwtDocumentId, pDocumentId, pRoleId, pUid, lRoleName || ' ' || (lDocumentRoleUndisclosedUsersCount + 1)::varchar);
			
			lRes.id = currval('usr_id_seq'::regclass);
		END IF;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs.spGetUndisclosedUid(
	pDocumentId bigint,
	pRoleId bigint,
	pUid int
) TO iusrpmt;
