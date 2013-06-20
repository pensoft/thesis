CREATE TABLE public.undisclosed_users(
	id int PRIMARY KEY DEFAULT nextval('usr_id_seq'::regclass),
	pwt_document_id int REFERENCES pwt.documents(id) NOT NULL,
	pjs_document_id int REFERENCES pjs.documents(id) NOT NULL,
	pjs_user_role_id int REFERENCES pjs.user_role_types(id) NOT NULL,
	uid int REFERENCES public.usr(id) NOT NULL,
	createdate timestamp DEFAULT now(),
	name varchar
);

GRANT ALL ON  public.undisclosed_users TO iusrpmt;

ALTER TABLE pjs.msg ADD COLUMN is_disclosed boolean DEFAULT true;
ALTER TABLE pjs.msg ADD COLUMN undisclosed_usr_id int REFERENCES public.undisclosed_users(id);

ALTER TABLE pwt.msg ADD COLUMN is_disclosed boolean DEFAULT true;
ALTER TABLE pwt.msg ADD COLUMN undisclosed_usr_id int REFERENCES public.undisclosed_users(id);

ALTER TABLE pjs.document_versions ADD COLUMN is_disclosed boolean DEFAULT true;
ALTER TABLE pjs.document_versions ADD COLUMN undisclosed_usr_id int REFERENCES public.undisclosed_users(id);

ALTER TABLE pjs.msg ALTER COLUMN is_disclosed SET NOT NULL;
ALTER TABLE pwt.msg ALTER COLUMN is_disclosed SET NOT NULL;
ALTER TABLE pjs.document_versions ALTER COLUMN is_disclosed SET NOT NULL;

ALTER TABLE pjs.document_versions ADD COLUMN createdate timestamp DEFAULT now();
