<?php

$gTemplArr = array(
	'regprof.registerfrm' => '
		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="results">
			<colgroup>
				<col width="150" />
				<col width="*" />
			</colgroup>
			<tr>
				<th colspan="2">' . getstr('regprof.registernow') . '</th>
			</tr>
			<tr>
				<td valign="top" align="right">{*name}</td>
				<td valign="top">{name} *</td>
			</tr>
			<tr>
				<td valign="top" align="right">{*email}</td>
				<td valign="top">{email} *</td>
			</tr>
			<tr>
				<td valign="top" align="right">{*phone}</td>
				<td valign="top">{phone}</td>
			</tr>
			<tr>
				<td valign="top" align="right">{*uname}</td>
				<td valign="top">{uname} *</td>
			</tr>
			<tr>
				<td valign="top" align="right">{*upass1}</td>
				<td valign="top">{upass1} *</td>
			</tr>
			<tr>
				<td valign="top" align="right">{*upass2}</td>
				<td valign="top">{upass2} *</td>
			</tr>
			<tr>
				<td valign="top" colspan="2">
					<div class="capholder">
						<div class="capcode">
							<img src="/lib/frmcaptcha.php" id="cappic" border="0" alt="" /><br/>
							<a href="javascript: void(0);" onclick="return reloadCaptcha();">' . getstr('register.php.generatenew') . '</a>
						</div>
						<div class="capinfo">
							<label for="captcha">' . getstr('register.php.spamconfirm') . '</label>
							<input type="text" name="captcha" id="captcha">
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="right">{register}</td>
			</tr>
		</table>
	',
	
	'regprof.profilefrm' => '
		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="results">
			<colgroup>
				<col width="150" />
				<col width="*" />
			</colgroup>
			<tr>
				<th colspan="2">' . getstr('regprof.yourprofile') . '</th>
			</tr>
			<tr>
				<td valign="top" align="right">{*name}</td>
				<td valign="top">{name} *</td>
			</tr>
			<tr>
				<td valign="top" align="right">{*email}</td>
				<td valign="top">{email} *</td>
			</tr>
			<tr>
				<td valign="top" align="right">{*phone}</td>
				<td valign="top">{phone}</td>
			</tr>
			<tr>
				<td valign="top" align="right">{*upass1}</td>
				<td valign="top">{upass1}</td>
			</tr>
			<tr>
				<td valign="top" align="right">{*upass2}</td>
				<td valign="top">{upass2}</td>
			</tr>
			<tr>
				<td colspan="2" align="right">{new}{save}</td>
			</tr>
		</table>
	',
	
	'regprof.mailcontent' => '
		<html>
		<body>
		<h3>Hello</h3>
		To confirm your registration please click on the following link:
		<a href="{siteurl}confmail.php?hash={confhash}">{siteurl}confmail.php?hash={confhash}</a><br/><br/>
		Best regards,<br/>
		<a href="{siteurl}">NCS.co.uk</a> Team
		</body>
		</html> 
	',
	
	'regprof.fpassform' => '
		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="results">
			<colgroup>
				<col width="100" />
				<col width="100%" />
			</colgroup>
			<tr>
				<th colspan="2">' . getstr('fpass.php.fpassheader') . '</th>
			</tr>
			<tr>
				<td valign="top">{*email}</td>
				<td valign="top">{email} *</td>
			</tr>
			<tr>
				<td align="right" colspan="2">{send}</td>
			</tr>
		</table>
	',
	
	'regprof.fpassmail' => '
		<html>
		<body>
		<h3>Hello</h3>
		{requestdate}.<br/>
		User: <b>{uname}</b> New password: <b>{pass}</b><br/>
		You can change it anytime from your profile page<br/><br/>
		Best regards,<br/>
		<a href="{siteurl}">NCS.co.uk</a> Team
		</body>
		</html>	
	',
);

?>