CREATE TABLE pjs.journals(
	id serial PRIMARY KEY,
	name varchar,
	description varchar
)
WITH OIDS
;

COMMENT ON COLUMN pjs.journals.name IS 'Name of the journal';
COMMENT ON COLUMN pjs.journals.description IS 'Description of the journal';

CREATE TABLE pjs.journal_issues(
	id serial PRIMARY KEY,
	journal_id int REFERENCES pjs.journals(id) NOT NULL, 
	name varchar,
	description varchar,
	is_published boolean DEFAULT false NOT NULL, 
	date_published timestamp, 
	is_regular_issue boolean DEFAULT true NOT NULL, 
	url_title varchar 
);

COMMENT ON COLUMN pjs.journal_issues.name IS 'Name of the issue';
COMMENT ON COLUMN pjs.journal_issues.description IS 'Description of the issue';
COMMENT ON COLUMN pjs.journal_issues.journal_id IS 'The journal of the issue';
COMMENT ON COLUMN pjs.journal_issues.is_published IS 'Whether the issue is published or not';
COMMENT ON COLUMN pjs.journal_issues.date_published IS 'The timestamp when the issue was published';
COMMENT ON COLUMN pjs.journal_issues.is_regular_issue IS 'Whether the issue is regular or special';
COMMENT ON COLUMN pjs.journal_issues.url_title IS 'The url title of the issue in the system';

CREATE TABLE pjs.document_states(
	id serial PRIMARY KEY,
	name varchar
);

INSERT INTO pjs.document_states(name) VALUES ('New article'), ('Submitted by author'), ('In review'), ('In layout review'), ('Published'), ('Archived');

CREATE TABLE pjs.documents(
	id bigserial PRIMARY KEY,
	submitting_author_id int REFERENCES public.usr(id) NOT NULL, 
	name varchar DEFAULT 'Untitled' NOT NULL,
	create_date timestamp DEFAULT now() NOT NULL,
	state_id int REFERENCES pjs.document_states(id) NOT NULL DEFAULT 1, 
	lastmod_date timestamp DEFAULT now() NOT NULL,
	is_approved boolean DEFAULT false NOT NULL,
	approve_date timestamp,
	is_published boolean DEFAULT false NOT NULL,
	publish_date timestamp
);

COMMENT ON COLUMN pjs.documents.name IS 'Name of the document';
COMMENT ON COLUMN pjs.documents.submitting_author_id IS 'ID of the submitting author of the document';
COMMENT ON COLUMN pjs.documents.create_date IS 'The timestamp when the document was created';
COMMENT ON COLUMN pjs.documents.state_id IS 'The id of the current state of the document';
COMMENT ON COLUMN pjs.documents.lastmod_date IS 'The timestamp when the document was changed last';
COMMENT ON COLUMN pjs.documents.is_approved IS 'Whether the document is approved or not';
COMMENT ON COLUMN pjs.documents.approve_date IS 'The timestamp when the document was approved';
COMMENT ON COLUMN pjs.documents.is_published  IS 'Whether the document is published or not';
COMMENT ON COLUMN pjs.documents.publish_date IS 'The timestamp when the document was published';

CREATE TABLE pjs.document_types(
	id serial PRIMARY KEY,
	name varchar
);

INSERT INTO pjs.document_types(name) VALUES ('PWT document'), ('Document from files');

ALTER TABLE pjs.documents ADD COLUMN document_type int REFERENCES pjs.document_types(id) NOT NULL;
COMMENT ON COLUMN pjs.documents.document_type IS 'This field indicates whether the document is a pwt document or a document from files';

CREATE TABLE pjs.document_version_types(
	id serial PRIMARY KEY,
	name varchar
);

INSERT INTO pjs.document_version_types(name) VALUES ('Author submitted version'), ('Reviewer version'), ('SE version');

CREATE TABLE pjs.document_versions(
	id bigserial PRIMARY KEY,
	uid bigint REFERENCES usr(id) NOT NULL,
	version_num int,
	version_type_id int REFERENCES pjs.document_version_types(id) NOT NULL
);

CREATE TABLE pjs.pwt_documents(
  document_id bigint REFERENCES pjs.documents(id) NOT NULL,
  pwt_id bigint REFERENCES pwt.documents(id) NOT NULL,  
  createdate timestamp without time zone NOT NULL DEFAULT now(),
  createuid integer,
  lastmoduid integer,
  journal_id integer
)
WITH (
  OIDS=FALSE
);
GRANT ALL ON TABLE pjs.pwt_documents TO iusrpmt;

CREATE TABLE pjs.pwt_document_versions(
	id bigserial PRIMARY KEY,
	version_id bigint REFERENCES pjs.document_versions(id) NOT NULL,
	"xml" xml,
	createdate timestamp DEFAULT now()
);

CREATE TABLE pjs.pwt_document_version_change_states(
	id serial PRIMARY KEY,
	name varchar
);

INSERT INTO pjs.pwt_document_version_change_states(name) VALUES ('New'), ('Processed');

