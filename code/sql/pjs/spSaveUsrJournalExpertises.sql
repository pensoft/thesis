-- Function: spSaveUsrJournalExpertises(integer, bigint, character varying, character varying, character varying)

DROP FUNCTION spSaveUsrJournalExpertises(integer, bigint, character varying, character varying, character varying);

CREATE OR REPLACE FUNCTION spSaveUsrJournalExpertises(
	pUid integer,
	pJournalId bigint,
	palerts_subject_cats character varying, 
	palerts_taxon_cats character varying,
	palerts_geographical_cats character varying
)
  RETURNS integer AS
$BODY$
DECLARE
	lResult int;
	lJournalUsrId int;
BEGIN
	SELECT INTO lJournalUsrId id 
	FROM pjs.journal_users 
	WHERE uid = pUid 
		AND journal_id = pJournalId
		AND role_id = 3; -- SE_ROLE
	
	IF EXISTS ( SELECT journal_usr_id FROM pjs.journal_users_expertises WHERE journal_usr_id = lJournalUsrId ) THEN
		UPDATE pjs.journal_users_expertises SET
			subject_categories = string_to_array(palerts_subject_cats, ',')::int[], 
			taxon_categories = string_to_array(palerts_taxon_cats, ',')::int[],
			geographical_categories = string_to_array(palerts_geographical_cats, ',')::int[]
		WHERE journal_usr_id = lJournalUsrId;
	ELSE
		INSERT INTO pjs.journal_users_expertises(journal_usr_id, subject_categories, taxon_categories, geographical_categories)
				VALUES( lJournalUsrId, string_to_array(palerts_subject_cats, ',')::int[], string_to_array(palerts_taxon_cats, ',')::int[], string_to_array(palerts_geographical_cats, ',')::int[] );
	END IF;
			
	RETURN 1;

END ;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spSaveUsrJournalExpertises(integer, bigint, character varying, character varying, character varying) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spSaveUsrJournalExpertises(integer, bigint, character varying, character varying, character varying) TO public;
GRANT EXECUTE ON FUNCTION spSaveUsrJournalExpertises(integer, bigint, character varying, character varying, character varying) TO postgres;
GRANT EXECUTE ON FUNCTION spSaveUsrJournalExpertises(integer, bigint, character varying, character varying, character varying) TO iusrpmt;
