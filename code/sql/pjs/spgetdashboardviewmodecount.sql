--
-- Name: spgetdashboardviewmodecount(integer, integer[], integer); Type: FUNCTION; Schema: pjs; Owner: pensoft
--

-- Type: pjs.ret_spgetdashboardviewmodecount

-- DROP TYPE pjs.ret_spgetdashboardviewmodecount;

/*
CREATE TYPE pjs.ret_spgetdashboardviewmodecount AS
   (role_id integer,
    viewmode_id integer,
    count integer);
ALTER TYPE pjs.ret_spgetdashboardviewmodecount OWNER TO postgres;
*/

CREATE FUNCTION spgetdashboardviewmodecount(pjournalid integer, pallowedrolesarr integer[], puid integer) RETURNS SETOF pjs.ret_spgetdashboardviewmodecount
    LANGUAGE plpgsql SECURITY DEFINER
    AS $$
	DECLARE
		Res pjs.ret_spGetDashboardViewmodeCount;
		Record record;
			
		SubjEdRole int := 3;
		EditorRole int := 2;		
		AuthorRole int := 11;
		ReviewerRole int := 5;
		LayoutRole int := 8;
		CopyEditRole int := 9;
		
		NewRequestStateId int := 1;
		
		lIncompleteDocumentStateId int := 1;
		UnassignedDocumentState int := 2;
		lInReviewDocumentStatesArr int[] = ARRAY[3, 9];
		lCopyEditingStatesArr int[] = ARRAY[14, 15, 8];
		lInCopyEditingStateId int := 8;
		lLayoutEditingStatesArr int[] = ARRAY[12, 13, 4, 10, 17];

		lReadyDocumentStateId int := 11;
		InProductionStates int[] :=  lCopyEditingStatesArr || lLayoutEditingStatesArr ||  ARRAY[lReadyDocumentStateId];
		ActiveStates int[] := ARRAY[UnassignedDocumentState] || lInReviewDocumentStatesArr || InProductionStates;

		RejectedStates int[] := ARRAY[7,6];
		
		
		lPublishedDocumentStateId int := 5;
	BEGIN		

--AUTHOR		
IF AuthorRole = any(pAllowedRolesArr) THEN 
      FOR Record IN
        SELECT count(*), 
        (case when d.state_id = ANY(ActiveStates) then 2
		    when d.state_id = ANY(RejectedStates)  then 4
		    when d.state_id = lIncompleteDocumentStateId then 1
		    when d.state_id = lPublishedDocumentStateId then 3
            end) as viewmode_id
          FROM pjs.documents d 
          JOIN pjs.document_users du ON du.document_id = d.id AND du.role_id = AuthorRole
          WHERE du.uid = pUid AND d.journal_id = pJournalId
          GROUP BY viewmode_id, role_id
          ORDER BY viewmode_id
	LOOP 
	      Res.count = Record.count;
	      Res.viewmode_id = Record.viewmode_id;
	      Res.role_id = AuthorRole;
	      RETURN NEXT Res;
	END LOOP;
END IF;

--REVIEWER requests
IF ReviewerRole = any(pAllowedRolesArr) THEN 
      FOR Record IN
        SELECT count(*) as count,
        51 as viewmode_id
	FROM pjs.document_user_invitations inv 
	JOIN pjs.documents d ON d.id = inv.document_id
        WHERE inv.uid = pUid 
          AND inv.state_id = NewRequestStateId
          AND d.journal_id = pJournalId
	LOOP 
	      Res.count = Record.count;
	      Res.viewmode_id = Record.viewmode_id;
	      Res.role_id = ReviewerRole;
	      RETURN NEXT Res;
	END LOOP;
END IF;

--REVIEWER reviews
IF ReviewerRole = any(pAllowedRolesArr) THEN 
      FOR Record IN

	SELECT count(*) as count,
	(case when rru.decision_id is NULL then 51
					   else 52 end) as viewmode_id
	FROM 
	pjs.documents d JOIN 
	pjs.document_users du ON du.document_id = d.id JOIN 
	pjs.document_review_round_users rru ON rru.document_user_id = du.id
	WHERE d.journal_id = pJournalId
	  AND du.role_id = ReviewerRole 
	  AND du.uid = pUid
	GROUP BY viewmode_id      
	LOOP 
	      Res.count = Record.count;
	      Res.viewmode_id = Record.viewmode_id;
	      Res.role_id = ReviewerRole;
	      RETURN NEXT Res;
	END LOOP;
END IF;

