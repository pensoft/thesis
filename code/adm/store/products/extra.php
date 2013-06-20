<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$historypagetype=HISTORY_ACTIVE;

HtmlStart();


$t = array(
	"guid" => array(
		"VType" => "int",
		"CType" => "hidden",
		"DisplayName" => "",
		"AllowNulls" => true,
	),
	
	"name" => array(
		"VType" => "string",
		"CType" => "hidden",
		"DisplayName" => "",
		"AllowNulls" => true,
	),
	
	
	"svelements" => array(
		"CType" => "action",
		"DisplayName" => "Редактиране на продукта",
		"SQL" => "",
		"ActionMask" => ACTION_REDIRECT,
		"RedirUrl" => '/store/products/edit.php?id={guid}&tAction=show',
		"AddTags" => array(
			"class" => "frmbutton",
		), 
	),
	
	"cancel" => array(
		"CType" => "action",
		"DisplayName" => "Назад",
		"ActionMask" => ACTION_REDIRECT,
		"RedirUrl" => '/',
		"AddTags" => array(
			"class" => "frmbutton",
		),
	)	
);

$h = '
<h3>Елементи свързани със "{#name}"</h3>
<p>{name}{guid}{svelements} {cancel}</p>
';

$kfor = new kfor($t, $h, "POST");
$kfor->debug = false;
echo $kfor->Display();

$guid = (int)$_GET['guid'];
$lSiteRights = GetSiteRights();
if ($guid) showProductRelatedItems($guid, 1);

HtmlEnd();
?>