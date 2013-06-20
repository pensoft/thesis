<?php

$gTemplArr = array(
	'createuser.create_user_form_ajax' => '{contents}',
	'createuser.form' => '{upass}{event_id}
		<h1 class="dashboard-title">' . getstr('pjs.create_user') . '</h1>
		<div class="leftMar10">
			<br/>
			{~}{~~}{guid}{journal_id}
			{mode}{documentid}{roundid}{role}{roles}
			<div class="input-reg-title">{*email} <span class="txtred">*</span></div>
			<div class="P-Input-Full-Width P-W300 fieldHolder">
				{email}
			</div>
			<div class="P-Clear"></div>
			
			<div class="input-reg-title">{*usrtitle}</div>
			<div class="P-Input-Full-Width P-W300 fieldHolder P-SelectHolder">
				{usrtitle}
			</div>
			<div class="P-Clear"></div>
			
			<div class="input-reg-title">{*firstname} <span class="txtred">*</span></div>
			<div class="P-Input-Full-Width P-W300 fieldHolder">
				{firstname}	
			</div>
			<div class="P-Clear"></div>
			
			<div class="input-reg-title">{*lastname} <span class="txtred">*</span></div>
			<div class="P-Input-Full-Width P-W300 fieldHolder">
				{lastname}
			</div>
			<div class="P-Clear"></div>
			
			<div class="input-reg-title">{*affiliation} <span class="txtred">*</span></div>
			<div class="P-Input-Full-Width P-W300 fieldHolder">
				{affiliation}
			</div>
			<div class="P-Clear"></div>
			
			<div class="input-reg-title">{*city} <span class="txtred">*</span></div>
			<div class="P-Input-Full-Width P-W300 fieldHolder">
				{city}
			</div>
			<div class="P-Clear"></div>
			
			<div class="input-reg-title">{*country} <span class="txtred">*</span></div>
			<div class="P-Input-Full-Width P-W300 fieldHolder P-SelectHolder">
				{country}	
				<script>
					DisableOptionByValue(\'#countries\');
				</script>
			</div>			
			<br/>
			{_showExpertises()}
			<div id="user_roles_checkbox" onclick="showCategoriesIfChecked(\'user_roles_checkbox\', \'categories_holder\');">
				{user_roles}
			</div>
			
			<div class="P-Clear"></div>
			<div id="categories_holder" style="display: none;">
				<br/>
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
					<script type="text/javascript">//<![CDATA[
						var lRole;
						lRole = $(\'#user_roles_checkbox input:eq(2)\');
						if((lRole).is(\':checked\'))
							$(\'#categories_holder\').css(\'display\', \'block\');
						// Тук популираме първоначално инпута със селектнатите стойности или ако има грешка при сейв със селектнатите + новоселектнатите преди сейв
						var lSelectedCats =  new Array();
						lSelectedCats = {_json_encode(subject_selected_vals)};
						var InputVal = new Array();
						for ( var i = 0; i < lSelectedCats.length; i++) {
							$("#alerts_subject_cats_autocomplete").tokenInput("add", lSelectedCats[i]);
						}
						//]]>
					</script>
					<div class="P-Clear"></div>
					<div class="P-Input-Full-Width P-W390 spacer10t">
						<div class="input-reg-title fbolddark">{*alerts_geographical_cats}</div>
							{alerts_geographical_cats}
						</div>
						<!-- Tree alerts_geographical_cats -->
						<div id="treealerts_geographical_cats" class="filterBy">
							{^geographical_tree}
						</div>
						{^geographical_tree_script}
						<script type="text/javascript">//<![CDATA[
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
						<!-- Tree #2 END -->
				</div>
				<div class="P-Registration-Content-Right-Fields">
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
						//<![CDATA[
						// Тук популираме първоначално инпута със селектнатите стойности или ако има грешка при сейв със селектнатите + новоселектнатите преди сейв
						var lSelectedCats =  new Array();
						lSelectedCats = {_json_encode(taxon_selected_vals)};
						var InputVal = new Array();
						for ( var i = 0; i < lSelectedCats.length; i++) {
							$("#alerts_taxon_cats_autocomplete").tokenInput("add", lSelectedCats[i]);
						}
						//]]>
					</script>
			</div>
			<div class="P-Clear"></div>
				</div>
				<div class="P-Clear"></div>
				<!-- Tree #3 END -->
			</div>
			<div class="P-Clear"></div>
			<div class="buttonsHolder">
				<div class="P-Grey-Btn-Holder P-Reg-Btn">
					<div class="P-Grey-Btn-Left"></div>
					<div class="P-Grey-Btn-Middle">
						{save}
					</div>
					<div class="P-Grey-Btn-Right"></div>
				</div>
				<div class="P-Grey-Btn-Holder P-Reg-Btn">
					<div class="P-Grey-Btn-Left"></div>
					<div class="P-Grey-Btn-Middle">
						{cancel}
					</div>
					<div class="P-Grey-Btn-Right"></div>
				</div>
			</div>
			<div class="clear"></div>
			<br/>
			<script type="text/javascript">
				showCategoriesIfChecked(\'user_roles_checkbox\', \'categories_holder\')
			</script>
			<div class="P-Clear"></div>
		</div>
	',

	'createuser.userexpertisesfrm' => '
		{se_uid}{journal_id}{document_id}
		<div class="P-Registration-Content-Fields">
			<div class="loginFormRegErrHolder">{~}{~~}</div>
			{_showExpertises()}
			
			<div class="P-Clear"></div>
			<div id="categories_holder">
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
					<script type="text/javascript">//<![CDATA[
						$(\'#categories_holder\').css(\'display\', \'block\');
						// Тук популираме първоначално инпута със селектнатите стойности или ако има грешка при сейв със селектнатите + новоселектнатите преди сейв
						var lSelectedCats =  new Array();
						lSelectedCats = {_json_encode(subject_selected_vals)};
						var InputVal = new Array();
						for ( var i = 0; i < lSelectedCats.length; i++) {
							$("#alerts_subject_cats_autocomplete").tokenInput("add", lSelectedCats[i]);
						}
						//]]>
					</script>
					<div class="P-Clear"></div>
					<div class="P-Input-Full-Width P-W390 spacer10t">
						<div class="input-reg-title fbolddark">{*alerts_geographical_cats}</div>
							{alerts_geographical_cats}
					</div>
					<!-- Tree alerts_geographical_cats -->
					<div id="treealerts_geographical_cats" class="filterBy">
						{^geographical_tree}
					</div>
					{^geographical_tree_script}
					<script type="text/javascript">//<![CDATA[
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
					<!-- Tree #2 END -->
				</div>
				<div class="P-Registration-Content-Right-Fields">
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
						//<![CDATA[
						// Тук популираме първоначално инпута със селектнатите стойности или ако има грешка при сейв със селектнатите + новоселектнатите преди сейв
						var lSelectedCats =  new Array();
						lSelectedCats = {_json_encode(taxon_selected_vals)};
						var InputVal = new Array();
						for ( var i = 0; i < lSelectedCats.length; i++) {
							$("#alerts_taxon_cats_autocomplete").tokenInput("add", lSelectedCats[i]);
						}
						//]]>
					</script>
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="invite_reviewer_btn invite_reviewer_btn_E invite_reviewer_btn_E_first" style="float:left;width:155px;">
				<div class="invite_reviewer_btn_left"></div>
				<div onclick="PerformUserExpertisesAction(\'save\', \'P-Registration-Content\')" class="invite_reviewer_btn_middle" style="width:147px;">Save</div>
				<div class="invite_reviewer_btn_right"></div>
				<div class="P-Clear"></div>
			</div>
			<div class="P-Clear"></div>
		</div>
		
		
	',
);

?>