--SUBJECT EDITOR
IF SubjEdRole = any(pAllowedRolesArr) THEN 
      FOR Record IN
        SELECT count(*), 
        (case when d.state_id = ANY(InProductionStates) then 24
		    when d.state_id = ANY(RejectedStates)  then 26
		    when d.state_id = ANY(lInReviewDocumentStatesArr) then 23
		    when d.state_id = lPublishedDocumentStateId then 25  
            end) as viewmode_id
          FROM pjs.documents d 
          JOIN pjs.document_users du ON du.document_id = d.id AND du.role_id = SubjEdRole
          WHERE du.uid = pUid AND d.journal_id = pJournalId
          GROUP BY viewmode_id, role_id
          ORDER BY viewmode_id
	LOOP 
	      Res.count = Record.count;
	      Res.viewmode_id = Record.viewmode_id;
	      Res.role_id = SubjEdRole;
	      RETURN NEXT Res;
	END LOOP;
END IF;

--EDITOR BY GROUP
IF EditorRole = any(pAllowedRolesArr) THEN 
      FOR Record IN
        SELECT count(*), 
        (case 
		when d.state_id = UnassignedDocumentState  then 31
		when d.state_id = ANY(lInReviewDocumentStatesArr) then 32
		when d.state_id = ANY(lCopyEditingStatesArr) then 33
		when d.state_id = ANY(lLayoutEditingStatesArr) then 34
		when d.state_id = lReadyDocumentStateId then 35
		when d.state_id = lPublishedDocumentStateId then 36
		when d.state_id = ANY(RejectedStates) then 37
            end) as viewmode_id
          FROM pjs.documents d 
          WHERE d.journal_id = pJournalId
          GROUP BY viewmode_id
          ORDER BY viewmode_id
	LOOP 
	      Res.count = Record.count;
	      Res.viewmode_id = Record.viewmode_id;
	      Res.role_id = EditorRole;
	      RETURN NEXT Res;
	END LOOP;
END IF;
--EDITOR ALL
IF EditorRole = any(pAllowedRolesArr) THEN 
      FOR Record IN
        SELECT count(*), 
		30 as viewmode_id
          FROM pjs.documents d 
          WHERE d.journal_id = pJournalId AND d.state_id = ANY(ActiveStates)         
          GROUP BY viewmode_id
          ORDER BY viewmode_id
	LOOP 
	      Res.count = Record.count;
	      Res.viewmode_id = Record.viewmode_id;
	      Res.role_id = EditorRole;
	      RETURN NEXT Res;
	END LOOP;
END IF;

--LAYOUT
IF LayoutRole = any(pAllowedRolesArr) THEN 
      FOR Record IN
        SELECT count(*), 
        (case when d.state_id = ANY(lLayoutEditingStatesArr) then 71
			  when d.state_id = lReadyDocumentStateId  	    then 72
			  when d.state_id = lPublishedDocumentStateId     then 73
            end) as viewmode_id
          FROM pjs.documents d 
          JOIN pjs.document_users du ON du.document_id = d.id AND du.role_id = LayoutRole
          WHERE du.uid = pUid AND d.journal_id = pJournalId
          GROUP BY viewmode_id, role_id
          ORDER BY viewmode_id
	LOOP 
	      Res.count = Record.count;
	      Res.viewmode_id = Record.viewmode_id;
	      Res.role_id = LayoutRole;
	      RETURN NEXT Res;
	END LOOP;
END IF;

--COPY EDIT
IF CopyEditRole = any(pAllowedRolesArr) THEN 
      FOR Record IN
        SELECT count(*), 
        (case when d.state_id = lInCopyEditingStateId then 61
			  else 62
            end) as viewmode_id
          FROM pjs.documents d 
          JOIN pjs.document_users du ON du.document_id = d.id AND du.role_id = CopyEditRole
          WHERE du.uid = pUid AND d.journal_id = pJournalId
          GROUP BY viewmode_id, role_id
          ORDER BY viewmode_id
	LOOP 
	      Res.count = Record.count;
	      Res.viewmode_id = Record.viewmode_id;
	      Res.role_id = CopyEditRole;
	      RETURN NEXT Res;
	END LOOP;
END IF;
		
		
	RETURN;
END
$$;


ALTER FUNCTION spgetdashboardviewmodecount(pjournalid integer, pallowedrolesarr integer[], puid integer) OWNER TO pensoft;

--
-- Name: spgetdashboardviewmodecount(integer, integer[], integer[], integer[], integer[], integer); Type: FUNCTION; Schema: pjs; Owner: postgres
--

