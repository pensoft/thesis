DROP TYPE ret_spreguserstep1;
CREATE TYPE ret_spreguserstep1 AS (
	userid integer,
    email character varying
);
ALTER TYPE ret_spreguserstep1 OWNER TO postgres;
-- Function: spregusrstep1(integer, integer, character varying, character varying)

DROP FUNCTION IF EXISTS spregusrstep1(integer, integer, character varying, character varying, integer);

CREATE OR REPLACE FUNCTION spregusrstep1(pid integer, pop integer, pemail character varying, pupass character varying, poldpjsid integer)
  RETURNS ret_spreguserstep1 AS
$BODY$
DECLARE
	lUsrId int;
	lAutologHash varchar;
	lRes ret_spreguserstep1;
BEGIN

	IF (pOp = 1) THEN
		
		lAutologHash := md5(now() || pupass);
		
		IF (pId is null) THEN
			IF EXISTS (SELECT * FROM usr WHERE trim(lower(uname)) = trim(lower(pEmail)) AND state IN (1,0)) THEN
				RAISE EXCEPTION 'This user exists!';
			END IF;
			 --Insert za Step 1
				INSERT INTO usr(uname, upass, create_date, modify_date, state, utype, autolog_hash, oldpjs_cid, plain_upass)
				VALUES(trim(lower(pEmail)), md5(pUPass), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, -1, 1, lAutologHash, poldpjsid, pupass);
				lRes.userid = currval('usr_id_seq');
				lRes.email  = trim(lower(pEmail));
			RETURN lRes;
		ELSE
			
			IF EXISTS (SELECT * FROM usr WHERE trim(lower(uname)) = trim(lower(pEmail)) AND id <> pId) THEN
				RAISE EXCEPTION 'This user exists!';
			END IF;
			
			UPDATE usr SET 
				upass = md5(pUPass), 
				autolog_hash = lAutologHash, 
				uname = trim(lower(pEmail)),
				plain_upass = pUPass
			WHERE id = pId;
			lRes.userid = pId;
			lRes.email  = trim(lower(pEmail));
			RETURN lRes;
			
		END IF;
	ELSIF (pOp = 3) THEN
		--DELETE FROM usr WHERE id = pId;
	END IF;

END ;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spregusrstep1(integer, integer, character varying, character varying, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spregusrstep1(integer, integer, character varying, character varying, integer) TO postgres;
GRANT EXECUTE ON FUNCTION spregusrstep1(integer, integer, character varying, character varying, integer) TO iusrpmt;
GRANT EXECUTE ON FUNCTION spregusrstep1(integer, integer, character varying, character varying, integer) TO pensoft;
