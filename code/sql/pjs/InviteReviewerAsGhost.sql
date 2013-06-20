--
-- Name: InviteReviewerAsGhost(integer, integer, integer); Type: FUNCTION; Schema: pjs; Owner: pensoft
--

CREATE FUNCTION "InviteReviewerAsGhost"(puid integer, pdocumentid integer, pcurrentroundid integer) RETURNS integer
    LANGUAGE plpgsql SECURITY DEFINER
    AS $$
DECLARE
	cUninvitedBySE CONSTANT int := 7;
	cAddedBySE CONSTANT int := 2;
BEGIN
	IF NOT EXISTS 
		( SELECT uid 
		FROM pjs.document_user_invitations 
		WHERE uid = pUid
		  AND document_id = pDocumentID 
		  AND round_id = pCurrentRoundId)
	THEN
		INSERT INTO pjs.document_user_invitations
				( uid, 	 document_id,  round_id, 	 state_id, 		 added_by_type_id) 
		VALUES 	(pUid, pDocumentID, pCurrentRoundId, cUninvitedBySE, cAddedBySE);
		RETURN 1;
	END IF;
	RETURN 0;
END
$$;


ALTER FUNCTION pjs."InviteReviewerAsGhost"(puid integer, pdocumentid integer, pcurrentroundid integer) OWNER TO pensoft;