CREATE FUNCTION spgetdashboardviewmodecount(pjournalid integer, pallowedrolesarr integer[], pactivestatesarr integer[], prejectedstatesarr integer[], pinproductionstatesarr integer[], puid integer) RETURNS SETOF pjs.ret_spgetdashboardviewmodecount
    LANGUAGE plpgsql SECURITY DEFINER
    AS $$

	DECLARE

		lRes pjs.ret_spGetDashboardViewmodeCount;	

		lSubjEdRoleId int;

		lEditorRoleId int;		

		lAuthorRoleId int;

		lReviewerRoleId int;

		lLayoutRoleId int;

		lCopyEditRoleId int;

		

		lRecord record;

		

		lNewRequestStateId int;

		

		lIncompleteDocumentStateId int;

		lPublishedDocumentStateId int;

		lInReviewDocumentStatesArr int[];

		lUnassignedDocumentStateId int;

		lReadyDocumentStateId int;

		lCopyEditingStatesArr int[];

		lInCopyEditingStateId int;

		lLayoutEditingStatesArr int[];

		

	BEGIN		

		lAuthorRoleId = 11;

		lSubjEdRoleId = 3;

		lEditorRoleId = 2;

		lReviewerRoleId = 5;

		lCopyEditRoleId = 9;

		lLayoutRoleId = 8;

		

		lNewRequestStateId = 1;



		lIncompleteDocumentStateId = 1;

		lUnassignedDocumentStateId = 2;

		lInCopyEditingStateId = 8;

		lInReviewDocumentStatesArr = ARRAY[3, 9];

		lPublishedDocumentStateId = 5;

		lReadyDocumentStateId = 11;

		lCopyEditingStatesArr = ARRAY[14, 15, 8];

		lLayoutEditingStatesArr = ARRAY[12, 13, 4, 10];



--AUTHOR		

IF lAuthorRoleId = any(pAllowedRolesArr) THEN 

      FOR lRecord IN

        SELECT count(*), 

        (case when state_id = ANY(pActiveStatesArr) then 2

		    when state_id = ANY(pRejectedStatesArr)  then 4

		    when state_id = lIncompleteDocumentStateId then 1

		    when state_id = lPublishedDocumentStateId then 3

            end) as viewmode_id

          FROM pjs.documents d 

          JOIN pjs.document_users du ON du.document_id = d.id AND du.role_id = lAuthorRoleId

          WHERE du.uid = pUid AND d.journal_id = pJournalId

          GROUP BY viewmode_id, role_id

          ORDER BY viewmode_id

	LOOP 

	      lRes.count = lRecord.count;

	      lRes.viewmode_id = lRecord.viewmode_id;

	      lRes.role_id = lAuthorRoleId;

	      RETURN NEXT lRes;

	END LOOP;

END IF;



--REVIEWER requests

IF lReviewerRoleId = any(pAllowedRolesArr) THEN 

      FOR lRecord IN

        SELECT count(*) as count,

        51 as viewmode_id

	FROM pjs.document_user_invitations inv 

	JOIN pjs.documents d ON d.id = inv.document_id

        WHERE inv.uid = pUid 

          AND inv.state_id = lNewRequestStateId

          AND d.journal_id = pJournalId

	LOOP 

	      lRes.count = lRecord.count;

	      lRes.viewmode_id = lRecord.viewmode_id;

	      lRes.role_id = lReviewerRoleId;

	      RETURN NEXT lRes;

	END LOOP;

END IF;



--REVIEWER reviews

IF lReviewerRoleId = any(pAllowedRolesArr) THEN 

      FOR lRecord IN



	SELECT count(*) as count,

	(case when rru.decision_id is NULL then 51

					   else 52 end) as viewmode_id

	FROM 

	pjs.documents d JOIN 

	pjs.document_users du ON du.document_id = d.id JOIN 

	pjs.document_review_round_users rru ON rru.document_user_id = du.id

	WHERE d.journal_id = pJournalId

	  AND du.role_id = lReviewerRoleId 

	  AND du.uid = pUid

	GROUP BY viewmode_id      

	LOOP 

	      lRes.count = lRecord.count;

	      lRes.viewmode_id = lRecord.viewmode_id;

	      lRes.role_id = lReviewerRoleId;

	      RETURN NEXT lRes;

	END LOOP;

END IF;



--SUBJECT EDITOR

