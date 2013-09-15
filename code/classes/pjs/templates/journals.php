<?php

$gTemplArr = array(
	'journals.list_startrs' => '
		<h1 class="dashboard-title withoutBorder">Journals</h1>
		<div style="margin-right: 305px;">
			<table border="0" width="100%">
				<!-- <tr>
					<td>' . getstr('pjs.journals.list_col_id') . '</td>
					<td>' . getstr('pjs.journals.list_col_name') . '</td>
					<td>' . getstr('pjs.journals.list_col_description') . '</td>
				</tr> -->
	',

	'journals.list_endrs' => '
			</table>
		</div>
	',

	'journals.list_row_' => '
				<tr>
					<td>{id}</td>
					<td>{name}</td>
					<td>{description}</td>
				</tr>
	',

	'journals.list_row' => '
			<tr>
				<td style="padding:15px 7px 13px 0px">
					<table border="0" cellspacing="0" cellpadding="0" class="price" width="100%">
						<tbody>
							<tr>
								<td width="200" height="214" rowspan="2" valign="top">
									<a href="/journals.php?journal_id={id}"><img src="/i/zookeys_static.jpg" border="0"></a>
								</td>
								<td valign="top" class="journalContent"><a href="/journals.php?journal_id={id}" class="green">{name}</a><br />
									<br/>
									{description}
								</td>
							</tr>
							<tr>
								<td></td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" bgcolor="#D1CDBB" height="2px"></td>
			</tr>
	',

	'journals.journal_documents_head' => '
				<div id="articles">
				<table width="795" cellspacing="0" cellpadding="0" border="0" id="homepage">
<tbody>
    <tr>
      <td width="200" valign="top" height="214" rowspan="2"><img border="0" src="/i/BDJ-homepage-cover.jpg" width="180" height="265" alt="Biodiversity Data Journal Cover" /></td>
      <td valign="top">
      	<h1 class="dashboard-title withoutBorder" style="padding-left: 0">Biodiversity Data Journal</h1>

        <p><q cite="http://norvig.com/chomsky.html">Science is a combination of gathering facts and making theories; neither can progress on its own. [...] In the history of science, the laborious accumulation of facts is the dominant mode, not a novelty.</q></p>
        <div style="text-align: right; margin-top: 4px "><p>Peter Norvig, Director of Research @Google Inc.</p></div>
        <p><b>Biodiversity Data Journal (BDJ)</b> is a community peer-reviewed, open-access, comprehensive online platform, designed to accelerate publishing, dissemination and sharing of biodiversity-related data of any kind. All structural elements of the articles &ndash; text, morphological descriptions, occurrences, data tables, etc. &ndash; will be treated and stored as DATA.</p>
      	<p>The journal will publish papers in biodiversity science containing taxonomic, floristic/faunistic, morphological, genomic, phylogenetic, ecological or environmental data on any taxon of any geological age from any part of the world with <b>no lower or upper</b> limit to manuscript size.</p>
      	<p>Download <a href="http://www.pensoft.net/J_FILES/Biodiversity%20Data%20Journal%20Leaflet.pdf">BDJ Information leaflet</a> and <a href="http://www.pensoft.net/J_FILES/Biodiversity_Data_Journal.pptx">presentation</a>.</p>
        <p><a href="http://www.pensoft.net/journals/bdj/editor_form.html" target="_blank"><span style="color: rgb(128,0,0)"><b>Editor Application Form</b></span></a></p>
        </td>
    </tr>
  </tbody>
</table>
			<!--	<h1 class="dashboard-title withoutBorder">Recent publications</h1>

	',
	'journals.journal_documents_row' => '

					<div class="article" style="border-top: none;">
						<div class="articleHeadline">
							<a href="#">
								{name}
							</a>
						</div>
						<p style="text-align: left;">
							{document_authors}
						</p>
						<img src="i/researchLeft.png" alt="Research Left Corner" class="floatLeft"></img>
						<div class="research">
							{journal_section_name}
						</div>
						<img src="i/researchRight.png" alt="Research Left Corner" class="floatLeft"></img>
						&nbsp;&nbsp;&nbsp;
						<a href="#" class="subLink">doi: {doi}</a>
						<span class="price"><span>Reprint price:</span> <b>&euro; {price}</b> <img src="i/cart.png" alt="cart"></img></span>
						<div class="info">
							<span><img src="i/paper.png" alt="paper"></img> {start_page}-{end_page}</span>
							<span><img src="i/articleCalendar.png" alt="Calendar"></img> {_getOnlyDatePart(publish_date)}</span>
							<span><img src="i/eye.png" alt="eye"></img> 465</span>
							<div>
								<a href="#">Abstract</a>
								<a href="#">HTML</a>
								<a href="#">XML</a>
								<a href="#" class="clearBorder">PDF</a>
							</div>
						</div>
					</div>
	',
	'journals.journal_documents_foot' => '-->
				</div>
				<script>
					$(document).ready(function() {
					  if({show_login_warning} == 1) {
					  	showLoginWarningMessage(\'{redirurl}\', \'{login_warning_text}\');
					  }
					});
				</script>
	',
	'journals.journal_documents_empty' => 'No Documents.',

	'journals.journal_document' => '
		<div class="landing_wrap">
			<div class="article_header">
				<img src="i/journal.png">
				<h1>Resolving the publishing bottleneck for biodiversity</h1>
				<blockquote>Science is a combination of gathering facts and making theories; neither can progress on its own. In the history of science, the laborious accumulation of facts is the dominant mode, not a novelty.</blockquote>
				<p>Peter Norvig</p>
			</div>
			<div class="content_article">
				<ul class="clearfix">
					<li>
						<img src="i/icon_data.png">
						<h2>Making “small” data big</h2>
						<ul>
							<li>No lower/upper limit of manuscript size</li>
							<li>Publish all kinds of biodiversity related data</li>
							<li>Reduced page charges affordable by all</li>
						</ul>
					</li>
					<li>
						<img src="i/icon_more.png">
						<h2>More than just data journal!</h2>
						<ul>
							<li>Integrated text and data publishing</li>
							<li>Completely online revisions and editing</li>
							<li>Community ownership of data</li>
						</ul>
					</li>
					<li>
						<img src="i/icon_community.png">
						<h2>Community peer-review</h2>
						<ul>
							<li>7 weeks from submission to decision</li>
							<li>3 days from acceptance to publication</li>
							<li>Public peer-review on author’s choice</li>
						</ul>
					</li>
				</ul>
				<div class="bdj_free_homepage_text">Free of charge in launch phase</div>
			</div>
			<div class="article_footer">
				<img src="i/frog.png">
				<h2>Why publish my data?</h2>
				<ul>
					<li>Citable publication</li>
					<li>Increase collaboration</li>
					<li>Re-use and multiply effect</li>
					<li>Establish scientific priority</li>
					<li>Link data to a bigger network</li>
					<li>Respond to funding requirements</li>
				</ul>
			</div>
		</div>
		<script>
			$(document).ready(function() {
			  if({show_login_warning} == 1) {
			  	showLoginWarningMessage(\'{redirurl}\', \'{login_warning_text}\');
			  }
			});
		</script>
	',
);
?>