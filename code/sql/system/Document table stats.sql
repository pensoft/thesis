select count(*), 'dto' as tbl from pwt.document_template_objects union
select count(*), 'doi' as tbl from pwt.document_object_instances union
select count(*), 'ifv' as tbl from pwt.instance_field_values union
select count(*), 'doc' as tbl from pwt.documents
order by tbl 

