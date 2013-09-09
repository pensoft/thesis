CREATE OR REPLACE FUNCTION spInstanceModifiedTrigger() RETURNS trigger AS 
$BODY$
    BEGIN
	    IF TG_OP = 'INSERT' OR TG_OP = 'UPDATE' THEN
        	PERFORM pwt.spMarkInstanceAsModified(NEW.id, NEW.document_id);        
        	RETURN NEW;
        ELSIF TG_OP = 'DELETE' THEN
        	PERFORM pwt.spMarkInstanceAsModified(OLD.parent_id, OLD.document_id);  
        	RETURN OLD;
        END IF;
        RETURN NEW;
    END;
$BODY$ 
LANGUAGE plpgsql;

GRANT EXECUTE ON FUNCTION spInstanceModifiedTrigger() TO iusrpmt;

DROP TRIGGER instance_update_trigger ON pwt.document_object_instances;
CREATE TRIGGER instance_update_trigger
    AFTER UPDATE ON pwt.document_object_instances
    FOR EACH ROW
    WHEN ((NEW.pos IS DISTINCT FROM OLD.pos OR
        	NEW.display_name IS DISTINCT FROM OLD.display_name OR
        	NEW.is_confirmed IS DISTINCT FROM OLD.is_confirmed))
    EXECUTE PROCEDURE spInstanceModifiedTrigger();

DROP TRIGGER instance_create_trigger ON pwt.document_object_instances;    
CREATE TRIGGER instance_create_trigger
    AFTER INSERT ON pwt.document_object_instances
    FOR EACH ROW    
    EXECUTE PROCEDURE spInstanceModifiedTrigger();
    
DROP TRIGGER instance_delete_trigger ON pwt.document_object_instances;
CREATE TRIGGER instance_delete_trigger
    AFTER DELETE ON pwt.document_object_instances
    FOR EACH ROW
    EXECUTE PROCEDURE spInstanceModifiedTrigger();