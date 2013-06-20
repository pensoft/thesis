--DROP VIEW pjs.v_getdocumentsandreviewers; 
DROP VIEW pjs.v_getdocumentsandauthors; 
DROP VIEW pjs.v_getdocumentsandusers;
CREATE OR REPLACE VIEW pjs.v_getdocumentsandusers AS 
SELECT 
 d.id as doc_id, 
 d.name AS title, 
 d.state_id,
 d.lastmod_date as mdate,
 d.publish_date as pub_date,
 d.submitted_date,
 d.approve_date as approve_date,
 d.journal_id as journal,
 d.journal_section_id as journal_section,
 d.doi as doi,
 d.document_review_type_id as review_type,
 d.editor_notes,
 d.issue_id,
 d.number_of_pages as pages,
 d.current_round_id as current_round,
 du.id as doc_user_id,
 u.id AS user_id,
 (case when u.id <> submitting_author_id then (u.first_name || ' ' || u.last_name) else NULL end) AS names,
 du.role_id as role, 
 d.submitting_author_id as submitter_id, 
 uu.first_name || ' ' || uu.last_name as submitter_name,
 uu.uname as submitter_email 
   FROM public.usr u
   JOIN pjs.document_users du ON u.id = du.uid
   JOIN pjs.documents d ON du.document_id = d.id
   JOIN public.usr uu on (d.submitting_author_id = uu.id)
;

ALTER TABLE pjs.v_getdocumentsandusers OWNER TO pensoft;
GRANT ALL ON TABLE pjs.v_getdocumentsandusers TO pensoft;
GRANT ALL ON TABLE pjs.v_getdocumentsandusers TO iusrpmt;
GRANT ALL ON TABLE pjs.v_getdocumentsandusers TO postgres;

--DROP VIEW pjs.v_getdocumentsandauthors;
CREATE OR REPLACE VIEW pjs.v_getdocumentsandauthors AS 
SELECT  v.doc_id as doc_id, 
	max(v.title) as title,
	max(v.state_id) as state, 
	max(v.mdate)::date as mdate,
	max(v.pub_date)::date as pub_date,
	max(v.approve_date)::date as approve_date,
	max(journal) as journal,
	max(journal_section) as journal_section,
	max(review_type) as review_type,
	max(doi) as doi,
	max(submitted_date) as submitted_date,
	max(editor_notes) as editor_notes,
	max(issue_id) as issue_id,
	max(pages) as pages,
	max(current_round) as current_round,
	string_agg(names, ', ') as authors,
	max(submitter_id) as submitter_id,
	max(submitter_name) as submitter_name,
	max(submitter_email) as submitter_email
FROM pjs.v_getdocumentsandusers v
where v.role = 11 --Author_role_id
GROUP BY v.doc_id;

ALTER TABLE pjs.v_getdocumentsandauthors OWNER TO pensoft;
GRANT ALL ON TABLE pjs.v_getdocumentsandauthors TO pensoft;
GRANT ALL ON TABLE pjs.v_getdocumentsandauthors TO iusrpmt;
GRANT ALL ON TABLE pjs.v_getdocumentsandauthors TO postgres;


CREATE OR REPLACE VIEW pjs.v_getdocumentsandreviewers AS 
SELECT  
v.doc_id as doc_id, 
v.title as title, 
v.authors as authors, v.submitter_name as submitter_name, v.submitter_email as submitter_email,
v.journal as journal,
v.issue_id as issue_id,
v.editor_notes as editor_notes,
rru.decision_id,
	--du.document_id, 	--join with authors view
	du.uid as uid,			--where by user_id
	du.role_id,		--where by role
	rru.due_date::date,	--due dates
	v.state			--to determite action/who
	, v.doi
	, rr.round_number
	, role.name as role	--readability	
	, state.name as state_name
	, v.current_round
FROM pjs.v_getdocumentsandauthors v 
JOIN pjs.document_users du  ON v.doc_id = du.document_id 
JOIN pjs.document_review_round_users rru ON rru.document_user_id = du.id
JOIN pjs.document_review_rounds rr  ON rru.round_id = rr.id 
	--readability
	JOIN pjs.user_role_types role ON role.id = du.role_id
	JOIN pjs.document_states state ON state.id = v.state


	--JOIN pjs.document_review_rounds      rr  ON rru.round_id = rr.id
	--pjs.user_role_types role ON role.id = du.role_id JOIN
	--pjs.document_review_round_decisions rrd ON rrd.id = rru.decision_id
--where uid = 531
--where v.doc_id = 51 and role_id = 5
--order by id
;
GRANT ALL ON TABLE pjs.v_getdocumentsandreviewers TO iusrpmt;