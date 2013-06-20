-- FUNCTION THAT RETURNS UNIQUE VALUES FROM ARRAY
DROP FUNCTION IF EXISTS array_sort_unique();

CREATE OR REPLACE FUNCTION array_sort_unique (ANYARRAY) RETURNS ANYARRAY
LANGUAGE SQL
AS $body$
  SELECT ARRAY(
    SELECT DISTINCT $1[s.i]
    FROM generate_series(array_lower($1,1), array_upper($1,1)) AS s(i)
    ORDER BY 1
  );
$body$;

DROP FUNCTION IF EXISTS expertise_taxon();

CREATE OR REPLACE FUNCTION expertise_taxon()
 RETURNS int AS
$BODY$
declare
	i int;
	j int;
	lResult RECORD;
BEGIN
	FOR lResult IN
		SELECT 	
			author_id,
			reg_co_authors,
			array_sort_unique(regexp_split_to_array(regexp_replace(trim(both '|' from reg_co_authors), '\|+', '|', 'g'), E'\\D'))::int[] as reg,
			(CASE WHEN (taxon <> '' AND trim(both '|' from taxon) <> '') THEN array_sort_unique(regexp_split_to_array(regexp_replace(trim(both '|' from taxon), '\|+', '|', 'g'), E'\\D')) ELSE null END)::int[] as expertise_taxon,
			taxon
		FROM 
		j_article_temp
		WHERE author_id <> 0 AND taxon <> '||' AND taxon <> '' AND status <> 'archived' AND status <> 'not finished'
		order by author_id
		--limit 200
	
	LOOP
		FOR i IN 1 .. array_upper(lResult.reg, 1)
		LOOP 
			IF (array_length(lResult.expertise_taxon, 1) > 0) THEN 
				FOR j IN 1 .. array_upper(lResult.expertise_taxon, 1)
				LOOP 
					--raise notice 'User:%',  lResult.reg[i];
					--raise notice 'Taxon:%', lResult.expertise_taxon[j];
					
					IF NOT EXISTS (
						SELECT * FROM usr
						WHERE lResult.expertise_taxon[j] = ANY(expertise_taxon_categories::int[])
						AND oldpjs_cid = lResult.reg[i]
					) THEN
							UPDATE usr
							SET expertise_taxon_categories = array_append(expertise_taxon_categories, lResult.expertise_taxon[j])
							WHERE oldpjs_cid = lResult.reg[i];
						--raise notice '%', lResult.reg[i];   -- single quotes!
					END IF;
				END LOOP;
			END IF;
		END LOOP;
	END LOOP;
	RETURN 1;
	
END ;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;


DROP FUNCTION IF EXISTS expertise_geographical();

CREATE OR REPLACE FUNCTION expertise_geographical()
 RETURNS int AS
$BODY$
declare
	i int;
	j int;
	lResult RECORD;
BEGIN
	FOR lResult IN
		SELECT 	
			author_id,
			reg_co_authors,
			array_sort_unique(regexp_split_to_array(regexp_replace(trim(both '|' from reg_co_authors), '\|+', '|', 'g'), E'\\D'))::int[] as reg,
			(CASE WHEN (geo_spatial <> '' AND trim(both '|' from geo_spatial) <> '') THEN array_sort_unique(regexp_split_to_array(regexp_replace(trim(both '|' from geo_spatial), '\|+', '|', 'g'), E'\\D')) ELSE null END)::int[] as expertise_geographical_categories,
			taxon
		FROM 
		j_article_temp
		WHERE author_id <> 0 AND geo_spatial <> '||' AND geo_spatial <> '' AND status <> 'archived' AND status <> 'not finished' 
		order by author_id
		--limit 200
	
	LOOP
		FOR i IN 1 .. array_upper(lResult.reg, 1)
		LOOP 
			IF (array_length(lResult.expertise_geographical_categories, 1) > 0) THEN 
				FOR j IN 1 .. array_upper(lResult.expertise_geographical_categories, 1)
				LOOP 
					IF NOT EXISTS (
						SELECT * FROM usr
						WHERE lResult.expertise_geographical_categories[j] = ANY(expertise_geographical_categories::int[])
						AND oldpjs_cid = lResult.reg[i]
					) THEN
							UPDATE usr
							SET expertise_geographical_categories = array_append(expertise_geographical_categories, lResult.expertise_geographical_categories[j])
							WHERE oldpjs_cid = lResult.reg[i];
						 	--raise notice '%', lResult.reg[i];   -- single quotes!
					END IF;
				END LOOP;
			END IF;
		END LOOP;
	END LOOP;
	RETURN 1;
	
