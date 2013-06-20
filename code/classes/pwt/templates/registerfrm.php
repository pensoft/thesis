<?php

$gTemplArr = array(
	
	'registerfrm.form_step1' => '
			{userid}{editprof}
			<div class="P-Registration-Content-Steps">
				<table cellspacing="0">
					<tr>
						<td class="reg-active-step"><span {_checkProfileEdit}>step 1</span><br>{_getProfileEditStepOneMessage}</td>
						<td {_checkProfileEdit}><span>step 2</span><br>Contact information</td>
						<td {_checkProfileEdit}><span>step 3</span><br>Subscribe to email / RSS alerts</td>
					</tr>
				</table>
			</div>
			<div class="P-Registration-Content-Fields">
				<div class="loginFormRegErrHolder"><!--###ERRORS###--></div>
				<div class="input-reg-title">{*email} <span class="txtred">*</span></div>
				<div class="P-Input-Full-Width P-W300">
					<div class="P-Input-Holder">
						<div class="P-Input-Left"></div>
						<div class="P-Input-Middle">
							{email}						
						</div>
						<div class="P-Input-Right"></div>
						<div class="P-Clear"></div>
					</div>
				</div>
				<div class="P-Clear"></div>
				
				<div class="P-Input-Full-Width P-W300 P-Left">
					<div class="input-reg-title">{*password} <span class="txtred">*</span></div>
					<div class="P-Input-Holder">
						<div class="P-Input-Left"></div>
						<div class="P-Input-Middle">
							{password}						
						</div>
						<div class="P-Input-Right"></div>
						<div class="P-Clear"></div>
					</div>
				</div>
				<div class="P-Input-Full-Width P-W300 P-LeftInputM">
					<div class="input-reg-title">{*password2} <span class="txtred">*</span></div>
					<div class="P-Input-Holder">
						<div class="P-Input-Left"></div>
						<div class="P-Input-Middle">
							{password2}						
						</div>
						<div class="P-Input-Right"></div>
						<div class="P-Clear"></div>
					</div>
				</div>
				<div class="P-Clear"></div>
				{_showRegEditProfileBtnsStep1}
			</div>
			<div class="P-Clear"></div>	
	',
	
	'registerfrm.form_step2' => '
			{userid}{photoid}{regstep}{editprof}
		<div class="P-Registration-Content-Steps">
				<table cellspacing="0">
					<tr>
						<td {_checkProfileEdit}><span>step 1</span><br>Account information</td>
						<td class="reg-active-step"><span {_checkProfileEdit}>step 2</span><br>{_getProfileEditStepTwoMessage}</td>
						<td {_checkProfileEdit}><span>step 3</span><br>Subscribe to email / RSS alerts</td>
					</tr>
				</table>
			</div>
			<div class="P-Registration-Content-Fields">
			<script type="text/javascript">
					 $(function(){
						var usrid = $(\'#userid\').val();
						var btnUpload = $(\'#changeprofpic\');
						var status = $(\'#status\');
						new AjaxUpload(btnUpload, {
							action: \'/SaveProfilePic.php\',
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
				<div class="loginFormRegErrHolder"><!--###ERRORS###--></div>
				<div class="P-Left-Col-Fields">
					<div class="P-Input-Full-Width P-W300">
						<div class="input-reg-title">{*firstname} <span class="txtred">*</span></div>
						<div class="loginFormRegErrHolder">
							{$firstname}
						</div>
						<div class="P-Input-Holder">
							<div class="P-Input-Left"></div>
							<div class="P-Input-Middle">
								{firstname}						
							</div>
							<div class="P-Input-Right"></div>
							<div class="P-Clear"></div>
						</div>
					</div>
					<div class="P-Input-Full-Width P-W300">
						<div class="input-reg-title">{*middlename}</div>
						<div class="P-Input-Holder">
							<div class="P-Input-Left"></div>
							<div class="P-Input-Middle">
								{middlename}						
							</div>
							<div class="P-Input-Right"></div>
							<div class="P-Clear"></div>
						</div>
					</div>
					<div class="P-Input-Full-Width P-W300">
						<div class="input-reg-title">{*lastname} <span class="txtred">*</span></div>
						<div class="loginFormRegErrHolder">
							{$lastname}
						</div>
						<div class="P-Input-Holder">
							<div class="P-Input-Left"></div>
							<div class="P-Input-Middle">
								{lastname}						
							</div>
							<div class="P-Input-Right"></div>
							<div class="P-Clear"></div>
						</div>
					</div>
				</div>
				<div class="P-Right-Col-Fields">
					<div class="P-Right-Col-Fields-Col">
						<div class="P-Right-Col-Fields-Client-Type">
							<div id="P-Reg-Profile-Pic">
								<span id="status"></span>
							</div>
							<div id="changeprofpic" class="P-Profile-Picture-Holder" href="javascript:void(0)">
								{_GetProfPic}
								<div class="P-Clear"></div>
							</div>
						</div>
						<div class="P-Input-Full-Width P-W80">
							<div class="input-reg-title">{*usrtitle} <span class="txtred">*</span></div>
							<div class="loginFormRegErrHolder">
								{$usrtitle}
							</div>
							<div class="P-Input-Holder">
								<div class="P-Input-Left"></div>
								<div class="P-Input-Middle">
									{usrtitle}						
								</div>
								<div class="P-Input-Right"></div>
								<div class="P-Clear"></div>
							</div>
						</div>
					</div>
					<div class="P-Right-Col-Fields-ColRadios">
						<div class="P-Right-Col-Fields-Client-Type-Txt">
							<div class="input-title clearpadding">{*clienttype} <span class="txtred">*</span></div>							
						</div>
						<div class="loginFormRegErrHolder">
							{$clienttype}
						</div>
						<div class="P-Clear"></div>
						<div class="P-User-Type-Radios">
							{clienttype}
						</div>
					</div>
				</div>
				<div class="P-Clear"></div>
				<div class="P-Inline-Line ptop30"></div>
				<div class="P-Left-Col-Fields">
					<div class="P-Input-Full-Width P-W300">
						<div class="input-reg-title">{*affiliation} <span class="txtred">*</span></div>
						<div class="loginFormRegErrHolder">
							{$affiliation}
						</div>
						<div class="P-Input-Holder">
							<div class="P-Input-Left"></div>
							<div class="P-Input-Middle">
								{affiliation}						
							</div>
							<div class="P-Input-Right"></div>
							
							<div class="P-Clear"></div>
						</div>
					</div>
					<div class="P-Input-Full-Width P-W300">
						<div class="input-reg-title">{*addrstreet} <span class="txtred">*</span></div>
						<div class="loginFormRegErrHolder">
							{$addrstreet}
						</div>
						<div class="P-Input-Holder">
							<div class="P-Input-Left"></div>
							<div class="P-Input-Middle">
								{addrstreet}						
							</div>
							<div class="P-Input-Right"></div>
							<div class="P-Clear"></div>
						</div>
					</div>
					<div class="P-Input-Full-Width P-W300">
						<div class="input-reg-title">{*city} <span class="txtred">*</span></div>
						<div class="loginFormRegErrHolder">
							{$city}
						</div>
						<div class="P-Input-Holder">
							<div class="P-Input-Left"></div>
							<div class="P-Input-Middle">
								{city}						
							</div>
							<div class="P-Input-Right"></div>
							<div class="P-Clear"></div>
						</div>
					</div>
					<div class="P-Input-Full-Width P-W300">
						<div class="input-reg-title">{*phone} </div>
						<div class="P-Input-Holder">
							<div class="P-Input-Left"></div>
							<div class="P-Input-Middle">
								{phone}						
							</div>
							<div class="P-Input-Right"></div>
							<div class="P-Clear"></div>
						</div>
					</div>
					<div class="input-reg-title">{*vatnumber}</div>	
					<div class="P-Input-Full-Width P-W300 P-Input-With-Help">
						<div class="P-Input-Inner-Wrapper">
							<div class="P-Input-Holder">
								<div class="P-Input-Left"></div>
								<div class="P-Input-Middle">
									{vatnumber}						
								</div>
								<div class="P-Input-Right"></div>
								<div class="P-Clear"></div>
							</div>
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
					<div class="P-Input-Full-Width P-W300">
						<div class="input-reg-title">{*department} </div>
						<div class="P-Input-Holder">
							<div class="P-Input-Left"></div>
							<div class="P-Input-Middle">
								{department}						
							</div>
							<div class="P-Input-Right"></div>
							<div class="P-Clear"></div>
						</div>
					</div>
					<div class="input-reg-title">{*postalcode}<span class="txtred">*</span></div>
					<div class="loginFormRegErrHolder">
						{$postalcode}
					</div>
					<div class="P-Input-Full-Width P-W80 P-Input-With-Help">
						<div class="P-Input-Inner-Wrapper">
							<div class="P-Input-Holder">
								<div class="P-Input-Left"></div>
								<div class="P-Input-Middle">
									{postalcode}						
								</div>
								<div class="P-Input-Right"></div>
								<div class="P-Clear"></div>
							</div>
						</div>
						<div class="P-Input-Help">
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
					<div class="P-Input-Full-Width P-W300">
						<div class="input-reg-title">{*country} <span class="txtred">*</span></div>
						<div class="loginFormRegErrHolder">
							{$country}
						</div>
						<script type="text/javascript">
							$(document).ready(function(){
								$("#P-RegFld-Country .disabled").attr("disabled","disabled");
							});
						</script>
						<div class="P-Input-Holder">
							<div class="P-Input-Left"></div>
							<div class="P-Input-Middle">
								{country}						
							</div>
							<div class="P-Input-Right"></div>
							<div class="P-Clear"></div>
						</div>
					</div>
					<div class="P-Input-Full-Width P-W300">
						<div class="input-reg-title">{*fax} </div>
						<div class="P-Input-Holder">
							<div class="P-Input-Left"></div>
							<div class="P-Input-Middle">
								{fax}						
							</div>
							<div class="P-Input-Right"></div>
							<div class="P-Clear"></div>
						</div>
					</div>
					<div class="P-Input-Full-Width P-W300">
						<div class="input-reg-title">{*website} </div>
						<div class="P-Input-Holder">
							<div class="P-Input-Left"></div>
							<div class="P-Input-Middle">
								{website}						
							</div>
							<div class="P-Input-Right"></div>
							<div class="P-Clear"></div>
						</div>
					</div>
				</div>
				<div class="P-Clear"></div>
				{_showRegEditProfileBtnsStep2}
			</div>
			<div class="P-Clear"></div>
	',
	
	'registerfrm.form_step3' => '
		{userid}{regstep}{editprof}
		<div id="P-Registration-Content3" class="Step3">
			<div class="P-Registration-Content-Steps">
				<table cellspacing="0">
					<tr>
						<td {_checkProfileEdit}><span>step 1</span><br>Account information</td>
						<td {_checkProfileEdit}><span>step 2</span><br>Contact information</td>
						<td class="reg-active-step"><span {_checkProfileEdit}>step 3</span><br>{_getProfileEditStepThreeMessage}</td>
					</tr>
				</table>
			</div>
			<div class="P-Registration-Content-Fields">
				<div class="loginFormRegErrHolder"><!--###ERRORS###--></div>
				<div class="P-Registration-Content-Left-Fields">
					<div class="P-Input-Holder">
						<div class="input-title fbold">{*producttypes} <span class="txtred">*</span></div>
					</div>
					<div class="P-Product-Type-Checkboxes">
						<input id="checkall" type="checkbox" name="producttype" class="nomargin" value="All" /> All
						{producttypes}					
					</div>
					<script type="text/javascript">
						$(document).ready(function(){
							$(\'.producttypes\').click(function() {
								$("#checkall").attr(\'checked\', false);
							});
							$("#checkall").click(function() {
								if($("#checkall").attr(\'checked\')) {
									$(\'.producttypes\' ).each( function() {
										$(".producttypes").attr(\'checked\', true);
									});
								} else {
									$(\'.producttypes\' ).each( function() {
										$(".producttypes").attr(\'checked\', false);
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
						{_initSubjectsTree}
					</div>
					<!-- Tree #1 END -->
					' . initAutocompleteAndBuildTree( (int) FIELD_HTML_SUBJECT_CLASSIFICATION_AUTOCOMPLETE_TYPE , "alerts_subject_cats") . '
					<script type="text/javascript">
						// Тук популираме първоначално инпута със селектнатите стойности или ако има грешка при сейв със селектнатите + новоселектнатите преди сейв
						var lSelectedCats =  new Array();
						lSelectedCats = {_PopulateSubjCats};
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
						{_initChronologicalTree}
					</div>
					<!-- Tree #2 END -->
					' . initAutocompleteAndBuildTree( (int) FIELD_HTML_CHRONOLOGICAL_CLASSIFICATION_AUTOCOMPLETE_TYPE , "alerts_chronical_cats") . '
					<script type="text/javascript">
						// Тук популираме първоначално инпута със селектнатите стойности или ако има грешка при сейв със селектнатите + новоселектнатите преди сейв
						var lSelectedCats =  new Array();
						lSelectedCats = {_PopulateChronCats};
						var InputVal = new Array();
						for ( var i = 0; i < lSelectedCats.length; i++) {
							$("#alerts_chronical_cats_autocomplete").tokenInput("add", lSelectedCats[i]);
						}
					</script>
					<div class="P-Clear"></div>
				</div>
				<div class="P-Registration-Content-Right-Fields">
					<div class="P-Input-Holder">
						<div class="input-title fbolddark">{*alertsfreq} <span class="txtred">*</span></div>
					</div>
					<div class="P-Alerts-Radios">
						{alertsfreq}
					</div>
					<div class="P-Clear"></div>
					<div class="P-Input-Full-Width P-W390 spacer10t">
						<div class="input-reg-title fbolddark">{*alerts_taxon_cats}</div>
							{alerts_taxon_cats}
					</div>
					<!-- Tree alerts_taxon_cats -->
					<div id="treealerts_taxon_cats">
						{_initTaxonsTree}
					</div>
					' . initAutocompleteAndBuildTree( (int) FIELD_HTML_TAXON_CLASSIFICATION_AUTOCOMPLETE_TYPE , "alerts_taxon_cats") . '
					<script type="text/javascript">
						// Тук популираме първоначално инпута със селектнатите стойности или ако има грешка при сейв със селектнатите + новоселектнатите преди сейв
						var lSelectedCats =  new Array();
						lSelectedCats = {_PopulateTaxonCats};
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
						{_initGeographicalTree}
					</div>
					' . initAutocompleteAndBuildTree( (int) FIELD_HTML_GEOGRAPHICAL_CLASSIFICATION_AUTOCOMPLETE_TYPE , "alerts_geographical_cats") . '
					<script type="text/javascript">
						// Тук популираме първоначално инпута със селектнатите стойности или ако има грешка при сейв със селектнатите + новоселектнатите преди сейв
						var lSelectedCats =  new Array();
						lSelectedCats = {_PopulateGeoCats};
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
						<div class="P-Registration-Email-Alerts-Journal-Checks">
							{journals}
						</div>
						<div class="P-Clear"></div>
					</div>
				</div>
				<div class="P-Registration-RSS-Feed">
					<div class="P-Right-Col-Fields-Client-Type-Txt">
						<div class="fbolddark">RSS Feed</div>
					</div>
					<div class="P-Registration-RSS-Feed-Links">
						<div class="P-Registration-RSS-Feed-IconLink">
							<a href="#"><img src="i/RSS_icon.png" alt="" /></a>
						</div>
						<div class="P-Registration-RSS-Feed-Txt">To read the RSS feeds for your Area(s) of interest, please copy and paste the link below in your favourite Feed reader</div>
						<div class="P-Registration-RSS-Feed-Txt"><a href="#">http://www.pensoft.net/rss/rsscust.php</a></div>
						<div class="P-Clear"></div>
					</div>
				</div>
				{_showRegEditProfileBtnsStep3}
			</div>
			<div class="P-Clear"></div>
		</div>
	',
	
	'registerfrm.registerfrmwrapper' => '
		{form}
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
		<img src="' . PJS_SITE_URL . '/lib/set_autologin_cookie.php?autologin_hash={hash}&logout={logout}" width="1" height="1" border="0" alt="Logging" />
		<img src="' . PJS_SITE_URL . '/login.php?logout={logout}" width="1" height="1" border="0" alt="" />
		<img src="' . OLD_PJS_SITE_URL . '/set_autologin_cookie.php?autologin_hash={hash}&logout={logout}" width="1" height="1" border="0" alt="in" />
		{_RetOldPjsLogoutImg(logout)}
		<script type="text/javascript">
			$(window).load(function() {
				window.location = \'{redirurl}\';
			});
		</script>
	'
);

?>