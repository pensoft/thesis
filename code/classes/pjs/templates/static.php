<?php

function getCommit(){
	return file_get_contents(dirname(__FILE__) . '/../../../../.git/refs/heads/master');
}

function infoMenu($items){
	$result = '';
	foreach ($items as $key => $value) {
		$result .= '<li data-info-type="' . $key . '"><span class="hyper unselectable">'.$value.'</span><span class="hidden-bold unselectable">'.$value.'</span></li>';
	}
	return $result;
}

function sortLink($pViewMode, $pOrderByColumn, $pSortOrder, $pCurrentColumn, $pTitle) {

	$lOrder = '';
	$pCurrentColumn = str_replace('sort_', '', $pCurrentColumn);
	// var_dump(array( $pViewMode, $pOrderByColumn, $pSortOrder,
	// $pCurrentColumn, $pTitle));
	if($pCurrentColumn == $pOrderByColumn)
		$lOrder = '&amp;order=' . $pSortOrder;
	return '<a href="?view_mode=' . $pViewMode . '&amp;sort_by=' . $pCurrentColumn . $lOrder . '">' . getstr($pTitle) . '</a>';
}

function initWorldTree($identifier) {
	if($identifier == "alerts_geographical_cats")
		return "children: [
		{
			title: 'World',
			key: 3,
			expand: true,
			children: [
			{
				title: 'Europe',
				key: 1,
				isLazy: true
			},
			{
				title: 'Asia',
				key: 5,
				isLazy: true
			}
			]
		}], ";
}

function reviewTypesPics($ids, $names) {
	$lRet = "";
	$i = 0;
	$id = explode(',', $ids);
	$name = explode(',', $names);
	for($i = 0; $i < count($id); $i ++){
		$lRet .= '<img src="/i/review_type' . $id[$i] . '.png" alt="' . $name[$i] . '" title="' . $name[$i] . '" />&nbsp;';
	}
	return $lRet;
}

function editorial_office($group_id) {
	if($group_id == CONTACT_GROUP_ID){
		return '<table>
		<tr><td style="vertical-align: top !important"><img src="/i/pensoft-eagle.png" alt="Pensoft&apos;s eagle" width="70" height="70" style="border: 1px; margin-right: 10px" /></td>
			<td id="contacts">
			<h3>Editorial Office</h3>
			<p>Prof. Georgi Zlatarski Street 12<br />
			1700 Sofia, Bulgaria <br />
			Tel. +359-2-8704281<br />
			Fax  +359-2-8704282<br />
			Email <a href="mailto:bdj@pensoft.net">bdj@pensoft.net</a></p>
			</td></tr>
		</table>';
	}
}

function to_xhtml($str) {
	return htmlspecialchars($str, ENT_XHTML | ENT_SUBSTITUTE | ENT_QUOTES, 'UTF-8', false);
}

function strip_invalid($str) {
	return preg_replace('/[\s\W]+/', '', $str);
}

function css_tag($css, $media = 'all') {
	return '<link type="text/css" rel="stylesheet" href="/lib/' . $css . '.css?v='. getCommit ().'"  media="' . $media . '" />
	';
}
function js_tag($js) {
	return '<script src="/lib/' . $js . '.js?v='. getCommit ().'" type="text/javascript"></script>
	';
}
function render_else($arg, $instead) {
	return $arg ? $arg : $instead;
}

function render_if($arg, $prefix, $suffix) {
	if($arg)
		return $prefix . htmlentities($arg, ENT_NOQUOTES | ENT_XHTML, 'UTF-8', false) . $suffix;
	else
		return '';
}

function unsafe_render_if($arg, $prefix = '', $suffix = '') {
	if($arg)
		return $prefix . $arg . $suffix;
	else
		return '';
}

function suggestedBy($id) {

	switch ($id) {
		case 1 :
			$res = 'by author';
			break;
		case 2 :
			$res = 'by editor';
			break;
		case 3 :
			$res = 'by Pensoft';
			break;
	}
	return getstr($res);
}

function filterSE($letter, $doc_id) {
	return "<div class='letter_row_filter'><a class='" . ($letter === strtoupper($_REQUEST['letter']) ? 'current' : '') . " letter'
	 href='/view_document?id=$doc_id&amp;view_role=2&amp;mode=1&amp;letter=$letter'
	 >$letter</a></div>";
}
function AlphabetFilter($doc_id) {
	$lRet = '';
	foreach(range('A', 'Z') as $letter){
		$lRet .= filterSE($letter, $doc_id);
	}
	return $lRet;
}

function SEexpertise($first, $second) {
	return $first . (($first && $second) ? '<br />&nbsp;' : '') . $second;
}
function translate($text, $late) {
	return  "<td class='$late'>" . getstr($text) . '</td>';
}
/*
function reduce($pWho, $late) {
	foreach($pWho as $key => $value){
		$pWho[$key] = translate($pWho[$key], $late[$key]);
	}
	return '<td>' . implode("</td><td>", $pWho) . '</td>';
}*/

function translate_row($action, $who, $schedule, $days, $state, $reminder){
	return translate($action, $state) .
		   translate($who, $state) .
		   translate($schedule, $state) .
		   translate($days, $state . ' days').
		   translate($reminder, $state);;
}

function merge_cells($action, $who, $schedule, $days, $state, $remind){
	$first_row = translate_row( array_shift($action),
								array_shift($who),
								array_shift($schedule),
								array_shift($days),
								array_shift($state),
								array_shift($remind)
			);
	return $first_row .
		implode('', array_map(
			function($action1, $who1, $schedule1, $days1, $state1, $remind1){
				$otherRow = translate_row($action1, $who1, $schedule1, $days1, $state1, $remind1);
				return "</tr>\n<tr>" . $otherRow;
			},
			$action, $who, $schedule, $days, $state, $remind));
}

function comma_if($lst) {
	return (strlen($lst) > 0) ? ", $lst" : "";
}

function comma_ifs($first, $second, $prefix, $suffix, $sep = ", ") {
	return render_if($first . (strlen($first) * strlen($second) ? $sep : "") . $second, $prefix, $suffix);
}
function editor_notes($notes) {
	return (strlen($notes) > 0) ? '<img src="/i/note.png" title="' . $notes . '" alt="' . getstr('pjs.dashboard.editor_note') . '" />' : "";
}
function issue($special_issue_name) {
	return ($special_issue_name ? '' : getstr('pjs.regularIssue'));
}

function nth($n) {
	switch ($n) {
		case 1 :
			$suffix = 'st';
			break;
		case 2 :
			$suffix = 'nd';
			break;
		case 3 :
			$suffix = 'rd';
			break;
		default :
			$suffix = 'th';
			break;
	}
	return "$n<sup>$suffix</sup>";
}
function action($state_id, $submitter_email, $submitter_name, $late) {
	$author = '<a href="mailto:' . $submitter_email . '" title="' . getstr('pjs.dashboards.EmailSubmittingAuthor') . '">' . $submitter_name . '</a>';
	$EditorialOffice = getstr('pjs.dashboards.EditorialOffice');
	$CopyEditor = "Copy editor";
	$LayoutEditor = "Layout Editor";
	$SubjectEditor = "Subject Editor";
	switch ($state_id) {
		// 1 incomplete - inline
		// 5 published - inline
		// 6 archived - inline
		// 7 rejected - inline

		case DOCUMENT_APPROVED_FOR_PUBLISH : // 11
			$who = $EditorialOffice;
			$status = getstr('ask Teo');
			break;
		case DOCUMENT_WAITING_SE_ASSIGNMENT_STATE : // 2
			$who = $EditorialOffice;
			$status = getstr('pjs.dashboards.actions.assignSubjectEditor');
			break;

		// Review
		case DOCUMENT_IN_REVIEW_STATE : // 3
			$who = $SubjectEditor;
			$status = getstr('take decision');
			break;

		case DOCUMENT_REVISIONS_AFTER_REVIEW_STATE : // 9
			$who = $author;
			$status = getstr('pjs.dashboards.actions.WaitingAuthorAfterReviewRound');
			break;

		// Copy
		case DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_COPY_EDITING_STATE : // 14
			$who = $author;
			$status = getstr('pjs.dashboards.actions.WaitingAuthorSubmitVersionCopyEdit');
			break;
		case DOCUMENT_READY_FOR_COPY_REVIEW_STATE : // 15
			$who = $EditorialOffice;
			$status = getstr('pjs.dashboards.actions.assignCopyEditor');
			break;
		case DOCUMENT_IN_COPY_REVIEW_STATE : // 8
			$who = $CopyEditor;
			$status = getstr('pjs.dashboards.actions.linguisticEditing');
			break;

		// Layout
		case DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_LAYOUT_EDITING_STATE : // 12
		case DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_LAYOUT_EDITING_AFTER_COPY_EDITING_STATE : // 17
			$who = $author;
			$status = getstr('pjs.dashboards.actions.WaitingAuthorSubmitVersionLayout');
			break;
		case DOCUMENT_READY_FOR_LAYOUT_STATE : // 13
			$who = $EditorialOffice;
			$status = getstr('pjs.dashboards.actions.assignLayoutEditor');
			break;
		case DOCUMENT_IN_LAYOUT_REVIEW_STATE : // 4
			$who = $LayoutEditor;
			$status = getstr('pjs.dashboards.actions.Layout');
			break;
		case DOCUMENT_WAITING_AUTHOR_VERSION_AFTER_LAYOUT_STATE : // 10
			$who = $author;
			$status = getstr('pjs.dashboards.actions.WaitingAuthorAfterLayout');
			break;
	}
	return $status . '</td><td class="' . $late . '">' . $who;
}

function myfunc($pP) {
	return $pP * 100;
}
function getClearRowIfMultiple($pIsMultiple, $pHtmlIdent) {
	if((int) $pIsMultiple == 2){
		return '';
	}else{
		return '$("#' . $pHtmlIdent . '_autocomplete").tokenInput("clear");';
	}
}

function disableTree($pIsDisabled, $pHtmlIdent) {
	if($pIsDisabled){
		return '$("#tree' . $pHtmlIdent . '").dynatree("disable");';
	}
	return '';
}

function checkIsMultipleTokenInput($pIsMultiple) {
	if($pIsMultiple)
		return '';
	return 'tokenLimit: 1,';
}

function getProfilePic($pPhotoId, $pViewMode = 0) {
	$lRet = '';

	if((int) $pPhotoId){
		$lRet = '<div class="Prof-Photo">
					<img class="P-Prof-Pic" width="67" height="70" src="/showimg.php?filename=c67x70y_' . (int) $pPhotoId . '.jpg" alt="Profile picture" />
					<div class="P-Clear"></div>
				</div>
				' . ((int) $pViewMode ? "" : getstr('pwt.changeProfilePicture'));
	}else{
		$lRet = '<div class="Prof-Photo">
					<img src="/i/no_photo.png" width="67" height="70" alt="Profile picture" />
					<div class="P-Clear"></div>
				</div>
				' . ((int) $pViewMode ? "" : getstr('pwt.addProfilePicture'));
	}
	return $lRet;
}

function getProfilePicWithLink($pPhotoId, $pJournalId, $pId, $pViewMode = 0) {
	$lRet = '';

	if((int) $pPhotoId){
		$lRet = '<div class="Prof-Photo">
					<a href="/browse_journal_articles_by_author.php?journal_id=' . $pJournalId . '&user_id=' . $pId . '">
						<img class="P-Prof-Pic" width="67" height="70" src="/showimg.php?filename=c67x70y_' . (int) $pPhotoId . '.jpg" alt="Profile picture" />
					</a>
				</div>
				' . ((int) $pViewMode ? "" : getstr('pwt.changeProfilePicture'));
	}else{
		$lRet = '<div class="Prof-Photo">
					<a href="/browse_journal_articles_by_author.php?journal_id=' . $pJournalId . '&user_id=' . $pId . '">
						<img src="/i/no_photo.png" width="67" height="70" alt="Profile picture" />
					</a>
				</div>
				' . ((int) $pViewMode ? "" : getstr('pwt.addProfilePicture'));
	}
	return $lRet;
}

function getProfilePicSmall($pPhotoId) {
	$lRet = '';

	if((int) $pPhotoId){
		$lRet = '<img class="P-Prof-Pic" width="32" height="32" src="/showimg.php?filename=c32x32y_' . $pPhotoId . '.jpg" alt="Profile picture" />';
	}else{
		$lRet = '<img width="32" height="32" src="i/add_photo.png" alt="Profile picture" />';
	}
	return $lRet;
}

function getFormHeaderStep1($pIsProfileEdit) {
	global $user;

	if((int) $pIsProfileEdit || $user->id){
		$lRet = '<h2>' . getstr('pjs.profile.editstepone') . '</h2>';
	}else{
		$lRet = '<table cellspacing="0">
					<tr>
						<td class="reg-active-step"><span>' . getstr('pjs.register_step') . ' 1</span><br>' . getstr('pjs.account_information') . '</td>
						<td><span>' . getstr('pjs.register_step') . ' 2</span><br>' . getstr('pjs.contact_information') . '</td>
						<td><span>' . getstr('pjs.register_step') . ' 3</span><br>' . getstr('pjs.subscribe_to_email_and_rss') . '</td>
					</tr>
				</table>';
	}
	return $lRet;
}

function getFormHeaderStep2($pIsProfileEdit) {
	global $user;

	if((int) $pIsProfileEdit || $user->id){
		$lRet = '<h2>' . getstr('pjs.profile.editsteptwo') . '</h2>';
	}else{
		$lRet = '<table cellspacing="0">
					<tr>
						<td><span>' . getstr('pjs.register_step') . ' 1</span><br>' . getstr('pjs.account_information') . '</td>
						<td class="reg-active-step"><span>' . getstr('pjs.register_step') . ' 2</span><br>' . getstr('pjs.contact_information') . '</td>
						<td><span>' . getstr('pjs.register_step') . ' 3</span><br>' . getstr('pjs.subscribe_to_email_and_rss') . '</td>
					</tr>
				</table>';
	}
	return $lRet;
}

function getFormHeaderStep3($pIsProfileEdit) {
	global $user;

	if((int) $pIsProfileEdit || $user->id){
		$lRet = '<h2>' . getstr('pjs.profile.editstepthree') . '</h2>';
	}else{
		$lRet = '<table cellspacing="0">
					<tr>
						<td><span>' . getstr('pjs.register_step') . ' 1</span><br>' . getstr('pjs.account_information') . '</td>
						<td><span>' . getstr('pjs.register_step') . ' 2</span><br>' . getstr('pjs.contact_information') . '</td>
						<td class="reg-active-step"><span>' . getstr('pjs.register_step') . ' 3</span><br>' . getstr('pjs.subscribe_to_email_and_rss') . '</td>
					</tr>
				</table>';
	}
	return $lRet;
}

function getFormButtonStep1($pIsProfileEdit) {
	global $user;

	if((int) $pIsProfileEdit || $user->id){
		$lRet = '<div class="P-Grey-Btn-Holder P-Reg-Btn">
					<div class="P-Grey-Btn-Left"></div>
					<div class="P-Grey-Btn-Middle" onclick="submitProfileForm(\'registerfrm\', 1);">Save</div>
					<div class="P-Grey-Btn-Right"></div>
				</div>
				<div class="P-Grey-Btn-Holder P-Reg-Btn-R">
					<div class="P-Grey-Btn-Left"></div>
					<div class="P-Grey-Btn-Middle" onclick="window.location.href=\'/profile.php\';">Cancel</div>
					<div class="P-Grey-Btn-Right"></div>
				</div>';
	}else{
		$lRet = '<div class="P-Grey-Btn-Holder P-Reg-Btn">
					<div class="P-Grey-Btn-Left"></div>
					<div class="P-Grey-Btn-Middle" onclick="SubmitRegForm(\'P-Registration-Content\', \'registerfrm\', 1, 1, 0)">' . getstr('pjs.nextstep') . ' 2 &raquo;</div>
					<div class="P-Grey-Btn-Right"></div>
				</div>';
	}
	return $lRet;
}

function getFormButtonStep2($pIsProfileEdit) {
	global $user;

	if((int) $pIsProfileEdit || $user->id){
		$lRet = '<div class="P-Grey-Btn-Holder P-Reg-Btn">
					<div class="P-Grey-Btn-Left"></div>
					<div class="P-Grey-Btn-Middle" onclick="submitProfileForm(\'registerfrm\', 2);">Save</div>
					<div class="P-Grey-Btn-Right"></div>
				</div>
				<div class="P-Grey-Btn-Holder P-Reg-Btn-R">
					<div class="P-Grey-Btn-Left"></div>
					<div class="P-Grey-Btn-Middle" onclick="window.location.href=\'/profile.php\';">Cancel</div>
					<div class="P-Grey-Btn-Right"></div>
				</div>';
	}else{
		$lRet = '<div class="P-Grey-Btn-Holder P-Reg-Btn">
					<div class="P-Grey-Btn-Left"></div>
					<div class="P-Grey-Btn-Middle" onclick="SubmitRegForm(\'P-Registration-Content\', \'registerfrm\', 2, 1, 0)">&laquo; ' . getstr('pjs.prevstep') . ' 1</div>
					<div class="P-Grey-Btn-Right"></div>
				</div>
				<div class="P-Grey-Btn-Holder P-Reg-Btn-R">
					<div class="P-Grey-Btn-Left"></div>
					<div class="P-Grey-Btn-Middle" onclick="SubmitRegForm(\'P-Registration-Content\', \'registerfrm\', 1, 2, 0)">' . getstr('pjs.nextstep') . ' 3 &raquo;</div>
					<div class="P-Grey-Btn-Right"></div>
				</div>';
	}
	return $lRet;
}

function getFormButtonStep3($pIsProfileEdit) {
	global $user;

	if((int) $pIsProfileEdit || $user->id){
		$lRet = '<div class="P-Grey-Btn-Holder P-Reg-Btn">
					<div class="P-Grey-Btn-Left"></div>
					<div class="P-Grey-Btn-Middle" onclick="submitProfileForm(\'registerfrm\', 3);">Save</div>
					<div class="P-Grey-Btn-Right"></div>
				</div>
				<div class="P-Grey-Btn-Holder P-Reg-Btn-R">
					<div class="P-Grey-Btn-Left"></div>
					<div class="P-Grey-Btn-Middle" onclick="window.location.href=\'/profile.php\';">Cancel</div>
					<div class="P-Grey-Btn-Right"></div>
				</div>';
	}else{
		$lRet = '<div class="P-Grey-Btn-Holder P-Reg-Btn">
					<div class="P-Grey-Btn-Left"></div>
					<div class="P-Grey-Btn-Middle" onclick="SubmitRegForm(\'P-Registration-Content\', \'registerfrm\', 2, 2, 0)">&laquo; ' . getstr('pjs.prevstep') . ' 2</div>
					<div class="P-Grey-Btn-Right"></div>
				</div>
				<div onclick="" class="P-Green-Btn-Holder P-Reg-Btn-R">
					<div class="P-Green-Btn-Left"></div>
					<div class="P-Green-Btn-Middle" onclick="SubmitRegForm(\'P-Registration-Content\', \'registerfrm\', 1, 3)">Finish</div>
					<div class="P-Green-Btn-Right"></div>
				</div>';
	}
	return $lRet;
}

