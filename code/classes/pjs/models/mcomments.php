<?php
require_once PATH_CLASSES . 'comments.php';
class mComments extends emBase_Model {
	function __construct(){
		$this->m_con = new DBCn();
		$this->m_con->Open();
		$this->m_con->SetFetchReturnType(PGSQL_ASSOC);
	}


	function GetVersionFilteredRootIdsList($pVersionId, $pDisplayResolved, $pDisplayInline, $pDisplayGeneral, $pFilterUsers, $pSelectedUsers, $pUid, $pFilterVersionRoleVisibility = false){
		$lResult = array();
		$lRootCommentSelectName = 'pjs.msg';
		if($pFilterVersionRoleVisibility){
			$lRootCommentSelectName = ' (SELECT * FROM pjs.spGetVersionRoleFilteredMsgRootIds(' . $pVersionId . ')) ';
		}

		$lSql = '
		SELECT DISTINCT m1.id
		FROM ' . $lRootCommentSelectName . ' m1
		JOIN pjs.msg m2 ON (m1.id = m2.rootid)
		WHERE m2.version_id =' .  $pVersionId;

		if(!$pDisplayResolved){
			$lSql .= ' AND coalesce(m1.is_resolved, false) = false ';
		}

		if(!$pDisplayInline){
			$lSql .= ' AND (coalesce(m1.start_object_instances_id, 0) = 0
				AND coalesce(m1.end_object_instances_id, 0) = 0
			) ';
		}

		if(!$pDisplayGeneral){
			$lSql .= ' AND (coalesce(m1.start_object_instances_id, 0) <> 0
				OR coalesce(m1.end_object_instances_id, 0) <> 0
			) ';
		}

		if($pFilterUsers){
			if(!is_array($pSelectedUsers) || !count($pSelectedUsers)){
				$pSelectedUsers = array(0);
			}else{
				$pSelectedUsers = array_map('intval', $pSelectedUsers);
			}
			$lSql .= ' AND (m2.usr_id IN (' . implode(',', $pSelectedUsers) . ') OR m2.undisclosed_usr_id IN (' . implode(',', $pSelectedUsers) . '))';
		}

