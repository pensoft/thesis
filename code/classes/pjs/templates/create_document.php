<?php
// @formatter->off
$gTemplArr = array(
	'create_document.error_row' => '{err_msg}<br/>',
	
	'create_document.zookeys_submission_form_step_1_pwt' => '{document_id} {step}
		<b>Submission preparation checklist</b>
		<div class="formErrors" style="color:red">{~}{~~}</div>

		<br/>
		Please indicate that this submission is ready to be considered for this
		journal by accepting the following conditions: 
		<br/><br/>
		{preparation_checklist}
		<br/>
		<b>Copyright notice</b>
		<br/><br/>
		Authors who publish with this journal agree to the following terms (please consult our <a href="http://biodiversitydatajournal.com/about#LicensesandCopyright" target="_blank">Licenses and Copyright</a> guidelines for text and data publication): 
		<br/>
		' . "
		<ol>
		<li> Authors retain copyright and grant the journal the right of first publication with the work simultaneously licensed under a <a href='http://creativecommons.org/licenses/by/3.0/' target='_blank'>Creative Commons Attribution License 3.0 (CC-BY)</a> which allows others to freely download and distribute the work provided that the work's  authorship and original publication in this journal are properly attributed.
		</li>  
 		<li>Authors grant the journal the right of open-access publication of data submitted with manuscripts as supplementary files under the Open Data Commons Attribution License, <a href='http://www.opendatacommons.org/licenses/by/1.0/' target='_blank'>http://www.opendatacommons.org/licenses/by/1.0/</a> (default).
		</li>  
 		<li>Authors confirm that all data linked to a journal article in external repositories are published in OPEN ACCESS.
		</li>  
 		<li>Authors confirm that text, images, multimedia and data submitted to the journal are not a subject of copyright violation.
		</li>  
 		<li>Authors are able and encouraged to distribute the published work and data (e.g., post it to institutional or international repositories and/or websites, or publish it in a book), provided that original publication in this journal is credited. 
		</li>
		</ol>
		
		
		{terms_agreement}
		<br/>
		<b>Journal's privacy statement</b>
		<br/><br/>
		" . '
		The names and email addresses entered in this journal site will be used exclusively for the stated purposes of this journal and will not be made available for any other purpose or to any other party. 
		<br/><br/>
		<table width="100%" id="steps_nav">
			<tr><td style="text-align: right; width: 50%"></td><td>{save}</td></tr>
		</table>
		
	',
	
	'create_document.zookeys_submission_form_step_2_pwt' => '{document_id} {step}
		<br/>
		Publishing in Biodiversity Data Journal is free during its launch phase and thereafter will be subject to a minimal fee that anyone can afford. 
		<br/><br/>

		<table width="100%" id="steps_nav">
			<tr><td style="text-align: right; width: 50%">{back}</td><td>{save}</td></tr>
		</table>
	',
	
	'create_document.zookeys_submission_form_step_3_pwt' => '{document_id} {step}
	
		{~}{~~}
		<h4>Assign to regular or special issue</h4>
		<p>{*intended_issue}
		{intended_issue}</p>
		<br/><br/>
		Encountering difficulties? Contact <a href="mailto:bdj@pensoft.net">Editorial Secretary</a> for assistance.
		<br/><br/>


		<table width="100%" id="steps_nav">
			<tr><td style="text-align: right; width: 50%">{back}</td><td>{save}</td></tr>
		</table>
	',
	
	'create_document.zookeys_submission_form_step_4_pwt' => '{document_id} {step} {event_id}
		{~}{~~}
		<b>Select the appropriate peer review option for this submission.</b>
		<br/><br/>
		Section: {@journal_section}
		<br/><br/>
		<fieldset>
			<legend>{*review_process_type}</legend>
			{review_process_type}
		</fieldset>
		<br/><br/>
		{*comments_to_editor}
		{comments_to_editor}
		<br/><br/>
		<table width="100%" id="steps_nav">
			<tr><td style="text-align: right; width: 50%">{back}</td><td>{save_finish}{save_next}</td></tr>
		</table>
		<script type="text/javascript">
		//<![CDATA[
			$(document).ready(function () {
				var lOptsLength = $("input:radio[name=review_process_type]").length;
				if(lOptsLength == 1) {
					$("input:radio[name=review_process_type]").attr(\'checked\', \'checked\');
					if($("input:radio[name=review_process_type]").val() == 1) {
						$("#finish_button").show();
						$("#next_button").hide();
					}
				}
				
				$("input:radio[name=review_process_type]").click(function() {
					var value = $(this).val();
					if(value == 1){
						$("#finish_button").show();
						$("#next_button").hide();
					} else {
						$("#next_button").show();
						$("#finish_button").hide();
					}
				});
			});
		//]]>
		</script>
	',
	
	'create_document.zookeys_submission_form_step_5_pwt' => '{document_id} {step} {event_id}
		<br/><br/>
		<div class="reviewers_footer">
			<div class="reviewers_footer_txt">{_getstr(pjs.addreviewerstolist)}</div>
			<div class="reviewers_footer_content">
				<div class="reviewers_footer_content_left">
					<div class="reviewers_footer_content_left_label">{_getstr(pjs.searchreviewers)}</div>
						<div class="reviewers_footer_content_left_inp_holder">
							<div class="fieldHolder">
								<input type="text" value="" name="reviewer_search" id="reviewer_search" />
								<script type="text/javascript">
								//<![CDATA[
								$.ui.autocomplete.prototype._renderMenu = function(ul, items) {
								  var self = this;
								  ul.append("<table width=\"100%\"><tbody></tbody></table>");
								  $.each( items, function( index, item ) {
									self._renderItem( ul.find("table tbody"), item );
								  });
								};
								$.ui.autocomplete.prototype._renderItem = function ( table, row ) {															
									var TR =  $( "<tr></tr>" )
										.data( "item.autocomplete", row )
										.append( 
												"<td class=\"name\">" + row.name + "</td>" + 
												"<td class=\"affiliation\">" + row.affiliation + "</td>" +
												"<td class=\"affiliation\">" + row.email + "</td>" +
												"<td><a href=\"javascript:void(0)\" onclick=\"ExecuteReviewerInvitation(1, \'/lib/ajax_srv/invite_reviewers_srv.php' . '\', {_returnFormStaticField(documentid)}, " + row.id + ");\">add to list</a></td>"
												)
										.appendTo( table );
									return TR;
								};
								
								$(document).ready(function () {
									$("#reviewer_search").autocomplete({
										source: "' . SITE_URL . 'lib/ajax_srv/usr_autocomplete_srv.php?action=get_subject_editors",
										autoFocus: true,
										minLength: 3, 
										select: function(){
											$("#reviewer_search").val("");
											return false;
										}
										
									});
								});
								//]]>
								</script>
						</div>
					</div>
				</div>
				<div class="reviewers_footer_content_middle" style="padding-right: 15px;">or</div>
				<div class="reviewers_footer_content_right" style="margin-top: 26px;">
					<div class="reviewers_search_btn_left"></div>
					<div class="reviewers_search_btn_middle"
					 onclick="window.location=\'/create_user.php?journal_id={_returnFormStaticField(journal_id)}&amp;mode=' . AUTHOR_ROLE . '&amp;document_id={_returnFormStaticField(documentid)}&amp;role=' . DEDICATED_REVIEWER_ROLE .
					 '\'">
						{_getstr(pjs.create_new_reviewer)}
					</div>
					<div class="reviewers_search_btn_right"></div>
					<div class="P-Clear"></div>
				</div>
				<div class="P-Clear"></div>
						<!--<div class="reviewers_footer_content_left_icon"></div>-->
				</div>
				</div>
				<div class="P-Clear"></div>
			<div class="h10"></div>
			<div class="h10"></div>
			<table width="100%" id="steps_nav">
				<tr><td style="text-align: right; width: 50%">{back}</td><td>{save}</td></tr>
			</table>
		</div>
	',
	
	'create_document.submission_step_title' => '
		<h1 class="dashboard-title">{title}</h1>
	',
	
	'create_document.submission_document_reviewers_row' => '
			<tr>
				<td>{author_name}</td>
				<td class="sm_font">{email}</td>
				<td class="sm_font"><a href="javascript:void(0);" onclick="ExecuteReviewerInvitation(2, \'/lib/ajax_srv/invite_reviewers_srv.php' . '\', {document_id}, {user_id}, {invitation_id});">' . getstr('pjs.Remove') . '</a></td>
			</tr>
	',
	
	'create_document.submission_document_reviewers_startrs' => '
		
	',
	
	'create_document.submission_document_reviewers_header' => '
		<table cellpadding="0" cellspacing="0" class="reviewer_tbl" width="100%">
			<colgroup>
				<col width="382px" />
				<col width="380px" />
				<col width="215px" />
			</colgroup>
			<tr>
				<th align="left">{_getstr(pjs.reviewers_name_label)}</th>
				<th align="left">{_getstr(pjs.reviewers_email_label)}</th>
				<th>{_getstr(pjs.reviewers_action_label)}</th>
			</tr>
	',
	
	'create_document.submission_document_reviewers_endrs' => '
		
	',
	
	'create_document.submission_document_reviewers_footer' => '
		</table>
	',
	
	'create_document.submission_document_reviewers_empty' => '
		<tr>
			<td colspan="3" style="padding-left: 10px">' . getstr('pjs.reviewers_empty') . '</td>
		</tr>
	',
	
	'create_document.form_step_3_pwt_document_data' => '
	
		<h2>{name}</h2>
		{authors_names}
		<br/><br/>
		<div style="font-size:12px;">
			<i>
				* Corresponding author
				<br/>
				Please note that only the submitting author ({submitting_author_name}) can handle this manuscript through in the journal system.
			</i>
		</div>
		
		<h4>Abstract</h4>
			<p>{abstract}</p>
		
		<h4>Keywords</h4>
			<p>{_render_else(keywords, none)}</p>
		<h4>Index terms</h4>
			<h5>Taxon</h5>
				<p>{taxon_categories}</p>
			<h5>Geo-spatial coverage</h5>
				<p>{_render_else(geographical_categories, none)}</p>
			<h5>Subject coverage, method or approach</h5>
				<p>{_render_else(subject_categories, none)}</p>
			<h5>Chronological or historical coverage</h5>
				<p>{_render_else(chronological_categories, none)}</p>
			<h5>Supporting Agencies</h5>
				<p>{supporting_agencies}<br />
		{supporting_agencies_txts}</p>
	',
/*	
	'create_document.permissions_form_step_1_pwt' => '{document_id} {step}
		STEP 1:Start your submission
		<br/><br/>
		Submission preparation checklist
		<div class="formErrors" style="color:red">{~}{~~}</div>

		<br/><br/>
		Indicate that this submission is ready to be considered for this journal by checking off the following conditions.
		<br/>
		{preparation_checklist}
		<br/><br/>
		Copyright Notice
		<br/><br/>
		Authors who publish with this journal agree to the following terms:
			<br/><br/>
		    1. Authors retain copyright and grant the journal right of first publication with the work simultaneously licensed under a Creative Commons Attribution License that allows others to share the work with an acknowledgement of the work\'s authorship and initial publication in this journal.
			<br/><br/>
		    2. Authors are able to enter into separate, additional contractual arrangements for the nonexclusive distribution of the journal\'s published version of the work (e.g., post it to an institutional repository or publish it in a book), with an acknowledgement of its initial publication in this journal.
			<br/><br/>
		    3. Authors are permitted and encouraged to post their work online (e.g., in institutional repositories or on their website) prior to and during the submission process, as it can lead to productive exchanges, as well as earlier and greater citation of published work .
		<br/><br/>
		{terms_agreement}
		<br/><br/>
		{save}
	',

	'create_document.permissions_form_step_2_pwt' => '{document_id} {step}
		STEP 2:Start your submission
		<br/><br/>
		Open access fee checklist
		<div class="formErrors" style="color:red">{~}{~~}</div>

		<br/><br/>
		Accepted manuscripts are subject to Open Access Fee on per-page basis with option to discount/waive them at strictly defined conditions, listed below.
		<br/><br/>
		Should you have any enquiries regarding payment of open access fee, please contact the Managing Editor (comments to the editor can also be added below).
		<br/><br/>
		{agree_to_cover_all_taxes}
		{want_15_discount}
		<div style="padding-left:15px">{fifteen_discount_reasons}</div>
		{want_10_discount}
		<div style="padding-left:15px">{ten_discount_reasons}</div>
		{want_waiver_discount}
		<div style="padding-left:15px">{waiver_discount_reasons}</div>
		{use_special_conditions}
		<br/><br/>
		Who is the person to be charged for the Open Access Fee ?
		<br/><br/>
		{person_to_charge}
		<br/><br/>
		Please specify the person to be charged (name, email, postal address)
		<br/>
		{person_to_charge_name}
		<br/><br/>
		Journal\'s Privacy Statement
		<br/><br/>
		The names and email addresses entered in this journal site will be used exclusively for the stated purposes of this journal and will not be made available for any other purpose or to any other party.
		<br/><br/>
		Comments for the Editor
		<br/><br/>
		Enter text (optional)
		(e.g., suggest reviewers or other comments)
		<br/>
		<script type="text/javascript">initPwtDocumentStep2Permissions(\'{@form_name}\')</script>
		{comments}
		<br/><br/>
		{back} {save}
	',
*/
	'create_document.success_msg' => '
		The document has been submitted successfully
		<script type="text/javascript">
			setTimeout(\'window.location = "/view_document.php?id={document_id}&view_role={view_role}&event_id={event_id}";\', 2000);
		</script>
	',
);

?>