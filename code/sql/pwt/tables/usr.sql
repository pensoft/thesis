create table usr(
	id serial NOT NULL,
	uname character varying(32) NOT NULL,
	upass character varying(32) NOT NULL,
	first_name character varying(32) NOT NULL,
	middle_name character varying(32),
	last_name character varying(32) NOT NULL,
	usr_title_id int not null,
	client_type_id int not null,
	affiliation character varying(128) not null,
	departament character varying(128),
	addr_street character varying(128) not null,
	addr_postcode character varying(128) not null,
	addr_city character varying(128) not null,
	country_id int not null,
	phone character varying(64),
	fax character varying(64),
	vat character varying(64),
	website character varying(256),
	state integer NOT NULL DEFAULT 0,
	utype integer NOT NULL DEFAULT 0,
	photo_id int,
	journals int[],
	usr_alerts_frequency_id int,
	product_types int[],
	subject_categories int[],
	taxon_categories int[],
	chronological_categories int[],
	geographical_categories int[],
	confhash character varying(32), -- Confirmation hash used to activate the user
	create_date timestamp without time zone NOT NULL, -- Creation date
	activate_date timestamp without time zone, -- Activation date
	modify_date timestamp without time zone NOT NULL, -- Date of last modification
	access_date timestamp without time zone, -- Date of last access
	reg_ip inet, -- Registration IP address
	activate_ip inet, -- Activation IP address
	access_ip inet, -- IP address of last access
	CONSTRAINT usr_pkey PRIMARY KEY (id),
	CONSTRAINT usr_country_id_fkey FOREIGN KEY (country_id)
      REFERENCES countries (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT usr_client_type_id_fkey FOREIGN KEY (client_type_id)
      REFERENCES client_types (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT usr_usr_title_id_fkey FOREIGN KEY (usr_title_id)
      REFERENCES usr_titles (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT usr_photo_id_fkey FOREIGN KEY (photo_id)
      REFERENCES photos (guid) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT usr_alerts_frequency_id_fkey FOREIGN KEY (usr_alerts_frequency_id)
      REFERENCES usr_alerts_frequency (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
) WITH (OIDS=TRUE);
ALTER TABLE usr OWNER TO postgres;
GRANT ALL ON TABLE usr TO postgres;
GRANT SELECT ON TABLE usr TO iusrpmt;