function showSEProceedButton($pWaitNominatedFlag, $pWaitPanelFlag, $pCanInviteNominatedFlag, $pReviews, $pRoundUserId, $pDocumentId, $pRoundNumber, $pReviewersCheck, $pDocumentReviewTypeId, $pDocumentReviewDueDate, $pRoundDueDate, $pUserVersionId, $pRole, $pRoundId, $pShowDivs = 1, $pCheckInvited = 1, $pMergeFlag = 1, $pReviewersLock = 'false') {
	$lShowProceedBtn = ($pReviewersCheck == 'true' ? true : false);
	$lMessage = '';

	/* Turn string to boolean vals */
	$pWaitNominatedFlag = ($pWaitNominatedFlag == 'true' ? true : false);
	$pWaitPanelFlag = ($pWaitPanelFlag == 'true' ? true : false);
	$pCanInviteNominatedFlag = ($pCanInviteNominatedFlag == 'true' ? true : false);
	/* Turn string to boolean vals */

	if($pRoundNumber == 3){
		$pReviewersLock = 'true';
	}

	/* START creating process messages (keys) */
	if((in_array($pRoundNumber, array(
		REVIEW_ROUND_ONE,
		REVIEW_ROUND_TWO
	))) && $pDocumentReviewTypeId == DOCUMENT_NON_PEER_REVIEW){
		//~ $lMessage = getstr('pjs.showproceedbtnround2_text');
		$lMessage = getstr('pjs.non_peer_review_take_decision_round_' . $pRoundNumber);
	}else{
		$lCheckRoundDueDateFlag = CheckDueDateDays($pRoundDueDate);
		/*
		var_dump($pWaitNominatedFlag);
		var_dump($lCheckRoundDueDateFlag);
		var_dump((int)$pReviews);
		var_dump($pWaitPanelFlag);
		var_dump($pCanInviteNominatedFlag);
		*/
		if($pRoundNumber == REVIEW_ROUND_ONE){

			switch($pDocumentReviewTypeId) {
				case DOCUMENT_CLOSED_PEER_REVIEW: {

					if($pWaitNominatedFlag && $lCheckRoundDueDateFlag['flag'] == 2) {
						$lMessage = getstr('pjs.conventional_peer_review_speedup_round_' . $pRoundNumber);
					} elseif ((int)$pReviews >= 1 && !($pWaitNominatedFlag || $pWaitPanelFlag)) {
						$lMessage = getstr('pjs.conventional_peer_review_take_decision_round_' . $pRoundNumber);
					} elseif ((int)$pReviews == 0 && !($pCanInviteNominatedFlag || $pWaitNominatedFlag || $pWaitPanelFlag)) {
						$lMessage = getstr('pjs.conventional_peer_review_take_decision_without_reviewers_round_' . $pRoundNumber);
					} else {
						$lMessage = getstr('pjs.conventional_peer_review_to_proceed_round_' . $pRoundNumber);
					}

				break;
				}
				case DOCUMENT_COMMUNITY_PEER_REVIEW: {
					if($pWaitNominatedFlag && $lCheckRoundDueDateFlag['flag'] == 2) {
						$lMessage = getstr('pjs.community_peer_review_speedup_round_' . $pRoundNumber);
					} elseif ($pReviews >= 1 && !($pWaitNominatedFlag || $pWaitPanelFlag)) {
						$lMessage = getstr('pjs.community_peer_review_take_decision_round_' . $pRoundNumber);
					} elseif ($pReviews == 0 && !($pCanInviteNominatedFlag || $pWaitNominatedFlag || $pWaitPanelFlag)) {
						$lMessage = getstr('pjs.community_peer_review_take_decision_without_reviewers_round_' . $pRoundNumber);
					} else {
						$lMessage = getstr('pjs.community_peer_review_to_proceed_round_' . $pRoundNumber);
					}

				break;
				}
				case DOCUMENT_PUBLIC_PEER_REVIEW: {
					if($pWaitNominatedFlag && $lCheckRoundDueDateFlag['flag'] == 2) {
						$lMessage = getstr('pjs.public_peer_review_speedup_round_' . $pRoundNumber);
					} elseif ($pReviews >= 1 && !($pWaitNominatedFlag || $pWaitPanelFlag)) {
						$lMessage = getstr('pjs.public_peer_review_take_decision_round_' . $pRoundNumber);
					} elseif ($pReviews == 0 && !($pCanInviteNominatedFlag || $pWaitNominatedFlag || $pWaitPanelFlag)) {
						$lMessage = getstr('pjs.public_peer_review_take_decision_without_reviewers_round_' . $pRoundNumber);
					} else {
						$lMessage = getstr('pjs.public_peer_review_to_proceed_round_' . $pRoundNumber);
					}

				break;
				}
				default:
				break;
			}
		}elseif($pRoundNumber == REVIEW_ROUND_TWO){

			switch($pDocumentReviewTypeId) {
				case DOCUMENT_CLOSED_PEER_REVIEW: {
					if($pWaitNominatedFlag && $lCheckRoundDueDateFlag['flag'] == 2) {
						$lMessage = getstr('pjs.conventional_peer_review_speedup_round_' . $pRoundNumber);
					} elseif ($pReviews >= 1 && !$pWaitNominatedFlag) {
						$lMessage = getstr('pjs.conventional_peer_review_take_decision_round_' . $pRoundNumber);
					} elseif ($pReviews == 0 && !$pWaitNominatedFlag) {
						$lMessage = getstr('pjs.conventional_peer_review_take_decision_without_reviewers_round_' . $pRoundNumber);
					} else {
						$lMessage = getstr('pjs.conventional_peer_review_to_proceed_round_' . $pRoundNumber);
					}

				break;
				}
				case DOCUMENT_COMMUNITY_PEER_REVIEW: {
					if($pWaitNominatedFlag && $lCheckRoundDueDateFlag['flag'] == 2) {
						$lMessage = getstr('pjs.community_peer_review_speedup_round_' . $pRoundNumber);
					} elseif ($pReviews >= 1 && !$pWaitNominatedFlag) {
						$lMessage = getstr('pjs.community_peer_review_take_decision_round_' . $pRoundNumber);
					} elseif ($pReviews == 0 && !$pWaitNominatedFlag) {
						$lMessage = getstr('pjs.community_peer_review_take_decision_without_reviewers_round_' . $pRoundNumber);
					} else {
						$lMessage = getstr('pjs.community_peer_review_to_proceed_round_' . $pRoundNumber);
					}

				break;
				}
				case DOCUMENT_PUBLIC_PEER_REVIEW: {
					if($pWaitNominatedFlag && $lCheckRoundDueDateFlag['flag'] == 2) {
						$lMessage = getstr('pjs.public_peer_review_speedup_round_' . $pRoundNumber);
					} elseif ($pReviews >= 1 && !$pWaitNominatedFlag) {
						$lMessage = getstr('pjs.public_peer_review_take_decision_round_' . $pRoundNumber);
					} elseif ($pReviews == 0 && !$pWaitNominatedFlag) {
						$lMessage = getstr('pjs.public_peer_review_take_decision_without_reviewers_round_' . $pRoundNumber);
					} else {
						$lMessage = getstr('pjs.public_peer_review_to_proceed_round_' . $pRoundNumber);
					}

				break;
				}
				default:
				break;
			}
		}elseif($pRoundNumber == REVIEW_ROUND_THREE){
			$lMessage = getstr('pjs.take_decision_round_' . $pRoundNumber);
		}
	}
	/* END creating process messages (keys) */

	$lUrl = '\'/view_version.php?version_id=' . $pUserVersionId . '&id=' . $pDocumentId . '&view_role=' . $pRole . '&round=' . $pRoundNumber . '&round_user_id=' . $pRoundUserId . '&duedate=' . $pRoundDueDate . '\'';
	if($pShowDivs == '_0'){
		if(in_array($pRoundNumber, array(
			REVIEW_ROUND_TWO,
			REVIEW_ROUND_THREE
		))){
			return '
				<div class="document_author_holder_content_no_review_yet_middle">
					<span class="yellow-green-txt">' . $lMessage . '</span>
					<table cellpadding="0" cellspacing="0" width="100%" style="padding-top:10px;">
						<tr>
							<td align="center">
								' . ($lShowProceedBtn ? '
								<div class="invite_reviewer_btn invite_reviewer_btn_E invite_reviewer_btn_E_first">
									<div class="invite_reviewer_btn_left"></div>
									<div class="invite_reviewer_btn_middle" onclick="' . ((int) $pCheckInvited && $pDocumentReviewTypeId != DOCUMENT_NON_PEER_REVIEW ? 'checkReviewersState(' . $pRoundId . ', ' . $lUrl . ', ' . (int) $pDocumentId . ', ' . (int) $pMergeFlag . ', \'' . $pReviewersLock . '\')' : 'openPopUp(' . $lUrl . ', 0, 0, \'window_' . $pUserVersionId . '\')') . '">' . getstr('pjs.se_editiorial_decision_text') . '</div>
									<div class="invite_reviewer_btn_right"></div>
									<div class="P-Clear"></div>
								</div>
								' : '<img src="./i/SE_decision_not_allowed.png"></img>') . '
							</td>
						</tr>
					</table>
				</div>
				' . ($pRoundNumber == REVIEW_ROUND_TWO && $pReviewersLock == 'false' ? '
				<div class="subm_reject_or_holder">
					<table cellpadding="0" cellspacing="0" width="100%">
						<colgroup>
							<col width="45%"></col>
							<col width="10%"></col>
							<col width="45%"></col>
						</colgroup>
						<tr>
							<td><div class="or_line"></div></td>
							<td align="center"><span class="or_text">OR</span></td>
							<td><div class="or_line"></div></td>
						</tr>
					</table>
				</div>' : '') . '
			';
		}else{
			return '';
		}
	}
	$lRes = '
		<div class="submission_notes_main_wrapper" style="padding-top:0px">
			<div class="document_author_holder_rev" style="padding-top:0px">
				<div class="document_author_holder_content">
					<div class="document_author_holder_content_no_review_yet">
						<div class="document_author_holder_content_no_review_yet_top">
							<table width="100%" cellspacing="0" cellpadding="0">
								<tbody><tr>
									<td width="5"><div class="yellow-top-left"></div></td>
									<td><div class="yellow-top-middle"></div></td>
									<td width="5"><div class="yellow-top-right"></div></td>
								</tr>
							</tbody></table>
						</div>
						<div class="document_author_holder_content_no_review_yet_middle">
							<span class="yellow-green-txt">' . $lMessage . '</span>
							<table cellpadding="0" cellspacing="0" width="100%" style="padding-top:10px;">
								<tr>
									<td align="center">
										' . ($lShowProceedBtn ? '
										<div class="invite_reviewer_btn invite_reviewer_btn_E invite_reviewer_btn_E_first">
											<div class="invite_reviewer_btn_left"></div>
											<div class="invite_reviewer_btn_middle" onclick="' . ((int) $pCheckInvited && $pDocumentReviewTypeId != DOCUMENT_NON_PEER_REVIEW ? 'checkReviewersState(' . $pRoundId . ', ' . $lUrl . ', ' . (int) $pDocumentId . ', ' . (int) $pMergeFlag . ', \'' . $pReviewersLock . '\')' : 'openPopUp(' . $lUrl . ', 0, 0, \'window_' . $pUserVersionId . '\')') . '">' . getstr('pjs.se_editiorial_decision_text') . '</div>
 											<div class="invite_reviewer_btn_right"></div>
											<div class="P-Clear"></div>
										</div>
										' : '<img src="./i/SE_decision_not_allowed.png"></img>') . '
									</td>
								</tr>
							</table>
						</div>
						<div class="document_author_holder_content_no_review_yet_bottom">
							<table width="100%" cellspacing="0" cellpadding="0">
								<tbody><tr>
									<td width="5"><div class="yellow-bottom-left"></div></td>
									<td><div class="yellow-bottom-middle"></div></td>
									<td width="5"><div class="yellow-bottom-right"></div></td>
								</tr>
							</tbody></table>
						</div>
					</div>
				</div>
			</div>
		</div>
	';

	return $lRes;
}
/*
 * function showSEProceedButton($pUserId, $pDocumentId, $pRoundNumber,
 * $pReviewersCheck, $pDocumentReviewTypeId, $pDocumentReviewDueDate,
 * $pRoundDueDate, $pUserVersionId, $pRole, $pRoundId) {
 * preg_match('/(\d+)[-–\/](\d+)[-–\/](\d+) (\d+):(\d+):(\d+)/', $pRoundDueDate,
 * $lMatch); $lNowDate = strtotime(date("Y-m-d H:i:s")); $lRoundDate =
 * strtotime($lMatch[3] . '-' . $lMatch[2] . '-' . $lMatch[1] . ' ' . $lMatch[4]
 * . ':' . $lMatch[5] . ':' . $lMatch[6]); $lMatch = array(); $lShowProceedBtn =
 * false; $lFlag = 0; if((in_array($pRoundNumber, array(REVIEW_ROUND_ONE,
 * REVIEW_ROUND_TWO))) && $pDocumentReviewTypeId == DOCUMENT_NON_PEER_REVIEW) {
 * $lShowProceedBtn = true; $lMessage = getstr('pjs.showproceedbtnround2_text');
 * $lFlag = 1; } if(!(int)$lFlag) { if($pRoundNumber == REVIEW_ROUND_ONE) {
 * $lMessage = getstr('pjs.showproceedbtnround1_text'); $lReviewTypeChecks =
 * true; if(in_array($pDocumentReviewTypeId,
 * array(DOCUMENT_COMMUNITY_PEER_REVIEW, DOCUMENT_PUBLIC_PEER_REVIEW))) {
 * preg_match('/(\d+)[-–\/](\d+)[-–\/](\d+) (\d+):(\d+):(\d+)/',
 * $pDocumentReviewDueDate, $lMatch); $lDocRoundDate = strtotime($lMatch[3] .
 * '-' . $lMatch[2] . '-' . $lMatch[1] . ' ' . $lMatch[4] . ':' . $lMatch[5] .
 * ':' . $lMatch[6]); if($lNowDate < $lDocRoundDate) { $lReviewTypeChecks =
 * false; } } if(($lNowDate > $lRoundDate) || ($pReviewersCheck == 'true' &&
 * $lReviewTypeChecks)) { $lShowProceedBtn = true; } } elseif ($pRoundNumber ==
 * REVIEW_ROUND_TWO) { $lMessage = getstr('pjs.showproceedbtnround2_text');
 * if(($lNowDate > $lRoundDate) || ($pReviewersCheck == 'true')) {
 * $lShowProceedBtn = true; } } elseif ($pRoundNumber == REVIEW_ROUND_THREE) {
 * $lMessage = getstr('pjs.showproceedbtnround2_text'); $lShowProceedBtn = true;
 * } } $lRes = ' <div class="submission_notes_main_wrapper"
 * style="padding-top:0px"> <div class="document_author_holder_rev"
 * style="padding-top:0px"> <div class="document_author_holder_content"> <div
 * class="document_author_holder_content_no_review_yet"> <div
 * class="document_author_holder_content_no_review_yet_top"> <table width="100%"
 * cellspacing="0" cellpadding="0"> <tbody><tr> <td width="5"><div
 * class="yellow-top-left"></div></td> <td><div
 * class="yellow-top-middle"></div></td> <td width="5"><div
 * class="yellow-top-right"></div></td> </tr> </tbody></table> </div> <div
 * class="document_author_holder_content_no_review_yet_middle"> <span
 * class="yellow-green-txt">' . $lMessage . '</span> <table cellpadding="0"
 * cellspacing="0" width="100%" style="padding-top:10px;"> <tr> <td
 * align="center"> ' . ($lShowProceedBtn ? ' <div class="invite_reviewer_btn
 * invite_reviewer_btn_E invite_reviewer_btn_E_first"> <div
 * class="invite_reviewer_btn_left"></div> <div
 * class="invite_reviewer_btn_middle"
 * onclick="openPopUp(\'/view_version.php?version_id=' . $pUserVersionId .
 * '&id=' . $pDocumentId . '&view_role=' . $pRole . '&round=' . $pRoundId .
 * '&round_user_id=' . $pUserId . '&duedate=' . $pRoundDueDate . '\')">'.
 * getstr('pjs.se_editiorial_decision_text') . '</div> <div
 * class="invite_reviewer_btn_right"></div> <div class="P-Clear"></div> </div> '
 * : '<img src="./i/SE_decision_not_allowed.png"></img>') . ' </td> </tr>
 * </table> </div> <div
 * class="document_author_holder_content_no_review_yet_bottom"> <table
 * width="100%" cellspacing="0" cellpadding="0"> <tbody><tr> <td width="5"><div
 * class="yellow-bottom-left"></div></td> <td><div
 * class="yellow-bottom-middle"></div></td> <td width="5"><div
 * class="yellow-bottom-right"></div></td> </tr> </tbody></table> </div> </div>
 * </div> </div> </div> '; return $lRes; }
 */
