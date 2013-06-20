<?php
// @formatter->off
$gTemplArr = array(
	'view_version_pwt.version_preview' => '
		<table width="100%" height="50" border="1" cellpadding="5"
			cellspacing="0"
			style="position: fixed; top: 0; left: 0; right: 0; background-color: #eee; z-index: 2">
			<tr>
				<td>&nbsp;</td>
				<td colspan="3" align="center"><strong>Track cha<span
						style="text-decoration: line-through">a</span>nge<span
						style="text-decoration: underline">s</span></strong></td>
				<td colspan="2" align="center"><strong>Comments</strong></td>
				<td width="288" rowspan="2" align="left"><input id="subjEd"
					type="checkbox" name="checkbox" checked="checked"> <label
					for="subjEd">Subj. Ed.: Pavel Stoev<br>
				</label> <input id="rev1" type="checkbox" name="checkbox" checked="checked">
					<label for="rev1">Review 1: Pavel Stoev</label> <br> <input
					id="rev2" type="checkbox" name="checkbox" checked="checked">
					<label for="rev2">Review 2: Anonymous</label> <br> <input
					id="panel" type="checkbox" name="checkbox" checked="checked">
					<label for="panel">Panel / Public</label></td>
			</tr>
			<tr>
				<td width="145"><img
					src="http://www.pensoft.net/img/JOURNALS/SMALLHEAD1323167046zk%20copy.jpg"
					alt="ZooKeys" height="35"></td>
				<td align="left" id="changes_display_holder">
					<input id="changes" name="changes_display" type="radio"
					checked="checked" value="1"> <label for="changes">Changes</label><br>
					<input id="final" name="changes_display" type="radio"> <label
					for="final" value="0">Final</label>
					<script type="text/javascript">
						$("#changes_display_holder :radio").bind("change", toggleChangesDisplay);
					</script>
				</td>

				<td align="left">
					<table width="100%">
						<tr>
							<td align="center"><img
								src="http://nytimes.github.com/ice/demo/lib/tinymce/jscripts/tiny_mce/plugins/ice/img/ice-accept.png"
								width="20" height="20"><br><a href="#" onclick="AcceptAllChanges()">Accept</a></td>
							<td align="center"><img
								src="http://nytimes.github.com/ice/demo/lib/tinymce/jscripts/tiny_mce/plugins/ice/img/ice-reject.png"
								width="20" height="20"><br><a href="#" onclick="RejectAllChanges()">Reject</a></td>
						</tr>
					</table>




				</td>
				<td align="left"><img src="img/icons/edit/next-change.png"
					width="27" height="17"> Next<br> <img
					src="img/icons/edit/previous-change.png" width="27" height="17">
					Previous</td>
				<td align="left"><img src="img/icons/edit/new-comment.png"
					width="19" height="16"> New<br> <img
					src="img/icons/edit/delete-comment.png" width="19" height="16">
					Delete</td>
				<td align="left"><img src="img/icons/edit/next-comment.png"
					width="27" height="17"> Next<br> <img
					src="img/icons/edit/previous-comment.png" width="20" height="17">
					Previous</td>
			</tr>
		</table>
		<div
			style="width: 190px; position: fixed; top: 100px; left: 0; border: 2px solid #eeeeee; border-radius: 13px;">
			<h2 style="margin: 0">Contents</h2>
			<ul style="padding: 10px 0 10px 20px; margin: 0">
				<li>Introduction</li>
				<li>Material and methods</li>
				<li>Data resources</li>
				<li>Results</li>
				<li>Systematics</li>
				<li>Discussions</li>
				<li>Identification Keys</li>
				<li>Acknowledgements</li>
				<li>References</li>
				<li>Supplementary files</li>
			</ul>
		</div>
		<div
			style="padding-left: 200px; margin-top: 100px; padding-right: 300px; z-index: 1;">
			<div class="P-Article-Preview-Holder" id="previewHolder">
				{preview}
			</div>
			<script type="text/javascript">InitTrackers({version_id})</script>
		<div>
	',

	'view_version_pwt.error_row' => '{err_msg}',
);

?>