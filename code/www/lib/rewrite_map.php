<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

global $rewrite_map;

$rewrite_map = array(
	'/show.php' => array(
		'main' => array(
			'preg_match(\'/storyid=([0-9]+)\&title=([^\&]*)/\', $params)' => '/show/$1_$2/',
			'preg_match(\'/page=([^\&]*)/\', $params)' => '/show/$3/',
			'true' => '/show/',
		),
		'replace' => array(
			'storyid' => '$1',
			'title' => '$2',
			'page' => '$3',
		),
		'params' => array(
			'\/p-$1' => array('p=$1', '([0-9]+)'),
		),
	),
);

?>