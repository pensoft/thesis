-- DROP FUNCTION spSaveJournalSection(integer, integer, character varying, character varying, character varying, integer[], integer);

CREATE OR REPLACE FUNCTION spSaveJournalSection(
	pGuid integer,
	pJournalId integer, 
	pTitle character varying, 
	pAbbreviation character varying,
	pPolicy character varying,
	pReviewType integer[],
	pPaperType integer
)
  RETURNS integer AS
$BODY$
DECLARE
	lRet integer;
	lPWTPaperTypeId integer;
BEGIN
	lRet := 0;
	
	IF pPaperType = 0 THEN
		lPWTPaperTypeId := NULL;
	ELSE
		lPWTPaperTypeId := pPaperType;
	END IF;
	
	IF pGuid IS NOT NULL THEN -- Update Existing Record
		UPDATE pjs.journal_sections 
			SET title = pTitle, 
				abr = pAbbreviation, 
				policy = pPolicy,
				pwt_paper_type_id = lPWTPaperTypeId,
				review_type_id = pReviewType
		WHERE id = pGuid AND journal_id = pJournalId;	
		lRet := 1;
	ELSE --Insert New Record
		INSERT INTO pjs.journal_sections (journal_id, title, abr, policy, pwt_paper_type_id, review_type_id)
								VALUES(pJournalId, pTitle, pAbbreviation, pPolicy, lPWTPaperTypeId, pReviewType);
		lRet := 1;
	END IF;
	
	RETURN lRet;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spSaveJournalSection(integer, integer, character varying, character varying, character varying, integer[], integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spSaveJournalSection(integer, integer, character varying, character varying, character varying, integer[], integer) TO postgres;
GRANT EXECUTE ON FUNCTION spSaveJournalSection(integer, integer, character varying, character varying, character varying, integer[], integer) TO iusrpmt;
GRANT EXECUTE ON FUNCTION spSaveJournalSection(integer, integer, character varying, character varying, character varying, integer[], integer) TO pensoft;

