<?php

$gTemplArr = array(
	'tables.document_tables_head' => '
		{*document.documentOnlyForm}
		<div class="P-Data-Resources">
			<table cellspacing="0" cellpadding="0" class="P-Data-Resources-Head">
				<tbody>
				<tr>
					<td class="P-Data-Resources-Head-Text">Tables</td>
					<td class="P-Inline-Line"></td>
				</tr>
				</tbody>
			</table>
			<div id="P-Document-Tables-Container">
	',

	'tables.document_tables_foot' => '
			</div>
		</div>
		<div class="P-Clear"></div>
		<div onclick="ShowAddTablePopup({document_id}, \'add-table-popup\')" class="P-Grey-Btn-Holder P-Add">
			<div class="P-Grey-Btn-Left"></div>
			<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>Add Table</div>
			<div class="P-Grey-Btn-Right"></div>
		</div>
		<div class="P-Clear"></div>
		<input type="hidden" name="document_id" value="{document_id}"></input>
		<script type="text/javascript">
			window.onbeforeunload = function(){
				unlock_document();
			};
			AutoSendDocumentLockSignal();
		</script>
	',

	'tables.document_tables_row' => '
		<div id="P-Table-Row-{id}" class="P-Data-Resources-Control">
			<input type="hidden" name="table_id" value="{id}"></input>
			<table cellspacing="0" cellpadding="0" border="0" class="P-Data-Table-Holder">
				<tbody><tr>
					<td class="P-Picture-Holder" rowspan="2"><img src="i/table_pic.png"></td>
					<td class="P-Block-Title-Holder">
						<div class="P-Block-Title">{title}</div>
						<div class="P-Clear"></div>
						<div class="P-Figure-Num">' . getstr('pwt.tableAntet') . ' {move_position}</div>
					</td>
					<td class="P-Block-Actions-Holder">
						<div class="P-Data-Resources-Head-Actions">
							<div onclick="ShowEditTablePopup({id}, \'P-PopUp-Main-Holder\')" class="P-Grey-Btn-Holder2 P-Edit">
								<div class="P-Grey-Btn-Left"></div>
								<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>Edit Table</div>
								<div class="P-Grey-Btn-Right"></div>
							</div>
							{_showUpDownMoveTablePositionArrows(document_id, move_position, max_position, min_position)}
							<div onclick="" class="P-Remove-Btn-Holder P-Remove-Right">
								<div class="P-Remove-Btn-Left"></div>
								<div class="P-Remove-Btn-Middle" onclick="DeleteTable({id}, {document_id})">Remove</div>
								<div class="P-Remove-Btn-Right"></div>
							</div>
						</div>
					</td>
				</tr>
				<tr>

				</tr>
			</tbody></table>
		</div>
	',

	'tables.document_tables_row_baloon' => '
		<div id="P-Table-Row-{id}" class="P-PopUp-Data-Table-Row">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td class="P-PopUp-Checkbox-Holder" align="left" valign="top">
						<input id="tbl-{id}" type="checkbox" name="tbl-{id}" figurenum="{move_position}" value="{id}"></input>
					</td>
					<td class="P-PopUp-Picture-Holder" valign="top">
						<label for="tbl-{id}">
							<div>
								<img src="i/table_pic.png" alt="" />
								<div class="P-Clear"></div>
							</div>
						</label>
					</td>
					<td valign="top">
						<label for="tbl-{id}">
							<div class="P-Block-Title">{title}</div>
							<div class="P-Figure-Num">' . getstr('pwt.tableAntet') . ' {move_position}</div>
						</label>
						<!-- <div class="P-Figure-Desc">{description}</div> -->
						<div class="P-Clear"></div>
					</td>
				</tr>
			</table>
			<div class="P-Clear"></div>
		</div>
		<div class="P-Clear"></div>
	',

	'tables.single_ajax_row' => '
		<div id="P-Table-Row-{id}" class="P-Data-Resources-Control">
			{*tables.single_ajax_row_notr}
		</div>
	',

	'tables.single_ajax_row_notr' => '
			<input type="hidden" name="table_id" value="{id}"></input>
			<table cellspacing="0" cellpadding="0" border="0" class="P-Data-Table-Holder">
				<tbody><tr>
					<td class="P-Picture-Holder" rowspan="2"><img src="i/table_pic.png"></td>
					<td class="P-Block-Title-Holder">
						<div class="P-Block-Title">{title}</div>
						<div class="P-Clear"></div>
						<div class="P-Figure-Num">' . getstr('pwt.tableAntet') . ' {move_position}</div>
					</td>
					<td class="P-Block-Actions-Holder">
						<div class="P-Data-Resources-Head-Actions">
							<div onclick="ShowEditTablePopup({id}, \'P-PopUp-Main-Holder\')" class="P-Grey-Btn-Holder2 P-Edit">
								<div class="P-Grey-Btn-Left"></div>
								<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>Edit Table</div>
								<div class="P-Grey-Btn-Right"></div>
							</div>
							{_showUpDownMoveTablePositionArrows(document_id, curr_position, max_position, min_position)}
							<div onclick="" class="P-Remove-Btn-Holder P-Remove-Right">
								<div class="P-Remove-Btn-Left"></div>
								<div class="P-Remove-Btn-Middle" onclick="DeleteTable({id}, {document_id})">Remove</div>
								<div class="P-Remove-Btn-Right"></div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class="P-Block-Title-Holder" colspan="2">

					</td>
				</tr>
			</tbody></table>
	',

	'tables.tables_popup' => '
		<div style="display: none;" class="P-PopUp" id="add-table-popup">
			<div class="P-PopUp-Main-Holder">

			</div>
		</div>
	',

	'tables.tables_popup_inpopup' => '
		<div class="P-PopUp-Content">
			<div class="P-PopUp-Title">Add table citation</div>
			<div class="P-PopUp-Content-Inner">
				<div class="P-Clear"></div>
				<div class="P-Data-Resources-Subsection-Title">
					<div class="P-Input-Full-Width ">
						<div class="P-Data-Resources-Control-Txt">
							<div class="P-Data-Resources-Control-Left">
								Table caption
								<span class="txtred">*</span>
							</div>
						</div>
						<div class="P-PopUp-Content-Desc">You can COPY & PASTE a table from a text processor, or as Coma Separated Value text.
															You can construct a new table or edit an inserted one with our integrated Table editor below
						</div>
						<div class="P-Input-Inner-Wrapper">
							<div class="P-Input-Holder">
								<textarea id="table_data_title_citt_textarea">{title}</textarea>
								{_createHtmlEditorBase(table_data_title_citt, 100, 840, ' . EDITOR_SMALL_TOOLBAR_NAME . ')}
								<div class="P-Clear"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="P-Control-And-Text-Holder">
					<div class="P-Data-Resources-Control-Txt">
						<div class="P-Data-Resources-Control-Left">
							Table editor
						</div>
					</div>
					<div class="P-Control-Textarea-Holder P-Control-Textarea-Holder-Table">
						<textarea id="table_data_description_textarea">{description}</textarea>
					</div>
					{_createHtmlEditorBase(table_data_description, 300, 866, ' . EDITOR_MODERATE_TABLE_TOOLBAR_NAME . ')}
				</div>
				<div class="P-Clear"></div>
			</div>
		</div>
		<div class="P-PopUp-Footer-Holder">
			<div class="P-Green-Btn-Holder P-90" onclick="SaveTableDataAndRefreshTablesBaloon({document_id}, \'table_data_title_citt_textarea\', \'table_data_description_textarea\');">
				<div class="P-Green-Btn-Left"></div>
				<div class="P-Green-Btn-Middle">Save</div>
				<div class="P-Green-Btn-Right"></div>
			</div>
			<div class="P-HSpace-10"></div>
			<div class="P-Grey-Btn-Holder" onclick="popUp(POPUP_OPERS.close, \'add-table-popup\', \'add-table-popup\'); gCurrentDialog.show();">
				<div class="P-Grey-Btn-Left"></div>
				<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>Close</div>
				<div class="P-Grey-Btn-Right"></div>
			</div>
			<div class="P-Clear"></div>
		</div>
	',

	'tables.table_row_popup' => '
		<div class="P-PopUp-Content">
		<div id="simplemodal-container"><a title="Close" onclick="popUp(POPUP_OPERS.close, \'add-table-popup\', \'add-table-popup\');" class="modalCloseImg simplemodal-close"></a></div>
			<form action="/" method="POST" name="table_form">
				<input type="hidden" value="999" name="testvamsi"></input>
				<div class="P-PopUp-Title">Add table citation</div>
				<div class="P-PopUp-Content-Inner">
					<div class="P-Clear"></div>
					<div class="P-Data-Resources-Subsection-Title">
						<div class="P-Input-Full-Width ">
							<div class="P-Data-Resources-Control-Txt">
								<div class="P-Data-Resources-Control-Left">
									Table caption
									<span class="txtred">*</span>
								</div>
							</div>
							<div class="P-Input-Inner-Wrapper">
								<div class="P-Input-Holder">
									<textarea id="table_data_title_textarea" name="table_title">{title}</textarea>
									{_createHtmlEditorBase(table_data_title, 100, 840, ' . EDITOR_SMALL_TOOLBAR_NAME . ')}
									<div class="P-Clear"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="P-PopUp-Content-Desc">You can COPY & PASTE a table from a text processor, or as Coma Separated Value text.
														You can construct a new table or edit an inserted one with our integrated Table editor below
					</div>
					<div class="P-Control-And-Text-Holder">
						<div class="P-Data-Resources-Control-Txt">
							<div class="P-Data-Resources-Control-Left">
								Table editor
							</div>
						</div>
						<div class="P-Control-Textarea-Holder P-Control-Textarea-Holder-Table">
							<textarea id="table_data_description_textarea" name="table_desc">{description}</textarea>
						</div>
						{_createHtmlEditorBase(table_data_description, 300, 849, ' . EDITOR_MODERATE_TABLE_TOOLBAR_NAME . ')}
					</div>
					<div class="P-Clear"></div>
				</div>
			</form>
		</div>
		<div class="P-PopUp-Footer-Holder">
			<div class="P-Green-Btn-Holder P-90" onclick="SaveTableData(\'table_data_title_textarea\', \'table_form\', {document_id}, {id});">
				<div class="P-Green-Btn-Left"></div>
				<div class="P-Green-Btn-Middle">Save</div>
				<div class="P-Green-Btn-Right"></div>
			</div>
			<div class="P-HSpace-10"></div>
			<div class="P-Grey-Btn-Holder" onclick="CloseTablePopUp(\'add-table-popup\', \'table_data_description_textarea\');">
				<div class="P-Grey-Btn-Left"></div>
				<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>Close</div>
				<div class="P-Grey-Btn-Right"></div>
			</div>
			<div class="P-Clear"></div>
		</div>
	',

	'tables.empty_row' => '
		<div class="P-Empty-Content">' . getstr('pwt.tables.nodata') . '</div>
	',

);

?>