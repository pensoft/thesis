-- Function: spSaveUsrExpertise(integer, character varying, character varying, character varying, character varying)

-- DROP FUNCTION spSaveUsrExpertise(integer, character varying, character varying, character varying, character varying);

CREATE OR REPLACE FUNCTION spSaveUsrExpertise(
	pid integer, 
	palerts_subject_cats character varying, 
	palerts_chronical_cats character varying, 
	palerts_taxon_cats character varying, 
	palerts_geographical_cats character varying
)
  RETURNS integer AS
$BODY$
DECLARE
	lResult int;
BEGIN
	
	UPDATE usr SET
			expertise_subject_categories = string_to_array(palerts_subject_cats, ',')::int[], 
			expertise_taxon_categories = string_to_array(palerts_taxon_cats, ',')::int[], 
			expertise_chronological_categories = string_to_array(palerts_chronical_cats, ',')::int[], 
			expertise_geographical_categories = string_to_array(palerts_geographical_cats, ',')::int[]
	WHERE id = pId;
			
	RETURN pId;

END ;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spSaveUsrExpertise(integer, character varying, character varying, character varying, character varying) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spSaveUsrExpertise(integer, character varying, character varying, character varying, character varying) TO public;
GRANT EXECUTE ON FUNCTION spSaveUsrExpertise(integer, character varying, character varying, character varying, character varying) TO postgres;
GRANT EXECUTE ON FUNCTION spSaveUsrExpertise(integer, character varying, character varying, character varying, character varying) TO iusrpmt;
