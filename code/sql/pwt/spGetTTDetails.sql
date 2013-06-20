DROP TYPE IF EXISTS ret_spGetTTDetails CASCADE;

CREATE TYPE ret_spGetTTDetails AS (
	status_type int,
	habitat_type int
);

CREATE OR REPLACE FUNCTION spGetTTDetails(
	pInstanceId bigint
)
  RETURNS ret_spGetTTDetails AS
$BODY$
	DECLARE
		lRes ret_spGetTTDetails;		
		lRecord record;
		lTaxonTreatmentObjectId int;
		lTaxonTreatmentStatusTypeFieldId int;
		lTaxonTreatmentHabitatTypeFieldId int;

		lTaxonTreatmentInstanceId int;
		lTTStatusTypeValue int;
		lTTHabitatTypeValue int;
		
	BEGIN		
		lTaxonTreatmentObjectId = 41;
		lTaxonTreatmentStatusTypeFieldId = 44;
		lTaxonTreatmentHabitatTypeFieldId = 45;
		
		-- Трябва да разберем точно кой тип секции да създадем
		-- За целта трябва да вземем полетo за rank на синонима
		SELECT INTO lTaxonTreatmentInstanceId p.id 
			FROM pwt.document_object_instances i
			JOIN pwt.document_object_instances p ON
				p.document_id = i.document_id AND char_length(p.pos) < char_length(i.pos) AND 
				p.pos = substring(i.pos, 1, char_length(p.pos))				
			WHERE i.id = pInstanceId AND p.object_id = lTaxonTreatmentObjectId;
		
		SELECT INTO lTTStatusTypeValue value_int
		FROM pwt.instance_field_values 
		WHERE instance_id = lTaxonTreatmentInstanceId AND field_id = lTaxonTreatmentStatusTypeFieldId;
		
		SELECT INTO lTTHabitatTypeValue value_int
		FROM pwt.instance_field_values 
		WHERE instance_id = lTaxonTreatmentInstanceId AND field_id = lTaxonTreatmentHabitatTypeFieldId;

		lRes.status_type = lTTStatusTypeValue;
		lRes.habitat_type = lTTHabitatTypeValue;
		--RAISE NOTICE 'lnstanceId: %, lTTHabitatTypeValue: %, lTTStatusTypeValue: %', lTaxonTreatmentInstanceId, lTTHabitatTypeValue, lTTStatusTypeValue;
		
		RETURN lRes;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetTTDetails(
	pInstanceId bigint
) TO iusrpmt;

--select * from spGetTTDetails(89369);