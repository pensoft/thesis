DROP TYPE IF EXISTS ret_spGetRoundReviewerMergedComments CASCADE;
CREATE TYPE ret_spGetRoundReviewerMergedComments AS (
	id int,
	document_id integer,
	author character varying(128),
	subject character varying,
	msg character varying,
	senderip inet,
	mdate timestamp without time zone,
	rootid integer,
	ord character varying,
	usr_id integer,
	flags integer,
	replies integer,
	views integer,
	lastmoddate timestamp without time zone,
	version_id bigint,
	start_object_instances_id bigint,
	end_object_instances_id bigint,
	start_object_field_id bigint,
	end_object_field_id bigint,
	start_offset integer,
	end_offset integer,
	original_id integer,
	is_resolved boolean,
	resolve_uid integer,
	resolve_date timestamp without time zone,
	original_mdate timestamp 
);

CREATE OR REPLACE FUNCTION spGetRoundReviewerMergedComments(
	pReviewRoundId bigint
)
  RETURNS SETOF ret_spGetRoundReviewerMergedComments AS
$BODY$
	DECLARE
		lRes ret_spGetRoundReviewerMergedComments;
		lSEVersionId bigint;
		lSERoleId int = 3;
		lDedicatedReviewerRoleId int = 5;
		lPublicReviewerRoleId int = 6;
		lCommunityReviewerRoleId int = 7;
		lRecord record;
		lRecord2 record;
		lCommentId bigint;
	BEGIN		
		
		SELECT INTO lSEVersionId v.id
		FROM pjs.document_versions v
		JOIN pjs.document_review_round_users ru ON ru.document_version_id = v.id
		JOIN pjs.document_users du ON du.id = ru.document_user_id AND du.role_id = lSERoleId
		JOIN pjs.document_review_rounds r ON r.id = ru.round_id AND decision_round_user_id = ru.id		
		WHERE ru.round_id = pReviewRoundId;
		
		IF lSEVersionId IS NULL THEN
			RETURN;
		END IF;
		
		CREATE TEMP TABLE msg_review(
			LIKE pjs.msg			
		);
		
		-- Copy all the SE comments
		INSERT INTO msg_review 
			SELECT *
		FROM pjs.msg 
		WHERE version_id = lSEVersionId;
		
		-- RAISE NOTICE 'SE Version %', lSEVersionId;
		
		-- Copy all the comments FROM the Reviewer versions(The reviewer versions should have its decision taken)
		FOR lRecord IN
			SELECT v.id as version_id
			FROM pjs.document_versions v
			JOIN pjs.document_review_round_users ru ON ru.document_version_id = v.id
			JOIN pjs.document_users du ON du.id = ru.document_user_id AND du.role_id IN (lDedicatedReviewerRoleId, lPublicReviewerRoleId, lCommunityReviewerRoleId)
			WHERE ru.round_id = pReviewRoundId AND decision_id IS NOT NULL
		LOOP
			--RAISE NOTICE 'Version %', lRecord.version_id;
			
			INSERT INTO msg_review 
				SELECT *
			FROM pjs.msg 
			WHERE version_id = lRecord.version_id;
		END LOOP;
		
		-- Remove all the comments with duplicate original ids
		-- The ones from the SE version should have priority (i.e. should not be removed)
		FOR lRecord IN
			SELECT original_id
			FROM msg_review
			GROUP BY original_id
			HAVING count(*) > 1
		LOOP
			-- SELECT the comment which will not be removed
			SELECT INTO lCommentId
				id
			FROM msg_review
			WHERE original_id = lRecord.original_id
			ORDER BY (CASE WHEN version_id = lSEVersionId THEN 1 ELSE 0 END ) DESC, mdate ASC, ord ASC
			LIMIT 1;
			
			--Update the replies of the comments which will be removed to be replies of the comment which will not be deleted			
			FOR lRecord2 IN
				SELECT *
				FROM msg_review
				WHERE original_id = lRecord.original_id AND id <> lCommentId
			LOOP 
				UPDATE msg_review SET
					rootid = lCommentId
				WHERE rootid = lRecord2.id;
			END LOOP;
			
			-- Remove the unnecessary comments
			DELETE 
			FROM msg_review
			WHERE original_id = lRecord.original_id AND id <> lCommentId;
		END LOOP;
		
		
		FOR lRes IN
			SELECT 	r.id, r.document_id, r.author, r.subject, r.msg, r.senderip, r.mdate,	r.rootid,
			r.ord, r.usr_id, r.flags, r.replies, r.views, r.lastmoddate, r.version_id, r.start_object_instances_id,
			r.end_object_instances_id, r.start_object_field_id, r.end_object_field_id, r.start_offset,
			r.end_offset, r.original_id, r.is_resolved, r.resolve_uid, m.mdate as original_mdate
			FROM msg_review r
			JOIN pjs.msg m ON m.id = r.original_id
		LOOP
			RETURN NEXT lRes;
		END LOOP;
			
		DROP TABLE msg_review;
		
		RETURN;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetRoundReviewerMergedComments(
	pReviewRoundId bigint
) TO iusrpmt;
