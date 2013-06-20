CREATE OR REPLACE FUNCTION spSaveUserExpertises(
	pSEUid integer,
	pJournalId bigint,
	pSubjectCategories character varying,
	pTaxonCategories character varying,
	pGeographicalCategories character varying
)
  RETURNS int AS
$BODY$
DECLARE
	
BEGIN
	
	IF EXISTS(SELECT id FROM public.usr u WHERE id = pSEUid) THEN
		PERFORM spUpdateUserRoles( pJournalId, pSEUid, ARRAY[3]::int[]);
		PERFORM spSaveUsrJournalExpertises(pSEUid, pJournalId, pSubjectCategories, pTaxonCategories, pGeographicalCategories);
	END IF;
	
	RETURN 1;
END ;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spSaveUserExpertises(integer, bigint, character varying, character varying, character varying) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spSaveUserExpertises(integer, bigint, character varying, character varying, character varying) TO public;
GRANT EXECUTE ON FUNCTION spSaveUserExpertises(integer, bigint, character varying, character varying, character varying) TO postgres;
GRANT EXECUTE ON FUNCTION spSaveUserExpertises(integer, bigint, character varying, character varying, character varying) TO iusrpmt;
GRANT EXECUTE ON FUNCTION spSaveUserExpertises(integer, bigint, character varying, character varying, character varying) TO pensoft;
