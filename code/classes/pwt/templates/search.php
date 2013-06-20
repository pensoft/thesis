<?php

$gTemplArr = array(
	
	'search.nodata' => '
		<div class="P-Empty-Content">' . getstr('pwt.search.thereHasNoManuscriptsMatched') . '</div>
	',
	
	'search.nodata_article' => '
		<div class="P-Empty-Content">' . getstr('pwt.search.noResultsForYourSearchInThisManuscript') . '</div>
	',
	
	'search.headCommon' => '

	',
	
	'search.footCommon' => '
	
	',
	
	'search.head' => '

	',
	'search.foot' => '',
	
	'search.start' => '
			
	',
	
	'search.startCommon' => '
			
	',
	
	'search.end' => '
		<div class="P-Paging">
			{nav}
		</div>
	',
	
	'search.endCommon' => '
		<div class="P-Paging">
			{nav}
		</div>
	',
	
	'search.holder' => '
		<div class="P-Wrapper-Container-Middle Dashboar-Container-Middle" style="margin-right:20px; border:none;">
					<div class="P-Content-Dashboard-Holder">
						<div class="P-Section-Title-Holder">
							<table class="P-Data-Resources-Head" cellspacing="0" cellpadding="0">
								<tbody>
									<tr>
									<td class="P-Data-Resources-Head-Text">' . getstr('pwt.search.results') . '</td>
									<td class="P-Inline-Line"></td>
									</tr>
								</tbody>
							</table>
						</div>
							{searchcontent}
						<div class="P-Clear"></div>
					</div>
					<div class="P-Clear"></div>
				</div>
				<div class="P-Clear"></div>
		
	',
	
	'search.articleRow' => '
		<div class="P-Content-Dashboard-Row">
			<div class="P-Content-Dashboard-Row-Left">
				<div class="P-Content-Dashboard-Row-Names">{fullname}</div>
				<div class="P-Content-Dashboard-Row-Title">
					<a href="search.php?document_id={document_id}&catsearch=' . SEARCH_IN_ARTICLE . '&stext={encoded_stext}">{name}</a>
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="P-Content-Dashboard-Row-Right">
				<div class="P-Content-Dashboard-Row-Status P-Status-Draft">draft</div>
				<div class="P-Content-Dashboard-Row-History">
					<div class="P-Content-Dashboard-Row-History-Icon-Holder">
						<div class="P-Icon-Clock"></div>
					</div>
					<div class="P-Content-Dashboard-Row-History-Link"><a href="/preview_revisions.php?document_id={document_id}">' . getstr('dashboard.revision_history') . '</a></div>
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="P-Clear"></div>
		</div>
	',
	
	'search.holder_in_document' => '
				<div class="P-Section-Title-Holder">
					<table class="P-Data-Resources-Head" cellspacing="0" cellpadding="0">
						<tbody>
							<tr>
							<td class="P-Data-Resources-Head-Text">' . getstr('pwt.search.results') . '</td>
							<td class="P-Inline-Line"></td>
							</tr>
						</tbody>
					</table>
				</div>
				{searchcontent}
				<div class="P-Clear"></div>
	',
	
	'search.articleRowInDocument' => '
		{searchresult}
	',
);

?>