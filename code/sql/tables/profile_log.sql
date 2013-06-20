CREATE TABLE profile_log(
	id serial PRIMARY KEY,
	taxon_name varchar,
	ip inet,
	date_logged timestamp,
	object_id varchar,
	object_classname varchar,
	object_parentobjectid varchar,
	object_params varchar,
	got_from_cache varchar,
	time_started timestamp,
	time_finished_retrieving_data timestamp,
	time_finished_parsing_data timestamp,
	seconds_retrieving float,
	seconds_parsing float	
	
);

GRANT ALL ON profile_log TO iusrpmt;