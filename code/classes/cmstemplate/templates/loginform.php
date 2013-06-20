<?php

$gTemplArr = array(

	'loginform.logged' => '
	<div id="login">
		<form action="/login.php" method="post" name="logoutform">
			<input type="hidden" name="url" value="' . $_SERVER['REQUEST_URI'] . '"/>
			<input type="hidden" name="logout" value="1"/>
			<div style="width: 180px;">{_getstr(loginform.greeting)}, <b>{fullname}</b></div>
			<div class="unfloat"></div>
			<input type="submit" class="coolbut" value="{_getstr(loginform.logout)}"/>
		</form>
	</div>	
	',
	
	'loginform.unlogged' => '
	<div id="login">
		{_showItemIfExists(err, <p class="loginerr">, </p>)}
		<form action="/login.php" method="post" id="loginform" name="loginform" onsubmit="CheckLoginForm(this, \'{_getstr(regprof.username)}\', \'{_getstr(regprof.upass1)}\');">
			<input type="hidden" name="url" value="' . $_SERVER['REQUEST_URI'] . '"/>
			<div style="margin-right: 6px;">
			<input type="text" name="uname" id="uname" value="{_getstr(regprof.username)}" onfocus="rldContent(this, \'{_getstr(regprof.username)}\');" onblur="rldContent2(this, \'{_getstr(regprof.username)}\');" class="coolinp" />
			</div>
			<div>
			<input type="password" name="upass" id="upass" value="{_getstr(regprof.upass1)}" onfocus="rldContent(this, \'{_getstr(regprof.upass1)}\');" onblur="rldContent2(this, \'{_getstr(regprof.upass1)}\');" class="coolinp" />
			</div>
			<div class="unfloat"></div>
			<input type="submit" class="coolbut" value="{_getstr(loginform.login)}"/>
			<ul>
				<li><a href="/register.php">{_getstr(loginform.register)}</a></li>
				<li><a href="/fpass.php">{_getstr(loginform.fpass)}</a></li>
			</ul>
		</form>
	</div>	
	',
);

?>