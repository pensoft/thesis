<?php
$docroot = getenv("DOCUMENT_ROOT");
ini_set('display_errors', 'Off');
require_once($docroot. '/lib/static.php');

HtmlStart();

$registeredUser = session_is_registered("suser");

$fck = count($_POST);
$url = $_POST['url'];
if (!$url) $url = $_GET['url'];
$usrname = $_POST['usrname'];
$passwd = $_POST['passwd'];
$l = $_GET['l'];

if ((!$fck) && empty($url)) $url = "/";


if ($registeredUser) {
	if ($l == 1) {
		//unset($_SESSION['suser']);
		session_destroy();
		$headerr="Location: /";
	} else 
		$headerr="Location: /index.php";
	if (!$gEcmsLibRequest) Header($headerr);
	exit();
}

if ($fck && $usrname && $passwd) {
	$user = new CUser($usrname, $passwd, ip2long(getenv("REMOTE_ADDR")));
	if ($user->id) {
		$suser = serialize($user);
		$_SESSION["suser"] = $suser; //  = session_register("suser");
		if (!$gEcmsLibRequest) {
			if (!empty($url)) {
				Header("Location: " . $url);
			}
			else 
				Header("Location: /");
		} else echo json_encode(array("err"=> 0, "rescnt" => 0, "res" => array() ));
		exit();
	}
}
if (!$gEcmsLibRequest)
	DisplayLogForm($fck, $user->error);
else echo json_encode(array("err"=> -780, "rescnt" => 0, "errdesc"=> (($err >= 1) ? getstr('admin.bannedUserErr') : getstr('admin.wrongPassErr')) ));

	
function DisplayLogForm($fck, $err) {
	global $usrname, $passwd;
	global $url;
	
	$errstr = getstr('admin.wrongPassErr');
	if ($err >= 1) {
		$errstr = getstr('admin.bannedUserErr');
	}

	echo '
	<form name="log" action="./" method="post">
		<div class="t loginform">
		<div class="b">
		<div class="l">
		<div class="r">
			<div class="bl">
			<div class="br">
			<div class="tl">
			<div class="tr">
				<table cellspacing="0" cellpadding="5" border="0" class="formtable">
					<colgroup>
						<col width="50%" />
						<col width="50%" />
					</colgroup>
					<tr>
						<th colspan="2">' . getstr('admin.loginHorm') . '</th>
					</tr>
					' . ($fck ? 
						'
						<tr>
							<td colspan="2" class="err">' . $errstr . '</td>
						</tr>
						' : ''
					) . '
					<tr>
						<td>
							' . getstr('admin.userName') . '<br/>
							<input class="coolinp" type="text" name="usrname" value="' . s($usrname) . '">
						</td>
						<td>
							' . getstr('admin.userPassword') . '<br/>
							<input class="coolinp" type="Password" name="passwd" value="">
						</td>
					</tr>
					<tr>
						<td colspan="2" align="right">
							<input type="submit" name="LogBtn" value="' . getstr('admin.enterButton') . '" class="frmbutton" />
						</td>
					</tr>
				</table>
			</div>
			</div>
			</div>
			</div>
		</div>
		</div>
		</div>
		</div>
		<input type = "hidden" name = "url" value = "' . $url . '">
		<script language="JavaScript">document.forms[0].elements["usrname"].focus();</script>
	</form>';
}

HtmlEnd();

?>