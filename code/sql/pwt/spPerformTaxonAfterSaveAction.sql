DROP TYPE ret_spPerformTaxonAfterSaveAction CASCADE;
CREATE TYPE ret_spPerformTaxonAfterSaveAction AS (
	result int
);

CREATE OR REPLACE FUNCTION spPerformTaxonAfterSaveAction(
	pTreatmentInstanceId bigint,
	pUid int
)
  RETURNS ret_spPerformTaxonAfterSaveAction AS
$BODY$
DECLARE
	lRes ret_spPerformTaxonAfterSaveAction;
	lTTMainSubobjCustomCreationRuleId int;
BEGIN	
	lTTMainSubobjCustomCreationRuleId = 10;
	IF NOT EXISTS (
		SELECT * 
		FROM pwt.document_object_instances 
		WHERE parent_id = pTreatmentInstanceId AND object_id IN (
			SELECT DISTINCT object_id 
			FROM pwt.custom_object_creation_combinations 
			WHERE custom_object_creation_id = lTTMainSubobjCustomCreationRuleId
		)
		LIMIT 1
	) THEN -- Трябва да създадем дървото надолу
		PERFORM spGenerateTaxonTreatmentSubTree(pTreatmentInstanceId, pUid);
	ELSE -- Трябва да ъпдейтнем дървото надолу - засега трябва да ъпдейтнем само материалите и обекта за името на таксона
		PERFORM spFixTaxonTreatmentSubobjects(pTreatmentInstanceId, pUid);
	END IF;

	lRes.result = 1;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spPerformTaxonAfterSaveAction(
	pTreatmentInstanceId bigint,
	pUid int
) TO iusrpmt;
