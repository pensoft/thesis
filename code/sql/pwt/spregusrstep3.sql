-- Function: spregusrstep3(integer, character varying, integer, integer[], integer, character varying, character varying, character varying, character varying, integer[])

-- DROP FUNCTION spregusrstep3(integer, character varying, integer, integer[], integer, character varying, character varying, character varying, character varying, integer[]);

CREATE OR REPLACE FUNCTION spregusrstep3(
	pid integer, 
	pemail character varying, 
	pop integer, 
	pproducttypes integer[], 
	palertsfreq integer, 
	palerts_subject_cats character varying, 
	palerts_chronical_cats character varying, 
	palerts_taxon_cats character varying, 
	palerts_geographical_cats character varying, 
	pjournals integer[]
)
  RETURNS integer AS
$BODY$
DECLARE
	lResult int;
BEGIN

	IF (pOp = 1) THEN
		
		IF (pId is not null) THEN
			IF EXISTS (
				SELECT * 
				FROM usr u
				JOIN usr u1 ON trim(lower(u1.uname)) = trim(lower(u.uname))
				WHERE u.id = pId AND u1.id <> u.id
				AND u1.state >= 0
			) THEN
				RAISE EXCEPTION 'This user already exists!';
			END IF;
			
			--Insert za Step 3
			UPDATE usr SET 	journals = pjournals, 
					usr_alerts_frequency_id = palertsfreq, 
					product_types = pproducttypes, 
					subject_categories = string_to_array(palerts_subject_cats, ',')::int[], 
					taxon_categories = string_to_array(palerts_taxon_cats, ',')::int[], 
					chronological_categories = string_to_array(palerts_chronical_cats, ',')::int[], 
					geographical_categories = string_to_array(palerts_geographical_cats, ',')::int[],
					state = 1 --active
			WHERE id = pId;
			SELECT INTO lResult id FROM usr WHERE id = pId;
		ELSE
			
			IF EXISTS (SELECT * FROM usr WHERE trim(lower(uname)) = trim(lower(pemail)) AND state = 1) THEN
				RAISE EXCEPTION 'This user already exists!';
			END IF;
			
			
		END IF;
	END IF;
	IF (pOp = 2) THEN
		IF (pId is not null) THEN
			IF EXISTS (SELECT * FROM usr WHERE uname = pemail AND state = 1) THEN
				--RAISE EXCEPTION 'This user already exists!';
			END IF;
			
			--Insert za Step 3
			UPDATE usr SET 	journals = pjournals, 
					usr_alerts_frequency_id = palertsfreq, 
					product_types = pproducttypes, 
					subject_categories = string_to_array(palerts_subject_cats, ',')::int[], 
					taxon_categories = string_to_array(palerts_taxon_cats, ',')::int[], 
					chronological_categories = string_to_array(palerts_chronical_cats, ',')::int[], 
					geographical_categories = string_to_array(palerts_geographical_cats, ',')::int[]
					
			WHERE id = pId;
			SELECT INTO lResult id FROM usr WHERE id = pId;
		ELSE
			
			IF EXISTS (SELECT * FROM usr WHERE uname = pemail AND state = 1) THEN
				RAISE EXCEPTION 'This user already exists!';
			END IF;
			
			
		END IF;
	END IF;
	RETURN lResult;

END ;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spregusrstep3(integer, character varying, integer, integer[], integer, character varying, character varying, character varying, character varying, integer[]) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spregusrstep3(integer, character varying, integer, integer[], integer, character varying, character varying, character varying, character varying, integer[]) TO public;
GRANT EXECUTE ON FUNCTION spregusrstep3(integer, character varying, integer, integer[], integer, character varying, character varying, character varying, character varying, integer[]) TO postgres;
GRANT EXECUTE ON FUNCTION spregusrstep3(integer, character varying, integer, integer[], integer, character varying, character varying, character varying, character varying, integer[]) TO iusrpmt;
