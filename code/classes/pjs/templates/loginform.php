<?php

$gTemplArr = array(

	'loginform.form' => '{redirurl}
						<div class="loginFormErrHolder">{~}{~~}</div>
						<div class="loginFormRowHolder">
							<div class="loginFormLabel">{*uname} <span>*</span></div>
							<div class="P-Input-Full-Width P-W300">
								<div class="P-Input-Holder">
									<div class="P-Input-Left"></div>
									<div class="P-Input-Middle">
										{uname}
									</div>
									<div class="P-Input-Right"></div>
									<div class="P-Clear"></div>
								</div>
							</div>
						</div>
						<div class="P-Clear"></div>
						<div class="loginFormRowHolder">
							<div class="loginFormLabel">{*upass} <span>*</span></div>
							<div class="P-Input-Full-Width P-W300">
								<div class="P-Input-Holder">
									<div class="P-Input-Left"></div>
									<div class="P-Input-Middle">
										{upass}
									</div>
									<div class="P-Input-Right"></div>
									<div class="P-Clear"></div>
								</div>
							</div>
						</div>
						{&captcha&}
						<div class="P-Clear"></div>
						<div class="loginFormButton">
							<div class="P-Green-Btn-Holder">
								<div class="P-Green-Btn-Left"></div>
								<div class="P-Green-Btn-Middle P-80">{login}</div>
								<div class="P-Green-Btn-Right"></div>
							</div>
							<div class="lostPassword"><a href="/fpass.php">' . getstr('pjs.lost_password') . '</a></div>
							<div class="P-Clear"></div>
						</div>
	',

	'loginform.captcha' => '
						<div class="capholder">
							<div class="capcode">
								<img src="/lib/frmcaptcha.php" id="cappic" border="0" alt="" /><br />
								<a class="antet" href="javascript: void(0);" onclick="return reloadCaptcha();">' . getstr('register.php.generatenew') . '</a>
							</div>
							<div class="loginFormRowHolder capinfo">
								<div class="loginFormLabel">' . getstr('register.php.spamconfirm') . ' <span>*</span></div>
								<div class="P-Input-Full-Width P-W300">
									<div class="P-Input-Holder">
										<div class="P-Input-Left"></div>
										<div class="P-Input-Middle">
											<input type="text" name="captcha" id="captcha" class="inputFld" onblur="changeFocus(2, this)" onfocus="changeFocus(1, this)" />
										</div>
										<div class="P-Input-Right"></div>
										<div class="P-Clear"></div>
									</div>
								</div>
							</div>
							<div class="P-Clear"></div>
						</div>
	',

	'loginform.fpass' => '{redirurl}
						<div class="loginFormErrHolder">{~}</div>
						<div class="loginFormRowHolder">
							<div class="loginFormLabel">{*email} <span>*</span></div>
							{!email}
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
						</div>
						<div class="P-Clear"></div>
						{&captcha&}
						<div class="loginFormButton">
							<div class="P-Green-Btn-Holder">
								<div class="P-Green-Btn-Left"></div>
								<div class="P-Green-Btn-Middle P-80">{send}</div>
								<div class="P-Green-Btn-Right"></div>
							</div>
							<div class="P-Clear"></div>
						</div>
	',

	'loginform.fpass_success' => '
		{success_msg}
	',

	'loginform.fpassmail' => '
	<html>
	<body>
	Dear {fullname},
	<br />
	<br /> You have successfully changed your password for account {uname}.
	<br /> Your new password is: {pass}
	<p>Click <a href="' . SITE_URL . '" title="Login">here</a> to login.</p>
	<p><a href="' . SITE_URL . '">The Pensoft PJS Team</a></p>
	</body>
	</html>
	',

);

?>