DROP TYPE IF EXISTS ret_spHandleTimeouts CASCADE;
CREATE TYPE ret_spHandleTimeouts AS (event_id int);

CREATE OR REPLACE FUNCTION spHandleTimeouts() RETURNS SETOF ret_spHandleTimeouts AS
$BODY$
	DECLARE
		lRes ret_spHandleTimeouts;	
		lRecord record;
		cClosedPeerReview CONSTANT int := 2;
		cCommunityPeerReview CONSTANT int := 3;
		cPublicPeerReview CONSTANT int := 4;
		cDocumentReviewStateID CONSTANT int := 3;
		cReviewerInvitationTimeoutEventTypeID CONSTANT int := 96;
		cReviewerTimedOutStateID CONSTANT int := 4;
		cReviewerRoleID CONSTANT int := 5;
		lOffset int;
		cInactiveReviewStateID CONSTANT int := 2;
	BEGIN		
	
		SELECT INTO lOffset "offset" FROM pjs.event_offset WHERE event_type_id = cReviewerInvitationTimeoutEventTypeID;
	
		FOR lRecord IN 
			SELECT d.id as document_id, d.journal_id, dui.uid as uid, dui.id as invitation_id, dui.round_id
			FROM pjs.documents d 
			JOIN pjs.document_review_rounds drr ON drr.id = d.current_round_id
			JOIN pjs.document_user_invitations dui ON dui.round_id = drr.id AND dui.document_id = d.id AND role_id = cReviewerRoleID
			WHERE d.state_id = cDocumentReviewStateID
				AND d.document_review_type_id IN (cClosedPeerReview,cCommunityPeerReview,cPublicPeerReview)
				AND dui.state_id = 1
				AND (dui.due_date + lOffset*INTERVAL '1 day')::date <= now()::date
		LOOP
			
			UPDATE pjs.document_review_round_users drru SET state_id = cInactiveReviewStateID 
			FROM (SELECT id FROM pjs.document_users WHERE document_id = lRecord.document_id AND uid = lRecord.uid AND role_id = cReviewerRoleID) as du
			WHERE du.id = drru.document_user_id AND drru.round_id = lRecord.round_id;
			
			UPDATE pjs.document_user_invitations SET state_id = cReviewerTimedOutStateID WHERE id = lRecord.invitation_id;
			
			SELECT INTO lRes.event_id event_id FROM spCreateEvent(cReviewerInvitationTimeoutEventTypeID, lRecord.document_id, lRecord.uid, lRecord.journal_id, lRecord.uid, cReviewerRoleID);
			
			RETURN NEXT lRes;
		END LOOP;
		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spHandleTimeouts() TO iusrpmt;