function DisplaySETextAboutDedicatedReviewer($pReviewerState, $pReviewerUsrState, $pDecisionId, $PreviewersAssignmentDuedate, $pDesicionName, $pRevUsrDueDate, $pRoundId, $pRoundUserId, $pReviwerId) {
	$lRes = '';
	if((int) $pDecisionId){
		$lRes = getstr('pjs.ready_text') . ': ' . $pDesicionName;
		return $lRes;
	}
	$lDatePicker = '
		$(function() {
			$( "#to_date" ).datepicker({
			showOn: "button",
			buttonImage: "i/articleCalendar.png",
			buttonImageOnly: true,
			dateFormat: \'dd/mm/yy\',
			onSelect: function(dateStr) {
			}
			});
		}); ';
	if(((int) $pReviewerUsrState != (int) REVIEWER_REMOVED || $pReviewerState == REVIEWER_CANCELLED_BY_SE_STATE || ((int) $pReviewerUsrState == (int) REVIEWER_REMOVED) && $pReviewerState == REVIEWER_INVITATION_NEW_STATE)){
		/*
		 * sended invitation (not confirmed)
		 */

		preg_match('/(\d+)[-–\/](\d+)[-–\/](\d+) (\d+):(\d+):(\d+)/', $PreviewersAssignmentDuedate, $lMatch);

		$lNowDate = strtotime(date("Y-m-d"));
		$lRevDate = strtotime($lMatch[3] . '-' . $lMatch[2] . '-' . $lMatch[1]);

		if($lNowDate <= $lRevDate){
			$lDueDateFlag = 1;
			$lSecDiff = $lRevDate - $lNowDate;
		}else{
			$lDueDateFlag = 2;
			$lSecDiff = $lNowDate - $lRevDate;
		}
		$lDaysDiff = floor($lSecDiff / 3600 / 24);
		switch ((int) $pReviewerState) {
			case REVIEWER_INVITATION_NEW_STATE :
				$lRes = ($lDueDateFlag == 1 ? 'Request will time out in ' . $lDaysDiff . ' day'.(abs($lDaysDiff)>1?'s':'').' <img title="'.getstr('pjs.tooltips.change_due_date').'" id="duedate_editimg" width="18" height="16" style="cursor:pointer;" class="ui-datepicker-trigger cursor"
					onclick="openDueDatePopUp(\'/updateduedate.php?action=user_invitation&roundduedate=' . $PreviewersAssignmentDuedate . '&roundid=' . $pRoundId . '&rounduserid=' . $pRoundUserId . '\', 400, 200)"
					src="../i/edit.png"></img>
				 	<!-- <input id="from_date" class="hasDatepicker" type="text" value="" name="from_date">

				 	<script >
				 		/*$(function() {
					        $( "#from_date" ).datepicker();
					    });*/



						$(function() {
							$( "#from_date" ).datepicker({
								showOn: "button",
								buttonImage: "/i/edit.png",
								buttonImageOnly: true,
								dateFormat: \'dd/mm/yy\',
								onSelect: function(dateStr) {
									alert(dateStr);
								}
							});
						});
					</script> -->
				 	' : '<span style="color:red">' . $lDaysDiff . ' day'.(abs($lDaysDiff)>1?'s':'').' late to respond <img title="'.getstr('pjs.tooltips.change_due_date').'" id="duedate_editimg" width="18" height="16" style="cursor:pointer;" class="ui-datepicker-trigger cursor" src="../i/edit.png"
					onclick="openDueDatePopUp(\'/updateduedate.php?action=user_invitation&roundduedate=' . $PreviewersAssignmentDuedate . '&roundid=' . $pRoundId . '&rounduserid=' . $pRoundUserId . '\', 400, 200)"
					></img></span>');

				break;
			case REVIEWER_CONFIRMED_STATE :
				if($pRevUsrDueDate){
					$lMatch = array();
					preg_match('/(\d+)[-–\/](\d+)[-–\/](\d+) (\d+):(\d+):(\d+)/', $pRevUsrDueDate, $lMatch);

					$lNowDate = strtotime(date("Y-m-d"));
					$lRevDate = strtotime($lMatch[3] . '-' . $lMatch[2] . '-' . $lMatch[1]);

					if($lNowDate <= $lRevDate){
						$lDueDateFlag = 1;
						$lSecDiff = $lRevDate - $lNowDate;
					}else{
						$lDueDateFlag = 2;
						$lSecDiff = $lNowDate - $lRevDate;
					}

					$lDaysDiff = floor($lSecDiff / 3600 / 24);
				}

				$lRes = ($lDueDateFlag == 1 ? '
					Review is due in ' . $lDaysDiff . ' day'.(abs($lDaysDiff)>1?'s':'').' <img title="'.getstr('pjs.tooltips.change_due_date').'" id="duedate_editimg" width="18" height="16" style="cursor:pointer;" class="ui-datepicker-trigger cursor" src="../i/edit.png"
					onclick="openDueDatePopUp(\'/updateduedate.php?action=user_decision&roundduedate=' . $PreviewersAssignmentDuedate . '&roundid=null&rounduserid=' . $pReviwerId . '\', 400, 200)"
					></img>' : '<span style="color:red">Review is ' . $lDaysDiff . ' day'.(abs($lDaysDiff)>1?'s':'').' late</span> <img title="'.getstr('pjs.tooltips.change_due_date').'" id="duedate_editimg" width="18" height="16" style="cursor:pointer;" class="ui-datepicker-trigger cursor" src="../i/edit.png"
					onclick="openDueDatePopUp(\'/updateduedate.php?action=user_decision&roundduedate=' . $PreviewersAssignmentDuedate . '&roundid=null&rounduserid=' . $pReviwerId . '\', 400, 200)"
					></img>');
				break;
			case REVIEWER_CANCELLED_STATE :
				$lRes = 'Request declined';
				break;
			case REVIEWER_TIMEDOUT_STATE :
			case REVIEWER_CANCELLED_BY_SE_STATE :
				$lRes = 'Request canceled/timeout';
				break;
			default :
				break;
		}

	}else{
		$lRes = 'Request canceled/timeout';
	}
	return $lRes;
}

function DisplayReviewIcon($pInvitationId, $pDecisionId, $pReviewerId, $pRoundId, $pDocumentId, $pDocUsrId, $pRoundNumber, $pReviewerVersionId) {
	if((int) $pDecisionId){
		$lRes .= '
			<span class="reviewer_act">
				<img title="'.getstr('pjs.tooltips.view_review').'" onclick="openPopUp(\'/view_version.php?version_id=' . $pReviewerVersionId . '&id=' . $pDocumentId . '&view_role=' . DEDICATED_REVIEWER_ROLE . '&round=' . $pRoundNumber . '&round_user_id=' . $pReviewerId . '&invitation_id=' . $pInvitationId . '\', 0, 0, \'window_' . $pReviewerVersionId . '\')" src="../i/review_ready.png"></img>
			</span>';
		return $lRes;
	}else{
		$lRes = '&nbsp;';
	}

	return $lRes;
}

function DisplaySEActionsAboutDedicatedReviewer($pInvitationId, $pReviewerState, $pReviewerUsrState, $pDecisionId, $PreviewersAssignmentDuedate, $pReviewerId, $pRoundId, $pDocumentId, $pDocUsrId, $pRoundNumber, $pReviewerVersionId) {
	$lRes = '';
	if((int) $pDecisionId){
		$lRes .= '<span class="reviewer_act"><img title="'.getstr('pjs.tooltips.view_review').'" onclick="openPopUp(\'/view_version.php?version_id=' . $pReviewerVersionId . '&id=' . $pDocumentId . '&view_role=' . DEDICATED_REVIEWER_ROLE . '&round=' . $pRoundNumber . '&round_user_id=' . $pReviewerId . '&invitation_id=' . $pInvitationId . '\', 0, 0, \'window_' . $pReviewerVersionId . '\')" src="../i/review_ready.png"></img></span>';
		return $lRes;
	}
	if(((int) $pReviewerUsrState != (int) REVIEWER_REMOVED || $pReviewerState == REVIEWER_CANCELLED_BY_SE_STATE || ((int) $pReviewerUsrState == (int) REVIEWER_REMOVED) && $pReviewerState == REVIEWER_INVITATION_NEW_STATE)){
		/*
		 * sended invitation (not confirmed)
		 */

		switch ((int) $pReviewerState) {
			case REVIEWER_INVITATION_NEW_STATE :
				$lRes .= '
					<span class="reviewer_act"><a href="javascript:void(0)" onclick="SEConfirmReviewerInvitation(' . (int) $pDocumentId . ', ' . $pInvitationId . ', ' . (int) $pReviewerId . ', \'' . getstr('pjs.SE_accept_reviewer_invitation_confirmation') . '\')"><img title="'.getstr('pjs.tooltips.accept_review_request').'" src="../i/review_accepted.png"></img></a></span>
					<span class="reviewer_act"><a href="javascript:void(0)" onclick="SECancelReviewerInvitation(' . (int) $pDocumentId . ', ' . $pInvitationId . ', ' . (int) $pReviewerId . ', \'' . getstr('pjs.SE_cancel_reviewer_invitation_confirmation') . '\')"><img title="'.getstr('pjs.tooltips.cancel_review_request').'" src="../i/remove_reviewer.png"></img></a></span>
				';
				break;
			case REVIEWER_CONFIRMED_STATE : // /view_version.php?version_id=1003&id=335&view_role=3&round=2&round_user_id=695!!!!!!!!!!!!!!!!!!!!!!!!!?version_id=1003&id='
			                               // . $pDocumentId . '

				$lRes .= '
					<span class="reviewer_act"><a href="javascript:void(0)" onclick="SECancelReviewerInvitation(' . (int) $pDocumentId . ', ' . $pInvitationId . ', ' . (int) $pReviewerId . ', \'' . getstr('pjs.SE_cancel_reviewer_review_confirmation') . '\')"><img title="'.getstr('pjs.tooltips.cancel_review').'" src="../i/remove_reviewer.png"></img></a></span>
				';

			/* 	$lRes .= '
					<span class="reviewer_act"><a href="javascript:openPopUp(\'/view_version.php?version_id=' . $pReviewerVersionId . '&id=' . $pDocumentId . '&view_role=' . DEDICATED_REVIEWER_ROLE . '&round=' . $pRoundNumber . '&round_user_id=' . $pReviewerId . '&invitation_id=' . $pInvitationId . '\')"><img title="'.getstr('pjs.tooltips.SE_reviewing_as_reviewer').'" src="../i/reviewing.png"></img></a></span>
					<span class="reviewer_act"><a href="javascript:void(0)" onclick="SECancelReviewerInvitation(' . (int) $pDocumentId . ', ' . $pInvitationId . ', ' . (int) $pReviewerId . ', \'' . getstr('pjs.SE_cancel_reviewer_review_confirmation') . '\')"><img title="'.getstr('pjs.tooltips.cancel_review').'" src="../i/remove_reviewer.png"></img></a></span>
				'; */

				$lToday = strtotime(date("d/m/Y"));
				$lAssignedDate = strtotime($PreviewersAssignmentDuedate);
				if($lAssignedDate){
					if($lAssignedDate < $lToday){
						$lRes .= '<span class="reviewer_act"><img src="../i/reviewer_notify.png"></img></span>';
					}
				}

				break;
			case REVIEWER_CANCELLED_STATE :
			case REVIEWER_TIMEDOUT_STATE :
				$lRes .= '
				 	<span class="reviewer_act">
					 	<a href="javascript:void(0)" onclick="ReInviteDocumentReviewer(' . $pDocumentId . ', ' . (int) $pDocUsrId . ', ' . $pRoundId . ', \'' . getstr('pjs.SE_reinvite_reviewer_confirmation') . '\')">
							<img title="'.getstr('pjs.tooltips.reinvite_reviewer').'" src="../i/in_review.png"></img>
						</a>
					</span>
				';
				break;
			case REVIEWER_CANCELLED_BY_SE_STATE :
				$lRes .= '
					<span class="reviewer_act">
						<a href="javascript:void(0)" onclick="ReInviteDocumentReviewer(' . $pDocumentId . ', ' . (int) $pDocUsrId . ', ' . $pRoundId . ', \'' . getstr('pjs.SE_reinvite_reviewer_confirmation') . '\')">
							<img title="'.getstr('pjs.tooltips.reinvite_reviewer').'" src="../i/in_review.png"></img>
						</a>
					</span>
				';
				break;
			default :
				break;
		}

	}else{
		$lRes .= '<span class="reviewer_act"><a href="javascript:void(0)" onclick="ReInviteDocumentReviewer(' . $pDocumentId . ', ' . (int) $pDocUsrId . ', ' . $pRoundId . ', \'' . getstr('pjs.SE_reinvite_reviewer_confirmation') . '\')"><img title="'.getstr('pjs.tooltips.reinvite_reviewer').'" src="../i/in_review.png"></img></a></span>';
	}
	return $lRes;
}

function createHtmlEditorBase($pTextareaId) {
	global $docroot;

	return '<script type="text/javascript">
		CKEDITOR.config.language = \'en\';
		var instance = CKEDITOR.instances[\'' . $pTextareaId . '\'];
		if(instance){
			instance.destroy(true);
		}
		CKEDITOR.replace( "' . $pTextareaId . '",
		{
			removePlugins: \'elementspath\',
		});
	</script>';

}
function createHtmlEditor($pTextareaId, $pHeight = EDITOR_DEFAULT_HEIGHT, $pWidth = 0, $pToolbarName = EDITOR_FULL_TOOLBAR_NAME, $pUseCommonToolbar = 0, $pCommonToolbarHolderId = '') {
	global $docroot;

	return '<script type="text/javascript">
		CKEDITOR.config.contentsCss = \'editor_iframe1.css\' ;
		CKEDITOR.config.language = \'en\';
		var instance = CKEDITOR.instances[\'' . $pTextareaId . '_textarea\'];
		if(instance){
			instance.destroy(true);
		}

		$( \'#' . $pTextareaId . '_textarea\' ).ckeditor(function(){
				fixEditorMaximizeBtn(this);
			}, {
			extraPlugins : \'figs,tbls,refs,autosave\',
			skin : \'office2003\',
			toolbar : \'' . $pToolbarName . '\',
			removePlugins: \'elementspath\',
			height: ' . (int) $pHeight . '
			' . ($pWidth > 0 ? (', width: ' . (int) $pWidth) : '') . ($pUseCommonToolbar ? (',
				sharedSpaces : {
					top: \'' . $pCommonToolbarHolderId . '\'
				}') : '') . '

		});

	</script>';

}

function addSpace($pPos) {
	$lRet = '';
	$lSpace = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	$lCountSymbols = strlen($pPos) / 2;
	for($i = 1; $i < $lCountSymbols; $i ++){
		$lRet .= $lSpace;
	}
	return $lRet;
}

function getBrowseRowClass($pPos, $pShowMode, $pCurrentRowNum) {
	$lCountSymbols = strlen($pPos);
	if((int) $pShowMode && (int) $pCurrentRowNum == 1)
		return ' storyRowTitle ';
	elseif($lCountSymbols == 2)
		return ' browseRootRow ';
	return ' browseRow ';
}

function parseUrls($txt) {
	$parsed = '';
	$lines = preg_split('/<br \/>/', $txt);
	foreach($lines as $line){
		$parsedLine = '';
		if($line){
			$words = preg_split('/[\ ]/', $line);
			foreach($words as $word){
				$w = parseWordUrls($word);
				$parsedLine .= $w . ' ';
			}
		}
		$parsed .= trim($parsedLine) . '<br />';
	}
	return $parsed;
}

function parseSpecialQuotes($str) {
	return str_replace(array(
		'&bdquo;',
		'„',
		'“',
		'”',
		'&laquo;',
		'&raquo;',
		'&ldquo;',
		'&rdquo;'
	), array(
		'"',
		'"',
		'"',
		'"',
		'"',
		'"',
		'"',
		'"'
	), $str);
}

function parseWordUrls($str) {
	$delim = '[\(\)\[\]\{\}\<\>\"\'\/\\\\.,;?!]*';
	$left_delim = $delim; // = '[\(\[\{\<\"\']*';
	$right_delim = $delim; // = '[\)\]\}\>\"\']*';
	$url_pattern = '/^' . $left_delim . '((http|https|ftp|gopher|news):\/\/|www\.)/';
	$email_pattern = '/^' . $left_delim . '[A-Za-z0-9._-]+@[A-Za-z0-9.-]+\.(?:[A-Za-z]{2}|com|org|net|biz|info|name|aero|jobs|museum)' . $right_delim . '$/';

	if(preg_match($url_pattern, $str)){
		$address = preg_replace('/^' . $left_delim . '(http:\/\/)?(www\.)?(.+?)' . $right_delim . '$/', 'http://${2}${3}', $str);
		$addr_orig = preg_replace('/^' . $left_delim . '(.+?)' . $right_delim . '$/', '${1}${2}', $str);
		$link = '<a href="' . trim($address) . '" target="_blank" >' . $addr_orig . '</a>';
		$res = preg_replace('/^(' . $left_delim . ')(.+?)(' . $right_delim . ')$/', '${1}' . $link . '${3}', $str);
	}elseif(preg_match($email_pattern, $str)){
		$address = preg_replace('/^' . $left_delim . '(.+?)' . $right_delim . '$/', '${1}', $str);
		$link = '<a href="mailto:' . trim($address) . '">' . $address . '</a>';
		$res = preg_replace('/^(' . $left_delim . ')(.+?)(' . $right_delim . ')$/', '${1}' . $link . '${3}', $str);
	}else{
		$res = $str;
	}
	return $res;
}

function hideStoryTitle($pHideTitle) {
	if((int) $pHideTitle){
		return ' P-Hidden ';
	}
	return '';
}

function showSEAddEvent($pDocumentId, $pId, $pAssignedSEUid, $pJournalId) {
	if($pAssignedSEUid){
		return 'Assigned';
	}else{
		return "<a href='javascript:DocumentAddSe($pDocumentId, $pId , $pJournalId);'>Assign</a>";
	}
}

function showCEAddEvent($pDocumentId, $pId, $pAssignedCEUid, $pCurrentRoundId) {
	if($pAssignedCEUid == $pId){
		return 'Assigned';
	}else{
		return '<a href="#" onclick="DocumentAddCE(' . $pDocumentId . ', ' . $pId . ', ' . $pCurrentRoundId . ');">Assign</a>';
	}
}

function showLEAddEvent($pDocumentId, $pId, $pAssignedLEUid) {
	if($pAssignedLEUid){
		return 'Assigned';
	}else{
		return '<a href="#" onclick="DocumentAddLE(' . $pDocumentId . ', ' . $pId . ');">Assign</a>';
	}
}

function checkFilterLetter($pActiveClass, $pLetter) {

	if(strtolower($pLetter) == strtolower($_REQUEST['letter'])){
		return $pActiveClass;
	}

	return '';
}
function CanInvitePanel($pRound, $pReview_type) {
	return ($pRound == 1 && ($pReview_type != DOCUMENT_CLOSED_PEER_REVIEW));
}
function ReviewerOptionsHeader($pRound, $pReview_type) {
	if(CanInvitePanel($pRound, $pReview_type))
		return '<th>' . getstr('pjs.reviewers_panel_label') . '</th>';
}
function ReviewerOptions($pRole_id, $pReviewer_id, $pRound, $pReview_type, $pDue_date) {
	$columns = '		 <td align="center"><input type="radio" name="' . $pReviewer_id . '"  value="n" '. ($pRole_id == DEDICATED_REVIEWER_ROLE ? 'checked="checked"' : '') . ' /></td>';
	if(CanInvitePanel($pRound, $pReview_type)){

		if(isset($pDue_date) && $pRole_id == COMMUNITY_REVIEWER_ROLE){
			$columns .= '<td align="center">invited</td>';
		}
		else {
			$columns .= '<td align="center"><input type="radio" name="' . $pReviewer_id . '"  value="p" '. ($pRole_id == COMMUNITY_REVIEWER_ROLE ? 'checked="checked"' : '') . ' /></td>';
		}

	}
	return $columns;
}

function showInvitationCheckBoxName($pInvited) {
	if($pInvited){
		return 'already_invited[]';
	}else{
		return 'nominate[]';
	}
}

function showInvitationCheckBoxDisable($pInvited) {
	if($pInvited){
		return 'disabled="disabled" checked="checked"';
	}else{
		return '';
	}
}

function GetArrayKeyValue($pArray, $pKey) {
	return $pArray[$pKey];
}

function getPrevIssueBtn($pJournalId, $pIssueId) {
	if((int) $pIssueId){
		$lRet = '<a href="/browse_journal_issue_documents.php?journal_id=' . $pJournalId . '&amp;issue_id=' . $pIssueId . '">
					&laquo; Previous
				</a>';
	}else{
		$lRet = '<a href="javascript: void(0);" class="notActive">
					&laquo; Previous
				</a>';
	}
	return $lRet;
}

function getNextIssueBtn($pJournalId, $pIssueId) {
	if((int) $pIssueId){
		$lRet = '<a href="/browse_journal_issue_documents.php?journal_id=' . $pJournalId . '&amp;issue_id=' . $pIssueId . '">
					Next &raquo;
				</a>';
	}else{
		$lRet = '<a href="javascript: void(0);" class="notActive">
					Next &raquo;
				</a>';
	}
	return $lRet;
}

function displayOriginalPic($pPicId) {
	if((int) $pPicId)
		return '<img src="/showimg.php?filename=oo_' . $pPicId . '.jpg"></img>';
}

function intThis($pParam) {
	return (int) $pParam;
}

function getIssueYear($pYear) {
	if((int) $pYear)
		return '(' . (int) $pYear . ')';
}

function getChangeStateBtn($pIssueState, $pJournalId, $pIssueId, $pBackIssue, $pIsCurrent) {
	if((int) $pIssueState){
		if(! $pIsCurrent)
			return '<a href="/edit_journal_issue.php?journal_id=' . (int) $pJournalId . '&amp;issue_id=' . (int) $pIssueId . '&amp;back_issue=' . (int) $pBackIssue . '&amp;tAction=makecurrent">' . getstr('pjs.make_current') . '</a>';
	}else{
		return '<a href="/edit_journal_issue.php?journal_id=' . (int) $pJournalId . '&amp;issue_id=' . (int) $pIssueId . '&amp;back_issue=' . (int) $pBackIssue . '&amp;tAction=changestate">' . getstr('pjs.publish_issue') . '</a>';
	}
}

function hideIfFirst($pRowNum) {
	if((int) $pRowNum == 1)
		return ' P-Hidden ';
}

function hideIfLast($pRowNum, $pRowsCount) {
	if((int) $pRowNum == (int) $pRowsCount)
		return ' P-Hidden ';
}

function getJournalFeaturesLinks($pJournalId, $pStoryId, $pTitle, $pType) {
	if(! (int) $pType){
		return '<a class="link" href="/about#' . strip_invalid($pTitle) . '"><span></span><span class="content">' . $pTitle . '</span></a>';
	}elseif((int)$pType == 1){
		return '<a class="link" href="/browse_journal_special_issues.php?journal_id=' . (int)$pJournalId . '"><span></span><span class="content">' . getstr($pTitle) . '</span></a>';
	}elseif((int)$pType == 2){
		return '<a class="link" href="/browse_journal_articles.php?preview=1&journal_id=' . (int)$pJournalId . '"><span></span><span class="content">' . getstr($pTitle) . '</span></a>';
	}
}

function getIssueEditors($pEditors) {
	if($pEditors){
		return getstr('pjs.editors') . ' ' . $pEditors;
	}
}
function getSubjectCategories($pCategories) {

	if($pCategories){
		return '' . getstr('pjs.subject') . ' - ' . $pCategories . '';
	}
}

function getTaxonCategories($pCategories) {
	if($pCategories){
		return '' . getstr('pjs.taxon') . ' - ' . $pCategories . '';
	}
}

function getChronoCategories($pCategories) {
	if($pCategories){
		return '' . getstr('pjs.chronological') . ' - ' . $pCategories . '';
	}
}

function getGeoCategories($pCategories) {
	if($pCategories){
		return '' . getstr('pjs.geographical') . ' - ' . $pCategories . '';
	}
}

function showReviewRoundDelimiters($pReviewRound) {

	if($pReviewRound == 1){
		return '<div class="document_info_row_border_line"></div>';
	}

	return '';
}

function isChecked($pChecked = 0) {
	if((int) $pChecked)
		return ' checked="checked" ';
	else
		return '';
}

function getUserExpertisesLink($pJournalId, $pUserId, $pIsSe) {
	if((int) $pIsSe)
		return '<a href="/user_journal_expertises?user_id=' . (int) $pUserId . '&amp;tAction=showedit">' . getstr('pjs.expertises') . '</a>';
}
function getSearchSelectItems($pDocumentId) {
	$lRet = '';

	if((int) $pDocumentId){
		$lRet .= '
			<option selected="selected" value="' . SEARCH_IN_ARTICLE . '">current article</option>
			<option value="2">all articles</option>
			';
	}else{
		$lRet .= '
			<option selected="selected" value="' . SEARCH_IN_ALL_ARTICLES . '">all articles</option>
			';
	}

	return $lRet;
}
function getSearchStr($pSearchStr) {
	if($pSearchStr == '' || $pSearchStr == 'search_str'){
		return getstr('pwt.defaultSearchLabel');
	}else
		return $pSearchStr;
}
function showProfilePic() {
	global $user;
	if($user->photo_id)
		return '<img border="0" alt="profile picture" src="/showimg.php?filename=c30x30y_' . (int)$user->photo_id . '.jpg" />';
	return '<img src="./i/user_no_img.png" alt="No image" />';
}
function getMatchingIssuesCount($pRecordsCnt) {
	if((int) $pRecordsCnt == 1)
		return 1 . ' ' . getstr('pjs.issue_matching_your_criteria') . ':';
	else
		return '1-' . (int) $pRecordsCnt . ' ' . getstr('pjs.issues_matching_your_criteria') . ':';
}

function getSpecialIssueTxt($pIsSpecialIssue) {
	if((int) $pIsSpecialIssue)
		return ': ' . getstr('pjs.special_issue');
}

function getCategoriesAndCount($pCatNames, $pCatCnt) {
	$lRet = '';
	if($pCatNames){
		$lNamesArr = explode(',', $pCatNames);
		$lCntArr = explode(',', $pCatCnt);
		for($i = 0; $i < count($lNamesArr); $i ++){
			$lRet .= $lNamesArr[$i] . ' ' . '<span>(' . $lCntArr[$i] . ')</span>, ';
		}
		return substr($lRet, 0, - 2);
	}
}

function getLastFiveYears($pJournalId, $pSpecial, $pCntYears) {
	$lRet = '';
	$lYear = (int) date('Y');
	for($i = 0; $i < (int) $pCntYears; $i ++){
		$lRet .= ' <span style="color: #b0ada2;">|</span> ' . '<a href="/browse_journal_issues.php?journal_id=' . (int) $pJournalId . '&year=' . ($lYear - $i) . '&special_issues=' . (int) $pSpecial . '" class="green">' . ($lYear - $i) . '</a>';
	}
	return $lRet;
}

function getLetters($pJournalId, $pAffiliationId) {
	$lRet = '';
	foreach(range('A', 'Z') as $letter){
		$lRet .= '<a class="green letter" href="javascript: filterAuthorsLetter(' . $pAffiliationId . ', ' . (int) $pJournalId . ', \'' . $letter . '\')">' . $letter . '</a>';
	}
	return $lRet;
}

function getUsersLetters($pJournalId, $pGrpId, $pRoleId) {
	$lRet = '';
	foreach(range('A', 'Z') as $letter){
		$lRet .= '<a class="green letter" href="javascript: filterUsersLetter(' . (int) $pJournalId . ', \'' . $letter . '\', ' . (int)$pGrpId . ', ' . (int)$pRoleId . ')">' . $letter . '</a>';
	}
	return $lRet;
}

function showAssignmentSEDueDate($pSEName, $pRoundDueDate, $pDocumentId, $pRoundId, $pRoundUserId, $pRoleId) {
	preg_match('/(\d+)[-–\/](\d+)[-–\/](\d+) (\d+):(\d+):(\d+)/', $pRoundDueDate, $lMatch);

	$lNowDate = strtotime(date("Y-m-d"));
	$lRoundDate = strtotime($lMatch[3] . '-' . $lMatch[2] . '-' . $lMatch[1]);

	if($lNowDate <= $lRoundDate){
		$lDueDateFlag = 1;
		$lSecDiff = $lRoundDate - $lNowDate;
	}else{
		$lDueDateFlag = 2;
		$lSecDiff = $lNowDate - $lRoundDate;
	}

	$lDaysDiff = floor($lSecDiff / 3600 / 24);

	if($lDueDateFlag == 1){
		return '
			<div class="document_author_holder_content_no_review_yet">
				<div class="document_author_holder_content_no_review_yet_top">
					<table width="100%" cellspacing="0" cellpadding="0">
						<tbody><tr>
							<td width="5"><div class="yellow-top-left"></div></td>
							<td><div class="yellow-top-middle"></div></td>
							<td width="5"><div class="yellow-top-right"></div></td>
						</tr>
					</tbody></table>
				</div>

				<div class="document_author_holder_content_no_review_yet_middle">
					<table cellpadding="0" cellspacing="0" width="100%">
						<colgroup>
							<col width="25%"></col>
							<col width="50%"></col>
							<col width="25%"></col>
						</colgroup>
						<tr>
							<td align="left">
								<span class="subj_editor_name_class_1">' . $pSEName . '</div>
							</td>
							<td align="center">
								<span class="se_due_date_txt se_due_date_txt_green">' . sprintf(getstr('pjs.submission_review_assignment'), $lDaysDiff) . '</span>
								<img title="'.getstr('pjs.tooltips.change_due_date').'" class="pointer" onclick="openDueDatePopUp(\'/updateduedate.php?action=reviewers_assignment&roundduedate=' . $pRoundDueDate . '&roundid=' . $pRoundId . '&rounduserid=' . $pRoundUserId . '\', 400, 200)" src="../i/edit.png" />
							</td>
							<td align="right">
								<a href="view_document.php?id=' . $pDocumentId . '&view_role=3&mode=1' . ($pRoleId == JOURNAL_EDITOR_ROLE ? '&e_redirect=1' : '') . '">Invite Reviewers</a>
							</td>
						</tr>
					</table>
				</div>
				<div class="document_author_holder_content_no_review_yet_bottom">
					<table width="100%" cellspacing="0" cellpadding="0">
						<tbody><tr>
							<td width="5"><div class="yellow-bottom-left"></div></td>
							<td><div class="yellow-bottom-middle"></div></td>
							<td width="5"><div class="yellow-bottom-right"></div></td>
						</tr>
					</tbody></table>
				</div>
			</div>
		';
	}else{
		return '
			<div class="document_author_holder_content_no_review_yet document_author_holder_content_no_review_yet2">
				<div class="document_author_holder_content_no_review_yet_top">
					<table width="100%" cellspacing="0" cellpadding="0">
						<tbody><tr>
							<td width="5"><div class="yellow-top-left"></div></td>
							<td><div class="yellow-top-middle"></div></td>
							<td width="5"><div class="yellow-top-right"></div></td>
						</tr>
					</tbody></table>
				</div>
				<div class="document_author_holder_content_no_review_yet_middle">
					<table width="100%" cellspacing="0" cellpadding="0">
						<colgroup>
							<col width="25%">
							<col width="50%">
							<col width="25%">
						</colgroup>
						<tbody><tr>
							<td align="left">
								<span class="subj_editor_name_class_1">' . $pSEName . '</div>
							</td>
							<td align="center">
								<span class="se_due_date_txt">Review assignments are ' . $lDaysDiff . ' day'.(abs($lDaysDiff)>1?'s':'').' late</span> &nbsp;<img class="pointer" onclick="openDueDatePopUp(\'/updateduedate.php?action=reviewers_assignment&roundduedate=' . $pRoundDueDate . '&roundid=' . $pRoundId . '&rounduserid=' . $pRoundUserId . '\', 400, 200)" src="../i/edit.png" src="../i/edit.png"/>
							</td>
							<td align="right">
								<a href="view_document.php?id=' . $pDocumentId . '&view_role=3&mode=1' . ($pRoleId == JOURNAL_EDITOR_ROLE ? '&e_redirect=1' : '') . '">Invite Reviewers</a>
							</td>
						</tr>
					</tbody></table>
				</div>
				<div class="document_author_holder_content_no_review_yet_bottom">
					<table width="100%" cellspacing="0" cellpadding="0">
						<tbody><tr>
							<td width="5"><div class="yellow-bottom-left"></div></td>
							<td><div class="yellow-bottom-middle"></div></td>
							<td width="5"><div class="yellow-bottom-right"></div></td>
						</tr>
					</tbody></table>
				</div>
			</div>
		';
	}

}

function showRejectSEButtons($pRoundNumber, $pUserId, $pDocumentId) {
	if($pRoundNumber == REVIEW_ROUND_ONE){
		return '
			<div class="subm_reject_or_holder">
				<table cellpadding="0" cellspacing="0" width="100%">
					<colgroup>
						<col width="45%"></col>
						<col width="10%"></col>
						<col width="45%"></col>
					</colgroup>
					<tr>
						<td><div class="or_line"></div></td>
						<td align="center"><span class="or_text">OR</span></td>
						<td><div class="or_line"></div></td>
					</tr>
				</table>
			</div>
			<div class="subm_reject_reasons_txt">
				<span class="yellow-green-txt">' . getstr('pjs.submission_se_reject_text_buttons') . '</span>
			</div>
			<div class="subm_textarea_holder subm_textarea_holder_E">
				<div class="subm_textarea_holder_top">
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="4">
								<div class="subm_textarea_holder_top_left"></div>
							</td>
							<td>
								<div class="subm_textarea_holder_top_middle"></div>
							</td>
							<td width="4">
								<div class="subm_textarea_holder_top_right"></div>
							</td>
						</tr>
					</table>
				</div>
				<div class="subm_textarea_holder_middle">
					<textarea onkeyup="ChangeRejectBtn(this, \'btn_rej_1\', \'btn_rej_2\', \'btn_rej_1_active\', \'btn_rej_2_active\')" name="notes_reject" id="ed_notes_reject"></textarea>
				</div>
				<div class="subm_textarea_holder">
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="4">
								<div class="subm_textarea_holder_top_left subm_textarea_holder_bottom_left"></div>
							</td>
							<td>
								<div class="subm_textarea_holder_top_middle subm_textarea_holder_bottom_middle"></div>
							</td>
							<td width="4">
								<div class="subm_textarea_holder_top_right subm_textarea_holder_bottom_right"></div>
							</td>
						</tr>
					</table>
				</div>
				<div class="document_btn_actions_editor_holder">
					<table cellpadding="0" cellspacing="0" width=100%>
						<colgroup>
							<col width="50%"></col>
							<col width="50%"></col>
						</colgroup>
						<tr>
							<td align="right">
								<div style="margin-left: -30px; margin-left: 125px; width: 300px; float: left;">
									<div id="btn_rej_1_active" class="btn_rej_1_active" style="display:none" onclick="SaveEditorDecision(' . $pUserId . ', ' . ROUND_DECISION_REJECT . ', ' . $pDocumentId . ')">
										<div class="btnContentHolder">' . getstr('pjs.reject') . '</div>
									</div>
									<div id="btn_rej_1"  class="invite_reviewer_btn invite_reviewer_btn_E invite_reviewer_btn_E_first">
										<div class="rejBtnMid" style="width: 158px;">
											' . getstr('pjs.reject') . '
										</div>
									</div>
								</div>
							</td>
							<td align="left">
								<div style="margin-left: -50px; margin-left: -60px; width: 300px; float: left;">
									<div id="btn_rej_2_active" class="btn_rej_2_active" style="display:none" onclick="SaveEditorDecision(' . $pUserId . ', ' . ROUND_DECISION_REJECT_BUT_RESUBMISSION . ', ' . $pDocumentId . ')">
										<div class="btnContentHolder">' . getstr('pjs.reject.but') . '</div>
									</div>
									<div id="btn_rej_2" class="invite_reviewer_btn invite_reviewer_btn_E invite_reviewer_btn_E_sec">
										<div class="rejBtnMid" style="width: 300px;">
											' . getstr('pjs.reject.but') . '
										</div>
									</div>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>
		';
	}

	return '';
}

function getUserPictureIfExist($pPreviewPicId) {
	$lRet = '';

	if((int) $pPreviewPicId){
		$lRet = '<div class="Prof-Photo">
					<img class="P-Prof-Pic" width="67" height="70" src="/showimg.php?filename=c67x70y_' . (int) $pPreviewPicId . '.jpg" alt="Profile picture" />
					<div class="P-Clear"></div>
				</div>';
	}else{
		$lRet = '<div class="Prof-Photo">
					<img src="/i/no_photo.png" width="67" height="70" alt="Profile picture" />
					<div class="P-Clear"></div>
				</div>';
	}
	return $lRet;
}

function getYourTasksBtn($pShow, $pJournalId) {
	if((int) $pShow){
		return '<button class="button_red button_tasks" onclick="window.location = \'/dashboard.php?view_mode=5&amp;journal_id=' . (int) $pJournalId . '\'"><span>' . getstr('pjs.your_tasks') . '</span></button>';
	}
}

function checkReviewRoundDate($pRoundDueDate, $pRoundId, $pRoundUserId) {
	$lDueDateArr = CheckDueDateDays($pRoundDueDate);
	if($lDueDateArr['flag'] == 2){
		return '<span class="red_txt_due_date">Editorial decision is ' . $lDueDateArr['datediff_days'] . ' day'.(abs($lDueDateArr['datediff_days'])>1?'s':'').' late</span>
					<img title="'.getstr('pjs.tooltips.change_due_date').'" id="duedate_editimg" width="18" height="16" style="cursor:pointer;" class="ui-datepicker-trigger pointer"
					onclick="openDueDatePopUp(\'/updateduedate.php?action=user_decision&roundduedate=' . $pRoundDueDate . '&roundid=' . $pRoundId . '&rounduserid=' . $pRoundUserId . '\', 400, 200)"
					src="../i/edit.png"></img>';
	}else{
		return '<span class="green_txt_due_date">Editorial decision is due in ' . $lDueDateArr['datediff_days'] . ' day'.(abs($lDueDateArr['datediff_days'])>1?'s':'').'</span>
		<img title="'.getstr('pjs.tooltips.change_due_date').'" id="duedate_editimg" width="18" height="16" style="cursor:pointer;" class="ui-datepicker-trigger pointer"
					onclick="openDueDatePopUp(\'/updateduedate.php?action=user_decision&roundduedate=' . $pRoundDueDate . '&roundid=' . $pRoundId . '&rounduserid=' . $pRoundUserId . '\', 400, 200)"
					src="../i/edit.png"></img>';
	}

}

function checkReviewRoundDateLinks($pRoundDueDate, $pUserVersionId, $pRole, $pRoundId, $pDocumentId, $pRoundUserId, $pCurrentRoundId, $pCheckInvited = 1, $pMergeFlag = 1, $pCanInviteReviewers = 'false', $pDocumentReviewTypeId = 0) {
	$lDueDateArr = CheckDueDateDays($pRoundDueDate);
	if($lDueDateArr['flag'] == 2){
		return '<a href="#">Send reminder</a> | <a href="#">Editorial decision</a>';
	}elseif($pRoundId == 2){
		$lUrl = '/view_version.php?version_id=' . $pUserVersionId . '&id=' . $pDocumentId . '&view_role=' . $pRole . '&round=' . $pRoundId . '&round_user_id=' . $pRoundUserId . '&duedate=' . $lDueDateArr["datediff_days"];
		$lReviewersUrl = '/view_document.php?id=' . $pDocumentId . '&view_role=3&mode=1';

		return (($pCanInviteReviewers != 'true' && $pCanInviteReviewers != 't') ? '<a style="cursor: pointer" onclick="window.location.href=\'' . $lReviewersUrl . '\'">Invite reviewers</a> |' : '') . '
				<a style="cursor: pointer" onclick="' . ((int) $pCheckInvited && $pDocumentReviewTypeId != DOCUMENT_NON_PEER_REVIEW ? 'checkReviewersState(' . $pCurrentRoundId . ', \'' . $lUrl . '\', ' . (int) $pDocumentId . ', ' . (int) $pMergeFlag . ', \'' . $pCanInviteReviewers . '\')' : 'openPopUp(\'' . $lUrl . '\', 0, 0, \'window_' . $pUserVersionId . '\')') . '">Editorial decision</a>';
	}else{
		$lUrl = '/view_version.php?version_id=' . $pUserVersionId . '&id=' . $pDocumentId . '&view_role=' . $pRole . '&round=' . $pRoundId . '&round_user_id=' . $pRoundUserId . '&duedate=' . $lDueDateArr["datediff_days"];
		// ~ return '<a style="cursor: pointer"
		// onclick="confirmDocumentVersionsMergeEditor(' . $pCurrentRoundId . ',
		// \'' . $lUrl . '\', ' . (int)$pDocumentId . ')">Editorial
		// decision</a>';
		return '<a style="cursor: pointer" onclick="' . ((int) $pCheckInvited ? 'checkReviewersState(' . $pCurrentRoundId . ', \'' . $lUrl . '\', ' . (int) $pDocumentId . ', ' . (int) $pMergeFlag . ', \'' . $pCanInviteReviewers . '\')' : 'openPopUp(\'' . $lUrl . '\', 0, 0, \'window_' . $pUserVersionId . '\')') . '">Editorial decision</a>';
		// ~ return '<a href="javascript:openPopUp(\''. $lUrl .'\');">Editorial
	// decision</a>';
		// ~ return '<a target="_blank" href="/view_version.php?version_id=' .
	// $pUserVersionId . '">Editorial decision</a>';
	}

}

function returnFormStaticField($pFieldValue) {
	if($pFieldValue){
		return $pFieldValue;
	}
	return '';
}

function showViewDocumentActiveSectionTab($pActiveTab, $pTab) {
	return ((int) $pActiveTab == (int) substr($pTab, 1) ? 'viewdoc_activetab' : '');
}

function showCoAuthorCheck($pCoAuthor) {
	if((int) $pCoAuthor){
		return '<img src="../i/review_accepted.png" />';
	}
	return '';
}

function showRoundVersionAndInfo($pRoundTypeId, $pVersionNum, $pRoundNumber) {
	if(in_array($pRoundTypeId, array(
		R_ROUND_TYPE,
		LE_ROUND_TYPE,
		CE_ROUND_TYPE
	))){
		return '
			<div class="document_author_holder_rev_info_top">
				<div class="document_author_holder_rev_info_top_left">
					' . (($pRoundNumber == REVIEW_ROUND_ONE && ! in_array($pRoundTypeId, array(
			LE_ROUND_TYPE,
			CE_ROUND_TYPE
		))) ? 'Author\'s original submission' : 'Authors revision') . ' (Version ' . $pVersionNum . ')
				</div>
				<div class="document_author_holder_rev_info_top_right">
					<img src="../i/eye.png">
					<a href="#">' . ($pRoundTypeId == LE_ROUND_TYPE ? 'Download as PDF' : 'View manuscript') . '</a>
				</div>
				<div class="P-Clear"></div>
			</div>
		';
	}
}

