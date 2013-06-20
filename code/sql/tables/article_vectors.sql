CREATE TABLE article_vectors(
	id serial PRIMARY KEY,
	article_id int REFERENCES articles(id),
	title_vector tsvector,
	all_vector tsvector,
	CONSTRAINT article_vectors_article_id UNIQUE(article_id)
);

GRANT ALL ON TABLE article_vectors TO iusrpmt;