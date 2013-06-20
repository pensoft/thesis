--DROP TABLE transliteration_words;

CREATE TABLE transliteration_words (
	id serial PRIMARY KEY,
	word_bg varchar NOT NULL,
	word_en varchar NOT NULL
);

GRANT ALL ON TABLE transliteration_words TO postgres84;
GRANT SELECT, INSERT ON TABLE transliteration_words TO iusrpmt;