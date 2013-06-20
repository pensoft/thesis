DROP TYPE ret_spGenerateCitationPreview CASCADE;

CREATE TYPE ret_spGenerateCitationPreview AS (
	citation_id bigint,
	preview varchar
);

CREATE OR REPLACE FUNCTION spGenerateCitationPreview(
	pCitationId bigint
)
  RETURNS ret_spGenerateCitationPreview AS
$BODY$
	DECLARE
		lRes ret_spGenerateCitationPreview;		
		lRecord record;
	BEGIN		
		SELECT INTO lRecord * 
		FROM pwt.citations 
		WHERE id = pCitationId;
		
		IF lRecord.id IS NULL THEN
			RETURN lRes;
		END IF;
		
		lRes.citation_id = lRecord.id;
		IF lRecord.is_dirty = false THEN
			lRes.preview = lRecord.preview;
			RETURN lRes;
		END IF;
		
		lRes.citation_id = lRecord.id;
		
		IF lRecord.citation_type = 1 THEN
			SELECT INTO lRes.preview preview FROM spGenerateFiguresCitationPreview(pCitationId);
		ELSEIF lRecord.citation_type = 2 THEN
			SELECT INTO lRes.preview preview FROM spGenerateTableCitationPreview(pCitationId);
		ELSEIF lRecord.citation_type = 3 THEN
			SELECT INTO lRes.preview preview FROM spGenerateReferenceCitationPreview(pCitationId);
		ELSEIF lRecord.citation_type = 4 THEN
			SELECT INTO lRes.preview preview FROM spGenerateSupFileCitationPreview(pCitationId);
		END IF;		
		
		UPDATE pwt.citations SET
			is_dirty = false,
			preview = lRes.preview
		WHERE id = pCitationId;
		RETURN lRes;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGenerateCitationPreview(
	pCitationId bigint
) TO iusrpmt;
