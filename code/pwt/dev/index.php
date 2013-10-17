<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Pensoft Developers</title>
<style type="text/css">
body{
	background-color: #000;
	color: #cecece;
	font-family: Calibri, Verdana, Geneva, sans-serif;}
h1, h2, h3{font-family: Cambria, Georgia, "Times New Roman", Times, serif;}
#devs{font-family: Consolas, "Courier New", Courier, monospace; font-size: xx-large; float: left;}
#pensoft-logo{ margin-top: 3px;}
.clear{clear: both}
a{color: #6fbe41; text-decoration:none}
a:hover{text-decoration: underline}
th{text-align: left}
code{display: block; margin: 10px; padding: 10px; background-color: #333; max-width: 800px; white-space: pre-wrap}
</style>
</head>

<body>
<img src="pensoft-logo.png" width="350" height="37" alt="Pensoft logo" id="pensoft-logo" />
<h1>Article import API</h1>
<p>To import a manuscript, submit an HTTP POST REQUEST to /api.php with the following parameters: </p>
<ul>
  <li>username</li>
  <li>password</li>
  <li>xml - the  contents of the .xml file, containing the manuscript</li>
  <li>action=process_document</li>
</ul>
<p>The request must include the following header:
  <br />
  <code>Content-Type: application/x-www-form-urlencoded</code>
</p>
<p>The body must therefore be URL encoded.  In particular, &amp; must be converted into %26amp;
</p>

<p>The API will report back in XML format.<br />
In case of success! the response body will contain a link to the document and the document ID:</p>
<code>
    &lt;?xml version="1.0" encoding="UTF-8"?&gt;
    &lt;result&gt;
      &lt;returnCode&gt;0&lt;/returnCode&gt;
      &lt;document_id&gt;123456&lt;/document_id&gt;
      &lt;document_link&gt;http://pwt.pensoft.net/preview.php?document_id=123456&lt;/document_link&gt;
    &lt;/result&gt;
</code>
<p>In case of errors :-(, the response body has this structure:</p>
<code>
&lt;?xml version="1.0" encoding="UTF-8"?&gt;
&lt;result&gt;
  &lt;returnCode&gt;1&lt;/returnCode&gt;
  &lt;errorCount&gt;1&lt;/errorCount&gt;
  &lt;errorMsg&gt;(most often: Xml is invalid!
     …
  &lt;/errorMsg&gt;
&lt;/result&gt;
</code>

<h3>Feedback is welcome via</h3>
<ul><li>our public issue tracker <a href="https://github.com/pensoft/dev/issues">https://github.com/pensoft/dev/issues</a></li>
  <li>email <a href="mailto:development@pensoft.net">development@pensoft.net</a></li>
</ul>

<h2>Article import API schemas</h2>
<table cellpadding="10" cellspacing="5">
	<tr>
		<th>Manuscript type</th>
		<th>Description</th>
	</tr>
<?php
	$docroot = getenv('DOCUMENT_ROOT');
	require_once($docroot . '/lib/conf.php');
	try {
	    $dbh = new PDO('pgsql:host='.PGDB_SRV.';port=5432;dbname='.PGDB_DB.';user=iusrpmt;password=oofskldkjn4l6s8jsd22');
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