function checkRoundLabelHistory($pRoundTypeId, $pRoundName, $pRoundNumber, $pStateId) {
	if($pRoundTypeId == LE_ROUND_TYPE){
		return 'Proof reading';
	}else{
		return showRoundNumberInfo($pRoundTypeId, $pRoundName, $pRoundNumber, $pStateId);
	}
}

function checkSERoundLabel($pRoundTypeId, $pRoundName, $pRoundNumber, $pStateId) {
	if($pStateId == DOCUMENT_WAITING_AUTHOR_VERSION_AFTER_REVIEW_STATE){
		return $pRoundName . ' round ' . ($pRoundNumber + 1);
	}else{
		return showRoundNumberInfo($pRoundTypeId, $pRoundName, $pRoundNumber, $pStateId);
	}
}

function showSERoundNumberInfo($pRoundTypeId, $pRoundName, $pRoundNumber, $pStateId, $pAcceptedRoundNum, $pCERoundsCount = 0) {
	if($pStateId == DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_LAYOUT_EDITING_AFTER_COPY_EDITING_STATE){
		return getstr('pjs.round_copy_editing_label');
	}

	if(in_array($pStateId, array(
		DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_LAYOUT_EDITING_STATE,
		DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_COPY_EDITING_STATE
	))){
		if((int)$pCERoundsCount > 0) {
			return getstr('pjs.copyeditinground_label_clear') . ' ' . $pCERoundsCount;
		} else {
			return 'Review round ' . $pAcceptedRoundNum;
		}
	}else{
		return showRoundNumberInfo($pRoundTypeId, $pRoundName, $pRoundNumber, $pStateId);
	}
}

function showAuthorLELabel($pStateId) {
	if($pStateId == DOCUMENT_READY_FOR_LAYOUT_STATE){
		return getstr('pjs.document_ready_for_layout_label_A_txt');
	}else{
		return getstr('pjs.document_in_layout_label_A_txt');
	}
}

function showRoundNumberInfo($pRoundTypeId, $pRoundName, $pRoundNumber, $pStateId) {
	// check for document state in layout

	if(in_array((int) $pStateId, array(
		DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_LAYOUT_EDITING_AFTER_COPY_EDITING_STATE,
		DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_LAYOUT_EDITING_STATE,
		DOCUMENT_READY_FOR_LAYOUT_STATE,
		DOCUMENT_IN_LAYOUT_EDITING_STATE
	))){
		return getstr('pjs.layoutround_label');
	}

	// check for document state in copyediting
	if(in_array($pStateId, array(
		DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_COPY_EDITING_STATE,
		DOCUMENT_READY_FOR_COPY_REVIEW_STATE
	))){
		return getstr('pjs.copyeditinground_label') . ' ' . $pRoundNumber;
	}

	if(! $pRoundTypeId){
		return $pRoundName . ' round ' . $pRoundNumber;
	}

	if($pRoundTypeId == R_ROUND_TYPE){
		return $pRoundName . ' round ' . $pRoundNumber;
	}else{
		return $pRoundName;
	}
}

function showViewersLink($pRoundTypeId) {
	if($pRoundTypeId == R_ROUND_TYPE){
		return '<img src="../i/eye.png"> <a href="#">View reviews</a>';
	}

	if($pRoundTypeId == CE_ROUND_TYPE){
		return '<a href="#">View copyedited version</a>';
	}

	return '&nbsp;';
}

