<?php

$gTemplArr = array(	
	'templates.objectListHeadCommonStart' => '
		<div class="t">
		<div class="b">
		<div class="l">
		<div class="r">
			<div class="bl">
			<div class="br">
			<div class="tl">
			<div class="tr">
				<form action="#" method="POST">
				<table  cellspacing="0" cellpadding="5" border="0" class="gridtable">
					<colgroup width="18%"></colgroup>
					<colgroup width="8%"></colgroup>
					<colgroup width="10%"></colgroup>
					<colgroup width="10%"></colgroup>
					<colgroup width="10%"></colgroup>
					<colgroup width="10%"></colgroup>
					<colgroup width="10%"></colgroup>
					<colgroup width="10%"></colgroup>
					<colgroup width="10%"></colgroup>
					<colgroup width="4%"></colgroup>
					<tr>
						<th class="gridtools" colspan="10">
	',
	
	'templates.objectListHeadCommonEnd' => '
						</th>
					</tr>
	',
	
	'templates.objectListHead' => '
						{*templates.objectListHeadCommonStart}
							<a href="./template_object.php?template_id={template_id}">' . getstr('pwt_admin.templates.addObjectItem') . '</a>
							' . getstr('pwt_admin.templates.objectsListRowsAntetka') . '
						{*templates.objectListHeadCommonEnd}	
	',
	
	
	
	'templates.objectSubobjectListHead' => '
						{*templates.objectListHeadCommonStart}							
							' . getstr('pwt_admin.templates.objectsListRowsSubobjectAntetka') . '
							
						{*templates.objectListHeadCommonEnd}
	',
	
	'templates.objectListNoData' => '
					<tr><td colspan="10"><p align="center"><b>' . getstr('pwt_admin.templates.objectsList.noData') . '</b></p></td></tr>
	',	
	
	'templates.objectSubobjectListNoData' => '
					<tr><td colspan="10"><p align="center"><b>' . getstr('pwt_admin.templates.objectsSubobjectList.noData') . '</b></p></td></tr>
	',	
	
	'templates.objectListCommon' => '
					<tr>
						<th>
							' . getstr('pwt_admin.templates.objects.colName') . '
						</th>
						<th>
							' . getstr('pwt_admin.templates.objects.colPos') . '
							
						</th>
						<th>
							' . getstr('pwt_admin.templates.objects.colDisplayInTree') . '
							
						</th>
						<th>
							' . getstr('pwt_admin.templates.objects.colAllowMove') . '
							
						</th>
						<th>
							' . getstr('pwt_admin.templates.objects.colAllowAdd') . '
							
						</th>
						<th>
							' . getstr('pwt_admin.templates.objects.colAllowRemove') . '
							
						</th>
						<th>
							' . getstr('pwt_admin.templates.objects.colDisplayTitleAndTopActions') . '
							
						</th>
						<th>
							' . getstr('pwt_admin.templates.objects.colDisplayDefaultActions') . '
							
						</th>
						<th>
							' . getstr('pwt_admin.templates.objects.colTitleDisplayStyle') . '
							
						</th>
						<th>
						
						</th>
					</tr>					
	',
	
	'templates.objectListStart' => '
					{*templates.objectListCommon}					
					<tr>
						<td colspan="10" style="padding:0px;">
							<table id="templates_objects_table" cellspacing="0" cellpadding="5" border="0" width="100%">
								<colgroup width="18%"></colgroup>
								<colgroup width="8%"></colgroup>
								<colgroup width="10%"></colgroup>
								<colgroup width="10%"></colgroup>
								<colgroup width="10%"></colgroup>
								<colgroup width="10%"></colgroup>
								<colgroup width="10%"></colgroup>
								<colgroup width="10%"></colgroup>
								<colgroup width="10%"></colgroup>
								<colgroup width="4%"></colgroup>
	',
	
	'templates.objectSubobjectListStart' => '
					{*templates.objectListCommon}
	',
	
	'templates.objectListEnd' => '
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="10" align="right">
							<script>
								$("#templates_objects_table").tableDnD();	
							</script>
							<input type="submit" class="frmbutton" onclick="saveTemplateObjectsOrder({template_id}); return false;" value="' . getstr('admin.saveButton') . '"></input>
						</td>
					</tr>				
	',
	
	'templates.objectSubobjectListEnd' => '
	
	',
	
	
	'templates.objectListFoot' => '
				</table>	
				</form>
			</div>
			</div>
			</div>
			</div>
		</div>
		</div>
		</div>
		</div>
	
	',
	
	'templates.objectListRow' => '
								<tr parent_id="{parent_id}" id="{id}" class="{_getTemplateObjectRowClass(parent_id)}">
									<td style="{_displayTemplateObjectLevelCssStyle(level)}">
										<input name="template_object_id[]" value="{id}" type="hidden" />
										<a href="/resources/objects/edit.php?id={object_id}&tAction=showedit">{object_name}</a> {_displayTemplateObjectTreeLink(id, children_count, subobject_external_link)} 
									</td>
									<td>
										{pos}
									</td>											
									<td>
										{_showYesNo(display_in_tree)}
									</td>
									<td>
										{_showYesNo(allow_movement)}
									</td>
									<td>
										{_showYesNo(allow_add)}
									</td>
									<td>
										{_showYesNo(allow_remove)}
									</td>
									<td>
										{_showYesNo(display_title_and_top_actions)}
									</td>
									<td>
										{_showYesNo(display_default_actions)}
									</td>
									<td>
										{title_style}
									</td>
									<td align="right">
										{_displayTemplateObjectDeleteLink(id, level)}
										<a href="./template_object.php?tAction=showedit&id={id}"><img src="/img/edit.gif" alt="' . getstr('admin.editLabel') . '" title="' . getstr('admin.editLabel') . '" border="0" /></a>							
									</td>							
								</tr>
	',
);
?>