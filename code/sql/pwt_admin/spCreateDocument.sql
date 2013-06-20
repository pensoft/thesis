CREATE OR REPLACE FUNCTION pwt.spCreateDocument(
	pPaperType int,
	pJournal_id int,
	pUid int
)
  RETURNS int AS
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
			SELECT INTO lTemplateId max(id) FROM pwt.templates WHERE papertype_id = pPaperType;
		END IF;
		IF (lTemplateId IS NOT NULL) THEN
			SELECT INTO lDocuementId id FROM pwt.spCreateDocumentByTemplate(lTemplateId, 'Untitled', pPaperType, pJournal_id, pUid);
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
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pwt.spCreateDocument(
	pPaperType int,
	pJournal_id  int,
	pUid int
) TO iusrpmt;
