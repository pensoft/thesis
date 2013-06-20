GRANT SELECT ON pg_ts_cfg to iusrpmt;
GRANT SELECT ON pg_ts_cfgmap to iusrpmt;
GRANT SELECT ON pg_ts_dict to iusrpmt;
GRANT SELECT ON pg_ts_parser to iusrpmt;



INSERT INTO pg_ts_dict(dict_name, dict_init, dict_initoption, dict_lexize, dict_comment)
	SELECT 'simple_bg', dict_init, dict_initoption, dict_lexize, dict_comment
	FROM pg_ts_dict
	WHERE dict_name = 'simple';

UPDATE pg_ts_dict 
	SET dict_initoption='/usr/share/ispell_utf8/bulgarian.stop.utf8' 
	WHERE dict_name='simple_bg';


INSERT INTO pg_ts_cfg (ts_name, prs_name, locale) VALUES ('bg_utf8', 'default', 'bg_BG.UTF-8');

INSERT INTO pg_ts_dict(dict_name, dict_init, dict_initoption, dict_lexize, dict_comment)
	SELECT 'ispell_bg', dict_init, dict_initoption, dict_lexize, dict_comment
	FROM pg_ts_dict
	WHERE dict_name = 'ispell_template';

UPDATE pg_ts_dict 
	SET dict_initoption='DictFile="/usr/share/ispell_utf8/bulgarian.dict.utf8" ,AffFile="/usr/share/ispell_utf8/bulgarian.aff.utf8", StopFile="/usr/share/ispell_utf8/bulgarian.stop.utf8"' 
	WHERE dict_name='ispell_bg';

INSERT INTO pg_ts_cfgmap
SELECT 'bg_utf8', tok_alias, dict_name
FROM pg_ts_cfgmap
WHERE ts_name = 'utf8_russian';

UPDATE pg_ts_cfgmap
SET dict_name = array['ispell_bg','simple_bg']
WHERE ts_name in ('bg_utf8') and dict_name = array['ru_stem_utf8'];


-- тва е яко - връща или щото пазари има 2 смисъла!!!
-- SELECT to_tsquery('bg_utf8', 'европейските & пазари & продукт');

-- SELECT to_tsvector('simple', 'камен каменица иванка иван камъни стрели');
-- SELECT to_tsvector('bg_utf8', 'Мамичката ви мръсна искам стоп думи да има');
-- SELECT to_tsvector('bg_utf8', 'камен каменица иванка иван камъни стрели на');

-- SELECT lexize('ispell_bg', 'стрели');
-- SELECT lexize('ispell_bg', 'а');
-- SELECT lexize('ispell_bg', 'мамодер');
-- SELECT lexize('simple_bg', 'мамодер');