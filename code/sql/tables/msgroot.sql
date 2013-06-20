DROP TABLE msgroot;
CREATE TABLE msgroot (
	msgid int primary key,
	dscid int,
	itemid int,
	replies int,
	lastpostid int,
	lastpost timestamp
);
GRANT SELECT ON TABLE msgroot TO iusrpmt;

CREATE INDEX idx_msgroot_itemid on msgroot (itemid);

CREATE OR REPLACE FUNCTION msgroot_upd() RETURNS trigger AS $$
BEGIN
	IF (NEW.id = NEW.rootid) THEN
		UPDATE msgroot SET 
			dscid = NEW.dscid,
			itemid = NEW.itemid,
			replies = NEW.replies
		WHERE msgid = NEW.id;
				
		IF (NOT FOUND) THEN
			INSERT INTO msgroot (msgid, dscid, itemid, replies)
				VALUES(NEW.id, NEW.dscid, NEW.itemid, NEW.replies);
		END IF;
	END IF;
	
	UPDATE msgroot SET lastpostid = NEW.id, lastpost = now() WHERE msgid = NEW.rootid;
	
	RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER msgroot_upd AFTER INSERT OR UPDATE ON msg
    FOR EACH ROW EXECUTE PROCEDURE msgroot_upd();

-- s tozi insert sum initnal tablicata
-- INSERT INTO msgroot (msgid, dscid, itemid, replies)
-- SELECT id, dscid, itemid, replies FROM msg WHERE id = rootid;
