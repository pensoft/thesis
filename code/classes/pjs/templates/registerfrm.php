<?php
$gTemplArr = array(
	
	'registerfrm.form_step1' => '
			{userid}{editprofile}
			<div class="P-Registration-Content-Steps">
				<div class="">
					{_getFormHeaderStep1(editprofile)}
				</div>
			</div>
			<div class="P-Registration-Content-Fields">
				<div class="loginFormRegErrHolder">{~}{~~}</div>
				<div class="input-reg-title">{*email} <span class="txtred">*</span></div>
				<div class="P-Input-Full-Width P-W100 fieldHolder">
						{email}
					</div>
				<div class="P-Clear"></div>
				
				<div class="P-Input-Full-Width P-W300 P-Left fieldHolder">
					<div class="input-reg-title">{*password} <span class="txtred">*</span></div>
						{password}
				</div>
				<div class="P-Input-Full-Width P-W300 P-LeftInputM fieldHolder">
					<div class="input-reg-title">{*password2} <span class="txtred">*</span></div>
						{password2}
				</div>
				<div class="P-Clear"></div>
				{_getFormButtonStep1(editprofile)}
			</div>
			<div class="P-Clear"></div>	
	',
	
	'registerfrm.form_step2' => '
			{userid}{photoid}{regstep}{editprofile}
		<div class="P-Registration-Content-Steps">
				<div class="">
					{_getFormHeaderStep2(editprofile)}
				</div>
				{success}
				{_showSeuccessMsg(success)}
			</div>
			<div class="P-Registration-Content-Fields">
				<div class="loginFormRegErrHolder">{_getBaseRegError(err_cnt)}</div>
				<div class="P-Left-Col-Fields">
					<div class="P-Input-Full-Width P-W300 fieldHolder">
						<div class="input-reg-title">{*firstname} <span class="txtred">*</span></div>
						<div class="loginFormRegErrHolder">
							{!firstname}
						</div>
							{firstname}
					</div>
					<div class="P-Input-Full-Width P-W300 fieldHolder">
						<div class="input-reg-title">{*middlename}</div>
							{middlename}
					</div>
					<div class="P-Input-Full-Width P-W300 fieldHolder">
						<div class="input-reg-title">{*lastname} <span class="txtred">*</span></div>
						<div class="loginFormRegErrHolder">
							{!lastname}
						</div>
							{lastname}
					</div>
				</div>
				<div class="P-Right-Col-Fields">
					<div class="P-Right-Col-Fields-Col">
						<div class="P-Right-Col-Fields-Client-Type">
							<div id="P-Reg-Profile-Pic">
								<span id="status"></span>
							</div>
							<div id="changeprofpic" class="P-Profile-Picture-Holder" href="javascript:void(0)">
								{_getProfilePic(photo_id)}
								<div class="P-Clear"></div>
							</div>
							<script type="text/javascript">
								 $(function(){
									var usrid = $(\'#userid\').val();
									var btnUpload = $(\'#changeprofpic\');
									var status = $(\'#status\');
									new AjaxUpload(btnUpload, {
										action: \'/uploadprofilepic.php\',
										name: \'uploadfile\',
										data: {
											userid: usrid,
										},
										onSubmit: function(file, ext){
											 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
												// extension is not allowed 
												status.text(\'Only JPG, PNG or GIF files are allowed\');
												return false;
											}
											status.text(\'Uploading...\');
										},
										onComplete: function(file, response){
											//On completion clear the status
											status.text(\'\');
											//Add uploaded file to list
											if(response != 0){
												$(\'#Prof-Photo\').html(response);
											} else{
												$(\'#Prof-Photo\').html(\'error uploading file\');
											}
										}
									});
								});       
							</script>
						</div>
						<div class="P-Input-Full-Width P-W80 fieldHolder">
							<div class="input-reg-title">{*usrtitle} <span class="txtred">*</span></div>
							<div class="loginFormRegErrHolder">
								{!usrtitle}
							</div>
								{usrtitle}
						</div>
					</div>
					<div class="P-Right-Col-Fields-ColRadios">
						<div class="P-Right-Col-Fields-Client-Type-Txt">
							<div class="input-title clearpadding">{*clienttype} <span class="txtred">*</span></div>							
						</div>
						<div class="loginFormRegErrHolder">
							{!clienttype}
						</div>
						<div class="P-Clear"></div>
						{clienttype}
					</div>
				</div>
				<div class="P-Clear"></div>
				<div class="P-Inline-Line ptop30"></div>
				<div class="P-Left-Col-Fields">
					<div class="P-Input-Full-Width P-W300 fieldHolder">
						<div class="input-reg-title">{*affiliation} <span class="txtred">*</span></div>
						<div class="loginFormRegErrHolder">
							{!affiliation}
						</div>
							{affiliation}
					</div>
					<div class="P-Input-Full-Width P-W300 fieldHolder">
						<div class="input-reg-title">{*addrstreet} <span class="txtred">*</span></div>
						<div class="loginFormRegErrHolder">
							{!addrstreet}
						</div>
							{addrstreet}
					</div>
					<div class="P-Input-Full-Width P-W300 fieldHolder">
						<div class="input-reg-title">{*city} <span class="txtred">*</span></div>
						<div class="loginFormRegErrHolder">
							{!city}
						</div>
						{city}
					</div>
					<div class="P-Input-Full-Width P-W300 fieldHolder">
						<div class="input-reg-title">{*phone} </div>
							{phone}
					</div>
					<div class="input-reg-title">{*vatnumber}</div>	
					<div class="P-Input-Full-Width P-W300 P-Input-With-Help fieldHolder">
						<div class="P-Input-Inner-Wrapper">
							{vatnumber}
						</div>
						<div class="P-Input-Help">
							<div class="P-Baloon-Holder">
								<div class="P-Baloon-Arrow"></div>
								<div class="P-Baloon-Top"></div>
								<div class="P-Baloon-Middle">
									<div class="P-Baloon-Content">
										 If present, for EU-based users only!
									</div>
									<div class="P-Clear"></div>
								</div>
								<div class="P-Baloon-Bottom"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="P-Right-Col-Fields">
					<div class="P-Input-Full-Width P-W300 fieldHolder">
						<div class="input-reg-title">{*department} </div>
							{department}
					</div>
					<div class="input-reg-title">{*postalcode}<span class="txtred">*</span></div>
					<div class="loginFormRegErrHolder">
						{!postalcode}
					</div>
					<div class="P-Input-Full-Width P-W80 P-Input-With-Help fieldHolder">
							{postalcode}
						<div class="P-Input-Help" style="margin-right: -12px;">
							<div class="P-Baloon-Holder">
								<div class="P-Baloon-Arrow"></div>
								<div class="P-Baloon-Top"></div>
								<div class="P-Baloon-Middle">
									<div class="P-Baloon-Content">
										Add also State, if present
									</div>
									<div class="P-Clear"></div>
								</div>
								<div class="P-Baloon-Bottom"></div>
							</div>
						</div>
					</div>
					<div class="P-Input-Full-Width P-W300 fieldHolder">
						<div class="input-reg-title">{*country} <span class="txtred">*</span></div>
						<div class="loginFormRegErrHolder">
							{!country}
						</div>
						<script type="text/javascript">
							$(document).ready(function(){
								$("#P-RegFld-Country .disabled").attr("disabled","disabled");
							});
						</script>
						{country}
					</div>
					<div class="P-Input-Full-Width P-W300 fieldHolder">
						<div class="input-reg-title">{*fax} </div>
							{fax}
					</div>
					<div class="P-Input-Full-Width P-W300 fieldHolder">
						<div class="input-reg-title">{*website} </div>
							{website}
					</div>
				</div>
				<div class="P-Clear"></div>
				{_getFormButtonStep2(editprofile)}
			</div>
			<div class="P-Clear"></div>
	',
	
	'registerfrm.form_step3' => '
		{userid}{regstep}{editprofile}
		<div id="P-Registration-Content3" class="Step3">
			<div class="P-Registration-Content-Steps">
				<div class="">
					{_getFormHeaderStep3(editprofile)}
				</div>
			</div>
			<div class="P-Registration-Content-Fields">
				<div class="loginFormRegErrHolder">
					{~~}
				</div>
				<div class="P-Registration-Content-Left-Fields">
						<div class="input-title fbold">{*producttypes} <span class="txtred">*</span></div>
					<div class="P-Product-Type-Checkboxes" id="product_types_holder">
						<div class="P-Registration-Email-Alerts-Journal-Checks">
							<input id="checkall" type="checkbox" name="producttype" class="nomargin" value="All" /> All
						</div>
						{producttypes}
						<div class="P-Clear"></div>						
					</div>
					<script type="text/javascript">
						$(document).ready(function(){
							$(\'#product_types_holder\').find(\':checkbox\').not(\':first\').click(function() {
								var notChecked = 0;
								$(\'#product_types_holder\').find(\':checkbox\').not(\':first\').each(function(){
									if($(this).is(\':checked\')){
									
									}else{
										notChecked = 1;
									}
								});
								if(notChecked)
									$("#checkall").attr(\'checked\', false);
								else
									$("#checkall").attr(\'checked\', true);
							});
							$("#checkall").click(function() {
								if($("#checkall").attr(\'checked\')) {
									$(\'#product_types_holder\').find(\':checkbox\').not(\':first\').each( function() {
										$(this).attr(\'checked\', true);
									});
								} else {
									$(\'#product_types_holder\').find(\':checkbox\').not(\':first\').each( function() {
										$(this).attr(\'checked\', false);
									});
								}
							});
						});
					</script>
					<div class="P-Registration-Content-Interest-Head">Area(s) of interest</div>
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
					<div class="P-Input-Full-Width P-W390">
						<div class="input-reg-title fbolddark">{*alerts_chronical_cats}</div>
							{alerts_chronical_cats}						
					</div>
					<!-- Tree alerts_chronical_cats -->
					<div id="treealerts_chronical_cats">
						{^chronological_tree}
					</div>
					<!-- Tree #2 END -->
					{^chronological_tree_script}
					<script type="text/javascript">
						// Тук популираме първоначално инпута със селектнатите стойности или ако има грешка при сейв със селектнатите + новоселектнатите преди сейв
						var lSelectedCats =  new Array();
						lSelectedCats = {_json_encode(chronological_selected_vals)};
						var InputVal = new Array();
						for ( var i = 0; i < lSelectedCats.length; i++) {
							$("#alerts_chronical_cats_autocomplete").tokenInput("add", lSelectedCats[i]);
						}
					</script>
					<div class="P-Clear"></div>
				</div>
				<div class="P-Registration-Content-Right-Fields">
						<div class="input-title fbolddark">{*alertsfreq} <span class="txtred">*</span></div>					
					{alertsfreq}
					<div class="P-Alerts-Radios"></div>
					<div class="P-Clear"></div>
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
					<div class="P-Input-Full-Width P-W390">
						<div class="input-reg-title fbolddark">{*alerts_geographical_cats}</div>
							{alerts_geographical_cats}
					</div>
					<!-- Tree alerts_geographical_cats -->
					<div id="treealerts_geographical_cats">
						{^geographical_tree}
					</div>
					{^geographical_tree_script}
					<script type="text/javascript">
							// Тук популираме първоначално инпута със селектнатите стойности или ако има грешка при сейв със селектнатите + новоселектнатите преди сейв
						var lSelectedCats =  new Array();
						lSelectedCats = {_json_encode(geographical_selected_vals)};
						var InputVal = new Array();
						for ( var i = 0; i < lSelectedCats.length; i++) {
							$("#alerts_geographical_cats_autocomplete").tokenInput("add", lSelectedCats[i]);
						}
					</script>
					<!-- Tree #4 END -->
					<div class="P-Clear"></div>
				</div>
				<div class="P-Clear"></div>
				<div class="P-Registration-Email-Alerts-Journal">
					<div class="P-Input-Holder">
						<div class="input-title fbold">{*journals}</div>
						{journals}
						<div class="P-Clear"></div>
					</div>
				</div>
				<div class="P-Registration-RSS-Feed">
					<div class="P-Right-Col-Fields-Client-Type-Txt">
						<div class="fbolddark">RSS Feed</div>
					</div>
					<div class="P-Registration-RSS-Feed-Links">
						<div class="P-Registration-RSS-Feed-IconLink">
							<a href="#"><img src="i/RSS_icon.png"></img></a>
						</div>
						<div class="P-Registration-RSS-Feed-Txt">To read the RSS feeds for your Area(s) of interest, please copy and paste the link below in your favourite Feed reader</div>
						<div class="P-Registration-RSS-Feed-Txt"><a href="#">http://www.pensoft.net/rss/rsscust.php</a></div>
						<div class="P-Clear"></div>
					</div>
				</div>
				{_getFormButtonStep3(editprofile)}
			</div>
			<div class="P-Clear"></div>
		</div>
	',
	
	'registerfrm.form_my_expertise' => '
		{userid}{regstep}{editprofile}
		<div id="P-Registration-Content3" class="Step3">
			<div class="P-Registration-Content-Steps">
				<div class="dashboardTitle">
					<h2>' . getstr('pjs.myexpertise') . '</h2>
				</div>
			</div>
			<div class="P-Registration-Content-Fields">
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
					<div class="P-Input-Full-Width P-W390">
						<div class="input-reg-title fbolddark">{*alerts_chronical_cats}</div>
							{alerts_chronical_cats}						
					</div>
					<!-- Tree alerts_chronical_cats -->
					<div id="treealerts_chronical_cats">
						{^chronological_tree}
					</div>
					<!-- Tree #2 END -->
					{^chronological_tree_script}
					<script type="text/javascript">
						// Тук популираме първоначално инпута със селектнатите стойности или ако има грешка при сейв със селектнатите + новоселектнатите преди сейв
						var lSelectedCats =  new Array();
						lSelectedCats = {_json_encode(chronological_selected_vals)};
						var InputVal = new Array();
						for ( var i = 0; i < lSelectedCats.length; i++) {
							$("#alerts_chronical_cats_autocomplete").tokenInput("add", lSelectedCats[i]);
						}
					</script>
					<div class="P-Clear"></div>
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
						// Тук популираме първоначално инпута със селектнатите стойности или ако има грешка при сейв със селектнатите + новоселектнатите преди сейв
						var lSelectedCats =  new Array();
						lSelectedCats = {_json_encode(taxon_selected_vals)};
						var InputVal = new Array();
						for ( var i = 0; i < lSelectedCats.length; i++) {
							$("#alerts_taxon_cats_autocomplete").tokenInput("add", lSelectedCats[i]);
						}
					</script>
					<!-- Tree #3 END -->
					<div class="P-Input-Full-Width P-W390">
						<div class="input-reg-title fbolddark">{*alerts_geographical_cats}</div>
							{alerts_geographical_cats}
					</div>
					<!-- Tree alerts_geographical_cats -->
					<div id="treealerts_geographical_cats">
						{^geographical_tree}
					</div>
					{^geographical_tree_script}
					<script type="text/javascript">
						// Тук популираме първоначално инпута със селектнатите стойности или ако има грешка при сейв със селектнатите + новоселектнатите преди сейв
						var lSelectedCats =  new Array();
						lSelectedCats = {_json_encode(geographical_selected_vals)};
						var InputVal = new Array();
						for ( var i = 0; i < lSelectedCats.length; i++) {
							$("#alerts_geographical_cats_autocomplete").tokenInput("add", lSelectedCats[i]);
						}
					</script>
					<!-- Tree #4 END -->
					<div class="P-Clear"></div>
				</div>
				<div class="P-Clear"></div>
				{_getFormButtonStep3(editprofile)}
			</div>
			<div class="P-Clear"></div>
		</div>
	',
	
	'registerfrm.registerfrmwrappersuccess' => '
		<div class="loginFormRightCol">
			<div class="loginFormRegErrHolder"><!--###ERRORS###--></div>
			<div class="loginFormRowHolder">
				<div class="loginFormTxt">Registration is completed. Please check your mail.</div>
				<div class="P-Clear"></div>
			</div>
		</div>
	',
	
	'registerfrm.editstep1success' => '
		<div class="loginFormTxt"><br/>{success_msg}</div>
		<div class="P-Clear"></div>
	',
	
	'registerfrm.mailcontent' => '
		<html>
		<body>
			Dear {user_fullname}, <br /><br/>
			You\'re only one step away from creating your Pensoft Writing Tool account. Just click on the link below or cut and paste it into your web browser to confirm this is a working email address:			
			<br /><br/>
			<a href="{siteurl}/confmail.php?hash={confhash}">{siteurl}/confmail.php?hash={confhash}</a>
			<br/><br/>
			<a title="Pensoft Writing Tool" href="{siteurl}">The Pensoft PWT team</a>
		</body>
		</html> 
	',
	
	'registerfrm.setcookie' => '
		<img src="' . PWT_SITE_URL . 'lib/set_autologin_cookie.php?autologin_hash={hash}&logout={logout}" width="1" height="1" border="0" alt="cookie">
		<img src="' . PWT_SITE_URL . 'login.php?logout={logout}" width="1" height="1" border="0">
		<img src="' . OLD_PJS_SITE_URL . 'set_autologin_cookie.php?autologin_hash={hash}&logout={logout}" width="1" height="1" border="0" alt="cookie">
		{_RetOldPjsLogoutImg(logout)}
		<script type="text/javascript">
			$(window).load(function() {
				window.location = \'{redirurl}\';
			});
		</script>
	'
);

?>