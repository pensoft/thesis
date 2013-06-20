GRANT ALL ON pjs.pwt_document_version_changes_id_seq TO iusrpmt;
ALTER TABLE pjs.pwt_document_versions ADD COLUMN change_user_ids int[];
COMMENT ON COLUMN pjs.pwt_document_versions.change_user_ids IS 'The ids of the users who have changes in the current version';