IF lSubjEdRoleId = any(pAllowedRolesArr) THEN 

      FOR lRecord IN

        SELECT count(*), 

        (case when state_id = ANY(pInProductionStatesArr) then 24

		    when state_id = ANY(pRejectedStatesArr)  then 26

		    when state_id = ANY(lInReviewDocumentStatesArr) then 23

		    when state_id = lPublishedDocumentStateId then 25  

            end) as viewmode_id

          FROM pjs.documents d 

          JOIN pjs.document_users du ON du.document_id = d.id AND du.role_id = lSubjEdRoleId

          WHERE du.uid = pUid AND d.journal_id = pJournalId

          GROUP BY viewmode_id, role_id

          ORDER BY viewmode_id

	LOOP 

	      lRes.count = lRecord.count;

	      lRes.viewmode_id = lRecord.viewmode_id;

	      lRes.role_id = lSubjEdRoleId;

	      RETURN NEXT lRes;

	END LOOP;

END IF;



--EDITOR BY GROUP

IF lEditorRoleId = any(pAllowedRolesArr) THEN 

      FOR lRecord IN

        SELECT count(*), 

        (case 

		when state_id = lUnassignedDocumentStateId  then 31

		when state_id = ANY(lInReviewDocumentStatesArr) then 32

		when state_id = ANY(lCopyEditingStatesArr) then 33

		when state_id = ANY(lLayoutEditingStatesArr) then 34

		when state_id = lReadyDocumentStateId then 35

		when state_id = lPublishedDocumentStateId then 36

		when state_id = ANY(pRejectedStatesArr) then 37

            end) as viewmode_id

          FROM pjs.documents d 

          WHERE d.journal_id = pJournalId

          GROUP BY viewmode_id

          ORDER BY viewmode_id

	LOOP 

	      lRes.count = lRecord.count;

	      lRes.viewmode_id = lRecord.viewmode_id;

	      lRes.role_id = lEditorRoleId;

	      RETURN NEXT lRes;

	END LOOP;

END IF;

--EDITOR ALL

IF lEditorRoleId = any(pAllowedRolesArr) THEN 

      FOR lRecord IN

        SELECT count(*), 

		30 as viewmode_id

          FROM pjs.documents d 

          WHERE d.journal_id = pJournalId AND state_id = ANY(pActiveStatesArr)         

          GROUP BY viewmode_id

          ORDER BY viewmode_id

	LOOP 

	      lRes.count = lRecord.count;

	      lRes.viewmode_id = lRecord.viewmode_id;

	      lRes.role_id = lEditorRoleId;

	      RETURN NEXT lRes;

	END LOOP;

END IF;



--LAYOUT

IF lLayoutRoleId = any(pAllowedRolesArr) THEN 

      FOR lRecord IN

        SELECT count(*), 

        (case when state_id = ANY(lLayoutEditingStatesArr) then 71

			  when state_id = lReadyDocumentStateId  	    then 72

			  when state_id = lPublishedDocumentStateId     then 73

            end) as viewmode_id

          FROM pjs.documents d 

          JOIN pjs.document_users du ON du.document_id = d.id AND du.role_id = lLayoutRoleId

          WHERE du.uid = pUid AND d.journal_id = pJournalId

          GROUP BY viewmode_id, role_id

          ORDER BY viewmode_id

	LOOP 

	      lRes.count = lRecord.count;

	      lRes.viewmode_id = lRecord.viewmode_id;

	      lRes.role_id = lLayoutRoleId;

	      RETURN NEXT lRes;

	END LOOP;

END IF;



--COPY EDIT

IF lCopyEditRoleId = any(pAllowedRolesArr) THEN 

      FOR lRecord IN

        SELECT count(*), 

        (case when state_id = lInCopyEditingStateId then 61

			  else 62

            end) as viewmode_id

          FROM pjs.documents d 

          JOIN pjs.document_users du ON du.document_id = d.id AND du.role_id = lCopyEditRoleId

          WHERE du.uid = pUid AND d.journal_id = pJournalId

          GROUP BY viewmode_id, role_id

          ORDER BY viewmode_id

	LOOP 

	      lRes.count = lRecord.count;

	      lRes.viewmode_id = lRecord.viewmode_id;

	      lRes.role_id = lCopyEditRoleId;

	      RETURN NEXT lRes;

	END LOOP;

END IF;

		

		

	RETURN;

END

$$;


ALTER FUNCTION spgetdashboardviewmodecount(pjournalid integer, pallowedrolesarr integer[], pactivestatesarr integer[], prejectedstatesarr integer[], pinproductionstatesarr integer[], puid integer) OWNER TO postgres;