END ;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;


DROP FUNCTION IF EXISTS expertise_chronological();

CREATE OR REPLACE FUNCTION expertise_chronological()
 RETURNS int AS
$BODY$
declare
	i int;
	j int;
	lResult RECORD;
BEGIN
	FOR lResult IN
		SELECT 	
			author_id,
			reg_co_authors,
			array_sort_unique(regexp_split_to_array(regexp_replace(trim(both '|' from reg_co_authors), '\|+', '|', 'g'), E'\\D'))::int[] as reg,
			(CASE WHEN (chronological <> '' AND trim(both '|' from chronological) <> '') THEN array_sort_unique(regexp_split_to_array(regexp_replace(trim(both '|' from chronological), '\|+', '|', 'g'), E'\\D')) ELSE null END)::int[] as expertise_chronological_categories
		FROM 
		j_article_temp
		WHERE author_id <> 0 AND chronological <> '||' AND chronological <> '' AND status <> 'archived' AND status <> 'not finished'
		order by author_id
		--limit 200
	
	LOOP
		FOR i IN 1 .. array_upper(lResult.reg, 1)
		LOOP 
			IF (array_length(lResult.expertise_chronological_categories, 1) > 0) THEN 
				FOR j IN 1 .. array_upper(lResult.expertise_chronological_categories, 1)
				LOOP 
					IF NOT EXISTS (
						SELECT * FROM usr
						WHERE lResult.expertise_chronological_categories[j] = ANY(expertise_chronological_categories::int[])
						AND oldpjs_cid = lResult.reg[i]
					) THEN
							UPDATE usr
							SET expertise_chronological_categories = array_append(expertise_chronological_categories, lResult.expertise_chronological_categories[j])
							WHERE oldpjs_cid = lResult.reg[i];
					END IF;
				END LOOP;
			END IF;
		END LOOP;
	END LOOP;
	RETURN 1;
	
END ;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;


DROP FUNCTION IF EXISTS expertise_subject();

CREATE OR REPLACE FUNCTION expertise_subject()
 RETURNS int AS
$BODY$
declare
	i int;
	j int;
	lResult RECORD;
BEGIN
	FOR lResult IN
		SELECT 	
			author_id,
			reg_co_authors,
			array_sort_unique(regexp_split_to_array(regexp_replace(trim(both '|' from reg_co_authors), '\|+', '|', 'g'), E'\\D'))::int[] as reg,
			(CASE WHEN (subject <> '' AND trim(both '|' from subject) <> '') THEN array_sort_unique(regexp_split_to_array(regexp_replace(trim(both '|' from subject), '\|+', '|', 'g'), E'\\D')) ELSE null END)::int[] as expertise_subject_categories
		FROM 
		j_article_temp
		WHERE author_id <> 0 AND subject <> '||' AND subject <> '' AND status <> 'archived' AND status <> 'not finished'
		order by author_id
		--limit 200
	
	LOOP
		FOR i IN 1 .. array_upper(lResult.reg, 1)
		LOOP 
			IF (array_length(lResult.expertise_subject_categories, 1) > 0) THEN 
				FOR j IN 1 .. array_upper(lResult.expertise_subject_categories, 1)
				LOOP 
					IF NOT EXISTS (
						SELECT * FROM usr
						WHERE lResult.expertise_subject_categories[j] = ANY(expertise_subject_categories::int[])
						AND oldpjs_cid = lResult.reg[i]
					) THEN
							UPDATE usr
							SET expertise_subject_categories = array_append(expertise_subject_categories, lResult.expertise_subject_categories[j])
							WHERE oldpjs_cid = lResult.reg[i];
					END IF;
				END LOOP;
			END IF;
		END LOOP;
	END LOOP;
	RETURN 1;
	
END ;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;



DROP FUNCTION IF EXISTS document_users_expertises();

CREATE OR REPLACE FUNCTION document_users_expertises()
 RETURNS int AS
$BODY$
declare
	i int;
	j int;
	lResult RECORD;
