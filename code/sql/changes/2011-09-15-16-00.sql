INSERT INTO indesign_remove_formatting_nodes(name, xpath) VALUES ('Kwd group', '/article/front/article-meta/kwd-group/label');
ALTER TABLE indesign_remove_formatting_nodes RENAME TO indesign_remove_bold_formatting_nodes;
GRANT ALL ON TABLE indesign_remove_bold_formatting_nodes TO iusrpmt;
CREATE TABLE indesign_remove_italic_formatting_nodes(
	id serial NOT NULL,
	"name" character varying,
	xpath character varying
);
GRANT ALL ON TABLE indesign_remove_italic_formatting_nodes TO iusrpmt;

INSERT INTO indesign_remove_italic_formatting_nodes(name, xpath) VALUES ('Author notes', '/article/front/article-meta/author-notes');
INSERT INTO indesign_remove_italic_formatting_nodes(name, xpath) VALUES ('Self uri', '/article/front/article-meta/self-uri');
INSERT INTO indesign_remove_italic_formatting_nodes(name, xpath) VALUES ('Kwd group', '/article/front/article-meta/kwd-group/label');