--
-- Name: spgetdocumentinfoforreviewer(bigint); Type: FUNCTION; Schema: pjs; Owner: postgres
--

-- Type: pjs.ret_spgetdocumentinfoforreviewer

-- DROP TYPE pjs.ret_spgetdocumentinfoforreviewer;

/*
CREATE TYPE pjs.ret_spgetdocumentinfoforreviewer AS
   (document_id bigint,
    submitting_author_id bigint,
    current_round_id integer);
ALTER TYPE pjs.ret_spgetdocumentinfoforreviewer OWNER TO postgres;
*/

CREATE FUNCTION spgetdocumentinfoforreviewer(pdocumentid bigint) RETURNS pjs.ret_spgetdocumentinfoforreviewer
    LANGUAGE plpgsql SECURITY DEFINER
    AS $$
	DECLARE
		lRes pjs.ret_spGetDocumentInfoForReviewer;
		lAuthorVersionType int;
	BEGIN
		lAuthorVersionType = 1;
		
		SELECT INTO lRes 
			d.id, d.submitting_author_id, d.current_round_id
		FROM pjs.documents d
		JOIN usr u ON u.id = d.submitting_author_id
		WHERE d.id = pDocumentId;
		
		RETURN lRes;
	END
$$;


ALTER FUNCTION spgetdocumentinfoforreviewer(pdocumentid bigint) OWNER TO postgres;


--
-- Name: spgetdashboardviewmodecount(integer, integer[], integer); Type: ACL; Schema: pjs; Owner: pensoft
--

REVOKE ALL ON FUNCTION spgetdashboardviewmodecount(pjournalid integer, pallowedrolesarr integer[], puid integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION spgetdashboardviewmodecount(pjournalid integer, pallowedrolesarr integer[], puid integer) FROM pensoft;
GRANT ALL ON FUNCTION spgetdashboardviewmodecount(pjournalid integer, pallowedrolesarr integer[], puid integer) TO pensoft;
GRANT ALL ON FUNCTION spgetdashboardviewmodecount(pjournalid integer, pallowedrolesarr integer[], puid integer) TO PUBLIC;
GRANT ALL ON FUNCTION spgetdashboardviewmodecount(pjournalid integer, pallowedrolesarr integer[], puid integer) TO iusrpmt;


--
-- Name: spgetdashboardviewmodecount(integer, integer[], integer[], integer[], integer[], integer); Type: ACL; Schema: pjs; Owner: postgres
--

REVOKE ALL ON FUNCTION spgetdashboardviewmodecount(pjournalid integer, pallowedrolesarr integer[], pactivestatesarr integer[], prejectedstatesarr integer[], pinproductionstatesarr integer[], puid integer) FROM PUBLIC;
REVOKE ALL ON FUNCTION spgetdashboardviewmodecount(pjournalid integer, pallowedrolesarr integer[], pactivestatesarr integer[], prejectedstatesarr integer[], pinproductionstatesarr integer[], puid integer) FROM postgres;
GRANT ALL ON FUNCTION spgetdashboardviewmodecount(pjournalid integer, pallowedrolesarr integer[], pactivestatesarr integer[], prejectedstatesarr integer[], pinproductionstatesarr integer[], puid integer) TO postgres;
GRANT ALL ON FUNCTION spgetdashboardviewmodecount(pjournalid integer, pallowedrolesarr integer[], pactivestatesarr integer[], prejectedstatesarr integer[], pinproductionstatesarr integer[], puid integer) TO PUBLIC;
GRANT ALL ON FUNCTION spgetdashboardviewmodecount(pjournalid integer, pallowedrolesarr integer[], pactivestatesarr integer[], prejectedstatesarr integer[], pinproductionstatesarr integer[], puid integer) TO iusrpmt;


--
-- Name: spgetdocumentinfoforreviewer(bigint); Type: ACL; Schema: pjs; Owner: postgres
--

REVOKE ALL ON FUNCTION spgetdocumentinfoforreviewer(pdocumentid bigint) FROM PUBLIC;
REVOKE ALL ON FUNCTION spgetdocumentinfoforreviewer(pdocumentid bigint) FROM postgres;
GRANT ALL ON FUNCTION spgetdocumentinfoforreviewer(pdocumentid bigint) TO postgres;
GRANT ALL ON FUNCTION spgetdocumentinfoforreviewer(pdocumentid bigint) TO PUBLIC;
GRANT ALL ON FUNCTION spgetdocumentinfoforreviewer(pdocumentid bigint) TO iusrpmt;