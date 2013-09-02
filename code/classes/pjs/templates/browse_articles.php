<?php

// @formatter->off
$gTemplArr = array(
	// Browse Journal Articles List Templates
	
	'browse_articles.header' => '
			<h1 class="dashboard-title withoutBorder">{records} ' . getstr('pjs.articles_matching_your_criteria') . '</h1>
			<div style="margin: 10px;">
				{_displayArticlesFilterCriteria(taxon, subject, geographical, chronical, fromdate, todate, sectiontype, fundingagency)}
				<p style="font-size: 14px;">' . getstr('pjs.refine_filters_in_the_left_panel') . '</p>
				<div style="border-top: 1px solid #EEECE5; border-bottom: 1px solid #EEECE5; margin-top: 20px; margin-bottom: 15px;">
					{nav}
				</div>
	',
	
	'browse_articles.startrs' => '',
	
	'browse_articles.row' => '
				<div class="article" style="border-top: none;">
					<div class="starHover"></div>
					<div class="articleHeadline">
						<a href="/articles.php?id={id}" target="_blank">
							{name}
						</a>
					</div>
					<div class="authors_list_holder">
						{authors_list}
					</div>
					
					<div class="research">
						{journal_section_name}
					</div>
				
					&nbsp;&nbsp;&nbsp;
					<a href="#" class="subLink">{_showDoiLinkIfExist(doi)}</a>
					<div class="info">
						<span><img src="i/articleCalendar.png" alt="Calendar"></img> {_getOnlyDatePart(publish_date)}</span>
						<span><img src="i/eye.png" alt="eye"></img> 465</span>
						<div>
							<a href="/articles.php?id={id}" target="_blank">HTML</a>
							<a href="#">XML</a>
							<a href="javascript: void(0)" onclick="GeneratePDFPreview({id})" class="clearBorder">PDF</a>
						</div>
					</div>
				</div>
	',
	'browse_articles.endrs' => '',
	
	'browse_articles.footer' => '
				<div>
			{nav}
			<div class="h10"></div>
			<div class="h10"></div>
			<div class="h10"></div>
		</div>
	</div>
	',
	
	'browse_articles.empty' => '
		<div class="h10"></div>
		<div class="h10"></div>
		<div class="textCenterAlign">No Articles.</div>
	',
	
	'browse_articles.sidebar_left' => '
				<div id="leftSider">
					<div class="leftSiderBlock bigBlock">
						<h3>' . getstr('pjs.browse') . '</h3>
						<div class="P-Clear"></div>
						<div class="siderBlockLinksHolder">
							<a class="link active" href="/browse_journal_articles.php?journal_id={journal_id}">
								<span></span>
								<span class="content">' . getstr('pjs.articles') . '</span>
							</a>
							<!-- <a class="link" href="/browse_journal_issues.php?journal_id={journal_id}">
								<span></span>
								<span class="content">' . getstr('pjs.issues') . '</span>
							</a> -->
							<a class="link" href="/browse_journal_authors.php?journal_id={journal_id}">
								<span></span>
								<span class="content">' . getstr('pjs.authors') . '</span>
							</a>
						</div>
						<div class="P-Clear"></div>
					</div>
					<div id="filters_form_holder">
						{search_form}
					</div>
				</div>
	',
	
	'browse_articles.search_form' => '
					{journal_id}
					<div class="leftSiderBlock bigBlock">
						<h3>' . getstr('pjs.filter') . '</h3>
						<div class="P-Clear"></div>
						<div class="filterBlock category">
							<div class="filterBlockTitle">
								' . getstr('pjs.bytaxon') . ' 
								<a id="taxon_arrow" class="blockUpArrow tree" href="javascript:void(0);" onclick="toggleBlock(\'taxon_arrow\', \'taxon_tree\')"></a>
							</div>
							<div class="filterBlockContent" id="taxon_tree">
								<div class="P-Input-Full-Width P-W390 spacer10t">
										{alerts_taxon_cats}
								</div>
								<!-- Tree alerts_taxon_cats -->
								<div id="treealerts_taxon_cats" class="filterBy">
									{^taxon_tree}
								</div>
								{^taxon_tree_script}
								<script type="text/javascript">
									//<![CDATA[
									// Тук популираме първоначално инпута със селектнатите стойности или ако има грешка при сейв със селектнатите + новоселектнатите преди сейв
									var lSelectedCats =  new Array();
									lSelectedCats = {_json_encode(taxon_selected_vals)};
									/*if(!lSelectedCats.length)
										toggleBlock(\'taxon_arrow\', \'taxon_tree\');*/
									var InputVal = new Array();
									for ( var i = 0; i < lSelectedCats.length; i++) {
										$("#alerts_taxon_cats_autocomplete").tokenInput("add", lSelectedCats[i]);
									}
									//]]>
								</script>
								<!-- Tree #3 END -->
								<div class="P-Clear"></div>
							</div>
						</div>
						<div class="filterBlock category">
							<div class="filterBlockTitle">
								' . getstr('pjs.bysubject') . '
								<a id="subject_arrow" class="blockUpArrow tree" href="javascript:void(0);" onclick="toggleBlock(\'subject_arrow\', \'subject_tree\')"></a>
							</div>
							<div class="filterBlockContent" id="subject_tree">
								<div class="P-Input-Full-Width P-W390">
									{alerts_subject_cats}						
								</div>
								<!-- Tree alerts_subject_cats -->
								<div id="treealerts_subject_cats" class="filterBy">
									{^subjects_tree}
								</div>
								<!-- Tree #1 END -->
								{^subjects_tree_script}
								<script type="text/javascript">
									//<![CDATA[
									// Тук популираме първоначално инпута със селектнатите стойности или ако има грешка при сейв със селектнатите + новоселектнатите преди сейв
									var lSelectedCats =  new Array();
									lSelectedCats = {_json_encode(subject_selected_vals)};
									var InputVal = new Array();
									/*if(!lSelectedCats.length)
										toggleBlock(\'subject_arrow\', \'subject_tree\');*/
									for ( var i = 0; i < lSelectedCats.length; i++) {
										$("#alerts_subject_cats_autocomplete").tokenInput("add", lSelectedCats[i]);
									}
									//]]>
								</script>
							</div>
						</div>
						
						<div class="filterBlock category">
							<div class="filterBlockTitle">
								' . getstr('pjs.bygeographical') . '
								<a id="geographical_arrow" class="blockUpArrow tree" href="javascript:void(0);" onclick="toggleBlock(\'geographical_arrow\', \'geographical_tree\')"></a>
							</div>
							<div class="filterBlockContent" id="geographical_tree">
								<div class="P-Input-Full-Width P-W390">
									{alerts_geographical_cats}
								</div>
								<!-- Tree alerts_geographical_cats -->
								<div id="treealerts_geographical_cats" class="filterBy">
									{^geographical_tree}
								</div>
								{^geographical_tree_script}
								<script type="text/javascript">
									//<![CDATA[
									// Тук популираме първоначално инпута със селектнатите стойности или ако има грешка при сейв със селектнатите + новоселектнатите преди сейв
									var lSelectedCats =  new Array();
									lSelectedCats = {_json_encode(geographical_selected_vals)};
									var InputVal = new Array();
									if(!lSelectedCats.length)
										toggleBlock(\'geographical_arrow\', \'geographical_tree\');
									for ( var i = 0; i < lSelectedCats.length; i++) {
										$("#alerts_geographical_cats_autocomplete").tokenInput("add", lSelectedCats[i]);
									}
									//]]>
								</script>
								<!-- Tree #4 END -->
							</div>
						</div>
						
						<div class="filterBlock category">
							<div class="filterBlockTitle">
								' . getstr('pjs.bygeochronocal') . '
								<a id="chronological_arrow" class="blockUpArrow tree" href="javascript:void(0);" onclick="toggleBlock(\'chronological_arrow\', \'chronological_tree\')"></a>
							</div>
							<div class="filterBlockContent" id="chronological_tree">
								<div class="P-Input-Full-Width P-W390">
									{alerts_chronical_cats}						
								</div>
								<!-- Tree alerts_chronical_cats -->
								<div id="treealerts_chronical_cats" class="filterBy">
									{^chronological_tree}
								</div>
								<!-- Tree #2 END -->
								{^chronological_tree_script}
								<script type="text/javascript">
									//<![CDATA[
									// Тук популираме първоначално инпута със селектнатите стойности или ако има грешка при сейв със селектнатите + новоселектнатите преди сейв
									var lSelectedCats =  new Array();
									lSelectedCats = {_json_encode(chronological_selected_vals)};
									var InputVal = new Array();
									if(!lSelectedCats.length)
										toggleBlock(\'chronological_arrow\', \'chronological_tree\');
									for ( var i = 0; i < lSelectedCats.length; i++) {
										$("#alerts_chronical_cats_autocomplete").tokenInput("add", lSelectedCats[i]);
									}
									initComplete = true;
									gFormToSubmit = \'filter_articles\';
									//]]>
								</script>
							</div>
						</div>
						
						<div class="filterBlock category">
							<div class="filterBlockTitle">
								' . getstr('pjs.bypublicationdate') . ' (dd/mm/yyyy)
							</div>
							<div class="filterBlockContent">
								<div class="dateHolder">
									<div style="float: left; line-height: 42px; margin-right: 5px;">' . getstr('pjs.from') . '</div>
									<div class="fieldHolder date">
										{from_date}
									</div>	
								</div>
								
								<div class="dateHolder">
									<div style="float: left; line-height: 42px; margin-right: 5px; margin-left: 10px;">' . getstr('pjs.to') . '</div>
									<div class="fieldHolder date">
										{to_date}
									</div>	
								</div>
								<div class="P-Clear"></div>
								<script type="text/javascript">
									$(function() {
										$( "#from_date, #to_date" ).datepicker({
											showOn: "button",
											buttonImage: "i/articleCalendar.png",
											buttonImageOnly: true,
											dateFormat: \'dd/mm/yy\',
											onSelect: function(dateStr) {
												$(\'#filter_articles\').submit();
											}
										});
									});
								</script>
							</div>
						</div>
						
						<div class="filterBlock category">
							<div class="filterBlockTitle">
								' . getstr('pjs.bysectiontype') . '
							</div>
							<div class="filterBlockContent">
								{section_type}
							</div>
						</div>
						
						<div class="filterBlock category">
							<div class="filterBlockTitle">
								' . getstr('pjs.byfundingagency') . '
							</div>
							<div class="filterBlockContent">
								<div class="fieldHolder bigColField fund_ag">
									{funding_agency}
								</div>
								<div class="P-Clear"></div>
							</div>
						</div>
						<div class="buttonsHolder">
							<!--<div class="P-Green-Btn-Holder">
								<div class="P-Green-Btn-Left"></div>
								<div class="P-Green-Btn-Middle P-80">
								-->
										{Filter}
								<!-- </div>
								<div class="P-Green-Btn-Right"></div>
							</div>-->
							<div class="P-Grey-Btn-Holder">
								<div class="P-Grey-Btn-Left"></div>
								<a class="P-Grey-Btn-Middle" href="/browse_journal_articles.php?journal_id={@journal_id}">' . getstr('pjs.clear_filters') . '</a>
								<div class="P-Grey-Btn-Right"></div>
							</div>
							<div class="P-Clear"></div>
						</div>
						<div class="P-Clear"></div>
					</div>
	',
	
	'browse_articles.by_author_sidebar_left' => '
				<div id="leftSider">
					<div class="leftSiderBlock bigBlock">
						<h3>' . getstr('pjs.author') . '</h3>
						<div class="P-Clear"></div>
						<div class="siderBlockLinksHolder">
							<div style="float:left; margin-left: 10px; margin-right: 10px;">
								{_getUserPictureIfExist(photo_id)}
							</div>
							<div style="float: left;">
								<div class="green">{fullname}</div>
								<br/>
								<div class="greenDesc">{affiliation}</div>
							</div>
						</div>
						<div class="P-Clear"></div>
					</div>
					<div class="leftSiderBlock bigBlock">
						<h3>' . getstr('pjs.browse') . '</h3>
						<div class="P-Clear"></div>
						<div class="siderBlockLinksHolder">
							<a class="link" href="/browse_journal_articles.php?journal_id={journal_id}">
								<span></span>
								<span class="content">' . getstr('pjs.articles') . '</span>
							</a>
							<!-- <a class="link" href="/browse_journal_issues.php?journal_id={journal_id}">
								<span></span>
								<span class="content">' . getstr('pjs.issues') . '</span>
							</a>
							-->
							<a class="link" href="/browse_journal_authors.php?journal_id={journal_id}">
								<span></span>
								<span class="content">' . getstr('pjs.authors') . '</span>
							</a>
						</div>
						<div class="P-Clear"></div>
					</div>
				</div>
	',
	
	
	'browse_articles.by_author_startrs' => '
			<h1 class="dashboard-title withoutBorder">{records} ' . getstr('pjs.articles_matching_your_criteria') . '</h1>
			<div style="margin: 10px;">
				<div style="border-top: 1px solid #EEECE5; border-bottom: 1px solid #EEECE5; margin-top: 20px; margin-bottom: 15px;">
					{nav}
				</div>
				
	',
	'browse_articles.by_author_row' => '
				<div class="article" style="border-top: none;">
					<div class="starHover"></div>
					<div class="articleHeadline">
						<a href="/articles.php?id={id}" target="_blank">
							{name}
						</a>
					</div>
					<div class="authors_list_holder">
						{authors_list}
					</div>

					<div class="research">
						{journal_section_name}
					</div>
				
					&nbsp;&nbsp;&nbsp;
					<a href="#" class="subLink">{_showDoiLinkIfExist(doi)}</a>
					<div class="info">
						<span><img src="i/articleCalendar.png" alt="Calendar"></img> {_getOnlyDatePart(publish_date)}</span>
						<span><img src="i/eye.png" alt="eye"></img> 465</span>
						<div>
							<a href="/articles.php?id={id}" target="_blank">HTML</a>
							<a href="#">XML</a>
							<a href="javascript: void(0)" onclick="GeneratePDFPreview({id})" class="clearBorder">PDF</a>
						</div>
					</div>
				</div>
	',
	'browse_articles.by_author_endrs' => '
				<div style="margin-top: 20px; margin-bottom: 15px;">
					{nav}
				</div>
			</div>
	',
	'browse_articles.by_author_empty' => '
		<br/>
		<br/>
		<div style="text-align: center;">No Articles.</div>
	',
	
	'browse_articles.public_startrs' => '<div style="margin: 10px">',
	
	'browse_articles.public_endrs' => '{nav}</div>',
	
	'browse_articles.journal_fetures_head' => '<div id="leftSider" style="width: 182px;">
					<div class="leftSiderBlock">
							<h3>' . getstr('pjs.journal_features') . '</h3>
							<div class="siderBlockLinksHolder">
					',
				
	'browse_articles.journal_fetures_foot' => '<div class="P-Clear"></div>
										</div>
									</div>
								</div>',
);
?>