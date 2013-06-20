DROP TYPE IF EXISTS ret_spSaveDocumentPermissionsSecondStep CASCADE;
CREATE TYPE ret_spSaveDocumentPermissionsSecondStep AS (
	result int
);

CREATE OR REPLACE FUNCTION spSaveDocumentPermissionsSecondStep(
	pDocumentId bigint,
	pAgreeToCoverAllTaxes int,
	pWant15Discount int,
	p15DiscountReasons int[],
	pWant10Discount int,
	p10DiscountReasons int[],
	pWantWaiverDiscount int,
	pWaiverDiscountReasons int[],
	pUseSpecialConditions int,
	pPersonToCharge int,
	pPersonToChargeName varchar,
	pComments varchar,
	pUid bigint
)
  RETURNS ret_spSaveDocumentPermissionsSecondStep AS
$BODY$
	DECLARE
		lRes ret_spSaveDocumentPermissionsSecondStep;			
		lSuccessfullySubmittedDocState int;
	BEGIN
		lSuccessfullySubmittedDocState = 2;
		UPDATE pjs.documents d SET 
			state_id = lSuccessfullySubmittedDocState,
			agree_to_cover_all_taxes = pAgreeToCoverAllTaxes,
			want_15_discount = pWant15Discount,
			fifteen_discount_reasons = p15DiscountReasons,
			want_10_discount = pWant10Discount,
			ten_discount_reasons = p10DiscountReasons,
			want_waiver_discount = pWantWaiverDiscount,
			waiver_discount_reasons = pWaiverDiscountReasons,
			use_special_conditions_discount = pUseSpecialConditions,
			person_to_charge = pPersonToCharge,
			person_to_charge_name = pPersonToChargeName,
			creation_comments = pComments,
			creation_step = 3,
			submitted_date = now()
		WHERE d.id = pDocumentId AND d.submitting_author_id = pUid AND d.state_id = 1 ;
		lRes.result = 1;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveDocumentPermissionsSecondStep(
	pDocumentId bigint,
	pAgreeToCoverAllTaxes int,
	pWant15Discount int,
	p15DiscountReasons int[],
	pWant10Discount int,
	p10DiscountReasons int[],
	pWantWaiverDiscount int,
	pWaiverDiscountReasons int[],
	pUseSpecialConditions int,
	pPersonToCharge int,
	pPersonToChargeName varchar,
	pComments varchar,
	pUid bigint
) TO iusrpmt;
