<?
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

$gPropType = 9;

HtmlStart(1);

$t = array(
	'guid' => array(
		'VType' => 'int',
		'CType' => 'hidden',
	),
	
	'pos' => array(
		'VType' => 'int',
		'CType' => 'text',
		'DisplayName' => getstr('admin.selink.posFld'),
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	
	'url' => array(
		'VType' => 'string',
		'CType' => 'text',
		'DisplayName' => getstr('admin.selink.urlFld'),
		'AllowNulls' => true,
		'AddTags' => array(
			'class' => 'coolinp',
		),	
	),
	
	'title' => array(
		'VType' => 'string',
		'CType' => 'text',
		'DisplayName' => getstr('admin.selink.titleFld'),
		'AllowNulls' => true,
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	
	'show' => array(
		'CType' => 'action',
		'Hidden' => true,
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
		'SQL' => 'SELECT * FROM spMoreLinks(0, {guid}, null, null, {pos}, ' . $gPropType . ' )',
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'SQL' => 'SELECT * FROM spMoreLinks(1, {guid}, {url}, {title}, {pos}, ' . $gPropType . ' )',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH,
		'AddTags' => array(
			'class' => 'frmbutton',
		),		
	),
	
);

$h = '
{guid}
<div class="t">
<div class="b">
<div class="l">
<div class="r">
	<div class="bl">
	<div class="br">
	<div class="tl">
	<div class="tr">
		<table cellspacing="0" cellpadding="5" border="0" class="formtable">
			<tr>
				<th>' . getstr('admin.selink.antetka') . '</th>
			</tr>
			<tr>
				<td><b>{*url}:</b><br/>{url}</td>
			</tr>
			<tr>	
				<td><b>{*title}:</b><br/>{title}</td>
			</tr>
			<tr>
				<td><b>{*pos}:</b><br/>{pos}</td>
			</tr>	
			<tr>
				<td align="right">{save}{show}</td>
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
';

$kfor = new kfor($t, $h, "GET");

$kfor->ExecAction();

echo $kfor->Display();

if ($kfor->lCurAction == 'save' && !$kfor->lErrorCount) {
	echo '
		<script>
			window.opener.location.reload();
			window.close();
		</script>
	';
}

HtmlEnd(1);
?>