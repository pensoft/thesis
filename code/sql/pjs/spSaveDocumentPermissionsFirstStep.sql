DROP TYPE ret_spSaveDocumentPermissionsFirstStep CASCADE;
CREATE TYPE ret_spSaveDocumentPermissionsFirstStep AS (
	preparation_checklist int,
	terms_agreement int
);

CREATE OR REPLACE FUNCTION spSaveDocumentPermissionsFirstStep(
	pOper int,
	pDocumentId bigint,
	pUid bigint,
	pPreparationChecklist int,
	pTermsAgreement int
)
  RETURNS ret_spSaveDocumentPermissionsFirstStep AS
$BODY$
	DECLARE
		lRes ret_spSaveDocumentPermissionsFirstStep;			
	BEGIN
	
		IF pOper = 1 THEN
			
			UPDATE pjs.documents d SET 
				creation_step = 2,
				preparation_checklist = pPreparationChecklist,
				terms_agreement = pTermsAgreement
			WHERE d.id = pDocumentId AND d.submitting_author_id = pUid AND d.state_id = 1;
		END IF;
		
		SELECT INTO lRes preparation_checklist, terms_agreement FROM pjs.documents WHERE id = pDocumentId AND submitting_author_id = pUid AND state_id = 1;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveDocumentPermissionsFirstStep(
	pOper int,
	pDocumentId bigint,
	pUid bigint,
	pPreparationChecklist int,
	pTermsAgreement int
) TO iusrpmt;
