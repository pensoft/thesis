--
-- PostgreSQL database dump
--

SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres84
--

COMMENT ON SCHEMA public IS 'Standard public schema';


--
-- Name: plpgsql; Type: PROCEDURAL LANGUAGE; Schema: -; Owner: postgres84
--

CREATE PROCEDURAL LANGUAGE plpgsql;


SET search_path = public, pg_catalog;

--
-- Name: ret_getstoriesbyrubr; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE ret_getstoriesbyrubr AS (
	guid integer,
	title character varying,
	author character varying,
	subtitle character varying,
	nadzaglavie character varying,
	description character varying,
	pubdate timestamp without time zone,
	createdate timestamp without time zone,
	previewpicid integer,
	link character varying,
	rubrid integer,
	rubrname character varying,
	state integer,
	priority integer,
	storytype integer
);


ALTER TYPE public.ret_getstoriesbyrubr OWNER TO postgres84;

--
-- Name: ret_getstoryrelateditems; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE ret_getstoryrelateditems AS (
	title character varying,
	relguid integer,
	pubdate timestamp without time zone,
	valstr character varying,
	valstr2 character varying,
	valint2 integer,
	propid integer,
	valint integer,
	imgname character varying,
	orientation integer,
	link character varying,
	author character varying,
	ptitle character varying,
	phototype integer,
	storytype integer,
	dim_x integer,
	dim_y integer
);


ALTER TYPE public.ret_getstoryrelateditems OWNER TO postgres84;

--
-- Name: ret_getsubrubrs; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE ret_getsubrubrs AS (
	id integer,
	name character varying,
	pos character varying,
	state integer
);


ALTER TYPE public.ret_getsubrubrs OWNER TO postgres84;

--
-- Name: ret_sggetrubrstories; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE ret_sggetrubrstories AS (
	comments integer,
	guid integer,
	title character varying,
	author character varying,
	subtitle character varying,
	nadzaglavie character varying,
	pubdate timestamp without time zone,
	previewpicid integer,
	rubrid integer,
	rubrname character varying,
	link character varying,
	state integer,
	linked integer,
	sitename character varying,
	siteurl character varying,
	description character varying
);


ALTER TYPE public.ret_sggetrubrstories OWNER TO postgres84;

--
-- Name: ret_sp_poll; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE ret_sp_poll AS (
	id integer,
	siteid integer,
	question character varying,
	startdate timestamp without time zone,
	enddate timestamp without time zone,
	flags integer,
	pos integer,
	description character varying,
	showforum integer,
	usrid integer,
	"language" integer,
	active integer,
	status integer
);


ALTER TYPE public.ret_sp_poll OWNER TO postgres84;

--
-- Name: ret_sp_poll_answer; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE ret_sp_poll_answer AS (
	id integer,
	pollid integer,
	ans character varying,
	flags integer,
	ord integer,
	votes integer
);


ALTER TYPE public.ret_sp_poll_answer OWNER TO postgres84;

--
-- Name: ret_spmultimedia; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE ret_spmultimedia AS (
	ftype integer,
	guid integer,
	"language" character varying(3),
	title character varying,
	description character varying,
	author character varying,
	createuid integer,
	"access" integer,
	accesscode character varying,
	length integer,
	dim_x integer,
	dim_y integer,
	filenameupl character varying,
	mediasize integer,
	mimetype character varying
);


ALTER TYPE public.ret_spmultimedia OWNER TO postgres84;

--
-- Name: ret_spsiterubr; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE ret_spsiterubr AS (
	id integer,
	sid integer,
	name character varying[],
	state integer,
	parentnode integer,
	cval integer
);


ALTER TYPE public.ret_spsiterubr OWNER TO postgres84;

--
-- Name: ret_userfpass; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE ret_userfpass AS (
	uname character varying,
	pass character varying
);


ALTER TYPE public.ret_userfpass OWNER TO postgres84;

--
-- Name: retforumgetmsgflathtml; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE retforumgetmsgflathtml AS (
	id integer,
	subject character varying,
	author character varying,
	msg text,
	mdate timestamp without time zone,
	rootid integer,
	dsc_name character varying,
	dsc_id integer,
	topic_name character varying,
	topic_id integer,
	topicflags integer,
	mflags integer,
	itemid integer,
	uid integer,
	uname character varying,
	ord character varying,
	replies integer,
	dsg_name character varying,
	fighttype integer
);


ALTER TYPE public.retforumgetmsgflathtml OWNER TO postgres84;

--
-- Name: retforumgetsinglemsg; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE retforumgetsinglemsg AS (
	id integer,
	subject character varying,
	author character varying,
	msg character varying,
	mdate timestamp without time zone,
	rootid integer,
	dsc_name character varying,
	dsc_id integer,
	topic_name character varying,
	topic_id integer,
	topicflags integer,
	mflags integer,
	uname character varying,
	dsg_name character varying
);


ALTER TYPE public.retforumgetsinglemsg OWNER TO postgres84;

--
-- Name: retforumgettopics; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE retforumgettopics AS (
	rootid integer,
	subject character varying,
	author character varying,
	mdate timestamp without time zone,
	replies integer,
	views integer,
	dscname character varying,
	lastmoddate timestamp without time zone,
	dscid integer,
	flags integer,
	points integer,
	uid integer,
	uname character varying,
	usertype integer,
	itemid integer
);


ALTER TYPE public.retforumgettopics OWNER TO postgres84;

--
-- Name: retgetallpolls; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE retgetallpolls AS (
	pollid integer,
	polltxt character varying,
	description character varying,
	polltype integer,
	startdate timestamp without time zone,
	enddate timestamp without time zone,
	count integer
);


ALTER TYPE public.retgetallpolls OWNER TO postgres84;

--
-- Name: retgetanketa; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE retgetanketa AS (
	pollid integer,
	ansid integer,
	polltxt character varying,
	description character varying,
	anstxt character varying,
	mult integer,
	votes integer,
	pollviewtype integer,
	sum integer
);


ALTER TYPE public.retgetanketa OWNER TO postgres84;

--
-- Name: retgetanketaarchiv; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE retgetanketaarchiv AS (
	pollid integer,
	ansid integer,
	polltxt character varying,
	description character varying,
	anstxt character varying,
	votes integer,
	pollviewtype integer,
	sum integer,
	startdate date,
	enddate date
);


ALTER TYPE public.retgetanketaarchiv OWNER TO postgres84;

--
-- Name: retgetattachment; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE retgetattachment AS (
	"access" integer,
	filename character varying,
	mimetype character varying,
	filetitle character varying,
	accesstype integer
);


ALTER TYPE public.retgetattachment OWNER TO postgres84;

--
-- Name: retgetattachmentsbystory; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE retgetattachmentsbystory AS (
	photoid integer,
	phototitle character varying,
	photoauthor character varying,
	pos integer,
	valstr character varying,
	valstr2 character varying,
	imgname character varying
);


ALTER TYPE public.retgetattachmentsbystory OWNER TO postgres84;

--
-- Name: retgetbulletinbasedata; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE retgetbulletinbasedata AS (
	guid integer,
	title character varying,
	primarysite integer
);


ALTER TYPE public.retgetbulletinbasedata OWNER TO postgres84;

--
-- Name: retgetgallerybasedata; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE retgetgallerybasedata AS (
	guid integer,
	title character varying,
	author character varying,
	pubdate timestamp without time zone,
	state integer,
	lastmod timestamp without time zone,
	createuid character varying,
	primarysite integer,
	showforum integer,
	storytype integer,
	"language" character varying(3),
	description character varying
);


ALTER TYPE public.retgetgallerybasedata OWNER TO postgres84;

--
-- Name: retgetlistrubr; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE retgetlistrubr AS (
	id integer,
	name character varying,
	url character varying,
	listpos integer,
	origpos character varying,
	rootnode integer
);


ALTER TYPE public.retgetlistrubr OWNER TO postgres84;

--
-- Name: retgetliststories; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE retgetliststories AS (
	guid integer,
	title character varying,
	author character varying,
	subtitle character varying,
	nadzaglavie character varying,
	description character varying,
	pubdate timestamp without time zone,
	previewpicid integer,
	priority integer,
	citiesid integer,
	vip integer,
	link character varying,
	posid integer,
	rubrid integer,
	rubrname character varying[],
	storytype integer
);


ALTER TYPE public.retgetliststories OWNER TO postgres84;

--
-- Name: retgetmediabasedata; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE retgetmediabasedata AS (
	guid integer,
	title character varying,
	author character varying,
	pubdate timestamp without time zone,
	state integer,
	lastmod timestamp without time zone,
	createuid character varying,
	primarysite integer,
	showforum integer,
	storytype integer,
	"language" character varying(3),
	storydesc character varying,
	ftype integer,
	mediaguid integer,
	description character varying,
	"access" integer,
	accesscode character varying,
	length integer,
	dim_x integer,
	dim_y integer,
	filenameupl character varying,
	mediasize integer,
	mimetype character varying,
	embedsource character varying
);


ALTER TYPE public.retgetmediabasedata OWNER TO postgres84;

--
-- Name: retgetrubrsiblings; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE retgetrubrsiblings AS (
	id integer,
	name character varying,
	ftype integer
);


ALTER TYPE public.retgetrubrsiblings OWNER TO postgres84;

--
-- Name: retgetstoriesbasedata; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE retgetstoriesbasedata AS (
	guid integer,
	title character varying,
	author character varying,
	pubdate timestamp without time zone,
	state integer,
	description character varying,
	keywords character varying,
	lastmod timestamp without time zone,
	createuid character varying,
	subtitle character varying,
	primarysite integer,
	link character varying,
	nadzaglavie character varying,
	showforum integer,
	storytype integer,
	"language" character varying(3),
	rubr character varying,
	rubrstr character varying,
	mainrubr integer,
	indexer integer
);


ALTER TYPE public.retgetstoriesbasedata OWNER TO postgres84;

--
-- Name: retmediabystory; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE retmediabystory AS (
	mid character varying,
	mtitle character varying,
	mauthor character varying,
	place integer,
	valstr character varying,
	ftype integer
);


ALTER TYPE public.retmediabystory OWNER TO postgres84;

--
-- Name: retphotosbystory; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE retphotosbystory AS (
	photoid character varying,
	phototitle character varying,
	photoauthor character varying,
	place integer,
	valstr character varying,
	imgname character varying,
	pos integer,
	frst integer
);


ALTER TYPE public.retphotosbystory OWNER TO postgres84;

--
-- Name: retsitelogin; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE retsitelogin AS (
	id integer,
	username character varying,
	fullname character varying,
	ip inet,
	ipallowed integer,
	"type" integer,
	state integer
);


ALTER TYPE public.retsitelogin OWNER TO postgres84;

--
-- Name: retsp_regprof; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE retsp_regprof AS (
	id integer,
	uname character varying,
	name character varying,
	email character varying,
	phone character varying,
	"type" integer
);


ALTER TYPE public.retsp_regprof OWNER TO postgres84;

--
-- Name: retspattachemnts; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE retspattachemnts AS (
	guid integer,
	storyid integer,
	title character varying,
	imgname character varying,
	underpic character varying
);


ALTER TYPE public.retspattachemnts OWNER TO postgres84;

--
-- Name: retsplogin; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE retsplogin AS (
	id integer,
	uname character varying,
	fullname character varying,
	url character varying,
	actype integer,
	error integer
);


ALTER TYPE public.retsplogin OWNER TO postgres84;

--
-- Name: retspmetadata; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE retspmetadata AS (
	id integer,
	title character varying,
	description character varying,
	keywords character varying
);


ALTER TYPE public.retspmetadata OWNER TO postgres84;

--
-- Name: retspmorelinks; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE retspmorelinks AS (
	guid integer,
	propid integer,
	url character varying,
	pos integer,
	title character varying
);


ALTER TYPE public.retspmorelinks OWNER TO postgres84;

--
-- Name: retspphotos; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE retspphotos AS (
	guid integer,
	storyid integer,
	title character varying,
	underpic character varying,
	place integer,
	pos integer,
	firstphoto integer
);


ALTER TYPE public.retspphotos OWNER TO postgres84;

--
-- Name: retunderforumgetmsgflathtml; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE retunderforumgetmsgflathtml AS (
	id integer,
	subject character varying,
	author character varying,
	msg text,
	mdate timestamp without time zone,
	rootid integer,
	dsc_name character varying,
	dsc_id integer,
	topic_name character varying,
	topic_id integer,
	topicflags integer,
	mflags integer,
	itemid integer,
	uid integer,
	uname character varying,
	ord character varying,
	replies integer,
	dsg_name character varying,
	mood integer
);


ALTER TYPE public.retunderforumgetmsgflathtml OWNER TO postgres84;

--
-- Name: statinfo; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE statinfo AS (
	word text,
	ndoc integer,
	nentry integer
);


ALTER TYPE public.statinfo OWNER TO postgres84;

--
-- Name: t_getmedia; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE t_getmedia AS (
	guid integer,
	title character varying,
	description character varying,
	author character varying,
	createdate timestamp without time zone,
	lastmod timestamp without time zone,
	filenameupl character varying,
	length integer,
	dim_x integer,
	dim_y integer,
	ftype integer
);


ALTER TYPE public.t_getmedia OWNER TO postgres84;

--
-- Name: tmenu; Type: TYPE; Schema: public; Owner: postgres84
--

CREATE TYPE tmenu AS (
	id integer,
	name character varying[],
	parentid integer,
	"type" integer,
	active integer,
	ord integer,
	href character varying[],
	img character varying[],
	mlevel integer
);


ALTER TYPE public.tmenu OWNER TO postgres84;

--
-- Name: addatttostory(integer, integer, character varying, integer, character varying); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION addatttostory(pattid integer, pstoryid integer, ptxt character varying, del integer, pextratxt character varying) RETURNS integer
    AS $$
BEGIN

IF (del = 0) THEN
IF EXISTS(SELECT guid FROM storyproperties WHERE guid=pStoryId AND propid=5 AND valint=pAttId) THEN --attachmenta ve4e su6testvuva
UPDATE storyproperties 
SET valstr=pTxt, valstr2=pExtraTxt 
WHERE guid = pStoryid AND propid = 5 AND valint = pAttId;
ELSE 
INSERT INTO storyproperties(guid, valint, valstr, propid, valstr2) 
VALUES (pStoryid, pAttId, pTxt, 5, pExtraTxt);
END IF;
ELSE -- DELETE
DELETE FROM storyproperties WHERE guid = pStoryId AND propid = 5 AND valint = pAttId;
END IF;

RETURN 0;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.addatttostory(pattid integer, pstoryid integer, ptxt character varying, del integer, pextratxt character varying) OWNER TO postgres84;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: listnames; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE listnames (
    listnameid integer NOT NULL,
    name character varying,
    objtype integer,
    sid integer
);


ALTER TABLE public.listnames OWNER TO postgres84;