BEGIN
	FOR lResult IN
		SELECT 
				jp.userid,
				(CASE WHEN (ja.subject <> '' AND trim(both '|' from ja.subject) <> '') THEN array_sort_unique(regexp_split_to_array(regexp_replace(trim(both '|' from ja.subject), '\|+', '|', 'g'), E'\\D')) ELSE null END)::int[] as expertise_subject_categories,
				(CASE WHEN (ja.chronological <> '' AND trim(both '|' from ja.chronological) <> '') THEN array_sort_unique(regexp_split_to_array(regexp_replace(trim(both '|' from ja.chronological), '\|+', '|', 'g'), E'\\D')) ELSE null END)::int[] as expertise_chronological_categories,
				(CASE WHEN (ja.taxon <> '' AND trim(both '|' from ja.taxon) <> '') THEN array_sort_unique(regexp_split_to_array(regexp_replace(trim(both '|' from ja.taxon), '\|+', '|', 'g'), E'\\D')) ELSE null END)::int[] as expertise_taxon_categories,
				(CASE WHEN (ja.geo_spatial <> '' AND trim(both '|' from ja.geo_spatial) <> '') THEN array_sort_unique(regexp_split_to_array(regexp_replace(trim(both '|' from ja.geo_spatial), '\|+', '|', 'g'), E'\\D')) ELSE null END)::int[] as expertise_geographical_categories
		FROM j_process_temp jp
		JOIN j_article_temp ja ON ja.article_id = jp.articleid
		WHERE jp.userrole <> 'layout' AND jp.accept = '1' AND ja.status <> 'archived' AND ja.status <> 'not finished'
		ORDER BY jp.userid
		--limit 200
	
	LOOP	
		-- INSERT expertise_subject_categories
		IF (array_length(lResult.expertise_subject_categories, 1) > 0) THEN 
			FOR j IN 1 .. array_upper(lResult.expertise_subject_categories, 1)
			LOOP 
				-- CHECK IF ID ALREADY EXISTS
				IF NOT EXISTS (
					SELECT * FROM usr
					WHERE lResult.expertise_subject_categories[j] = ANY(expertise_subject_categories::int[])
					AND oldpjs_cid = lResult.userid
				) THEN
						UPDATE usr
						SET expertise_subject_categories = array_append(expertise_subject_categories, lResult.expertise_subject_categories[j])
						WHERE oldpjs_cid = lResult.userid;
				END IF;
			END LOOP;
		END IF;

		-- INSERT expertise_chronological_categories
		IF (array_length(lResult.expertise_chronological_categories, 1) > 0) THEN 
			FOR j IN 1 .. array_upper(lResult.expertise_chronological_categories, 1)
			LOOP 
				IF NOT EXISTS (
					SELECT * FROM usr
					WHERE lResult.expertise_chronological_categories[j] = ANY(expertise_chronological_categories::int[])
					AND oldpjs_cid = lResult.userid
				) THEN
						UPDATE usr
						SET expertise_chronological_categories = array_append(expertise_chronological_categories, lResult.expertise_chronological_categories[j])
						WHERE oldpjs_cid = lResult.userid;
				END IF;
			END LOOP;
		END IF;

		-- INSERT expertise_taxon_categories
		IF (array_length(lResult.expertise_taxon_categories, 1) > 0) THEN 
			FOR j IN 1 .. array_upper(lResult.expertise_taxon_categories, 1)
			LOOP 
				IF NOT EXISTS (
					SELECT * FROM usr
					WHERE lResult.expertise_taxon_categories[j] = ANY(expertise_taxon_categories::int[])
					AND oldpjs_cid = lResult.userid
				) THEN
						UPDATE usr
						SET expertise_taxon_categories = array_append(expertise_taxon_categories, lResult.expertise_taxon_categories[j])
						WHERE oldpjs_cid = lResult.userid;
				END IF;
			END LOOP;
		END IF;

		-- INSERT expertise_geographical_categories
		IF (array_length(lResult.expertise_geographical_categories, 1) > 0) THEN 
			FOR j IN 1 .. array_upper(lResult.expertise_geographical_categories, 1)
			LOOP 
				IF NOT EXISTS (
					SELECT * FROM usr
					WHERE lResult.expertise_geographical_categories[j] = ANY(expertise_geographical_categories::int[])
					AND oldpjs_cid = lResult.userid
				) THEN
						UPDATE usr
						SET expertise_geographical_categories = array_append(expertise_geographical_categories, lResult.expertise_geographical_categories[j])
						WHERE oldpjs_cid = lResult.userid;
				END IF;
			END LOOP;
		END IF;
	END LOOP;
	RETURN 1;
	
