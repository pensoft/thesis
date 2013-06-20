INSERT INTO secsites(id, url, name, cnt, ord, type) VALUES (54, '/resources/doaj_export/', 'DOAJ Export', 3, 13, 1);
ALTER SEQUENCE secsites_id_seq RESTART WITH 55;


INSERT INTO secgrpacc(gid, sid, type) VALUES (2, 54, 6);