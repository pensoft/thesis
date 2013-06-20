DROP TYPE IF EXISTS ret_spGetDocumentInfo CASCADE;
CREATE TYPE ret_spGetDocumentInfo AS (
	document_id bigint,
	createuid bigint,
	state_id int,
	document_source_id int,
	creation_step int,
	name varchar,
	abstract varchar,
	keywords varchar,
	journal_id int,
	editor_notes varchar,
	le_notes varchar, 
	document_review_type_id int,
	document_review_due_date timestamp,
	version_num int,
	review_type_name varchar,
	document_type_name varchar,
	author_name varchar,
	round_number int,
	round_name varchar,
	round_type int,
	notes_to_editor varchar,
	user_version_id int,
	submitted_date timestamp,
	can_proceed boolean,
	enough_reviewers boolean,
	reviewers_assignment_duedate timestamp,
	community_public_due_date timestamp,
	current_round_id bigint,
	review_lock boolean,
	check_invited_users int,
	merge_flag int,
	author_version_id bigint,
	copy_editor_version_id bigint,
	layout_version_id bigint,
	reject_round_decision_notes varchar,
	pwt_id bigint,
	se_first_name varchar,
	se_last_name varchar,
	se_uname varchar
);

CREATE OR REPLACE FUNCTION spGetDocumentInfo(
	pDocumentId bigint,
	pRoleType int
)
  RETURNS ret_spGetDocumentInfo AS
