<?php

$gTemplArr = array(	
	'eol_export.taxonHead' => '
		<div class="t">
		<div class="b">
		<div class="l">
		<div class="r">
			<div class="bl">
			<div class="br">
			<div class="tl">
			<div class="tr">
				<form action="/resources/exports/eol_export/save_taxon_data.php" method="POST">
					<input name="export_id" type="hidden" value="{export_id}"></input>
					<input name="tAction" type="hidden" value="save"></input>
					<table cellspacing="0" cellpadding="5" border="0" class="gridtable">
						<tr>
							<th class="gridtools" colspan="5">							
								' . getstr('admin.eol_export.taxonRowsAntetka') . '
							</th>
						</tr>
	',
	
	'eol_export.taxonNoData' => '
						<tr><td colspan="8"><p align="center"><b>' . getstr('admin.eol_export.noTaxonRows') . '</b></p></td></tr>
	',
	
	'eol_export.taxonErrRow' => '
						<tr><td colspan="8"><p align="center"><b>{err_msg}</b></p></td></tr>
	',
	
	'eol_export.taxonStart' => '
						<tr>
							<th>
								' . getstr('admin.eol_export.colDoi') . '
							</th>
							<th>
								' . getstr('admin.eol_export.colScientificName') . '
								
							</th>
							<th>
								' . getstr('admin.eol_export.colXmlLink') . '
								
							</th>
							<th>
								' . getstr('admin.eol_export.colPdfLink') . '
								
							</th>
							<th>
								' . getstr('admin.eol_export.colKingdom') . '						
							</th>					
							<th>
								' . getstr('admin.eol_export.colFamily') . '
							</th>
							<th>
								' . getstr('admin.eol_export.colHasDescription') . '
							</th>
							<th>
								' . getstr('admin.eol_export.colHasDistribution') . '
							</th>
							<th>
								' . getstr('admin.eol_export.colHasFigures') . '
							</th>				
						</tr>
	',
	
	'eol_export.taxonEnd' => '
						<tr>
							<td colspan="8">
								<input type="submit" class="frmbutton" value="' . getstr('admin.saveButton') . '"></input>
							</td>					
						</tr>
	',
	
	
	'eol_export.taxonFoot' => '
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
	
	'eol_export.taxonRow' => '
					<tr>
						<td>
							{identifier}
							<input name="identifier[]" type="hidden" value="{identifier}"></input>
						</td>
						<td>
							{scientificName}
						</td>
											
						<td>
							{_getPensoftArticleLinkByIdentifier(identifier, 1)}							
						</td>
						<td>
							{_getPensoftArticleLinkByIdentifier(identifier)}
						</td>	
						<td>
							<input name="kingdom[{identifier}]" type="text" value="{kingdom}"></input>
						</td>
						<td>
							<input name="family[{identifier}]" type="text" value="{family}"></input>						
						</td>
						<td>
							{_showYesNo(desc_count)}
						</td>
						<td>
							{_showYesNo(dist_count)}
						</td>
						<td>
							{_showYesNo(fig_count)}
						</td>
					</tr>
	',
	
	
);
?>