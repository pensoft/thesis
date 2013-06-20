<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Pensoft Developers</title>
<style type="text/css">
body{
	background-color: #000;
	color: #cecece;
	font-family: Calibri, Verdana, Geneva, sans-serif;}
h1{font-family: Cambria, Georgia, "Times New Roman", Times, serif;}
#devs{font-family: Consolas, "Courier New", Courier, monospace; font-size: xx-large; float: left;}
#pensoft-logo{ margin-top: 3px;}
.clear{clear: both}
a{color: #6fbe41; text-decoration:none}
a:hover{text-decoration: underline}
th{text-align: left}
</style>
</head>

<body>
<img src="pensoft-logo.png" width="350" height="37" alt="Pensoft logo" id="pensoft-logo" />

<h1>Article import API schemas</h1>
<table cellpadding="10" cellspacing="5">
	<tr>
		<th>Manuscript type</th>
		<th>Description</th>
	</tr>
<?php
	try {
	    $dbh = new PDO('pgsql:host=localhost;port=5432;dbname=pensoft2;user=iusrpmt;password=oofskldkjn4l6s8jsd22');
	} catch (PDOException $e) {
	    echo 'Connection failed: ' . $e->getMessage();
	}
	$sth = $dbh->prepare("
		SELECT p.name, p.title
		FROM pwt.papertypes p
		WHERE is_active
		ORDER BY p.name
	");
	$sth->execute();
	$types = $sth->fetchAll(PDO::FETCH_ASSOC);
	foreach ($types as $type) {
		echo '<tr>
				<td><a href="'. strtolower(str_replace(' ', '_', str_replace(' / ', '_', $type['name']))) .'.xsd">'. $type['name'] .'</a></td>
				<td>'.$type['title'].'</td>
			  </tr>';
	}
?>
</table>

</body>
</html>