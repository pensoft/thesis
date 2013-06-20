CREATE FUNCTION spUpdateDocumentDirtyFlag() 
	RETURNS TRIGGER AS $BODY$
    BEGIN
	
		IF(TG_OP = 'INSERT' OR TG_OP = 'UPDATE') THEN
			UPDATE pwt.documents SET xml_is_dirty = TRUE WHERE id = NEW.document_id;
			RETURN NEW;
		ELSE
			UPDATE pwt.documents SET xml_is_dirty = TRUE WHERE id = OLD.document_id;
			RETURN OLD;
		END IF;
		
    END;
$BODY$ LANGUAGE plpgsql;

CREATE TRIGGER document_is_dirty_update BEFORE INSERT OR UPDATE OR DELETE ON pwt.media
    FOR EACH ROW EXECUTE PROCEDURE spUpdateDocumentDirtyFlag();

CREATE TRIGGER document_is_dirty_update BEFORE INSERT OR UPDATE OR DELETE ON pwt.tables
    FOR EACH ROW EXECUTE PROCEDURE spUpdateDocumentDirtyFlag();