$BODY$
	DECLARE
		lRes ret_spGetDocumentInfo;
		lAuthorVersionType int;
		lEditorRoundType int;
		lAuthorRoundType int;
		lReviewRoundType int;
		lARoleType int;
		lERoleType int;
		lSERoleType int;
		lRRoleType int;
		lCERoleType int;
		lVersionType int;
		lInvitedReviewerStateId int;
		lPanelRRoleType CONSTANT int := 7;
		lCurrentAuthorVersionID bigint;
		
		-- waiting author document states
		cWaitingAuthorVersionAfterReviewRound CONSTANT int := 9;
		cWaitingAuthorVersionAfterLayoutRound CONSTANT int := 10;
		cWaitingAuthorToProceedToLayout CONSTANT int := 12;
		cWaitingAuthorToProceedToCopyEditing CONSTANT int := 14;
		cWaitingAuthorToProceedToLayoutAfterCopyEditing CONSTANT int := 17;
		
		-- some other document states
		cInLayoutState CONSTANT int := 4;
		cInCopyEditingState CONSTANT int := 8;
		cInApprovedForPublicationState CONSTANT int := 11;
		cReadyForLayoutState CONSTANT int := 13;
		cReadyForCopyEditingState CONSTANT int := 15;
		cCEDocumentVersionType CONSTANT int := 7;
		cLEDocumentVersionType CONSTANT int := 5;
		
		cRejectDecisionId CONSTANT int := 2;
		cRejectButDecisionId CONSTANT int := 5;
	BEGIN
		lAuthorVersionType = 1;
		lAuthorRoundType = 5;
		lEditorRoundType = 4;
		lReviewRoundType = 1;
		
		lInvitedReviewerStateId = 1;
		
		lARoleType = 11;
		lERoleType = 2;
		lSERoleType = 3;
		lRRoleType = 5;
		lCERoleType = 9;
		
		-- document info
		SELECT INTO lRes 
			d.id, d.submitting_author_id, d.state_id, d.document_source_id, d.creation_step, 
			d.name, d.abstract, d.keywords, d.journal_id, d.editor_notes, d.layout_notes, d.document_review_type_id, null,
			dv.version_num, dvt.name, js.title, null, null, null, null, d.notes_to_editor, null, d.submitted_date, null, null, null, d.community_public_due_date, d.current_round_id, 
			null, null, null, dv.id as author_version_id, null, null, null, pd.pwt_id, null, null, null
		FROM pjs.documents d
		JOIN pjs.document_review_rounds dr ON dr.id = d.current_round_id
		JOIN pjs.document_versions dv ON dv.id = dr.create_from_version_id
		JOIN pjs.document_review_types dvt ON dvt.id = d.document_review_type_id
		JOIN pjs.document_types dt ON dt.id = d.document_type_id
		JOIN pjs.journal_sections js ON js.id = d.journal_section_id
		JOIN pjs.pwt_documents pd ON pd.document_id = d.id
		WHERE d.id = pDocumentId;
		
		-- selecting SE data
		SELECT INTO 
			lRes.se_first_name, 
			lRes.se_last_name, 
			lRes.se_uname 
			
			u.first_name,
			u.last_name,
			u.uname
		FROM usr u 
		JOIN pjs.document_users du ON du.uid = u.id
		WHERE du.document_id = pDocumentId AND role_id = lSERoleType
		LIMIT 1;
		
		-- checking if we must show the author current version
		IF(
			(
				(
					lRes.state_id = cWaitingAuthorVersionAfterReviewRound OR 
					lRes.state_id = cWaitingAuthorVersionAfterLayoutRound OR 
					lRes.state_id = cWaitingAuthorToProceedToLayout OR 
					lRes.state_id = cWaitingAuthorToProceedToCopyEditing OR 
					lRes.state_id = cWaitingAuthorToProceedToLayoutAfterCopyEditing
				) AND 
				pRoleType <> lARoleType
			) OR
			(
				pRoleType = lSERoleType AND 
				(
					lRes.state_id = cInLayoutState OR 
					lRes.state_id = cInCopyEditingState OR 
					lRes.state_id = cInApprovedForPublicationState OR 
					lRes.state_id = cReadyForLayoutState OR 
					lRes.state_id = cReadyForCopyEditingState
				)
			)
		) THEN
			lRes.version_num = NULL;
			lRes.author_version_id = NULL;
		END IF;
		
		-- tuk vzemame predi6nata avtorova versiq ako teku6tata e v process na obrabotka ot avtora
		/*IF(
				(
					lRes.state_id = cWaitingAuthorVersionAfterReviewRound OR 
					lRes.state_id = cWaitingAuthorVersionAfterLayoutRound OR 
					lRes.state_id = cWaitingAuthorToProceedToLayout OR 
					lRes.state_id = cWaitingAuthorToProceedToCopyEditing OR 
					lRes.state_id = cWaitingAuthorToProceedToLayoutAfterCopyEditing
				) AND 
				pRoleType = lARoleType
			) THEN
			SELECT INTO lCurrentAuthorVersionID create_from_version_id
			FROM pjs.document_review_rounds 
			WHERE id = lRes.current_round_id;
			
			SELECT INTO lRes.version_num, lRes.author_version_id version_num, id
			FROM pjs.document_versions
			WHERE version_type_id = lAuthorVersionType AND document_id = pDocumentId AND id <> lCurrentAuthorVersionID
			ORDER BY id DESC
			LIMIT 1;
		END IF;*/
		
		-- current round info
		SELECT INTO 
			lRes.round_name,
			lRes.round_number,
			lRes.round_type,
			lRes.can_proceed,
			lRes.enough_reviewers,
			lRes.review_lock,
			lRes.reviewers_assignment_duedate
			
			drt.name as round_name, 
			dr.round_number,
			drt.id as round_type,
			dr.can_proceed,
			dr.enough_reviewers,
			dr.review_lock,
			dr.reviewers_assignment_duedate
			
		FROM pjs.document_review_rounds dr
		JOIN pjs.document_review_round_types drt ON drt.id = dr.round_type_id
		WHERE dr.document_id = pDocumentId AND round_type_id NOT IN (lEditorRoundType, lAuthorRoundType)
		ORDER BY dr.id DESC
		LIMIT 1;
		
		-- document authors
		SELECT INTO lRes.author_name aggr_concat_coma(u.first_name || ' ' || u.last_name)
		FROM pjs.document_users du
		JOIN usr u ON u.id = du.uid
		WHERE du.document_id = pDocumentId AND du.role_id = lARoleType
		GROUP BY du.document_id;
		
		IF pRoleType = lARoleType THEN
			lVersionType = 1;
		ELSEIF (pRoleType = lERoleType OR pRoleType = lSERoleType) THEN
			lVersionType = 3;
		ELSEIF (pRoleType = lRRoleType OR pRoleType = lPanelRRoleType) THEN
			lVersionType = 2;
		ELSEIF pRoleType = lCERoleType THEN
			lVersionType = 7;
		END IF;
		
		SELECT INTO lRes.user_version_id id FROM pjs.document_versions WHERE document_id = pDocumentId AND version_type_id = lVersionType ORDER BY id DESC LIMIT 1;
		
		SELECT INTO lRes.copy_editor_version_id id FROM pjs.document_versions WHERE document_id = pDocumentId AND version_type_id = cCEDocumentVersionType ORDER BY id DESC LIMIT 1;
		SELECT INTO lRes.layout_version_id id FROM pjs.document_versions WHERE document_id = pDocumentId AND version_type_id = cLEDocumentVersionType ORDER BY id DESC LIMIT 1;
		
		SELECT INTO lRes.reject_round_decision_notes decision_notes FROM pjs.document_review_rounds WHERE document_id = pDocumentId AND decision_id IN (cRejectDecisionId, cRejectButDecisionId) LIMIT 1;
		
		-- if it's first editor round (on document submission) then show Review round 1
		IF lRes.round_type IS NULL THEN
			SELECT INTO lRes.round_name, lRes.round_number, lRes.round_type 'Review', 1, lReviewRoundType;
		END IF;
		
		-- check for invited users
		SELECT INTO lRes.check_invited_users
			count(dui.id)
		FROM pjs.document_user_invitations dui
		JOIN pjs.document_review_rounds drr ON drr.id = dui.round_id
		WHERE dui.role_id = lRRoleType AND dui.round_id = lRes.current_round_id AND dui.state_id IN (1,2,5) AND dui.document_id = pDocumentId;
		
		-- must merge flag
		SELECT INTO lRes.merge_flag	
					count(* )
		FROM pjs.document_review_round_users dru
		JOIN pjs.document_users du ON du.id = dru.document_user_id
		JOIN pjs.document_review_rounds drr ON drr.id = dru.round_id AND drr.can_proceed = TRUE
		WHERE dru.state_id = 1 AND dru.decision_id IS NOT NULL AND du.role_id IN (lRRoleType, lPanelRRoleType) AND dru.round_id = lRes.current_round_id;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetDocumentInfo(
	pDocumentId bigint,
	pRoleType int
) TO iusrpmt;
