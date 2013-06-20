-- Function: pwt.spcreatedocument(integer, integer, integer)

-- DROP FUNCTION pwt.spcreatedocument(integer, integer, integer);

CREATE OR REPLACE FUNCTION pwt.spcreatedocument(ppapertype integer, pjournal_id integer, puid integer)
  RETURNS integer AS
$BODY$
	DECLARE
		lTemplateId int;
		lDocuementId int;
		
		lTaxonTreatmentPaperType int;
		lTaxonTreatmentTemplateId int;
		
	BEGIN	
		lTaxonTreatmentPaperType = 4;
		lTaxonTreatmentTemplateId = 2;
		
		lTemplateId  = NULL;
		IF pPaperType = lTaxonTreatmentPaperType THEN
			lTemplateId = lTaxonTreatmentTemplateId;
		ELSE 
			SELECT INTO lTemplateId max(id) FROM pwt.templates WHERE pPaperType = ANY (papertype_id);
		END IF;
		IF (lTemplateId IS NOT NULL) THEN
			SELECT INTO lDocuementId id FROM pwt.spCreateDocumentByTemplate(lTemplateId, 'Untitled', pPaperType, pJournal_id, pUid);
			
			UPDATE pwt.documents SET xml_is_dirty = TRUE WHERE id = lDocuementId;
			UPDATE pwt.document_object_instances SET is_modified = TRUE WHERE document_id = lDocuementId;
			
			IF (lDocuementId IS NOT NULL AND lDocuementId > 0) THEN
				RETURN lDocuementId;
			ELSE 
				RETURN -1;
			END IF;
			
		ELSE
			RETURN 0;
		END IF;
	END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION pwt.spcreatedocument(integer, integer, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spcreatedocument(integer, integer, integer) TO public;
GRANT EXECUTE ON FUNCTION pwt.spcreatedocument(integer, integer, integer) TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spcreatedocument(integer, integer, integer) TO iusrpmt;