function showBorderLine($pHasAbstractkeyworddata) {
	if((int) $pHasAbstractkeyworddata){
		return '<div class="document_info_row_border_line"></div>';
	}
	return '';
}
function returnFormGetField() {
	return $_GET['fieldName'];
}
function changeMenuStyleToBold($pGrpId, $pRoleId, $pAction) {
	if($pRoleId != 0 || $pAction == "Filter"){

		return '<script type="text/javascript">
			$(\'.siderBlockLinksHolder #subj_editors_link\').css(\'font-weight\', \'bold\');
		</script>';
	}else{
		return '<script type="text/javascript">
				var lHref = $(\'.siderBlockLinksHolder .link\').attr(\'st\');
				$(\'.siderBlockLinksHolder .link\').each(function(){
					if($(this).attr(\'st\') == ' . $pGrpId . ') {
						$(this).css(\'font-weight\', \'bold\');
					}
				});
		</script>';
	}
}

function showCurrentVersionAfterCopyEditing($pVersionNumber, $pStateId) {
	if($pStateId == DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_LAYOUT_EDITING_AFTER_COPY_EDITING_STATE){
		return 'Copy editor version';
	}else{
		return showCurrentVersion($pVersionNumber);
	}
}

function showCurrentAuthorVersion($pVersionNumber, $pVersionId, $pDocumentId) {
	if((int) $pVersionId){
		$lAuthorVersionLinkStart = '<a href="javascript:void(0);" onclick="openPopUp(\'/view_version.php?version_id=' . (int) $pVersionId . '&id=' . (int) $pDocumentId . '&view_role=' . (int) AUTHOR_ROLE . '\', 0, 0, \'window_' . $pVersionId . '\')">';
		return '
				<div class="document_author_holder_rev_info_top">
					<div class="document_author_holder_rev_info_top_left">
						' . showCurrentVersion($pVersionNumber, $lAuthorVersionLinkStart) . '
					</div>
					<div class="document_author_holder_rev_info_top_right">
						<img src="../i/eye.png"/>
						' . $lAuthorVersionLinkStart . getstr('pjs.viewManuscriptText') . '</a>
					</div>
					<div class="P-Clear"></div>
				</div>
				<div class="document_author_holder_line"></div>
		';
	}

	return '';
}

function showCurrentAuthorVersionCERound($pDocumentId, $pCEVersionId) {
	if($pCEVersionId){
		return '
			<div class="document_author_holder_content">
				<div class="doc_holder_reviewer_list" style="padding-top:0px;">
					<table width="100%" cellspacing="0" cellpadding="0">
						<colgroup>
							<col width="33%"></col>
							<col width"33%"></col>
							<col width"34%"></col>
						</colgroup>
						<tbody><tr>
							<td align="left">
								<span class="ed_decision_class_holder">
									Copy editor version
								</span>
							</td>
							<td align="center">&nbsp;</td>
							<td align="right">
								<a href="javascript:void(0);" onclick="openPopUp(\'/view_version.php?version_id=' . (int) $pCEVersionId . '&id=' . (int) $pDocumentId . '&view_role=' . (int) CE_ROLE . '\', 0, 0, \'window_' . $pCEVersionId . '\')">View copyedited version</a>
							</td>
						</tr>
					</tbody></table>
				</div>
			</div>';
	}
}

function showCurrentVersion($pVersionNumber, $pVersionLink) {
	if((int) $pVersionNumber == 1){
		return ($pVersionLink ? $pVersionLink . getstr('pjs.authorOriginalSubmissionVersionLabel') . '</a>' : getstr('pjs.authorOriginalSubmissionVersionLabel')) . ' (Version ' . $pVersionNumber . ')';
	}else{
		return ($pVersionLink ? $pVersionLink . getstr('pjs.authorRevisionVersionLabel') . '</a>' : getstr('pjs.authorRevisionVersionLabel')) . ' (Version ' . $pVersionNumber . ')';
	}
}
function displayErrorIfExist($pError) {
	if($pError){
		return '<span class="errstr">' . $pError . '</span>';
	}
}

function showViewScriptLabel($pStateId) {
	if($pStateId == DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_LAYOUT_EDITING_AFTER_COPY_EDITING_STATE){
		return 'View copyedited version';
	}else{
		return 'View manuscript';
	}
}

function checkAReviewRoundDate($pRoundDueDate, $pRoundId, $pRoundUserId) {
	$lDueDateArr = CheckDueDateDays($pRoundDueDate);

	if($lDueDateArr['flag'] == 2){
		return '<span class="red_txt_due_date">Author\'s revised version is ' . $lDueDateArr['datediff_days'] . ' day'.(abs($lDueDateArr['datediff_days'])>1?'s':'').' late</span>
				<img title="'.getstr('pjs.tooltips.change_due_date').'" id="duedate_editimg" width="18" height="16" style="cursor:pointer;" class="ui-datepicker-trigger pointer"
				onclick="openDueDatePopUp(\'/updateduedate.php?action=user_decision&roundduedate=' . $pRoundDueDate . '&roundid=' . $pRoundId . '&rounduserid=' . $pRoundUserId . '\', 400, 200)"
				src="../i/edit.png"></img>
				';
	}else{
		return '<span class="green_txt_due_date">Author\'s revised version is due in ' . $lDueDateArr['datediff_days'] . ' day'.(abs($lDueDateArr['datediff_days'])>1?'s':'').'
				<img title="'.getstr('pjs.tooltips.change_due_date').'" id="duedate_editimg" width="18" height="16" style="cursor:pointer;" class="ui-datepicker-trigger pointer"
					onclick="openDueDatePopUp(\'/updateduedate.php?action=user_decision&roundduedate=' . $pRoundDueDate . '&roundid=' . $pRoundId . '&rounduserid=' . $pRoundUserId . '\', 400, 200)"
					src="../i/edit.png"></img>
		</span>';
	}
}

function checkAReviewRoundDateReminder($pRoundDueDate) {
	$lDueDateArr = CheckDueDateDays($pRoundDueDate);

	if($lDueDateArr['flag'] == 2){
		return '<a href="#">Send reminder</a>';
	}else{
		return '&nbsp;';
	}

}

function showAuthorCurrentRoundLabel($pStateId, $pCERoundsCount = 0) {
	switch ($pStateId) {
		case (int) DOCUMENT_READY_FOR_COPY_REVIEW_STATE :
			if((int)$pCERoundsCount == 0) {
				return '
					<div class="document_author_review_round_top">
						<div class="document_author_review_round_top_left">' . getstr('pjs.copyeditinground_label_1') . '</div>
						<div class="P-Clear"></div>
					</div>';
			}
			break;
		case (int) DOCUMENT_IN_COPY_REVIEW_STATE:
			return '
				<div class="document_author_review_round_top">
					<div class="document_author_review_round_top_left">' . getstr('pjs.copyeditinground_label_clear') . ' ' . ($pCERoundsCount + 1) . '</div>
					<div class="P-Clear"></div>
				</div>';
		default:
			return '';
			break;
	}
}

function showEditorCurrentRoundLabel($pStateId, $pCERoundsCount = 0) {

	switch ($pStateId) {
		case DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_COPY_EDITING_STATE:
		case DOCUMENT_READY_FOR_COPY_REVIEW_STATE:
			if((int)$pCERoundsCount == 0) {
				return '
					<div class="document_author_review_round_top">
						<div class="document_author_review_round_top_left">' . getstr('pjs.copyeditinground_label_1') . '</div>
						<div class="P-Clear"></div>
					</div>';
			}
			break;
		case DOCUMENT_IN_COPY_REVIEW_STATE:
			return '
				<div class="document_author_review_round_top">
					<div class="document_author_review_round_top_left">' . getstr('pjs.copyeditinground_label_clear') . ' ' . ($pCERoundsCount + 1) . '</div>
					<div class="P-Clear"></div>
				</div>';
		default:
			return '';
			break;
	}

	return '';
}

function ShowRoundNameByDocumentState($pStateId, $pCERoundsCount = 0) {
	if(in_array($pStateId, array(
		DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_LAYOUT_EDITING_AFTER_COPY_EDITING_STATE,
		DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_LAYOUT_EDITING_STATE
	))){
		return getstr('pjs.layoutround_label');
	}

	if(in_array($pStateId, array(
		DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_COPY_EDITING_STATE
	))){
		return getstr('pjs.copyeditinground_label');
	}

	return '';

}

function checkEditorLEAssignDueDate($pRoundDueDate) {
	$lDueDateArr = CheckDueDateDays($pRoundDueDate);

	if($lDueDateArr['flag'] == 2){
		return '<span class="red_txt_due_date">Layout editor assignment is ' . $lDueDateArr['datediff_days'] . ' day'.(abs($lDueDateArr['datediff_days'])>1?'s':'').' late</span>';
	}else{
		return '<span class="green_txt_due_date">Layout editor assignment is due in ' . $lDueDateArr['datediff_days'] . ' day'.(abs($lDueDateArr['datediff_days'])>1?'s':'').'</span>';
	}

}

function CheckDueDateDays($pRoundDueDate, $pFormatType = 'days') {
	$lResArr = array();
	preg_match('/(\d+)[-–\/](\d+)[-–\/](\d+) (\d+):(\d+):(\d+)/', $pRoundDueDate, $lMatch);

	$lNowDate = strtotime(date("Y-m-d"));
	$lRoundDate = strtotime($lMatch[3] . '-' . $lMatch[2] . '-' . $lMatch[1]);

	if($lNowDate <= $lRoundDate){
		$lResArr['flag'] = 1;
		$lResArr['datediff_days'] = $lRoundDate - $lNowDate;
	}else{
		$lResArr['flag'] = 2;
		$lResArr['datediff_days'] = $lNowDate - $lRoundDate;
	}

	switch ($pFormatType) {
		case 'days' :
			$lResArr['datediff_days'] = floor($lResArr['datediff_days'] / 3600 / 24);
			break;
		default :
			break;
	}

	return $lResArr;
}

function checkEditorCEAssignDueDate($pRoundDueDate) {
	$lDueDateArr = CheckDueDateDays($pRoundDueDate);

	if($lDueDateArr['flag'] == 2){
		return '<span class="red_txt_due_date">Copyeditor assignment is ' . $lDueDateArr['datediff_days'] . ' day'.(abs($lDueDateArr['datediff_days'])>1?'s':'').' late</span>';
	}else{
		return '<span class="green_txt_due_date">Copyeditor assignment is due in ' . $lDueDateArr['datediff_days'] . ' day'.(abs($lDueDateArr['datediff_days'])>1?'s':'').'</span>';
	}

}

function checkEditorLEDecisionDueDate($pRoundDueDate, $pRoundId, $pRoundUserId) {
	$lDueDateArr = CheckDueDateDays($pRoundDueDate);

	if($lDueDateArr['flag'] == 2){
		return '<span class="red_txt_due_date">First proof is ' . $lDueDateArr['datediff_days'] . ' day'.(abs($lDueDateArr['datediff_days'])>1?'s':'').' late
		<img title="'.getstr('pjs.tooltips.change_due_date').'" id="duedate_editimg" width="18" height="16" style="cursor:pointer;" class="pointer"
					onclick="openDueDatePopUp(\'/updateduedate.php?action=user_decision&roundduedate=' . $pRoundDueDate . '&roundid=' . $pRoundId . '&rounduserid=' . $pRoundUserId . '\', 400, 200)"
					src="../i/edit.png" />
		</span>';
	}else{
		return '<span class="green_txt_due_date">First proof is due in ' . $lDueDateArr['datediff_days'] . ' day'.(abs($lDueDateArr['datediff_days'])>1?'s':'').'
		<img title="'.getstr('pjs.tooltips.change_due_date').'" id="duedate_editimg" width="18" height="16" style="cursor:pointer;" class="ui-datepicker-trigger pointer"
					onclick="openDueDatePopUp(\'/updateduedate.php?action=user_decision&roundduedate=' . $pRoundDueDate . '&roundid=' . $pRoundId . '&rounduserid=' . $pRoundUserId . '\', 400, 200)"
					src="../i/edit.png" />
		</span>';
	}

}

function showELEDecisionActions($pRoundDueDate, $pDocumentId) {
	$lDueDateArr = CheckDueDateDays($pRoundDueDate);

	if($lDueDateArr['flag'] == 2){
		$lRes .= '<a href="#">Send reminder</a> | ';
	}

	$lRes .= '<a href="/view_document.php?id=' . $pDocumentId . '&view_role=2&mode=1">Change layout editor</a>';
	return $lRes;
}

function checkEditorCEDecisionDueDate($pRoundDueDate, $pRoundId, $pRoundUserId) {
	$lDueDateArr = CheckDueDateDays($pRoundDueDate);

	if($lDueDateArr['flag'] == 2){
		return '<span class="red_txt_due_date">Linguistic editing is ' . $lDueDateArr['datediff_days'] . ' day'.(abs($lDueDateArr['datediff_days'])>1?'s':'').' late
				<img title="'.getstr('pjs.tooltips.change_due_date').'" id="duedate_editimg" width="18" height="16" style="cursor:pointer;" class="pointer"
					onclick="openDueDatePopUp(\'/updateduedate.php?action=user_decision&roundduedate=' . $pRoundDueDate . '&roundid=' . $pRoundId . '&rounduserid=' . $pRoundUserId . '\', 400, 200)"
					src="../i/edit.png" />
				</span>';
	}else{
		return '<span class="green_txt_due_date">Linguistic editing is due in ' . $lDueDateArr['datediff_days'] . ' day'.(abs($lDueDateArr['datediff_days'])>1?'s':'').'
					<img title="'.getstr('pjs.tooltips.change_due_date').'" id="duedate_editimg" width="18" height="16" style="cursor:pointer;" class="pointer"
					onclick="openDueDatePopUp(\'/updateduedate.php?action=user_decision&roundduedate=' . $pRoundDueDate . '&roundid=' . $pRoundId . '&rounduserid=' . $pRoundUserId . '\', 400, 200)"
					src="../i/edit.png" />
				</span>';
	}

}

function showECEDecisionActions($pRoundDueDate, $pDocumentId) {
	$lDueDateArr = CheckDueDateDays($pRoundDueDate);

	if($lDueDateArr['flag'] == 2){
		$lRes .= '<a href="#">Send reminder</a> | ';
	}

	$lRes .= '<a href="/view_document.php?id=' . $pDocumentId . '&view_role=2&mode=1">Change copyeditor</a>';
	return $lRes;
}
function displayGroupNames($pId, $pJournalId, $pTitle) {
	return '
		<a class="link" st="' . $pId . '" href="/browse_journal_groups.php?journal_id=' . $pJournalId . '&amp;grp_id=' . $pId . '">
			<span></span>
			<span class="content">' . $pTitle . '</span>
		</a>
	';
}
function displayGroupName($pGroupTitle, $pSubtitle) {
	$lResult = '';

	if($pGroupTitle != "grptitle"){
		$lResult .= '<h1 class="dashboard-title withoutBorder">' . $pGroupTitle . '</h1>';
		if($pSubtitle != "grpsubtitle")
			$lResult .= '<h3 class="dashboard-title withoutBorder groupSubtitle" >&nbsp;' . $pSubtitle . '</h3>';

	}else{
		$lResult .= '<h1 class="dashboard-title withoutBorder">' . getstr('pjs.subject_editors') . '</h1><a target="_blank" href="http://www.pensoft.net/journals/bdj/editor_form.html"><span style="color: rgb(128,0,0); margin-top: -27px; float: right; margin-right: 24px;"><b>Editor application form</b></span></a>';
	}
	return $lResult;
}

function showAgencies($pAgencies) {
	$lArrAgencies = explode(',', $pAgencies);

	$lRes = '';
	foreach($lArrAgencies as $key => $value){
		$lRes .= $value . '<br>';
	}

	return $lRes;
}

function formatDateDMY($pDate, $pSeparator = '-') {
	preg_match('/(\d+)[-–\/](\d+)[-–\/](\d+) (\d+):(\d+):(\d+)/', $pDate, $lMatch);
	$lFormatedDate = $lMatch[1] . $pSeparator . $lMatch[2] . $pSeparator . $lMatch[3];

	return $lFormatedDate;
}

function getClearDiv($pRowNum) {
	if((int) $pRowNum % 2 == 0)
		return '
					<div class="P-Clear"></div>
				';
}

function showCopyEditingText($pStateId, $pCERoundsCount = 0) {
	if($pStateId == DOCUMENT_READY_FOR_COPY_REVIEW_STATE){
		if($pCERoundsCount == 0) {
			return getstr('pjs.copy_editing_text');
		} else {
			return getstr('pjs.copy_layout_editing_text');
		}
	}else{
		return getstr('pjs.copy_editing_proof_text');
	}
}
function displayFilterBox($pLegend) {
	if($pLegend && $pLegend != 'user_legend'){
		// var_dump($pLegend);
		return '
			<div class="box filter" align="center">
				<h3>Show markup</h3>
				<div class="popup">
					<br />
					<a href="#" style="border-bottom: 1px solid #E2E2DC; border-top: none;" onclick="openFilterPopUp();"><img src="/i/filter.png" alt="filter" /> Filter by reviewer</a>
					' . $pLegend . '
					<br />
					<a href="javascript:void(0);" onclick="$(\'#previewIframe\')[0].contentWindow.ShowAllReviews();">Show all reviews</a>
				</div>
				<div class="optionHolder">
				<a href="#" onclick="openFilterPopUp(); return false;">
					<img src="/i/filter.png" alt="filter" /> Filter by reviewer
				</a>
				</div>
			</div>';
	}
}
function showExpertises() {
	return '
	<script type="text/javascript">
		//<![CDATA[
		var lMode = $(\'#role\').val();
		if(lMode == 3){
			$(\'#categories_holder\').css(\'display\', \'block\');
			$(\'#user_roles_checkbox\').css(\'display\', \'none\');
			$(\'#user_roles_checkbox input\').css(\'display\', \'none\');
		}
		//]]>
	</script>';
}

function showViewManuscriptLink($pStateId) {
	if($pStateId == DOCUMENT_IN_LAYOUT_EDITING_STATE){
		return '<a href="#">Download as PDF</a>';
	}
	return '<a href="#">View manuscript</a>';
}

/*
 * function displaySortArrow($pOper, $pDirection){ if((int)$pOper == 1){ return
 * '<img src="/i/toparrow.png" alt="vote up" />'; }else { return '<img
 * src="/i/bottomarrow.png" alt="vote down" class="vote" />'; } }
 */
function displayFilterCriteria($pIssue, $pYear, $pLetter, $pAffiliation) {
	$lResult = '';
	if($pYear)
		$lResult .= '[' . $pYear . ']';
	if($pIssue)
		$lResult .= ' AND [Special issues]';
	if($pLetter)
		$lResult .= '<span class="Letter-in-filter">' . $pLetter . '</span>';
	if($pAffiliation)
		$lResult .= ' AND [' . $pAffiliation . ']';

	if($lResult)
		return '<div class="filterCriteria">' . $lResult . '</div>';
}
function displayArticlesFilterCriteria($pTaxon, $pSubject, $pGeographical, $pChronical, $pFromdate, $pToDate, $pSectionType, $pFoundingAgency) {
	$lResult = '';
	if($pTaxon)
		$lResult .= 'Taxon=[' . $pTaxon . ']';
	if($pSubject)
		$lResult .= ($pTaxon ? ' AND ' : '') . 'Subject=[' . $pSubject . ']';
	if($pGeographical)
		$lResult .= ($pTaxon || $pSubject ? ' AND ' : '') . 'Geographical region=[' . $pGeographical . ']';
	if($pChronical)
		$lResult .= ($pTaxon || $pSubject || $pGeographical ? ' AND ' : '') . 'Chronological period=[' . $pChronical . ']';
	if($pFromdate)
		$lResult .= ($pTaxon || $pSubject || $pGeographical || $pChronical ? ' AND ' : '') . 'Publication date=[' . $pFromdate . ' to ' . $pToDate . ']';
	if($pSectionType)
		$lResult .= ($pTaxon || $pSubject || $pGeographical || $pChronical || $pFromdate ? ' AND ' : '') . 'Section=[' . $pSectionType . ']';
	if($pFoundingAgency)
		$lResult .= ($pTaxon || $pSubject || $pGeographical || $pChronical || $pFromdate || $pSectionType ? ' AND ' : '') . 'Funding agency≈[' . $pFoundingAgency . ']';
	if($lResult)
		return '<div class="filterCriteria">' . $lResult . '</div>';
}
function displayArticlesFilterText($pRecords, $pTaxon, $pSubject, $pGeographical, $pChronical, $pFromdate, $pToDate, $pSectionType, $pFoundingAgency, $pFormName) {
	if($pTaxon || $pSubject || $pGeographical || $pChronical || $pFromdate || $pSectionType || $pFoundingAgency || $pFormName == 'article_search') {
		return (int)$pRecords . ' article'. ($pRecords == 1 ? '' : 's' ) .  ' matching your criteria';
	}
	//return 'Article' . ($pRecords == 1 ? '' : 's' );
	return 'Article' . ($pRecords == 1 ? '' : 's' );
}
function htmlformid($pHtmlFormId) {
	if($pHtmlFormId != "htmlformid" && trim($pHtmlFormId) != ''){
		return 'id="' . $pHtmlFormId . '"';
	}
	return '';
}

function showTasksPopUp($pEventIds, $pUrl = '') {
	$lReviewersFlag = (int)$_GET['reviewers_email_flag'];
	$lDocumentId = (int)$_GET['id'];
	$lRoleRedirect = (int)$_GET['role_redirect'];

	if(is_array($pEventIds) && (int) count($pEventIds)){
		return '
			<script>
				$(document).ready(function() {
					var eventids = ' . json_encode($pEventIds) . ';
					LayerEventTasksFrm(\'P-Registration-Content\', eventids, \'' . $pUrl . '\', ' . (int)$lReviewersFlag . ', ' . (int)$lDocumentId . ', ' . (int)$lRoleRedirect . ');
				});
			</script>
		';
	}
	return '';
}

function convertToJSArray($pEventIds) {
	return '[' . implode(',', $pEventIds) . ']';
}

function setRecCheckedAttr($pStateId) {
	if($pStateId == TASK_DETAIL_SKIP_STATE_ID){
		return 'checked="checked"';
	}
	return '';
}

function showEditTaskActionButtons($pStateId, $pRecipientsCount) {
	if($pStateId == TASK_DETAIL_NEW_STATE_ID){
		return '
			<div class="invite_reviewer_btn invite_reviewer_btn_E invite_reviewer_btn_E_first" style="float:left;width:155px;">
				<div class="invite_reviewer_btn_left"></div>
				<div onclick="PerformTaskAction(\'sendthis\', \'P-Registration-Content\')" class="invite_reviewer_btn_middle" style="width:147px;">Send this email</div>
				<div class="invite_reviewer_btn_right"></div>
				<div class="P-Clear"></div>
			</div>
			' . ($pRecipientsCount > 1 ? '
				<div class="invite_reviewer_btn invite_reviewer_btn_E invite_reviewer_btn_E_first" style="float:left;width:155px;">
					<div class="invite_reviewer_btn_left"></div>
					<div onclick="PerformTaskAction(\'sendall\', \'P-Registration-Content\')" class="invite_reviewer_btn_middle" style="width:147px;">Send all emails</div>
					<div class="invite_reviewer_btn_right"></div>
					<div class="P-Clear"></div>
				</div>
			' : '') . '
			<div class="P-Clear"></div>
		';
	}else{
		return '
			<div class="invite_reviewer_btn invite_reviewer_btn_E invite_reviewer_btn_E_first btn_inactive" style="float:left;width:155px;">
				<div class="invite_reviewer_btn_left"></div>
				<div class="invite_reviewer_btn_middle" style="width:147px;">Send this email</div>
				<div class="invite_reviewer_btn_right"></div>
				<div class="P-Clear"></div>
			</div>
			' . ($pRecipientsCount > 1 ? '
				<div class="invite_reviewer_btn invite_reviewer_btn_E invite_reviewer_btn_E_first btn_inactive" style="float:left;width:155px;">
					<div class="invite_reviewer_btn_left"></div>
					<div class="invite_reviewer_btn_middle" style="width:147px;">Send all emails</div>
					<div class="invite_reviewer_btn_right"></div>
					<div class="P-Clear"></div>
				</div>
			' : '') . '
			<div class="P-Clear"></div>
		';
	}
}

function checkSelectedRecipient($pDetTaskId, $pSelectedDetTaskId) {
	return ((int) $pDetTaskId == (int) $pSelectedDetTaskId ? 'taskpopup-list-row-holder-selected' : '');
}
function returnArrField($pFieldValue) {
	$lRes = str_replace('{', '', $pFieldValue);
	$lRes = str_replace('}', '', $lRes);
	$lRes = str_replace('"', ' ', $lRes);
	return $lRes;
}

/**
 * Here we will import the css for the versions page
 * in which we will replace the user ids-
 * so that the user changes to have style
 *
 * @param $pUserIds unknown_type
 *       	 an array containing info about all the users that have changes in
 *        	the currently viewwed version
 *       	 The format of the array is
 *       	 usr_id => usr_name
 */
function IncludeVersionCss($pUserIds) {
	if($pUserIds){
		$lCss = file_get_contents(VERSION_USR_CSS_PATH);
		if(! $lCss){
			return '';
		}
		$lUserIdx = 1;
		foreach($pUserIds as $lUsrData){
			$lUsrId = $lUsrData['id'];
			$lCss = str_replace('$user_' . $lUserIdx . '$', $lUsrId, $lCss);
			$lUserIdx ++;
		}
		return $lCss;
	}
}

function closePopUp($pClose, $pUrlParams) {
	if($pClose == 1){
		return '
		<script type="text/javascript">
			popupClosingAndReloadParent(\'' . $pUrlParams . '\');
		</script>';
	}
}

function disableFormFields(){
		return '<script type="text/javascript">
				// <![CDATA[
					$(function(){
						lPreviewMode = $(\'#previewMode\').val();
						if (lPreviewMode == 1){
							$(\'form[name="document_review_form"] :input\').attr(\'disabled\', \'disabled\');
							$(\'.saveForm .P-Green-Btn-Left\').css(\'background\', \'url(/i/green_btn_left_inactive.png) no-repeat\');
							$(\'.saveForm .P-Green-Btn-Middle\').css(\'background\', \'url(/i/green_btn_middle_inactive.png) repeat-x\');
							$(\'.saveForm .P-Green-Btn-Right\').css(\'background\', \'url(/i/green_btn_right_inactive.png) no-repeat\');
						}
					});
				// ]]>
				</script>';
}
function returnGrayCloseBtn($pPreviewMode, $pGreenClass, $pGrayClass) {
	return '<script type="text/javascript">
			// <![CDATA[
			$(function(){
				lPreviewMode = $(\'#previewMode\').val();
				if (lPreviewMode == 1){
					$(\'.previewBtn\').html(\'<div class="P-Grey-Btn-Left"></div><div class="P-Grey-Btn-Middle"><a onclick="popupClose()" href="javascript: void(0)">' . getstr('pjs.reviwer.form.close') . '</a></div><div class="P-Grey-Btn-Right"></div>\');
				}
			});
			// ]]>
		</script>';

}
function addSingleDocumentClass($pRole) {
	if($pRole == DEDICATED_REVIEWER_ROLE || $pRole == COMMUNITY_REVIEWER_ROLE || $pRole == CE_ROLE)
		return '<script type="text/javascript">
				// <![CDATA[
					$(function(){
						$(\'#previewHolder\').addClass(\'singleVersion\');
					});
				// ]]>
				</script>';
}

function checkCommunityPublicDueDate($pReviewProcessType, $pPanelDueDate, $pPublicDueDate) {

	if((int)$pReviewProcessType == DOCUMENT_PUBLIC_PEER_REVIEW) {
		$lDueDateArr = CheckDueDateDays($pPublicDueDate);
	} else {
		$lDueDateArr = CheckDueDateDays($pPanelDueDate);
	}

	if($lDueDateArr['flag'] == 2){
		return '<span class="red_txt_due_date">Reviews are ' . $lDueDateArr['datediff_days'] . ' day'.(abs($lDueDateArr['datediff_days'])>1?'s':'').' late</span>';
	}else{
		return '<span class="green_txt_due_date">Reviews expected in ' . $lDueDateArr['datediff_days'] . ' day'.(abs($lDueDateArr['datediff_days'])>1?'s':'').'</span>';
	}

}

function showSeparatorReviewers($pFirstName, $pLastName, $pRecords, $pRownum, $pVersionId) {
	if((int)$pVersionId) {
		$lStartReviewer = '<a title="'.getstr('pjs.tooltips.view_review').'" href="javascript: void(0)" onclick="openPopUp(\'/view_version.php?version_id=' . (int)$pVersionId . '\', 0, 0, \'window_' . $pVersionId . '\')">';
		$lEndReviewer = '</a>';
	}

	if($pRecords != $pRownum){
		return $lStartReviewer . $pFirstName . ' ' . $pLastName . $lEndReviewer . ', ';
	}else{
		return $lStartReviewer . $pFirstName . ' ' . $pLastName . $lEndReviewer;
	}
}

function showHideReviewersText($pHideReviewersText) {
	if($pHideReviewersText){
		return 'style="display:none"';
	}
}

function checkManagePaddings($pHideReviewersText) {
	if($pHideReviewersText){
		return 'style="padding-top:0px;"';
	}
}
// ~ showAssignmentSEDueDate
function ChangeDueDate($pOper) {
	if($pOper == 1)
		return '';
	elseif($pOper == 2)
		return '';
	elseif($pOper == 3)
		return '';
}
function showAddReviewersSection($pCanAddFlag, $pDocumentId, $pERedirect = 0) {
	if($pCanAddFlag == 'false')
		return '<div class="reviewholder_top">
					<div class="reviewholder_top_left">Reviewers</div>
					<div class="reviewholder_top_right">
						<img src="../i/plus.png" alt="" /> <a href="/view_document.php?id=' . (int) $pDocumentId . '&amp;view_role=' . SE_ROLE . '&amp;mode=1' . ($pERedirect ? '&e_redirect=1' : '') . '">Invite more reviewers</a>
					</div>
					<div class="P-Clear"></div>
				</div>';
}

function showIfItemExists($item, $itemtoshow) {
	return ($item ? $itemtoshow : '');
}

function ReturnSaveOrCloseBtn($pPreviewMode) {
	if($pPreviewMode == 1) // Close Btn
		return '<div class="P-Grey-Btn-Holder">
					<div class="P-Grey-Btn-Left"></div>
					<div class="P-Grey-Btn-Middle"><a href="javascript: void(0)" onclick="popupClose()">' . getstr('pjs.reviwer.form.close') . '</a></div>
					<div class="P-Grey-Btn-Right"></div>
				</div>';
	else
		return '<div class="P-Green-Btn-Holder">
				<div class="P-Green-Btn-Left"></div>
				<div class="P-Green-Btn-Middle P-80">
					<input id="submit-view-version-form" onclick="SubmitFormByName(\'document_review_form\');" type="submit" name="tAction" value="' . getstr('admin.article_versions.save') . '"  class="inputBtn" />
				</div>
				<div class="P-Green-Btn-Right"></div>
			</div>
		';
}
function displayChangesButtons($pRole) {
	if($pRole == DEDICATED_REVIEWER_ROLE || $pRole == COMMUNITY_REVIEWER_ROLE){
		/*
		 * return '<script type="text/javascript"> $(function(){
		 * $(\'#changes\').click(function(){ AcceptAllChanges(); }); });
		 * </script>';
		 */
	}else{
		return '<div class="box" align="center">
					<h3>Changes</h3>
					<div class="optionHolder">
						<a href="#" onclick="AcceptRejectCurrentChange(1);return false;" id="P-Accept-Change-Btn-Id" class="P-Disabled-Btn">
							<img src="/i/adddoc-small.png" alt="Accept current change" />
							<span>Accept</span>
						</a>
					</div>
					<div class="optionHolder">
						<a href="#" onclick="AcceptRejectCurrentChange();return false;" id="P-Reject-Change-Btn-Id" class="P-Disabled-Btn">
							<img src="/i/removedoc-small.png" alt="Reject current change" />
							<span>Reject</span>
						</a>
					</div>
					<div class="optionHolder" onclick="SelectPreviousNextChange(1);return false;" >
						<a href="#">
							<img src="/i/docleftarrow.png" alt="Go to previous change" />
							<span>Previous</span>
						</a>
					</div>
					<div class="optionHolder" onclick="SelectPreviousNextChange();return false;" >
						<a href="#">
							<img src="/i/docrightarrow.png" alt="Go to next change" />
							<span>Next</span>
						</a>
					</div>
					<script>InitChangeBtns()</script>
			</div>
		';
	}

}

function checkReadOnlyAndHasLegend($pReadOnly, $pUserLegend) {
	if($pUserLegend && $pUserLegend != 'user_legend' && $pReadOnly){
		return 'seVersionWithLegend';
	}
	return '';
}

/*
 *
 */
function decisionFormPreviewMode($pReadOnly, $pRole, $pName, $pDecision, $pUserLegend = '', $pAuthorVersionNum = '', $pAuthorName = '') {
	if($pReadOnly == 1){
		if($pRole != AUTHOR_ROLE && $pRole != PUBLIC_ROLE){
			$lHasLegent = 0;
			if($pUserLegend && $pUserLegend != 'user_legend'){
				$lHasLegent = 1;
			}
			$lRes .= '<div class="userDecision ' . ($lHasLegent ? 'userDecisionWithLegent' : '') . '">' . $pName . '\'s decision: ' . $pDecision . '</div>';
		}else{
			if($pRole == PUBLIC_ROLE) {
				$lRes .= '<div class="userDecision"></div>';
			} else {
				$lRes .= '<div class="userDecision">' . $pAuthorName . '\'s version: ' . $pAuthorVersionNum . '</div>';
			}

		}
		$lRes .= '<script type="text/javascript">
					// <![CDATA[
			$(document).ready(function(){
				$(".P-Wrapper-Container-Left").css("margin-top", "0px");
				$(".P-Article-Content").css("margin-top", "136px");
				$(".P-Wrapper-Container-Right").css("top", "136px");
				$(".buttons").css({
					"border-left": "none",
				});
			});
			// ]]>
		</script>';
	}else{
		$lRes .= '<div class="box clearBorder" style="width: 155px;" id="changes_display_holder">
				<h3>View</h3>
				<input type="radio" id="changes" name="changes_display" checked="checked" value="1" /> <label for="changes">Changes</label>
				<input type="radio" id="final" name="changes_display" /> <label for="final"> Final </label>
					<script type="text/javascript">
						$("#changes_display_holder :radio").bind("change", function(){
							$(\'#previewIframe\')[0].contentWindow.toggleChangesDisplay();
						});
					</script>
				</div>
				' . displayChangesButtons($pRole) . '
				';
	}
	return $lRes;
}
function changeReviwerDecisionBtnText($pRole) {
	if($pRole == DEDICATED_REVIEWER_ROLE || $pRole == COMMUNITY_REVIEWER_ROLE){
		return getstr('pjs.reviwer.decision');
	}elseif($pRole == CE_ROLE){
		return getstr('pjs.general_comments');
	}else{
		return getstr('pjs.editorial.decision');
	}
}

function showAssignReviewersBackLink($pDocumentId, $pEditorFlag = 0) {
	if((int) $pEditorFlag){
		return '<div class="back_link">
					&laquo; <a href="/view_document.php?id=' . (int) $pDocumentId . '&amp;view_role=' . JOURNAL_EDITOR_ROLE . '">back</a>
				</div>';
	}else{
		return '<div class="back_link">
					&laquo; <a href="/view_document.php?id=' . (int) $pDocumentId . '&amp;view_role=' . SE_ROLE . '">back</a>
				</div>';
	}
}

function showBorderReviewer($pCnt) {
	if($pCnt == 2){
		return '<div class="document_info_row_border_line"></div>';
	}
	return '';
}

function showReviewerRoundStateObjs($pDecisionId, $pInvitationState, $pUsrRoleName, $pUsrVersionId, $pUsrRoleId, $pRoundNumber, $pRoundUsrId, $pDocumentId) {
	if($pDecisionId){
		return '
			<span class="yellow-green-txt">' . $pUsrRoleName . '</span>
				<table width="100%" cellspacing="0" cellpadding="0">
				<tbody>
					<tr>
						<td align="center">
							<div class="invite_reviewer_btn invite_reviewer_btn_E invite_reviewer_btn_E_first">
								<div class="invite_reviewer_btn_left"></div>
								<div class="invite_reviewer_btn_middle" onclick="openPopUp(\'/view_version.php?version_id=' . $pUsrVersionId . '&view_role=' . $pUsrRoleId . '&round=' . $pRoundNumber . '&round_user_id=' . $pRoundUsrId . '&id=' . $pDocumentId . '\', 0, 0, \'window_' . $pUsrVersionId . '\')">See review</div>
								<div class="invite_reviewer_btn_right"></div>
								<div class="P-Clear"></div>
							</div>
						</td>
					</tr>
				</tbody>
				</table>
		';
	}elseif(in_array($pInvitationState, array(
		REVIEWER_CANCELLED_STATE,
		REVIEWER_CANCELLED_BY_SE_STATE
	))){
		return '<span class="yellow-green-txt">Request canceled/timeout</span>';
	}else{
		return '';
	}
}
function changeHeaderSize($pReadOnly) {
	if($pReadOnly == 1)
		return 'height: 59px;overflow:hidden';
}
function returnQuestion($pQuestionNum){
	return getstr('admin.article_versions.quest' . ($pQuestionNum + 1));
}
function seoUrl($pString) {
	$lString = strtolower($pString);
	$lString = preg_replace("/[\s-]+/", " ", $lString);
	$lString = preg_replace("/[\s_]/", "_", $lString);
	$lString = str_replace(array(
		'(',
		')'
	), '', $lString);

	return $lString;
}

function showCurrentAuthorVersionReviewer($pVersionNum, $pAuthorVersionNum, $pAuthorVersionId, $pDocumentId) {
	if((int) $pVersionNum){
		$lVersionNum = $pVersionNum;
	}else{
		$lVersionNum = $pAuthorVersionNum;
	}
	return showCurrentAuthorVersion($lVersionNum, $pAuthorVersionId, $pDocumentId);
}

function changeReviewType($pState, $pDocumentId, $pReviewTypeId, $pRole) {
	if((int) $pState == DOCUMENT_WAITING_SE_ASSIGNMENT_STATE && $pRole == JOURNAL_EDITOR_ROLE){
		return '<img title="'.getstr('pjs.tooltips.change_review_process').'" style="cursor: pointer;" onclick="openReviewTypePopUp(\'/chanrereviewtype.php?document_id=' . $pDocumentId . '&review_type_id=' . $pReviewTypeId . '\', 400, 200)" src="../i/edit.png" alt="edit">';
	}
}
function RetOldPjsLogoutImg($pLogout) {
	if((int) $pLogout){
		return '<img src="' . OLD_PJS_SITE_URL . '/logout.php" width="1" height="1" border="0" alt="" />';
	}
	return '';
}

function showHideByRole($pRole, $pSeOpen = 0) {
	if($pRole == PUBLIC_ROLE || $pRole == AUTHOR_ROLE || $pRole == CE_ROLE){
		return 'style="display:none"';
	}
}

function showRejectStatus($pStateId) {
	if($pStateId == DOCUMENT_REJECTED_STATE){
		return getstr('pjs.document_rejected_text');
	}elseif($pStateId == DOCUMENT_REJECTED_BUT_RESUBMISSION){
		return getstr('pjs.document_rejected_but_alabala_text');
	}
}

function showFormLabelByRole($pRole) {
	if($pRole == SE_ROLE || $pRole == E_ROLE) {
		return getstr('admin.article_versions.SEviewForm');
	}
	return getstr('admin.article_versions.previewForm');
}

function showPollHeaderByRole($pRole) {
	if($pRole == SE_ROLE || $pRole == E_ROLE) {
		return getstr('admin.article_versions.quest1_SE');
	}
	return getstr('admin.article_versions.quest1');
}

function showRejectNotes($pRejectRoundDecisionNotes) {
	if($pRejectRoundDecisionNotes){
		return '
			<div class="reject_notes_text">
				<div class="decision_notes_reject">Notes:</div>
				<div class="decision_notes_text_rejected">' . $pRejectRoundDecisionNotes . '</div>
			</div>';
	}
	return '';
}

function showHistoryTab($pActiveTab, $pDocumentId, $pViewRole, $pHasHistory) {
	if((int) $pHasHistory){
		return '
			<div class="tabRow ' . showViewDocumentActiveSectionTab($pActiveTab, 'a' . GET_HISTORY_SECTION) . '" onclick="window.location=\'view_document.php?id=' . $pDocumentId . '&view_role=' . $pViewRole . '&section=' . GET_HISTORY_SECTION . '\'">
				<div class="tabRowLeft_Inactive"></div>
				<div class="tabRowMiddle_Inactive">
					' . getstr('pjs.history_label_tab') . '
				</div>
				<div class="tabRowRight_Inactive"></div>
				<div class="P-Clear"></div>
			</div>
		';
	}

	return '';

}

function showReviewerAETextByViewMode($pViewMode) {
	if((int) $pViewMode){
		return '&nbsp;' . getstr('admin.article_versions.viewyourpreview');
	}else{
		return '&nbsp;' . getstr('admin.article_versions.yourpreview');
	}
}

function showHideTextByViewMode($pViewMode) {
	if((int) $pViewMode){
		return 'style="display:none"';
	}

	return '';
}

function showHideTextByViewMode2($pViewMode) {
	if(! (int) $pViewMode){
		return 'style="display:none"';
	}

	return '';
}

function showSeuccessMsg($pSuccess) {
	if($pSuccess == 1){
		return '<div class="form_success_update">Profile has been updated successful</div>';
	}
}

function showRequiredStart($pRole) {
	if($pRole != COMMUNITY_REVIEWER_ROLE){
		return '<span class="txtred">*</span>';
	}
	return '';
}

function showHideErrRow($pErrCnt) {
	return ((int) $pErrCnt ? '' : 'style="display:none;"');
}

function showViewVersionIconPanelR($pHasPanelReviews) {
	if((int) $pHasPanelReviews){
		return '<a href="#"><img src="../i/eye.png"></a>';
	}
	return '';
}

function SetVersionMode($pRole) {
	if((int) $pRole != SE_ROLE){
		return '';
	}
	return '<script>SetVersionSERoleMode()</script>';
}

function showInviteReviewersButton($pCanInviteReviewers, $pDocumentId, $pRoundNumber = 1, $pReviewType, $pShowInRound) {
	if($pCanInviteReviewers != 'true' && $pCanInviteReviewers != 't'){
		if($pRoundNumber == 1){
			$lRoundText = '1';
		}else if($pRoundNumber == 2){
			$lRoundText = '2';
		}

		$lMessage = '';
		switch ($pReviewType) {
			case DOCUMENT_CLOSED_PEER_REVIEW :
				$lMessage = sprintf(getstr('pjs.submission_peer_review_proces_not_invited_reviewers_closed_peer'), $lRoundText, REVIEW_ROUND_ONE_NEEDED_DEDICATED_REVIEWERS);
				break;
			case DOCUMENT_COMMUNITY_PEER_REVIEW :
			case DOCUMENT_PUBLIC_PEER_REVIEW :
				if($pRoundNumber == 1){
					$lMessage = sprintf(getstr('pjs.submission_peer_review_proces_not_invited_reviewers_community_public_peer'), $lRoundText, REVIEW_ROUND_ONE_NEEDED_DEDICATED_REVIEWERS, REVIEW_ROUND_ONE_NEEDED_PANEL_REVIEWERS);
				}else{
					$lMessage = sprintf(getstr('pjs.submission_peer_review_proces_not_invited_reviewers_closed_peer'), $lRoundText, REVIEW_ROUND_ONE_NEEDED_DEDICATED_REVIEWERS);
				}
				break;
		}
		$pShowInRound = substr($pShowInRound, 1);
		if((int)$pShowInRound != (int)$pRoundNumber)
			return '';
		return '
			<span class="yellow-green-txt">' . $lMessage . '</span>
			<div class="document_btn_actions_editor_holder">
				<table cellpadding="0" cellspacing="0" width=100%>
					<tr>
						<td align="center">
							<div class="invite_reviewer_btn invite_reviewer_btn_E invite_reviewer_btn_E_first" onclick="window.location=\'/view_document.php?id=' . $pDocumentId . '&amp;view_role=3&mode=1\'">
								<div class="invite_reviewer_btn_left"></div>
								<div class="invite_reviewer_btn_middle">Invite reviewers</div>
								<div class="invite_reviewer_btn_right"></div>
								<div class="P-Clear"></div>
							</div>
						</td>
					</tr>
				</table>
			</div>
		';
	}

	return '';
}

function showSEDocumentInfo($pDocumentId, $pUname, $pFirstName, $pLastName, $pJournalName) {
	$lRole = $_GET['view_role'];
	if($pUname && $pUname != 'se_uname'){

		$lChangeIcon = '';
		if($lRole == E_ROLE){
			$lChangeIcon = '<img src="../i/edit.png" title="'.getstr('pjs.tooltips.change_SE').'" onclick="window.location=\'/view_document.php?id=' . (int) $pDocumentId . '&view_role=' . E_ROLE . '&mode=1&suggested=1\'" class="ui-datepicker-trigger pointer">';
		}

		$lAddSubjToEmail = '?subject=[' . $pJournalName . '] Inquiry regarding a manuscript ' . $pDocumentId;

		return '
			<div class="document_info_se">
				<div class="document_info_bottom_info_right_left" style="width:79px; text-align: right;">
					Subject editor:
				</div>
				<div class="document_info_bottom_info_right_right">
					' . $pFirstName . ' ' . $pLastName . '
					<a href="mailto:' . $pUname . $lAddSubjToEmail . '"><img title="'.getstr('pjs.tooltips.send_email').'" src="../i/mail.png"></a>
					' . $lChangeIcon . '
				</div>
			</div>
		';
	}
}

function showInviteReviewersCorrectText($pRoundNumber, $pReviewType) {
	$lRes = '';
	if($pRoundNumber == 1){
		switch ($pReviewType) {
			case DOCUMENT_CLOSED_PEER_REVIEW :
				$lRes = sprintf(getstr('pjs.invite_reviewers_page_closed_peer_text_round1'), REVIEW_ROUND_ONE_NEEDED_DEDICATED_REVIEWERS);
				break;
			case DOCUMENT_PUBLIC_PEER_REVIEW :
			case DOCUMENT_COMMUNITY_PEER_REVIEW :
				$lRes = sprintf(getstr('pjs.invite_reviewers_page_community_public_peer_text_round1'), REVIEW_ROUND_ONE_NEEDED_DEDICATED_REVIEWERS, REVIEW_ROUND_ONE_NEEDED_PANEL_REVIEWERS);
				break;
		}
	}else if($pRoundNumber == 2){
		$lRes = sprintf(getstr('pjs.invite_reviewers_page__peer_process_text_round2'), REVIEW_ROUND_TWO_NEEDED_DEDICATED_REVIEWERS);
	}

	return $lRes;
}

function showNoDedicatedReviewersData($pType, $pReviewType, $pDocumentId) {
	if($pType == 1 && ($pReviewType == DOCUMENT_PUBLIC_PEER_REVIEW || $pReviewType == DOCUMENT_COMMUNITY_PEER_REVIEW)){
		return '
			<div class="no_dedicated_reviewers_data">
				<div class="no_dedicated_reviewers_data_title">
					Nominated reviewers
				</div>
				<div class="no_dedicated_reviewers_data_content">
					<a href="/view_document.php?id=' . (int) $pDocumentId . '&view_role=3&mode=1">
						Please assign at least 1 nominated reviewer
					</a>
				</div>
			</div>
		';
	}
}

function showNoPanelsData($pType, $pReviewType, $pDocumentId, $pRoundNumber) {
	if($pType == 2 && $pRoundNumber == 1 && ($pReviewType == DOCUMENT_PUBLIC_PEER_REVIEW || $pReviewType == DOCUMENT_COMMUNITY_PEER_REVIEW)){
		return '
			<div class="no_dedicated_reviewers_data">
				<div class="no_dedicated_reviewers_data_title">
					Panel reviewers
				</div>
				<div class="no_dedicated_reviewers_data_content">
					<a href="/view_document.php?id=' . (int) $pDocumentId . '&view_role=3&mode=1">
						Please assign at least 1 panel reviewer
					</a>
				</div>
			</div>
		';
	}
}

function showCommentPic($pPhotoId, $pIsDisclosed, $pUserRealId, $pCurrentUserIsEditor, $pCurrentUserId) {
	$lIsDisclosed = CheckIfUserIsDisclosed($pIsDisclosed, $pUserRealId, $pCurrentUserIsEditor, $pCurrentUserId);
	if($pPhotoId && $lIsDisclosed)
		return '<img border="0" alt="" height="27" width="27" src="' . PWT_URL . '/showimg.php?filename=c27x27y_' . (int)$pPhotoId . '.jpg" />';
	return '<img src="' . PWT_URL . '/i/user_no_img.png" alt="" height="27" width="27" />';
}

function showFormatedPubDate($pPubdate, $pDateOnly = false, $pSwitchDateYear = false) {
	global $gMonths;
	if (!preg_match('/(\d+)[-–\/](\d+)[-–\/](\d+)/', $pPubdate, $lMatch)) {
		return $pPubdate;
	}
	$lMonth = ltrim($lMatch[2], '0');
	if ($pDateOnly) {
		return (int)$lMatch[1] . ' ' . substr(ucfirst($gMonths[$lMonth]), 0, 3) . '. ';
	}
	if($pSwitchDateYear){
		return $lMatch[3] . ' ' . substr(ucfirst($gMonths[$lMonth]), 0, 3) . '. ' . (int)$lMatch[1];
	}
	return (int)$lMatch[1] . ' ' . substr(ucfirst($gMonths[$lMonth]), 0, 3) . '. ' . $lMatch[3];
}

function showFormatedPubDateInJSON($pPubdate, $pDateOnly = false, $pSwitchDateYear = false){
	return json_encode(showFormatedPubDate($pPubdate, $pDateOnly, $pSwitchDateYear));
}

function DisplayCommentAnswerForm($pAnswerForms, $pRootId){
	return $pAnswerForms[$pRootId];
}

function DisplayCommentEditForm($pEditForms, $pCommentId, $pCommentUsrId, $pCurrentUsrId, $pVersionIsReadonly = false){
// 	var_dump($pEditForms);
	if((int)$pCommentUsrId == (int)$pCurrentUsrId && !$pVersionIsReadonly){
		return $pEditForms[$pCommentId];
	}
}

function displayCommentReplyDetails($pRootId, $pCommentReplyForms, $pVersionIsReadonly = false){
	if($pVersionIsReadonly){
		return ;
	}
	$lResult = '
				<div id="P-Comment-Form_' . (int)$pRootId . '" class="P-Comment-Reply-Form-Wrapper" style="display: none;">
					<div class="P-Comment-Reply-Form">
						' . DisplayCommentAnswerForm($pCommentReplyForms, $pRootId) . '
						<div class="P-Clear"></div>
						<div class="reply_btn" onmousedown="SubmitCommentReplyForm(' . (int)$pRootId . ');"></div>
						<div class="P-Comment-Reply-Form-Cancel-Btn" onmousedown="showCommentForm(' . (int)$pRootId . ');"></div>
						<div class="P-Clear"></div>
					</div>
				</div>
			';
	return $lResult;
}

function putCommentOnClickEvent($pCommentId, $pCommentUsrId, $pCurrentUsrId, $pVersionIsReadonly = false){
	if($pVersionIsReadonly || $pCurrentUsrId != $pCommentUsrId){
		return;
	}
	return ' onclick="displayCommentEditForm(' . (int)$pCommentId . ')"';
}

function PutPreviewAutosaveScript($pVersionIsReadonly = false){
	if($pVersionIsReadonly){
		return;
	}
	return ' <script>SetAutosaveTimeout();</script>';
}



function showArticlePriceIfExist($pState){
	if ((int)$pState == 1){
		return;
	} else {
		return '<span class="price"><span>Reprint price:</span> <b>&euro; ' . $pPrice . '</b> <img src="/i/cart.png" alt="cart"></img></span>';
	}
}
function showDoiLinkIfExist($pDoi){
	if($pDoi){
		return '<a href="http://dx.doi.org/' . $pDoi . '" class="subLink">doi: ' . $pDoi . '</a>';
	}
}

function fixTopPositionLeftCol($pReadOnly) {
	if($pReadOnly == 1) {
		return 'style="top:136px"';
	} else {
		return 'style="top:110px"';
	}
}

function fixMarginTop($pReadOnly) {
	if($pReadOnly == 1) {
		return 'margin-top:136px;';
	} else {
		return 'margin-top:110px;';
	}
}

function GetRootCommentStyle($pStartInstanceId, $pStartFieldId, $pEndInstanceId, $pEndFieldId){
	if((int)$pStartInstanceId && (int)$pStartFieldId && (int)$pEndInstanceId && $pEndFieldId){
		return ' P-Inline-Comment ';
	}
	return ' P-Global-Comment ';
}

function DisplayDeleteCommentLink($pId, $pRootId, $pOriginalId, $pUsrId, $pVersionIsReadonly = false){
	global $user;
// 	$lResult = $pId . '_' . $pRootId . '_' . $pOriginalId . '_' . $pUsrId;
	if(!$pVersionIsReadonly && $pId == $pRootId && $pId == $pOriginalId && $pUsrId == $user->id){
		$lResult = '<span class="P-Delete-Comment P-Comment-Delete-Btn" onclick="DeleteComment(' . (int)$pId . ')">delete</span>';
	}
	return $lResult;
}

function displayResolvedInfo($pCommentId, $pIsResolved, $pResolveUid, $pResolveUserFullname, $pResolveDate, $pVersionIsReadonly = false){
	$lResult = '<div class="Comment-Resolve-Info">';

	if(!$pVersionIsReadonly){
		$lResult .= '<input type="checkbox" onclick="ResolveComment(' . $pCommentId . ')" name="is_resolved_' . $pCommentId . '" id="is_resolved_' . $pCommentId . '" value="1" ' . ($pIsResolved ? 'checked="checked"' : '') . '>';
		$lResult .= '<label id="label_is_resolved_' . $pCommentId . '" for="is_resolved_' . $pCommentId . '" class="' . ($pIsResolved ? ('Resolved-Comment-Label') : '') . '">' . ($pIsResolved ? ('Resolved by: <br/>' . $pResolveUserFullname) : 'Resolve') . '</label>';
	}else{
		if($pIsResolved){
			$lResult .= '<div class="P-Comment-Resolved-Read-Only-Info">Resolved by: ' . $pResolveUserFullname . '</div>';
		}
	}

	$lResult .= '</div>';
	return $lResult;
}

function  displayNewCommentBtn($pVersionIsReadonly){
	if((int)$pVersionIsReadonly){
		return;
	}
	return '<div style="margin-right:8px" class="comment_btn floatLeft P-Comment-Inline-Main-Btn" id="P-Comment-Btn-Inline" onmousedown="submitPreviewNewComment();return false"></div>
			<div class="comment_btn floatLeft " id="P-Comment-Btn-General" title="Comment issues related to the whole manuscript." onmousedown="submitPreviewNewComment(1);return false"></div>';
}

function  displayCommentsHelp($pVersionIsReadonly){
	if((int)$pVersionIsReadonly){
		return;
	}

	return '<div class="P-Input-Help" style="float: left; left: 100px;">
				<div class="P-Baloon-Holder" style="top: 22px; left: -87px; position: absolute; z-index: 999;">
					<div class="P-Baloon-Arrow" style="top: -4px; background-image: url(\'/i/boloon_arrow_top.png\'); height: 13px; left: 42px; position: absolute; width: 22px;"></div>
					<div class="P-Baloon-Top"></div>
					<div class="P-Baloon-Middle" style="width:280px;">
						<div class="P-Baloon-Content" style="font-weight:normal; color:#333;">
							There are two kinds of comments you can make on a manuscript.<br><br><b>Inline comments</b> are linked to a text selected in an editable field  (orange/gray outline on click/hover), but not to selected template texts, such as titles of the manuscript sections.<br><br><b>General comments</b> should be associated with the whole manuscript and not with selected parts parts of it.
						</div>
						<div class="P-Clear"></div>
					</div>
					<div class="P-Baloon-Bottom"></div>
				</div>
			</div>';
}

function displayNewCommentForm($pVersionIsReadonly, $pForm){
	if((int)$pVersionIsReadonly){
		return;
	}
	return '<div id="P-Comment-Unavailable-Text" style="display:none">
				' . getstr('comments.currentSelectionCommentIsUnavailable') . '
			</div>' . $pForm;
}

function displayPrevCommentVersionReadonlyClass($pVersionIsReadonly = false){
	if((int)$pVersionIsReadonly){
		return ' Comment-Prev-Readonly ';
	}
}

function displayReadonlyVersionHeaderBox($pVersionIsReadonly){
	if(!$pVersionIsReadonly){
		return;
	}
	return '
		<div class="P-Document-Err-Notification">
			<img src="' . PWT_URL . '/i/excl_ico.png" alt=""/>
			' . getstr('pjs.versionIsReadonly') . '
		</div>
	';
}

function DisplayCommentUserName($pIsDisclosed, $pUserRealId, $pCurrentUserIsEditor, $pCurrentUserId, $pCommentUserRealFullName, $pCommentUserUndisclosedName){
	if(CheckIfUserIsDisclosed($pIsDisclosed, $pUserRealId, $pCurrentUserIsEditor, $pCurrentUserId)){
		return $pCommentUserRealFullName;
	}
	return $pCommentUserUndisclosedName;
}

function showCopyEditorHolder($pRoundNumber, $pRoundDueDate, $pDocumentID) {
	if($pRoundNumber == 1) {
		return '
			<tr>
				<td align="left" style="padding:0px 0px 0px 20px">
					<div class="document_author_review_round_top_left document_author_review_round_top_left_editor">Editorial office</div>
				</td>
				<td align="center">
					' . checkEditorCEAssignDueDate($pRoundDueDate) . '
				</td>
				<td align="right" style="padding:0px 20px 0px 0px">
					<a href="view_document.php?id=' . (int)$pDocumentID . '&amp;view_role=2&amp;mode=1">Assign Copy Editor</a>
				</td>
			</tr>';
	} else {
		return '
			<tr>
				<td align="left" style="padding:0px 0px 0px 20px">
					<div class="document_author_review_round_top_left document_author_review_round_top_left_editor">Editorial office</div>
				</td>
				<td align="center">
					' . checkEditorCEAssignDueDate($pRoundDueDate) . '
				</td>
				<td align="right" style="padding:0px 20px 0px 0px">
					<a href="view_document.php?id=' . (int)$pDocumentID . '&amp;view_role=2&amp;mode=1">Assign Copy Editor</a>
				</td>
			</tr>
			<tr>
				<td align="left" style="padding:20px 0px 0px 20px">
					<div class="document_author_review_round_top_left document_author_review_round_top_left_editor">Editorial office</div>
				</td>
				<td align="center" style="padding-top:20px;">
					' . checkEditorLEAssignDueDate($pRoundDueDate) . '
				</td>
				<td align="right" style="padding:20px 20px 0px 0px">
					<a href="view_document.php?id=' . (int)$pDocumentID . '&amp;view_role=2&amp;mode=2">Assign Layout Editor</a>
				</td>
			</tr>';
	}
}

function CheckIfUserIsDisclosed($pIsDisclosed, $pUserRealId, $pCurrentUserIsEditor, $pCurrentUserId){
	if($pIsDisclosed || $pCurrentUserIsEditor || $pUserRealId == $pCurrentUserId){
		return true;
	}
	return false;
}

function scrollFormIfErrors($pErrs){
	if($pErrs) {
		return 'scrollToForm()';
	}
}

function displayCommentLastModdate($pCommentId, $pDate, $pDateInSeconds, $pIsRoot = false){
	$lResult = '';
	$pDate = showCommentDate($pDate);
	$lSpanId = 'comment_date_';
	if($pIsRoot){
		$lSpanId .= 'root_';
	}
	$lSpanId .= $pCommentId;
	$lCurrentSeconds = time();
	$lDiff = $lCurrentSeconds - $pDateInSeconds;
	$lResult = '<span id="' . $lSpanId . '" title="' . $pDate . '">
					<script>SetCommentDateLabel(' . json_encode($lSpanId) . ', ' . (int)$pDateInSeconds . ', ' . json_encode($pDate) . ')</script>
				</span>';
	return $lResult;
}

function showCommentDate($pPubDate){
	global $gMonths;
	if (!preg_match('/(\d+)[-–\/](\d+)[-–\/](\d+)\s+(\d+:\s*\d+)/', $pPubDate, $lMatch)) {
		return '';
	}
	$lMonth = ltrim($lMatch[2], '0');
	$lMonthName = substr(ucfirst($gMonths[$lMonth]), 0, 3);

	return $lMatch[4] . ' on ' . (int)$lMatch[1] . ' ' . $lMonthName . '. ' . $lMatch[3];
}

function displaySingleCommentInfo($pCommentId, $pRootId, $pCurrentUserIsEditor, $pUserPhotoId, $pIsDisclosed, $pUserRealId, $pCurrentUserId, $pCommentUserRealFullName, $pCommentUserUndisclosedName, $pCommentDate, $pCommentDateInSeconds,
		$pInRoot = false, $pStartInstanceId = 0, $pStartFieldId = 0, $pEndInstanceId = 0, $pEndFieldId = 0){
	if((int)$pCommentId == (int)$pRootId && !$pInRoot){
		return false;
	}
	$lIsGeneral = true;
	$lImgSrc = '/i/general_comment_icon.png';
	if((int)$pStartInstanceId && (int)$pStartFieldId && (int)$pEndInstanceId && (int)$pEndFieldId){
		$lIsGeneral = false;
		$lImgSrc = '/i/inline_comment_icon.png';
	}
	$lResult = '
			<div class="P-Comments-Info">
				' . ($pInRoot ?
						'<div class="P-Comment-Type-Icon"><img alt="" width="19" height="27" src="' . $lImgSrc . '" /></div>'
						:
						''
				) . '
				<div class="P-Comment-User-Pic">
					' . showCommentPic($pUserPhotoId, $pIsDisclosed, $pUserRealId, $pCurrentUserIsEditor, $pCurrentUserId) . '
				</div>
				<div class="P-Comment-Text-Data">
					<div class="P-Comment-UserName">
						 ' . DisplayCommentUserName($pIsDisclosed, $pUserRealId, $pCurrentUserIsEditor, $pCurrentUserId, $pCommentUserRealFullName, $pCommentUserUndisclosedName) . '
					</div>
					<div class="P-Comment-Date">
						' . displayCommentLastModdate($pCommentId, $pCommentDate, $pCommentDateInSeconds, $pInRoot) . '
					</div>
				</div>
				<div class="P-Clear"></div>
			</div>

	';
	return $lResult;
}

function displayRootCommentActions($pCommentId, $pOriginalId, $pIsResolved, $pResolveUid, $pResolveUserFullname, $pResolveDate, $pUsrId, $pCommentReplyForms, $pVersionIsReadonly = false){
// 		$pVersionIsReadonly = 0;
// 	var_dump($pCommentReplyForms, $pVersionIsReadonly);
	if($pVersionIsReadonly){
		return '
			<div class="P-Comment-Root-Action-Btns">
				' . displayResolvedInfo($pCommentId, $pIsResolved, $pResolveUid, $pResolveUserFullname, $pResolveDate, $pVersionIsReadonly) . '
				<div class="P-Clear"></div>
			</div>';
	}

	$lResult = '
			<div class="P-Inline-Line"></div>
			<div class="P-Comment-Root-Action-Btns">
				<div onclick="showCommentForm(' . (int)$pCommentId . ');" class="reply_btn P-Comment-Reply-Btn" id="P-Comment-Btn-' . (int)$pCommentId . '"></div>
				' . displayResolvedInfo($pCommentId, $pIsResolved, $pResolveUid, $pResolveUserFullname, $pResolveDate, $pVersionIsReadonly) . '
				' . DisplayDeleteCommentLink($pCommentId, $pCommentId, $pOriginalId, $pUsrId, $pVersionIsReadonly) . '

				<div class="P-Clear"></div>
				' . displayCommentReplyDetails($pCommentId, $pCommentReplyForms, $pVersionIsReadonly) . '

				<div class="P-Clear"></div>
			</div>';
	return $lResult;
}

function displayCommentSingleRowClass($pCommentId, $pRootId){
	if((int)$pCommentId != (int)$pRootId){
		return ' P-Comments-Single-Row-Non-Root ';
	}
}

function ShowHideAuthorAction($pCreateUid, $pPWTid) {
	global $user;
	if($pCreateUid == $user->id) {
		return '
			<div class="invite_reviewer_btn invite_reviewer_btn_E invite_reviewer_btn_E_first" onclick="window.location=\'' . PWT_URL . 'display_document.php?document_id=' . $pPWTid . '\'">
				<div class="invite_reviewer_btn_left"></div>
				<div class="invite_reviewer_btn_middle">Proceed</div>
				<div class="invite_reviewer_btn_right"></div>
				<div class="P-Clear"></div>
			</div>
		';
	} else {
		return '
			<div class="invite_reviewer_btn invite_reviewer_btn_E invite_reviewer_btn_E_first btn_inactive">
				<div class="invite_reviewer_btn_left"></div>
				<div class="invite_reviewer_btn_middle">Proceed</div>
				<div class="invite_reviewer_btn_right"></div>
				<div class="P-Clear"></div>
			</div>
		';
	}
}

function shortTitle($pText) {
	$CUT = 70;
	$text = strip_tags($pText);
	$CUTOFF = strpos($text, ' ', $CUT);
	return $CUTOFF ? mb_substr($text, 0, $CUTOFF) . ' ...' : $text;
}

function showPollAnswerErrClass($pAnswer, $pUserRole) {
	if(($_REQUEST['tAction'] == getstr('admin.article_versions.review')) && !$pAnswer && $pUserRole != COMMUNITY_REVIEWER_ROLE) {
		return 'class="poll_answer_err"';
	}
	return '';
}

function showAuthors($pAuthorNames, $pAuthorEmails, $pDocumentId, $pJournalName) {
	$lAuthorNamesArr = explode(',', $pAuthorNames);
	$lAuthorEmailsArr = explode(',', $pAuthorEmails);
	$lRes = '';

	$lAddSubjToEmail = '?subject=[' . $pJournalName . '] Inquiry regarding a manuscript ' . $pDocumentId;
	for ($i=0; $i < count($lAuthorNamesArr) ; $i++) {
		$lRes .= $lAuthorNamesArr[$i] . '
			<a href="mailto:' . $lAuthorEmailsArr[$i] . $lAddSubjToEmail . '">
				<img title="Send e-mail" src="../i/mail.png">
			</a>' . ($i == count($lAuthorNamesArr)-1 ? '' : ', ');
	}
	return $lRes;
}

function showHideSearchBoxEditorialTeam($pRoleId) {
	if($pRoleId == 3) {
		return 'style="display:block"';
	}
	return 'style="display:none"';
}

//01/09/2013 02:42:05.071563
//Fri, 28 Jun 2013 00:00:00 +0200
function formatDateForRSS($pDate) {
	preg_match('/(\d+)[-–\/](\d+)[-–\/](\d+) (\d+):(\d+):(\d+)/', $pDate, $lMatch);
	return date('D, j M Y G:i:s O', mktime($lMatch[4], $lMatch[5], $lMatch[6], $lMatch[2], $lMatch[1], $lMatch[3]));
}

function removeFierstParagraph($pText) {
	$lText = trim($pText);
	if(substr($lText, 0, 3) == '<p>') {
		$lText = substr($lText, 3);
	}

	if(substr($lText, -4) == '</p>') {
		$lText = substr($lText, 0, -4);
	}
	return $lText;
}

function showRSSLink() {
	global $user;
	return '<a target="_blank" href="/rss.php"><img src="/i/rss_icon.gif" alt="mendeley" /></a>';
}

function generateFBLink($pId) {
	if($pId) {
		$lUrl = urldecode(SITE_URL . '/articles.php?id=' . $pId);
		return 'href="#" onclick="window.open(\'https://www.facebook.com/sharer/sharer.php?u=' . $lUrl . '\',\'facebook-share-dialog\', \'width=550,height=450\'); return false;"';
	}
	return 'href="#"';
}

function generateTwitterLink($pId) {
	if($pId) {
		$lUrl = urldecode(SITE_URL . '/articles.php?id=' . $pId);
		return "href=\"javascript:(function(){window.twttr=window.twttr||{};var D=550,A=450,C=screen.height,B=screen.width,H=Math.round((B/2)-(D/2)),G=0,F=document,E;if(C&gt;A){G=Math.round((C/2)-(A/2))}window.twttr.shareWin=window.open('http://twitter.com/share','','left='+H+',top='+G+',width='+D+',height='+A+',personalbar=0,toolbar=0,scrollbars=1,resizable=1');E=F.createElement('script');E.src='http://platform.twitter.com/bookmarklets/share.js?v=1';F.getElementsByTagName('head')[0].appendChild(E)}());\"";
	}
	return 'href="#"';
}

function generateGPlusLink($pId) {
	if($pId) {
		$lUrl = urldecode(SITE_URL . '/articles.php?id=' . $pId);
		return "href=\"https://plus.google.com/share?url=$lUrl\" onclick=\"javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=550,width=450');return false;\"";
	}
	return 'href="#"';
}

function generateMendeleyLink($pId) {
	if($pId) {
		$lUrl = urldecode(SITE_URL . '/articles.php?id=' . $pId);
		return "href=\"#\" onclick=\"javascript:window.open('http://www.mendeley.com/import/?url=$lUrl', '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=550,width=450');return false;\"";
	}
	return 'href="#"';
}

function rewriteNames($pNamesString) {
	$lNamesArr = explode(',', $pNamesString);
	$lFirstNames = $lNamesArr[1];
	$lLastNames = $lNamesArr[0];
	$lFirstNamesArr = explode(' ', $lFirstNames);
	foreach ($lFirstNamesArr as $key => $value) {
		$lRes .= mb_substr($value, 0, 1, 'UTF-8');
	}
	return $lLastNames . ' ' . $lRes;
}

function generateEmailLink($pArticleId, $pDocumentName, $pJournalName, $pJournalShortName, $pDoi, $pAuthors, $pPublishDate) {
	if($pArticleId) {
		$pDocumentName = trim($pDocumentName);
		preg_match('/(\d+)[-–\/](\d+)[-–\/](\d+)/', $pPublishDate, $lMatch);
		$lDocumentLink = SITE_URL . '/articles.php?id=' . $pArticleId;

		$lAuthorsArr = explode('; ', $pAuthors);
		$pAuthors = '';
		$i = 1;
		foreach ($lAuthorsArr as $key => $value) {
			$pAuthors .= rewriteNames($value) . (count($lAuthorsArr) == $i ? '' : ', ');
			$i++;
		}
	
		$lSubject = 'Paper published in the ' . $pJournalName;
		$lBody = "Hi," .  urlencode("\n") .  urlencode("\n") . "Here is an interesting paper published in the " . $pJournalName . ": " . urlencode("\n") . urlencode("\n") . $pAuthors . " (". $lMatch[3] .") ".GetArticleTitleForCitation(trim($pDocumentName))." ".$pJournalName." 1: e".$pArticleId.". DOI: http://dx.doi.org/" . $pDoi . urlencode("\n");
		return 'href="mailto:?Subject=' . urldecode($lSubject) . '&amp;body=' . $lBody . '"';
	}
	return 'href="#"';
}

function getSortOpts($pSortBy) {
	return '
		<option value="0" style="padding-left: 10px;" ' . ($pSortBy == 0 ? 'selected="selected"' : '') . '>Publication date oldest</option>
		<option value="3" style="padding-left: 10px;" ' . ($pSortBy == 3 ? 'selected="selected"' : '') . '>Publication date newest</option>
		<option value="1" style="padding-left: 10px;" ' . ($pSortBy == 1 ? 'selected="selected"' : '') . '>Total views</option>
		<option value="2" style="padding-left: 10px;" ' . ($pSortBy == 2 ? 'selected="selected"' : '') . '>Unique views</option>
	';
}

function GetArticleTitleForCitation($pTitle){
	$pTitle = trim($pTitle);
	$lLastSymbol = mb_substr($pTitle, -1);
	if(!in_array($lLastSymbol, array('.', '?', '!'))){
		$pTitle .= '.';
	}
	return $pTitle;
}

function showMostVisitedLinkIfStaff() {
	global $user;
	return '
		<div class="leftSiderBlock withoutHeader">
			<div class="siderBlockLinksHolder">
				<div class="mostVisited">
					<div class="P-Clear"></div>
					<a href="/browse_journal_articles?journal_id=1&sortby=1">
						Most visited papers
					</a>
				</div>
			</div>
		</div>
	';
}

function displayArticlesFilterText2($pRecords) {
	return 'article' . ($pRecords == 1 ? '' : 's' ) . ' by this author';
}

function showAdditionalAuthorInfo($pAffiliation, $pCity, $pCountry, $pWebsite) {
	if($pAffiliation || $pCity || $pCountry) {
		$lRes = '<div class="greenDesc">';

		if($pAffiliation) {
			$lRes .= $pAffiliation;
		}

		if($pCity) {
			$lRes .= ($pAffiliation ? ', ' : '') . $pCity;
		}

		if($pCountry) {
			$lRes .= (($pAffiliation || $pCity) ? ', ' : '') . $pCountry;
		}

		$lRes .='</div>';
	}
	if($pWebsite) {
		$lRes .= '<div class="greenDesc"><a target="_blank" href="' . $pWebsite . '">' . $pWebsite . '</a></div>';
	}

	return $lRes;

}

function getPreviousRecordsCount($pPageNum, $pPageSize){
	return ($pPageNum - 1) * $pPageSize;
}

function displayRequestParamIfExists($pParamName, $pParamValue){
	if( $pParamValue == '')
		return;
	return ' ' . $pParamName . '="' . h($pParamValue) . '"';
}

function displayModsIssueNumber($pIssueNumber, $pShowIssueNumber = 0){
	if((int)$pIssueNumber != 1 )
		return;
	return '<mods:detail type="issue">
										<mods:number>' . xmlEscape($pIssueNumber) . '</mods:number>
									</mods:detail>';
}

/*
 Махаме xml-таговете. За целта лоудваме в xml dom и връщаме textContent-а на руута. Ако не стане лоуд-а - просто връщаме резултата от изпълнението на функцията xmlEscape
*/
function stripXmlTags($pValue){
	$lXmlDom = new DOMDocument('1.0', 'utf-8');
	$pValue = strip_tags($pValue);
	return xmlEscape($pValue);
}

function displayOaiRecordSets($pSets, $pMetadataPrefix, $pViewObject){
	$lData = array(
		array(
			'spec' => $pSets,
		),
	);	
	$lObject = new evList_Display(array(
		'name_in_viewobject' => 'sets_' . $pMetadataPrefix,
		'controller_data' => $lData,
		'view_object' => $pViewObject,
	));
	return $lObject->Display();
}

function displayOaiRecordKeywords($pKeywords, $pMetadataPrefix, $pViewObject){
	$lKeywords = strip_tags($pKeywords);
	$lKeywordsArr = explode(',', $lKeywords);
	$lKeywordsArr = array_map(function($pElement){return trim($pElement);}, $lKeywordsArr);
	$lKeywordsArr = array_filter($lKeywordsArr, function($pElement){return $pElement != '';});
	$lData = array();
	foreach ($lKeywordsArr as $lKeyword){
		$lData[] = array('name' => $lKeyword);
	}
	$lObject = new evList_Display(array(
		'name_in_viewobject' => 'keywords_' . $pMetadataPrefix,
		'controller_data' => $lData,
		'view_object' => $pViewObject,
	));
	return $lObject->Display();
}

function displayOaiRecordAuthors($pAuthorsData, $pMetadataPrefix, $pViewObject){
	$lObject = new evList_Display(array(
		'name_in_viewobject' => 'authors_' . $pMetadataPrefix,
		'controller_data' => $pAuthorsData,
		'view_object' => $pViewObject,
	));
	return $lObject->Display();
}

function GetOaiErrCode($pCode){
	$lErrCodeMsgs = array(
		OAI_ERR_CODE_BAD_RESUMPTION_TOKEN => 'badResumptionToken',
		OAI_ERR_CODE_BAD_ARGUMENT => 'badArgument',
		OAI_ERR_CODE_NO_SET_HEIRARCHY => 'noSetHierarchy',
		OAI_ERR_CODE_NO_RECORDS => 'noRecordsMatch',	
		OAI_ERR_CODE_ID_DOES_NOT_EXIST => 'idDoesNotExist',	
	);
	$lResult = $lErrCodeMsgs[$pCode];
	
	return xmlEscape($lResult);
}


function setAuthorRowOpenDiv($pRownum, $pRecords) {
	if($pRownum%2 == 1) {
		return '<div class="author_holder_row">';
	}
	return '';
}

function setAuthorRowCloseDiv($pRownum, $pRecords) {
	if(($pRownum%2 == 0) || $pRecords == $pRownum) {
		return '<div class="P-Clear"></div></div>';
	}
	return '';
}

function showCommentUserPic($pPhotoId) {
	//$lIsDisclosed = CheckIfUserIsDisclosed($pIsDisclosed, $pUserRealId, $pCurrentUserIsEditor, $pCurrentUserId);
	if($pPhotoId)
		return '<img border="0" alt="" height="27" width="27" src="' . PWT_URL . '/showimg.php?filename=c27x27y_' . (int)$pPhotoId . '.jpg" />';
	return '<img src="' . PWT_URL . '/i/user_no_img.png" alt="" height="27" width="27" />';
}

//<div class="Comment-Resolve-Info"><input type="checkbox" onclick="ResolveComment(3015)" name="is_resolved_3015" id="is_resolved_3015" value="1"><label id="label_is_resolved_3015" for="is_resolved_3015" class="">Resolve</label></div>

function showEditOptions($pId, $pCanEdit, $pState) {
	if($pCanEdit && in_array($pState, array(FORUM_MESSAGE_STATE_UNAPPROVED))) {
			
		return '
			<div class="forum_list_actions">
				<input type="checkbox" onclick="submitArticleNewComment(2, \'article_comments_form\', ' . $pId . ')" name="approve" id="approve_' . $pId . '" value="1">
				<label for="approve_' . $pId . '" class="">Approve</label>
				<input type="checkbox" onclick="submitArticleNewComment(3, \'article_comments_form\', ' . $pId . ')" name="reject" id="reject_' . $pId . '" value="1">
				<label for="reject_' . $pId . '" class="">Reject</label>
			</div>
		';
	}
	if($pCanEdit && in_array($pState, array(FORUM_MESSAGE_STATE_APPROVED, FORUM_MESSAGE_STATE_REJECTED))){
		return '
			<div class="forum_list_actions">
				<input type="checkbox" ' . ($pState == FORUM_MESSAGE_STATE_APPROVED ? 'checked="checked" disabled="true"' : 'onclick="submitArticleNewComment(2, \'article_comments_form\', ' . $pId . ')"') . ' name="approve" id="approve_' . $pId . '" value="1">
				<label ' . ($pState == FORUM_MESSAGE_STATE_APPROVED ? '' : 'for="approve_' . $pId) . '">Approve</label>
				<input type="checkbox" ' . ($pState == FORUM_MESSAGE_STATE_REJECTED ? 'checked="checked" disabled="true"' : 'onclick="submitArticleNewComment(3, \'article_comments_form\', ' . $pId . ')"') . ' name="reject" id="reject_' . $pId . '" value="1">
				<label ' . ($pState == FORUM_MESSAGE_STATE_REJECTED ? '' : 'for="reject_' . $pId) . '">Reject</label>
			</div>
		';
	}
	return '';
}

function showCommentHeadElementByFlag($pFlag){
	if(!$pFlag) {
		return '
			<div class="forum_wrapper">
				<div class="forum_list_head">
					' . getstr('pjs.article_forum_list_head') . '
				</div>
				<div class="article_messages_wrapper_content" id="article_messages_wrapper_content">
		';
	} else {
		return '';
	}
}

function showCommentFootElementByFlag($pFlag){
	if(!$pFlag) {
		return '
				</div>
			</div>
		';
	} else {
		return '';
	}
}

function setCommentLoginRedirLink($pArticleId) {
	return '/login.php?redirurl=' . urlencode('/articles.php?id=' . $pArticleId . '&display_type=list&element_type=' . ARTICLE_MENU_ELEMENT_TYPE_FORUM);
}

function showAOFPoll() {
	global $gQuestions;
	if(count($gQuestions)){
		foreach ($gQuestions as $key => $value) {
			$lRes .= '
			<tr>
				<td class="form_questions" colspan="4">{*question' . $value . '}</td>
			</tr>
			<tr>
				{question' . $value . '}
			</tr>
			<tr>
				<td colspan="4"><div class="form_line"></div></td>
			</tr>
			';
		}
	}
	return $lRes;
}

function getPollAnswerLabel($pAnswer) {
	return getstr('admin.article_versions.option' . (int)$pAnswer);
}

function showAOFPollIfExists($pHasPoll, $pId, $pState) {
	if((int)$pHasPoll && $pState == FORUM_MESSAGE_STATE_APPROVED) {
		return '<div class="aof_view_poll_link">
			<a href="javascript:void(0)" onclick="LayerViewPoll(\'P-Post-Review-Form-Poll\', ' . $pId . ', ' . AOF_COMMENT_POLL_ELEMENT_TYPE . ');">
				' . getstr('pjs.aof_view_poll_link') . '
			</a>
		</div>';
	}
}

function showAOFCommentMessage($pMessage, $pState) {
	if($pState == FORUM_MESSAGE_STATE_APPROVED) {
		return $pMessage;
	} else {
		return '<span class="comment_rejected">' . getstr('pjs.comment_is_rejected') . '</span>';
	}
}
?>