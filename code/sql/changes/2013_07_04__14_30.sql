CREATE TABLE pwt.custom_data_src_rules
(
	id serial PRIMARY KEY,
	name character varying,
	default_data_src_id int REFERENCES pwt.data_src(id)
)
WITH (
  OIDS=FALSE
);
GRANT ALL ON TABLE pwt.custom_data_src_rules TO pensoft;
GRANT ALL ON TABLE pwt.custom_data_src_rules TO iusrpmt;

CREATE TABLE pwt.custom_data_src_rules_parameters
(
  id serial PRIMARY KEY,
  rule_id int NOT NULL REFERENCES pwt.custom_data_src_rules(id),
  name character varying,
  ord integer NOT NULL,
  CONSTRAINT custom_data_src_rules_parameter_ord_key UNIQUE (rule_id, ord)
)
WITH (
  OIDS=FALSE
);
GRANT ALL ON TABLE pwt.custom_data_src_rules_parameters TO pensoft;
GRANT ALL ON TABLE pwt.custom_data_src_rules_parameters TO iusrpmt;

CREATE TABLE pwt.custom_data_src_rules_combinations
(
  id serial PRIMARY KEY,
  rule_id int NOT NULL REFERENCES pwt.custom_data_src_rules(id),
  data_src_id int NOT NULL REFERENCES pwt.data_src(id),
  priority integer NOT NULL DEFAULT 1,
  description character varying
)
WITH (
  OIDS=FALSE
);

GRANT ALL ON TABLE pwt.custom_data_src_rules_combinations TO pensoft;
GRANT ALL ON TABLE pwt.custom_data_src_rules_combinations TO iusrpmt;

CREATE TABLE pwt.custom_data_src_rules_combinations_details
(
	id serial PRIMARY KEY,
	combination_id int NOT NULL REFERENCES pwt.custom_data_src_rules_combinations(id),
	value integer NOT NULL,  
	parameter_id int NOT NULL REFERENCES pwt.custom_data_src_rules_parameters(id)  
)
WITH (
  OIDS=FALSE
);
GRANT ALL ON TABLE pwt.custom_data_src_rules_combinations_details TO pensoft;
GRANT ALL ON TABLE pwt.custom_data_src_rules_combinations_details TO postgres;

INSERT INTO pwt.custom_data_src_rules(name) 
	VALUES ('TTM Treatment data_src_id');
	
INSERT INTO pwt.custom_data_src_rules_parameters(rule_id, name, ord) 
	VALUES (1, 'Nomenclatural code', 1), (1, 'Treatment type', 2), (1, 'Rank', 3);
	
INSERT INTO pwt.custom_data_src_rules_combinations(rule_id, data_src_id, priority, description)
	VALUES (1, 49, 1, 'TT Material type ICN new genus'), -- 1 
		(1, 49, 2, 'TT Material type ICN redescription genus'), -- 2
		(1, 49, 3, 'TT Material type ICN redescription species'), -- 3
		(1, 48, 4, 'TT Material type ICZN new genus'), -- 4
		(1, 48, 5, 'TT Material type ICZN redescription genus'), -- 5 
		(1, 48, 6, 'TT Material type ICZN redescription species'), -- 6
		(1, 36, 2, 'TT Material type ICN new species'), -- 7
		(1, 10, 3, 'TT Material type ICZN new species');-- 8

INSERT INTO pwt.custom_data_src_rules_combinations_details(combination_id, parameter_id, value)
	VALUES (1, 1, 2), (1, 2, 1), (1, 3, 2),
	(2, 1, 2), (2, 2, 5), (2, 3, 2),
	(3, 1, 2), (3, 2, 5), (3, 3, 1),
	(4, 1, 1), (4, 2, 1), (4, 3, 2),
	(5, 1, 1), (5, 2, 5), (5, 3, 2),
	(6, 1, 1), (6, 2, 5), (6, 3, 1),
	(7, 1, 2), (7, 2, 1), (7, 3, 1),
	(8, 1, 1), (8, 2, 1), (8, 3, 1);
		
/*
 * Modified SP
 * spPerformCustomDataSrcRule 
 * spperformttmaftercreation
 */