DROP TYPE ret_spGetIssueInfo CASCADE;
CREATE TYPE ret_spGetIssueInfo AS (
	issue_id bigint,
	issue_num integer,
	next_issue_id bigint,
	prev_issue_id bigint,
	issue_name character varying,
	issue_description character varying,
	min_issue_num integer,
	max_issue_num integer,
	issue_price numeric(10, 2),
	count_documents integer,
	count_pages integer,
	count_color_pages integer
);

CREATE OR REPLACE FUNCTION spGetIssueInfo(
	pJournalId bigint,
	pIssueId bigint,
	pIssueVolume integer
)
  RETURNS ret_spGetIssueInfo AS
$BODY$
	DECLARE
		lRes ret_spGetIssueInfo;		
	BEGIN
		
		IF pIssueVolume IS NOT NULL AND pIssueVolume > 0 THEN
			SELECT INTO lRes.issue_id, lRes.issue_num, lRes.issue_name, lRes.issue_description, 
						lRes.issue_price, lRes.count_documents, lRes.count_pages,
						lRes.count_color_pages 
						i.id, i.volume::int, i.name, i.description, i.price, 
						count(d.id) as count_documents, sum(d.number_of_pages) as count_pages,
						sum(d.number_of_color_pages) as count_color_pages 
				FROM pjs.journal_issues i
				LEFT JOIN journals j ON j.id = i.journal_id
				LEFT JOIN pjs.documents d ON d.issue_id = i.id AND d.is_published = TRUE
				WHERE i.journal_id = pJournalId 
					AND i.volume::int = pIssueVolume
					AND i.is_published = TRUE
					AND i.is_active = TRUE
				GROUP BY i.id, i.volume, i.name, i.description, i.price;
		ELSE
			SELECT INTO lRes.issue_id, lRes.issue_num, lRes.issue_name, lRes.issue_description, 
						lRes.issue_price, lRes.count_documents, lRes.count_pages,
						lRes.count_color_pages 
						i.id, i.volume::int, i.name, i.description, i.price, 
						count(d.id) as count_documents, sum(d.number_of_pages) as count_pages,
						sum(d.number_of_color_pages) as count_color_pages
				FROM pjs.journal_issues i
				LEFT JOIN journals j ON j.id = i.journal_id
				LEFT JOIN pjs.documents d ON d.issue_id = i.id AND d.is_published = TRUE
				WHERE i.journal_id = pJournalId 
					AND i.id = pIssueId
					AND i.is_published = TRUE
					AND i.is_active = TRUE
				GROUP BY i.id, i.volume, i.name, i.description, i.price;
		END IF;
		
		IF lRes.issue_id IS NULL THEN
			SELECT INTO lRes.issue_id, lRes.issue_num, lRes.issue_name, lRes.issue_description, 
						lRes.issue_price, lRes.count_documents, lRes.count_pages,
						lRes.count_color_pages 
						i.id, i.volume::int, i.name, i.description, i.price, 
						count(d.id) as count_documents, sum(d.number_of_pages) as count_pages,
						sum(d.number_of_color_pages) as count_color_pages 
				FROM pjs.journal_issues i
				LEFT JOIN journals j ON j.id = i.journal_id
				LEFT JOIN pjs.documents d ON d.issue_id = i.id AND d.is_published = TRUE
				WHERE i.journal_id = pJournalId 
					AND i.is_published = TRUE
					AND i.is_active = TRUE
				GROUP BY i.id, i.volume, i.name, i.description, i.price
				ORDER BY i.is_current DESC, i.date_published DESC
				LIMIT 1;
		END IF;
		
		SELECT INTO lRes.next_issue_id id 
			FROM pjs.journal_issues
			WHERE journal_id = pJournalId 
				AND is_published = TRUE
				AND is_active = TRUE
				AND volume::int > lRes.issue_num
			ORDER BY volume ASC
			LIMIT 1;
			
		SELECT INTO lRes.prev_issue_id id 
			FROM pjs.journal_issues
			WHERE journal_id = pJournalId 
				AND is_published = TRUE
				AND is_active = TRUE
				AND volume::int < lRes.issue_num
			ORDER BY volume DESC
			LIMIT 1;
		
		SELECT INTO lRes.min_issue_num, lRes.max_issue_num min(volume::int), max(volume::int)
			FROM pjs.journal_issues
			WHERE journal_id = pJournalId 
				AND is_published = TRUE
				AND is_active = TRUE;
				
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spGetIssueInfo(bigint, bigint, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spGetIssueInfo(bigint, bigint, integer) TO postgres;
GRANT EXECUTE ON FUNCTION spGetIssueInfo(bigint, bigint, integer) TO iusrpmt;
GRANT EXECUTE ON FUNCTION spGetIssueInfo(bigint, bigint, integer) TO pensoft;
