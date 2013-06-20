DROP TYPE IF EXISTS ret_spGetTaskDefinition CASCADE;
CREATE TYPE ret_spGetTaskDefinition AS (
	id int,
	name varchar,
	event_type_id int,
	is_automated boolean,
	journal_id integer,
	subject varchar,
	content_template varchar,
	recipients int[],
	document_journal_id int,
	document_id bigint
);

CREATE OR REPLACE FUNCTION spgettaskdefinition(pJournalId int, pdocumentid bigint, peventid bigint)
  RETURNS SETOF ret_spgettaskdefinition AS
$BODY$
	DECLARE
		lRes ret_spGetTaskDefinition;	
		lEventType int;
		lRecord record;
		lJournalId int;
	BEGIN
		SELECT INTO lEventType event_type_id FROM pjs.event_log WHERE id = pEventId;
		
		--RAISE EXCEPTION 'document_id: %', pDocumentId;
		IF(pDocumentId IS NOT NULL) THEN
			-- get Document Info
			SELECT INTO lJournalId journal_id FROM pjs.documents WHERE id = pDocumentId;
		ELSE
			lJournalId = pJournalId;
		END IF;

		FOR lRecord IN SELECT * FROM pjs.email_task_definitions etd WHERE event_type_id = lEventType AND parent_id IS NULL
		LOOP
			
			-- selecting child definition if exists
			SELECT INTO 
				lRes.id, 
				lRes.subject, 
				lRes.content_template, 
				lRes.recipients,
				lRes.is_automated
				
				id, 
				subject, 
				content_template, 
				coalesce(recipients, NULL),
				is_automated
			FROM pjs.email_task_definitions 
			WHERE parent_id = lRecord.id
				AND event_type_id = lEventType
				AND journal_id = lJournalId;
			
			IF(lRes.id IS NULL) THEN
				lRes.id  := lRecord.id;
			END IF;
			
			IF(lRes.subject IS NULL) THEN
				lRes.subject  := lRecord.subject;
			END IF;
			
			IF(lRes.content_template IS NULL) THEN
				lRes.content_template  := lRecord.content_template;
			END IF;
			
			IF(lRes.recipients IS NULL) THEN
				lRes.recipients := lRecord.recipients;
			END IF;
			
			IF(lRes.is_automated IS NULL) THEN
				lRes.is_automated  := lRecord.is_automated;
			END IF;
			
			lRes.name = lRecord.name;
			lRes.event_type_id = lRecord.event_type_id;
			lRes.journal_id = lRecord.journal_id;
			
			lRes.document_journal_id = lJournalId;
			lRes.document_id = pDocumentId;
			
			RETURN NEXT lRes;
		END LOOP;
		
	END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100
  ROWS 1000;
ALTER FUNCTION spgettaskdefinition(int, bigint, bigint) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spgettaskdefinition(int, bigint, bigint) TO public;
GRANT EXECUTE ON FUNCTION spgettaskdefinition(int, bigint, bigint) TO postgres;
GRANT EXECUTE ON FUNCTION spgettaskdefinition(int, bigint, bigint) TO iusrpmt;
GRANT EXECUTE ON FUNCTION spgettaskdefinition(int, bigint, bigint) TO pensoft;