// 		var_dump($pDisplayResolved, $pDisplayInline, $pDisplayGeneral);

		$this->m_con->Execute($lSql);
		while(!$this->m_con->Eof()){
			$lResult[] = $this->m_con->mRs['id'];
			$this->m_con->MoveNext();
		}
		// 		var_dump($lResult);
		return $lResult;
	}

	function GetCommentDetails($pCommentId){
		$lResult = array();
		$lSql = '
			SELECT m2.id as id, m2.rootid, m2.version_id,
					m2.document_id as document_id,
					m2.author as author,
					m2.msg as msg,
					m2.subject as subject,
					m2.usr_id as usr_id,
					m2.lastmoddate as lastmoddate,
					u.photo_id as photo_id,
					coalesce(u.first_name, \'\') || \' \' || coalesce(u.last_name, \'\') as fullname,
					m2.mdate as mdate,
					coalesce(m2.start_object_instances_id, 0) as start_instance_id,
					coalesce(m2.end_object_instances_id, 0) as end_instance_id,
					coalesce(m2.start_object_field_id, 0) as start_field_id,
					coalesce(m2.end_object_field_id, 0) as end_field_id,
					coalesce(m2.start_offset, 0) as start_offset,
					coalesce(m2.end_offset, 0) as end_offset,
					m2.original_id,
					m2.is_resolved::int as is_resolved,
					m2.resolve_uid,
					coalesce(u2.first_name, \'\') || \' \' || coalesce(u2.last_name, \'\') as resolve_fullname,
					m2.resolve_date,
					m2.is_disclosed::int as is_disclosed,
					m2.undisclosed_usr_id,
					uu.name as undisclosed_user_fullname
			FROM pjs.msg m1
			JOIN pjs.msg m2 ON (m1.id = m2.rootid)
			JOIN usr u ON m2.usr_id = u.id
			LEFT JOIN usr u2 ON m2.resolve_uid = u2.id
			LEFT JOIN undisclosed_users uu ON uu.id = m2.undisclosed_usr_id
			JOIN usr_titles ut ON ut.id = u.usr_title_id
			WHERE m2.id =' .  $pCommentId. '
			ORDER BY m2.rootid, m2.mdate
			LIMIT 1
		';
// 		var_dump($lSql);
		$this->m_con->Execute($lSql);
		$lResult = $this->m_con->mRs;

// 		var_dump($lResult);
		return $lResult;
	}

	function DeleteComment($pCommentId, $pUsrId){
		$lResult = array(
			'err_cnt' => 0,
			'err_msg' => ''
		);
		$lSql = 'SELECT * FROM pjs.spDeleteComment(' . $pCommentId . ',' . $pUsrId . ');';
// 		var_dump($this->m_con->Execute($lSql));
// 		var_dump($lSql);
// 		var_dump($this->m_con->Execute($lSql));
		if(!$this->m_con->Execute($lSql)){
			$lResult['err_cnt']++;
			$lResult['err_msg'] = getstr($this->m_con->GetLastError());
		}

// 		var_dump($this->m_con->mRs);
		return $lResult;
	}

	/**
	 * Resolve/Unresolve a comment
	 * @param unknown_type $pCommentId
	 * @param unknown_type $pResolve
	 * @param unknown_type $pUsrId
	 */
	function ResolveComment($pCommentId, $pResolve, $pUsrId){
		$lResult = array(
			'err_cnt' => 0,
			'err_msg' => ''
		);
		$lSql = 'SELECT m.*, m.is_resolved::int as is_resolved,
					m.resolve_uid,
					coalesce(u2.first_name, \'\') || \' \' || coalesce(u2.last_name, \'\') as resolve_fullname,
					m.resolve_date,
					m2.is_disclosed::int as is_disclosed,
					uu.name as undisclosed_user_fullname
				FROM pjs.spResolveComment(' . $pCommentId . ',' . (int)$pResolve . ',' . $pUsrId . ') m
				LEFT JOIN usr u2 ON m.resolve_uid = u2.id
				LEFT JOIN undisclosed_users uu ON uu.id = m2.undisclosed_usr_id
				;
		';
		// 		var_dump($this->m_con->Execute($lSql));
		// 		var_dump($lSql);
		// 		var_dump($this->m_con->Execute($lSql));
		if(!$this->m_con->Execute($lSql)){
			$lResult['err_cnt']++;
			$lResult['err_msg'] = getstr($this->m_con->GetLastError());
		}else{
			$lResult['is_resolved'] = (int)$this->m_con->mRs['is_resolved'];
			$lResult['resolve_date'] = $this->m_con->mRs['resolve_date'];
			$lResult['resolve_fullname'] = $this->m_con->mRs['resolve_fullname'];
			$lResult['resolve_uid'] = (int)$this->m_con->mRs['resolve_uid'];
		}

// 		var_dump($lResult);
		// 		var_dump($this->m_con->mRs);
		return $lResult;
	}

	function GetVersionCommentsNum($pVersionId){
		$lSql = 'SELECT count(*)
			FROM pjs.msg
			WHERE version_id = ' . $pVersionId;
		$this->m_con->Execute($lSql);
		return (int)$this->m_con->mRs['count'];
	}

	function ChangeCommentOffset($pCommentId, $pNewOffset, $pPositionFixType = COMMENT_START_POS_TYPE){
		$lSql = 'BEGIN;UPDATE pjs.msg SET
		' . ($pPositionFixType == COMMENT_START_POS_TYPE ? 'start_offset' : 'end_offset') . ' = ' . (int)$pNewOffset . '
		WHERE id = ' . (int)$pCommentId . ';COMMIT;';
// 		var_dump($lSql);
		$this->m_con->Execute($lSql);
// 		var_dump($this->m_con);


	}

	function GetCommentVersionId($pCommentId){
		$lSql = 'SELECT version_id
			FROM pjs.msg
			WHERE id = ' . $pCommentId;
		$this->m_con->Execute($lSql);
		return (int)$this->m_con->mRs['version_id'];
	}

	function GetVersionFieldComments($pVersionId, $pInstanceId, $pFieldId, $pCon = false){
		$lSql = 'SELECT m.id as comment_id, coalesce(m.start_object_instances_id, 0) as start_object_instances_id, coalesce(m.start_object_field_id, 0) as start_object_field_id, coalesce(m.start_offset, 0) as start_offset,
			coalesce(m.end_object_instances_id, 0) as end_object_instances_id, coalesce(m.end_object_field_id, 0) as end_object_field_id, coalesce(m.end_offset, 0) as end_offset
		FROM pjs.msg m
		WHERE m.version_id = ' . (int)$pVersionId . ' AND
			((m.start_object_instances_id = ' . (int)$pInstanceId . ' AND m.start_object_field_id = ' . $pFieldId . ') OR
			(m.end_object_instances_id = ' . (int)$pInstanceId . ' AND m.end_object_field_id = ' . $pFieldId . '))
		ORDER BY m.id
		';

// 		var_dump($lSql);
		$lCon = $this->m_con;
		if($pCon instanceof DBCn){
			$lCon = $pCon;
		}
		$lCon->Execute($lSql);
		$lResult = array();
		while(!$lCon->Eof()){
			$lRes = $lCon->mRs;

			$lCommentPrefixTypes = array(
				COMMENTS_FIX_TYPE_START_POS => 'start_',
				COMMENTS_FIX_TYPE_END_POS => 'end_',
			);

			foreach ($lCommentPrefixTypes as $lFixType => $lPrefix) {
				$lInstanceId = (int)$lRes[$lPrefix . 'object_instances_id'];
				$lFieldId = (int)$lRes[$lPrefix . 'object_field_id'];
				$lCommentId = (int)$lRes['comment_id'];
// 								var_dump($lInstanceId, $lFieldId, $lCommentId);
				if($lInstanceId && $lFieldId && $lCommentId && $lInstanceId == (int) $pInstanceId && $lFieldId == (int)$pFieldId){
					if(!array_key_exists($lCommentId, $lResult)){
						$lResult[$lCommentId] = array(
							'previous_' . $lPrefix . 'offset' => $lRes[$lPrefix . 'offset'],
							$lPrefix . 'offset' => $lRes[$lPrefix . 'offset'],
							'position_fix_type' => $lFixType,
						);
					}else{
						$lResult[$lCommentId]['previous_' . $lPrefix . 'offset'] = $lRes[$lPrefix . 'offset'];
						$lResult[$lCommentId][$lPrefix . 'offset'] = $lRes[$lPrefix . 'offset'];
						$lResult[$lCommentId]['position_fix_type'] = $lResult[$lCommentId]['position_fix_type'] | $lFixType;
					}
					$lResult[$lCommentId]['comment_pos_type'] = $lResult[$lCommentId]['position_fix_type'];
				}
			}


			$lCon->MoveNext();

		}
// 		var_dump($lResult);
		return $lResult;
	}

	/**
	 * Recalculate the positions of the comments in the specified field
	 * @param unknown_type $pVersionId
	 * @param unknown_type $pInstanceId
	 * @param unknown_type $pFieldId
	 * @param unknown_type $pNewFieldValue
	 * @param unknown_type $pPreviousFieldValue
	 * @param bool $pStoreNewPositionsToDb - if set to true the new positions will be stored in the db
	 */
	function RecalculateFieldCommentPositions($pVersionId, $pInstanceId, $pFieldId, $pNewFieldValue, $pPreviousFieldValue, $pStoreNewPositionsToDb = true, $pCon = false){
		$lFieldComments = $this->GetVersionFieldComments($pVersionId, $pInstanceId, $pFieldId, $pCon);
// 		var_dump($lFieldComments);
// 		var_dump($lFieldComments);
// 		var_dump($pPreviousFieldValue);
		$lResult = array();
		if(count($lFieldComments)){
			$lInlineComments = GetCommentNodesPosition($pNewFieldValue);
// 			var_dump($lInlineComments[339]);
			$lCommentsToRecalculateWithDiff = $lFieldComments;
// 			var_dump($lCommentsToRecalculateWithDiff);
			foreach ($lInlineComments as $lCommentId => $lCommentData) {
				if($lCommentData['position_fix_type'] & COMMENTS_FIX_TYPE_START_POS){
					if($lCommentsToRecalculateWithDiff[$lCommentId]['position_fix_type'] & COMMENTS_FIX_TYPE_START_POS){
						$lCommentsToRecalculateWithDiff[$lCommentId]['position_fix_type'] -= COMMENTS_FIX_TYPE_START_POS;
						$lFieldComments[$lCommentId]['new_start_offset'] = $lCommentData['new_start_offset'];
					}
				}

				if($lCommentData['position_fix_type'] & COMMENTS_FIX_TYPE_END_POS){
					if($lCommentsToRecalculateWithDiff[$lCommentId]['position_fix_type'] & COMMENTS_FIX_TYPE_END_POS){
						$lCommentsToRecalculateWithDiff[$lCommentId]['position_fix_type'] -= COMMENTS_FIX_TYPE_END_POS;
						$lFieldComments[$lCommentId]['new_end_offset'] = $lCommentData['new_end_offset'];
					}
				}
				if(!$lCommentsToRecalculateWithDiff[$lCommentId]['position_fix_type']){
					//This comment's position has already been commented by the comment node
					//do not recalculate it with diff
					unset($lCommentsToRecalculateWithDiff[$lCommentId]);
				}
			}
// 			var_dump($lCommentsToRecalculateWithDiff);

			$lResult = $lFieldComments;
// 			var_dump($lCommentsToRecalculateWithDiff);
			if(count($lCommentsToRecalculateWithDiff)){
				$lDiffModifiedComments = RecalculateCommentsPositions($pPreviousFieldValue, $pNewFieldValue, $lCommentsToRecalculateWithDiff);
// 				var_dump($lResult, $lDiffModifiedComments);
				foreach ($lDiffModifiedComments as $lCommentId => $lCommentData){
					if($lCommentData['position_fix_type'] & COMMENTS_FIX_TYPE_START_POS){
						$lResult[$lCommentId]['new_start_offset'] = $lCommentData['new_start_offset'];
					}
					if($lCommentData['position_fix_type'] & COMMENTS_FIX_TYPE_END_POS){
						$lResult[$lCommentId]['new_end_offset'] = $lCommentData['new_end_offset'];
					}
				}
				//Do not use array merge here because it messes the key values of the arrays
// 				$lResult = array_merge($lResult, $lDiffModifiedComments);
			}

			if($pStoreNewPositionsToDb){
				$this->SaveCommentRecalculatedPositions($lResult, $pCon);
			}
		}
		return $lResult;
	}

	/**
	 * Saves the positions of the modified comments in the db
	 * @param unknown_type $pCommentPositions
	 * @param unknown_type $pCon - if an instance of DBCn is passed, the
	 * 		update queries will be performed with it
	 * @throws Exception - if an sql error occurs
	 */
	function SaveCommentRecalculatedPositions($pCommentPositions, $pCon = false){
		$lCon = $this->m_con;
		if($pCon instanceof DBCn){
			$lCon = $pCon;
		}
		foreach ($pCommentPositions as $lCommentId => $lCommentData) {
			$lSql = 'UPDATE pjs.msg SET ';

			if($lCommentData['position_fix_type'] & COMMENTS_FIX_TYPE_START_POS){
				$lSql .= 'start_offset = ' . (int)$lCommentData['new_start_offset'];
			}
			if($lCommentData['position_fix_type'] & COMMENTS_FIX_TYPE_END_POS){
				if($lCommentData['position_fix_type'] & COMMENTS_FIX_TYPE_START_POS){
					$lSql .= ', ';
				}
				$lSql .= 'end_offset = ' . (int)$lCommentData['new_end_offset'];
			}

			$lSql .= 'WHERE id = ' . (int)$lCommentId;
			if(!(int)$lCon->Execute($lSql)){
				throw new Exception($lCon->GetLastError());
			}
		}
	}

	/**
	 * @formatter:off
	 * Returns a list of comments for the specific version which have are associated with instances.
	 * The format of the returned array is the following
	 *
	 *		array(
	 * 			instance_id => array(
	 * 				non_field_comments => array(//Comments which are in the beginning/end of the instance - not in a specific field
	 *	 				comment_id => array(
	 * 						start_offset => val,
	 *	 					end_offset => val,
	 * 						comment_pos_type => val
	 * 					),
	 * 				),
	 * 				field_comments => array(
	 * 					field_id => array(
	 * 						comment_id => array(
	 * 							start_offset => val,
	 *	 						end_offset => val,
	 * 							comment_pos_type => val
	 * 						),
	 * 					),
	 * 				),
	 * 			),
	 * 		)
	 * @param unknown_type $pVersionId
	 * @return multitype:multitype:multitype:
	 * @formatter:on
	 */
	function GetVersionInstanceComments($pVersionId, $pCon = false){
		$lCon = $this->m_con;
		if($pCon instanceof DBCn){
			$lCon = $pCon;
		}
		$lSql = '
		SELECT m.*
		FROM pjs.msg m
		WHERE m.version_id = ' . (int)$pVersionId . ' AND m.start_object_instances_id > 0 AND m.end_object_instances_id > 0
		';
		$lResult = array();
		$lCon->Execute($lSql);
		$lPositions = array(
			COMMENT_START_POS_TYPE => 'start_',
			COMMENT_END_POS_TYPE => 'end_',
		);
		while(!$lCon->Eof()){
			// 		var_dump($lCon->mRs);
			foreach ($lPositions as $lType => $lPrefix) {
				if(!is_array($lResult[$lCon->mRs[$lPrefix . 'object_instances_id']])){
					$lResult[$lCon->mRs[$lPrefix . 'object_instances_id']] = array(
						'non_field_comments' => array(),
						'field_comments' => array(),
					);
				}
				$lCommentId = $lCon->mRs['id'];
				$lInstanceComments = &$lResult[$lCon->mRs[$lPrefix . 'object_instances_id']];
				$lCommentsSubArray = &$lInstanceComments['non_field_comments'];
				if((int)$lCon->mRs[$lPrefix . 'object_field_id']){
					$lCommentsSubArray = &$lInstanceComments['field_comments'][(int)$lCon->mRs[$lPrefix . 'object_field_id']];
				}
				if(!array_key_exists($lCommentId, $lCommentsSubArray)){
					$lCommentsSubArray[$lCommentId] = array(
						$lPrefix . 'offset' => $lCon->mRs[$lPrefix . 'offset'],
						'comment_pos_type' => $lType,
					);
				}else{
					$lCommentsSubArray[$lCommentId][$lPrefix . 'offset'] = $lCon->mRs[$lPrefix . 'offset'];
					$lCommentsSubArray[$lCommentId]['comment_pos_type'] = $lCommentsSubArray[$lCommentId]['comment_pos_type'] | $lType;
				}
			}
			$lCon->MoveNext();
		}
		return $lResult;
	}

	/**
	 * @formatter:off
	 * Returns a list of all the merged comments of all the reviewers
	 * which are to be added to the SE version
	 * @param unknown_type $pReviewRoundId
	 * The format of the result array will be the following
	 * 		'comments_list' => array(//A list of the details of all the comments
	 * 			comment_id => lCommentData
	 * 		),
	 * 		'field_comments' => array(
	 * 			instance_id => array(//A list of all the comments for the specific instance
	 * 				field_id => array(//A list of all the comments for the specific field of the parent instance
	 * 					version_id => array(//A list of all the comments for the specific version for the parent field of the parent instance
	 * 						comment_id => position_fix_type// The fix type that should be performed for this comment for this field
	 * 							// A bitmask of COMMENTS_FIX_TYPE_START_POS and COMMENTS_FIX_TYPE_END_POS
	 * 					)
	 * 				)
	 * 			)
	 * 		)
	 *
	 *
	 * @formatter:on
	 */
	function GetReviewRoundMergedComments($pReviewRoundId){
		$lSql = 'SELECT *
			FROM spGetRoundReviewerMergedComments(' . (int)$pReviewRoundId . ')
			ORDER BY rootid ASC, (CASE WHEN id = rootid THEN 1 ELSE 0 END) DESC, original_mdate ASC, ord ASC';
		$this->m_con->Execute($lSql);
		$lCommentsList = array();
		$lFieldComments = array();
		$lCommentRoots = array();
		while(!$this->m_con->Eof()){
			$lRow = $this->m_con->mRs;
			$lRow['old_start_offset'] = $lRow['start_offset'];//Offset according to the version from which the comment is copied
			$lRow['old_end_offset'] = $lRow['end_offset'];
			$lRow['new_start_offset'] = $lRow['start_offset'];//Offset according to the new SE version
			$lRow['new_end_offset'] = $lRow['end_offset'];
			$lRow['original_start_offset'] = $lRow['start_offset'];//Offset according to the original author version
			$lRow['original_end_offset'] = $lRow['end_offset'];
			$lRow['start_offset_in_insert'] = 0;//If the position is in an insert change - offset according to the start of the change
			$lRow['end_offset_in_insert'] = 0;
			$lRow['start_offset_calculation_is_complete'] = false;
			$lRow['end_offset_calculation_is_complete'] = false;
			$lCommentId = $lRow['id'];

			if($lRow['rootid'] == $lCommentId){
				$lCommentRoots[$lCommentId] = array();
			}else{
				$lCommentRoots[(int)$lRow['rootid']][] = $lCommentId;
// 				var_dump($lCommentRoots);
			}



			$lCommentsList[$lCommentId] = $lRow;
			$lVersionId = (int)$lRow['version_id'];
			$lCommentPrefixTypes = array(
				COMMENTS_FIX_TYPE_START_POS => 'start_',
				COMMENTS_FIX_TYPE_END_POS => 'end_',
			);
			foreach ($lCommentPrefixTypes as $lFixType => $lPrefix){
				$lInstanceId = (int)$lRow[$lPrefix . 'object_instances_id'];
				$lFieldId = (int)$lRow[$lPrefix . 'object_field_id'];
				if($lInstanceId && $lFieldId){
					if(!array_key_exists($lInstanceId, $lFieldComments)){
						$lFieldComments[$lInstanceId] = array();
					}
					if(!array_key_exists($lFieldId, $lFieldComments[$lInstanceId])){
						$lFieldComments[$lInstanceId][$lFieldId] = array();
					}
					if(!array_key_exists($lVersionId, $lFieldComments[$lInstanceId][$lFieldId])){
						$lFieldComments[$lInstanceId][$lFieldId][$lVersionId] = array();
					}
					if(!array_key_exists($lVersionId, $lFieldComments[$lInstanceId][$lFieldId])){
						$lFieldComments[$lInstanceId][$lFieldId][$lVersionId] = array();
					}
					if(!array_key_exists($lCommentId, $lFieldComments[$lInstanceId][$lFieldId][$lVersionId])){
						$lFieldComments[$lInstanceId][$lFieldId][$lVersionId][$lCommentId] = $lFixType;
					}else{
						$lFieldComments[$lInstanceId][$lFieldId][$lVersionId][$lCommentId] = $lFieldComments[$lInstanceId][$lFieldId][$lVersionId][$lCommentId] | $lFixType;
					}
				}
			}

			$this->m_con->MoveNext();
		}
		$lResult = array(
			'comments_list' => $lCommentsList,
			'field_comments' => $lFieldComments,
			'comment_roots' => $lCommentRoots,
		);
		return $lResult;
	}

	/**
	 * Recalculate the positions of the comments for the specific field of the specific version
	 * @param array $pComments - an array of comments in the format returned from GetReviewRoundMergedComments
	 * @param unknown_type $pVersionId
	 * @param unknown_type $pInstanceId
	 * @param unknown_type $pFieldId
	 * @param unknown_type $pFieldPatch
	 */
	function RecalculateSingleVersionFieldMergedCommentPositions(&$pComments, $pVersionId, $pInstanceId, $pFieldId, &$pFieldPatch){
		//Check if there are any comments
		if(!is_array($pComments) || !array_key_exists('field_comments', $pComments)){
			return;
		}
		//Check if there are comments for the specific field of the version
		$lFieldComments = &$pComments['field_comments'];
		if(!array_key_exists($pInstanceId, $lFieldComments) || !array_key_exists($pFieldId, $lFieldComments[$pInstanceId]) || !array_key_exists($pVersionId, $lFieldComments[$pInstanceId][$pFieldId])){
			return;
		}
		$lVersionFieldComments = &$lFieldComments[$pInstanceId][$pFieldId][$pVersionId];
		if(!is_array($lVersionFieldComments) || !count($lVersionFieldComments)){
			return;
		}

// 		if($pVersionId == 4426){
// 			var_dump($pFieldPatch);
// 		}



		$lCommentPrefixTypes = array(
			COMMENTS_FIX_TYPE_START_POS => 'start_',
			COMMENTS_FIX_TYPE_END_POS => 'end_',
		);
		$lCommentsList = &$pComments['comments_list'];
		foreach ($pFieldPatch as $lCurrentChange){
			$lChangeStartIdx = $lCurrentChange['start_idx'];
			$lChangeLength = mb_strlen($lCurrentChange['modified_text']);
			$lChangeType = $lCurrentChange['change_type'];
// 			if($pVersionId == 3270){
// 				var_dump($lCurrentChange, $lChangeLength);
// 			}
			foreach ($lVersionFieldComments as $lCommentId => $lCommentFixType){
// 				if($pVersionId == 4563 && $lCommentId == 358){
// 					var_dump($lCurrentChange, $lChangeLength);
// 					var_dump($pComments['comments_list'][358]);
// 				}
				foreach ($lCommentPrefixTypes as $lFixType => $lPrefix){
					if($lCommentFixType & $lFixType){
						if($lChangeType == CHANGE_DELETE_TYPE && $lChangeStartIdx <= $lCommentsList[$lCommentId]['original_' . $lPrefix . 'offset']){//Delete change before the comment
// 							if($lCommentId == 159){
// 								var_dump($lChangeLength);
// 							}
							$lCommentsList[$lCommentId]['original_' . $lPrefix . 'offset'] += $lChangeLength;
							$lCommentsList[$lCommentId]['new_' . $lPrefix . 'offset'] = $lCommentsList[$lCommentId]['original_' . $lPrefix . 'offset'];
						}elseif($lChangeType == CHANGE_INSERT_TYPE){
							if($lChangeStartIdx + $lChangeLength < $lCommentsList[$lCommentId]['original_' . $lPrefix . 'offset']){//Change is before comment
// 								if($lCommentId == 159){
// 									var_dump(-$lChangeLength);
// 								}
								$lCommentsList[$lCommentId]['original_' . $lPrefix . 'offset'] -= $lChangeLength;
								$lCommentsList[$lCommentId]['new_' . $lPrefix . 'offset'] = $lCommentsList[$lCommentId]['original_' . $lPrefix . 'offset'];
							}elseif($lChangeStartIdx <= $lCommentsList[$lCommentId]['original_' . $lPrefix . 'offset'] && $lChangeStartIdx + $lChangeLength >= $lCommentsList[$lCommentId]['original_' . $lPrefix . 'offset']){//Comment is in the change
								$lCommentsList[$lCommentId][$lPrefix . 'offset_in_insert'] = $lCommentsList[$lCommentId]['original_' . $lPrefix . 'offset'] - $lChangeStartIdx;
								$lCommentsList[$lCommentId]['original_' . $lPrefix . 'offset'] = $lChangeStartIdx;
								$lCommentsList[$lCommentId]['new_' . $lPrefix . 'offset'] = $lCommentsList[$lCommentId]['original_' . $lPrefix . 'offset'];
							}
						}
					}
				}
			}
		}
// 		if($pVersionId == 3270){
// 			var_dump($pComments['comments_list'][559]);
// 		}


// 		var_dump($pFieldPatch);
// 		var_dump($pComments);
// 		exit;
	}

	/**
	 * Recalculate the positions of the comments for the specific field for all the versions
	 * @param array $pComments - an array of comments in the format returned from GetReviewRoundMergedComments
	 * @param unknown_type $pVersionId
	 * @param unknown_type $pInstanceId
	 * @param unknown_type $pFieldId
	 * @param unknown_type $pFieldPatch
	 */
	function RecalculateSingleFieldMergedCommentPositions(&$pComments, $pInstanceId, $pFieldId, &$pFieldPatch){
// 		var_dump($pFieldPatch);
		//Check if there are any comments
		if(!is_array($pComments) || !array_key_exists('field_comments', $pComments)){
			return;
		}
		//Check if there are comments for the specific field of the version
		$lAllFieldComments = &$pComments['field_comments'];
		if(!array_key_exists($pInstanceId, $lAllFieldComments) || !array_key_exists($pFieldId, $lAllFieldComments[$pInstanceId])){
			return;
		}
		$lFieldComments = &$lAllFieldComments[$pInstanceId][$pFieldId];
		if(!is_array($lFieldComments) || !count($lFieldComments)){
			return;
		}
		$lCommentPrefixTypes = array(
			COMMENTS_FIX_TYPE_START_POS => 'start_',
			COMMENTS_FIX_TYPE_END_POS => 'end_',
		);
		$lCommentsList = &$pComments['comments_list'];
// 		var_dump($pFieldPatch);
		foreach ($pFieldPatch as $lCurrentChange){
			$lChangeStartIdx = $lCurrentChange['start_idx'];
			$lChangeLength = mb_strlen($lCurrentChange['modified_text']);
			$lChangeType = $lCurrentChange['change_type'];
			if($lChangeType == CHANGE_DELETE_TYPE){
				continue;
			}

// 			var_dump($lChangeLength);
			$lChangeUid = $lCurrentChange['uid'];
			$lChangeVersionId = 0;
			if(is_array($lChangeUid) && count($lChangeUid)){
				$lChangeVersionId = (int)$lChangeUid[0]['version_id'];
			}
			foreach ($lFieldComments as $lVersionId => $lVersionComments){
				foreach ($lVersionComments as $lCommentId => $lCommentFixType){
					foreach ($lCommentPrefixTypes as $lFixType => $lPrefix){
						if($lCommentFixType & $lFixType && !$lCommentsList[$lCommentId][$lPrefix . 'offset_calculation_is_complete']){
							if($lChangeStartIdx > $lCommentsList[$lCommentId]['original_' . $lPrefix . 'offset']){//This change is after the comment - proceed with next position/comment
								$lCommentsList[$lCommentId][$lPrefix . 'offset_calculation_is_complete'] = true;
								continue;
							}
							if($lChangeVersionId == $lVersionId && $lChangeStartIdx == $lCommentsList[$lCommentId]['original_' . $lPrefix . 'offset']){//The insert in which the comment is
								$lCommentsList[$lCommentId]['new_' . $lPrefix . 'offset'] += $lCommentsList[$lCommentId][$lPrefix . 'offset_in_insert'];
								$lCommentsList[$lCommentId][$lPrefix . 'offset_calculation_is_complete'] = true;
							}else{//A change before the comment
								if($lChangeStartIdx < $lCommentsList[$lCommentId]['original_' . $lPrefix . 'offset'] || $lCommentsList[$lCommentId]['original_start_offset'] == $lCommentsList[$lCommentId]['original_end_offset'] || $lFixType == COMMENTS_FIX_TYPE_START_POS){
									$lCommentsList[$lCommentId]['new_' . $lPrefix . 'offset'] += $lChangeLength;
								}
							}
						}
					}
				}
			}
		}
	}

	function ImportReviewVersionMergedComments($pVersionId, &$pComments, &$pCon = false){
// 		var_dump($pComments["comments_list"][559]);
// 		return true;
		$lCon = $pCon;
		if(!$lCon){
			$lCon = $this->m_con;
		}
		if(!(int)$pVersionId || !array_key_exists('comments_list', $pComments) || !count($pComments)){
			return true;
		}

		foreach($pComments['comment_roots'] as $lRootId => $lSubComments){
			$lCommentData = $pComments['comments_list'][$lRootId];
			$lSql = 'SELECT * FROM spImportMergedComment(' . (int)$pVersionId . ', ' . (int) $lRootId . ', ' . (int)$lCommentData['original_id'] . ', ' . (int)$lRootId . ', '
			. (int) $lCommentData['new_start_offset'] . ', ' . (int) $lCommentData['new_end_offset'] . ') ';

			if(!$lCon->Execute($lSql)){
				return false;
			}
			$lRootId = (int)$lCon->mRs['id'];
			if(!$lRootId){
				return false;
			}
			foreach ($lSubComments as $lSubCommentId){
				$lCommentData = $pComments['comments_list'][$lSubCommentId];
				$lSql = 'SELECT * FROM spImportMergedComment(' . (int)$pVersionId . ', ' . (int) $lSubCommentId . ', ' . (int)$lCommentData['original_id'] . ', ' . (int)$lRootId . ', '
				. (int) $lCommentData['new_start_offset'] . ', ' . (int) $lCommentData['new_end_offset'] . ') ';

				if(!$lCon->Execute($lSql)){
					return false;
				}
			}
		}
		return true;
	}


}
?>