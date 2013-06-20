INSERT INTO node_split(name, xpath, climb_up) VALUES ('Object id rule', '//object-id', 0);
INSERT INTO node_split(name, xpath, climb_up) VALUES ('Taxon identifier rule', '//tp:taxon-identifier', 0);
INSERT INTO node_split(name, xpath, climb_up) VALUES ('Nomenclature citation rule', '//tp:nomenclature-citation', 0);
INSERT INTO node_split(name, xpath, climb_up) VALUES ('Tr rule', '//tr', 0);

CREATE TABLE indesign_remove_formatting_nodes(
	id serial NOT NULL,
	"name" character varying,
	xpath character varying
);

INSERT INTO indesign_remove_formatting_nodes(name, xpath) VALUES ('Figs rule', '//article_figs_and_tables/fig/label');
INSERT INTO indesign_remove_formatting_nodes(name, xpath) VALUES ('Article title rule', '//front/article-meta/title-group/article-title');
GRANT ALL ON TABLE indesign_remove_formatting_nodes TO iusrpmt