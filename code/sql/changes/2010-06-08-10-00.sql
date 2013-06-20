CREATE TABLE node_split (
	id serial,
	name varchar,
	xpath varchar,
	climb_up int DEFAULT 0
);

GRANT ALL ON node_split TO iusrpmt;

INSERT INTO secsites(id, url, name, cnt, ord, type) VALUES 
	(48, '/resources/node_split/', 'Split nodes rules', 3, 11, 1);	

INSERT INTO secgrpacc (sid, gid, type) VALUES 	
	(48, 2, 6);

ALTER SEQUENCE secsites_id_seq RESTART WITH 49;