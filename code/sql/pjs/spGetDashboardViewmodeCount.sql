DROP TYPE ret_spGetDashboardViewmodeCount CASCADE;
CREATE TYPE ret_spGetDashboardViewmodeCount AS (
	role_id int,
	viewmode_id int,
	count int
);

CREATE OR REPLACE FUNCTION spGetDashboardViewmodeCount(	
	pJournalId int,
	pAllowedRolesArr int[],
	pActiveStatesArr int[],
	pRejectedStatesArr int[],
	pInProductionStatesArr int[],
	pUid int
)
  RETURNS SETOF ret_spGetDashboardViewmodeCount AS
$BODY$
	DECLARE
		lRes ret_spGetDashboardViewmodeCount;	
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
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetDashboardViewmodeCount(
	pJournalId int,
	pAllowedRolesArr int[],
	pActiveStatesArr int[],
	pRejectedStatesArr int[],
	pInProductionStatesArr int[],
	pUid int
) TO iusrpmt;