--
-- Name: addlist(integer, integer, character varying, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION addlist(poper integer, plnid integer, pname character varying, pobjtype integer, psid integer) RETURNS listnames
    AS $$

DECLARE
	lResult listnames;
BEGIN
	lResult.listnameid := pLNid;
	
	IF pOper = 0 THEN -- GET
		SELECT INTO lResult * FROM listnames WHERE listnameid = pLNid;
	ELSIF pOper = 1 THEN --INSERT UPDATE
		IF pLNid IS NULL THEN -- INSERT
			INSERT INTO listnames (name, objtype, sid)
				VALUES (pName, pObjtype, pSid);
			lResult.listnameid := currval('listnames_listnameid_seq');
		ELSE --UPDATE
			UPDATE listnames
			SET 
				name = pName,
				objtype = pObjtype,
				sid = pSid
			WHERE
				listnameid = pLNid;
		END IF;
	ELSIF pOper = 3 THEN -- DELETE
		DELETE FROM listnames WHERE listnameid = pLNid;
	END IF;
	
	RETURN lResult ;
END ;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.addlist(poper integer, plnid integer, pname character varying, pobjtype integer, psid integer) OWNER TO postgres84;

--
-- Name: addmediatostory(integer, integer, integer, integer, character varying); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION addmediatostory(pftype integer, pid integer, pstoryid integer, pplace integer, ptxt character varying) RETURNS integer
    AS $$
DECLARE
	lPropid int;
	oldpos int;
	lastmaxfree int;
BEGIN
	IF pFtype = 3 THEN -- audio
		lPropid := 12;
	ELSIF (pFtype = 4 OR pFtype = 5) THEN -- video or embed video
		lPropid := 13;
	END IF;
		
	IF (pPlace = 0) THEN
		DELETE FROM storyproperties WHERE guid = pStoryid AND valint = pID AND propid = lPropid;
	ELSE 
		IF EXISTS (SELECT * FROM storyproperties WHERE guid = pStoryid AND valint = pID) THEN
			UPDATE storyproperties SET 
				valint2 = pPlace, 
				valstr = pTxt, 
				propid = lPropid 
			WHERE guid = pStoryid AND valint = pID;
		ELSE 
			INSERT INTO storyproperties (guid, valint, propid, valint2, valstr) 
				VALUES (pStoryid, pID, lPropid, pPlace, pTxt);
		END IF;
	END IF;
	
	RETURN 0;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.addmediatostory(pftype integer, pid integer, pstoryid integer, pplace integer, ptxt character varying) OWNER TO postgres84;

--
-- Name: addphototostory(integer, integer, integer, character varying, integer, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION addphototostory(pphotoid integer, pstoryid integer, pplace integer, ptxt character varying, pfirst integer, psid integer, ppos integer) RETURNS integer
    AS $$
DECLARE
lPos int;
rr RECORD;
BEGIN

IF pplace = 0 AND pfirst IS NULL THEN -- iztrivane na snimka
SELECT INTO lPos coalesce(valint3, 1) FROM storyproperties WHERE guid = pstoryid 
AND propid = 2 AND valint = pphotoid;

DELETE FROM storyproperties WHERE guid = pstoryid 
AND propid = 2 AND valint = pphotoid;

FOR rr IN SELECT valint FROM storyproperties 
WHERE guid = pstoryid AND propid = 2 AND (valint3 > lPos OR valint3 IS NULL)
ORDER BY valint3
LOOP
UPDATE storyproperties SET valint3 = lPos WHERE valint = rr.valint;
lPos := lPos + 1;
END LOOP;

IF EXISTS(SELECT guid FROM stories WHERE guid = pstoryid AND previewpicid = pphotoid) THEN
UPDATE stories SET previewpicid = NULL WHERE guid = pstoryid; 
END IF;

ELSE

IF ppos IS NULL THEN
SELECT INTO lPos coalesce((max(valint3) + 1), 1) FROM storyproperties WHERE guid = pstoryid AND propid = 2;
ELSE 
-- RAISE NOTICE 'bbb-%', ppos;
lPos := ppos + 1;
-- RAISE NOTICE 'ccc-%', lPos;
FOR rr IN SELECT valint FROM storyproperties 
WHERE guid = pstoryid AND propid = 2 AND (valint3 >= ppos OR valint3 IS NULL) AND valint <> pphotoid
ORDER BY valint3
LOOP
UPDATE storyproperties SET valint3 = lPos WHERE valint = rr.valint;
-- RAISE NOTICE 'aa-%', lPos;
lPos := lPos + 1;
END LOOP;

lPos := ppos;
END IF;

DELETE FROM storyproperties WHERE guid = pstoryid 
AND propid = 2 AND valint = pphotoid;

INSERT INTO storyproperties (guid, propid, valint, valint2, valstr, valint3) 
VALUES (pstoryid, 2, pphotoid, pplace, ptxt, lPos);

		IF pfirst IS NOT NULL THEN
			UPDATE stories SET previewpicid = pphotoid WHERE guid = pstoryid;
		ELSE
			IF EXISTS(SELECT guid FROM stories WHERE guid = pstoryid AND previewpicid = pphotoid) THEN
				UPDATE stories SET previewpicid = NULL WHERE guid = pstoryid;
			END IF;
		END IF;

END IF;

RETURN 0;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.addphototostory(pphotoid integer, pstoryid integer, pplace integer, ptxt character varying, pfirst integer, psid integer, ppos integer) OWNER TO postgres84;

--
-- Name: addstorytostory(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION addstorytostory(pstoryid integer, pstoryid1 integer, pproptype integer) RETURNS integer
    AS $$
DECLARE
lguid INT;
BEGIN
IF NOT EXISTS(SELECT guid FROM storyproperties WHERE guid = pstoryid AND valint = pstoryid1 AND propid = pproptype) THEN
INSERT INTO storyproperties (guid, propid, valint) VALUES (pstoryid, pproptype, pstoryid1);
END IF;
IF NOT EXISTS(SELECT guid FROM storyproperties WHERE guid = pstoryid1 AND valint = pstoryid AND propid = pproptype) THEN
INSERT INTO storyproperties (guid, propid, valint) VALUES (pstoryid1, pproptype, pstoryid);
END IF;
RETURN 1;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.addstorytostory(pstoryid integer, pstoryid1 integer, pproptype integer) OWNER TO postgres84;

--
-- Name: addtomessaging(character varying, character varying, character varying, timestamp without time zone); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION addtomessaging(pmfrom character varying, pmto character varying, psubject character varying, psenddate timestamp without time zone) RETURNS integer
    AS $$
DECLARE
BEGIN
INSERT INTO messaging(mfrom, mto, subject, filename, senddate, state) VALUES(pmfrom, pmto, psubject, (currval('messaging_id_seq'))::varchar || '.txt', psenddate, -1);
RETURN currval('messaging_id_seq');

END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.addtomessaging(pmfrom character varying, pmto character varying, psubject character varying, psenddate timestamp without time zone) OWNER TO postgres84;

--
-- Name: attupload(integer, integer, integer, character varying, character varying, character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION attupload(poper integer, pid integer, psrc integer, ptitle character varying, pfnupl character varying, pdescr character varying, pext character varying, pmimetype character varying) RETURNS integer
    AS $$
DECLARE
lRes int;
BEGIN
lRes := 0;

IF pOper = 1 THEN -- INSERT
INSERT INTO photos (ftype, lang, title, createuid, filenameupl, source, description, mimetype) 
VALUES (1, 'bg', pTitle, 1, pFnUpl, pSrc, pDescr, pMimetype);
lRes := currval('stories_guid_seq');

UPDATE photos SET imgname = lRes || pExt WHERE guid = lRes;

ELSIF pOper = 3 THEN -- DELETE
DELETE FROM photos WHERE guid = pID;
END IF;

RETURN lRes;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.attupload(poper integer, pid integer, psrc integer, ptitle character varying, pfnupl character varying, pdescr character varying, pext character varying, pmimetype character varying) OWNER TO postgres84;

--
-- Name: bitwise_or_for_aggregate(integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION bitwise_or_for_aggregate(integer, integer) RETURNS integer
    AS $_$select case when $1 is null then $2 else $1|$2 end;$_$
    LANGUAGE sql;


ALTER FUNCTION public.bitwise_or_for_aggregate(integer, integer) OWNER TO postgres84;

--
-- Name: cmsetsecgrpsite(integer, integer, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION cmsetsecgrpsite(pgid integer, psid integer, ptype integer, pop integer) RETURNS integer
    AS $$
	DECLARE
	BEGIN
		IF (pop = 1) THEN
			INSERT INTO secgrpacc VALUES(pgid, psid, ptype);
		ELSE
			DELETE FROM secgrpacc WHERE gid = pgid AND sid = psid;
		END	IF;	
		RETURN 1;
	END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.cmsetsecgrpsite(pgid integer, psid integer, ptype integer, pop integer) OWNER TO postgres84;

--
-- Name: cmsetsecusergrp(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION cmsetsecusergrp(pgid integer, puid integer, pop integer) RETURNS integer
    AS $$
	DECLARE
	BEGIN
		IF (pop = 1) THEN
			INSERT INTO secgrpdet VALUES(pgid, puid);
		ELSE
			DELETE FROM secgrpdet WHERE gid = pgid AND uid = puid;
		END	IF;	
		RETURN 1;
	END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.cmsetsecusergrp(pgid integer, puid integer, pop integer) OWNER TO postgres84;


--
-- Name: concat(text, text); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION concat(text, text) RETURNS text
    AS $_$
  DECLARE
    t text;
  BEGIN
    IF  character_length($1) > 0 THEN
      t = $1 ||'; '|| $2;
    ELSE
      t = $2;
    END IF;
    RETURN t;
  END;
$_$
    LANGUAGE plpgsql;


ALTER FUNCTION public.concat(text, text) OWNER TO postgres84;

--
-- Name: confmail(character varying); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION confmail(pconfhash character varying) RETURNS integer
    AS $$
DECLARE
	lResult int;
	lId int;
BEGIN
	
	lResult := 0;
	
	SELECT INTO lId id FROM usr WHERE state = 0 AND confhash = pConfHash;
	
	IF lId IS NOT NULL THEN 
		lResult := 1;
		--UPDATE
		UPDATE usr SET state = 1 WHERE id = lId;
	END IF;
	
	RETURN lResult;
END;

$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.confmail(pconfhash character varying) OWNER TO postgres84;

--
-- Name: deleterelateditemsfromstory(integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION deleterelateditemsfromstory(pguid integer) RETURNS integer
    AS $$
BEGIN
	DELETE FROM storyproperties WHERE guid = pGuid AND propid IN (12,13);
	
	RETURN 1;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.deleterelateditemsfromstory(pguid integer) OWNER TO postgres84;

--
-- Name: deletestory(integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION deletestory(pstoryid integer) RETURNS integer
    AS $$
DECLARE
	lStoryId int;
	lSidsTable varchar[];
	lQuery text;
	lStaticId int;
BEGIN
	SELECT INTO lStoryId guid FROM stories WHERE guid = pStoryId;
	
	IF (lStoryId IS NOT NULL) THEN
		-- sid* tables
		SELECT INTO lSidsTable ARRAY(SELECT (CASE WHEN coalesce(guid, 0) <> 0 THEN 'sid' || guid || 'storyprops' ELSE NULL END)::varchar FROM sites);
		FOR i IN array_lower(lSidsTable, 1) .. array_upper(lSidsTable, 1) LOOP
			IF (EXISTS(SELECT tablename FROM pg_tables WHERE tablename = lSidsTable[i])) THEN
				lQuery := 'DELETE FROM ' || lSidsTable[i] || ' WHERE guid = ' || lStoryId || '';
				EXECUTE lQuery;
				RAISE NOTICE '%', lQuery;
			END IF;
		END LOOP;
		
		-- static_article
		FOR lStaticId IN (SELECT * FROM static_article WHERE lStoryId = ANY(artid)) LOOP
			DELETE FROM static_article WHERE static_id = lStaticId;
			RAISE NOTICE 'DELETE FROM static_article WHERE static_id = %', lStaticId;
		END LOOP;
		
		-- storyproperties table
		IF (EXISTS(SELECT * FROM storyproperties WHERE guid = lStoryId)) THEN
			DELETE FROM storyproperties WHERE guid = lStoryId;
			RAISE NOTICE 'DELETE FROM storyproperties WHERE guid = %', lStoryId;
		END IF;
		
		-- msg table
		IF (EXISTS(SELECT * FROM msg WHERE itemid = lStoryId)) THEN
			DELETE FROM msg WHERE itemid = lStoryId;
			RAISE NOTICE 'DELETE FROM msg WHERE itemid = %', lStoryId;
		END IF;
		
		-- msgroot table
		IF (EXISTS(SELECT * FROM msgroot WHERE itemid = lStoryId)) THEN
			DELETE FROM msgroot WHERE itemid = lStoryId;
			RAISE NOTICE 'DELETE FROM msgroot WHERE itemid = %', lStoryId;
		END IF;
		
		-- storychangelog table
		IF (EXISTS(SELECT * FROM storychangelog WHERE guid = lStoryId)) THEN
			DELETE FROM storychangelog WHERE guid = lStoryId;
			RAISE NOTICE 'DELETE FROM storychangelog WHERE guid = %', lStoryId;
		END IF;
		
		-- storiesft table
		IF (EXISTS(SELECT * FROM storiesft WHERE guid = lStoryId)) THEN
			DELETE FROM storiesft WHERE guid = lStoryId;
			RAISE NOTICE 'DELETE FROM storiesft WHERE guid = %', lStoryId;
		END IF;
		
		-- listdets table
		IF (EXISTS(SELECT * FROM listdets WHERE objid = lStoryId)) THEN
			DELETE FROM listdets WHERE objid = lStoryId;
			RAISE NOTICE 'DELETE FROM listdets WHERE objid = %', lStoryId;
		END IF;
		
		-- stories table
		DELETE FROM stories WHERE guid = lStoryId;
		RAISE NOTICE 'DELETE FROM stories WHERE guid = %', lStoryId;
		
		RETURN 1;
	ELSE
		RETURN 0;
	END IF;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.deletestory(pstoryid integer) OWNER TO postgres84;

--
-- Name: deletevideo(integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION deletevideo(pstoryid integer) RETURNS integer
    AS $$
DECLARE
	lPhotoId int;
	lRet int;
BEGIN
	SELECT INTO lPhotoId valint
		FROM photos p
		JOIN storyproperties sp ON (sp.valint = p.guid AND sp.propid IN (12, 13))
		WHERE sp.guid = pStoryId;
	
	IF (lPhotoId IS NOT NULL) THEN
		DELETE FROM photos WHERE guid = lPhotoId;
		SELECT INTO lRet * FROM deleteStory(pStoryId);
		RETURN lRet;
	ELSE
		SELECT INTO lRet * FROM deleteStory(pStoryId);
		RETURN lRet;
	END IF;
	
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.deletevideo(pstoryid integer) OWNER TO postgres84;

--
-- Name: delstoryfromstory(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION delstoryfromstory(pstoryid integer, pstoryid1 integer, ppropid integer) RETURNS integer
    AS $$
DECLARE
lguid INT;
BEGIN
DELETE FROM storyproperties WHERE guid = pstoryid AND valint = pstoryid1 AND propid = ppropid;
DELETE FROM storyproperties WHERE guid = pstoryid1 AND valint = pstoryid AND propid = ppropid;
RETURN 1;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.delstoryfromstory(pstoryid integer, pstoryid1 integer, ppropid integer) OWNER TO postgres84;

--
-- Name: forumaddfirstmsg(integer, integer, integer, character varying); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION forumaddfirstmsg(pdscid integer, pitemid integer, puid integer, puname character varying) RETURNS integer
    AS $$
DECLARE
lItemName varchar;
lRes int;
lUID int;
--lSid int;
lCurTime timestamp;
BEGIN
lCurTime := current_timestamp;

--SELECT INTO lSid siteid FROM dsc WHERE id = pDscID;

lUID := pUID;
SELECT INTO lItemName getitemname FROM getItemName(1, pItemID);
IF lUID IS NULL THEN
	SELECT INTO lUID createuid FROM stories WHERE guid = pItemID;
END IF;

IF lItemName IS NULL THEN
RETURN lRes;
END IF;

INSERT INTO msg (dscID, itemID, subject, msg, ord, flags, mdate, lastmoddate, uid, uname) 
VALUES ( pDscID, pItemID, lItemName, '', '', 0, lCurTime, lCurTime, lUID, pUName);
lRes := currval('msg_id_seq');

UPDATE msg  SET rootID = lRes WHERE id = lRes;

UPDATE dsc SET tpcCount = tpcCount + 1 WHERE id = pDscID;


RETURN lRes;
END
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.forumaddfirstmsg(pdscid integer, pitemid integer, puid integer, puname character varying) OWNER TO postgres84;

--
-- Name: forumaddmsg(integer, integer, integer, character varying, character varying, text, text, inet, integer, character varying, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION forumaddmsg(preplyid integer, pdscid integer, pitemid integer, pauthor character varying, psubject character varying, pmsg text, pmsghtml text, psenderip inet, puid integer, puname character varying, phidden integer) RETURNS integer
    AS $$
	DECLARE
		lMsgID int;
		lNewOrd varchar;
		lReplyOrd varchar;
		lItemID int;
		lDscID int;
		lRootID int;
		lReplyID int;
		lAscii int;
		lLastID int;
		lCurTime timestamp;
		lTopicFlags int;
		lItemName varchar;
		lFlag int;
		lTopicAuthorId int;
		lTopicSubject varchar;
	BEGIN
		lCurTime := current_timestamp; --tva go polzvame za da insertnem i updatenem s edno i sushto vreme na vsiakude
		IF pReplyID IS NOT NULL THEN
			SELECT INTO lRootID, lDscID, lItemID, lTopicFlags  rootID, dscID, itemID, flags 
			FROM msg
			WHERE
				id = pReplyID;
			
			IF (lTopicFlags = 1) THEN --AKO TEMATA E ZATVORENA ZA POSTOVE
				RETURN 0;
			END IF;
			lReplyID := pReplyID;
		ELSE
			lReplyID := NULL;
			if pItemID IS NOT NULL THEN
				SELECT INTO lReplyID rootid
				FROM msg
				WHERE
					id = rootid
					AND itemid = pItemID
					AND dscid = pDscID;
					
				IF lReplyID IS NULL AND pDscID IS NOT NULL THEN
					-- Suzdavame tema pri purvi posting za dadeno itemID
					SELECT INTO lRootID forumaddfirstmsg FROM ForumAddFirstMsg(pDscID, pItemID, pUID, pUName);
					IF lRootID IS NULL THEN
						RETURN 0;
					END IF;	
					
					lReplyID := lRootID;
				ELSE
					SELECT INTO lRootID, lDscID, lItemID rootID, dscID, itemID
					FROM msg
					WHERE
						id = lReplyID;
				END IF;
			END IF;
			
			lDscID := pDscID;
			lItemID := pItemID ;
		END IF;
		--TRIABVA DA SE DOBAVI PROVERKA DALI SUSHTESTVUVA TAKAVA DISCUSIA
		IF (pReplyID IS NULL AND pHidden IS NOT NULL) THEN
			lFlag := 2;
		ELSE
			lFlag := 0;
		END IF;
		
		IF lDscID IS NULL THEN 
			RAISE EXCEPTION 'Invalid action';
		END IF;
		
		INSERT INTO msg (dscID, itemID, author, subject, msg, msghtml, senderip, rootID, uid, uname, mdate, lastmoddate, flags) 
			VALUES (lDscID, lItemID, pAuthor, pSubject, pMsg, pMsgHtml, pSenderIP, lRootID, pUID, pUName, lCurTime, lCurTime, lFlag);
		
		lMsgID := currval('msg_id_seq');
		
		
		IF lReplyID IS NULL THEN
			-- Suzdava se nova tema
			UPDATE msg
			SET 
				rootID = lMsgID,
				ord = 'AA'
			WHERE 
				id = lMsgID;
				
			UPDATE dsc 
			SET tpcCount = tpcCount + 1
			WHERE id = pDscID;
			--IF pitemid IS NULL THEN
				--UPDATE msg SET replies = replies + 1, lastmoddate = lCurTime WHERE id = lMsgID;
			--END IF;
		ELSE
			SELECT INTO lReplyOrd ord
				FROM msg
				WHERE id = lReplyID;
			
			UPDATE msg SET replies = replies + 1, lastmoddate = lCurTime WHERE id = lReplyID;
			
			SELECT INTO lNewOrd, lLastID 
				max(ord),
				max (
					CASE
						WHEN flags & 1 = 1 THEN id
						ELSE NULL
					END
				)
			FROM msg 
			WHERE
				rootID = lRootID
				AND ord LIKE COALESCE(lReplyOrd,'') || '__';
				
			IF lNewOrd IS NULL THEN
				lNewOrd := 'AA';
			ELSE
				lNewOrd := ForumGetNextOrd(substring(lNewOrd from char_length(lNewOrd)-1));
			END IF;
			
			IF lLastID IS NOT NULL THEN
				UPDATE msg SET flags = flags # 1 WHERE id = lLastID;
			END IF;
			
			UPDATE msg 
			SET 
				ord = COALESCE(lReplyOrd,'') || lNewOrd,
				flags = flags | 1
			WHERE
				id = lMsgID;
				
		END IF;
		RETURN 1;
	END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.forumaddmsg(preplyid integer, pdscid integer, pitemid integer, pauthor character varying, psubject character varying, pmsg text, pmsghtml text, psenderip inet, puid integer, puname character varying, phidden integer) OWNER TO postgres84;

--
-- Name: forumgetmsgflathtml(integer, integer, integer, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION forumgetmsgflathtml(prootid integer, psiteid integer, pdscgrpid integer, pdscid integer, pitemid integer) RETURNS SETOF retforumgetmsgflathtml
    AS $_$
	UPDATE msg SET 
		views = views +1
	WHERE
		id = $1;
	SELECT m.id, m.subject, m.author, m.msghtml, m.mdate, m.rootid, d.name, d.id,
			m2.subject, m2.id, m2.flags, m.flags, m.itemid, m.uid, m.uname, m.ord, m2.replies, g.name, m.fighttype
		FROM msg m
		INNER JOIN dsc d ON (m.dscid = d.id) 
		INNER JOIN dsg g ON (d.dsgid  = g.id)
		LEFT JOIN msg m2 ON m2.id = m.rootid and m2.rootid = m2.id
		WHERE 
			(($1 is not null and m.rootid = $1) OR ($1 is null and m.itemid = $5))
			AND (d.dsgid = $3 or $3 is null)
			AND (d.id = $4 or $4 is null);
--			AND d.siteid = $2;
	
			
$_$
    LANGUAGE sql SECURITY DEFINER;


ALTER FUNCTION public.forumgetmsgflathtml(prootid integer, psiteid integer, pdscgrpid integer, pdscid integer, pitemid integer) OWNER TO postgres84;

--
-- Name: forumgetnextord(character varying); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION forumgetnextord(pord character varying) RETURNS character varying
    AS $$
DECLARE
lResult varchar;
lAscii integer;
lOrd1 varchar;
lOrd2 varchar;
BEGIN
lAscii := ascii(substring(pOrd FROM char_length(pOrd)));

lOrd1 := substring(pOrd, char_length(pOrd) - 1, 1);
lOrd2 := substring(pOrd, char_length(pOrd), 1);

IF (lOrd2 = 'Z') THEN
lOrd2 = 'A';
lOrd1 = chr(ascii(lOrd1) + 1);
ELSE
lOrd2 = chr(ascii(lOrd2) + 1);
END IF;

RETURN lOrd1 || lOrd2;
END;
$$
    LANGUAGE plpgsql;


ALTER FUNCTION public.forumgetnextord(pord character varying) OWNER TO postgres84;

--
-- Name: forumgetsinglemsg(integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION forumgetsinglemsg(pmsgid integer, pdscgroup integer) RETURNS SETOF retforumgetsinglemsg
    AS $$
DECLARE
	lResult retForumGetSingleMsg%ROWTYPE;
	lDscName varchar;
	lDscId int;	
	lTopicName varchar;
	lTopicId int;
	lFlags int;
BEGIN
	FOR lResult IN
		SELECT m.id, m.subject, m.author, m.msghtml as msg, m.mdate, m.rootid, d.name, lDscId, 
				m2.subject, m2.id, m2.flags, m.flags, m.uname, g.name
			FROM msg m
			LEFT JOIN msg m2 ON m2.id = m.rootid and m2.rootid = m2.id
			INNER JOIN dsc d ON (m.dscid = d.id)
			INNER JOIN dsg g ON (d.dsgid = g.id)
			WHERE m.id = pMsgId AND (d.dsgid = pDscGroup OR pDscGroup IS NULL OR pDscGroup = 0)
	LOOP
		RETURN NEXT lResult;
	END LOOP;
	RETURN;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.forumgetsinglemsg(pmsgid integer, pdscgroup integer) OWNER TO postgres84;

--
-- Name: forumgettopics(integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION forumgettopics(pdiscid integer, pdscgroup integer) RETURNS SETOF retforumgettopics
    AS $_$
DECLARE
lRow retForumGetTopics;
BEGIN
	
	FOR lRow IN SELECT m.rootid, m.subject, m.author, m.mdate as mdate, m.replies, m.views,
		d.name, m.lastmoddate as lastmoddate, d.id, m.flags, m.points, m.uid, m.uname,
		null::int, m.itemid
	FROM msg m
	JOIN dsc d ON (m.dscid = d.id)
	WHERE m.dscid = $1 AND m.rootid = m.id AND (d.dsgid = $2) AND (m.flags & 8 <> 8) AND d.flags > 0 
	LOOP
		RETURN NEXT lRow;
	END LOOP;
	
	RETURN ;
END;
$_$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.forumgettopics(pdiscid integer, pdscgroup integer) OWNER TO postgres84;

--
-- Name: forumsetflags(integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION forumsetflags(pmsgid integer, pflags integer) RETURNS integer
    AS $$
	DECLARE
		mflags int;
		mCtrDif int;		
	BEGIN
		SELECT INTO mflags flags FROM msg WHERE id = pmsgid;
		
		IF (pflags & 4 = 4) THEN
			IF (mflags & 4 = 4) THEN
				mCtrDif := 1;
			ELSE
				mCtrDif := -1;
			END IF;
		END IF;
		
		UPDATE msg SET flags = flags # pflags WHERE id = pmsgid; 
		
		IF (mCtrDif IS NOT NULL) THEN
			UPDATE msg SET replies = replies + mCtrDif
				WHERE id = (SELECT rootid FROM msg WHERE id = pmsgid);
		END IF;		
		
		RETURN 1;
	END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.forumsetflags(pmsgid integer, pflags integer) OWNER TO postgres84;


--
-- Name: getallpolls(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION getallpolls(pposid integer, psid integer, plang integer) RETURNS SETOF retgetallpolls
    AS $$
	DECLARE
		lResult retGetAllPolls%ROWTYPE;
		lCount int;
	BEGIN			
		FOR lResult IN
			SELECT p.id, p.question, p.description, (CASE WHEN ( p.active <> 1 OR (p.enddate::date < now()::date ) ) THEN -1 ELSE 1 END) as polltype, p.startdate, p.enddate
				FROM poll p
				WHERE
					p.pos = pPosid
					AND p.siteid = pSid 
					AND p.startdate::date <= now()::date
					AND p.lang = pLang
					AND p.status = 1
				ORDER BY polltype DESC, id DESC
		LOOP
			RETURN NEXT lResult;
		END LOOP;
		RETURN;
	END ;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.getallpolls(pposid integer, psid integer, plang integer) OWNER TO postgres84;

--
-- Name: getanketa(inet, integer, integer, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION getanketa(pip inet, ppos integer, psid integer, ppollid integer, plang integer) RETURNS SETOF retgetanketa
    AS $$
	DECLARE
		lResult retGetAnketa%ROWTYPE;
		lPollId int;
		lViewType int;
		lSum int;
		lTmp int;
		lPollFlags int;
	BEGIN
		IF (pPollId IS NULL OR pPollId = 0) THEN
			SELECT INTO lPollId, lSum, lPollFlags p.id, sum(votes), p.flags
				FROM poll p
				INNER JOIN pans pa ON (p.id = pa.pollid)
				WHERE
					p.pos = pPos
					AND p.siteid = pSid 
					AND p.active = 1 AND p.startdate::date <= now()::date AND p.enddate::date >= now()::date
					AND pa.flags = 1
					AND p.lang = pLang
				GROUP BY p.id, p.flags;
		ELSE
			lPollId := pPollId;
			SELECT INTO lTmp, lSum, lPollFlags p.id, sum(votes) , p.flags
				FROM poll p
				INNER JOIN pans pa ON (p.id = pa.pollid)
				WHERE p.id = pPollId
				AND p.active = 1 AND p.startdate::date <= now()::date AND p.enddate::date >= now()::date
				AND pa.flags = 1
				AND p.lang = pLang
				GROUP BY p.id, p.flags;
		END IF;
		
		IF ((lPollFlags & 4 = 4) OR EXISTS(SELECT pollid FROM pollogs WHERE pollid = lPollId AND ip = pIp)) THEN
			lViewType = 1;--trebe da pokajem samo resultatite
		ELSE
			lViewType = 0;--ne e glasuvano ot tova ip i trebe da ia pokajem za glasuvane
		END IF;
		
		FOR lResult IN
			SELECT p.id, pa.id, p.question, p.description, pa.ans, p.flags, pa.votes, lViewType, lSum
				FROM poll p
				INNER JOIN pans pa ON (p.id = pa.pollid)
				WHERE
					p.id = lPollId 
					AND p.siteid = pSid 
					AND pa.flags = 1
					AND p.active = 1 AND p.startdate::date <= now()::date AND p.enddate::date >= now()::date
					AND p.lang = pLang
				ORDER BY pa.ord
		LOOP
			RETURN NEXT lResult;
		END LOOP;
		RETURN;
	END ;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.getanketa(pip inet, ppos integer, psid integer, ppollid integer, plang integer) OWNER TO postgres84;

--
-- Name: getanketaarchiv(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION getanketaarchiv(ppollid integer, psid integer, plang integer) RETURNS SETOF retgetanketaarchiv
    AS $$
	DECLARE
		lResult retGetAnketaArchiv%ROWTYPE;
		lSum int;
	BEGIN
		SELECT INTO lSum sum(votes)
			FROM poll p
			INNER JOIN pans pa ON (p.id = pa.pollid)
			WHERE
				p.id = ppollid 
				AND p.siteid = psid 
				AND p.lang = pLang
			GROUP BY p.id;
		
		FOR lResult IN
			SELECT p.id, pa.id, p.question, p.description, pa.ans, pa.votes, 1, lSum, startdate, enddate
				FROM poll p
				INNER JOIN pans pa ON (p.id = pa.pollid)
				WHERE
					p.id = ppollid 
					AND p.siteid = psid 
					AND p.lang = pLang
				ORDER BY pa.ord
		LOOP
			RETURN NEXT lResult;
		END LOOP;
		RETURN;
	END ;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.getanketaarchiv(ppollid integer, psid integer, plang integer) OWNER TO postgres84;

--
-- Name: getattachment(integer, character varying, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION getattachment(pguid integer, pcode character varying, purole integer) RETURNS SETOF retgetattachment
    AS $$
DECLARE
lResult retGetAttachment%ROWTYPE;
l_access int;
l_filename varchar;
l_mimetype varchar;
l_filetitle varchar;
BEGIN

SELECT INTO l_access, l_mimetype, l_filename, l_filetitle 
access, mimetype, imgname, filenameupl 
FROM photos WHERE guid = pguid;

IF (l_access = 0) THEN
lResult.access = 1;
END IF;
IF (l_access = 1) THEN --s code
IF (pcode IS NOT NULL) THEN
IF EXISTS(SELECT guid FROM careersAttCodes WHERE code = pcode AND guid = pguid) THEN
lResult.access = 1;
ELSE
lResult.access = 0;
END IF;
ELSE
lResult.access = 0;
END IF;
END IF;
IF (l_access = 2) THEN -- samo za abonati
lResult.access = 1;
END IF;
IF (lResult.access = 1) THEN
lResult.filename = l_filename;
lResult.filetitle = l_filetitle;
END IF;
lResult.accesstype = l_access;
RETURN NEXT lResult;
RETURN;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.getattachment(pguid integer, pcode character varying, purole integer) OWNER TO postgres84;

--
-- Name: getattachmentsbystory(integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION getattachmentsbystory(pstoryid integer) RETURNS SETOF retgetattachmentsbystory
    AS $_$

SELECT sp.valint, p.title, p.author, sp.valint3, sp.valstr, sp.valstr2, p.imgname
FROM storyproperties sp JOIN photos p ON (p.guid = sp.valint)
WHERE sp.guid = $1 AND propid = 5
ORDER BY sp.valint3 ASC;

$_$
    LANGUAGE sql STABLE SECURITY DEFINER;


ALTER FUNCTION public.getattachmentsbystory(pstoryid integer) OWNER TO postgres84;

--
-- Name: getbulletinbasedata(integer, integer, integer, character varying); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION getbulletinbasedata(pguid integer, pstorytype integer, puid integer, plang character varying) RETURNS retgetbulletinbasedata
    AS $$
DECLARE
	lResult retGetBulletinBaseData%ROWTYPE;
	lBulletinGuid int;
	lGuid int;
BEGIN
	
	lGuid := pGuid;
	IF pGuid IS NULL THEN
		SELECT INTO lGuid * FROM SaveStoriesBaseData(null, 1, pLang, 'Untitled',null, null,now()::timestamp, null, 
			pUID,null, 0, null, null, pStorytype, null, null, 
			1, null, 1, 0, 1, null
		);	
	END IF;
	
	SELECT INTO lResult s.guid, s.title, s.primarysite
		FROM stories s 
		LEFT JOIN usr u on (s.createuid = u.id)
		WHERE s.guid = lGuid AND storytype = pStorytype;
	
	IF lResult.guid IS NULL THEN
		RAISE EXCEPTION 'GetBulletinBaseData.nosuchstory';
	END IF;
		
	RETURN lResult;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.getbulletinbasedata(pguid integer, pstorytype integer, puid integer, plang character varying) OWNER TO postgres84;

--
-- Name: getgallerybasedata(integer, integer, integer, character varying); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION getgallerybasedata(pguid integer, pstorytype integer, puid integer, plang character varying) RETURNS retgetgallerybasedata
    AS $$
DECLARE
	lResult retGetGalleryBaseData%ROWTYPE;
	lMediaGuid int;
	lGuid int;
BEGIN
	lGuid := pGuid;
	IF pGuid IS NULL THEN
		SELECT INTO lGuid * FROM SaveStoriesBaseData(null, 1, pLang, 'Untitled',null, null,now()::timestamp, null, 
			pUID,null, 0, null, null, pStorytype, null, null, 
			1, 1, 1, 1, null
		);	
	END IF;
	
	SELECT INTO lResult s.guid, s.title, s.author, s.pubdate, s.state, s.lastmod, u.name, 
		s.primarysite,s.showforum, s.storytype, s.lang, s.description
		FROM stories s 
		LEFT JOIN usr u on (s.createuid = u.id)
		WHERE s.guid = lGuid AND storytype = pStorytype;
	
	IF lResult.guid IS NULL THEN
		RAISE EXCEPTION 'GetGalleryBaseData.nosuchstory';
	END IF;
		
	RETURN lResult;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.getgallerybasedata(pguid integer, pstorytype integer, puid integer, plang character varying) OWNER TO postgres84;

--
-- Name: getitemname(integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION getitemname(pitemtype integer, pitemid integer) RETURNS character varying
    AS $$
DECLARE
lResult varchar ;
BEGIN 
if pItemType = 1 THEN
SELECT INTO lResult title FROM stories WHERE guid = pItemID;
-- elsif pItemType = then
ELSE
lResult := NULL;
END IF;
RETURN lResult;
END;
$$
    LANGUAGE plpgsql;


ALTER FUNCTION public.getitemname(pitemtype integer, pitemid integer) OWNER TO postgres84;

--
-- Name: getlistrubr(integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION getlistrubr(plistid integer, plangid integer) RETURNS SETOF retgetlistrubr
    AS $_$
SELECT r.id, r.name[$2], ('/browse.php?rubrid=' || r.id), ld.posid, r.pos, r.rootnode 
FROM listnames l 
JOIN listdets ld USING(listnameid) 
JOIN rubr r ON ld.objid = r.id 
WHERE l.objtype = 2 AND l.listnameid = $1 
ORDER BY ld.posid 
$_$
    LANGUAGE sql SECURITY DEFINER;


ALTER FUNCTION public.getlistrubr(plistid integer, plangid integer) OWNER TO postgres84;

--
-- Name: getliststories(integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION getliststories(plistid integer, plangid integer) RETURNS SETOF retgetliststories
    AS $_$

SELECT DISTINCT ON (ld.posid, s.guid)
s.guid,
s.title,
s.author,
s.subtitle, 
s.nadzaglavie, 
s.description, 
s.pubdate,
s.previewpicid,
sd.priority,
sd.citiesid,
sd.vip,
(case 
when s.link is not null then s.link 
else '/show.php?storyid=' || s.guid end) as link,
ld.posid,
r.id as rubrid,
r.name[$2] as rubrname,
s.storytype
FROM listnames l 
JOIN listdets ld USING(listnameid) 
JOIN stories s ON s.guid = ld.objid
JOIN sid1storyprops sd USING(guid) 
JOIN storyproperties sp ON sp.guid = s.guid AND sp.propid = 4 
JOIN rubr r ON sp.valint = r.id 
WHERE s.state IN (3,4)
AND l.listnameid = $1
AND l.objtype = 1 
AND s.pubdate < current_timestamp
AND s.lang = (select code from languages where langid=$2)
ORDER BY ld.posid;
$_$
    LANGUAGE sql SECURITY DEFINER;


ALTER FUNCTION public.getliststories(plistid integer, plangid integer) OWNER TO postgres84;

--
-- Name: getmedia(); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION getmedia() RETURNS SETOF t_getmedia
    AS $$
SELECT DISTINCT 
p.guid,
p.title,
p.description,
p.author,
p.createdate,
p.lastmod,
p.filenameupl,
p.length,
p.dim_x,
p.dim_y,
p.ftype
FROM photos p
WHERE ftype IN (3,4);
$$
    LANGUAGE sql STABLE SECURITY DEFINER;


ALTER FUNCTION public.getmedia() OWNER TO postgres84;

--
-- Name: getmediabasedata(integer, integer, integer, character varying); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION getmediabasedata(pguid integer, pstorytype integer, puid integer, plang character varying) RETURNS retgetmediabasedata
    AS $$
DECLARE
	lResult retGetMediaBaseData%ROWTYPE;
	lMediaGuid int;
	lGuid int;
BEGIN
	
	lGuid := pGuid;
	IF pGuid IS NULL THEN
		SELECT INTO lGuid * FROM SaveStoriesBaseData(null, 1, pLang, 'Untitled',null, null,now()::timestamp, null, 
			pUID,null, 0, null, null, pStorytype, null, null, 
			1, 1, 1, 1, null
		);	
	END IF;
	
	SELECT INTO lResult s.guid, s.title, s.author, s.pubdate, s.state, s.lastmod, u.name, 
		s.primarysite,s.showforum, s.storytype, s.lang, s.description as storydesc
		FROM stories s 
		LEFT JOIN usr u on (s.createuid = u.id)
		WHERE s.guid = lGuid AND s.storytype = pStorytype;
	
	IF lResult.guid IS NULL THEN
		RAISE EXCEPTION 'getmediabasedata.nosuchstory';
	END IF;
		
	IF pGuid IS NOT NULL THEN
		SELECT INTO lMediaGuid valint FROM storyproperties WHERE guid = pguid AND propid IN (12, 13);
		SELECT INTO --lResult *, null, null, null, null
			lResult.ftype, lResult.mediaguid, lResult.description,
				lResult.access, lResult.length, lResult.dim_x, lResult.dim_y, lResult.filenameupl, lResult.mediasize, lResult.mimetype, lResult.embedsource
			ftype, guid, description, access, length, dim_x, dim_y, filenameupl, mediasize, mimetype, imgname
		FROM photos
		WHERE guid = lMediaGuid;
	END IF;
	RETURN lResult;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.getmediabasedata(pguid integer, pstorytype integer, puid integer, plang character varying) OWNER TO postgres84;

--
-- Name: getmediabystory(integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION getmediabystory(pstoryid integer) RETURNS SETOF retmediabystory
    AS $$
DECLARE
lResult retMediaByStory%ROWTYPE;
BEGIN

FOR lResult IN
SELECT sp.valint as mid, p.title, p.author, sum(sp.valint2) as place, max(sp.valstr) as valstr, p.ftype
FROM storyproperties sp
INNER JOIN photos p ON sp.valint = p.guid
LEFT JOIN stories s ON s.guid = pstoryid
WHERE sp.guid = pstoryid AND sp.propid IN (12,13)
GROUP BY sp.valint, p.title, p.author, p.ftype, s.guid
LOOP
RETURN NEXT lResult;
END LOOP;

RETURN;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.getmediabystory(pstoryid integer) OWNER TO postgres84;

--
-- Name: getmenucontents(integer, integer, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION getmenucontents(integer, integer, integer, integer) RETURNS SETOF tmenu
    AS $_$
	DECLARE
		pMenuId	ALIAS FOR $1 ;
		pLevel	ALIAS FOR $2 ;
		psid	ALIAS FOR $3 ;
		pactive	ALIAS FOR $4 ;
		rec		tMenu ;
		recTmp	tMenu ;
		--offset	varchar ;
	begin
		for rec IN 
			SELECT
				id, 
				name,
				parentid,
				type,
				active,
				ord,
				href,
				img,
				pLevel as mlevel
			FROM menus
			WHERE parentid = pMenuId and sid=psid and (not (pactive>0) or ((active>0) and (name[pactive] is not null) and (char_length(name[pactive])>0))) 
			ORDER BY ord ASC
		loop
			if rec.type = 1 then
				return NEXT rec ;
				for recTmp IN
					SELECT * FROM getMenuContents( rec.id, pLevel + 1 ,psid, pactive)
				loop
					return NEXT recTmp ;
				end loop ;
			else
				return NEXT rec ;
			end if ;
		end loop ;
		
		return ;
	end ;
$_$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.getmenucontents(integer, integer, integer, integer) OWNER TO postgres84;

--
-- Name: getphotosbystory(integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION getphotosbystory(pstoryid integer, psid integer) RETURNS SETOF retphotosbystory
    AS $$
DECLARE
lResult retPhotosByStory%ROWTYPE;
lphotoid int;
lpropid int;
BEGIN
-- IF psid = 3 THEN
lpropid := 2;
-- END IF;
FOR lResult IN
SELECT sp.valint as picid, p.title, p.author, sum(sp.valint2) as place, max(sp.valstr) as valstr, p.imgname, sp.valint3 as pos, s.guid as frst
FROM storyproperties sp
INNER JOIN photos p ON (sp.valint = p.guid)
LEFT JOIN stories s on s.previewpicid = sp.valint and s.guid = pstoryid
WHERE sp.guid = pstoryid AND sp.propid = lpropid
GROUP BY sp.valint, p.title, p.author, p.imgname, sp.valint3,s.guid
ORDER BY pos ASC
LOOP
RETURN NEXT lResult;
END LOOP;

RETURN;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.getphotosbystory(pstoryid integer, psid integer) OWNER TO postgres84;

--
-- Name: getrubrsiblings(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION getrubrsiblings(prubrid integer, psid integer, plangid integer) RETURNS SETOF retgetrubrsiblings
    AS $_$

DECLARE
	lRes retGetRubrSiblings;
	lParentPos varchar;		
BEGIN
	if ((pRubrID is NULL) or (pRubrID=0)) then
		for lRes in select id, name[$3] as name,0 as ftype from rubr 
			where sid=pSid and (name[$3] is not null) and  (char_length(name[$3])>0) and (id=rootnode)
			order by pos
		loop
			return NEXT lRes ;
		end loop;
	else
		lParentPos=(select pos from rubr where id=pRubrID);
		for lRes in select id, name[$3] as name,(case when id=pRubrID then 1 when char_length(lParentPos)=char_length(pos) then 0 else 2 end) as ftype from rubr 
			where (name[$3] is not null) and  (char_length(name[$3])>0) 
					and ((pos like (lParentPos || '__')) or ( substring(pos from 1 for (char_length(pos)-2)) = substring(lParentPos from 1 for (char_length(lParentPos)-2)) ))
			order by pos
		loop
			return NEXT lRes ;
		end loop; 
	end if;
	return;
END
$_$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.getrubrsiblings(prubrid integer, psid integer, plangid integer) OWNER TO postgres84;

--
-- Name: getstoriesbasedata(integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION getstoriesbasedata(pguid integer, plangid integer) RETURNS SETOF retgetstoriesbasedata
    AS $$
DECLARE
	lResult retGetStoriesBaseData%ROWTYPE;
	lIcons varchar;
	lrubrids varchar;
	lrubrtxt varchar;
	lRubrRes RECORD;
BEGIN
	lIcons = '';
	lrubrids = '';
	lrubrtxt = '';

	FOR lResult IN SELECT s.guid, s.title, s.author, s.pubdate, s.state, s.description, s.keywords, s.lastmod, u.name, 
		s.subtitle, s.primarysite, s.link, s.nadzaglavie, s.showforum, s.storytype, s.lang, null, null, sp.valint, sp.valint2
		FROM stories s 
		JOIN sid1storyprops sd USING(guid) 
		LEFT JOIN storyproperties sp ON s.guid = sp.guid AND sp.propid = 4 
		LEFT JOIN usr u on (s.createuid = u.id)
		WHERE s.guid = pguid
	LOOP

		FOR lRubrRes IN
			SELECT sp.valint, r.name[pLangid]
			FROM storyproperties sp 
			JOIN rubr r ON sp.valint = r.id 
			WHERE sp.guid = pguid AND sp.propid = 1
		LOOP
			lrubrids := lrubrids || lRubrRes.valint::varchar || ',';
			lrubrtxt := lrubrtxt || lRubrRes.name::varchar || ', ';
		END LOOP;

		lResult.rubr := lrubrids;
		lResult.rubrstr := lrubrtxt;
		
		RETURN NEXT lResult;
	END LOOP;

	RETURN;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.getstoriesbasedata(pguid integer, plangid integer) OWNER TO postgres84;

--
-- Name: getstoriesbyrubr(integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION getstoriesbyrubr(prubr integer, plang integer) RETURNS SETOF ret_getstoriesbyrubr
    AS $_$

SELECT DISTINCT ON (s.pubdate::date, sd.priority, s.pubdate, s.guid)
	s.guid,
	s.title,
	s.author,
	s.subtitle,
	s.nadzaglavie,
	s.description,
	s.pubdate,
	s.createdate,
	s.previewpicid,
	(case 
		when s.link is not null then s.link 
		else '/show.php?storyid=' || s.guid end) as link,
	sp.valint as rubrid,
	r.name[$2] as rubrname,
	s.state,
	sd.priority,
	s.storytype
FROM stories s
JOIN storyproperties sp ON s.guid = sp.guid AND sp.propid IN (4, 1)
JOIN rubr r ON r.id = sp.valint AND r.sid = 1 
JOIN sid1storyprops sd ON s.guid = sd.guid
JOIN languages l ON s.lang = l.code
WHERE (r.id = $1 or r.rootnode = $1 OR $1 IS NULL OR $1= 0)
AND s.state IN (3,4) 
AND l.langid = $2
AND s.pubdate > (current_timestamp - '4 months'::interval)
AND s.pubdate < current_timestamp
ORDER BY s.pubdate::date DESC, sd.priority DESC, s.pubdate DESC;

$_$
    LANGUAGE sql STABLE SECURITY DEFINER;


ALTER FUNCTION public.getstoriesbyrubr(prubr integer, plang integer) OWNER TO postgres84;

--
-- Name: getstoryrelateditems(integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION getstoryrelateditems(pstoryid integer) RETURNS SETOF ret_getstoryrelateditems
    AS $_$

UPDATE sid1storyprops
SET viewed = coalesce(viewed,0) + 1
WHERE guid = $1;

SELECT s.title,
s.guid as relguid,
s.pubdate, 
sp.valstr,
sp.valstr2,
sp.valint2,
sp.propid,
sp.valint,
p.imgname,
p.orientation,
(case 
when s.link is not null then s.link 
else '/show.php?storyid=' || s.guid end) as link,
p.author,
p.title as ptitle,
p.type as phototype,
s.storytype,
p.dim_x, p.dim_y
FROM storyproperties sp
LEFT JOIN photos p on sp.valint = p.guid 
LEFT JOIN stories s on sp.valint = s.guid
LEFT JOIN sid1storyprops sd on sd.guid = s.guid
LEFT JOIN sites si on si.guid = s.primarysite
WHERE sp.guid = $1 AND sp.propid in (2, 3, 5, 9, 6, 12, 13, 14, 15) AND (sp.valint2 > 0 OR sp.valint2 IS NULL)
ORDER BY propid, valint3;

$_$
    LANGUAGE sql SECURITY DEFINER;


ALTER FUNCTION public.getstoryrelateditems(pstoryid integer) OWNER TO postgres84;

--
-- Name: getsubrubrs(integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION getsubrubrs(prubr integer) RETURNS SETOF ret_getsubrubrs
    AS $_$

SELECT r2.id, r2.name, r2.pos, r2.state 
FROM rubr r1 
JOIN rubr r2 ON r1.id = r2.rootnode AND r2.pos LIKE r1.pos || '__'
WHERE r1.id = $1;

$_$
    LANGUAGE sql STABLE SECURITY DEFINER;


ALTER FUNCTION public.getsubrubrs(prubr integer) OWNER TO postgres84;

--
-- Name: languages; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE languages (
    langid integer NOT NULL,
    code character varying(3) NOT NULL,
    name character varying NOT NULL
);


ALTER TABLE public.languages OWNER TO postgres84;

--
-- Name: langs(integer, integer, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION langs(pop integer, plangid integer, pcode character varying, pname character varying) RETURNS languages
    AS $$
DECLARE
	lResult languages;
BEGIN
	
IF (pOp = 0) THEN
	SELECT INTO lResult *
		FROM languages
		WHERE code = pCode;
ELSIF (pOp = 1) THEN
	IF NOT EXISTS(SELECT * FROM languages WHERE code = pCode) THEN --INSERT
		INSERT INTO languages(langid, code, name)
			VALUES(pLangid, pCode, pName);
	ELSE --UPDATE
		UPDATE languages
		SET langid = pLangid,
			code = pCode,
			name = pName
		WHERE code = pCode;
	END IF;
ELSIF (pOp = 3) THEN
	DELETE FROM languages WHERE code = pCode;
END IF;

RETURN lResult;

END ;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.langs(pop integer, plangid integer, pcode character varying, pname character varying) OWNER TO postgres84;


--
-- Name: msgroot_upd(); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION msgroot_upd() RETURNS "trigger"
    AS $$
BEGIN
	IF (NEW.id = NEW.rootid) THEN
		UPDATE msgroot SET 
			dscid = NEW.dscid,
			itemid = NEW.itemid,
			replies = NEW.replies
		WHERE msgid = NEW.id;
				
		IF (NOT FOUND) THEN
			INSERT INTO msgroot (msgid, dscid, itemid, replies)
				VALUES(NEW.id, NEW.dscid, NEW.itemid, NEW.replies);
		END IF;
	END IF;
	
	UPDATE msgroot SET lastpostid = NEW.id, lastpost = now() WHERE msgid = NEW.rootid;
	
	RETURN NULL;
END;
$$
    LANGUAGE plpgsql;


ALTER FUNCTION public.msgroot_upd() OWNER TO postgres84;


--
-- Name: picsupload(integer, integer, integer, character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION picsupload(poper integer, pid integer, psrc integer, ptitle character varying, pfnupl character varying, pdescr character varying) RETURNS integer
    AS $$
DECLARE
lRes int;
BEGIN
lRes := 0;

IF pOper = 1 THEN -- INSERT
INSERT INTO photos (lang, title, createuid, description, filenameupl, source, mimetype) 
VALUES ('bg', pTitle, 1, pDescr, pFnUpl, pSrc, 'image/jpeg');
lRes := currval('stories_guid_seq');
ELSIF pOper = 3 THEN -- DELETE
DELETE FROM photos WHERE guid = pID;
END IF;

RETURN lRes;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.picsupload(poper integer, pid integer, psrc integer, ptitle character varying, pfnupl character varying, pdescr character varying) OWNER TO postgres84;



--
-- Name: rubrikirearange(integer, integer[]); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION rubrikirearange(psiteid integer, pmodifiedarr integer[]) RETURNS integer
    AS $$
DECLARE
arrSize int;
arrIter int;
lRubID int;
lRNOld int;
lPosLNew varchar;
lPosLOld varchar;
lSid int;
lRubrs RECORD;
lOverlayFrom int;
BEGIN
lPosLNew := 'AA';

arrSize := array_upper(pModifiedArr, 1);
arrIter := 1;

<< firstloop >>
WHILE arrIter <= arrSize 
LOOP
lRubID := pModifiedArr[arrIter];

IF lRubID > 0 THEN
SELECT INTO lPosLOld, lRNOld pos, rootnode FROM rubr WHERE id = lRubID;

lOverlayFrom := 1;
IF char_length(lPosLOld) > 2 THEN
lOverlayFrom := (char_length(lPosLOld) / 2);

IF mod(lOverlayFrom, 2) = 0 THEN
lOverlayFrom := lOverlayFrom + 1;
ELSE
lOverlayFrom := lOverlayFrom + 2;
END IF;

RAISE NOTICE 'posold - % --- posnew - % --- oo - %', lPosLOld, lPosLNew, lOverlayFrom;
END IF;

UPDATE rubr SET
pos = overlay(pos placing lPosLNew from lOverlayFrom for 2)
WHERE id = lRubID;

<< secondloop >>
FOR lRubrs IN SELECT * FROM rubr WHERE rootnode = lRNOld AND pos LIKE lPosLOld || '%' AND char_length(pos) > char_length(lPosLOld) ORDER BY pos
LOOP
RAISE NOTICE 'id - % ---- posold - % ---- posnew - %', lRubrs.id, lPosLOld, lPosLNew;
UPDATE rubr SET
pos = overlay(pos placing lPosLNew from lOverlayFrom for 2)
WHERE id = lRubrs.id;

END LOOP secondloop;

lPosLNew := ForumGetNextOrd(lPosLNew);
END IF;
arrIter := arrIter + 1;
END LOOP firstloop;

RETURN 1;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.rubrikirearange(psiteid integer, pmodifiedarr integer[]) OWNER TO postgres84;

--
-- Name: savestoriesbasedata(integer, integer, character varying, character varying, character varying, character varying, timestamp without time zone, character varying, integer, character varying, integer, character varying, character varying, integer, integer, character varying, integer, integer, integer, integer, text); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION savestoriesbasedata(pguid integer, pprimarysite integer, plang character varying, ptitle character varying, plink character varying, pdescription character varying, ppubdate timestamp without time zone, pauthor character varying, pcreateuid integer, pkeywords character varying, pstate integer, psubtitle character varying, pnadzaglavie character varying, pstorytype integer, pmainrubr integer, prubr character varying, ppriority integer, pindexer integer, pshowforum integer, pdscid integer, pbody text) RETURNS integer
    AS $$
DECLARE
	lRubrArr text[];
	lArrSize integer;
	lArrIter integer;
	lRubr varchar;
	lGuid int;
	lRootID int;
	lKeyw text;
	lAllowedKwdArr text[];
	lKeyWords text[];
	lNewKeyWords text;

BEGIN
	
	IF (pRubr IS NOT NULL AND pMainRubr IS NULL) THEN
		RAISE EXCEPTION 'savestoriesbasedata.mustSelectMainRubr';
	END IF;
	
	-- Kluchovi dumi
	lNewKeyWords := '';
	lAllowedKwdArr := NULL;

	IF (pkeywords <> '') THEN 
		lKeyWords := string_to_array(pkeywords, ',');
				
		FOR i IN array_lower(lKeyWords, 1) .. array_upper(lKeyWords, 1) LOOP
			lKeyw := replace(replace(trim(translate(lKeyWords[i], E'\n\r\t', '   ')), '  ', ' '), '   ', ' ');
			
			IF (lAllowedKwdArr IS NULL) THEN
				lAllowedKwdArr := ARRAY[lKeyw];
			ELSE
				lAllowedKwdArr := lAllowedKwdArr || lKeyw;
			END IF;
			
			lNewKeyWords := array_to_string(lAllowedKwdArr, ', ');
		END LOOP;
	END IF;
	
	IF (trim(E'\n\r \t' from pauthor) <> '') THEN 
		IF NOT EXISTS (SELECT * from authors WHERE upper(authors_name) = upper(trim(E'\n\r \t' from pauthor))) THEN
			INSERT INTO authors VALUES (default,trim(E'\n\r \t' from pauthor));
		END IF;
	END IF;
	
	IF (pguid IS NULL) THEN
		INSERT INTO stories
			(lang, title, link, description, pubdate, author, createdate, lastmod, createuid, 
			 keywords, state, subtitle, primarysite, nadzaglavie, showforum, storytype)
			VALUES (pLang, ptitle, plink, pdescription, ppubdate, pauthor, current_timestamp, 
			current_timestamp, pcreateuid, lNewKeyWords, pstate, psubtitle, pprimarysite,
			pnadzaglavie, pshowforum, pStoryType);
		
		lGuid := currval('stories_guid_seq');
	ELSE
		UPDATE stories 
			SET lang = pLang, 
				title = ptitle, 
				link = plink, 
				description = pdescription, 
				pubdate = ppubdate, 
				author = pauthor, 
				lastmod = current_timestamp, 
				keywords = lNewKeyWords,
				state = pstate, 
				subtitle = psubtitle, 
				primarysite = pprimarysite, 
				nadzaglavie = pnadzaglavie, 
				showforum = pshowforum, 
				storytype = pStoryType
			WHERE guid = pguid;
		lGuid := pguid;
	END IF;
	
	SELECT INTO lRootID rootid
	FROM msg
	WHERE
		id = rootid
		AND itemid = lGuid
		AND dscid = pDscID;
	
	IF lRootID IS NULL THEN
		SELECT INTO lRootID forumaddfirstmsg FROM ForumAddFirstMsg(pDscID, lGuid, pcreateuid, null);
	ELSE
		IF pguid IS NOT NULL THEN
			UPDATE msg SET subject = ptitle WHERE id = rootid AND itemid = lGuid AND dscid = pDscID;
		END IF;
	END IF;
	
	IF EXISTS (SELECT * FROM sid1storyprops WHERE guid = lGuid) THEN
		UPDATE sid1storyprops SET priority = pPriority WHERE guid = lGuid;
	ELSE 
		INSERT INTO sid1storyprops (guid, priority, viewed) 
		VALUES (lGuid, pPriority, 0);
	END IF;
	
	PERFORM SaveStoriesRubriki(lGuid, pRubr, pMainRubr, pIndexer, pprimarysite);
	PERFORM StoriesIndexer(lGuid, pIndexer, pstate, pBody);
	
	INSERT INTO storychangelog (guid, modtime, userid, status, init) 
		VALUES (lGuid, current_timestamp, pcreateuid, pstate, (CASE WHEN pguid IS NULL THEN 1 ELSE 0 END));
	
	RETURN lGuid;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.savestoriesbasedata(pguid integer, pprimarysite integer, plang character varying, ptitle character varying, plink character varying, pdescription character varying, ppubdate timestamp without time zone, pauthor character varying, pcreateuid integer, pkeywords character varying, pstate integer, psubtitle character varying, pnadzaglavie character varying, pstorytype integer, pmainrubr integer, prubr character varying, ppriority integer, pindexer integer, pshowforum integer, pdscid integer, pbody text) OWNER TO postgres84;

--
-- Name: savestoriesrubriki(integer, character varying, integer, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION savestoriesrubriki(pguid integer, prubrstr character varying, pmainrubr integer, pindexer integer, psid integer) RETURNS integer
    AS $$
	DECLARE
		lRubrPropId int;
		lRubrArr text[];
		lArrSize int;
		lArrIter int;
		lRubr varchar;
		lRubrPos varchar;
		lRubrRoot int;
	BEGIN
		--predpolagam che vuv vseki site shte ima rubriki i za tva tazi chast e obshta za vischki site-ve - ZA SEGA
		--iztrivame vsichki rubriki za tozi sait
		DELETE FROM storyproperties
			WHERE guid = pguid
			AND propid in (1, 4)
			AND (pSid is null or valint in (SELECT id from rubr where sid = pSid));
		
		-- tva e main rubrikata
		IF (pmainrubr IS NOT NULL) THEN
			INSERT INTO storyproperties (guid, propid, valint, valint2) VALUES (pguid, 4, pmainrubr, pindexer);
			SELECT INTO lRubrRoot, lRubrPos rootnode, pos FROM rubr WHERE id = pmainrubr;
			INSERT INTO storyproperties (guid, propid, valint, valint2)
				SELECT pguid, 1, id, pindexer
				FROM rubr 
				WHERE (rootnode = lRubrRoot AND substring(lRubrPos for length(pos)) = pos OR id = lRubrRoot)
				AND id NOT IN (SELECT valint FROM storyproperties WHERE guid = pguid AND propid = 1);
		END IF;
		
		IF (prubrstr IS NOT NULL) THEN
			lRubrArr := string_to_array(pRubrStr, ',');
			lArrSize := array_upper(lRubrArr, 1);
			lArrIter := 1;
			while (lArrIter < lArrSize) loop
				lRubr := lRubrArr[lArrIter]::int;
				SELECT INTO lRubrRoot, lRubrPos rootnode, pos FROM rubr WHERE id = lRubr::int;
				INSERT INTO storyproperties (guid, propid, valint, valint2)
					SELECT pguid, 1, id, pindexer
					FROM rubr 
					WHERE (rootnode = lRubrRoot AND substring(lRubrPos for length(pos)) = pos OR id = lRubrRoot)
					AND id NOT IN (SELECT valint FROM storyproperties WHERE guid = pguid AND propid = 1);
				--INSERT INTO storyproperties (guid, propid, valint, valint2) VALUES (pguid, 1, lRubr::int, pstorytype);
				lArrIter := lArrIter + 1;
			END LOOP;
		END IF;
		RETURN 1;
	END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.savestoriesrubriki(pguid integer, prubrstr character varying, pmainrubr integer, pindexer integer, psid integer) OWNER TO postgres84;

--
-- Name: savetransliterationwords(integer, integer, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION savetransliterationwords(pop integer, pid integer, pwordbg character varying, pworden character varying) RETURNS integer
    AS $$
DECLARE
BEGIN
	IF(pOp = 1) THEN
		IF (pID IS NULL) THEN
			INSERT INTO transliteration_words(word_bg, word_en) VALUES (pWordBg, pWordEn);
		ELSE
			UPDATE transliteration_words SET
				word_bg = pWordBg,
				word_en = pWordEn
			WHERE id = pID;
		END IF;
	ELSEIF ( pOp = 2) THEN
		DELETE FROM transliteration_words WHERE id = pID;
	END IF;
	RETURN 1;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.savetransliterationwords(pop integer, pid integer, pwordbg character varying, pworden character varying) OWNER TO postgres84;

--
-- Name: secsitesrearrange(integer, integer[]); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION secsitesrearrange(pparentid integer, psiteids integer[]) RETURNS integer
    AS $$
DECLARE
	arrSize int;
	arrIter int;
	lSid int;
	lSiteId int;
	lCount int;
BEGIN
	arrSize := array_upper(pSiteIds, 1);
	arrIter := 1;
	lCount :=1;
	WHILE arrIter <= arrSize 
		LOOP
			lSiteId := pSiteIds[arrIter];

			IF lSiteId > 0 THEN
				IF pParentId > 0 THEN 
					UPDATE secsites s SET
						ord = lCount
						FROM secsites s1 
					WHERE s1.id = pParentId AND ( (s.url ILIKE (s1.url || '%') AND s.cnt = s1.cnt + 1 AND s.type = 1) OR (s.type = 2 AND s.url = s1.url)) AND s.id = lSiteId;
					lCount := lCount + 1;
				ELSE
					UPDATE secsites s SET
						ord = lCount
					WHERE s.cnt <=2 AND s.id = lSiteId;
					lCount := lCount + 1;				
				END IF;
			END IF;
			arrIter := arrIter + 1;
		END LOOP;

	RETURN 1;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.secsitesrearrange(pparentid integer, psiteids integer[]) OWNER TO postgres84;

--
-- Name: setconfhash(character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION setconfhash(puser character varying, pemail character varying, pconfhash character varying) RETURNS integer
    AS $_$

UPDATE usr SET confhash = $3 WHERE uname = $1 AND email = $2;
SELECT 1;

$_$
    LANGUAGE sql SECURITY DEFINER;


ALTER FUNCTION public.setconfhash(puser character varying, pemail character varying, pconfhash character varying) OWNER TO postgres84;

--
-- Name: sggetrubrstories(integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION sggetrubrstories(prubr integer, plangid integer) RETURNS SETOF ret_sggetrubrstories
    AS $_$

SELECT DISTINCT ON (s.pubdate::date, s.pubdate, s.guid)
msg.replies as comments, 
s.guid,
s.title,
s.author,
s.subtitle,
s.nadzaglavie,
s.pubdate,
s.previewpicid,
sp.valint as rubrid,
r.name [$2] as rubrname,
s.link as link,
s.state,
(CASE WHEN s.primarysite <> 17 THEN 1 ELSE 0 END),
si.name,
si.siteurl,
s.description
FROM stories s
LEFT JOIN sites si on si.guid = s.primarysite 
LEFT JOIN (
SELECT msg.* FROM msg 
JOIN dsc d on d.id = msg.dscid AND d.siteid = 17
) as msg ON msg.itemid = s.guid AND msg.id = msg.rootid 
JOIN storyproperties sp ON s.guid = sp.guid AND sp.propid IN (4, 1)
JOIN rubr r ON r.id = sp.valint
JOIN languages l on (s.lang=l.code)
WHERE (r.id = $1 or r.rootnode = $1) and l.langid = $2 
ORDER BY s.pubdate::date DESC, s.pubdate DESC;

$_$
    LANGUAGE sql STABLE SECURITY DEFINER;


ALTER FUNCTION public.sggetrubrstories(prubr integer, plangid integer) OWNER TO postgres84;

--
-- Name: sggetrubrstories(integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION sggetrubrstories(plangid integer) RETURNS SETOF ret_sggetrubrstories
    AS $_$

SELECT DISTINCT ON (s.pubdate::date, s.pubdate, s.guid)
msg.replies as comments, 
s.guid,
s.title,
s.author,
s.subtitle,
s.nadzaglavie,
s.pubdate,
s.previewpicid,
sp.valint as rubrid,
r.name [$1] as rubrname,
s.link as link,
s.state,
(CASE WHEN s.primarysite <> 17 THEN 1 ELSE 0 END),
si.name,
si.siteurl,
s.description
FROM stories s
LEFT JOIN sites si on si.guid = s.primarysite 
LEFT JOIN (
SELECT msg.* FROM msg 
JOIN dsc d on d.id = msg.dscid AND d.siteid = 17
) as msg ON msg.itemid = s.guid AND msg.id = msg.rootid 
JOIN storyproperties sp ON s.guid = sp.guid AND sp.propid IN (4, 1)
JOIN rubr r ON r.id = sp.valint
JOIN languages l on (s.lang=l.code)
WHERE l.langid = $1 
ORDER BY s.pubdate::date DESC, s.pubdate DESC;

$_$
    LANGUAGE sql STABLE SECURITY DEFINER;


ALTER FUNCTION public.sggetrubrstories(plangid integer) OWNER TO postgres84;

--
-- Name: sitelogin(character varying, character varying, inet); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION sitelogin(puname character varying, ppass character varying, pip inet) RETURNS retsitelogin
    AS $$
DECLARE
	lResult retSiteLogin;
	rr RECORD;
	lallowed int;
BEGIN
	
	lallowed := 1;
	
	SELECT INTO rr id, uname, name as fullname, utype, state 
	FROM usr WHERE uname = pUname AND upass = md5(ppass);
	
	IF rr.id IS NOT NULL THEN
		SELECT INTO lResult rr.id, rr.uname, rr.fullname, pip, lallowed, rr.utype, rr.state;
	END IF;
	
	RETURN lResult;
END ;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.sitelogin(puname character varying, ppass character varying, pip inet) OWNER TO postgres84;

--
-- Name: sp_poll(integer, integer, integer, character varying, character varying, timestamp without time zone, timestamp without time zone, integer, integer, integer, integer, integer, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION sp_poll(poper integer, pid integer, psiteid integer, pquestion character varying, pdescription character varying, pstartdate timestamp without time zone, penddate timestamp without time zone, pcreateuid integer, pshowforum integer, pposition integer, pflags integer, plang integer, pactive integer, pstatus integer) RETURNS ret_sp_poll
    AS $$
DECLARE
	lResult ret_sp_poll;
BEGIN
	lResult.id := pID;
	lResult.siteID := pSiteID;
	lResult.question := pQuestion;
	lResult.flags := pFlags;
	
	IF pOper = 1 THEN
		IF pID IS NULL THEN
			-- INSERT
			
			INSERT INTO poll ( siteID, question, description, startdate, enddate, flags, showforum, pos, usrid, lang, active, status )
			VALUES ( pSiteID, pQuestion, pDescription, pStartDate, pEndDate, pflags, pShowForum, pPosition, pCreateUID, pLang, pActive, pStatus );
			lResult.id := currval( 'poll_id_seq' );
			
		ELSE
			--UPDATE
			lResult.id := pID;
			
			UPDATE poll
			SET
				question = pQuestion,
				description = pDescription,
				showforum = pShowForum,
				startdate = pStartDate,
				enddate = pEndDate,
				flags = pflags,
				pos = pPosition,				
				siteID = pSiteID,
				lang = pLang,
				active = pActive,
				status = pStatus
			WHERE
				id = pID;
				
		END IF;
		
		IF pActive = 1 THEN 
			UPDATE poll SET active = 0 WHERE pos = pPosition AND id <> lResult.id;
		END IF;
		
	ELSIF pOper = 3 THEN
		-- DELETE
		
		-- Triem logovete s otgovorite kum anketata
		DELETE FROM pollogs WHERE pollID = pID;
		
		-- Triem otgovorite na anketata
		DELETE FROM pans WHERE pollID = pID;
		
		-- Triem samata anketa
		DELETE FROM poll WHERE id = pID;
		
	END IF;
	
	
	
	SELECT INTO lResult * FROM poll WHERE id = lResult.id;
	
	
	RETURN lResult;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.sp_poll(poper integer, pid integer, psiteid integer, pquestion character varying, pdescription character varying, pstartdate timestamp without time zone, penddate timestamp without time zone, pcreateuid integer, pshowforum integer, pposition integer, pflags integer, plang integer, pactive integer, pstatus integer) OWNER TO postgres84;

--
-- Name: sp_poll_answer(integer, integer, integer, character varying, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION sp_poll_answer(poper integer, pid integer, ppollid integer, panswer character varying, pflags integer, pord integer) RETURNS ret_sp_poll_answer
    AS $$
DECLARE
	lResult ret_sp_poll_answer;
BEGIN
	lResult.id := pID;
	
	IF pOper = 1 THEN
		IF pID IS NULL THEN
			-- INSERT
			
			INSERT INTO pans ( pollid, ans, flags, ord)
			VALUES ( pPollid, pAnswer, pFlags, pOrd );
			lResult.id := currval( 'pans_id_seq' );
			
		ELSE
			--UPDATE
			lResult.id := pID;
			
			UPDATE pans
			SET
				pollid = pPollid,
				ans = pAnswer,
				flags = pFlags,
				ord = pOrd
			WHERE
				id = pID;
				
		END IF;
		
	ELSIF pOper = 3 THEN
		-- DELETE
		
		-- Triem otgovorite na anketata
		DELETE FROM pans WHERE id = pID;
		
	END IF;
	
	
	
	SELECT INTO lResult * FROM pans WHERE id = lResult.id;
	
	
	RETURN lResult;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.sp_poll_answer(poper integer, pid integer, ppollid integer, panswer character varying, pflags integer, pord integer) OWNER TO postgres84;

--
-- Name: sp_regprof(integer, integer, character varying, character varying, integer, character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION sp_regprof(poper integer, pid integer, pusername character varying, pupass character varying, putype integer, pname character varying, pemail character varying, pphone character varying) RETURNS retsp_regprof
    AS $$
DECLARE
	lResult retsp_regprof;
BEGIN
	IF pOper = 1 THEN
		
		IF pID IS NULL THEN
			-- INSERT
			IF EXISTS (SELECT * FROM usr WHERE uname = pUserName) THEN 
				RAISE EXCEPTION 'regprof.userexists';
			END IF;
			
			INSERT INTO usr (uname, upass, name, email, phone, utype, confhash) 
				VALUES (pUserName, md5(pUpass), pName, pEmail, pPhone, pUtype);
			lResult.id := currval('usr_id_seq');
			
		ELSE
			--UPDATE
			UPDATE usr SET
				upass = coalesce(md5(pUpass), upass),
				name = pName,
				email = pEmail,
				phone = pPhone
			WHERE id = pID;
		END IF;
		
	ELSIF pOper = 2 THEN
		--UPDATE ot admin
		UPDATE usr SET
			upass = coalesce(md5(pUpass), upass),
			name = pName,
			email = pEmail,
			utype = pUtype,
			phone = pPhone
		WHERE id = pID;
	END IF;
	
	SELECT INTO lResult id, uname, name, email, phone, utype
	FROM usr WHERE id = coalesce(pID, lResult.id);
	
	RETURN lResult;
END;

$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.sp_regprof(poper integer, pid integer, pusername character varying, pupass character varying, putype integer, pname character varying, pemail character varying, pphone character varying) OWNER TO postgres84;

--
-- Name: spattachemnts(integer, integer, integer, integer, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION spattachemnts(poper integer, pid integer, pstoryid integer, ppicid integer, ptitle character varying, pdescr character varying) RETURNS retspattachemnts
    AS $$
DECLARE
lResult retspAttachemnts;
BEGIN

IF pStoryID IS NULL AND pOper <> 0 THEN 
RETURN lResult;
END IF;

IF pOper = 1 THEN

IF pID IS NULL THEN
-- INSERT

INSERT INTO storyproperties (guid, propid, valint, valstr) 
VALUES (pStoryID, 5, pPicID, pDescr);

ELSE

--UPDATE
IF pPicID IS NULL THEN 
UPDATE storyproperties SET
valstr = pDescr
WHERE guid = pStoryID 
AND propid = 5 AND valint = pID;
ELSE 

DELETE FROM storyproperties WHERE guid = pStoryID 
AND propid = 5 AND valint = pID;

INSERT INTO storyproperties (guid, propid, valint, valstr) 
VALUES (pStoryID, 5, pPicID, pDescr);

END IF;

END IF;

END IF;

IF pOper = 3 THEN 
DELETE FROM storyproperties WHERE guid = pStoryID 
AND propid = 5 AND valint = pID;
END IF;

SELECT INTO lResult.guid, lResult.title, lResult.imgname
p.guid, p.title, p.imgname 
FROM photos p 
WHERE p.guid = coalesce(pPicID, pID);

SELECT INTO lResult.storyid, lResult.underpic sp.guid, sp.valstr 
FROM storyproperties sp 
WHERE sp.valint = coalesce(pPicID, pID) AND sp.propid = 5 AND sp.guid = pStoryID; 

RETURN lResult;
END;

$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.spattachemnts(poper integer, pid integer, pstoryid integer, ppicid integer, ptitle character varying, pdescr character varying) OWNER TO postgres84;

--
-- Name: splogin(character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION splogin(puname character varying, ppass character varying, pip character varying) RETURNS SETOF retsplogin
    AS $$
DECLARE
lResult retspLogin;
luname varchar;
lfullname varchar;
lid int;
lstate int;
ltype int;
lerr int;
BEGIN
SELECT INTO lid, luname, lfullname, lstate, ltype 
	id, uname, name, state, utype 
FROM usr 
WHERE uname = puname AND upass = md5(ppass);

lerr := 0;
IF lstate = 0 THEN
	lerr := 1;
END IF;

IF lstate = 1 AND ltype = 1 THEN
	lerr := 2;
END IF;

IF lid IS NOT NULL AND lerr = 0 THEN
	FOR lResult IN
		SELECT 
			lid, 
			luname,
			lfullname,
			s.url,
			MAX(ga.type)
		FROM 
			secgrpdet gd
			JOIN secgrpacc ga ON (gd.gid = ga.gid)
			JOIN secsites s ON (s.id = ga.sid AND s.type = 1)
		WHERE 
			gd.uid = lid
		GROUP BY ga.sid, s.url
	LOOP
		RETURN NEXT lResult;
	END LOOP;
ELSE 
	lResult.error := lerr;
	RETURN NEXT lResult;
END IF;

RETURN;
END ;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.splogin(puname character varying, ppass character varying, pip character varying) OWNER TO postgres84;

--
-- Name: spmetadata(integer, integer, character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION spmetadata(poper integer, pid integer, ptitle character varying, pdescription character varying, pkeywords character varying) RETURNS retspmetadata
    AS $$
DECLARE
	lResult retspMetadata%ROWTYPE;
	
BEGIN
	IF pOper = 1 THEN
		IF pId IS NULL AND NOT EXISTS(SELECT * FROM metadata WHERE title = pTitle AND description = pDescription AND keywords = pKeywords) THEN
			INSERT INTO metadata(title, description, keywords) VALUES (pTitle, pDescription, pKeywords);
		ELSEIF pId IS NOT NULL THEN
			UPDATE metadata SET
				title = pTitle, 
				description = pDescription,
				keywords = pKeywords
			WHERE id = pId;
		ELSE
			RAISE EXCEPTION 'Съществуват такива данни';
		END IF;
	ELSEIF pOper = 3 THEN
		DELETE FROM metadata WHERE id = pId;
	END IF;
	
	SELECT INTO lResult * FROM metadata WHERE id = pId;
	
	RETURN lResult;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.spmetadata(poper integer, pid integer, ptitle character varying, pdescription character varying, pkeywords character varying) OWNER TO postgres84;

--
-- Name: spmorelinks(integer, integer, character varying, character varying, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION spmorelinks(poper integer, pguid integer, purl character varying, ptitle character varying, ppos integer, ppropid integer) RETURNS retspmorelinks
    AS $$

DECLARE
lResult retspmorelinks;
oldpos integer;
BEGIN

IF pOper = 0 THEN -- GET

SELECT INTO lResult  
guid, propid, valstr, valint, valstr2 
FROM storyproperties 
WHERE guid = pGuid 
AND propid = pPropId 
AND valint = pPos;

ELSIF pOper = 1 THEN --INSERT

IF NOT EXISTS (SELECT * FROM storyproperties WHERE guid = pGuid AND propid = pPropId 
AND valstr = pUrl AND valstr2 = pTitle) THEN

-- Proverqvame dali ima link s takava poziciq i ako ima updatevame poziciite s edna nagore
IF EXISTS (SELECT valint FROM storyproperties WHERE guid = pGuid AND propid = pPropId AND valint = pPos) THEN
UPDATE storyproperties SET valint = valint + 1 WHERE valint >= pPos AND guid = pGuid AND propid = pPropId;
END IF;

-- INSERT
INSERT INTO storyproperties VALUES (pGuid, pPropId, pUrl, pPos, null, null, pTitle);

ELSE

-- Ako ima takyv link izvejdame greshka
RAISE EXCEPTION 'Този линк е вече въведен!'; 

END IF;

ELSIF pOper = 3 THEN -- DELETE

-- Iztrivame linka i smykvame linkovete nad nego s edna poziciq nadolu
oldpos := pPos;
DELETE FROM storyproperties WHERE guid = pGuid AND propid = pPropId AND valint = pPos;
UPDATE storyproperties SET valint = valint - 1 WHERE valint > oldpos AND guid = pGuid AND propid = pPropId;

END IF;

RETURN lResult ;
END ;

$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.spmorelinks(poper integer, pguid integer, purl character varying, ptitle character varying, ppos integer, ppropid integer) OWNER TO postgres84;

--
-- Name: spmultimedia(integer, integer, integer, character varying, character varying, character varying, character varying, integer, integer, character varying, character varying, integer, integer, integer, integer, integer, character varying, integer, integer, character varying); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION spmultimedia(poper integer, pftype integer, pguid integer, plang character varying, ptitle character varying, pdescription character varying, pauthor character varying, pcreateuid integer, paccess integer, paccesscode character varying, prealname character varying, pdimx integer, pdimy integer, plength integer, psrcid integer, pplace integer, pmediatxt character varying, pmediasize integer, psrctype integer, pmimetype character varying) RETURNS ret_spmultimedia
    AS $$
DECLARE
	lResult ret_spMultimedia;
	lLang varchar;
	lTitle varchar;
	lOldSecCode varchar;
	lImgName varchar;
BEGIN
	IF (pLang IS NULL) THEN
		lLang := 'bg';
	ELSE
		lLang := pLang;
	END IF;
	lResult.guid := pGuid;
	
	lTitle := pTitle;
	IF lTitle IS NULL THEN
		lTitle := pRealName;
	END IF;
	
	IF pOper = 1 THEN -- INSERT/UPDATE
		IF pGuid IS NULL THEN -- INSERT
			INSERT INTO photos (lang, filenameupl, title, description, pubdate, author, createdate, lastmod, createuid, ftype, access, length, dim_x, dim_y, mediasize, mimetype)
			VALUES (lLang, pRealName, lTitle, pDescription, CURRENT_TIMESTAMP, pAuthor, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, pCreateUID, pFType, coalesce(pAccess, 0), pLength, pDimX, pDimY, pMediaSize, pMimetype);
			lResult.guid = currval('stories_guid_seq');
			
			IF (pAccess = 1) THEN
				INSERT INTO careersattcodes (guid, code) VALUES (lResult.guid, pAccessCode);
			END IF;
		ELSE --UPDATE
			RAISE NOTICE '%', pRealName;
			UPDATE photos
			SET lang = lLang, 
				title = lTitle, 
				description = pDescription, 
				author = pAuthor, 
				ftype = pFType,
				imgname = (CASE
								WHEN pFType = 3 THEN pGuid || '.mp3'
								WHEN pFType = 4 THEN pGuid || '.flv'
								WHEN pFType = 5 THEN pRealName
							END),
				lastmod = CURRENT_TIMESTAMP, 
				access = COALESCE(pAccess, 0), 
				length = pLength,
				filenameupl = pRealName,
				dim_x = pDimX,
				dim_y = pDimY,
				mediasize = pMediaSize,
				mimetype = pMimetype
			WHERE guid = pGuid;
			lResult.guid := pGuid;
			
			IF (pAccess = 1 AND pAccessCode <> '') THEN
				SELECT INTO lOldSecCode code FROM careersattcodes WHERE guid = pGuid;
				IF (lOldSecCode IS NULL) THEN
					INSERT INTO careersattcodes (guid, code) VALUES (pGuid, pAccessCode);
				ELSE
					UPDATE careersattcodes SET code = pAccessCode WHERE guid = pGuid;
				END IF;
			ELSIF (pAccess = 0) THEN
				DELETE FROM careersattcodes WHERE guid = pGuid;
			END IF;
		END IF;
		
		IF (trim(E'\n\r \t' FROM pAuthor) <> '') THEN 
			IF NOT EXISTS (SELECT * FROM pauthors WHERE upper(pauthors_name) = upper(trim(E'\n\r \t' FROM pAuthor))) THEN
				INSERT INTO pauthors VALUES (default, trim(E'\n\r \t' FROM pAuthor));
			END IF;
		END IF;
		
	END IF;
		
	SELECT INTO --lResult *, null, null, null, null
		lResult.ftype, lResult.guid, lResult.language, lResult.title, lResult.description, lResult.author, lResult.createuid, 
		lResult.access, lResult.length, lResult.dim_x, lResult.dim_y, lResult.filenameupl, lResult.mediasize, lResult.mimetype
		ftype, guid, lang, title, description, author, createuid, access, length, dim_x, dim_y, filenameupl, mediasize, mimetype
	FROM photos
	WHERE guid = lResult.guid;
	
	IF (pGuid IS NOT NULL) THEN
		SELECT INTO lResult.accesscode code FROM careersattcodes WHERE guid = pGuid;
	END IF;
	
	IF pSrcID IS NOT NULL THEN
		IF (pSrcType = 2 OR pSrcType = 3) THEN
			PERFORM AddMediaToAd(pFType, lResult.guid, pSrcID);
		ELSIF (pSrcType = 1) THEN 
			PERFORM AddMediaToStory(pFType, lResult.guid, pSrcID, pPlace, pMediaTxt);
		END IF;
	END IF;
	
	IF pOper = 1 THEN 
		lImgName := (CASE
						WHEN lResult.ftype = 3 THEN lResult.guid || '.mp3'
						WHEN lResult.ftype = 4 THEN lResult.guid || '.flv'
						WHEN lResult.ftype = 5 THEN lResult.filenameupl
					END);
		UPDATE photos SET imgname = lImgName WHERE guid = lResult.guid;
	END IF;
	
	IF pOper = 3 THEN -- DELETE
		IF (EXISTS(SELECT * FROM storyproperties WHERE propid IN (12, 13) AND valint = pGuid)) THEN
			RAISE EXCEPTION 'Медията не може да бъде изтрита, защото е вързана към статии.';
		END IF;
		
		DELETE FROM storyproperties WHERE guid = pGuid;
		DELETE FROM photos WHERE guid = pGuid;
		DELETE FROM careersattcodes WHERE guid = pGuid;
	END IF;

	RETURN lResult;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.spmultimedia(poper integer, pftype integer, pguid integer, plang character varying, ptitle character varying, pdescription character varying, pauthor character varying, pcreateuid integer, paccess integer, paccesscode character varying, prealname character varying, pdimx integer, pdimy integer, plength integer, psrcid integer, pplace integer, pmediatxt character varying, pmediasize integer, psrctype integer, pmimetype character varying) OWNER TO postgres84;

SET default_with_oids = false;

--
-- Name: newsletter; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE newsletter (
    id integer NOT NULL,
    tstamp timestamp without time zone DEFAULT now() NOT NULL,
    email character varying(255),
    active integer DEFAULT 0 NOT NULL,
    confmail integer DEFAULT 0 NOT NULL,
    ip inet,
    confhash character varying(32)
);


ALTER TABLE public.newsletter OWNER TO postgres84;

--
-- Name: spnewsletter(integer, character varying, inet, integer, character varying); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION spnewsletter(poper integer, pusrmail character varying, pusrip inet, punsign integer, phash character varying) RETURNS newsletter
    AS $$

DECLARE
	lResult newsletter;
	lHash varchar(32);
BEGIN
	
	lHash := coalesce(pHash, md5('Unreg' || lower(pUsrMail) || current_timestamp));
	
	IF pOper = 1 THEN
		
		IF NOT EXISTS (SELECT * FROM newsletter WHERE lower(email) = lower(pUsrMail)) THEN 
			
			INSERT INTO newsletter (email, ip, tstamp, active, confmail, confhash) 
			VALUES (lower(pUsrMail), pUsrIP, now(), 1, 0, lHash);					
			
		ELSIF EXISTS (SELECT * FROM newsletter WHERE lower(email) = lower(pUsrMail)) THEN
			
			UPDATE newsletter SET 
				active = 1,
				confhash = lHash 
			WHERE lower(email) = lower(pUsrMail);
			
		END IF;
		
	ELSIF pOper = 2 THEN
	
		-- VALIDATE
		IF EXISTS (SELECT * FROM newsletter WHERE confhash = pHash) THEN
			
			UPDATE newsletter SET 
				confmail = 1,
				confhash = lHash
			WHERE confhash = pHash;
			
		ELSE 
			RAISE EXCEPTION 'Грешен код.';
		END IF;
		
	ELSIF pOper = 3 THEN
	
		-- UNSIGN
		IF EXISTS (SELECT * FROM newsletter WHERE confhash = pHash) THEN
			
			SELECT INTO lResult.email email
			FROM newsletter
			WHERE confhash = pHash;
			
			UPDATE newsletter SET 
				active = 0,
				confmail = 0,
				confhash = lHash
			WHERE confhash = pHash;
			
		ELSE 
			RAISE EXCEPTION 'Грешен код.';
		END IF;
		
	END IF;
	
	SELECT INTO lResult * 
	FROM newsletter
	WHERE
		email = pUsrMail OR confhash = lHash;
	
	RETURN lResult;
END ;

$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.spnewsletter(poper integer, pusrmail character varying, pusrip inet, punsign integer, phash character varying) OWNER TO postgres84;

--
-- Name: spobjorder(integer, integer[], integer[]); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE OR REPLACE FUNCTION spobjorder(plnid integer, pobjids integer[], pstyles integer[], pLang varchar)
  RETURNS integer AS
$$

DECLARE
	arrSize int;
	arrIter int;
	lobjid int;
	lposid int;
BEGIN
	arrSize := array_upper(pObjIDs, 1) ;
	
	IF (array_upper(pStyles, 1) <> arrSize) THEN
		RETURN 0;
	END IF;
	
	DELETE FROM listdets WHERE listnameid = pLNid AND lang LIKE pLang;
	
	arrIter := 1 ;
	lposid := 1 ;
	WHILE arrIter <= arrSize 
	LOOP
		lobjid := pObjIDs[arrIter];
		IF lobjid > 0 THEN
			INSERT INTO listdets (listnameid, posid, objid, styletype, lang) VALUES (pLNid, lposid, lobjid, pStyles[arrIter], pLang);
			lposid := lposid + 1;
		END IF;
		arrIter := arrIter + 1;
	END LOOP;
	RETURN 1;
END;
$$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;


ALTER FUNCTION public.spobjorder(plnid integer, pobjids integer[], pstyles integer[], varchar) OWNER TO postgres84;

--
-- Name: sppasswd(integer, character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION sppasswd(pid integer, poldpass character varying, pupass character varying, pupass2 character varying) RETURNS integer
    AS $$
BEGIN
	
	IF NOT EXISTS (SELECT id FROM usr WHERE id = pID AND upass = md5(pOldPass)) THEN
		RAISE EXCEPTION 'adm.sppasswd.wrongpass';
	END IF;
	
	IF (pUpass <> pUpass2) THEN
		RAISE EXCEPTION 'adm.sppasswd.passnotmatch';
	END IF;
		
	UPDATE usr SET upass = md5(pUpass) WHERE id = pID;
	
	RETURN 1;
END;

$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.sppasswd(pid integer, poldpass character varying, pupass character varying, pupass2 character varying) OWNER TO postgres84;

--
-- Name: spphotos(integer, integer, integer, integer, character varying, character varying, integer, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION spphotos(poper integer, pid integer, pstoryid integer, ppicid integer, ptitle character varying, pdescr character varying, pplace integer, pfirstphoto integer, ppos integer) RETURNS retspphotos
    AS $$
DECLARE
lResult retspPhotos;
lPos int;
rr RECORD;
BEGIN

IF pStoryID IS NULL AND pOper <> 0 THEN 
RETURN lResult;
END IF;

IF pOper = 1 THEN

IF pPos IS NULL THEN
SELECT INTO lPos coalesce((max(valint3) + 1), 1) FROM storyproperties WHERE guid = pStoryID AND propid = 2;
ELSE 

lPos := pPos + 1;
FOR rr IN SELECT valint FROM storyproperties 
WHERE guid = pStoryID AND propid = 2 AND (valint3 >= pPos OR valint3 IS NULL) AND valint <> coalesce(pPicID, pID)
ORDER BY valint3
LOOP
UPDATE storyproperties SET valint3 = lPos WHERE valint = rr.valint;
lPos := lPos + 1;
END LOOP;

lPos := pPos;

END IF;

IF pID IS NULL THEN
-- INSERT

INSERT INTO storyproperties (guid, propid, valint, valint2, valstr, valint3) 
VALUES (pStoryID, 2, pPicID, pPlace, pDescr, lPos);

ELSE
--UPDATE
IF pPicID IS NULL THEN 
UPDATE storyproperties SET
valint2 = pPlace,
valint3 = lPos,
valstr = pDescr
WHERE guid = pStoryID 
AND propid = 2 AND valint = pID;
ELSE 

DELETE FROM storyproperties WHERE guid = pStoryID 
AND propid = 2 AND valint = pID;

INSERT INTO storyproperties (guid, propid, valint, valint2, valstr, valint3) 
VALUES (pStoryID, 2, pPicID, pPlace, pDescr, lPos);

END IF;

END IF;

IF pFirstPhoto IS NOT NULL THEN
UPDATE stories SET previewpicid = coalesce(pPicID, pID) WHERE guid = pStoryID;
END IF;

END IF;

IF pOper = 3 THEN 
SELECT INTO lPos coalesce(valint3, 1) FROM storyproperties WHERE guid = pStoryID 
AND propid = 2 AND valint = pID;

DELETE FROM storyproperties WHERE guid = pStoryID 
AND propid = 2 AND valint = pID;

FOR rr IN SELECT valint FROM storyproperties 
WHERE guid = pStoryID AND propid = 2 AND (valint3 > lPos OR valint3 IS NULL)
ORDER BY valint3
LOOP
UPDATE storyproperties SET valint3 = lPos WHERE valint = rr.valint;
lPos := lPos + 1;
END LOOP;

IF EXISTS(SELECT guid FROM stories WHERE guid = pStoryID AND previewpicid = pID) THEN
UPDATE stories SET previewpicid = NULL WHERE guid = pStoryID; 
END IF;

END IF;

SELECT INTO lResult.guid, lResult.title
p.guid, p.title 
FROM photos p 
WHERE p.guid = coalesce(pPicID, pID);

SELECT INTO lResult.storyid, lResult.underpic, lResult.place, lResult.pos
sp.guid, sp.valstr, sp.valint2, sp.valint3 
FROM storyproperties sp 
WHERE sp.valint = coalesce(pPicID, pID) AND sp.propid = 2 AND sp.guid = pStoryID; 

RETURN lResult;
END;

$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.spphotos(poper integer, pid integer, pstoryid integer, ppicid integer, ptitle character varying, pdescr character varying, pplace integer, pfirstphoto integer, ppos integer) OWNER TO postgres84;

SET default_with_oids = true;

--
-- Name: secsites; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE secsites (
    id integer NOT NULL,
    url character varying(255) NOT NULL,
    name character varying(255),
    cnt integer,
    ord integer,
    "type" integer
);


ALTER TABLE public.secsites OWNER TO postgres84;

--
-- Name: spsecsites(integer, integer, character varying, character varying, integer, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION spsecsites(poper integer, pid integer, pname character varying, purl character varying, pord integer, ptype integer, pcnt integer) RETURNS secsites
    AS $$

DECLARE
lResult secsites;
BEGIN

lResult.id := pID;

IF pOper = 0 THEN
-- GET
SELECT INTO lResult * 
FROM secsites
WHERE
id = pID;

ELSIF pOper = 1 then
IF pID IS NULL THEN
-- INSERT
INSERT INTO secsites (name, url, ord, type, cnt) VALUES (pName, pUrl, pOrd, pType, pCnt);
lResult.id := currval('secsites_id_seq');

ELSE
--UPDATE
UPDATE secsites
SET
name = pName,
url = pUrl,
ord = pOrd,
cnt = pCnt,
type = pType
WHERE
id = pID;
END IF;

ELSIF pOper = 3 THEN
-- DELETE

DELETE FROM secsites WHERE id = pID;
END IF;

RETURN lResult ;
END ;

$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.spsecsites(poper integer, pid integer, pname character varying, purl character varying, pord integer, ptype integer, pcnt integer) OWNER TO postgres84;

SET default_with_oids = false;

--
-- Name: menus; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE menus (
    id integer NOT NULL,
    name character varying[] NOT NULL,
    sid integer DEFAULT 1,
    parentid integer,
    "type" integer NOT NULL,
    active integer DEFAULT 1 NOT NULL,
    ord integer DEFAULT 1 NOT NULL,
    href character varying[],
    img character varying[]
);


ALTER TABLE public.menus OWNER TO postgres84;

--
-- Name: spsitemenu(integer, integer, character varying[], integer, integer, integer, integer, integer, character varying[], character varying[]); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION spsitemenu(poper integer, pid integer, pname character varying[], psid integer, pparentid integer, ptype integer, pactive integer, pord integer, phref character varying[], pimg character varying[]) RETURNS menus
    AS $$

DECLARE
	lRes menus%ROWTYPE;
	lID integer;	
BEGIN
	lRes.id := pID;
	lRes.sid := pSID;
	
	IF pOper = 0 THEN -- GET
		SELECT INTO lRes * 
		FROM menus WHERE id = pID;
		
	
	ELSIF pOper = 1 THEN --INSERT UPDATE
		
		
		IF NOT EXISTS (SELECT * FROM menus WHERE id = pID) THEN -- INSERT
		
			IF EXISTS (SELECT * FROM menus WHERE type = 0 AND id = pparentid) THEN
				RAISE EXCEPTION 'Не можете да добавяте подменю на линк!';
			ELSE
				if (pID is not NULL) then
					INSERT INTO menus(id, name, sid, parentid, type, active, ord, href , img)
					VALUES (pid, pname, psid, pparentid, ptype, pactive, pord, phref , pimg);
					lID := pID;
				else
					INSERT INTO menus(name, sid, parentid, "type", active, ord, href , img)
					VALUES (pname, psid, pparentid, ptype, pactive, pord, phref , pimg);
					lID := currval('menus_id_seq');
				end if;
				
				lRes.id = lID;

			END IF;
		
		ELSE --UPDATE
			
			update menus
			set name=pname, sid=psid, parentid=pparentid, "type"=ptype, active=pactive, ord=pord, href=phref , img=pimg
			where id=pid;
			
		END IF;
	ELSIF pOper = 3 THEN -- DELETE
		IF EXISTS (SELECT *
			FROM menus
			where parentid=pID
		) THEN
			RAISE EXCEPTION 'Не можете да изтриете меню което има подменюта!';
		ELSE
			DELETE FROM menus WHERE id = pID;
		END IF;
	END IF;
	
	
	RETURN lRes;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.spsitemenu(poper integer, pid integer, pname character varying[], psid integer, pparentid integer, ptype integer, pactive integer, pord integer, phref character varying[], pimg character varying[]) OWNER TO postgres84;

--
-- Name: spsiterubr(integer, integer, integer, character varying[], integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION spsiterubr(poper integer, pid integer, psid integer, pname character varying[], pstate integer, pparent integer) RETURNS ret_spsiterubr
    AS $$
DECLARE
	lRes ret_spsiterubr;
	lParentPos varchar;
	lMaxPos varchar;
	lParentRoot int;
	lID int;
	lTmpRoot int;
	lTmpPos varchar;
	lOldPos varchar;
		
BEGIN
	lRes.id := pID;
	lRes.sid := pSID;
	
	IF pOper = 0 THEN -- GET
		SELECT INTO lRes.sid, lRes.name, lRes.state, lParentRoot, lParentPos
			sid, name, "state", rootnode, substring(pos from 1 for (char_length(pos) - 2)) 
		FROM rubr WHERE id = pID;
		lRes.id = pID;
		
		SELECT INTO lRes.parentnode id FROM rubr WHERE rootnode = lParentRoot AND pos = lParentPos;
	
	ELSIF pOper = 1 THEN --INSERT UPDATE
		
		-- Tezi gi iznasqm tuk shtoto se polzvat nqkoi neshta i za insert i za update
		IF pParent IS NULL THEN
			SELECT INTO lMaxPos pos FROM rubr WHERE sid = pSID AND char_length(pos) = 2 ORDER BY pos DESC LIMIT 1;
		ELSE
			SELECT INTO lParentRoot, lParentPos rootnode, pos FROM rubr WHERE id = pParent;
			SELECT INTO lOldPos pos FROM rubr WHERE id = pID;
			
			IF pParent = pID OR position(lOldPos in lParentPos) = 1 THEN
				RAISE EXCEPTION 'Не можем да местим рубрика във себе си или в неините деца!';
			END IF;
					
			SELECT INTO lMaxPos pos FROM rubr WHERE sid = pSID AND pos LIKE lParentPos || '%' ORDER BY pos DESC LIMIT 1;
		END IF;
		
		lMaxPos := ForumGetNextOrd(lMaxPos);
		IF pParent IS NOT NULL THEN
			lMaxPos := lParentPos || lMaxPos;
		END IF;
		
		lMaxPos := coalesce(lMaxPos, 'AA');
		
		IF NOT EXISTS (SELECT * FROM rubr WHERE id = pID) THEN -- INSERT
			
			if (pID is not NULL) then
				INSERT INTO rubr (id, sid, "state", pos, name, rootnode)
				VALUES (pID, pSID, pState, lMaxPos, pName, 0);
				lID := pID;
			else
				INSERT INTO rubr (sid, "state", pos, name, rootnode)
				VALUES (pSID, pState, lMaxPos, pName, 0);
				lID := currval('rubr_id_seq');
			end if;
			
			UPDATE rubr SET 
				rootnode = (case WHEN pParent IS NULL THEN lID ELSE lParentRoot end) 
			WHERE id = lID;
			
			lRes.id = lID;
			
		ELSE --UPDATE
			
			SELECT INTO lTmpPos pos FROM rubr WHERE id = pID;
			
			IF lTmpPos <> lMaxPos THEN 
				
				IF EXISTS (SELECT *
					FROM rubr r1
					JOIN rubr r2 ON r2.pos LIKE r1.pos || '%' AND r1.id <> r2.id AND r1.sid = r2.sid
					WHERE r1.id = pID
				) THEN
					UPDATE rubr SET
						pos = overlay(pos placing lMaxPos from 1 for char_length(lTmpPos)),
						rootnode = coalesce(lParentRoot, pID)
					WHERE sid = pSID AND 
						pos LIKE lTmpPos || '%' AND char_length(pos) > char_length(lTmpPos);
				END IF;				
				
				UPDATE rubr SET 
					name = pName,
					"state" = pState,
					pos = lMaxPos,
					rootnode = coalesce(lParentRoot, id)
				WHERE id = pID;
				
			ELSE 
				UPDATE rubr SET 
					name = pName,
					"state" = pState
				WHERE id = pID;
			END IF;
			
		END IF;
	ELSIF pOper = 3 THEN -- DELETE
		IF EXISTS (SELECT *
			FROM rubr r1
			JOIN rubr r2 ON r2.pos LIKE r1.pos || '%' AND r1.id <> r2.id AND r1.sid = r2.sid
			WHERE r1.id = pID
		) THEN
			RAISE EXCEPTION 'Не можете да изтриете рубрика която има подрубрики!';
		ELSE
			DELETE FROM rubr WHERE id = pID;
		END IF;
	END IF;
	
	SELECT INTO lRes.cval id FROM rubr WHERE id < 10000 ORDER BY id DESC LIMIT 1; 
	
	RETURN lRes;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.spsiterubr(poper integer, pid integer, psid integer, pname character varying[], pstate integer, pparent integer) OWNER TO postgres84;

--
-- Name: static_article; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE static_article (
    static_id integer NOT NULL,
    artname character varying,
    artid integer[],
    siteid integer
);


ALTER TABLE public.static_article OWNER TO postgres84;

--
-- Name: spstatic(integer, integer, integer[], character varying, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION spstatic(pop integer, pid integer, partnum integer[], partname character varying, psiteid integer) RETURNS static_article
    AS $$
DECLARE
lResult static_article;
BEGIN

IF (pOp = 0) THEN
	SELECT INTO lResult *
	FROM static_article
	WHERE static_id = pId;
ELSIF (pOp = 1) THEN
	IF (pId is null) THEN
		INSERT INTO static_article(artid, artname, siteid)
		VALUES(pArtNum, pArtName, pSiteId);
		lResult.static_id = currval('static_article_static_id_seq');
	ELSE
		UPDATE static_article
		SET artid = pArtNum,
		artname = pArtName,
		siteid = pSiteId
		WHERE static_id = pId;
	END IF;
ELSIF (pOp = 3) THEN
	DELETE FROM static_article WHERE static_id = pId;
END IF;

RETURN lResult;

END ;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.spstatic(pop integer, pid integer, partnum integer[], partname character varying, psiteid integer) OWNER TO postgres84;

--
-- Name: storyusage; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE storyusage (
    guid integer,
    uid integer,
    uname character varying,
    tst timestamp without time zone
);


ALTER TABLE public.storyusage OWNER TO postgres84;

--
-- Name: spstoryusage(integer, integer, character varying); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION spstoryusage(pguid integer, puid integer, puname character varying) RETURNS SETOF storyusage
    AS $$
DECLARE
lret storyusage%ROWTYPE;
lctime timestamp;
BEGIN
lctime := now();

IF EXISTS (SELECT * FROM storyusage WHERE guid = pGuid 
AND tst >= (lctime - '10 mins'::interval) 
AND uid = pUid
) THEN 
UPDATE storyusage SET tst = lctime WHERE guid = pGuid AND uid = pUid;
ELSE
INSERT INTO storyusage (guid, uid, uname, tst) 
VALUES (pGuid, pUid, pUname, lctime);
END IF;

FOR lret IN SELECT * FROM storyusage WHERE guid = pGuid AND tst >= (lctime - '10 mins'::interval)
LOOP
RETURN NEXT lret;
END LOOP;

DELETE FROM storyusage WHERE guid = pGuid AND tst < (lctime - '10 mins'::interval);

RETURN;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.spstoryusage(pguid integer, puid integer, puname character varying) OWNER TO postgres84;

--
-- Name: usr; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE usr (
    id integer NOT NULL,
    uname character varying(32) NOT NULL,
    upass character varying(32) NOT NULL,
    name character varying(255),
    usrphoto character varying,
    email character varying(64),
    phone character varying,
    state integer DEFAULT 0 NOT NULL,
    utype integer DEFAULT 0 NOT NULL,
    confhash character varying(32)
);


ALTER TABLE public.usr OWNER TO postgres84;

--
-- Name: spusr(integer, integer, character varying, character varying, character varying, character varying, character varying, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION spusr(pop integer, pid integer, puname character varying, pname character varying, pupass character varying, pemail character varying, pphone character varying, pstate integer, ptype integer) RETURNS usr
    AS $$
DECLARE
lResult usr;
BEGIN

IF (pOp = 0) THEN
SELECT INTO lResult *
FROM usr
WHERE id = pId;
ELSIF (pOp = 1) THEN

IF (pId is null) THEN
IF EXISTS (SELECT * FROM usr WHERE uname = pUname) THEN
RAISE EXCEPTION 'This user exists!';
END IF;

INSERT INTO usr(uname, name, upass, email, phone, state, utype)
VALUES(pUname, pName, md5(pUPass), pEmail, pPhone, pState, pType);
lResult.id = currval('usr_id_seq');
ELSE

IF EXISTS (SELECT * FROM usr WHERE uname = pUname AND id <> pId) THEN
RAISE EXCEPTION 'This user exists!';
END IF;

UPDATE usr SET 
name = pName,
email = pEmail,
phone = pPhone,
state = pState,
utype = pType,
upass = coalesce(md5(pUPass), upass)
WHERE id = pId;
END IF;
ELSIF (pOp = 3) THEN
DELETE FROM usr WHERE id = pId;
END IF;

RETURN lResult;

END ;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.spusr(pop integer, pid integer, puname character varying, pname character varying, pupass character varying, pemail character varying, pphone character varying, pstate integer, ptype integer) OWNER TO postgres84;

--
-- Name: storiesindexer(integer, integer, integer, text); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION storiesindexer(pguid integer, pindexer integer, pstate integer, pbody text) RETURNS integer
    AS $$
DECLARE 
lTitleV tsvector;
lContentV tsvector;
lBodyV tsvector;
BEGIN

/*
IF (pIndexer = 0) OR (pState NOT IN (3,4)) THEN
DELETE FROM storiesft WHERE guid = pGuid;
RETURN 0;
END IF;
*/

SELECT INTO lTitleV, lContentV 
setweight(to_tsvector('bg_utf8', lower(coalesce(title, ''))), 'A'),
(
setweight(to_tsvector('bg_utf8', lower(coalesce(title, ''))), 'A') || 
setweight(to_tsvector('bg_utf8', lower(coalesce(keywords, ''))), 'A') || 
setweight(to_tsvector('bg_utf8', lower(coalesce(subtitle, ''))), 'B') || 
setweight(to_tsvector('bg_utf8', lower(coalesce(nadzaglavie, ''))), 'B') || 
setweight(to_tsvector('bg_utf8', lower(coalesce(description, ''))), 'B') || 
setweight(to_tsvector('bg_utf8', lower(coalesce(author, ''))), 'B')
) 
FROM stories WHERE guid = pGuid;

lBodyV := coalesce(lContentV, '') || setweight(to_tsvector('bg_utf8', lower(coalesce(pBody, ''))), 'A');

IF EXISTS (SELECT guid FROM storiesft WHERE guid = pGuid) THEN
UPDATE storiesft SET
newstext = pBody,
title = lTitleV,
content = lContentV,
body = lBodyV 
WHERE guid = pGuid;
ELSE
INSERT INTO storiesft (guid, newstext, title, content, body) 
VALUES (pGuid, pBody, lTitleV, lContentV, lBodyV);
END IF;

RETURN 1;
END ;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.storiesindexer(pguid integer, pindexer integer, pstate integer, pbody text) OWNER TO postgres84;

--
-- Name: underforumgetmsgflathtml(integer, integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION underforumgetmsgflathtml(pdscid integer, pitemid integer) RETURNS SETOF retunderforumgetmsgflathtml
    AS $_$
	UPDATE msg SET
		views = views + 1
		FROM dsc d 
		INNER JOIN dsg g ON (d.dsgid  = g.id)
	WHERE msg.dscid = d.id AND msg.itemid = $2 AND d.id = $1 AND msg.id = msg.rootid;
		

	SELECT m.id, m.subject, m.author, m.msghtml, m.mdate, m.rootid, d.name, d.id,
		m2.subject, m2.id, m2.flags, m.flags, m.itemid, m.uid, m.uname, m.ord, m2.replies, g.name, m.points
		FROM msg m
		INNER JOIN dsc d ON (m.dscid = d.id) 
		INNER JOIN dsg g ON (d.dsgid  = g.id)
		LEFT JOIN msg m2 ON (m2.id = m.rootid AND m2.rootid = m2.id)
	WHERE m.itemid = $2 AND d.id = $1 AND m.id <> m.rootid;

$_$
    LANGUAGE sql SECURITY DEFINER;


ALTER FUNCTION public.underforumgetmsgflathtml(pdscid integer, pitemid integer) OWNER TO postgres84;

--
-- Name: updatestoriesstate(integer); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION updatestoriesstate(pguid integer) RETURNS void
    AS $$
DECLARE
BEGIN
	UPDATE stories SET state = 3 WHERE guid = pguid;
	RETURN;
END;
$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.updatestoriesstate(pguid integer) OWNER TO postgres84;

--
-- Name: userfpass(character varying); Type: FUNCTION; Schema: public; Owner: postgres84
--

CREATE FUNCTION userfpass(pemail character varying) RETURNS ret_userfpass
    AS $$
DECLARE
	lResult ret_UserFpass;
	lId int;
	lname varchar;
	ltmppass varchar;
BEGIN

	SELECT INTO lId, lname id, uname FROM usr WHERE email = pEmail;
	
	IF lname IS NOT NULL THEN 
		
		ltmppass := md5(now() || lname);
		ltmppass := substring(ltmppass from 1 for 6);
		
		--UPDATE
		UPDATE usr SET upass = md5(ltmppass) WHERE id = lId;
		SELECT INTO lResult lname, ltmppass;
		
	END IF;
	
	RETURN lResult;
END;

$$
    LANGUAGE plpgsql SECURITY DEFINER;


ALTER FUNCTION public.userfpass(pemail character varying) OWNER TO postgres84;

--
-- Name: aggr_concat(text); Type: AGGREGATE; Schema: public; Owner: postgres84
--

CREATE AGGREGATE aggr_concat(text) (
    SFUNC = public.concat,
    STYPE = text
);


ALTER AGGREGATE public.aggr_concat(text) OWNER TO postgres84;

--
-- Name: bitwise_or(integer); Type: AGGREGATE; Schema: public; Owner: postgres84
--

CREATE AGGREGATE bitwise_or(integer) (
    SFUNC = bitwise_or_for_aggregate,
    STYPE = integer
);


ALTER AGGREGATE public.bitwise_or(integer) OWNER TO postgres84;

SET default_with_oids = true;

--
-- Name: authors; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE authors (
    authors_id integer NOT NULL,
    authors_name text
);


ALTER TABLE public.authors OWNER TO postgres84;

--
-- Name: authors_authors_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres84
--

CREATE SEQUENCE authors_authors_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.authors_authors_id_seq OWNER TO postgres84;

--
-- Name: authors_authors_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres84
--

ALTER SEQUENCE authors_authors_id_seq OWNED BY authors.authors_id;


--
-- Name: authors_authors_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres84
--

SELECT pg_catalog.setval('authors_authors_id_seq', 5, true);


--
-- Name: careersattcodes; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE careersattcodes (
    guid integer NOT NULL,
    code character varying NOT NULL
);


ALTER TABLE public.careersattcodes OWNER TO postgres84;

--
-- Name: dsc; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE dsc (
    id integer NOT NULL,
    dsgid integer NOT NULL,
    siteid integer,
    itemtype integer DEFAULT 0 NOT NULL,
    name character varying(64) NOT NULL,
    ord integer,
    flags integer DEFAULT 0 NOT NULL,
    tpccount integer DEFAULT 0
);


ALTER TABLE public.dsc OWNER TO postgres84;

--
-- Name: dsc_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres84
--

CREATE SEQUENCE dsc_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.dsc_id_seq OWNER TO postgres84;

--
-- Name: dsc_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres84
--

ALTER SEQUENCE dsc_id_seq OWNED BY dsc.id;


--
-- Name: dsc_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres84
--

SELECT pg_catalog.setval('dsc_id_seq', 1, false);


--
-- Name: dsg; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE dsg (
    id integer NOT NULL,
    siteid integer NOT NULL,
    name character varying(64) NOT NULL,
    ord integer,
    dsccount integer DEFAULT 0 NOT NULL,
    flags integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.dsg OWNER TO postgres84;

--
-- Name: dsg_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres84
--

CREATE SEQUENCE dsg_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.dsg_id_seq OWNER TO postgres84;

--
-- Name: dsg_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres84
--

ALTER SEQUENCE dsg_id_seq OWNED BY dsg.id;


--
-- Name: dsg_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres84
--

SELECT pg_catalog.setval('dsg_id_seq', 1, false);


--
-- Name: listdets; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE listdets (
    listdetid integer NOT NULL,
    listnameid integer,
    posid integer,
    objid integer,
    styletype integer
);


ALTER TABLE public.listdets OWNER TO postgres84;

--
-- Name: listdets_listdetid_seq; Type: SEQUENCE; Schema: public; Owner: postgres84
--

CREATE SEQUENCE listdets_listdetid_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.listdets_listdetid_seq OWNER TO postgres84;

--
-- Name: listdets_listdetid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres84
--

ALTER SEQUENCE listdets_listdetid_seq OWNED BY listdets.listdetid;


--
-- Name: listdets_listdetid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres84
--

SELECT pg_catalog.setval('listdets_listdetid_seq', 1, false);


--
-- Name: listnames_listnameid_seq; Type: SEQUENCE; Schema: public; Owner: postgres84
--

CREATE SEQUENCE listnames_listnameid_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.listnames_listnameid_seq OWNER TO postgres84;

--
-- Name: listnames_listnameid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres84
--

ALTER SEQUENCE listnames_listnameid_seq OWNED BY listnames.listnameid;


--
-- Name: listnames_listnameid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres84
--

SELECT pg_catalog.setval('listnames_listnameid_seq', 1, false);


--
-- Name: menus_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres84
--

CREATE SEQUENCE menus_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.menus_id_seq OWNER TO postgres84;

--
-- Name: menus_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres84
--

ALTER SEQUENCE menus_id_seq OWNED BY menus.id;


--
-- Name: menus_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres84
--

SELECT pg_catalog.setval('menus_id_seq', 1, false);


--
-- Name: messaging; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE messaging (
    id integer NOT NULL,
    mfrom character varying NOT NULL,
    mto character varying NOT NULL,
    subject character varying NOT NULL,
    filename character varying NOT NULL,
    state integer DEFAULT 0 NOT NULL,
    opid integer DEFAULT 0 NOT NULL,
    senddate timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL
);


ALTER TABLE public.messaging OWNER TO postgres84;

--
-- Name: messaging_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres84
--

CREATE SEQUENCE messaging_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.messaging_id_seq OWNER TO postgres84;

--
-- Name: messaging_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres84
--

ALTER SEQUENCE messaging_id_seq OWNED BY messaging.id;


--
-- Name: messaging_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres84
--

SELECT pg_catalog.setval('messaging_id_seq', 1, false);


SET default_with_oids = false;

--
-- Name: metadata; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE metadata (
    id integer NOT NULL,
    title character varying,
    description character varying,
    keywords character varying
);


ALTER TABLE public.metadata OWNER TO postgres84;

--
-- Name: metadata_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres84
--

CREATE SEQUENCE metadata_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.metadata_id_seq OWNER TO postgres84;

--
-- Name: metadata_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres84
--

ALTER SEQUENCE metadata_id_seq OWNED BY metadata.id;


--
-- Name: metadata_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres84
--

SELECT pg_catalog.setval('metadata_id_seq', 11, true);


SET default_with_oids = true;

--
-- Name: msg; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE msg (
    id integer NOT NULL,
    dscid integer NOT NULL,
    itemid integer,
    author character varying(128),
    subject character varying NOT NULL,
    msg text NOT NULL,
    senderip inet,
    mdate timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    rootid integer,
    ord character varying,
    points integer DEFAULT 0 NOT NULL,
    uid integer,
    flags integer DEFAULT 0 NOT NULL,
    replies integer DEFAULT 0,
    views integer DEFAULT 0,
    lastmoddate timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    msghtml text,
    uname character varying(128),
    fighttype integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.msg OWNER TO postgres84;

--
-- Name: msg_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres84
--

CREATE SEQUENCE msg_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.msg_id_seq OWNER TO postgres84;

--
-- Name: msg_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres84
--

ALTER SEQUENCE msg_id_seq OWNED BY msg.id;


--
-- Name: msg_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres84
--

SELECT pg_catalog.setval('msg_id_seq', 19, true);


--
-- Name: msgroot; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE msgroot (
    msgid integer NOT NULL,
    dscid integer,
    itemid integer,
    replies integer,
    lastpostid integer,
    lastpost timestamp without time zone
);


ALTER TABLE public.msgroot OWNER TO postgres84;

--
-- Name: newsletter_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres84
--

CREATE SEQUENCE newsletter_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.newsletter_id_seq OWNER TO postgres84;

--
-- Name: newsletter_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres84
--

ALTER SEQUENCE newsletter_id_seq OWNED BY newsletter.id;


--
-- Name: newsletter_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres84
--

SELECT pg_catalog.setval('newsletter_id_seq', 1, false);


SET default_with_oids = false;

--
-- Name: pans; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE pans (
    id integer NOT NULL,
    pollid integer NOT NULL,
    ans character varying(128) NOT NULL,
    votes integer DEFAULT 0 NOT NULL,
    flags integer DEFAULT 0 NOT NULL,
    ord integer
);


ALTER TABLE public.pans OWNER TO postgres84;

--
-- Name: pans_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres84
--

CREATE SEQUENCE pans_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.pans_id_seq OWNER TO postgres84;

--
-- Name: pans_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres84
--

ALTER SEQUENCE pans_id_seq OWNED BY pans.id;


--
-- Name: pans_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres84
--

SELECT pg_catalog.setval('pans_id_seq', 2, true);


--
-- Name: pauthors; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE pauthors (
    pauthors_id integer NOT NULL,
    pauthors_name text
);


ALTER TABLE public.pauthors OWNER TO postgres84;

--
-- Name: pauthors_pauthors_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres84
--

CREATE SEQUENCE pauthors_pauthors_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.pauthors_pauthors_id_seq OWNER TO postgres84;

--
-- Name: pauthors_pauthors_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres84
--

ALTER SEQUENCE pauthors_pauthors_id_seq OWNED BY pauthors.pauthors_id;


--
-- Name: pauthors_pauthors_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres84
--

SELECT pg_catalog.setval('pauthors_pauthors_id_seq', 1, true);


SET default_with_oids = true;

--
-- Name: stories; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE stories (
    guid integer NOT NULL,
    lang character varying(3) NOT NULL,
    title character varying NOT NULL,
    link character varying,
    description character varying,
    pubdate timestamp without time zone,
    author character varying(255),
    createdate timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    lastmod timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    createuid integer NOT NULL,
    keywords character varying,
    previewpicid integer,
    state integer DEFAULT 0 NOT NULL,
    subtitle character varying,
    primarysite integer,
    dnimp_itemid integer,
    nadzaglavie character varying,
    euimp_itemid integer,
    showforum smallint DEFAULT 1,
    storytype integer DEFAULT 0,
    icons integer DEFAULT 0,
    fight integer DEFAULT 0 NOT NULL,
    themes character varying
);


ALTER TABLE public.stories OWNER TO postgres84;

--
-- Name: stories_guid_seq; Type: SEQUENCE; Schema: public; Owner: postgres84
--

CREATE SEQUENCE stories_guid_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.stories_guid_seq OWNER TO postgres84;

--
-- Name: stories_guid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres84
--

ALTER SEQUENCE stories_guid_seq OWNED BY stories.guid;


--
-- Name: stories_guid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres84
--

SELECT pg_catalog.setval('stories_guid_seq', 24, true);


--
-- Name: photos; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE photos (
    guid integer DEFAULT nextval('stories_guid_seq'::regclass) NOT NULL,
    lang character varying(3) NOT NULL,
    title character varying NOT NULL,
    link character varying,
    description character varying,
    pubdate timestamp without time zone,
    author character varying(255),
    createdate timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    lastmod timestamp without time zone DEFAULT ('now'::text)::timestamp(6) with time zone NOT NULL,
    createuid integer NOT NULL,
    keywords character varying,
    orientation integer,
    imgname character varying,
    ftype integer DEFAULT 0,
    "access" integer DEFAULT 0 NOT NULL,
    "type" integer,
    source integer,
    filenameupl character varying,
    length integer,
    dim_x integer,
    dim_y integer,
    mediasize integer,
    mimetype character varying(255)
);


ALTER TABLE public.photos OWNER TO postgres84;

--
-- Name: photos_guid_seq; Type: SEQUENCE; Schema: public; Owner: postgres84
--

CREATE SEQUENCE photos_guid_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.photos_guid_seq OWNER TO postgres84;

--
-- Name: photos_guid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres84
--

ALTER SEQUENCE photos_guid_seq OWNED BY photos.guid;


--
-- Name: photos_guid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres84
--

SELECT pg_catalog.setval('photos_guid_seq', 1, false);


SET default_with_oids = false;

--
-- Name: poll; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE poll (
    id integer NOT NULL,
    siteid integer NOT NULL,
    question character varying(128) NOT NULL,
    startdate timestamp without time zone,
    enddate timestamp without time zone,
    flags integer DEFAULT 0 NOT NULL,
    pos integer,
    description character varying,
    showforum integer,
    usrid integer,
    lang integer,
    active integer,
    status integer
);


ALTER TABLE public.poll OWNER TO postgres84;

--
-- Name: poll_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres84
--

CREATE SEQUENCE poll_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.poll_id_seq OWNER TO postgres84;

--
-- Name: poll_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres84
--

ALTER SEQUENCE poll_id_seq OWNED BY poll.id;


--
-- Name: poll_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres84
--

SELECT pg_catalog.setval('poll_id_seq', 5, true);


--
-- Name: pollogs; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE pollogs (
    pollid integer NOT NULL,
    pansid integer NOT NULL,
    ip inet,
    tstamp timestamp without time zone DEFAULT now()
);


ALTER TABLE public.pollogs OWNER TO postgres84;

--
-- Name: propnames; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE propnames (
    propid integer NOT NULL,
    propname character varying(255) NOT NULL,
    propsite integer
);


ALTER TABLE public.propnames OWNER TO postgres84;

--
-- Name: propnames_propid_seq; Type: SEQUENCE; Schema: public; Owner: postgres84
--

CREATE SEQUENCE propnames_propid_seq
    START WITH 16
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.propnames_propid_seq OWNER TO postgres84;

--
-- Name: propnames_propid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres84
--

ALTER SEQUENCE propnames_propid_seq OWNED BY propnames.propid;


--
-- Name: propnames_propid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres84
--

SELECT pg_catalog.setval('propnames_propid_seq', 16, false);


SET default_with_oids = true;

--
-- Name: rubr; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE rubr (
    id integer NOT NULL,
    sid integer NOT NULL,
    state integer DEFAULT 0 NOT NULL,
    rootnode integer NOT NULL,
    pos character varying NOT NULL,
    name character varying[] NOT NULL
);


ALTER TABLE public.rubr OWNER TO postgres84;

--
-- Name: rubr_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres84
--

CREATE SEQUENCE rubr_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.rubr_id_seq OWNER TO postgres84;

--
-- Name: rubr_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres84
--

ALTER SEQUENCE rubr_id_seq OWNED BY rubr.id;


--
-- Name: rubr_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres84
--

SELECT pg_catalog.setval('rubr_id_seq', 1, true);


--
-- Name: secgrp; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE secgrp (
    id integer NOT NULL,
    name character varying(127) NOT NULL
);


ALTER TABLE public.secgrp OWNER TO postgres84;

--
-- Name: secgrp_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres84
--

CREATE SEQUENCE secgrp_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.secgrp_id_seq OWNER TO postgres84;

--
-- Name: secgrp_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres84
--

ALTER SEQUENCE secgrp_id_seq OWNED BY secgrp.id;


--
-- Name: secgrp_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres84
--

SELECT pg_catalog.setval('secgrp_id_seq', 2, true);


--
-- Name: secgrpacc; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE secgrpacc (
    gid integer NOT NULL,
    sid integer NOT NULL,
    "type" integer NOT NULL
);


ALTER TABLE public.secgrpacc OWNER TO postgres84;

--
-- Name: secgrpdet; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE secgrpdet (
    gid integer NOT NULL,
    uid integer NOT NULL
);


ALTER TABLE public.secgrpdet OWNER TO postgres84;

--
-- Name: secsites_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres84
--

CREATE SEQUENCE secsites_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.secsites_id_seq OWNER TO postgres84;

--
-- Name: secsites_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres84
--

ALTER SEQUENCE secsites_id_seq OWNED BY secsites.id;


--
-- Name: secsites_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres84
--

SELECT pg_catalog.setval('secsites_id_seq', 30, true);


--
-- Name: sid1storyprops; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE sid1storyprops (
    guid integer,
    priority integer,
    viewed integer
);


ALTER TABLE public.sid1storyprops OWNER TO postgres84;

--
-- Name: sites; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE sites (
    guid integer NOT NULL,
    name character varying(255),
    storyurl character varying,
    siteurl character varying
);


ALTER TABLE public.sites OWNER TO postgres84;

--
-- Name: sites_guid_seq; Type: SEQUENCE; Schema: public; Owner: postgres84
--

CREATE SEQUENCE sites_guid_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.sites_guid_seq OWNER TO postgres84;

--
-- Name: sites_guid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres84
--

ALTER SEQUENCE sites_guid_seq OWNED BY sites.guid;


--
-- Name: sites_guid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres84
--

SELECT pg_catalog.setval('sites_guid_seq', 1, true);


--
-- Name: static_article_static_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres84
--

CREATE SEQUENCE static_article_static_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.static_article_static_id_seq OWNER TO postgres84;

--
-- Name: static_article_static_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres84
--

ALTER SEQUENCE static_article_static_id_seq OWNED BY static_article.static_id;


--
-- Name: static_article_static_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres84
--

SELECT pg_catalog.setval('static_article_static_id_seq', 3, true);


SET default_with_oids = false;

--
-- Name: storiesft; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE storiesft (
    guid integer NOT NULL,
    newstext text,
    title tsvector NOT NULL,
    content tsvector,
    body tsvector
);


ALTER TABLE public.storiesft OWNER TO postgres84;

--
-- Name: storychangelog; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE storychangelog (
    guid integer NOT NULL,
    modtime timestamp without time zone,
    userid integer,
    status integer,
    description character varying,
    init integer
);


ALTER TABLE public.storychangelog OWNER TO postgres84;

SET default_with_oids = true;

--
-- Name: storyproperties; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE storyproperties (
    guid integer NOT NULL,
    propid integer NOT NULL,
    valstr character varying,
    valint integer,
    valint2 integer,
    valint3 integer,
    valstr2 character varying,
    lang character varying(3)
);


ALTER TABLE public.storyproperties OWNER TO postgres84;

SET default_with_oids = false;

--
-- Name: transliteration_words; Type: TABLE; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE TABLE transliteration_words (
    id integer NOT NULL,
    word_bg character varying NOT NULL,
    word_en character varying NOT NULL
);


ALTER TABLE public.transliteration_words OWNER TO postgres84;

--
-- Name: transliteration_words_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres84
--

CREATE SEQUENCE transliteration_words_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.transliteration_words_id_seq OWNER TO postgres84;

--
-- Name: transliteration_words_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres84
--

ALTER SEQUENCE transliteration_words_id_seq OWNED BY transliteration_words.id;


--
-- Name: transliteration_words_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres84
--

SELECT pg_catalog.setval('transliteration_words_id_seq', 4, true);


--
-- Name: usr_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres84
--

CREATE SEQUENCE usr_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.usr_id_seq OWNER TO postgres84;

--
-- Name: usr_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres84
--

ALTER SEQUENCE usr_id_seq OWNED BY usr.id;


--
-- Name: usr_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres84
--

SELECT pg_catalog.setval('usr_id_seq', 1, true);


--
-- Name: authors_id; Type: DEFAULT; Schema: public; Owner: postgres84
--

ALTER TABLE authors ALTER COLUMN authors_id SET DEFAULT nextval('authors_authors_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres84
--

ALTER TABLE dsc ALTER COLUMN id SET DEFAULT nextval('dsc_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres84
--

ALTER TABLE dsg ALTER COLUMN id SET DEFAULT nextval('dsg_id_seq'::regclass);


--
-- Name: listdetid; Type: DEFAULT; Schema: public; Owner: postgres84
--

ALTER TABLE listdets ALTER COLUMN listdetid SET DEFAULT nextval('listdets_listdetid_seq'::regclass);


--
-- Name: listnameid; Type: DEFAULT; Schema: public; Owner: postgres84
--

ALTER TABLE listnames ALTER COLUMN listnameid SET DEFAULT nextval('listnames_listnameid_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres84
--

ALTER TABLE menus ALTER COLUMN id SET DEFAULT nextval('menus_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres84
--

ALTER TABLE messaging ALTER COLUMN id SET DEFAULT nextval('messaging_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres84
--

ALTER TABLE metadata ALTER COLUMN id SET DEFAULT nextval('metadata_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres84
--

ALTER TABLE msg ALTER COLUMN id SET DEFAULT nextval('msg_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres84
--

ALTER TABLE newsletter ALTER COLUMN id SET DEFAULT nextval('newsletter_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres84
--

ALTER TABLE pans ALTER COLUMN id SET DEFAULT nextval('pans_id_seq'::regclass);


--
-- Name: pauthors_id; Type: DEFAULT; Schema: public; Owner: postgres84
--

ALTER TABLE pauthors ALTER COLUMN pauthors_id SET DEFAULT nextval('pauthors_pauthors_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres84
--

ALTER TABLE poll ALTER COLUMN id SET DEFAULT nextval('poll_id_seq'::regclass);


--
-- Name: propid; Type: DEFAULT; Schema: public; Owner: postgres84
--

ALTER TABLE propnames ALTER COLUMN propid SET DEFAULT nextval('propnames_propid_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres84
--

ALTER TABLE rubr ALTER COLUMN id SET DEFAULT nextval('rubr_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres84
--

ALTER TABLE secgrp ALTER COLUMN id SET DEFAULT nextval('secgrp_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres84
--

ALTER TABLE secsites ALTER COLUMN id SET DEFAULT nextval('secsites_id_seq'::regclass);


--
-- Name: guid; Type: DEFAULT; Schema: public; Owner: postgres84
--

ALTER TABLE sites ALTER COLUMN guid SET DEFAULT nextval('sites_guid_seq'::regclass);


--
-- Name: static_id; Type: DEFAULT; Schema: public; Owner: postgres84
--

ALTER TABLE static_article ALTER COLUMN static_id SET DEFAULT nextval('static_article_static_id_seq'::regclass);


--
-- Name: guid; Type: DEFAULT; Schema: public; Owner: postgres84
--

ALTER TABLE stories ALTER COLUMN guid SET DEFAULT nextval('stories_guid_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres84
--

ALTER TABLE transliteration_words ALTER COLUMN id SET DEFAULT nextval('transliteration_words_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres84
--

ALTER TABLE usr ALTER COLUMN id SET DEFAULT nextval('usr_id_seq'::regclass);

--
--listdets -> opraven e problema s ezika za naredenite spisyci
--

ALTER TABLE listdets ADD COLUMN lang varchar(3) NOT NULL;

--
-- Data for Name: authors; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY authors (authors_id, authors_name) FROM stdin;
\.


--
-- Data for Name: careersattcodes; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY careersattcodes (guid, code) FROM stdin;
\.


--
-- Data for Name: dsc; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY dsc (id, dsgid, siteid, itemtype, name, ord, flags, tpccount) FROM stdin;
\.


--
-- Data for Name: dsg; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY dsg (id, siteid, name, ord, dsccount, flags) FROM stdin;
\.


--
-- Data for Name: languages; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY languages (langid, code, name) FROM stdin;
1	bg	Български
2	en	English
\.


--
-- Data for Name: listdets; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY listdets (listdetid, listnameid, posid, objid, styletype) FROM stdin;
\.


--
-- Data for Name: listnames; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY listnames (listnameid, name, objtype, sid) FROM stdin;
\.


--
-- Data for Name: menus; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY menus (id, name, sid, parentid, "type", active, ord, href, img) FROM stdin;
\.


--
-- Data for Name: messaging; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY messaging (id, mfrom, mto, subject, filename, state, opid, senddate) FROM stdin;
\.


--
-- Data for Name: metadata; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY metadata (id, title, description, keywords) FROM stdin;
\.


--
-- Data for Name: msg; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY msg (id, dscid, itemid, author, subject, msg, senderip, mdate, rootid, ord, points, uid, flags, replies, views, lastmoddate, msghtml, uname, fighttype) FROM stdin;
\.


--
-- Data for Name: msgroot; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY msgroot (msgid, dscid, itemid, replies, lastpostid, lastpost) FROM stdin;
\.


--
-- Data for Name: newsletter; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY newsletter (id, tstamp, email, active, confmail, ip, confhash) FROM stdin;
\.


--
-- Data for Name: pans; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY pans (id, pollid, ans, votes, flags, ord) FROM stdin;
\.


--
-- Data for Name: pauthors; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY pauthors (pauthors_id, pauthors_name) FROM stdin;
\.


--
-- Data for Name: photos; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY photos (guid, lang, title, link, description, pubdate, author, createdate, lastmod, createuid, keywords, orientation, imgname, ftype, "access", "type", source, filenameupl, length, dim_x, dim_y, mediasize, mimetype) FROM stdin;
\.


--
-- Data for Name: poll; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY poll (id, siteid, question, startdate, enddate, flags, pos, description, showforum, usrid, lang, active, status) FROM stdin;
\.


--
-- Data for Name: pollogs; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY pollogs (pollid, pansid, ip, tstamp) FROM stdin;
\.


--
-- Data for Name: propnames; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY propnames (propid, propname, propsite) FROM stdin;
1	Вторични Рубрики	\N
2	Снимки	\N
3	Свързана статия	\N
4	Главна рубрика	\N
5	Attachment	\N
6	Фотогалерия	\N
7	Категории	\N
8	MD5 на снимка	\N
9	Link	\N
10	Индустрия	\N
11	Логo	\N
12	audio	\N
13	video	\N
14	Keywords	\N
15	Themes	\N
16	Продукти	\N
\.


--
-- Data for Name: rubr; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY rubr (id, sid, state, rootnode, pos, name) FROM stdin;
\.


--
-- Data for Name: secgrp; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY secgrp (id, name) FROM stdin;
1	Default
2	Admin
\.


--
-- Data for Name: secgrpacc; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY secgrpacc (gid, sid, "type") FROM stdin;
1	1	1
1	2	1
1	19	1
1	20	1
2	1	6
2	2	6
2	3	6
2	4	6
2	5	6
2	6	6
2	7	6
2	8	6
2	9	6
2	10	6
2	11	6
2	12	6
2	13	6
2	14	6
2	15	6
2	16	6
2	17	6
2	18	6
2	19	6
2	20	6
2	21	6
2	22	6
2	23	6
2	24	6
2	25	6
2	26	6
2	27	6
2	28	6
2	29	6
2	30	6
\.


--
-- Data for Name: secgrpdet; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY secgrpdet (gid, uid) FROM stdin;
1	1
2	1
\.


--
-- Data for Name: secsites; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY secsites (id, url, name, cnt, ord, "type") FROM stdin;
1	/	*Home	1	1	1
2	/login/	*Login	2	2	1
3	*sid1	*sid1	0	0	1
4	/options/	Настройки	2	1	1
5	/options/languages/	Езици	3	1	1
6	/options/menus/	Менюта (фронтсайт)	3	2	1
11	/resources/	Ресурси	2	2	1
12	/resources/editrubr/	Рубрики	3	1	1
13	/resources/stories/	Статии	3	2	1
14	/resources/static/	Статични страници	3	3	1
15	/resources/lists/	Наредени списъци	3	4	1
16	/resources/photos/	Снимки	3	5	1
17	/resources/attachments/	Файлове	3	6	1
18	/resources/media/	Мултимедия	3	7	1
19	/profile/	Профил	2	3	1
20	/profile/passwd/	Смяна на парола	3	1	1
21	/resources/polls/	Анкети	3	8	1
22	/resources/bulletin/	Бюлетин	3	9	1
23	/resources/bulletin/send/	Изпрати бюлетин	4	1	1
24	/resources/bulletin/subscribers/	Абонати	4	2	1
25	/resources/forum/	Форум	3	10	1
26	/resources/gallery/	Галерии	3	3	1
27	/options/administration/	Администрация	3	1	1
8	/options/administration/usrs/	Потребители	4	2	1
9	/options/administration/grp/	Групи	4	3	1
10	/options/administration/permitions/	Права	4	4	1
7	/options/administration/secsites/	Менюта (администрация)	4	1	1
28	/options/transliteration_words/	Транслитерация - изключения	3	5	1
29	/options/metadata/	Метаданни	3	6	1
30	/resources/video/	Видео	3	12	1
\.


--
-- Data for Name: sid1storyprops; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY sid1storyprops (guid, priority, viewed) FROM stdin;
\.


--
-- Data for Name: sites; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY sites (guid, name, storyurl, siteurl) FROM stdin;
1	Etaligent.NET	http://www.etaligent.net	http://www.etaligent.net/show.php
\.


--
-- Data for Name: static_article; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY static_article (static_id, artname, artid, siteid) FROM stdin;
\.


--
-- Data for Name: stories; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY stories (guid, lang, title, link, description, pubdate, author, createdate, lastmod, createuid, keywords, previewpicid, state, subtitle, primarysite, dnimp_itemid, nadzaglavie, euimp_itemid, showforum, storytype, icons, fight, themes) FROM stdin;
\.


--
-- Data for Name: storiesft; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY storiesft (guid, newstext, title, content, body) FROM stdin;
\.


--
-- Data for Name: storychangelog; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY storychangelog (guid, modtime, userid, status, description, init) FROM stdin;
\.


--
-- Data for Name: storyproperties; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY storyproperties (guid, propid, valstr, valint, valint2, valint3, valstr2, lang) FROM stdin;
\.


--
-- Data for Name: storyusage; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY storyusage (guid, uid, uname, tst) FROM stdin;
\.


--
-- Data for Name: transliteration_words; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY transliteration_words (id, word_bg, word_en) FROM stdin;
\.


--
-- Data for Name: usr; Type: TABLE DATA; Schema: public; Owner: postgres84
--

COPY usr (id, uname, upass, name, usrphoto, email, phone, state, utype, confhash) FROM stdin;
1	admin	f2e6d4490f1bc8ee303eb943f2a99984	Admin Admin	\N	\N	\N	1	0	\N
\.


--
-- Name: authors_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY authors
    ADD CONSTRAINT authors_pkey PRIMARY KEY (authors_id);


--
-- Name: code; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY careersattcodes
    ADD CONSTRAINT code PRIMARY KEY (code);


--
-- Name: dsc_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY dsc
    ADD CONSTRAINT dsc_pkey PRIMARY KEY (id);


--
-- Name: dsg_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY dsg
    ADD CONSTRAINT dsg_pkey PRIMARY KEY (id);


--
-- Name: languages_langid_key; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_langid_key UNIQUE (langid);


--
-- Name: languages_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_pkey PRIMARY KEY (code);


--
-- Name: listdets_listnameid_key1; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY listdets
    ADD CONSTRAINT listdets_listnameid_key1 UNIQUE (posid, objid);

--
-- Name: listdets_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY listdets
    ADD CONSTRAINT listdets_pkey PRIMARY KEY (listdetid);


--
-- Name: listnames_name_key; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY listnames
    ADD CONSTRAINT listnames_name_key UNIQUE (name);


--
-- Name: listnames_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY listnames
    ADD CONSTRAINT listnames_pkey PRIMARY KEY (listnameid);


--
-- Name: menus_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY menus
    ADD CONSTRAINT menus_pkey PRIMARY KEY (id);


--
-- Name: messaging_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY messaging
    ADD CONSTRAINT messaging_pkey PRIMARY KEY (id);


--
-- Name: metadata_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY metadata
    ADD CONSTRAINT metadata_pkey PRIMARY KEY (id);


--
-- Name: msg_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY msg
    ADD CONSTRAINT msg_pkey PRIMARY KEY (id);


--
-- Name: msgroot_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY msgroot
    ADD CONSTRAINT msgroot_pkey PRIMARY KEY (msgid);


--
-- Name: newsletter_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY newsletter
    ADD CONSTRAINT newsletter_pkey PRIMARY KEY (id);


--
-- Name: pans_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY pans
    ADD CONSTRAINT pans_pkey PRIMARY KEY (id);


--
-- Name: pauthors_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY pauthors
    ADD CONSTRAINT pauthors_pkey PRIMARY KEY (pauthors_id);

--
-- Name: photos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY photos
    ADD CONSTRAINT photos_pkey PRIMARY KEY (guid);

ALTER TABLE photos CLUSTER ON photos_pkey;


--
-- Name: poll_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY poll
    ADD CONSTRAINT poll_pkey PRIMARY KEY (id);


--
-- Name: propnames_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY propnames
    ADD CONSTRAINT propnames_pkey PRIMARY KEY (propid);


--
-- Name: rubr_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY rubr
    ADD CONSTRAINT rubr_pkey PRIMARY KEY (id);


--
-- Name: secgrp_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY secgrp
    ADD CONSTRAINT secgrp_pkey PRIMARY KEY (id);


--
-- Name: secsites_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY secsites
    ADD CONSTRAINT secsites_pkey PRIMARY KEY (id);


--
-- Name: sites_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY sites
    ADD CONSTRAINT sites_pkey PRIMARY KEY (guid);


--
-- Name: static_article_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY static_article
    ADD CONSTRAINT static_article_pkey PRIMARY KEY (static_id);


--
-- Name: stories_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY stories
    ADD CONSTRAINT stories_pkey PRIMARY KEY (guid);

ALTER TABLE stories CLUSTER ON stories_pkey;


--
-- Name: storiesft_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY storiesft
    ADD CONSTRAINT storiesft_pkey PRIMARY KEY (guid);


--
-- Name: transliteration_words_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY transliteration_words
    ADD CONSTRAINT transliteration_words_pkey PRIMARY KEY (id);


--
-- Name: usr_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres84; Tablespace: 
--

ALTER TABLE ONLY usr
    ADD CONSTRAINT usr_pkey PRIMARY KEY (id);


--
-- Name: idx_dsc_dsgid; Type: INDEX; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE INDEX idx_dsc_dsgid ON dsc USING btree (dsgid);


--
-- Name: idx_dsc_siteid; Type: INDEX; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE INDEX idx_dsc_siteid ON dsc USING btree (siteid);


--
-- Name: idx_dsg_siteid; Type: INDEX; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE INDEX idx_dsg_siteid ON dsg USING btree (siteid);


--
-- Name: idx_msg_dscid; Type: INDEX; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE INDEX idx_msg_dscid ON msg USING btree (dscid);


--
-- Name: idx_msg_itemidord; Type: INDEX; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE INDEX idx_msg_itemidord ON msg USING btree (itemid, ord);


--
-- Name: idx_msg_ord; Type: INDEX; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE INDEX idx_msg_ord ON msg USING btree (ord);


--
-- Name: idx_msg_rootid; Type: INDEX; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE INDEX idx_msg_rootid ON msg USING btree (rootid);


--
-- Name: idx_msgroot_itemid; Type: INDEX; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE INDEX idx_msgroot_itemid ON msgroot USING btree (itemid);


--
-- Name: idx_stories_pubdate; Type: INDEX; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE INDEX idx_stories_pubdate ON stories USING btree (pubdate);


--
-- Name: idx_stories_pubdatedate; Type: INDEX; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE INDEX idx_stories_pubdatedate ON stories USING btree (((pubdate)::date));


--
-- Name: idx_stories_state; Type: INDEX; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE INDEX idx_stories_state ON stories USING btree (state);


--
-- Name: idx_storyproperties_guid; Type: INDEX; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE INDEX idx_storyproperties_guid ON storyproperties USING btree (guid);

ALTER TABLE storyproperties CLUSTER ON idx_storyproperties_guid;


--
-- Name: idx_storyproperties_propid_valint; Type: INDEX; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE INDEX idx_storyproperties_propid_valint ON storyproperties USING btree (propid, valint);


--
-- Name: idxftbody_idx; Type: INDEX; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE INDEX idxftbody_idx ON storiesft USING gist (body);


--
-- Name: idxftcontent_idx; Type: INDEX; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE INDEX idxftcontent_idx ON storiesft USING gist (content);


--
-- Name: idxfttitle_idx; Type: INDEX; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE INDEX idxfttitle_idx ON storiesft USING gist (title);


--
-- Name: msg_itemid_idx; Type: INDEX; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE INDEX msg_itemid_idx ON msg USING btree (itemid);


--
-- Name: secgrpacc_pkey; Type: INDEX; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE UNIQUE INDEX secgrpacc_pkey ON secgrpacc USING btree (gid, sid);


--
-- Name: secgrpdet_pkey; Type: INDEX; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE UNIQUE INDEX secgrpdet_pkey ON secgrpdet USING btree (gid, uid);


--
-- Name: stories_preview_idx; Type: INDEX; Schema: public; Owner: postgres84; Tablespace: 
--

CREATE INDEX stories_preview_idx ON stories USING btree (previewpicid);


--
-- Name: msgroot_upd; Type: TRIGGER; Schema: public; Owner: postgres84
--

CREATE TRIGGER msgroot_upd
    AFTER INSERT OR UPDATE ON msg
    FOR EACH ROW
    EXECUTE PROCEDURE msgroot_upd();


--
-- Name: dsc_dsgid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres84
--

ALTER TABLE ONLY dsc
    ADD CONSTRAINT dsc_dsgid_fkey FOREIGN KEY (dsgid) REFERENCES dsg(id);


--
-- Name: listdets_listnameid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres84
--

ALTER TABLE ONLY listdets
    ADD CONSTRAINT listdets_listnameid_fkey FOREIGN KEY (listnameid) REFERENCES listnames(listnameid);


--
-- Name: listnames_sid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres84
--

ALTER TABLE ONLY listnames
    ADD CONSTRAINT listnames_sid_fkey FOREIGN KEY (sid) REFERENCES sites(guid);


--
-- Name: pans_pollid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres84
--

ALTER TABLE ONLY pans
    ADD CONSTRAINT pans_pollid_fkey FOREIGN KEY (pollid) REFERENCES poll(id) ON UPDATE CASCADE;


--
-- Name: poll_lang_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres84
--

ALTER TABLE ONLY poll
    ADD CONSTRAINT poll_lang_fkey FOREIGN KEY (lang) REFERENCES languages(langid);


--
-- Name: poll_siteid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres84
--

ALTER TABLE ONLY poll
    ADD CONSTRAINT poll_siteid_fkey FOREIGN KEY (siteid) REFERENCES sites(guid);


--
-- Name: poll_usrid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres84
--

ALTER TABLE ONLY poll
    ADD CONSTRAINT poll_usrid_fkey FOREIGN KEY (usrid) REFERENCES usr(id);


--
-- Name: pollogs_pansid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres84
--

ALTER TABLE ONLY pollogs
    ADD CONSTRAINT pollogs_pansid_fkey FOREIGN KEY (pansid) REFERENCES pans(id) ON UPDATE CASCADE;


--
-- Name: pollogs_pollid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres84
--

ALTER TABLE ONLY pollogs
    ADD CONSTRAINT pollogs_pollid_fkey FOREIGN KEY (pollid) REFERENCES poll(id) ON UPDATE CASCADE;


--
-- Name: static_article_siteid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres84
--

ALTER TABLE ONLY static_article
    ADD CONSTRAINT static_article_siteid_fkey FOREIGN KEY (siteid) REFERENCES sites(guid);


--
-- Name: stories_lang_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres84
--

ALTER TABLE ONLY stories
    ADD CONSTRAINT stories_lang_fkey FOREIGN KEY (lang) REFERENCES languages(code);


--
-- Name: storyproperties_propid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres84
--

ALTER TABLE ONLY storyproperties
    ADD CONSTRAINT storyproperties_propid_fkey FOREIGN KEY (propid) REFERENCES propnames(propid);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres84
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres84;
GRANT ALL ON SCHEMA public TO postgres84;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- Name: addatttostory(integer, integer, character varying, integer, character varying); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION addatttostory(pattid integer, pstoryid integer, ptxt character varying, del integer, pextratxt character varying) FROM PUBLIC;
REVOKE ALL ON FUNCTION addatttostory(pattid integer, pstoryid integer, ptxt character varying, del integer, pextratxt character varying) FROM postgres84;
GRANT ALL ON FUNCTION addatttostory(pattid integer, pstoryid integer, ptxt character varying, del integer, pextratxt character varying) TO postgres84;
GRANT ALL ON FUNCTION addatttostory(pattid integer, pstoryid integer, ptxt character varying, del integer, pextratxt character varying) TO iusrpmt;


--
-- Name: listnames; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE listnames FROM PUBLIC;
REVOKE ALL ON TABLE listnames FROM postgres84;
GRANT ALL ON TABLE listnames TO postgres84;
GRANT SELECT ON TABLE listnames TO iusrpmt;


--
-- Name: addlist(integer, integer, character varying, integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION addlist(poper integer, plnid integer, pname character varying, pobjtype integer, psid integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION addlist(poper integer, plnid integer, pname character varying, pobjtype integer, psid integer) FROM postgres84;
GRANT ALL ON FUNCTION addlist(poper integer, plnid integer, pname character varying, pobjtype integer, psid integer) TO postgres84;
GRANT ALL ON FUNCTION addlist(poper integer, plnid integer, pname character varying, pobjtype integer, psid integer) TO iusrpmt;


--
-- Name: addmediatostory(integer, integer, integer, integer, character varying); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION addmediatostory(pftype integer, pid integer, pstoryid integer, pplace integer, ptxt character varying) FROM PUBLIC;
REVOKE ALL ON FUNCTION addmediatostory(pftype integer, pid integer, pstoryid integer, pplace integer, ptxt character varying) FROM postgres84;
GRANT ALL ON FUNCTION addmediatostory(pftype integer, pid integer, pstoryid integer, pplace integer, ptxt character varying) TO postgres84;
GRANT ALL ON FUNCTION addmediatostory(pftype integer, pid integer, pstoryid integer, pplace integer, ptxt character varying) TO iusrpmt;


--
-- Name: addstorytostory(integer, integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION addstorytostory(pstoryid integer, pstoryid1 integer, pproptype integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION addstorytostory(pstoryid integer, pstoryid1 integer, pproptype integer) FROM postgres84;
GRANT ALL ON FUNCTION addstorytostory(pstoryid integer, pstoryid1 integer, pproptype integer) TO postgres84;
GRANT ALL ON FUNCTION addstorytostory(pstoryid integer, pstoryid1 integer, pproptype integer) TO iusrpmt;


--
-- Name: addtomessaging(character varying, character varying, character varying, timestamp without time zone); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION addtomessaging(pmfrom character varying, pmto character varying, psubject character varying, psenddate timestamp without time zone) FROM PUBLIC;
REVOKE ALL ON FUNCTION addtomessaging(pmfrom character varying, pmto character varying, psubject character varying, psenddate timestamp without time zone) FROM postgres84;
GRANT ALL ON FUNCTION addtomessaging(pmfrom character varying, pmto character varying, psubject character varying, psenddate timestamp without time zone) TO postgres84;
GRANT ALL ON FUNCTION addtomessaging(pmfrom character varying, pmto character varying, psubject character varying, psenddate timestamp without time zone) TO iusrpmt;


--
-- Name: attupload(integer, integer, integer, character varying, character varying, character varying, character varying, character varying); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION attupload(poper integer, pid integer, psrc integer, ptitle character varying, pfnupl character varying, pdescr character varying, pext character varying, pmimetype character varying) FROM PUBLIC;
REVOKE ALL ON FUNCTION attupload(poper integer, pid integer, psrc integer, ptitle character varying, pfnupl character varying, pdescr character varying, pext character varying, pmimetype character varying) FROM postgres84;
GRANT ALL ON FUNCTION attupload(poper integer, pid integer, psrc integer, ptitle character varying, pfnupl character varying, pdescr character varying, pext character varying, pmimetype character varying) TO postgres84;
GRANT ALL ON FUNCTION attupload(poper integer, pid integer, psrc integer, ptitle character varying, pfnupl character varying, pdescr character varying, pext character varying, pmimetype character varying) TO iusrpmt;


--
-- Name: cmsetsecgrpsite(integer, integer, integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION cmsetsecgrpsite(pgid integer, psid integer, ptype integer, pop integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION cmsetsecgrpsite(pgid integer, psid integer, ptype integer, pop integer) FROM postgres84;
GRANT ALL ON FUNCTION cmsetsecgrpsite(pgid integer, psid integer, ptype integer, pop integer) TO postgres84;
GRANT ALL ON FUNCTION cmsetsecgrpsite(pgid integer, psid integer, ptype integer, pop integer) TO iusrpmt;


--
-- Name: cmsetsecusergrp(integer, integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION cmsetsecusergrp(pgid integer, puid integer, pop integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION cmsetsecusergrp(pgid integer, puid integer, pop integer) FROM postgres84;
GRANT ALL ON FUNCTION cmsetsecusergrp(pgid integer, puid integer, pop integer) TO postgres84;
GRANT ALL ON FUNCTION cmsetsecusergrp(pgid integer, puid integer, pop integer) TO iusrpmt;


--
-- Name: confmail(character varying); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION confmail(pconfhash character varying) FROM PUBLIC;
REVOKE ALL ON FUNCTION confmail(pconfhash character varying) FROM postgres84;
GRANT ALL ON FUNCTION confmail(pconfhash character varying) TO postgres84;
GRANT ALL ON FUNCTION confmail(pconfhash character varying) TO iusrpmt;


--
-- Name: deleterelateditemsfromstory(integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION deleterelateditemsfromstory(pguid integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION deleterelateditemsfromstory(pguid integer) FROM postgres84;
GRANT ALL ON FUNCTION deleterelateditemsfromstory(pguid integer) TO postgres84;
GRANT ALL ON FUNCTION deleterelateditemsfromstory(pguid integer) TO iusrpmt;


--
-- Name: deletestory(integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION deletestory(pstoryid integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION deletestory(pstoryid integer) FROM postgres84;
GRANT ALL ON FUNCTION deletestory(pstoryid integer) TO postgres84;
GRANT ALL ON FUNCTION deletestory(pstoryid integer) TO iusrpmt;


--
-- Name: deletevideo(integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION deletevideo(pstoryid integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION deletevideo(pstoryid integer) FROM postgres84;
GRANT ALL ON FUNCTION deletevideo(pstoryid integer) TO postgres84;
GRANT ALL ON FUNCTION deletevideo(pstoryid integer) TO iusrpmt;


--
-- Name: delstoryfromstory(integer, integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION delstoryfromstory(pstoryid integer, pstoryid1 integer, ppropid integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION delstoryfromstory(pstoryid integer, pstoryid1 integer, ppropid integer) FROM postgres84;
GRANT ALL ON FUNCTION delstoryfromstory(pstoryid integer, pstoryid1 integer, ppropid integer) TO postgres84;
GRANT ALL ON FUNCTION delstoryfromstory(pstoryid integer, pstoryid1 integer, ppropid integer) TO iusrpmt;


--
-- Name: forumaddfirstmsg(integer, integer, integer, character varying); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION forumaddfirstmsg(pdscid integer, pitemid integer, puid integer, puname character varying) FROM PUBLIC;
REVOKE ALL ON FUNCTION forumaddfirstmsg(pdscid integer, pitemid integer, puid integer, puname character varying) FROM postgres84;
GRANT ALL ON FUNCTION forumaddfirstmsg(pdscid integer, pitemid integer, puid integer, puname character varying) TO postgres84;
GRANT ALL ON FUNCTION forumaddfirstmsg(pdscid integer, pitemid integer, puid integer, puname character varying) TO PUBLIC;
GRANT ALL ON FUNCTION forumaddfirstmsg(pdscid integer, pitemid integer, puid integer, puname character varying) TO iusrpmt;


--
-- Name: forumaddmsg(integer, integer, integer, character varying, character varying, text, text, inet, integer, character varying, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION forumaddmsg(preplyid integer, pdscid integer, pitemid integer, pauthor character varying, psubject character varying, pmsg text, pmsghtml text, psenderip inet, puid integer, puname character varying, phidden integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION forumaddmsg(preplyid integer, pdscid integer, pitemid integer, pauthor character varying, psubject character varying, pmsg text, pmsghtml text, psenderip inet, puid integer, puname character varying, phidden integer) FROM postgres84;
GRANT ALL ON FUNCTION forumaddmsg(preplyid integer, pdscid integer, pitemid integer, pauthor character varying, psubject character varying, pmsg text, pmsghtml text, psenderip inet, puid integer, puname character varying, phidden integer) TO postgres84;
GRANT ALL ON FUNCTION forumaddmsg(preplyid integer, pdscid integer, pitemid integer, pauthor character varying, psubject character varying, pmsg text, pmsghtml text, psenderip inet, puid integer, puname character varying, phidden integer) TO PUBLIC;
GRANT ALL ON FUNCTION forumaddmsg(preplyid integer, pdscid integer, pitemid integer, pauthor character varying, psubject character varying, pmsg text, pmsghtml text, psenderip inet, puid integer, puname character varying, phidden integer) TO iusrpmt;


--
-- Name: forumgetmsgflathtml(integer, integer, integer, integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION forumgetmsgflathtml(prootid integer, psiteid integer, pdscgrpid integer, pdscid integer, pitemid integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION forumgetmsgflathtml(prootid integer, psiteid integer, pdscgrpid integer, pdscid integer, pitemid integer) FROM postgres84;
GRANT ALL ON FUNCTION forumgetmsgflathtml(prootid integer, psiteid integer, pdscgrpid integer, pdscid integer, pitemid integer) TO postgres84;
GRANT ALL ON FUNCTION forumgetmsgflathtml(prootid integer, psiteid integer, pdscgrpid integer, pdscid integer, pitemid integer) TO iusrpmt;


--
-- Name: forumgetnextord(character varying); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION forumgetnextord(pord character varying) FROM PUBLIC;
REVOKE ALL ON FUNCTION forumgetnextord(pord character varying) FROM postgres84;
GRANT ALL ON FUNCTION forumgetnextord(pord character varying) TO postgres84;
GRANT ALL ON FUNCTION forumgetnextord(pord character varying) TO PUBLIC;
GRANT ALL ON FUNCTION forumgetnextord(pord character varying) TO iusrpmt;


--
-- Name: forumgetsinglemsg(integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION forumgetsinglemsg(pmsgid integer, pdscgroup integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION forumgetsinglemsg(pmsgid integer, pdscgroup integer) FROM postgres84;
GRANT ALL ON FUNCTION forumgetsinglemsg(pmsgid integer, pdscgroup integer) TO postgres84;
GRANT ALL ON FUNCTION forumgetsinglemsg(pmsgid integer, pdscgroup integer) TO PUBLIC;
GRANT ALL ON FUNCTION forumgetsinglemsg(pmsgid integer, pdscgroup integer) TO iusrpmt;


--
-- Name: forumgettopics(integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION forumgettopics(pdiscid integer, pdscgroup integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION forumgettopics(pdiscid integer, pdscgroup integer) FROM postgres84;
GRANT ALL ON FUNCTION forumgettopics(pdiscid integer, pdscgroup integer) TO postgres84;
GRANT ALL ON FUNCTION forumgettopics(pdiscid integer, pdscgroup integer) TO iusrpmt;


--
-- Name: forumsetflags(integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION forumsetflags(pmsgid integer, pflags integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION forumsetflags(pmsgid integer, pflags integer) FROM postgres84;
GRANT ALL ON FUNCTION forumsetflags(pmsgid integer, pflags integer) TO postgres84;
GRANT ALL ON FUNCTION forumsetflags(pmsgid integer, pflags integer) TO PUBLIC;
GRANT ALL ON FUNCTION forumsetflags(pmsgid integer, pflags integer) TO iusrpmt;


--
-- Name: getallpolls(integer, integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION getallpolls(pposid integer, psid integer, plang integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION getallpolls(pposid integer, psid integer, plang integer) FROM postgres84;
GRANT ALL ON FUNCTION getallpolls(pposid integer, psid integer, plang integer) TO postgres84;
GRANT ALL ON FUNCTION getallpolls(pposid integer, psid integer, plang integer) TO iusrpmt;


--
-- Name: getanketa(inet, integer, integer, integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION getanketa(pip inet, ppos integer, psid integer, ppollid integer, plang integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION getanketa(pip inet, ppos integer, psid integer, ppollid integer, plang integer) FROM postgres84;
GRANT ALL ON FUNCTION getanketa(pip inet, ppos integer, psid integer, ppollid integer, plang integer) TO postgres84;
GRANT ALL ON FUNCTION getanketa(pip inet, ppos integer, psid integer, ppollid integer, plang integer) TO iusrpmt;


--
-- Name: getanketaarchiv(integer, integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION getanketaarchiv(ppollid integer, psid integer, plang integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION getanketaarchiv(ppollid integer, psid integer, plang integer) FROM postgres84;
GRANT ALL ON FUNCTION getanketaarchiv(ppollid integer, psid integer, plang integer) TO postgres84;
GRANT ALL ON FUNCTION getanketaarchiv(ppollid integer, psid integer, plang integer) TO iusrpmt;


--
-- Name: getattachment(integer, character varying, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION getattachment(pguid integer, pcode character varying, purole integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION getattachment(pguid integer, pcode character varying, purole integer) FROM postgres84;
GRANT ALL ON FUNCTION getattachment(pguid integer, pcode character varying, purole integer) TO postgres84;
GRANT ALL ON FUNCTION getattachment(pguid integer, pcode character varying, purole integer) TO iusrpmt;


--
-- Name: getattachmentsbystory(integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION getattachmentsbystory(pstoryid integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION getattachmentsbystory(pstoryid integer) FROM postgres84;
GRANT ALL ON FUNCTION getattachmentsbystory(pstoryid integer) TO postgres84;
GRANT ALL ON FUNCTION getattachmentsbystory(pstoryid integer) TO PUBLIC;
GRANT ALL ON FUNCTION getattachmentsbystory(pstoryid integer) TO iusrpmt;


--
-- Name: getbulletinbasedata(integer, integer, integer, character varying); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION getbulletinbasedata(pguid integer, pstorytype integer, puid integer, plang character varying) FROM PUBLIC;
REVOKE ALL ON FUNCTION getbulletinbasedata(pguid integer, pstorytype integer, puid integer, plang character varying) FROM postgres84;
GRANT ALL ON FUNCTION getbulletinbasedata(pguid integer, pstorytype integer, puid integer, plang character varying) TO postgres84;
GRANT ALL ON FUNCTION getbulletinbasedata(pguid integer, pstorytype integer, puid integer, plang character varying) TO iusrpmt;


--
-- Name: getgallerybasedata(integer, integer, integer, character varying); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION getgallerybasedata(pguid integer, pstorytype integer, puid integer, plang character varying) FROM PUBLIC;
REVOKE ALL ON FUNCTION getgallerybasedata(pguid integer, pstorytype integer, puid integer, plang character varying) FROM postgres84;
GRANT ALL ON FUNCTION getgallerybasedata(pguid integer, pstorytype integer, puid integer, plang character varying) TO postgres84;
GRANT ALL ON FUNCTION getgallerybasedata(pguid integer, pstorytype integer, puid integer, plang character varying) TO PUBLIC;
GRANT ALL ON FUNCTION getgallerybasedata(pguid integer, pstorytype integer, puid integer, plang character varying) TO iusrpmt;


--
-- Name: getitemname(integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION getitemname(pitemtype integer, pitemid integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION getitemname(pitemtype integer, pitemid integer) FROM postgres84;
GRANT ALL ON FUNCTION getitemname(pitemtype integer, pitemid integer) TO postgres84;
GRANT ALL ON FUNCTION getitemname(pitemtype integer, pitemid integer) TO PUBLIC;
GRANT ALL ON FUNCTION getitemname(pitemtype integer, pitemid integer) TO iusrpmt;


--
-- Name: getlistrubr(integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION getlistrubr(plistid integer, plangid integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION getlistrubr(plistid integer, plangid integer) FROM postgres84;
GRANT ALL ON FUNCTION getlistrubr(plistid integer, plangid integer) TO postgres84;
GRANT ALL ON FUNCTION getlistrubr(plistid integer, plangid integer) TO PUBLIC;
GRANT ALL ON FUNCTION getlistrubr(plistid integer, plangid integer) TO iusrpmt;


--
-- Name: getliststories(integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION getliststories(plistid integer, plangid integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION getliststories(plistid integer, plangid integer) FROM postgres84;
GRANT ALL ON FUNCTION getliststories(plistid integer, plangid integer) TO postgres84;
GRANT ALL ON FUNCTION getliststories(plistid integer, plangid integer) TO PUBLIC;
GRANT ALL ON FUNCTION getliststories(plistid integer, plangid integer) TO iusrpmt;


--
-- Name: getmedia(); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION getmedia() FROM PUBLIC;
REVOKE ALL ON FUNCTION getmedia() FROM postgres84;
GRANT ALL ON FUNCTION getmedia() TO postgres84;
GRANT ALL ON FUNCTION getmedia() TO iusrpmt;


--
-- Name: getmediabasedata(integer, integer, integer, character varying); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION getmediabasedata(pguid integer, pstorytype integer, puid integer, plang character varying) FROM PUBLIC;
REVOKE ALL ON FUNCTION getmediabasedata(pguid integer, pstorytype integer, puid integer, plang character varying) FROM postgres84;
GRANT ALL ON FUNCTION getmediabasedata(pguid integer, pstorytype integer, puid integer, plang character varying) TO postgres84;
GRANT ALL ON FUNCTION getmediabasedata(pguid integer, pstorytype integer, puid integer, plang character varying) TO iusrpmt;


--
-- Name: getmediabystory(integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION getmediabystory(pstoryid integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION getmediabystory(pstoryid integer) FROM postgres84;
GRANT ALL ON FUNCTION getmediabystory(pstoryid integer) TO postgres84;
GRANT ALL ON FUNCTION getmediabystory(pstoryid integer) TO iusrpmt;


--
-- Name: getmenucontents(integer, integer, integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION getmenucontents(integer, integer, integer, integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION getmenucontents(integer, integer, integer, integer) FROM postgres84;
GRANT ALL ON FUNCTION getmenucontents(integer, integer, integer, integer) TO postgres84;
GRANT ALL ON FUNCTION getmenucontents(integer, integer, integer, integer) TO PUBLIC;
GRANT ALL ON FUNCTION getmenucontents(integer, integer, integer, integer) TO iusrpmt;


--
-- Name: getphotosbystory(integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION getphotosbystory(pstoryid integer, psid integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION getphotosbystory(pstoryid integer, psid integer) FROM postgres84;
GRANT ALL ON FUNCTION getphotosbystory(pstoryid integer, psid integer) TO postgres84;
GRANT ALL ON FUNCTION getphotosbystory(pstoryid integer, psid integer) TO iusrpmt;


--
-- Name: getrubrsiblings(integer, integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION getrubrsiblings(prubrid integer, psid integer, plangid integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION getrubrsiblings(prubrid integer, psid integer, plangid integer) FROM postgres84;
GRANT ALL ON FUNCTION getrubrsiblings(prubrid integer, psid integer, plangid integer) TO postgres84;
GRANT ALL ON FUNCTION getrubrsiblings(prubrid integer, psid integer, plangid integer) TO PUBLIC;
GRANT ALL ON FUNCTION getrubrsiblings(prubrid integer, psid integer, plangid integer) TO iusrpmt;


--
-- Name: getstoriesbasedata(integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION getstoriesbasedata(pguid integer, plangid integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION getstoriesbasedata(pguid integer, plangid integer) FROM postgres84;
GRANT ALL ON FUNCTION getstoriesbasedata(pguid integer, plangid integer) TO postgres84;
GRANT ALL ON FUNCTION getstoriesbasedata(pguid integer, plangid integer) TO PUBLIC;
GRANT ALL ON FUNCTION getstoriesbasedata(pguid integer, plangid integer) TO iusrpmt;


--
-- Name: getstoriesbyrubr(integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION getstoriesbyrubr(prubr integer, plang integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION getstoriesbyrubr(prubr integer, plang integer) FROM postgres84;
GRANT ALL ON FUNCTION getstoriesbyrubr(prubr integer, plang integer) TO postgres84;
GRANT ALL ON FUNCTION getstoriesbyrubr(prubr integer, plang integer) TO PUBLIC;
GRANT ALL ON FUNCTION getstoriesbyrubr(prubr integer, plang integer) TO iusrpmt;


--
-- Name: getstoryrelateditems(integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION getstoryrelateditems(pstoryid integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION getstoryrelateditems(pstoryid integer) FROM postgres84;
GRANT ALL ON FUNCTION getstoryrelateditems(pstoryid integer) TO postgres84;
GRANT ALL ON FUNCTION getstoryrelateditems(pstoryid integer) TO PUBLIC;
GRANT ALL ON FUNCTION getstoryrelateditems(pstoryid integer) TO iusrpmt;


--
-- Name: getsubrubrs(integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION getsubrubrs(prubr integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION getsubrubrs(prubr integer) FROM postgres84;
GRANT ALL ON FUNCTION getsubrubrs(prubr integer) TO postgres84;
GRANT ALL ON FUNCTION getsubrubrs(prubr integer) TO PUBLIC;
GRANT ALL ON FUNCTION getsubrubrs(prubr integer) TO iusrpmt;


--
-- Name: languages; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE languages FROM PUBLIC;
REVOKE ALL ON TABLE languages FROM postgres84;
GRANT ALL ON TABLE languages TO postgres84;
GRANT SELECT ON TABLE languages TO iusrpmt;


--
-- Name: langs(integer, integer, character varying, character varying); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION langs(pop integer, plangid integer, pcode character varying, pname character varying) FROM PUBLIC;
REVOKE ALL ON FUNCTION langs(pop integer, plangid integer, pcode character varying, pname character varying) FROM postgres84;
GRANT ALL ON FUNCTION langs(pop integer, plangid integer, pcode character varying, pname character varying) TO postgres84;
GRANT ALL ON FUNCTION langs(pop integer, plangid integer, pcode character varying, pname character varying) TO iusrpmt;


--
-- Name: picsupload(integer, integer, integer, character varying, character varying, character varying); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION picsupload(poper integer, pid integer, psrc integer, ptitle character varying, pfnupl character varying, pdescr character varying) FROM PUBLIC;
REVOKE ALL ON FUNCTION picsupload(poper integer, pid integer, psrc integer, ptitle character varying, pfnupl character varying, pdescr character varying) FROM postgres84;
GRANT ALL ON FUNCTION picsupload(poper integer, pid integer, psrc integer, ptitle character varying, pfnupl character varying, pdescr character varying) TO postgres84;
GRANT ALL ON FUNCTION picsupload(poper integer, pid integer, psrc integer, ptitle character varying, pfnupl character varying, pdescr character varying) TO iusrpmt;


--
-- Name: rubrikirearange(integer, integer[]); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION rubrikirearange(psiteid integer, pmodifiedarr integer[]) FROM PUBLIC;
REVOKE ALL ON FUNCTION rubrikirearange(psiteid integer, pmodifiedarr integer[]) FROM postgres84;
GRANT ALL ON FUNCTION rubrikirearange(psiteid integer, pmodifiedarr integer[]) TO postgres84;
GRANT ALL ON FUNCTION rubrikirearange(psiteid integer, pmodifiedarr integer[]) TO PUBLIC;
GRANT ALL ON FUNCTION rubrikirearange(psiteid integer, pmodifiedarr integer[]) TO iusrpmt;


--
-- Name: savestoriesbasedata(integer, integer, character varying, character varying, character varying, character varying, timestamp without time zone, character varying, integer, character varying, integer, character varying, character varying, integer, integer, character varying, integer, integer, integer, integer, text); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION savestoriesbasedata(pguid integer, pprimarysite integer, plang character varying, ptitle character varying, plink character varying, pdescription character varying, ppubdate timestamp without time zone, pauthor character varying, pcreateuid integer, pkeywords character varying, pstate integer, psubtitle character varying, pnadzaglavie character varying, pstorytype integer, pmainrubr integer, prubr character varying, ppriority integer, pindexer integer, pshowforum integer, pdscid integer, pbody text) FROM PUBLIC;
REVOKE ALL ON FUNCTION savestoriesbasedata(pguid integer, pprimarysite integer, plang character varying, ptitle character varying, plink character varying, pdescription character varying, ppubdate timestamp without time zone, pauthor character varying, pcreateuid integer, pkeywords character varying, pstate integer, psubtitle character varying, pnadzaglavie character varying, pstorytype integer, pmainrubr integer, prubr character varying, ppriority integer, pindexer integer, pshowforum integer, pdscid integer, pbody text) FROM postgres84;
GRANT ALL ON FUNCTION savestoriesbasedata(pguid integer, pprimarysite integer, plang character varying, ptitle character varying, plink character varying, pdescription character varying, ppubdate timestamp without time zone, pauthor character varying, pcreateuid integer, pkeywords character varying, pstate integer, psubtitle character varying, pnadzaglavie character varying, pstorytype integer, pmainrubr integer, prubr character varying, ppriority integer, pindexer integer, pshowforum integer, pdscid integer, pbody text) TO postgres84;
GRANT ALL ON FUNCTION savestoriesbasedata(pguid integer, pprimarysite integer, plang character varying, ptitle character varying, plink character varying, pdescription character varying, ppubdate timestamp without time zone, pauthor character varying, pcreateuid integer, pkeywords character varying, pstate integer, psubtitle character varying, pnadzaglavie character varying, pstorytype integer, pmainrubr integer, prubr character varying, ppriority integer, pindexer integer, pshowforum integer, pdscid integer, pbody text) TO iusrpmt;


--
-- Name: savestoriesrubriki(integer, character varying, integer, integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION savestoriesrubriki(pguid integer, prubrstr character varying, pmainrubr integer, pindexer integer, psid integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION savestoriesrubriki(pguid integer, prubrstr character varying, pmainrubr integer, pindexer integer, psid integer) FROM postgres84;
GRANT ALL ON FUNCTION savestoriesrubriki(pguid integer, prubrstr character varying, pmainrubr integer, pindexer integer, psid integer) TO postgres84;
GRANT ALL ON FUNCTION savestoriesrubriki(pguid integer, prubrstr character varying, pmainrubr integer, pindexer integer, psid integer) TO iusrpmt;


--
-- Name: savetransliterationwords(integer, integer, character varying, character varying); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION savetransliterationwords(pop integer, pid integer, pwordbg character varying, pworden character varying) FROM PUBLIC;
REVOKE ALL ON FUNCTION savetransliterationwords(pop integer, pid integer, pwordbg character varying, pworden character varying) FROM postgres84;
GRANT ALL ON FUNCTION savetransliterationwords(pop integer, pid integer, pwordbg character varying, pworden character varying) TO postgres84;
GRANT ALL ON FUNCTION savetransliterationwords(pop integer, pid integer, pwordbg character varying, pworden character varying) TO iusrpmt;


--
-- Name: setconfhash(character varying, character varying, character varying); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION setconfhash(puser character varying, pemail character varying, pconfhash character varying) FROM PUBLIC;
REVOKE ALL ON FUNCTION setconfhash(puser character varying, pemail character varying, pconfhash character varying) FROM postgres84;
GRANT ALL ON FUNCTION setconfhash(puser character varying, pemail character varying, pconfhash character varying) TO postgres84;
GRANT ALL ON FUNCTION setconfhash(puser character varying, pemail character varying, pconfhash character varying) TO iusrpmt;


--
-- Name: sggetrubrstories(integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION sggetrubrstories(prubr integer, plangid integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION sggetrubrstories(prubr integer, plangid integer) FROM postgres84;
GRANT ALL ON FUNCTION sggetrubrstories(prubr integer, plangid integer) TO postgres84;
GRANT ALL ON FUNCTION sggetrubrstories(prubr integer, plangid integer) TO PUBLIC;
GRANT ALL ON FUNCTION sggetrubrstories(prubr integer, plangid integer) TO iusrpmt;


--
-- Name: sggetrubrstories(integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION sggetrubrstories(plangid integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION sggetrubrstories(plangid integer) FROM postgres84;
GRANT ALL ON FUNCTION sggetrubrstories(plangid integer) TO postgres84;
GRANT ALL ON FUNCTION sggetrubrstories(plangid integer) TO PUBLIC;
GRANT ALL ON FUNCTION sggetrubrstories(plangid integer) TO iusrpmt;


--
-- Name: sitelogin(character varying, character varying, inet); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION sitelogin(puname character varying, ppass character varying, pip inet) FROM PUBLIC;
REVOKE ALL ON FUNCTION sitelogin(puname character varying, ppass character varying, pip inet) FROM postgres84;
GRANT ALL ON FUNCTION sitelogin(puname character varying, ppass character varying, pip inet) TO postgres84;
GRANT ALL ON FUNCTION sitelogin(puname character varying, ppass character varying, pip inet) TO iusrpmt;


--
-- Name: sp_regprof(integer, integer, character varying, character varying, integer, character varying, character varying, character varying); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION sp_regprof(poper integer, pid integer, pusername character varying, pupass character varying, putype integer, pname character varying, pemail character varying, pphone character varying) FROM PUBLIC;
REVOKE ALL ON FUNCTION sp_regprof(poper integer, pid integer, pusername character varying, pupass character varying, putype integer, pname character varying, pemail character varying, pphone character varying) FROM postgres84;
GRANT ALL ON FUNCTION sp_regprof(poper integer, pid integer, pusername character varying, pupass character varying, putype integer, pname character varying, pemail character varying, pphone character varying) TO postgres84;
GRANT ALL ON FUNCTION sp_regprof(poper integer, pid integer, pusername character varying, pupass character varying, putype integer, pname character varying, pemail character varying, pphone character varying) TO iusrpmt;


--
-- Name: spattachemnts(integer, integer, integer, integer, character varying, character varying); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION spattachemnts(poper integer, pid integer, pstoryid integer, ppicid integer, ptitle character varying, pdescr character varying) FROM PUBLIC;
REVOKE ALL ON FUNCTION spattachemnts(poper integer, pid integer, pstoryid integer, ppicid integer, ptitle character varying, pdescr character varying) FROM postgres84;
GRANT ALL ON FUNCTION spattachemnts(poper integer, pid integer, pstoryid integer, ppicid integer, ptitle character varying, pdescr character varying) TO postgres84;
GRANT ALL ON FUNCTION spattachemnts(poper integer, pid integer, pstoryid integer, ppicid integer, ptitle character varying, pdescr character varying) TO iusrpmt;


--
-- Name: splogin(character varying, character varying, character varying); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION splogin(puname character varying, ppass character varying, pip character varying) FROM PUBLIC;
REVOKE ALL ON FUNCTION splogin(puname character varying, ppass character varying, pip character varying) FROM postgres84;
GRANT ALL ON FUNCTION splogin(puname character varying, ppass character varying, pip character varying) TO postgres84;
GRANT ALL ON FUNCTION splogin(puname character varying, ppass character varying, pip character varying) TO PUBLIC;
GRANT ALL ON FUNCTION splogin(puname character varying, ppass character varying, pip character varying) TO iusrpmt;


--
-- Name: spmetadata(integer, integer, character varying, character varying, character varying); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION spmetadata(poper integer, pid integer, ptitle character varying, pdescription character varying, pkeywords character varying) FROM PUBLIC;
REVOKE ALL ON FUNCTION spmetadata(poper integer, pid integer, ptitle character varying, pdescription character varying, pkeywords character varying) FROM postgres84;
GRANT ALL ON FUNCTION spmetadata(poper integer, pid integer, ptitle character varying, pdescription character varying, pkeywords character varying) TO postgres84;
GRANT ALL ON FUNCTION spmetadata(poper integer, pid integer, ptitle character varying, pdescription character varying, pkeywords character varying) TO iusrpmt;


--
-- Name: spmorelinks(integer, integer, character varying, character varying, integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION spmorelinks(poper integer, pguid integer, purl character varying, ptitle character varying, ppos integer, ppropid integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION spmorelinks(poper integer, pguid integer, purl character varying, ptitle character varying, ppos integer, ppropid integer) FROM postgres84;
GRANT ALL ON FUNCTION spmorelinks(poper integer, pguid integer, purl character varying, ptitle character varying, ppos integer, ppropid integer) TO postgres84;
GRANT ALL ON FUNCTION spmorelinks(poper integer, pguid integer, purl character varying, ptitle character varying, ppos integer, ppropid integer) TO iusrpmt;


--
-- Name: spmultimedia(integer, integer, integer, character varying, character varying, character varying, character varying, integer, integer, character varying, character varying, integer, integer, integer, integer, integer, character varying, integer, integer, character varying); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION spmultimedia(poper integer, pftype integer, pguid integer, plang character varying, ptitle character varying, pdescription character varying, pauthor character varying, pcreateuid integer, paccess integer, paccesscode character varying, prealname character varying, pdimx integer, pdimy integer, plength integer, psrcid integer, pplace integer, pmediatxt character varying, pmediasize integer, psrctype integer, pmimetype character varying) FROM PUBLIC;
REVOKE ALL ON FUNCTION spmultimedia(poper integer, pftype integer, pguid integer, plang character varying, ptitle character varying, pdescription character varying, pauthor character varying, pcreateuid integer, paccess integer, paccesscode character varying, prealname character varying, pdimx integer, pdimy integer, plength integer, psrcid integer, pplace integer, pmediatxt character varying, pmediasize integer, psrctype integer, pmimetype character varying) FROM postgres84;
GRANT ALL ON FUNCTION spmultimedia(poper integer, pftype integer, pguid integer, plang character varying, ptitle character varying, pdescription character varying, pauthor character varying, pcreateuid integer, paccess integer, paccesscode character varying, prealname character varying, pdimx integer, pdimy integer, plength integer, psrcid integer, pplace integer, pmediatxt character varying, pmediasize integer, psrctype integer, pmimetype character varying) TO postgres84;
GRANT ALL ON FUNCTION spmultimedia(poper integer, pftype integer, pguid integer, plang character varying, ptitle character varying, pdescription character varying, pauthor character varying, pcreateuid integer, paccess integer, paccesscode character varying, prealname character varying, pdimx integer, pdimy integer, plength integer, psrcid integer, pplace integer, pmediatxt character varying, pmediasize integer, psrctype integer, pmimetype character varying) TO iusrpmt;


--
-- Name: newsletter; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE newsletter FROM PUBLIC;
REVOKE ALL ON TABLE newsletter FROM postgres84;
GRANT ALL ON TABLE newsletter TO postgres84;
GRANT SELECT ON TABLE newsletter TO iusrpmt;


--
-- Name: spobjorder(integer, integer[], integer[]); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION spobjorder(plnid integer, pobjids integer[], pstyles integer[],  varchar) FROM PUBLIC;
REVOKE ALL ON FUNCTION spobjorder(plnid integer, pobjids integer[], pstyles integer[],  varchar) FROM postgres84;
GRANT ALL ON FUNCTION spobjorder(plnid integer, pobjids integer[], pstyles integer[],  varchar) TO postgres84;
GRANT ALL ON FUNCTION spobjorder(plnid integer, pobjids integer[], pstyles integer[],  varchar) TO iusrpmt;

--
-- Name: sppasswd(integer, character varying, character varying, character varying); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION sppasswd(pid integer, poldpass character varying, pupass character varying, pupass2 character varying) FROM PUBLIC;
REVOKE ALL ON FUNCTION sppasswd(pid integer, poldpass character varying, pupass character varying, pupass2 character varying) FROM postgres84;
GRANT ALL ON FUNCTION sppasswd(pid integer, poldpass character varying, pupass character varying, pupass2 character varying) TO postgres84;
GRANT ALL ON FUNCTION sppasswd(pid integer, poldpass character varying, pupass character varying, pupass2 character varying) TO iusrpmt;


--
-- Name: spphotos(integer, integer, integer, integer, character varying, character varying, integer, integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION spphotos(poper integer, pid integer, pstoryid integer, ppicid integer, ptitle character varying, pdescr character varying, pplace integer, pfirstphoto integer, ppos integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION spphotos(poper integer, pid integer, pstoryid integer, ppicid integer, ptitle character varying, pdescr character varying, pplace integer, pfirstphoto integer, ppos integer) FROM postgres84;
GRANT ALL ON FUNCTION spphotos(poper integer, pid integer, pstoryid integer, ppicid integer, ptitle character varying, pdescr character varying, pplace integer, pfirstphoto integer, ppos integer) TO postgres84;
GRANT ALL ON FUNCTION spphotos(poper integer, pid integer, pstoryid integer, ppicid integer, ptitle character varying, pdescr character varying, pplace integer, pfirstphoto integer, ppos integer) TO iusrpmt;


--
-- Name: secsites; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE secsites FROM PUBLIC;
REVOKE ALL ON TABLE secsites FROM postgres84;
GRANT ALL ON TABLE secsites TO postgres84;
GRANT ALL ON TABLE secsites TO iusrpmt;


--
-- Name: spsecsites(integer, integer, character varying, character varying, integer, integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION spsecsites(poper integer, pid integer, pname character varying, purl character varying, pord integer, ptype integer, pcnt integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION spsecsites(poper integer, pid integer, pname character varying, purl character varying, pord integer, ptype integer, pcnt integer) FROM postgres84;
GRANT ALL ON FUNCTION spsecsites(poper integer, pid integer, pname character varying, purl character varying, pord integer, ptype integer, pcnt integer) TO postgres84;
GRANT ALL ON FUNCTION spsecsites(poper integer, pid integer, pname character varying, purl character varying, pord integer, ptype integer, pcnt integer) TO PUBLIC;
GRANT ALL ON FUNCTION spsecsites(poper integer, pid integer, pname character varying, purl character varying, pord integer, ptype integer, pcnt integer) TO iusrpmt;


--
-- Name: spsitemenu(integer, integer, character varying[], integer, integer, integer, integer, integer, character varying[], character varying[]); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION spsitemenu(poper integer, pid integer, pname character varying[], psid integer, pparentid integer, ptype integer, pactive integer, pord integer, phref character varying[], pimg character varying[]) FROM PUBLIC;
REVOKE ALL ON FUNCTION spsitemenu(poper integer, pid integer, pname character varying[], psid integer, pparentid integer, ptype integer, pactive integer, pord integer, phref character varying[], pimg character varying[]) FROM postgres84;
GRANT ALL ON FUNCTION spsitemenu(poper integer, pid integer, pname character varying[], psid integer, pparentid integer, ptype integer, pactive integer, pord integer, phref character varying[], pimg character varying[]) TO postgres84;
GRANT ALL ON FUNCTION spsitemenu(poper integer, pid integer, pname character varying[], psid integer, pparentid integer, ptype integer, pactive integer, pord integer, phref character varying[], pimg character varying[]) TO PUBLIC;
GRANT ALL ON FUNCTION spsitemenu(poper integer, pid integer, pname character varying[], psid integer, pparentid integer, ptype integer, pactive integer, pord integer, phref character varying[], pimg character varying[]) TO iusrpmt;


--
-- Name: spsiterubr(integer, integer, integer, character varying[], integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION spsiterubr(poper integer, pid integer, psid integer, pname character varying[], pstate integer, pparent integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION spsiterubr(poper integer, pid integer, psid integer, pname character varying[], pstate integer, pparent integer) FROM postgres84;
GRANT ALL ON FUNCTION spsiterubr(poper integer, pid integer, psid integer, pname character varying[], pstate integer, pparent integer) TO postgres84;
GRANT ALL ON FUNCTION spsiterubr(poper integer, pid integer, psid integer, pname character varying[], pstate integer, pparent integer) TO iusrpmt;


--
-- Name: static_article; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE static_article FROM PUBLIC;
REVOKE ALL ON TABLE static_article FROM postgres84;
GRANT ALL ON TABLE static_article TO postgres84;
GRANT SELECT,UPDATE ON TABLE static_article TO iusrpmt;


--
-- Name: spstatic(integer, integer, integer[], character varying, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION spstatic(pop integer, pid integer, partnum integer[], partname character varying, psiteid integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION spstatic(pop integer, pid integer, partnum integer[], partname character varying, psiteid integer) FROM postgres84;
GRANT ALL ON FUNCTION spstatic(pop integer, pid integer, partnum integer[], partname character varying, psiteid integer) TO postgres84;
GRANT ALL ON FUNCTION spstatic(pop integer, pid integer, partnum integer[], partname character varying, psiteid integer) TO iusrpmt;


--
-- Name: storyusage; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE storyusage FROM PUBLIC;
REVOKE ALL ON TABLE storyusage FROM postgres84;
GRANT ALL ON TABLE storyusage TO postgres84;
GRANT SELECT ON TABLE storyusage TO iusrpmt;


--
-- Name: spstoryusage(integer, integer, character varying); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION spstoryusage(pguid integer, puid integer, puname character varying) FROM PUBLIC;
REVOKE ALL ON FUNCTION spstoryusage(pguid integer, puid integer, puname character varying) FROM postgres84;
GRANT ALL ON FUNCTION spstoryusage(pguid integer, puid integer, puname character varying) TO postgres84;
GRANT ALL ON FUNCTION spstoryusage(pguid integer, puid integer, puname character varying) TO PUBLIC;
GRANT ALL ON FUNCTION spstoryusage(pguid integer, puid integer, puname character varying) TO iusrpmt;


--
-- Name: usr; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE usr FROM PUBLIC;
REVOKE ALL ON TABLE usr FROM postgres84;
GRANT ALL ON TABLE usr TO postgres84;
GRANT SELECT ON TABLE usr TO iusrpmt;


--
-- Name: spusr(integer, integer, character varying, character varying, character varying, character varying, character varying, integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION spusr(pop integer, pid integer, puname character varying, pname character varying, pupass character varying, pemail character varying, pphone character varying, pstate integer, ptype integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION spusr(pop integer, pid integer, puname character varying, pname character varying, pupass character varying, pemail character varying, pphone character varying, pstate integer, ptype integer) FROM postgres84;
GRANT ALL ON FUNCTION spusr(pop integer, pid integer, puname character varying, pname character varying, pupass character varying, pemail character varying, pphone character varying, pstate integer, ptype integer) TO postgres84;
GRANT ALL ON FUNCTION spusr(pop integer, pid integer, puname character varying, pname character varying, pupass character varying, pemail character varying, pphone character varying, pstate integer, ptype integer) TO iusrpmt;


--
-- Name: storiesindexer(integer, integer, integer, text); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION storiesindexer(pguid integer, pindexer integer, pstate integer, pbody text) FROM PUBLIC;
REVOKE ALL ON FUNCTION storiesindexer(pguid integer, pindexer integer, pstate integer, pbody text) FROM postgres84;
GRANT ALL ON FUNCTION storiesindexer(pguid integer, pindexer integer, pstate integer, pbody text) TO postgres84;
GRANT ALL ON FUNCTION storiesindexer(pguid integer, pindexer integer, pstate integer, pbody text) TO iusrpmt;


--
-- Name: underforumgetmsgflathtml(integer, integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION underforumgetmsgflathtml(pdscid integer, pitemid integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION underforumgetmsgflathtml(pdscid integer, pitemid integer) FROM postgres84;
GRANT ALL ON FUNCTION underforumgetmsgflathtml(pdscid integer, pitemid integer) TO postgres84;
GRANT ALL ON FUNCTION underforumgetmsgflathtml(pdscid integer, pitemid integer) TO PUBLIC;
GRANT ALL ON FUNCTION underforumgetmsgflathtml(pdscid integer, pitemid integer) TO iusrpmt;


--
-- Name: updatestoriesstate(integer); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION updatestoriesstate(pguid integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION updatestoriesstate(pguid integer) FROM postgres84;
GRANT ALL ON FUNCTION updatestoriesstate(pguid integer) TO postgres84;
GRANT ALL ON FUNCTION updatestoriesstate(pguid integer) TO PUBLIC;
GRANT ALL ON FUNCTION updatestoriesstate(pguid integer) TO iusrpmt;


--
-- Name: userfpass(character varying); Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON FUNCTION userfpass(pemail character varying) FROM PUBLIC;
REVOKE ALL ON FUNCTION userfpass(pemail character varying) FROM postgres84;
GRANT ALL ON FUNCTION userfpass(pemail character varying) TO postgres84;
GRANT ALL ON FUNCTION userfpass(pemail character varying) TO iusrpmt;


--
-- Name: authors; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE authors FROM PUBLIC;
REVOKE ALL ON TABLE authors FROM postgres84;
GRANT ALL ON TABLE authors TO postgres84;
GRANT SELECT ON TABLE authors TO iusrpmt;


--
-- Name: careersattcodes; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE careersattcodes FROM PUBLIC;
REVOKE ALL ON TABLE careersattcodes FROM postgres84;
GRANT ALL ON TABLE careersattcodes TO postgres84;
GRANT SELECT ON TABLE careersattcodes TO iusrpmt;


--
-- Name: dsc; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE dsc FROM PUBLIC;
REVOKE ALL ON TABLE dsc FROM postgres84;
GRANT ALL ON TABLE dsc TO postgres84;
GRANT SELECT ON TABLE dsc TO iusrpmt;


--
-- Name: dsg; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE dsg FROM PUBLIC;
REVOKE ALL ON TABLE dsg FROM postgres84;
GRANT ALL ON TABLE dsg TO postgres84;
GRANT SELECT ON TABLE dsg TO iusrpmt;


--
-- Name: listdets; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE listdets FROM PUBLIC;
REVOKE ALL ON TABLE listdets FROM postgres84;
GRANT ALL ON TABLE listdets TO postgres84;
GRANT SELECT ON TABLE listdets TO iusrpmt;


--
-- Name: messaging; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE messaging FROM PUBLIC;
REVOKE ALL ON TABLE messaging FROM postgres84;
GRANT ALL ON TABLE messaging TO postgres84;
GRANT SELECT,UPDATE ON TABLE messaging TO iusrpmt;


--
-- Name: metadata; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE metadata FROM PUBLIC;
REVOKE ALL ON TABLE metadata FROM postgres84;
GRANT ALL ON TABLE metadata TO postgres84;
GRANT SELECT,UPDATE ON TABLE metadata TO iusrpmt;


--
-- Name: msg; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE msg FROM PUBLIC;
REVOKE ALL ON TABLE msg FROM postgres84;
GRANT ALL ON TABLE msg TO postgres84;
GRANT ALL ON TABLE msg TO iusrpmt;


--
-- Name: msgroot; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE msgroot FROM PUBLIC;
REVOKE ALL ON TABLE msgroot FROM postgres84;
GRANT ALL ON TABLE msgroot TO postgres84;
GRANT SELECT ON TABLE msgroot TO iusrpmt;


--
-- Name: pans; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE pans FROM PUBLIC;
REVOKE ALL ON TABLE pans FROM postgres84;
GRANT ALL ON TABLE pans TO postgres84;
GRANT SELECT ON TABLE pans TO iusrpmt;


--
-- Name: pauthors; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE pauthors FROM PUBLIC;
REVOKE ALL ON TABLE pauthors FROM postgres84;
GRANT ALL ON TABLE pauthors TO postgres84;
GRANT SELECT ON TABLE pauthors TO iusrpmt;



--
-- Name: stories; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE stories FROM PUBLIC;
REVOKE ALL ON TABLE stories FROM postgres84;
GRANT ALL ON TABLE stories TO postgres84;
GRANT SELECT,INSERT ON TABLE stories TO iusrpmt;


--
-- Name: stories_guid_seq; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON SEQUENCE stories_guid_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE stories_guid_seq FROM postgres84;
GRANT SELECT,UPDATE ON SEQUENCE stories_guid_seq TO postgres84;
GRANT SELECT,UPDATE ON SEQUENCE stories_guid_seq TO iusrpmt;


--
-- Name: photos; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE photos FROM PUBLIC;
REVOKE ALL ON TABLE photos FROM postgres84;
GRANT ALL ON TABLE photos TO postgres84;
GRANT SELECT,UPDATE ON TABLE photos TO iusrpmt;


--
-- Name: poll; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE poll FROM PUBLIC;
REVOKE ALL ON TABLE poll FROM postgres84;
GRANT ALL ON TABLE poll TO postgres84;
GRANT SELECT ON TABLE poll TO iusrpmt;


--
-- Name: pollogs; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE pollogs FROM PUBLIC;
REVOKE ALL ON TABLE pollogs FROM postgres84;
GRANT ALL ON TABLE pollogs TO postgres84;
GRANT SELECT ON TABLE pollogs TO iusrpmt;


--
-- Name: rubr; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE rubr FROM PUBLIC;
REVOKE ALL ON TABLE rubr FROM postgres84;
GRANT ALL ON TABLE rubr TO postgres84;
GRANT SELECT ON TABLE rubr TO iusrpmt;


--
-- Name: secgrp; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE secgrp FROM PUBLIC;
REVOKE ALL ON TABLE secgrp FROM postgres84;
GRANT ALL ON TABLE secgrp TO postgres84;
GRANT SELECT ON TABLE secgrp TO iusrpmt;


--
-- Name: secgrpacc; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE secgrpacc FROM PUBLIC;
REVOKE ALL ON TABLE secgrpacc FROM postgres84;
GRANT ALL ON TABLE secgrpacc TO postgres84;
GRANT SELECT ON TABLE secgrpacc TO iusrpmt;


--
-- Name: secgrpdet; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE secgrpdet FROM PUBLIC;
REVOKE ALL ON TABLE secgrpdet FROM postgres84;
GRANT ALL ON TABLE secgrpdet TO postgres84;
GRANT SELECT ON TABLE secgrpdet TO iusrpmt;


--
-- Name: sid1storyprops; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE sid1storyprops FROM PUBLIC;
REVOKE ALL ON TABLE sid1storyprops FROM postgres84;
GRANT ALL ON TABLE sid1storyprops TO postgres84;
GRANT SELECT ON TABLE sid1storyprops TO iusrpmt;


--
-- Name: sites; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE sites FROM PUBLIC;
REVOKE ALL ON TABLE sites FROM postgres84;
GRANT ALL ON TABLE sites TO postgres84;
GRANT SELECT ON TABLE sites TO iusrpmt;


--
-- Name: storiesft; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE storiesft FROM PUBLIC;
REVOKE ALL ON TABLE storiesft FROM postgres84;
GRANT ALL ON TABLE storiesft TO postgres84;
GRANT SELECT ON TABLE storiesft TO iusrpmt;


--
-- Name: storychangelog; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE storychangelog FROM PUBLIC;
REVOKE ALL ON TABLE storychangelog FROM postgres84;
GRANT ALL ON TABLE storychangelog TO postgres84;
GRANT SELECT ON TABLE storychangelog TO iusrpmt;


--
-- Name: storyproperties; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE storyproperties FROM PUBLIC;
REVOKE ALL ON TABLE storyproperties FROM postgres84;
GRANT ALL ON TABLE storyproperties TO postgres84;
GRANT SELECT,INSERT ON TABLE storyproperties TO iusrpmt;


--
-- Name: transliteration_words; Type: ACL; Schema: public; Owner: postgres84
--

REVOKE ALL ON TABLE transliteration_words FROM PUBLIC;
REVOKE ALL ON TABLE transliteration_words FROM postgres84;
GRANT ALL ON TABLE transliteration_words TO postgres84;
GRANT SELECT,INSERT ON TABLE transliteration_words TO iusrpmt;


--
-- PostgreSQL database dump complete
--

