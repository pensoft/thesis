ALTER TABLE pwt.template_objects ADD COLUMN view_xpath_sel character varying;
ALTER TABLE pwt.template_objects ADD COLUMN view_xsl_templ_mode character varying;
ALTER TABLE pwt.document_template_objects ADD COLUMN view_xpath_sel character varying;
ALTER TABLE pwt.document_template_objects ADD COLUMN view_xsl_templ_mode character varying;
ALTER TABLE pwt.templates ADD COLUMN xsl_dir_name character varying;