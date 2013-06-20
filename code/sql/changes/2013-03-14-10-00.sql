-- References
UPDATE pwt.template_objects SET
	xml_file_name = 'book_reference.xml'
WHERE template_id = 2 AND object_id = 98;

UPDATE pwt.template_objects SET
	xml_file_name = 'book_chapter_reference.xml'
WHERE template_id = 2 AND object_id = 99;

UPDATE pwt.template_objects SET
	xml_file_name = 'journal_article_reference.xml'
WHERE template_id = 2 AND object_id = 102;

UPDATE pwt.template_objects SET
	xml_file_name = 'conference_paper_reference.xml'
WHERE template_id = 2 AND object_id = 103;

UPDATE pwt.template_objects SET
	xml_file_name = 'conference_proceedings_reference.xml'
WHERE template_id = 2 AND object_id = 105;

UPDATE pwt.template_objects SET
	xml_file_name = 'thesis_reference.xml'
WHERE template_id = 2 AND object_id = 106;

UPDATE pwt.template_objects SET
	xml_file_name = 'software_reference.xml'
WHERE template_id = 2 AND object_id = 107;

UPDATE pwt.template_objects SET
	xml_file_name = 'website_reference.xml'
WHERE template_id = 2 AND object_id = 108;

-- Taxon treatments
UPDATE pwt.template_objects SET
	xml_file_name = 'new_tt_species_protozoa_animalia.xml'
WHERE template_id = 2 AND object_id = 179;

UPDATE pwt.template_objects SET
	xml_file_name = 'new_tt_species_fungi_plantae_chromista.xml'
WHERE template_id = 2 AND object_id = 182;

UPDATE pwt.template_objects SET
	xml_file_name = 'new_tt_genus_protozoa_animalia.xml'
WHERE template_id = 2 AND object_id = 184;

UPDATE pwt.template_objects SET
	xml_file_name = 'new_tt_genus_fungi_plantae_chromista.xml'
WHERE template_id = 2 AND object_id = 192;

UPDATE pwt.template_objects SET
	xml_file_name = 'redescription_tt_species_fungi_plantae_chromista.xml'
WHERE template_id = 2 AND object_id = 196;

UPDATE pwt.template_objects SET
	xml_file_name = 'redescription_tt_species_protozoa_animalia.xml'
WHERE template_id = 2 AND object_id = 197;
