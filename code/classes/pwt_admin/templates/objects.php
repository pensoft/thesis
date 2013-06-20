<?php

$gTemplArr = array(	
	'objects.fieldListHead' => '
		<div class="t">
		<div class="b">
		<div class="l">
		<div class="r">
			<div class="bl">
			<div class="br">
			<div class="tl">
			<div class="tr">
				<table cellspacing="0" cellpadding="6" border="0" class="gridtable">
					<tr>
						<th class="gridtools" colspan="6">	
							<a href="./object_field.php?object_id={object_id}">' . getstr('pwt_admin.objects.addFieldItem') . '</a>
							' . getstr('pwt_admin.objects.fieldListRowsAntetka') . '
						</th>
					</tr>
	',
	
	'objects.fieldListNoData' => '
					<tr><td colspan="5"><p align="center"><b>' . getstr('pwt_admin.objects.fieldList.noData') . '</b></p></td></tr>
	',	
	
	'objects.fieldListStart' => '
					<tr>
						<th>
							' . getstr('pwt_admin.objects.fieldList.colFieldId') . '
						</th>
						<th>
							' . getstr('pwt_admin.objects.fieldList.colFieldName') . '
						</th>
						<th>
							' . getstr('pwt_admin.objects.fieldList.colControlType') . '
							
						</th>
						<th>
							' . getstr('pwt_admin.objects.fieldList.colLabel') . '
							
						</th>
						<th>
							' . getstr('pwt_admin.objects.fieldList.colAllowNulls') . '
							
						</th>
						<th>
						
						</th>
					</tr>
	',
	
	'objects.fieldListEnd' => '
					
	',
	
	
	'objects.fieldListFoot' => '
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
	
	'objects.fieldListRow' => '
					<tr>
						<td>
							{field_id}
						</td>
						<td>
							<a href="/resources/fields/edit.php?id={field_id}&tAction=showedit">{field_name}</a>							
						</td>
						<td>
							{control_type}
						</td>											
						<td>
							{label}							
						</td>
						<td>
							{_showYesNo(allow_nulls)}
						</td>	
						<td align="right">
							<a href="./object_field.php?tAction=showedit&id={id}"><img src="/img/edit.gif" alt="' . getstr('admin.editLabel') . '" title="' . getstr('admin.editLabel') . '" border="0" /></a>
							<a href="javascript:if (confirm(\'' . getstr('pwt_admin.objects.fields.confirmDel') . '\')) window.location = \'/resources/objects/object_field.php?id={id}&tAction=delete\'; else ;">
								<img src="/img/trash2.gif" alt="' . getstr('admin.deleteButton') . '" title="' . getstr('admin.deleteButton') . '" border="0" />
							</a>
						</td>							
					</tr>
	',
	
	'objects.subobjectListHead' => '
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
							<a href="./object_subobject.php?object_id={object_id}">' . getstr('pwt_admin.objects.addSubobjectItem') . '</a>
							' . getstr('pwt_admin.objects.subobjectsListRowsAntetka') . '
						</th>
					</tr>
	',
	
	'objects.subobjectListNoData' => '
					<tr><td colspan="5"><p align="center"><b>' . getstr('pwt_admin.objects.subobjectsList.noData') . '</b></p></td></tr>
	',	
	
	'objects.subobjectListStart' => '
					<tr>
						<th>
							' . getstr('pwt_admin.objects.subobjectsList.colObjectId') . '
						</th>
						<th>
							' . getstr('pwt_admin.objects.subobjectsList.colFieldName') . '
						</th>
						<th>
							' . getstr('pwt_admin.objects.subobjectsList.colMinOccurrence') . '
							
						</th>						
						<th>
							' . getstr('pwt_admin.objects.subobjectsList.colMaxOccurrence') . '
							
						</th>
						<th>
							' . getstr('pwt_admin.objects.subobjectsList.colInitialOccurrence') . '
							
						</th>
						<th>
						
						</th>
					</tr>
	',
	
	'objects.subobjectListEnd' => '
					
	',
	
	
	'objects.subobjectListFoot' => '
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
	
	'objects.subobjectListRow' => '
					<tr>
						<td>
							{subobject_id}
						</td>
						<td>
							<a href="/resources/objects/edit.php?id={subobject_id}&tAction=showedit">{object_name}</a>							
						</td>
						<td>
							{min_occurrence}
						</td>											
						<td>
							{max_occurrence}							
						</td>
						<td>
							{initial_occurrence}
						</td>
						<td align="right">
							<a href="./object_subobject.php?tAction=showedit&id={id}"><img src="/img/edit.gif" alt="' . getstr('admin.editLabel') . '" title="' . getstr('admin.editLabel') . '" border="0" /></a>
							<a href="javascript:if (confirm(\'' . getstr('pwt_admin.objects.subobjects.confirmDel') . '\')) window.location = \'/resources/objects/object_subobject.php?id={id}&tAction=delete\'; else ;">
								<img src="/img/trash2.gif" alt="' . getstr('admin.deleteButton') . '" title="' . getstr('admin.deleteButton') . '" border="0" />
							</a>
						</td>							
					</tr>
	',
	
	
);
?>