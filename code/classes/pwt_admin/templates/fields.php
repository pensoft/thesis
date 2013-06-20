<?php

$gTemplArr = array(
	'fields.relatedObjectsListHead' => '
		<div class="t">
		<div class="b">
		<div class="l">
		<div class="r">
			<div class="bl">
			<div class="br">
			<div class="tl">
			<div class="tr">
				<table cellspacing="0" cellpadding="5" border="0" class="gridtable">
					<tr>
						<th class="gridtools" colspan="5">							
							' . getstr('pwt_admin.fields.relatedObjectsListRowsAntetka') . '
						</th>
					</tr>
	',
	
	'fields.relatedObjectsListNoData' => '
					<tr><td colspan="5"><p align="center"><b>' . getstr('pwt_admin.fields.relatedObjectsList.noData') . '</b></p></td></tr>
	',	
	
	'fields.relatedObjectsListStart' => '
					<tr>
						<th>
							' . getstr('pwt_admin.fields.relatedObjectsList.colName') . '
						</th>
						<th>
							' . getstr('pwt_admin.fields.relatedObjectsList.colControlType') . '
							
						</th>
						<th>
							' . getstr('pwt_admin.fields.relatedObjectsList.colLabel') . '
							
						</th>
						<th>
							' . getstr('pwt_admin.fields.relatedObjectsList.colAllowNulls') . '
							
						</th>
						<th>
						
						</th>
					</tr>
	',
	
	'fields.relatedObjectsListEnd' => '
					
	',
	
	
	'fields.relatedObjectsListFoot' => '
				</table>				
			</div>
			</div>
			</div>
			</div>
		</div>
		</div>
		</div>
		</div>
	
	',
	
	'fields.relatedObjectsListRow' => '
					<tr>
						<td>
							<a href="/resources/objects/edit.php?id={object_id}&tAction=showedit">{object_name}</a>							
						</td>
						<td>
							{control_type_name}
						</td>											
						<td>
							{label}							
						</td>
						<td>
							{_showYesNo(allow_nulls)}
						</td>	
						<td align="right">
							<a href="/resources/objects/object_field.php?tAction=showedit&id={id}"><img src="/img/edit.gif" alt="' . getstr('admin.editLabel') . '" title="' . getstr('admin.editLabel') . '" border="0" /></a>							
						</td>							
					</tr>
	',
);
?>