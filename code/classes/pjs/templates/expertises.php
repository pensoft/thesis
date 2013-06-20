<?php

$gTemplArr = array(
	
	'expertises.user_journal_expertises_form' => '
		{user_id}{journal_id}
		<h1 class="dashboard-title">' . getstr('pjs.manage_user_journal_expertises') . '</h1>
		<div style="margin: 10px;" class="P-Registration-Content-Fields">
			<div class="loginFormRegErrHolder">{_getBaseRegError(err_cnt)}</div>
			<div class="P-Registration-Content-Left-Fields">
				<div class="P-Input-Full-Width P-W390">
					<div class="input-reg-title fbolddark">{*alerts_subject_cats}</div>
						{alerts_subject_cats}						
				</div>
				<!-- Tree alerts_subject_cats -->
				<div id="treealerts_subject_cats">
					{^subjects_tree}
				</div>
				<!-- Tree #1 END -->
				{^subjects_tree_script}
				<script type="text/javascript">
					// Тук популираме първоначално инпута със селектнатите стойности или ако има грешка при сейв със селектнатите + новоселектнатите преди сейв
					var lSelectedCats =  new Array();
					lSelectedCats = {_json_encode(subject_selected_vals)};
					var InputVal = new Array();
					for ( var i = 0; i < lSelectedCats.length; i++) {
						$("#alerts_subject_cats_autocomplete").tokenInput("add", lSelectedCats[i]);
					}
				</script>
			</div>
			
			<div class="P-Registration-Content-Left-Fields">
				<div class="P-Input-Full-Width P-W390 spacer10t">
					<div class="input-reg-title fbolddark">{*alerts_taxon_cats}</div>
						{alerts_taxon_cats}
				</div>
				<!-- Tree alerts_taxon_cats -->
				<div id="treealerts_taxon_cats">
					{^taxon_tree}
				</div>
				{^taxon_tree_script}
				<script type="text/javascript">
					// Тук популираме първоначално инпута със селектнатите стойности или ако има грешка при сейв със селектнатите + новоселектнатите преди сейв
					var lSelectedCats =  new Array();
					lSelectedCats = {_json_encode(taxon_selected_vals)};
					var InputVal = new Array();
					for ( var i = 0; i < lSelectedCats.length; i++) {
						$("#alerts_taxon_cats_autocomplete").tokenInput("add", lSelectedCats[i]);
					}
				</script>
				<!-- Tree #3 END -->
			</div>
			<div class="clear"></div>
			<div class="P-Registration-Content-Right-Fields clearMargin">
				<div class="P-Input-Full-Width P-W390">
					<div class="input-reg-title fbolddark">
						{*alerts_geographical_cats}
					</div>
					{alerts_geographical_cats}
				</div>
				<!-- Tree alerts_geographical_cats -->
				<div id="treealerts_geographical_cats" class="filterBy">
					{^geographical_tree}
				</div>
				{^geographical_tree_script}
				<script type="text/javascript">
					// Тук популираме първоначално инпута със селектнатите стойности или ако има грешка при сейв със селектнатите + новоселектнатите преди сейв
					var lSelectedCats =  new Array();
					lSelectedCats = {_json_encode(geographical_selected_vals)};
					var InputVal = new Array();
					if(!lSelectedCats.length)
						toggleBlock(\'geographical_arrow\', \'geographical_tree\');
					for ( var i = 0; i < lSelectedCats.length; i++) {
						$("#alerts_geographical_cats_autocomplete").tokenInput("add", lSelectedCats[i]);
					}
				</script>
				<!-- Tree #2 END -->
			
				
				<div class="P-Clear"></div>
			</div>
			<div class="P-Clear"></div>
			<div class="P-Grey-Btn-Holder P-Reg-Btn">
				<div class="P-Grey-Btn-Left"></div>
				<div class="P-Grey-Btn-Middle" onclick="submitUserJournalExpertisesForm(\'user_expertises\');">Save</div>
				<div class="P-Grey-Btn-Right"></div>
			</div>
			<div class="P-Grey-Btn-Holder P-Reg-Btn-R">
				<div class="P-Grey-Btn-Left"></div>
				<div class="P-Grey-Btn-Middle" onclick="window.location.href=\'/manage_journal_users.php?journal_id={@journal_id}\';">Cancel</div>
				<div class="P-Grey-Btn-Right"></div>
			</div>
		</div>
		<div class="P-Clear"></div>
	',
	
	'expertises.frmwrappersuccess' => '
		<h1 class="dashboard-title">' . getstr('pjs.manage_user_journal_expertises') . '</h1>
		<br/>
		<div class="leftMar10">
			{success_msg}
		</div>
		<div class="P-Clear"></div>
	',
);

?>