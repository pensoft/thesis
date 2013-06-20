ALTER TABLE taxon_categories
  ADD COLUMN "nomenclaturalCode" smallint;
COMMENT ON COLUMN taxon_categories."nomenclaturalCode" IS '1 = ICZN, 2 = ICN';

UPDATE taxon_categories SET "nomenclaturalCode" = 1;
UPDATE taxon_categories SET "nomenclaturalCode" = 2 WHERE rootnode IN (6, 7) or id in (6, 7);


UPDATE pwt.fields SET default_control_type = 45 WHERE id = 41;
UPDATE pwt.object_fields SET  control_type = 45 WHERE field_id = 41;