CREATE TABLE pjs.pwt_document_version_changes(
	id bigserial PRIMARY KEY,
	pwt_document_version_id bigint REFERENCES pjs.pwt_document_versions(id) NOT NULL,
	instance_id bigint NOT NULL,
	field_id bigint NOT NULL,
	createdate timestamp DEFAULT now(),
	state_id int REFERENCES pjs.pwt_document_version_change_states(id) NOT NULL DEFAULT 1,
	value varchar
);

CREATE TABLE pjs.pwt_documents_msg
(
  id serial NOT NULL,
  document_version_id bigint REFERENCES pjs.pwt_document_versions(id) NOT NULL,
  document_id bigint REFERENCES pjs.documents(id) NOT NULL,
  author character varying(128),
  subject character varying NOT NULL,
  msg character varying NOT NULL,
  senderip inet,
  mdate timestamp without time zone NOT NULL DEFAULT ('now'::text)::timestamp(6) with time zone,
  rootid integer,
  ord character varying,
  usr_id integer,
  flags integer NOT NULL DEFAULT 0,
  replies integer DEFAULT 0,
  views integer DEFAULT 0,
  lastmoddate timestamp without time zone NOT NULL DEFAULT ('now'::text)::timestamp(6) with time zone,
  root_object_instance_id bigint,
  start_object_instances_id bigint,
  end_object_instances_id bigint,
  start_object_field_id bigint,
  end_object_field_id bigint,
  start_offset integer,
  end_offset integer,
  CONSTRAINT msg_usr_id_fkey FOREIGN KEY (usr_id)
      REFERENCES usr (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=TRUE
);



CREATE TABLE pjs.journal_issue_documents(
	id bigint PRIMARY KEY,
	issue_id int REFERENCES pjs.journal_issues(id) NOT NULL,
	document_id bigint REFERENCES pwt.documents(id) NOT NULL,
	ord int DEFAULT 1 NOT NULL,
	start_page int DEFAULT 1 NOT NULL,
	end_page int DEFAULT 1 NOT NULL,
	CHECK (end_page >= start_page)
);

COMMENT ON COLUMN pjs.journal_issue_documents.issue_id IS 'The id of the issue';
COMMENT ON COLUMN pjs.journal_issue_documents.document_id IS 'The id of the document';
COMMENT ON COLUMN pjs.journal_issue_documents.ord IS 'The index of the document';
COMMENT ON COLUMN pjs.journal_issue_documents.start_page IS 'The number of the page of the issue on which the document begins';
COMMENT ON COLUMN pjs.journal_issue_documents.end_page IS 'The number of the page of the issue on which the document ends';

CREATE TABLE pjs.document_user_roles(
	id serial PRIMARY KEY,
	name varchar
);

INSERT INTO pjs.document_user_roles(name) VALUES ('Submitting author'), ('Subject editor'), ('Layout editor'), ('Dedicated reviewer'), ('Community peer reviewer');

CREATE TABLE pjs.document_users(
	id bigserial PRIMARY KEY,
	document_id bigint REFERENCES pjs.documents(id) NOT NULL,
	user_id int REFERENCES public.usr(id) NOT NULL,
	role_id int REFERENCES pjs.document_user_roles(id) NOT NULL
);

COMMENT ON COLUMN pjs.document_users.document_id IS 'The id of the document';
COMMENT ON COLUMN pjs.document_users.user_id IS 'The id of the user';
COMMENT ON COLUMN pjs.document_users.role_id IS 'The id of the role which the specified user has for the specified document';

CREATE TABLE pjs.document_review_round_types(
	id serial PRIMARY KEY,
	name varchar
);

INSERT INTO pjs.document_review_round_types(name) VALUES ('Review'), ('Linguistic review'), ('Layout review');

CREATE TABLE pjs.document_review_round_decisions(
	id serial PRIMARY KEY,
	name varchar
);

INSERT INTO pjs.document_review_round_decisions(name) VALUES ('Accept'), ('Reject'), ('Reject with minor corrections'), ('Reject with major corrections');

CREATE TABLE pjs.document_review_rounds(
	id bigserial PRIMARY KEY,
	document_id bigint REFERENCES pjs.documents(id),
	decision_id int REFERENCES pjs.document_review_round_decisions(id),
	round_number int DEFAULT 1 NOT NULL,
	round_type_id int REFERENCES pjs.document_review_round_types(id) NOT NULL
);
COMMENT ON COLUMN pjs.document_review_rounds.decision_id IS 'The id of the final decision of the current round';
COMMENT ON COLUMN pjs.document_review_rounds.document_id IS 'The id of document which is being reviewed';
COMMENT ON COLUMN pjs.document_review_rounds.round_number IS 'The number of the round for the specified review type';
COMMENT ON COLUMN pjs.document_review_rounds.round_type_id IS 'The type of the review round (e.g. layout review round, linguistic review round etc.)';

CREATE TABLE pjs.document_review_round_reviewers(
	id bigserial PRIMARY KEY,
	round_id bigint REFERENCES pjs.document_review_rounds(id) NOT NULL,
	decision_id int REFERENCES pjs.document_review_round_decisions(id),
	document_user_id bigint REFERENCES pjs.document_users(id) NOT NULL,
	document_version_id bigint REFERENCES pjs.document_versions(id) NOT NULL
);

COMMENT ON COLUMN pjs.document_review_round_reviewers.decision_id IS 'The id of the final decision of the reviewer';
COMMENT ON COLUMN pjs.document_review_round_reviewers.round_id IS 'The id of the review round for which the reviewer takes decision';
COMMENT ON COLUMN pjs.document_review_round_reviewers.document_user_id IS 'The id of the document user (which consists of user id and role id) who is making the review';


CREATE TABLE pjs.event_types(
	id serial PRIMARY KEY,
	name varchar
);

COMMENT ON TABLE pjs.event_types IS 'This table holds the different types of events (e.g. document publication, addition of document reviewer ...)';

CREATE TABLE pjs.event_data_value_types(
	id serial PRIMARY KEY,
	name varchar,
	column_name varchar
);

COMMENT ON TABLE pjs.event_data_value_types IS 'This table holds the different types of the value of the data of the events (e.g. int field, int array field ...)';

COMMENT ON COLUMN pjs.event_data_value_types.column_name IS 'The column in the pjs.event_data table in which the real value for the data is stored(e.g value_int)';


CREATE TABLE pjs.event_data_types(
	id serial PRIMARY KEY,
	name varchar,
	value_type int REFERENCES pjs.event_data_value_types(id) NOT NULL
);

COMMENT ON TABLE pjs.event_data_types IS 'This table holds the different types of data which an event may have (e.g. the id of the reviewer, which has been added for a specific document)';

CREATE TABLE pjs.event_log(
	id bigserial PRIMARY KEY,
	event_type_id int REFERENCES pjs.event_types(id) NOT NULL,
	journal_id int REFERENCES pjs.journals(id) NOT NULL,
	eventdate timestamp DEFAULT now() NOT NULL
);
COMMENT ON TABLE pjs.event_log IS 'This table holds a log of all the events that have occurred in the system';
COMMENT ON COLUMN pjs.event_log.eventdate IS 'The timestamp when the specific event occurred';

CREATE TABLE pjs.event_data(
	id bigserial PRIMARY KEY,
	event_id bigint REFERENCES pjs.event_log(id) NOT NULL,
	value_int bigint,
	value_int_arr bigint[],
	value_str varchar,
	value_str_arr varchar[],
	value_date timestamp,
	value_date_arr timestamp
);
COMMENT ON TABLE pjs.event_data IS 'This table holds the data for each event from the log';

INSERT INTO pjs.event_data_value_types(name, column_name) VALUES ('Int', 'value_int'), ('Int array', 'value_int_arr'), ('String', 'value_str'), ('String array', 'value_str_arr'), ('Date', 'value_date'), ('Date array', 'value_date_arr');


CREATE TABLE pjs.email_templates(
	id serial PRIMARY KEY,
	name varchar NOT NULL,
	content varchar 
);
COMMENT ON TABLE pjs.email_templates IS 'This table holds the base email templates for the mails which will be generated when a specific event occurs';

CREATE TABLE pjs.email_task_definitions(
	id serial PRIMARY KEY,
	name varchar NOT NULL,
	event_type_id int REFERENCES pjs.event_types(id) NOT NULL,
	default_template_id int REFERENCES pjs.email_templates(id) NOT NULL,
	is_automated boolean DEFAULT true,
	journal_id int REFERENCES pjs.journals(id) NOT NULL,
	recipients int NOT NULL
);
COMMENT ON TABLE pjs.email_task_definitions IS 'This table holds the definitions for the email tasks which should be created when a specific event for a specific journal occurs';

CREATE TABLE pjs.email_task_states(
	id serial PRIMARY KEY,
	name varchar
);

INSERT INTO pjs.email_task_states(name) VALUES ('New'), ('Sent'), ('Waiting to be sent');

CREATE TABLE pjs.email_tasks(
	id bigserial PRIMARY KEY,
	task_definition_id int REFERENCES pjs.email_task_definitions(id) NOT NULL,
	event_id bigint REFERENCES pjs.event_log(id) NOT NULL,
	state_id int REFERENCES pjs.email_task_states(id) NOT NULL,
	createdate timestamp DEFAULT now() NOT NULL,
	senddate timestamp,
);
COMMENT ON TABLE pjs.email_tasks IS 'This table holds the email tasks which have been generated for the past events';

CREATE TABLE pjs.email_task_detail_states(
	id serial PRIMARY KEY,
	name varchar
);

INSERT INTO pjs.email_task_detail_states(name) VALUES ('New'), ('Ready'), ('Skip'), ('Sent');

CREATE TABLE pjs.email_task_details(
	id bigserial PRIMARY KEY,
	email_task_id bigint REFERENCES pjs.email_tasks(id) NOT NULL,
	uid int REFERENCES usr(id) NOT NULL,
	state_id int REFERENCES pjs.email_task_detail_states(id) NOT NULL,
	template varchar
);

COMMENT ON TABLE pjs.email_task_details IS 'This table holds all the mails which should be sent for each task (i.e. the mails for each user who has to notified by the specific task)';