  DROP TYPE pjs.ret_spgetdocumentinfoforpdf CASCADE;

CREATE TYPE pjs.ret_spgetdocumentinfoforpdf AS
   (document_title character varying,
    document_id bigint,
    author_list character varying,
    author_list_short character varying,
    document_type_name character varying,
    doi character varying,
    idtext character varying,
    year character varying
    );
ALTER TYPE pjs.ret_spgetdocumentinfoforpdf
  OWNER TO postgres;


-- DROP FUNCTION pjs."spGetDocumentInfoForPDF"(bigint);

CREATE OR REPLACE FUNCTION pjs."spGetDocumentInfoForPDF"(pdocumentid bigint)
  RETURNS pjs.ret_spgetdocumentinfoforpdf AS
$BODY$

	SELECT 
    
	trim(regexp_replace(d.name, E'[\\n\\r]+', ' ', 'g' )) AS document_title,
	pDocumentId as document_id,
	(SELECT aggr_concat_coma(a.author_name)
		FROM (
			SELECT (du.last_name || ' ' || substring(du.first_name, 1, 1)) as author_name 
			FROM pjs.document_users du
			WHERE du.document_id = pDocumentId AND du.role_id = 11 AND du.state_id = 1
			ORDER BY du.ord
		) a) as author_list,
	(SELECT case when count(*) < 3 then aggr_concat_coma(a.author_name)  
			else ((array_agg(author_name))[1] || ' et al.') end
	FROM (
		SELECT (du.last_name || ' ' || substring(du.first_name from 1 for 1)) as author_name 
		FROM pjs.document_users du
		WHERE du.document_id = pDocumentId AND du.role_id = 11 AND du.state_id = 1
		ORDER BY du.ord
	) a) as author_list_short,
	js.title as document_type_name,
	d.doi,
	((select name from journals where id = d.journal_id) || ' ' ||
	 (select "number" from pjs.journal_issues where id = d.issue_id) ||': e' || d.id::text) as idtext,
	extract(year from d.publish_date)::text as year
	FROM pjs.documents d
	JOIN pjs.document_review_types drt ON drt.id = d.document_review_type_id
	JOIN pjs.journal_sections js ON js.id = d.journal_section_id
	WHERE d.id = pDocumentId;
	
    
$BODY$
  LANGUAGE sql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION pjs."spGetDocumentInfoForPDF"(bigint)
  OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pjs."spGetDocumentInfoForPDF"(bigint) TO public;
GRANT EXECUTE ON FUNCTION pjs."spGetDocumentInfoForPDF"(bigint) TO postgres;
GRANT EXECUTE ON FUNCTION pjs."spGetDocumentInfoForPDF"(bigint) TO iusrpmt;
GRANT EXECUTE ON FUNCTION pjs."spGetDocumentInfoForPDF"(bigint) TO pensoft;
