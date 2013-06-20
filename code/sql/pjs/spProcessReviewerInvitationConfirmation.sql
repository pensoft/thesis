DROP TYPE IF EXISTS ret_spProcessReviewerInvitationConfirmation CASCADE;
CREATE TYPE ret_spProcessReviewerInvitationConfirmation AS (
	result int
);

CREATE OR REPLACE FUNCTION spProcessReviewerInvitationConfirmation(
	pInvitationId bigint
)
  RETURNS ret_spProcessReviewerInvitationConfirmation AS
$BODY$
	DECLARE
		lRes ret_spProcessReviewerInvitationConfirmation;
		lRecord record;
		lAuthorVersionType int;
		lReviewerVersionType int;
		lVersionId bigint;
		lReviewerDocumentUserId bigint;
		lAuthorRoundType int;
		lAuthorRoleId int;
		lAuthorVersion bigint;
		lAuthorUid bigint;
		lAuthorSubmittedVersionType int;
		lReviewAcceptedEventType int := 6;
		lReviewRoundUsrId bigint;
	BEGIN		
		lAuthorVersionType = 1;
		lReviewerVersionType = 2;
		lAuthorRoundType = 5;
		lAuthorRoleId = 11;
		lAuthorSubmittedVersionType = 1;
		
		SELECT INTO lRecord * 
		FROM pjs.document_user_invitations
		WHERE id = pInvitationId;
		
		IF lRecord.id IS NULL THEN
			RETURN lRes;
		END IF;
		
		SELECT INTO lReviewerDocumentUserId id
		FROM pjs.document_users
		WHERE document_id = lRecord.document_id AND role_id = lRecord.role_id AND uid = lRecord.uid;
		
		-- Insert the reviewer in document_users if (s)he is not already there
		IF lReviewerDocumentUserId IS NULL THEN
			INSERT INTO pjs.document_users(document_id, uid, role_id) VALUES (lRecord.document_id, lRecord.uid, lRecord.role_id);
			lReviewerDocumentUserId = currval('pjs.document_users_id_seq');
		END IF;
		
		/*SELECT INTO lAuthorVersion document_version_id 
		FROM pjs.document_review_rounds drr
		JOIN pjs.document_review_round_users drru ON drru.id = drr.decision_round_user_id
		WHERE drr.round_type_id = lAuthorRoundType AND document_id = lRecord.document_id
		ORDER BY drr.id DESC
		LIMIT 1;
		*/
		--IF lAuthorVersion IS NULL THEN
			/*SELECT INTO lAuthorUid submitting_author_id
			FROM pjs.documents
			WHERE id = lRecord.document_id;*/
			/*
			SELECT INTO lAuthorUid uid 
			FROM pjs.document_users
			WHERE document_id = lRecord.document_id AND role_id = lAuthorRoleId;
			*/
			-- get last author version for this document
			SELECT INTO lAuthorVersion id 
			FROM pjs.document_versions 
			WHERE version_type_id = lAuthorSubmittedVersionType 
				AND document_id = lRecord.document_id 
			ORDER BY id DESC 
			LIMIT 1;
		--END IF;
		
		IF NOT EXISTS( SELECT id FROM pjs.document_review_round_users WHERE document_user_id = lReviewerDocumentUserId AND round_id = lRecord.round_id) THEN
			SELECT INTO lVersionId id FROM spCreateDocumentVersion(lRecord.document_id, lRecord.uid, lReviewerVersionType, lAuthorVersion);
			INSERT INTO pjs.document_review_round_users(round_id, document_user_id, document_version_id, due_date) VALUES (lRecord.round_id, lReviewerDocumentUserId, lVersionId, now() + '1 week'::interval);
		END IF;
		
		SELECT INTO lReviewRoundUsrId id FROM pjs.document_review_round_users WHERE document_user_id = lReviewerDocumentUserId AND round_id = lRecord.round_id;
		PERFORM pjs.spUpdateDueDates(1, lRecord.document_id, lReviewAcceptedEventType, null, lReviewRoundUsrId);
		
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spProcessReviewerInvitationConfirmation(
	pInvitationId bigint
) TO iusrpmt;
