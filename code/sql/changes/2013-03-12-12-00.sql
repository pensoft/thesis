ALTER TABLE pwt.document_object_instances ADD COLUMN is_modified boolean DEFAULT false;
ALTER TABLE pwt.documents ADD COLUMN xml_is_dirty boolean DEFAULT false;