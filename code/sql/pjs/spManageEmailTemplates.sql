DROP FUNCTION IF EXISTS pjs.spmanageemailtemplates(pOper int, pID int, pTemplateName varchar, pEventTypeId int, pJournalId int, pSubject varchar, pBody varchar, pParent int, pSubjectDefault int, pBodyDefault int);
DROP TYPE IF EXISTS ret_manageemailtemplates;

CREATE TYPE ret_manageemailtemplates AS (
	id int,
	template_name varchar,
	event_type_id int,
	parent_id int,
	type varchar,
	subject varchar,
	default_subject int,
	template_body varchar,
	default_body int,
	event_type varchar,
	recipients varchar
);

CREATE OR REPLACE FUNCTION pjs.spmanageemailtemplates(pOper int, pID int, pTemplateName varchar, pEventTypeId int, pJournalId int, pSubject varchar, pBody varchar, pParent int, pSubjectDefault int, pBodyDefault int)
  RETURNS ret_manageemailtemplates AS
$$
DECLARE
	lRes ret_manageemailtemplates;
	lId int;
	lSubject varchar;
	lSubjectDefault varchar;
	lContent varchar;
	lContentDefault varchar;
	lState int;
	lIs_automated boolean;
BEGIN
	--~ lRes.id := pID;
	
	IF pOper = 0 THEN -- GET
			SELECT 
				INTO lRes.template_name, lRes.parent_id, lRes.event_type_id, lRes.recipients, lRes.type, lRes.subject, lRes.default_subject, lRes.template_body, lRes.default_body, lRes.event_type
				t1.name, max(t2.parent_id) as parent_id, t1.event_type_id as event_type_id, array_agg(grp.name) as recipients,
				(case when t1.is_automated = true THEN 'automated' ELSE 'controlled' END) as is_automated,
				(case when max(t2.subject) is not null then max(t2.subject) else t1.subject end) as subject,
				(case when max(t2.subject) is not null then 0 else 1 end) as subject_state,
				(case when max(t2.content_template) is not null then max(t2.content_template) else t1.content_template end) as body,
				(case when max(t2.content_template) is not null then 0 else 1 end) as body_state,
				max(e.name)
			FROM pjs.email_task_definitions t1
			left join pjs.email_task_definitions t2 ON (t1.id = t2.parent_id and t2.journal_id = 1) 
			left join pjs.event_types e ON t1.event_type_id = e.id
			left join pjs.email_groups grp ON grp.id = ANY(t1.recipients)
			WHERE t1.id = pID
			GROUP BY t1.id;
		lRes.id = pID;
	ELSEIF pOper = 1 THEN --INSERT UPDATE
			SELECT INTO lId, lSubjectDefault, lContentDefault, lIs_automated id, subject, content_template, is_automated from pjs.email_task_definitions WHERE id = pID; -- Original template data
			
			lSubject = pSubject;
			lContent = pBody;
			
			IF pSubjectDefault = 1 THEN -- Default
				lSubject = NULL;
			ELSE 
				lState = 1;
			END IF;
			
			IF pBodyDefault = 1 THEN -- Default
				lContent = NULL;
			ELSE 
				lState = 1;
			END IF;

			IF pParent IS NULL AND lState = 1 THEN -- nqma nov templeit polzva se default -- INSERT
				INSERT INTO pjs.email_task_definitions (name, journal_id, event_type_id, subject, content_template, is_automated, parent_id)
					VALUES (pTemplateName, pJournalId, pEventTypeId, lSubject, lContent, lIs_automated, pID);
			ELSE -- template e predefiniran -- UPDATE
				UPDATE pjs.email_task_definitions SET subject = lSubject, content_template = lContent WHERE parent_id = pParent;
			END IF;
			
			IF pSubjectDefault = 1 AND pBodyDefault = 1 THEN
				DELETE FROM pjs.email_task_definitions WHERE parent_id = pParent;
			END IF;
	ELSEIF pOper = 3 THEN -- DELETE
		
		
	END IF;
	
	RETURN lRes;
END;
$$
  LANGUAGE 'plpgsql' SECURITY DEFINER;

ALTER FUNCTION pjs.spmanageemailtemplates(pOper int, pID int, pTemplateName varchar, pEventTypeId int, pJournalId int, pSubject varchar, pBody varchar, pParent int, pSubjectDefault int, pBodyDefault int) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pjs.spmanageemailtemplates(pOper int, pID int, pTemplateName varchar, pEventTypeId int, pJournalId int, pSubject varchar, pBody varchar, pParent int, pSubjectDefault int, pBodyDefault int) TO public;
GRANT EXECUTE ON FUNCTION pjs.spmanageemailtemplates(pOper int, pID int, pTemplateName varchar, pEventTypeId int, pJournalId int, pSubject varchar, pBody varchar, pParent int, pSubjectDefault int, pBodyDefault int) TO postgres;
GRANT EXECUTE ON FUNCTION pjs.spmanageemailtemplates(pOper int, pID int, pTemplateName varchar, pEventTypeId int, pJournalId int, pSubject varchar, pBody varchar, pParent int, pSubjectDefault int, pBodyDefault int) TO iusrpmt;
GRANT EXECUTE ON FUNCTION pjs.spmanageemailtemplates(pOper int, pID int, pTemplateName varchar, pEventTypeId int, pJournalId int, pSubject varchar, pBody varchar, pParent int, pSubjectDefault int, pBodyDefault int) TO pensoft;
