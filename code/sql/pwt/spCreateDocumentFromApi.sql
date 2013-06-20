DROP FUNCTION pwt.spCreateDocumentFromApi(
	pTemplateId int, 
	pDocumentName character varying, 
	pPapertType integer, 
	pJournalId integer, 
	pSubmittingAuthorUid int,
	pUid integer
);

CREATE OR REPLACE FUNCTION pwt.spCreateDocumentFromApi(
	pTemplateId int, 
	pDocumentName character varying, 
	pPapertType integer, 
	pJournalId integer, 
	pSubmittingAuthorUid int,
	pUid integer
)
  RETURNS ret_spCreateDocument AS
$BODY$
		DECLARE
			lRes ret_spCreateDocument;					
		BEGIN	
			SELECT INTO lRes * FROM spCreateDocumentByTemplate(pTemplateId, pDocumentName, pPapertType, pJournalId, pSubmittingAuthorUid);			
			
			UPDATE pwt.documents SET
				imported_by_api = true, 
				import_api_uid = pUid,
				createuid = pSubmittingAuthorUid
			WHERE id = lRes.id;
			RETURN lRes;
		END
	$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
