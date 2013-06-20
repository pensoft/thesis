<?php

$gTemplArr = array(
	'sup_files.single_sup_file_preview' => '
		<div id="P-Sup-File-Row-{id}" class="">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td class="P-PopUp-Checkbox-Holder" align="left" valign="top">
						<input type="checkbox" name="sup-file-{id}" position="{rownum}" value="{id}"></input>
					</td>
					<td valign="top">
						<div class="P-Reference-Desc" id="Sup-File-Preview-{id}">{preview}</div>
						<div class="P-Clear"></div>
					</td>
				</tr>
			</table>
			<div class="P-Clear"></div>
		</div>
		<div class="P-Clear"></div>
	',

	'sup_files.empty_row' => '
		<div class="P-Empty-Content">' . getstr('pwt.supFiles.nodata') . '</div>
	',

);

?>