DROP VIEW pwt.v_tabbed_items;
CREATE OR REPLACE VIEW pwt.v_tabbed_items AS 
	SELECT ti.id as tabbed_item_id, ti.default_active_object_id, tid.pos, tid.object_id, tid.css_class 
	FROM pwt.object_container_tabbed_items ti
	JOIN pwt.object_container_tabbed_item_details tid ON tid.object_container_tabbed_item_id = ti.id
 ;
   
   
GRANT ALL ON TABLE pwt.v_tabbed_items TO iusrpmt;
