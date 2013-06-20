create table countries(
	id serial NOT NULL,
	name varchar(128) not null,
	code varchar(8),
	state int not null default 1,
	CONSTRAINT countries_pkey PRIMARY KEY (id)	
) WITH (OIDS=TRUE);
ALTER TABLE countries OWNER TO postgres;
GRANT ALL ON TABLE countries TO postgres;
GRANT SELECT ON TABLE countries TO iusrpmt;

create table usr_titles(
	id serial NOT NULL,
	name varchar(128) not null,
	state int not null default 1,
	CONSTRAINT usr_titles_pkey PRIMARY KEY (id)	
) WITH (OIDS=TRUE);
ALTER TABLE usr_titles OWNER TO postgres;
GRANT ALL ON TABLE usr_titles TO postgres;
GRANT SELECT ON TABLE usr_titles TO iusrpmt;

create table client_types(
	id serial NOT NULL,
	name varchar(128) not null,
	state int not null default 1,
	CONSTRAINT client_type_pkey PRIMARY KEY (id)	
) WITH (OIDS=TRUE);
ALTER TABLE client_types OWNER TO postgres;
GRANT ALL ON TABLE client_types TO postgres;
GRANT SELECT ON TABLE client_types TO iusrpmt;

create table usr_alerts_frequency(
	id serial NOT NULL,
	name varchar(128) not null,
	state int not null default 1,
	CONSTRAINT usr_alerts_frequency_pkey PRIMARY KEY (id)	
) WITH (OIDS=TRUE);
ALTER TABLE usr_alerts_frequency OWNER TO postgres;
GRANT ALL ON TABLE usr_alerts_frequency TO postgres;
GRANT SELECT ON TABLE usr_alerts_frequency TO iusrpmt;

create table journals(
	id serial NOT NULL,
	name varchar(128) not null,
	state int not null default 1,
	CONSTRAINT journals_pkey PRIMARY KEY (id)	
) WITH (OIDS=TRUE);
ALTER TABLE journals OWNER TO postgres;
GRANT ALL ON TABLE journals TO postgres;
GRANT SELECT ON TABLE journals TO iusrpmt;

create table product_types(
	id serial NOT NULL,
	name varchar(128) not null,
	state int not null default 1,
	CONSTRAINT product_types_pkey PRIMARY KEY (id)	
) WITH (OIDS=TRUE);
ALTER TABLE product_types OWNER TO postgres;
GRANT ALL ON TABLE product_types TO postgres;
GRANT SELECT ON TABLE product_types TO iusrpmt;


create table subject_categories(
	id serial NOT NULL,
	name varchar(128) not null,
	state int not null default 1,
	rootnode int not null default 0,
	pos varchar not null,
	CONSTRAINT subject_categories_pkey PRIMARY KEY (id)	
) WITH (OIDS=TRUE);
ALTER TABLE subject_categories OWNER TO postgres;
GRANT ALL ON TABLE subject_categories TO postgres;
GRANT SELECT ON TABLE subject_categories TO iusrpmt;


create table taxon_categories(
	id serial NOT NULL,
	name varchar(128) not null,
	state int not null default 1,
	rootnode int not null default 0,
	pos varchar not null,
	CONSTRAINT taxon_categories_pkey PRIMARY KEY (id)	
) WITH (OIDS=TRUE);
ALTER TABLE taxon_categories OWNER TO postgres;
GRANT ALL ON TABLE taxon_categories TO postgres;
GRANT SELECT ON TABLE taxon_categories TO iusrpmt;


create table chronological_categories(
	id serial NOT NULL,
	name varchar(128) not null,
	state int not null default 1,
	rootnode int not null default 0,
	pos varchar not null,
	CONSTRAINT chronological_categories_pkey PRIMARY KEY (id)	
) WITH (OIDS=TRUE);
ALTER TABLE chronological_categories OWNER TO postgres;
GRANT ALL ON TABLE chronological_categories TO postgres;
GRANT SELECT ON TABLE chronological_categories TO iusrpmt;

create table geographical_categories(
	id serial NOT NULL,
	name varchar(128) not null,
	state int not null default 1,
	rootnode int not null default 0,
	pos varchar not null,
	CONSTRAINT geographical_categories_pkey PRIMARY KEY (id)	
) WITH (OIDS=TRUE);
ALTER TABLE geographical_categories OWNER TO postgres;
GRANT ALL ON TABLE geographical_categories TO postgres;
GRANT SELECT ON TABLE geographical_categories TO iusrpmt;