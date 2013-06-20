select * from pwt.object_containers where object_id = 38;
-- update ord of containers
UPDATE pwt.object_containers set ord = ord + 1 where object_id = 38 and ord >= 2;
-- pwt.object_containers
INSERT INTO pwt.object_containers(object_id, mode_id, ord, type, name) VALUES(38, 1, 2, 1, 'Taxon treatment materials download as CSV holder');

--INSERT INTO pwt.object_container_html_items(name, content) VALUES('Taxon treatment materials download as CSV holder', '<div class="P-Taxon-Materials-DownLoadHolder"><a href="javascript:void(0);" onclick="DownloadMaterialsAsCSV({instance_id})">Download materials</a></div><script type="text/javascript">ShowDownloadMaterialsLink({instance_id})</script>');
-- pwt.object_container_details
INSERT INTO pwt.object_container_details(container_id, item_id, ord, item_type) VALUES(779, 31, 1, 3); 
-- select the new container
select * from pwt.object_container_details where container_id = 779;


<div class="P-Taxon-Materials-DownLoadHolder"><a href="javascript:void(0);" onclick="DownloadMaterialsAsCSV({instance_id})">Download materials</a></div><script type="text/javascript">ShowDownloadMaterialsLink({instance_id})</script>