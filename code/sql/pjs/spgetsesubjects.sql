
--
-- Name: spgetsesubjects(integer); Type: FUNCTION; Schema: pjs; Owner: pensoft
--

CREATE FUNCTION spgetsesubjects(pjournal_id integer) RETURNS SETOF pjs.ret_spuserexpertises
    LANGUAGE plpgsql SECURITY DEFINER
    AS $$
	DECLARE
		Res pjs.ret_spUserExpertises;
		Record record;
		Record2 record;
BEGIN
 FOR Record IN 

	SELECT subjects.pos, ju.uid
	FROM pjs.journal_users_expertises jue
	JOIN pjs.journal_users ju ON jue.journal_usr_id = ju.id
	JOIN public.subject_categories subjects ON subjects .id = ANY(jue.subject_categories)
	WHERE ju.journal_id = pJournal_id
	--AND ju.role_id = 3 --SE_ROLE --right now only SE's have journal expertises	

	LOOP 
		FOR Record2 IN
			SELECT id FROM subject_categories 
			WHERE Record.pos like pos || '%'
		LOOP
			Res.category_id = Record2.id;
			Res.uid = Record.uid;
			RETURN NEXT Res;
		END LOOP;
	END LOOP;
	RETURN;
END
$$;


ALTER FUNCTION spgetsesubjects(pjournal_id integer) OWNER TO pensoft;

--
-- Name: spgetsetaxons(integer); Type: FUNCTION; Schema: pjs; Owner: pensoft
--

CREATE FUNCTION spgetsetaxons(pjournal_id integer) RETURNS SETOF pjs.ret_spuserexpertises
    LANGUAGE plpgsql SECURITY DEFINER
    AS $$
	DECLARE
		Res pjs.ret_spUserExpertises;
		Record record;
		Record2 record;
BEGIN
 FOR Record IN 
 
	SELECT taxa.pos, ju.uid
	FROM pjs.journal_users_expertises jue
	JOIN pjs.journal_users ju ON jue.journal_usr_id = ju.id
	JOIN public.taxon_categories taxa ON taxa.id = ANY(jue.taxon_categories)
	WHERE ju.journal_id = pJournal_id
	--AND ju.role_id = 3 --SE_ROLE --right now only SE's have journal expertises
	
	LOOP 
		FOR Record2 IN
			SELECT id FROM taxon_categories 
			WHERE Record.pos like pos || '%'
		LOOP
			Res.category_id = Record2.id;
			Res.uid = Record.uid;
			RETURN NEXT Res;
		END LOOP;
	END LOOP;
	RETURN;
END
$$;


ALTER FUNCTION spgetsetaxons(pjournal_id integer) OWNER TO pensoft;

--
-- Name: spgetusubjects(integer); Type: FUNCTION; Schema: pjs; Owner: pensoft
--

CREATE FUNCTION spgetusubjects(pjournal_id integer) RETURNS SETOF pjs.ret_spuserexpertises
    LANGUAGE plpgsql SECURITY DEFINER
    AS $$
	DECLARE
		Res pjs.ret_spUserExpertises;
		Record record;
		Record2 record;
BEGIN
 FOR Record IN 

	SELECT subjects.pos, u.id
	FROM public.usr u
	JOIN public.subject_categories subjects ON subjects.id = ANY(u.expertise_subject_categories)
	--JOIN pjs.journal_users ju ON u.id = ju.uid
	--WHERE ju.journal_id = pJournal_id

	LOOP 
		FOR Record2 IN
			SELECT id FROM subject_categories 
			WHERE Record.pos like pos || '%'
		LOOP
			Res.category_id = Record2.id;
			Res.uid = Record.id;
			RETURN NEXT Res;
		END LOOP;
	END LOOP;
	RETURN;
END
$$;


ALTER FUNCTION spgetusubjects(pjournal_id integer) OWNER TO pensoft;

--
-- Name: spgetutaxons(integer); Type: FUNCTION; Schema: pjs; Owner: pensoft
--

CREATE FUNCTION spgetutaxons(pjournal_id integer) RETURNS SETOF pjs.ret_spuserexpertises
    LANGUAGE plpgsql SECURITY DEFINER
    AS $$
	DECLARE
		Res pjs.ret_spUserExpertises;
		Record record;
		Record2 record;
BEGIN
 FOR Record IN 
 
	SELECT taxa.pos, u.id
	FROM public.usr u
	JOIN public.taxon_categories taxa ON taxa.id = ANY(u.expertise_taxon_categories)
	--JOIN pjs.journal_users ju ON u.id = ju.uid
	--WHERE ju.journal_id = pJournal_id
		
	LOOP 
		FOR Record2 IN
			SELECT id
			 FROM taxon_categories 
			 WHERE Record.pos like pos || '%'
		LOOP
			Res.category_id = Record2.id;
			Res.uid = Record.id;
			RETURN NEXT Res;
		END LOOP;
	END LOOP;
	RETURN;
END
$$;


ALTER FUNCTION spgetutaxons(pjournal_id integer) OWNER TO pensoft;



--
-- Name: spgetsesubjects(integer); Type: ACL; Schema: pjs; Owner: pensoft
--

REVOKE ALL ON FUNCTION spgetsesubjects(pjournal_id integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION spgetsesubjects(pjournal_id integer) FROM pensoft;
GRANT ALL ON FUNCTION spgetsesubjects(pjournal_id integer) TO pensoft;
GRANT ALL ON FUNCTION spgetsesubjects(pjournal_id integer) TO PUBLIC;
GRANT ALL ON FUNCTION spgetsesubjects(pjournal_id integer) TO iusrpmt;


--
-- Name: spgetsetaxons(integer); Type: ACL; Schema: pjs; Owner: pensoft
--

REVOKE ALL ON FUNCTION spgetsetaxons(pjournal_id integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION spgetsetaxons(pjournal_id integer) FROM pensoft;
GRANT ALL ON FUNCTION spgetsetaxons(pjournal_id integer) TO pensoft;
GRANT ALL ON FUNCTION spgetsetaxons(pjournal_id integer) TO PUBLIC;
GRANT ALL ON FUNCTION spgetsetaxons(pjournal_id integer) TO iusrpmt;


--
-- Name: spgetusubjects(integer); Type: ACL; Schema: pjs; Owner: pensoft
--

REVOKE ALL ON FUNCTION spgetusubjects(pjournal_id integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION spgetusubjects(pjournal_id integer) FROM pensoft;
GRANT ALL ON FUNCTION spgetusubjects(pjournal_id integer) TO pensoft;
GRANT ALL ON FUNCTION spgetusubjects(pjournal_id integer) TO PUBLIC;
GRANT ALL ON FUNCTION spgetusubjects(pjournal_id integer) TO iusrpmt;


--
-- Name: spgetutaxons(integer); Type: ACL; Schema: pjs; Owner: pensoft
--

REVOKE ALL ON FUNCTION spgetutaxons(pjournal_id integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION spgetutaxons(pjournal_id integer) FROM pensoft;
GRANT ALL ON FUNCTION spgetutaxons(pjournal_id integer) TO pensoft;
GRANT ALL ON FUNCTION spgetutaxons(pjournal_id integer) TO PUBLIC;
GRANT ALL ON FUNCTION spgetutaxons(pjournal_id integer) TO iusrpmt;
