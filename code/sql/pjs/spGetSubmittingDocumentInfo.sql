DROP TYPE IF EXISTS  pjs."ret_spGetSubmittingDocumentInfo" CASCADE;
CREATE TYPE pjs."ret_spGetSubmittingDocumentInfo" AS (
	document_id bigint,
	createuid bigint,
	document_source_id int,
	name varchar,
	abstract varchar,
	keywords varchar,
	journal_id int,
	authors_names varchar,
	submitting_author_name varchar,
	taxon_categories text,
	geographical_categories text,
	subject_categories text,
	chronological_categories text,
	supporting_agencies text,
	supporting_agencies_txts varchar,
	pwt_paper_type_id int,
	journal_section varchar
);

CREATE OR REPLACE FUNCTION pjs."spGetSubmittingDocumentInfo"(
	pDocumentId bigint
)
  RETURNS pjs."ret_spGetSubmittingDocumentInfo"  AS
$BODY$
	DECLARE
		lRes pjs."ret_spGetSubmittingDocumentInfo" ;
		cAuthorRoleId CONSTANT int := 11;
	BEGIN
		SELECT INTO lRes
			d.id as document_id, 
			d.submitting_author_id as createuid, 
			d.document_source_id,
			d.name as name,
			d.abstract,
			d.keywords,
			d.journal_id,
			(SELECT string_agg(u.first_name || ' ' || u.last_name || 
													(case when co_author = 1 then '*'
																										else '' end) || ' - ' || u.affiliation, '<br />') as author_name
				
			 FROM pjs.document_users du 
			 JOIN public.usr u ON du.uid = u.id
			 WHERE du.document_id = pDocumentId
			  AND role_id = cAuthorRoleId)
			   as author_name,
			   
			   (SELECT u.first_name || ' ' || u.last_name			 																						
					from public.usr  u
						WHERE u.id = d.submitting_author_id) as  submitting_author_name,
			   
			   
			(SELECT string_agg("name", ', ') FROM public.subject_categories     	WHERE id = ANY(d.subject_categories)) 		AS subject_categories,
			(SELECT string_agg("name", ', ') FROM public.taxon_categories        	WHERE id = ANY(d.taxon_categories)) 		AS taxon_categories, 
			(SELECT string_agg("name", ', ') FROM public.geographical_categories 	WHERE id = ANY(d.geographical_categories)) 	AS geographical_categories,
			(SELECT string_agg("name", ', ') FROM public.chronological_categories   WHERE id = ANY(d.chronological_categories)) AS chronological_categories,
			(SELECT string_agg(title, '<br />')  FROM public.supporting_agencies 		WHERE id = ANY(d.supporting_agencies_ids)) 	AS supporting_agencies,
			d.supporting_agencies_texts as supporting_agencies_txts,
			d.pwt_paper_type_id as pwt_paper_type_id,
			js.title as journal_section
		FROM pjs.documents d
		LEFT JOIN pjs.journal_sections js ON js.id = d.journal_section_id AND js.journal_id = d.journal_id
		WHERE  d.id = pDocumentId;	
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION  pjs."spGetSubmittingDocumentInfo"(
	pDocumentId bigint
) TO iusrpmt;