END ;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

DROP FUNCTION IF EXISTS spInsertOldPJSUsers(int);

CREATE OR REPLACE FUNCTION spInsertOldPJSUsers(pDeleteFlag int)
 RETURNS int AS
$BODY$
DECLARE
	lResult RECORD;
	lUsrId int;
BEGIN
	
	IF pDeleteFlag = 1 THEN -- DELETE OLD PJS USERS
		--TRUNCATE usr_addresses;
		--DELETE FROM usr WHERE id > 727;
		--ALTER SEQUENCE usr_id_seq RESTART WITH 728;
	END IF;

	
	FOR lResult IN
		SELECT DISTINCT ON (ct.cid)
			ct.cid as oldpjs_cid,
			ct.email as uname,
			--ct.salut as usr_title_id,
			ut.id as usr_title_id,
			ct.ime as first_name,
			ct.prezime as middle_name,
			ct.familia as last_name,
			ct.firma as affiliation,
			ct.depart as departament,
			ct.address as addr_street,
			ct.city as addr_city,
			--ct.country as country_id,
			c.id as country_id,
			ct.www as website,
			ct.phone as phone,
			ct.fax as fax,
			ct.dn as vat,
			--ct.ctip as client_type_id,
			cty.id as client_type_id,
			ct.ab1 as product_types_books,
			ct.ab2 as product_types_e_books,
			ct.ab3 as product_types_journals,
			--ct.emnot as usr_alerts_frequency_id,
			ua.id as usr_alerts_frequency_id,
			ct.rewrint as expertize,
			md5(ct.pass) as upass,
			--ct.active as state,
			(CASE WHEN ct.active = 'Active' THEN 1  ELSE 0 END ) as state,
			ct.mdate as modify_date,
			ct.img as photo_id,
			ct.d_zip as zip,
			--ct.d_country as d_country,
			c1.id as d_country,
			ct.d_city as d_city,
			ct.d_ime as name,
			ct.d_firma as firm,
			ct.d_address as address,
			md5(random() || ct.pass) as autolog_hash,
			--ea.journals as journals,
			--ea.srids as subject_categories2,
			--ea.trids as taxon_categories,
			--ea.crids as chronological_categories,
			--ea.rids as geographical_categories,
			(CASE WHEN 
						(length(ea.rids) > 1 AND length(ea.rids) <> 2)  
			--THEN array_sort_unique(regexp_split_to_array(substring(substr(regexp_replace(ea.rids, '\|+', '|', 'g'), 2 , length(regexp_replace(ea.rids, '\|+', '|', 'g'))) from 1 for length(regexp_replace(ea.rids, '\|+', '|', 'g'))-2), E'\\D'))::int[]
			THEN array_sort_unique(regexp_split_to_array(regexp_replace(trim(both '|' from ea.rids), '\|+', '|', 'g'), E'\\D'))::int[]
			ELSE NULL 
			END) as subject_categories,
			(CASE WHEN 
						(length(ea.trids) > 1 AND length(ea.trids) <> 2)  
			--THEN array_sort_unique(regexp_split_to_array(substring(substr(regexp_replace(ea.trids, '\|+', '|', 'g'), 2 , length(regexp_replace(ea.trids, '\|+', '|', 'g'))) from 1 for length(regexp_replace(ea.trids, '\|+', '|', 'g'))-2), E'\\D'))::int[]
			THEN array_sort_unique(regexp_split_to_array(regexp_replace(trim(both '|' from ea.trids), '\|+', '|', 'g'), E'\\D'))::int[]
			ELSE NULL 
			END) as taxon_categories,
			(CASE WHEN 
						(length(ea.srids) > 1 AND length(ea.srids) <> 2)  
			--THEN array_sort_unique(regexp_split_to_array(substring(substr(regexp_replace(ea.srids, '\|+', '|', 'g'), 2 , length(regexp_replace(ea.srids, '\|+', '|', 'g'))) from 1 for length(regexp_replace(ea.srids, '\|+', '|', 'g'))-2), E'\\D'))::int[]
			THEN array_sort_unique(regexp_split_to_array(regexp_replace(trim(both '|' from ea.srids), '\|+', '|', 'g'), E'\\D'))::int[]
			ELSE NULL 
			END) as geographical_categories,
			(CASE WHEN 
						(length(ea.journals) > 1 AND length(ea.journals) <> 2)  
			--THEN array_sort_unique(regexp_split_to_array(substring(substr(regexp_replace(ea.journals, '\|+', '|', 'g'), 2 , length(regexp_replace(ea.journals, '\|+', '|', 'g'))) from 1 for length(regexp_replace(ea.journals, '\|+', '|', 'g'))-2), E'\\D'))::int[]
			THEN array_sort_unique(regexp_split_to_array(regexp_replace(trim(both '|' from ea.journals), '\|+', '|', 'g'), E'\\D'))::int[]
			ELSE NULL 
			END) as journals,
			(CASE WHEN 
						(length(ea.crids) > 1 AND length(ea.crids) <> 2)  
			--THEN array_sort_unique(regexp_split_to_array(substring(substr(regexp_replace(ea.crids, '\|+', '|', 'g'), 2 , length(regexp_replace(ea.crids, '\|+', '|', 'g'))) from 1 for length(regexp_replace(ea.crids, '\|+', '|', 'g'))-2), E'\\D'))::int[]
			THEN array_sort_unique(regexp_split_to_array(regexp_replace(trim(both '|' from ea.crids), '\|+', '|', 'g'), E'\\D'))::int[]
			ELSE NULL 
			END) as chronological_categories
			--ja.taxon as expertise_taxon_categories,
			--ja.geo_spatial as expertise_geographical_categories,
			--ja.chronological as expertise_chronological_categories,
			--ja.subject as expertise_subject_categories
		FROM clients_temp ct
		LEFT JOIN e_alerts_temp ea ON trim(lower(ea.email)) = trim(lower(ct.email))
		--LEFT JOIN j_process_temp jp ON jp.userid = ct.cid AND jp.userrole <> 'layout'
		--LEFT JOIN j_article_temp ja ON ja.article_id = jp.articleid
		LEFT JOIN countries c ON trim(lower(c.name)) = trim(lower(ct.country))
		LEFT JOIN countries c1 ON trim(lower(c.name)) = trim(lower(ct.d_country))
		LEFT JOIN usr_titles ut ON trim(lower(ut.name)) = trim(lower(ct.salut))
		LEFT JOIN client_types cty ON trim(lower(cty.name)) = trim(lower(ct.ctip))
		LEFT JOIN usr_alerts_frequency ua ON trim(lower(ua.name)) = trim(lower(ct.emnot))
		--where ct.cid = 121991
		ORDER BY oldpjs_cid
		
	LOOP
		-- INSERT USR DATA IF NOT EXISTS
		IF NOT EXISTS (
			SELECT u.oldpjs_cid
			FROM usr u
			WHERE u.oldpjs_cid = lResult.oldpjs_cid
		) THEN
			
			INSERT INTO usr(
					--id, 
					uname, 
					upass, 
					first_name, 
					middle_name, 
					last_name, 
					usr_title_id, 
					client_type_id, 
					affiliation, 
					departament, 
					addr_street, 
					addr_city, 
					country_id, 
					phone, 
					fax, 
					vat, 
					website, 
					state, 
					utype, 
					--photo_id, 
					journals, 
					usr_alerts_frequency_id, 
					--product_types, 
					subject_categories, 
					taxon_categories, 
					chronological_categories, 
					geographical_categories, 
					--confhash, 
					create_date, 
					--activate_date, 
					modify_date, 
					--access_date, 
					--reg_ip, 
					--activate_ip, 
					--access_ip, 
					autolog_hash, 
					--expertise_subject_categories, 
					--expertise_chronological_categories, 
					--expertise_taxon_categories, 
					--expertise_geographical_categories, 
					oldpjs_cid)
			VALUES (
					trim(lower(lResult.uname)),
					lResult.upass, 
					lResult.first_name, 
					lResult.middle_name, 
					lResult.last_name, 
					lResult.usr_title_id,
					lResult.client_type_id,
					lResult.affiliation, 
					lResult.departament,
					lResult.addr_street,
					lResult.addr_city,
					lResult.country_id, 
					lResult.phone, 
					lResult.fax, 
					lResult.vat, 
					lResult.website, 
					lResult.state, 
					1, 
					--lResult.photo_id,
					lResult.journals, 
					lResult.usr_alerts_frequency_id, 
					--?, 
					lResult.subject_categories, 
					lResult.taxon_categories, 
					lResult.chronological_categories, 
					lResult.geographical_categories, 
					--?, 
					CURRENT_TIMESTAMP, 
					--?,
					CURRENT_TIMESTAMP,
					--lResult.modify_date::timestamp without time zone,
					--?, 
					--?, 
					--?, 
					--?, 
					lResult.autolog_hash,
					--?, 
					--?, 
					--?, 
					--?, 
					--?, 
					--?, 
					lResult.oldpjs_cid
					);
			
			
			lUsrId := currval('usr_id_seq');
			RAISE NOTICE 'INSERT lUsrId: %', lUsrId;
			
			-- INSERT ADDRESSES
			INSERT INTO usr_addresses(
				uid, "name", firm, address, city, zip, country_id)
			VALUES (lUsrId, lResult.name, lResult.firm, lResult.address, lResult.d_city, lResult.zip, 1);
			
		ELSE -- UPDATE
			
			UPDATE usr SET
				uname = trim(lower(lResult.uname)),
				upass = lResult.upass,
				first_name = lResult.first_name,
				middle_name = lResult.middle_name,
				last_name = lResult.last_name,
				usr_title_id = lResult.usr_title_id,
				client_type_id = lResult.client_type_id,
				affiliation = lResult.affiliation,
				departament = lResult.departament,
				addr_street = lResult.addr_street,
				addr_city = lResult.addr_city,
				country_id = lResult.country_id,
				phone = lResult.phone,
				fax = lResult.fax,
				vat = lResult.vat,
				website = lResult.website, 
				state = lResult.state,
				--utype = lResult.utype,
				--photo_id = lResult.photo_id,
				journals = lResult.journals,
				usr_alerts_frequency_id = lResult.usr_alerts_frequency_id,
				--product_types = ?,
				subject_categories = lResult.subject_categories,
				taxon_categories = lResult.taxon_categories,
				chronological_categories = lResult.chronological_categories,
				geographical_categories = lResult.geographical_categories,
				--confhash = ?, 
				--create_date = ?, 
				--activate_date = ?, 
				modify_date = CURRENT_TIMESTAMP, 
				--access_date = ?, 
				--reg_ip = ?,
				--activate_ip = ?, 
				--access_ip = ?,
				autolog_hash = lResult.autolog_hash
				--expertise_subject_categories = ?,
				--expertise_chronological_categories = ?,
				--expertise_taxon_categories = ?,
				--expertise_geographical_categories = ?
		
			WHERE oldpjs_cid = lResult.oldpjs_cid;
			
			
			UPDATE usr_addresses AS ua -- UPDATE ADDRESSES
			SET 
				name = lResult.name,
				firm = lResult.firm,
				address = lResult.address,
				city = lResult.d_city,
				zip = lResult.zip,
				country_id = lResult.d_country
			FROM usr AS u
			WHERE ua.uid = u.id AND u.oldpjs_cid = lResult.oldpjs_cid;
			
			
			RAISE NOTICE 'UPDATE lResult.oldpjs_cid: %', lResult.oldpjs_cid;
		END IF;
		
	END LOOP;
		
	-- EXPERTISES
	--PERFORM expertise_taxon();
	--PERFORM expertise_geographical();
	--PERFORM expertise_chronological();
	--PERFORM expertise_subject();
	--PERFORM document_users_expertises();
			
	RETURN 1;
	
END ;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spInsertOldPJSUsers(int) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spInsertOldPJSUsers(int) TO postgres;
GRANT EXECUTE ON FUNCTION spInsertOldPJSUsers(int) TO iusrpmt;


DROP FUNCTION IF EXISTS spPerformExpertises();

CREATE OR REPLACE FUNCTION spPerformExpertises()
 RETURNS int AS
$BODY$
DECLARE
	--lResult RECORD;
	--lUsrId int;
BEGIN
	-- EXPERTISES
	PERFORM expertise_taxon();
	PERFORM expertise_geographical();
	PERFORM expertise_chronological();
	PERFORM expertise_subject();
	PERFORM document_users_expertises();
			
	RETURN 1;
	
END ;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spPerformExpertises() OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spPerformExpertises() TO postgres;
GRANT EXECUTE ON FUNCTION spPerformExpertises() TO iusrpmt;