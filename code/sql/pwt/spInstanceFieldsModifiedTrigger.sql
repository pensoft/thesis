CREATE OR REPLACE FUNCTION spInstanceFieldsModifiedTrigger() RETURNS trigger AS 
$BODY$
    BEGIN	    
        PERFORM pwt.spMarkInstanceAsModified(NEW.instance_id, NEW.document_id);
        RETURN NEW;
    END;
$BODY$ 
LANGUAGE plpgsql;

GRANT EXECUTE ON FUNCTION spInstanceFieldsModifiedTrigger() TO iusrpmt;


CREATE TRIGGER instance_field_values_update_trigger
    AFTER UPDATE ON pwt.instance_field_values
    FOR EACH ROW
    WHEN (NEW.value_str IS DISTINCT FROM OLD.value_str OR
        	NEW.value_arr_str IS DISTINCT FROM OLD.value_arr_str OR
        	NEW.value_int IS DISTINCT FROM OLD.value_int OR
        	NEW.value_arr_int IS DISTINCT FROM OLD.value_arr_int OR
        	NEW.value_date IS DISTINCT FROM OLD.value_date OR
        	NEW.value_arr_date IS DISTINCT FROM OLD.value_arr_date OR
        	NEW.data_src_id IS DISTINCT FROM OLD.data_src_id)
    EXECUTE PROCEDURE spInstanceFieldsModifiedTrigger();