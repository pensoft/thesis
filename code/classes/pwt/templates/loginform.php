<?php

$gTemplArr = array(

	'loginform.form' => '{back_uri}
			<div class="loginFormLeftCol">
				<div class="loginFormLogo">
					<a href="/"><img src="' . PENSOFT_LOGO_IMG . '" alt="" /></a>
					<div class="P-Clear"></div>
				</div>
				<div class="loginFormTxt"></div>
				<div class="loginFormLinksHolder">
					<a href="#" onclick="LayerRegFrm(\'P-Registration-Content\', 1);" style="font-size: 12px">Sign up</a> for a Pensoft account
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="loginFormRightCol">
				<div class="loginFormErrHolder" style="height: 100px; margin-top: -10px;"><!--###ERRORS###--></div>
				{_returTxt}

				<p style="margin-bottom: 2em; color: #666"><img src="/i/lightbulb.png" alt="Note:" width="24" height="24" style="float: left; margin: 2px 7px 0 0" /> If you are already registered with a Pensoft journal, please use your credentials to sign in.
				</p>
				<div class="loginFormRowHolder">
					<div class="loginFormLabel">{*username} <span>*</span></div>
					<div class="P-Input-Full-Width">
						<div class="P-Input-Inner-Wrapper">
							<div class="P-Input-Holder">
								<div class="P-Input-Left"></div>
								<div class="P-Input-Middle">
									{username}
								</div>
								<div class="P-Input-Right"></div>
								<div class="P-Clear"></div>
							</div>
						</div>
					</div>
					<div class="P-Clear"></div>
				</div>
				<div class="P-Clear"></div>
				<div class="loginFormRowHolder">
					<div class="loginFormLabel">{*password} <span>*</span></div>
					<div class="P-Input-Full-Width">
						<div class="P-Input-Inner-Wrapper">
							<div class="P-Input-Holder">
								<div class="P-Input-Left"></div>
								<div class="P-Input-Middle">
									{password}
								</div>
								<div class="P-Input-Right"></div>
								<div class="P-Clear"></div>
							</div>
						</div>
					</div>
					<div class="P-Clear"></div>
				</div>
				{_GetLoginFormCaptchaTemplate}
				<div class="P-Clear"></div>
				<div class="P-Green-Btn-Holder">
					<div class="P-Green-Btn-Left"></div>
					<div class="P-Green-Btn-Middle">
						{login}
					</div>
					<div class="P-Green-Btn-Right"></div>
				</div>
				<div class="loginForgot"><a href="/fpass.php">Forgot your password?</a></div>
			</div>
			<div class="P-Clear"></div>
	',

	'loginform.fpassform' => '
			<div class="loginFormLeftCol">
				<div class="loginFormLogo">
					<a href="/"><img src="' . PENSOFT_LOGO_IMG . '" alt="" /></a>
					<div class="P-Clear"></div>
				</div>
				<div class="loginFormTxt"></div>
				<div class="loginFormLinksHolder">
					<a href="#" onclick="LayerRegFrm(\'P-Registration-Content\', 1);" style="font-size: 12px">Sign up</a> for a Pensoft account
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="loginFormRightCol">
				<div class="loginFormErrHolder fpassErrHolder">{$email}</div>
				<div class="loginFormRowHolder">
					<div class="loginFormLabel">{*email} <span>*</span></div>
					<div class="P-Input-Full-Width">
						<div class="P-Input-Inner-Wrapper">
							<div class="P-Input-Holder">
								<div class="P-Input-Left"></div>
								<div class="P-Input-Middle">
									{email}
								</div>
								<div class="P-Input-Right"></div>
								<div class="P-Clear"></div>
							</div>
						</div>
					</div>
					<div class="P-Clear"></div>
				</div>
				<div class="P-Clear"></div>
				<div class="loginFormRowHolder" style="margin-bottom: 10px;">
					<div class="capholder">
						<div class="capcode">
							<img src="/lib/frmcaptcha.php" id="cappic" border="0" alt="" /><br/>
							<a href="javascript: void(0);" onclick="return reloadCaptcha();">' . getstr('register.php.generatenew') . '</a>
						</div>
					</div>
				</div>
				<div class="loginFormRowHolder">
					<div class="loginFormErrHolder" style="height: 20px;">{$captcha}</div>
					<div class="loginFormLabel">' . getstr('register.php.spamconfirm') . '<span>*</span></div>
					<div class="P-Input-Full-Width">
						<div class="P-Input-Inner-Wrapper">
							<div class="P-Input-Holder">
								<div class="P-Input-Left"></div>
								<div class="P-Input-Middle">
									{captcha}
									<!-- <input type="text" name="captcha" id="captcha" class="input" /> -->
								<div class="P-Input-Right"></div>
								<div class="P-Clear"></div>
							</div>
						</div>
					</div>
					<div class="P-Clear"></div>
					<script type="text/javascript">
						$(function(){
							$(\'#captcha\').attr(\'value\', \'\');
						});
					</script>
				</div>
			</div>
			<div class="P-Green-Btn-Holder">
					<div class="P-Green-Btn-Left"></div>
					<div class="P-Green-Btn-Middle">
						{send}
					</div>
					<div class="P-Green-Btn-Right"></div>
				</div>
			<div class="P-Clear"></div>
	',

	'loginform.form_copy' => '
				<!-- start of login form -->
				<div class="QB-Login-Form-Wrapper QB-Login-Form-WrapperMarginTop">
					<div class="QB-LG-Form-FieldSet">
						<div class="QB-LG-Form-Row">
							<div class="QB-LG-Form-Antet">
								{*username}
							</div>
							<div class="QB-LG-Form-InpHolder">
								{username}
							</div>
							<div class="clear"></div>
						</div>

						<div class="QB-LG-Form-Row">
							<div class="QB-LG-Form-Antet">
								{*password}
							</div>
							<div class="QB-LG-Form-InpHolder">
								{password}
							</div>
							<div class="clear"></div>
						</div>

						<!-- <div class="QB-LG-Form-Row">
							<div class="QB-LG-Form-Antet">

							</div>
							<div class="QB-LG-Form-InpHolder">
								here must be remember me
							</div>
							<div class="clear"></div>
						</div> -->

						<div class="QB-LG-Form-Row">
							<div class="QB-LG-Form-Antet">

							</div>
							<div class="QB-LG-Form-InpHolder">

								<!-- start of login but -->
								<div class="QB-Button-Main-Wrapper">
									<span class="QB-Button-Main">

										<div class="QB-Button-Main-Middle">
													<div class="QB-Button-Main-Middle-Middle">
														<!-- sup link -->
														{login}
														<!-- sub links -->
													</div>
										</div>

									</span>
								</div>
								<!-- end of login but -->

							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
				<div class="QB-Clear"></div>
				<!-- end of login form -->
	',

	'loginform.loginformwrapper' => '
	<div class="loginFormWrapper loginpage1">
		{form}
	</div>
	',

	'loginform.loginformwrapperfpasssucess' => '
		<div class="loginFormWrapper">
			<div class="loginFormLeftCol">
					<div class="loginFormLogo">
						<a href="/"><img src="' . PENSOFT_LOGO_IMG . '" alt="" /></a>
						<div class="P-Clear"></div>
					</div>
					<div class="loginFormTxt"></div>
					<div class="loginFormLinksHolder">
						<a href="#" onclick="LayerRegFrm(\'P-Registration-Content\', 1);" style="font-size: 12px">Sign up</a> for a Pensoft account
					</div>
					<div class="P-Clear"></div>
				</div>
				<div class="loginFormRightCol">
					<div class="loginFormErrHolder"><!--###ERRORS###--></div>
					<div class="loginFormRowHolder">
						{msg} {email}
						<a href="/fpass.php">' . getstr('pwt.fpass.try.again') . '</a>
					</div>
				</div>
			</div>
		</div>
	',
	/* {requestdate} */
	'loginform.fpassmail' => '
	<html>
	<body>
	Dear {fullname},
	<br />
	<br /> You have successfully changed your password for account {uname}.
	<br /> Your new password is: {pass}
	<p>Click <a href="' . SITE_URL . '" title="Login">here</a> to login.</p>
	<p><a href="' . SITE_URL . '">The Pensoft PWT Team</a></p>
	</body>
	</html>
	',



	'loginform.document_author' => '
		<html>
		<body>
			<b>{usrfrom}{msg}</b><br/>
			<b><a href="' . SITE_URL . '/display_document.php?document_id={document_id}">Click on this link to view the document</a></b><br/>
		</body>
		</html>
	',

	'loginform.document_newauthor' => '
		<html>
		<body>
			<b>{usrfrom}{msg}</b><br/>
			<b><a href="' . SITE_URL . '/display_document.php?document_id={document_id}">Click on this link to view the document</a></b><br/>
			<b>Use your email as username.</b><br />
			<b>Your password is: {upass}</b><br />

		</body>
		</html>
	',
);

?>