<?php

$gTemplArr = array(

	'figures.document_figures_head' => '
		{*document.documentOnlyForm}

		<div class="P-Data-Resources">
			<table cellspacing="0" cellpadding="0" class="P-Data-Resources-Head">
				<tbody>
				<tr>
					<td class="P-Data-Resources-Head-Text">Figures</td>
					<td class="P-Inline-Line"></td>
				</tr>
				</tbody>
			</table>
			<table width="100%" id="P-Document-Figures-Container">
				<colgroup width="90"></colgroup>
				<colgroup width="*"></colgroup>
				<colgroup width="265"></colgroup>
				<tbody>
	',
	'figures.plate_appearance_1' => '
		<div class="P-Plate-Appearance-Box">A</div>
		<div class="P-Clear"></div>
		<div class="P-Plate-Appearance-Box">B</div>
	',
		
	'figures.plate_appearance_2' => '
		<div class="P-Plate-Appearance-Box-Big">A</div>
		<div class="P-Plate-Appearance-Box-Big">B</div>
	',
	
	'figures.plate_appearance_3' => '
		<div class="P-Plate-Appearance-Box">A</div>
		<div class="P-Plate-Appearance-Box">B</div>
		<div class="P-Clear"></div>
		<div class="P-Plate-Appearance-Box">C</div>
		<div class="P-Plate-Appearance-Box">D</div>
	',
		
	'figures.plate_appearance_4' => '
		<div class="P-Plate-Appearance-Box-Small">A</div>
		<div class="P-Plate-Appearance-Box-Small">B</div>
		<div class="P-Clear"></div>
		<div class="P-Plate-Appearance-Box-Small">C</div>
		<div class="P-Plate-Appearance-Box-Small">D</div>
		<div class="P-Clear"></div>
		<div class="P-Plate-Appearance-Box-Small">E</div>
		<div class="P-Plate-Appearance-Box-Small">F</div>
	',
	

	'figures.document_figures_foot' => '
				</tbody>
			</table>
		</div>
		<div class="P-Clear"></div>
		<div class="P-Figure-Add-Buttons">
			<div onclick="ChangeFiguresForm( \'image\', {document_id}, \'P-PopUp-Content-Inner\', 0, 2);popUp(POPUP_OPERS.open, \'add-figure-popup\', \'add-figure-popup\');" class="P-Grey-Btn-Holder P-Add">
				<div class="P-Grey-Btn-Left"></div>
				<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>' . getstr('pwt.figures.addfigure') . '</div>
				<div class="P-Grey-Btn-Right"></div>
			</div>
			<div onclick="ChangeFiguresForm( \'plate\', {document_id}, \'P-PopUp-Content-Inner\', 0, 1);popUp(POPUP_OPERS.open, \'add-figure-popup\', \'add-figure-popup\');" class="P-Grey-Btn-Holder P-Add">
				<div class="P-Grey-Btn-Left"></div>
				<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>' . getstr('pwt.figures.addplate') . '</div>
				<div class="P-Grey-Btn-Right"></div>
			</div>
			<div onclick="ChangeFiguresForm(\'video\', {document_id}, \'P-PopUp-Content-Inner\', 0, 0);popUp(POPUP_OPERS.open, \'add-figure-popup\', \'add-figure-popup\');" class="P-Grey-Btn-Holder P-Add">
				<div class="P-Grey-Btn-Left"></div>
				<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>' . getstr('pwt.figures.addvideo') . '</div>
				<div class="P-Grey-Btn-Right"></div>
			</div>
		</div>
		<input type="hidden" name="document_id" value="{document_id}"></input>
		<script type="text/javascript">
			window.onbeforeunload = function(){
				unlock_document();
			};
			AutoSendDocumentLockSignal();
		</script>
	',

	'figures.document_figures_row' => '
		<tr id="P-Figures-Row-{plate_id}{photo_id}" class="P-Data-Table-Holder">
			<td class="P-Picture-Holder" valign="top" >
				<div onclick="{_showEditFigureAction(plate_id, photo_id, document_id, ftype)}" class="pointerLink">
					{_showPlatePhotoSrc(plate_id, photo_id, c90x82y, photo_ids_arr, format_type, photo_positions_arr, ftype, link)}
					<div class="P-Clear"></div>
				</div>
			</td>
			<td class="P-Block-Title-Holder">
				<div class="P-Figure-Num">Figure {move_position}</div>
				<div class="P-Figure-Desc">{_showPlatePhotoDesc(plate_desc, photo_desc)}</div>
				<input type="hidden" name="plate_photo_id" value="{_showPlatePhotoVal(plate_id, photo_id)}"></input>
				<div class="plate_photo_id" style="display:none;">{_showPlatePhotoVal(plate_id, photo_id)}<div></div></div>
			</td>
			<td class="P-Block-Actions-Holder">
				<div class="P-Data-Resources-Head-Actions">
					<div onclick="{_showEditFigureAction(plate_id, photo_id, document_id, ftype)}" class="P-Grey-Btn-Holder2 P-Edit">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>{_EditFigurePhotoBtnText(photo_id, ftype)}</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					{_showUpDownMoveFigurePositionArrows(document_id, plate_id, photo_id, move_position, max_position, min_position)}
					<div class="P-Remove-Btn-Holder P-Remove-Right">
						<div class="P-Remove-Btn-Left"></div>
						<div class="P-Remove-Btn-Middle" onclick="{_showDeletePlatePhotoAction(plate_id, photo_id, document_id)}">Remove</div>
						<div class="P-Remove-Btn-Right"></div>
					</div>
				</div>
			</td>
		</tr>

	',

	'figures.document_figures_row_baloon' => '
		<div id="P-Figures-Row-{plate_id}{photo_id}" class="P-PopUp-Data-Table-Row-Figs">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td class="P-PopUp-Checkbox-Holder" align="left" valign="top">
						<input id="fig-{plate_id}{photo_id}" type="checkbox" onclick="checkAllSubPhotos(this)" name="fig-{plate_id}{photo_id}" figtype="{_checkFigureType(photo_id, plate_id)}" figurenum="{move_position}" value="{plate_id}{photo_id}"></input>
					</td>
					<td class="P-PopUp-Picture-Holder" valign="top">
						<label for="fig-{plate_id}{photo_id}">
							<div>
								{_showPlatePhotoSrc(plate_id, photo_id, c90x82y, photo_ids_arr, format_type, photo_positions_arr, ftype, link, 1)}
								<div class="P-Clear"></div>
							</div>
						</label>
					</td>
					<td valign="top">
						<label for="fig-{plate_id}{photo_id}">
							<div class="P-Figure-Num">Figure {move_position}</div>

							<div class="P-Figure-Desc">{_showPlatePhotoDesc(plate_desc, photo_desc)}</div>
						</label>
						{_displayPlatePhotos(plate_id, format_type, photo_ids_arr, photo_positions_arr)}

						<div class="P-Clear"></div>
					</td>
				</tr>
			</table>
			<div class="P-Clear"></div>
		</div>
		<div class="P-Clear"></div>
	',

	'figures.plate_1' => '
		<form name="plate_photo_form">
			<div class="P-Data-Resources-Control-Txt">
				<div class="P-Data-Resources-Control-Left">
					Plate parts
					<span class="txtred">*</span>
				</div>
			</div>
			<div class="P-Plate-Part-Holder">
				<input type="hidden" value="" name="picture_id_1A">
				<div class="P-Plate-Part">
					<div id="figures_image_plate_holder_1A" class="P-Add-Plate-Holder">
						<div class="P-Grey-Btn-Holder P-Add" onclick="">
							<div class="P-Grey-Btn-Left"></div>
							<script type="text/javascript"zz>ajaxFileUpload(\'add_figure_plate_1A_photo\', \'figures_plate1_1A_photo\', {document_id}, \'figures_image_plate_holder_1A\', \'picture_id_1A\', {plate_val}, \'c288x206y\', 0, 1);</script>
							<div class="P-Grey-Btn-Middle" id="add_figure_plate_1A_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.add_image') . ' A</div>
							<div class="P-Grey-Btn-Right"></div>
						</div>
						<div class="P-Clear"></div>
					</div>
					<div class="P-Edit-Plate-Holder-Background" onMouseOver="ajaxFileUpload(\'change_figure_plate_1A_photo\', \'figures_plate1_1A_photo\', {document_id}, \'figures_image_plate_holder_1A\', \'picture_id_1A\', {plate_val}, \'c288x206y\', 0, 1);"></div>
					<div class="P-Edit-Plate-Holder">
						<div class="P-Grey-Btn-Holder" onclick="">
							<div class="P-Grey-Btn-Left"></div>
							<div class="P-Grey-Btn-Middle" id="change_figure_plate_1A_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.change_image') . '</div>
							<div class="P-Grey-Btn-Right"></div>
						</div>
						<div class="P-Clear"></div>
						<div class="P-VSpace-10"></div>
					</div>
				</div>
				<div class="P-Plate-Textarea-Holder">
					<textarea id="figures_plate1_1A_photo" name="1A">{description}</textarea>
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="P-Clear"></div>
			<div class="P-Plate-Part-Holder">
				<input type="hidden" value="" name="picture_id_2A">
				<div class="P-Plate-Part">
					<div id="figures_image_plate_holder_2A" class="P-Add-Plate-Holder">
						<div class="P-Grey-Btn-Holder P-Add" onclick="">
							<div class="P-Grey-Btn-Left"></div>
							<script type="text/javascript">ajaxFileUpload(\'add_figure_plate_2A_photo\', \'figures_plate1_2A_photo\', {document_id}, \'figures_image_plate_holder_2A\', \'picture_id_2A\', {plate_val}, \'c288x206y\', 0, 2);</script>
							<div class="P-Grey-Btn-Middle" id="add_figure_plate_2A_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.add_image') . ' B</div>
							<div class="P-Grey-Btn-Right"></div>
						</div>
						<div class="P-Clear"></div>
					</div>
					<div class="P-Edit-Plate-Holder-Background" onMouseOver="ajaxFileUpload(\'change_figure_plate_2A_photo\', \'figures_plate1_2A_photo\', {document_id}, \'figures_image_plate_holder_2A\', \'picture_id_2A\', {plate_val}, \'c288x206y\', 0, 2);"></div>
					<div class="P-Edit-Plate-Holder">
						<div class="P-Grey-Btn-Holder" onclick="">
							<div class="P-Grey-Btn-Left"></div>
							<div class="P-Grey-Btn-Middle" id="change_figure_plate_2A_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.change_image') . '</div>
							<div class="P-Grey-Btn-Right"></div>
						</div>
						<div class="P-Clear"></div>
						<div class="P-VSpace-10"></div>
					</div>
				</div>
				<div class="P-Plate-Textarea-Holder">
					<textarea id="figures_plate1_2A_photo" name="2A">{description}</textarea>
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="P-Clear"></div>
		</form>
	',

	'figures.plate_2' => '
	<form name="plate_photo_form">
		<div class="P-Data-Resources-Control-Txt">
			<div class="P-Data-Resources-Control-Left">
				Plate parts
				<span class="txtred">*</span>
			</div>
		</div>
		<div class="P-Plate-Part-Holder P-Plate-Part-Holder-Margin-Roght">
			<input type="hidden" value="" name="picture_id_1A">
			<div class="P-Plate-Part P-Plate-Part-Big">
				<div id="figures_image_plate_holder_1A" class="P-Add-Plate-Holder">
					<div class="P-Grey-Btn-Holder P-Add" onclick="">
						<div class="P-Grey-Btn-Left"></div>
						<script type="text/javascript">ajaxFileUpload(\'add_figure_plate_1A_photo\', \'figures_plate1_1A_photo\', {document_id}, \'figures_image_plate_holder_1A\', \'picture_id_1A\', {plate_val}, \'c288x206y\', 0, 1);</script>
						<div class="P-Grey-Btn-Middle" id="add_figure_plate_1A_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.add_image') . ' A</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				</div>
				<div class="P-Edit-Plate-Holder-Background" onMouseOver="ajaxFileUpload(\'change_figure_plate_1A_photo\', \'figures_plate1_1A_photo\', {document_id}, \'figures_image_plate_holder_1A\', \'picture_id_1A\', {plate_val}, \'c288x206y\', 0, 2);"></div>
				<div class="P-Edit-Plate-Holder">
					<div class="P-Grey-Btn-Holder" onclick="">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle" id="change_figure_plate_1A_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.change_image') . '</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="P-VSpace-10"></div>
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="P-Plate-Textarea-Holder">
				<textarea id="figures_plate1_1A_photo" name="1A">{description}</textarea>
			</div>
			<div class="P-Clear"></div>
		</div>
		<div class="P-Plate-Part-Holder">
			<input type="hidden" value="" name="picture_id_1B">
			<div class="P-Plate-Part P-Plate-Part-Big">
				<div id="figures_image_plate_holder_1B" class="P-Add-Plate-Holder">
					<div class="P-Grey-Btn-Holder P-Add" onclick="">
						<div class="P-Grey-Btn-Left"></div>
						<script type="text/javascript">ajaxFileUpload(\'add_figure_plate_1B_photo\', \'figures_plate1_1B_photo\', {document_id}, \'figures_image_plate_holder_1B\', \'picture_id_1B\', {plate_val}, \'c288x206y\', 0, 2);</script>
						<div class="P-Grey-Btn-Middle" id="add_figure_plate_1B_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.add_image') . ' B</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				</div>
				<div class="P-Edit-Plate-Holder-Background" onMouseOver="ajaxFileUpload(\'change_figure_plate_1B_photo\', \'figures_plate1_1B_photo\', {document_id}, \'figures_image_plate_holder_1A\', \'picture_id_1A\', {plate_val}, \'c288x206y\', 0, 2);"></div>
				<div class="P-Edit-Plate-Holder">
					<div class="P-Grey-Btn-Holder" onclick="">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle" id="change_figure_plate_1B_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.change_image') . '</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="P-VSpace-10"></div>
				</div>
			</div>
			<div class="P-Plate-Textarea-Holder">
				<textarea id="figures_plate1_1B_photo" name="1B">{description}</textarea>
			</div>
			<div class="P-Clear"></div>
		</div>
		<div class="P-Clear"></div>
	</form>
	',

	'figures.plate_3' => '
	<form name="plate_photo_form">
		<div class="P-Data-Resources-Control-Txt">
			<div class="P-Data-Resources-Control-Left">
				Plate parts
				<span class="txtred">*</span>
			</div>
		</div>
		<div class="P-Plate-Part-Holder P-Plate-Part-Holder-Margin-Roght">
			<input type="hidden" value="" name="picture_id_1A">
			<div class="P-Plate-Part">
				<div id="figures_image_plate_holder_1A" class="P-Add-Plate-Holder">
					<div class="P-Grey-Btn-Holder P-Add" onclick="">
						<div class="P-Grey-Btn-Left"></div>
						<script type="text/javascript">ajaxFileUpload(\'add_figure_plate_1A_photo\', \'figures_plate1_1A_photo\', {document_id}, \'figures_image_plate_holder_1A\', \'picture_id_1A\', {plate_val}, \'c288x206y\', 0, 1);</script>
						<div class="P-Grey-Btn-Middle" id="add_figure_plate_1A_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.add_image') . ' A</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				</div>
				<div class="P-Edit-Plate-Holder-Background" onMouseOver="ajaxFileUpload(\'change_figure_plate_1A_photo\', \'figures_plate1_1A_photo\', {document_id}, \'figures_image_plate_holder_1A\', \'picture_id_1A\', {plate_val}, \'c288x206y\', 0, 1);"></div>
				<div class="P-Edit-Plate-Holder">
					<div class="P-Grey-Btn-Holder" onclick="">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle" id="change_figure_plate_1A_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.change_image') . '</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="P-VSpace-10"></div>
				</div>
			</div>
			<div class="P-Plate-Textarea-Holder">
				<textarea id="figures_plate1_1A_photo" name="1A">{description}</textarea>
			</div>
			<div class="P-Clear"></div>
		</div>
		<div class="P-Plate-Part-Holder">
			<input type="hidden" value="" name="picture_id_1B">
			<div class="P-Plate-Part">
				<div id="figures_image_plate_holder_1B" class="P-Add-Plate-Holder">
					<div class="P-Grey-Btn-Holder P-Add" onclick="">
						<div class="P-Grey-Btn-Left"></div>
						<script type="text/javascript">ajaxFileUpload(\'add_figure_plate_1B_photo\', \'figures_plate1_1B_photo\', {document_id}, \'figures_image_plate_holder_1B\', \'picture_id_1B\', {plate_val}, \'c288x206y\', 0, 2);</script>
						<div class="P-Grey-Btn-Middle" id="add_figure_plate_1B_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.add_image') . ' B</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				</div>
				<div class="P-Edit-Plate-Holder-Background" onMouseOver="ajaxFileUpload(\'change_figure_plate_1B_photo\', \'figures_plate1_1B_photo\', {document_id}, \'figures_image_plate_holder_1B\', \'picture_id_1B\', {plate_val}, \'c288x206y\', 0, 3);"></div>
				<div class="P-Edit-Plate-Holder">
					<div class="P-Grey-Btn-Holder" onclick="">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle" id="change_figure_plate_1B_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.change_image') . '</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="P-VSpace-10"></div>
				</div>
			</div>
			<div class="P-Plate-Textarea-Holder">
				<textarea id="figures_plate1_1B_photo" name="1B">{description}</textarea>
			</div>
			<div class="P-Clear"></div>
		</div>

		<div class="P-Clear"></div>
		<div class="P-Plate-Part-Holder P-Plate-Part-Holder-Margin-Roght">
			<input type="hidden" value="" name="picture_id_2A">
			<div class="P-Plate-Part">
				<div id="figures_image_plate_holder_2A" class="P-Add-Plate-Holder">
					<div class="P-Grey-Btn-Holder P-Add" onclick="">
						<div class="P-Grey-Btn-Left"></div>
						<script type="text/javascript">ajaxFileUpload(\'add_figure_plate_2A_photo\', \'figures_plate1_2A_photo\', {document_id}, \'figures_image_plate_holder_2A\', \'picture_id_2A\', {plate_val}, \'c288x206y\', 0, 3);</script>
						<div class="P-Grey-Btn-Middle" id="add_figure_plate_2A_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.add_image') . ' C</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				</div>
				<div class="P-Edit-Plate-Holder-Background" onMouseOver="ajaxFileUpload(\'change_figure_plate_2A_photo\', \'figures_plate1_2A_photo\', {document_id}, \'figures_image_plate_holder_2A\', \'picture_id_2A\', {plate_val}, \'c288x206y\', 0, 2);"></div>
				<div class="P-Edit-Plate-Holder">
					<div class="P-Grey-Btn-Holder" onclick="">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle" id="change_figure_plate_2A_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.change_image') . '</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="P-VSpace-10"></div>
				</div>
			</div>
			<div class="P-Plate-Textarea-Holder">
				<textarea id="figures_plate1_2A_photo" name="2A">{description}</textarea>
			</div>
			<div class="P-Clear"></div>
		</div>
		<div class="P-Plate-Part-Holder">
			<input type="hidden" value="" name="picture_id_2B">
			<div class="P-Plate-Part">
				<div id="figures_image_plate_holder_2B" class="P-Add-Plate-Holder">
					<div class="P-Grey-Btn-Holder P-Add" onclick="">
						<div class="P-Grey-Btn-Left"></div>
						<script type="text/javascript">ajaxFileUpload(\'add_figure_plate_2B_photo\', \'figures_plate1_2B_photo\', {document_id}, \'figures_image_plate_holder_2B\', \'picture_id_2B\', {plate_val}, \'c288x206y\', 0, 4);</script>
						<div class="P-Grey-Btn-Middle" id="add_figure_plate_2B_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.add_image') . ' D</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				</div>
				<div class="P-Edit-Plate-Holder-Background" onMouseOver="ajaxFileUpload(\'change_figure_plate_2B_photo\', \'figures_plate1_2B_photo\', {document_id}, \'figures_image_plate_holder_2B\', \'picture_id_2B\', {plate_val}, \'c288x206y\', 0, 4);"></div>
				<div class="P-Edit-Plate-Holder">
					<div class="P-Grey-Btn-Holder">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle" id="change_figure_plate_2B_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.change_image') . '</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="P-VSpace-10"></div>
				</div>
			</div>
			<div class="P-Plate-Textarea-Holder">
				<textarea id="figures_plate1_2B_photo" name="2B">{description}</textarea>
			</div>
			<div class="P-Clear"></div>
		</div>
	</form>
	',

	'figures.plate_4' => '
	<form name="plate_photo_form">
		<div class="P-Data-Resources-Control-Txt">
			<div class="P-Data-Resources-Control-Left">
				Plate parts
				<span class="txtred">*</span>
			</div>
		</div>
		<div class="P-Plate-Part-Holder P-Plate-Part-Holder-Margin-Roght">
			<input type="hidden" value="" name="picture_id_1A">
			<div class="P-Plate-Part">
				<div id="figures_image_plate_holder_1A" class="P-Add-Plate-Holder">
					<div class="P-Grey-Btn-Holder P-Add" onclick="">
						<div class="P-Grey-Btn-Left"></div>
						<script type="text/javascript">ajaxFileUpload(\'add_figure_plate_1A_photo\', \'figures_plate1_1A_photo\', {document_id}, \'figures_image_plate_holder_1A\', \'picture_id_1A\', {plate_val}, \'c288x206y\', 0, 1);</script>
						<div class="P-Grey-Btn-Middle" id="add_figure_plate_1A_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.add_image') . ' A</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				</div>
				<div class="P-Edit-Plate-Holder-Background" onMouseOver="ajaxFileUpload(\'change_figure_plate_1A_photo\', \'figures_plate1_1A_photo\', {document_id}, \'figures_image_plate_holder_1A\', \'picture_id_1A\', {plate_val}, \'c288x206y\', 0, 1);"></div>
				<div class="P-Edit-Plate-Holder">
					<div class="P-Grey-Btn-Holder" onclick="">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle" id="change_figure_plate_1A_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.change_image') . '</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="P-VSpace-10"></div>
				</div>
			</div>
			<div class="P-Plate-Textarea-Holder">
				<textarea id="figures_plate1_1A_photo" name="1A">{description}</textarea>
			</div>
			<div class="P-Clear"></div>
		</div>
		<div class="P-Plate-Part-Holder">
			<input type="hidden" value="" name="picture_id_1B">
			<div class="P-Plate-Part">
				<div id="figures_image_plate_holder_1B" class="P-Add-Plate-Holder">
					<div class="P-Grey-Btn-Holder P-Add" onclick="">
						<div class="P-Grey-Btn-Left"></div>
						<script type="text/javascript">ajaxFileUpload(\'add_figure_plate_1B_photo\', \'figures_plate1_1B_photo\', {document_id}, \'figures_image_plate_holder_1B\', \'picture_id_1B\', {plate_val}, \'c288x206y\', 0, 2);</script>
						<div class="P-Grey-Btn-Middle" id="add_figure_plate_1B_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.add_image') . ' B</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				</div>
				<div class="P-Edit-Plate-Holder-Background" onMouseOver="ajaxFileUpload(\'change_figure_plate_1B_photo\', \'figures_plate1_1B_photo\', {document_id}, \'figures_image_plate_holder_1B\', \'picture_id_1B\', {plate_val}, \'c288x206y\', 0, 3);"></div>
				<div class="P-Edit-Plate-Holder">
					<div class="P-Grey-Btn-Holder" onclick="">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle" id="change_figure_plate_1B_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.change_image') . '</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="P-VSpace-10"></div>
				</div>
			</div>
			<div class="P-Plate-Textarea-Holder">
				<textarea id="figures_plate1_1B_photo" name="1B">{description}</textarea>
			</div>
			<div class="P-Clear"></div>
		</div>
		<div class="P-Clear"></div>
		<div class="P-Plate-Part-Holder P-Plate-Part-Holder-Margin-Roght">
			<input type="hidden" value="" name="picture_id_2A">
			<div class="P-Plate-Part">
				<div id="figures_image_plate_holder_2A" class="P-Add-Plate-Holder">
					<div class="P-Grey-Btn-Holder P-Add" onclick="">
						<div class="P-Grey-Btn-Left"></div>
						<script type="text/javascript">ajaxFileUpload(\'add_figure_plate_2A_photo\', \'figures_plate1_2A_photo\', {document_id}, \'figures_image_plate_holder_2A\', \'picture_id_2A\', {plate_val}, \'c288x206y\', 0, 3);</script>
						<div class="P-Grey-Btn-Middle" id="add_figure_plate_2A_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.add_image') . ' C</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				</div>
				<div class="P-Edit-Plate-Holder-Background" onMouseOver="ajaxFileUpload(\'change_figure_plate_2A_photo\', \'figures_plate1_2A_photo\', {document_id}, \'figures_image_plate_holder_2A\', \'picture_id_2A\', {plate_val}, \'c288x206y\', 0, 2);"></div>
				<div class="P-Edit-Plate-Holder">
					<div class="P-Grey-Btn-Holder" onclick="">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle" id="change_figure_plate_2A_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.change_image') . '</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="P-VSpace-10"></div>
				</div>
			</div>
			<div class="P-Plate-Textarea-Holder">
				<textarea id="figures_plate1_2A_photo" name="2A">{description}</textarea>
			</div>
			<div class="P-Clear"></div>
		</div>
		<div class="P-Plate-Part-Holder">
			<input type="hidden" value="" name="picture_id_2B">
			<div class="P-Plate-Part">
				<div id="figures_image_plate_holder_2B" class="P-Add-Plate-Holder">
					<div class="P-Grey-Btn-Holder P-Add" onclick="">
						<div class="P-Grey-Btn-Left"></div>
						<script type="text/javascript">ajaxFileUpload(\'add_figure_plate_2B_photo\', \'figures_plate1_2B_photo\', {document_id}, \'figures_image_plate_holder_2B\', \'picture_id_2B\', {plate_val}, \'c288x206y\', 0, 4);</script>
						<div class="P-Grey-Btn-Middle" id="add_figure_plate_2B_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.add_image') . ' D</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				</div>
				<div class="P-Edit-Plate-Holder-Background" onMouseOver="ajaxFileUpload(\'change_figure_plate_2B_photo\', \'figures_plate1_2B_photo\', {document_id}, \'figures_image_plate_holder_2B\', \'picture_id_2B\', {plate_val}, \'c288x206y\', 0, 4);"></div>
				<div class="P-Edit-Plate-Holder">
					<div class="P-Grey-Btn-Holder">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle" id="change_figure_plate_2B_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.change_image') . '</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="P-VSpace-10"></div>
				</div>
			</div>
			<div class="P-Plate-Textarea-Holder">
				<textarea id="figures_plate1_2B_photo" name="2B">{description}</textarea>
			</div>
			<div class="P-Clear"></div>
		</div>
		<div class="P-Clear"></div>
		<div class="P-Plate-Part-Holder P-Plate-Part-Holder-Margin-Roght">
			<input type="hidden" value="" name="picture_id_3A">
			<div class="P-Plate-Part">
				<div id="figures_image_plate_holder_3A" class="P-Add-Plate-Holder">
					<div class="P-Grey-Btn-Holder P-Add" onclick="">
						<div class="P-Grey-Btn-Left"></div>
						<script type="text/javascript">ajaxFileUpload(\'add_figure_plate_3A_photo\', \'figures_plate1_3A_photo\', {document_id}, \'figures_image_plate_holder_3A\', \'picture_id_3A\', {plate_val}, \'c288x206y\', 0, 5);</script>
						<div class="P-Grey-Btn-Middle" id="add_figure_plate_3A_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.add_image') . ' E</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				</div>
				<div class="P-Edit-Plate-Holder-Background" onMouseOver="ajaxFileUpload(\'change_figure_plate_3A_photo\', \'figures_plate1_3A_photo\', {document_id}, \'figures_image_plate_holder_3A\', \'picture_id_3A\', {plate_val}, \'c288x206y\', 0, 5);"></div>
				<div class="P-Edit-Plate-Holder">
					<div class="P-Grey-Btn-Holder">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle" id="change_figure_plate_3A_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.change_image') . '</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="P-VSpace-10"></div>
				</div>
			</div>
			<div class="P-Plate-Textarea-Holder">
				<textarea id="figures_plate1_3A_photo" name="3A">{description}</textarea>
			</div>
			<div class="P-Clear"></div>
		</div>
		<div class="P-Plate-Part-Holder">
			<input type="hidden" value="" name="picture_id_3B">
			<div class="P-Plate-Part">
				<div id="figures_image_plate_holder_3B" class="P-Add-Plate-Holder">
					<div class="P-Grey-Btn-Holder P-Add" onclick="">
						<div class="P-Grey-Btn-Left"></div>
						<script type="text/javascript">ajaxFileUpload(\'add_figure_plate_3B_photo\', \'figures_plate1_3B_photo\', {document_id}, \'figures_image_plate_holder_3B\', \'picture_id_3B\', {plate_val}, \'c288x206y\', 0, 6);</script>
						<div class="P-Grey-Btn-Middle" id="add_figure_plate_3B_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.add_image') . ' F</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				</div>
				<div class="P-Edit-Plate-Holder-Background" onMouseOver="ajaxFileUpload(\'change_figure_plate_3B_photo\', \'figures_plate1_3B_photo\', {document_id}, \'figures_image_plate_holder_3B\', \'picture_id_3B\', {plate_val}, \'c288x206y\', 0, 6);"></div>
				<div class="P-Edit-Plate-Holder">
					<div class="P-Grey-Btn-Holder">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle" id="change_figure_plate_3B_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.change_image') . '</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="P-VSpace-10"></div>
				</div>
			</div>
			<div class="P-Plate-Textarea-Holder">
				<textarea id="figures_plate1_3B_photo" name="3B">{description}</textarea>
			</div>
			<div class="P-Clear"></div>
		</div>
	</form>
	',

	'figures.figures_form_image' => '
			<input type="hidden" value="{citation}" name="citation" id="citation_flag"></input>
			<div class="P-Control-And-Text-Holder">
				<div class="P-Data-Resources-Control-Txt">
					<div class="P-Data-Resources-Control-Left">
						Figure caption
						<span class="txtred">*</span>
					</div>
				</div>
				<div class="P-Control-Textarea-Holder">
					<textarea id="photo_plate_description_textarea">{_getFigurePhotoDescription(photo_id, plate_id)}</textarea>
				</div>
				{_createHtmlEditorBase(photo_plate_description, ' . EDITOR_DEFAULT_HEIGHT . ', 0, ' . EDITOR_MODERATE_TOOLBAR_NAME . ', 0, \'\', 1)}
			</div>
			<div class="P-Grey-Btn-Holder P-Add" onclick="">
				<input type="hidden" value="{photo_id}" name="single_picture_id"></input>
				<div class="P-Grey-Btn-Left"></div>
				<script type="text/javascript">ajaxFileUpload(\'add_figure_photo\', \'photo_plate_description_textarea\', {document_id}, \'figures_image_holder\', \'single_picture_id\', 0, \'\', 1, 0);</script>
				<div class="P-Grey-Btn-Middle" id="add_figure_photo"><div class="P-Btn-Icon"></div>{_AddChangePhotoBtnText(photo_id)}</div>
				<div class="P-Grey-Btn-Right"></div>
			</div>
			<div class="P-Clear"></div>
			<div class="P-PopUp-Content-Desc">Supported formats are JPEG, GIF &amp; PNG. Allowed image size is 15 MB.</div>
			<div class="P-Plates-Holder">
				<div class="P-Plate-Part-Holder">
					<div class="P-Plate-Part" {_showResizeStyleIfPicExists(photo_id, 1)}>
						<div id="figures_image_holder" class="P-Add-Plate-Holder" {_showResizeStyleIfPicExists(photo_id)}>
							{_showFiguresPhotoLink(photo_id)}
						</div>
					</div>
					<div class="P-Clear"></div>
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="P-Clear"></div>
	',

	'figures.figures_form_plate' => '
			<input type="hidden" value="{citation}" name="citation" id="citation_flag"></input>
			<div class="P-Control-And-Text-Holder">
				<div class="P-Data-Resources-Control-Txt">
					<div class="P-Data-Resources-Control-Left">
						Plate caption
						<span class="txtred">*</span>
					</div>
				</div>
				<div class="P-Control-Textarea-Holder">
					<textarea id="photo_plate_description_textarea">{_getFigurePhotoDescription(photo_id, plate_id)}</textarea>
				</div>
				{_createHtmlEditorBase(photo_plate_description, ' . EDITOR_DEFAULT_HEIGHT . ', 0, ' . EDITOR_MODERATE_TOOLBAR_NAME . ')}
			</div>
			<div class="P-Plate-Appearance-Holder">
				<div class="P-Data-Resources-Control-Txt">
					<div class="P-Data-Resources-Control-Left">
						Plate appearance
						<span class="txtred">*</span>
					</div>
				</div>
				<div class="P-Plate-Appearance-Holder-Inner">
					<div class="P-Plate-Appearance-Radio">
						<input type="radio" name="plate_appearance" {_checkPlateVal(plate_id, 1)} value="1" id="plate_appearance_1"></input>
					</div>
					<div class="P-Plate-Appearance-Picture" onclick="toggleRadioCheck(\'plate_appearance_1\')">
						<div class="P-Plate-Appearance-Box">A</div>
						<div class="P-Clear"></div>
						<div class="P-Plate-Appearance-Box">B</div>
					</div>
				</div>
				<div class="P-Plate-Appearance-Holder-Inner">
					<div class="P-Plate-Appearance-Radio">
						<input type="radio" name="plate_appearance" {_checkPlateVal(plate_id, 2)} value="2" id="plate_appearance_2"></input>
					</div>
					<div class="P-Plate-Appearance-Picture" onclick="toggleRadioCheck(\'plate_appearance_2\')">
						<div class="P-Plate-Appearance-Box-Big">A</div>
						<div class="P-Plate-Appearance-Box-Big">B</div>
					</div>
				</div>
				<div class="P-Plate-Appearance-Holder-Inner">
					<div class="P-Plate-Appearance-Radio">
						<input type="radio" name="plate_appearance" {_checkPlateVal(plate_id, 3)} value="3" id="plate_appearance_3"></input>
					</div>
					<div class="P-Plate-Appearance-Picture" onclick="toggleRadioCheck(\'plate_appearance_3\')">
						<div class="P-Plate-Appearance-Box">A</div>
						<div class="P-Plate-Appearance-Box">B</div>
						<div class="P-Clear"></div>
						<div class="P-Plate-Appearance-Box">C</div>
						<div class="P-Plate-Appearance-Box">D</div>
					</div>
				</div>
				<script type="text/javascript">
					var plate = new plateAppearance(\'plate_appearance\', {document_id});
				</script>
				<div class="P-Plate-Appearance-Holder-Inner">
					<div class="P-Plate-Appearance-Radio">
						<input type="radio" name="plate_appearance"  {_checkPlateVal(plate_id, 4)} value="4" id="plate_appearance_4"></input>
					</div>
					<div class="P-Plate-Appearance-Picture" onclick="toggleRadioCheck(\'plate_appearance_4\')">
						<div class="P-Plate-Appearance-Box-Small">A</div>
						<div class="P-Plate-Appearance-Box-Small">B</div>
						<div class="P-Clear"></div>
						<div class="P-Plate-Appearance-Box-Small">C</div>
						<div class="P-Plate-Appearance-Box-Small">D</div>
						<div class="P-Clear"></div>
						<div class="P-Plate-Appearance-Box-Small">E</div>
						<div class="P-Plate-Appearance-Box-Small">F</div>
					</div>
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="P-PopUp-Content-Desc"><br />Compose plate from several images. Supported formats are JPEG, GIF &amp; PNG.</div>
			<div class="P-Plates-Holder">
				<script type="text/javascript">ChangePlateAppearance({_getPlateTypeById(plate_id)}, {document_id}, {plate_id})</script>
			</div>
			<div class="P-Clear"></div>
	',

	'figures.figures_form_3dimage' => '
	',

	'figures.figures_form_video' => '
			<input type="hidden" value="{citation}" name="citation" id="citation_flag"></input>
			<div class="P-Data-Resources-Subsection-Title">
				<div class="P-PopUp-Content-Desc">Paste Youtube video link here</div>
				<div class="P-Input-Full-Width ">
					<div class="P-Input-Inner-Wrapper">
						<div class="P-Input-Holder">
							<div class="P-Input-Left"></div>
							<div class="P-Input-Middle">
								<input type="text" id="video_link_field" value="{_getVideoLink(photo_id)}" name="video_link_field" onfocus="changeFocus(1, this)" onblur="changeFocus(2, this)"></input>
								<script type="text/javascript">
									initVideoLinkDetection(\'video_link_field\', \'youtube_video_frame\', {document_id}, {edit});
								</script>
							</div>
							<div class="P-Input-Right"></div>
							<div class="P-Clear"></div>
						</div>
					</div>
				</div>
				<div class="P-Control-And-Text-Holder">
					<div class="P-Data-Resources-Control-Txt">
						<div class="P-Data-Resources-Control-Left">
							Video caption
							<span class="txtred">*</span>
						</div>
					</div>
					<div class="P-Control-Textarea-Holder">
						<textarea id="video_title_textarea">{_getVideoTitle(photo_id)}</textarea>
					</div>
					{_createHtmlEditorBase(video_title, ' . EDITOR_DEFAULT_HEIGHT . ', 0, ' . EDITOR_MODERATE_TOOLBAR_NAME . ')}
				</div>
			</div>
			<iframe id="youtube_video_frame" width="420" height="315" src="" frameborder="0" allowfullscreen></iframe>
	',

	'figures.figures_form_audio' => '
	',

	'figures.figures_form_interactive_map' => '
	',

	'figures.figures_popup' => '
		<div style="display: none;" class="P-PopUp" id="add-figure-popup" tabindex="-1">
			<div onclick="popUp(POPUP_OPERS.close, \'add-figure-popup\', \'add-figure-popup\');" id="simplemodal-container"><a class="modalCloseImg simplemodal-close" title="Close"></a></div>
			<div class="P-PopUp-Main-Holder">
				<div class="P-PopUp-Content">
					<div id="P-PopUp-Figures-Title" class="P-PopUp-Title">Add figure</div>
					<div class="P-PopUp-Menu-Holder">
						<ul id="popUp_nav">
							<li class="P-Active">
								<div class="P-PopUp-Menu-Elem-Left"></div>
								<div class="P-PopUp-Menu-Elem-Middle" onclick="ChangeFiguresForm(\'image\', {document_id}, \'P-PopUp-Content-Inner\');">Image</div>
								<div class="P-PopUp-Menu-Elem-Right"></div>
							</li>
							<li>
								<div class="P-PopUp-Menu-Elem-Left"></div>
								<div class="P-PopUp-Menu-Elem-Middle" onclick="ChangeFiguresForm(\'plate\', {document_id}, \'P-PopUp-Content-Inner\');">Multiple images (plate)</div>
								<div class="P-PopUp-Menu-Elem-Right"></div>
							</li>
							<li>
								<div class="P-PopUp-Menu-Elem-Left"></div>
								<div class="P-PopUp-Menu-Elem-Middle" onclick="ChangeFiguresForm(\'video\', {document_id}, \'P-PopUp-Content-Inner\');">Video</div>
								<div class="P-PopUp-Menu-Elem-Right"></div>
							</li>
							<div class="P-Clear"></div>
						</ul>
						<div class="P-Clear"></div>
						<script type="text/javascript">
							var menu = new popUpMenu(\'popUp_nav\');
						</script>
						<input type="hidden" id="plate_id_value" name="plate_id" value="0"/>
					</div>
					<div class="P-PopUp-Content-Inner">

					</div>
				</div>
				<div class="P-PopUp-Footer-Holder">
					<div class="P-Green-Btn-Holder P-90" id="P-Figures-Save-Button" onclick="SavePlateData(\'photo_plate_description_textarea\', \'plate_id\', \'single_picture_id\', \'plate_photo_form\', 1);">
						<div class="P-Green-Btn-Left"></div>
						<div class="P-Green-Btn-Middle">Save</div>
						<div class="P-Green-Btn-Right"></div>
					</div>
					<div class="P-Green-Btn-Holder P-90" style="display: none;" id="save_video_btn">
						<div class="P-Green-Btn-Left"></div>
						<div class="P-Green-Btn-Middle">Save</div>
						<div class="P-Green-Btn-Right"></div>
					</div>
					<div class="P-HSpace-10"></div>
					<div class="P-Grey-Btn-Holder" onclick="ShowDialogOnClose(\'#citation_flag\');popUp(POPUP_OPERS.close, \'add-figure-popup\', \'add-figure-popup\'); ChangeFiguresForm(\'video\', {document_id}, \'P-PopUp-Content-Inner\', 1);">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>Close</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				</div>
			</div>
		</div>

	',

	'figures.figures_popup_inpopup' => '
		<div style="display: none;" class="P-PopUp" id="add-figure-popup" tabindex="-1">

			<div class="P-PopUp-Main-Holder">
				<div class="P-PopUp-Content">
					<div class="P-PopUp-Title">Add figure citation</div>
					<div class="P-PopUp-Menu-Holder">
						<ul id="popUp_nav">
							<li class="P-Active">
								<div class="P-PopUp-Menu-Elem-Left"></div>
								<div class="P-PopUp-Menu-Elem-Middle" onclick="ChangeFiguresForm(\'image\', {document_id}, \'P-PopUp-Content-Inner\', 0, 0, 0, 0, 1);">Image</div>
								<div class="P-PopUp-Menu-Elem-Right"></div>
							</li>
							<li>
								<div class="P-PopUp-Menu-Elem-Left"></div>
								<div class="P-PopUp-Menu-Elem-Middle" onclick="ChangeFiguresForm(\'plate\', {document_id}, \'P-PopUp-Content-Inner\', 0, 0, 0, 0, 1);">Multiple images (plate)</div>
								<div class="P-PopUp-Menu-Elem-Right"></div>
							</li>
							<li>
								<div class="P-PopUp-Menu-Elem-Left"></div>
								<div class="P-PopUp-Menu-Elem-Middle" onclick="ChangeFiguresForm(\'video\', {document_id}, \'P-PopUp-Content-Inner\', 0, 0, 0, 0, 1);">Video</div>
								<div class="P-PopUp-Menu-Elem-Right"></div>
							</li>
							<div class="P-Clear"></div>
						</ul>
						<div class="P-Clear"></div>
						<script type="text/javascript">
							var menu = new popUpMenu(\'popUp_nav\');
						</script>
						<input type="hidden" id="plate_id_value" name="plate_id" value="0"/>
					</div>
					<div class="P-PopUp-Content-Inner">

					</div>
				</div>
				<div class="P-PopUp-Footer-Holder">
					<div class="P-Green-Btn-Holder P-90" id="P-Figures-Save-Button" onclick="SavePlateDataAndUpdateFiguresPopUp(\'photo_plate_description_textarea\', \'plate_id\', \'single_picture_id\', \'plate_photo_form\');">
						<div class="P-Green-Btn-Left"></div>
						<div class="P-Green-Btn-Middle">Save</div>
						<div class="P-Green-Btn-Right"></div>
					</div>
					<div class="P-Green-Btn-Holder P-90" style="display: none;" id="save_video_btn">
						<div class="P-Green-Btn-Left"></div>
						<div class="P-Green-Btn-Middle">Save</div>
						<div class="P-Green-Btn-Right"></div>
					</div>
					<div class="P-HSpace-10"></div>
					<div class="P-Grey-Btn-Holder" onclick="ShowDialogOnClose(\'#citation_flag\');popUp(POPUP_OPERS.close, \'add-figure-popup\', \'add-figure-popup\');ChangeFiguresForm(\'video\', {document_id}, \'P-PopUp-Content-Inner\', 1);">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>Close</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				</div>
			</div>
		</div>

	',

	'figures.plate_1_head' => '
		<form name="plate_photo_form">
			<div class="P-Data-Resources-Control-Txt">
				<div class="P-Data-Resources-Control-Left">
					Plate parts
					<span class="txtred">*</span>
				</div>
			</div>

	',

	'figures.plate_1_foot' => '
			{_AddPlatesIfEmpty(records, document_id, 1)}
		</form>
	',

	'figures.plate_1_row' => '
		<div class="P-Plate-Part-Holder">
			<input type="hidden" value="{picid}" name="picture_id_{picid}">
			<div class="P-Plate-Part-WithPic">
				<div id="figures_image_plate_holder_{picid}" class="P-Add-Plate-Holder">
					<img id="uploaded_photo" src="/showfigure.php?filename=c288x206y_{picid}.jpg&{rand}">
				</div>
				<script type="text/javascript">ajaxFileUpload(\'change_figure_plate_{picid}_photo\', \'figures_image_plate1_{picid}\', {document_id}, \'figures_image_plate_holder_{picid}\', \'picture_id_{picid}\', {plate_val}, \'c288x206y\', 0, {rownum});</script>
				<div class="P-Edit-Plate-Holder-Background"></div>
				<div class="P-Edit-Plate-Holder">
					<div class="P-Grey-Btn-Holder" onclick="">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle" id="change_figure_plate_{picid}_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.change_image') . '</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="P-VSpace-10"></div>
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="P-Clear"></div>
			<div class="P-Plate-Textarea-Holder">
				<textarea id="figures_plate1_{picid}_photo" name="{picid}">{description}</textarea>
			</div>
			<div class="P-Clear"></div>
		</div>
		<div class="P-Clear"></div>
	',

	'figures.empty_plate_holder_1' => '
		{_ClearRowFloat(curr_holder_id, 1)}
		<div class="P-Plate-Part-Holder">
			<input type="hidden" value="" name="picture_id_{holder_id}">
			<div class="P-Plate-Part">
				<div id="figures_image_plate_holder_row_{holder_id}" class="P-Add-Plate-Holder">
					<div class="P-Grey-Btn-Holder P-Add">
						<div class="P-Grey-Btn-Left"></div>
						<script type="text/javascript">ajaxFileUpload(\'add_figure_plate_row_{holder_id}_photo\', \'figures_image_plate1_row_{holder_id}\', {document_id}, \'figures_image_plate_holder_row_{holder_id}\', \'picture_id_{holder_id}\', {plate_val}, \'c288x410y\', 0, {holder_id});</script>
						<div class="P-Grey-Btn-Middle" id="add_figure_plate_row_{holder_id}_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.add_image') . ' {_GetPlateImageLetterByHolder(holder_id)}</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>

				</div>
				<script type="text/javascript">ajaxFileUpload(\'change_figure_plate_{holder_id}_photo\', \'figures_image_plate1_row_{holder_id}\', {document_id}, \'figures_image_plate_holder_row_{holder_id}\', \'picture_id_{holder_id}\', {plate_val}, \'c288x410y\', 0, {holder_id});</script>
				<div class="P-Edit-Plate-Holder-Background"></div>
				<div class="P-Edit-Plate-Holder">
					<div class="P-Grey-Btn-Holder" onclick="">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle" id="change_figure_plate_{holder_id}_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.change_image') . '</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="P-VSpace-10"></div>
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="P-Plate-Textarea-Holder">
				<textarea id="figures_plate1_{holder_id}_photo" name="{holder_id}"></textarea>
			</div>
		</div>
		{_ClearRowFloat(next_holder_id, 1)}
	',

	'figures.plate_2_head' => '
		<form name="plate_photo_form">
			<div class="P-Data-Resources-Control-Txt">
				<div class="P-Data-Resources-Control-Left">
					Plate parts
					<span class="txtred">*</span>
				</div>
			</div>
	',

	'figures.plate_2_foot' => '
			{_AddPlatesIfEmpty(records, document_id, 2)}
		</form>
	',

	'figures.plate_2_row' => '
		<div class="P-Plate-Part-Holder {_showRightPicMargin(rownum)}">
			<input type="hidden" value="{picid}" name="picture_id_{picid}">
			<div class="P-Plate-Part-WithPic P-Plate-Part-Big">
				<div id="figures_image_plate_holder_{picid}" class="P-Add-Plate-Holder">
					<img id="uploaded_photo_{picid}" src="/showfigure.php?filename=c288x410y_{picid}.jpg&{rand}">
				</div>
				<script type="text/javascript">ajaxFileUpload(\'change_figure_plate_{picid}_photo\', \'figures_image_plate1_{picid}\', {document_id}, \'figures_image_plate_holder_{picid}\', \'picture_id_{picid}\', {plate_val}, \'c288x410y\', 0, {rownum});</script>
				<div class="P-Edit-Plate-Holder-Background" onMouseOver=""></div>
				<div class="P-Edit-Plate-Holder">
					<div class="P-Grey-Btn-Holder" onclick="">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle" id="change_figure_plate_{picid}_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.change_image') . '</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="P-VSpace-10"></div>
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="P-Plate-Textarea-Holder">
				<textarea id="figures_plate1_{picid}_photo" name="{picid}">{description}</textarea>
			</div>
			<div class="P-Clear"></div>
		</div>

	',

	'figures.empty_plate_holder_2' => '
		{_ClearRowFloat(curr_holder_id, 2)}
		<div class="P-Plate-Part-Holder {_showRightPicMargin(holder_id)}">
			<input type="hidden" value="" name="picture_id_{holder_id}">
			<div class="P-Plate-Part P-Plate-Part-Big">
				<div id="figures_image_plate_holder_row_{holder_id}" class="P-Add-Plate-Holder">
					<div class="P-Grey-Btn-Holder P-Add">
						<div class="P-Grey-Btn-Left"></div>
						<script type="text/javascript">ajaxFileUpload(\'add_figure_plate_row_{holder_id}_photo\', \'figures_image_plate1_row_{holder_id}\', {document_id}, \'figures_image_plate_holder_row_{holder_id}\', \'picture_id_{holder_id}\', {plate_val}, \'c288x410y\', 0, {holder_id});</script>
						<div class="P-Grey-Btn-Middle" id="add_figure_plate_row_{holder_id}_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.add_image') . ' {_GetPlateImageLetterByHolder(holder_id)}</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				</div>
				<script type="text/javascript">ajaxFileUpload(\'change_figure_plate_{holder_id}_photo\', \'figures_image_plate1_row_{holder_id}\', {document_id}, \'figures_image_plate_holder_row_{holder_id}\', \'picture_id_{holder_id}\', {plate_val}, \'c288x410y\', 0, {holder_id});</script>
				<div class="P-Edit-Plate-Holder-Background"></div>
				<div class="P-Edit-Plate-Holder">
					<div class="P-Grey-Btn-Holder" onclick="">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle" id="change_figure_plate_{holder_id}_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.change_image') . '</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="P-VSpace-10"></div>
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="P-Plate-Textarea-Holder">
				<textarea id="figures_plate1_{holder_id}_photo" name="{holder_id}"></textarea>
			</div>
		</div>
		{_ClearRowFloat(next_holder_id, 2)}
	',

	'figures.plate_3_head' => '
		<form name="plate_photo_form">
			<div class="P-Data-Resources-Control-Txt">
				<div class="P-Data-Resources-Control-Left">
					Plate parts
					<span class="txtred">*</span>
				</div>
			</div>

	',

	'figures.plate_3_foot' => '
			{_AddPlatesIfEmpty(records, document_id, 3)}
		</form>
	',

	'figures.plate_3_row' => '
		<div class="P-Plate-Part-Holder {_showRightPicMargin(rownum)}">
			<input type="hidden" value="{picid}" name="picture_id_{picid}">
			<div class="P-Plate-Part-WithPic">
				<div id="figures_image_plate_holder_{picid}" class="P-Add-Plate-Holder">
					<img id="uploaded_photo_{picid}" src="/showfigure.php?filename=c288x206y_{picid}.jpg&{rand}">
				</div>
				<script type="text/javascript">ajaxFileUpload(\'change_figure_plate_{picid}_photo\', \'figures_image_plate1_{picid}\', {document_id}, \'figures_image_plate_holder_{picid}\', \'picture_id_{picid}\', {plate_val}, \'c288x206y\', 0, {rownum});</script>
				<div class="P-Edit-Plate-Holder-Background"></div>
				<div class="P-Edit-Plate-Holder">
					<div class="P-Grey-Btn-Holder">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle" id="change_figure_plate_{picid}_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.change_image') . '</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="P-VSpace-10"></div>
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="P-Plate-Textarea-Holder">
				<textarea id="figures_plate1_{picid}_photo" name="{picid}">{description}</textarea>
			</div>
			<div class="P-Clear"></div>
		</div>

	',

	'figures.empty_plate_holder_3' => '
		{_ClearRowFloat(curr_holder_id, 3)}
		<div class="P-Plate-Part-Holder {_showRightPicMargin(holder_id)}">
			<input type="hidden" value="" name="picture_id_{holder_id}">
			<div class="P-Plate-Part">
				<div id="figures_image_plate_holder_row_{holder_id}" class="P-Add-Plate-Holder">
					<div class="P-Grey-Btn-Holder P-Add">
						<div class="P-Grey-Btn-Left"></div>
						<script type="text/javascript">ajaxFileUpload(\'add_figure_plate_row_{holder_id}_photo\', \'figures_image_plate1_row_{holder_id}\', {document_id}, \'figures_image_plate_holder_row_{holder_id}\', \'picture_id_{holder_id}\', {plate_val}, \'c288x206y\', 0, {holder_id});</script>
						<div class="P-Grey-Btn-Middle" id="add_figure_plate_row_{holder_id}_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.add_image') . ' {_GetPlateImageLetterByHolder(holder_id)}</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				</div>
				<script type="text/javascript">ajaxFileUpload(\'change_figure_plate_{holder_id}_photo\', \'figures_image_plate1_row_{holder_id}\', {document_id}, \'figures_image_plate_holder_row_{holder_id}\', \'picture_id_{holder_id}\', {plate_val}, \'c288x206y\', 0, {holder_id});</script>
				<div class="P-Edit-Plate-Holder-Background"></div>
				<div class="P-Edit-Plate-Holder">
					<div class="P-Grey-Btn-Holder" onclick="">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle" id="change_figure_plate_{holder_id}_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.change_image') . '</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="P-VSpace-10"></div>
				</div>
			</div>
			<div class="P-Plate-Textarea-Holder">
				<textarea id="figures_plate1_{holder_id}_photo" name="{holder_id}"></textarea>
			</div>
		</div>
		{_ClearRowFloat(next_holder_id, 3)}
	',

	'figures.plate_4_head' => '
		<form name="plate_photo_form">
			<div class="P-Data-Resources-Control-Txt">
				<div class="P-Data-Resources-Control-Left">
					Plate parts
					<span class="txtred">*</span>
				</div>
			</div>

	',

	'figures.plate_4_foot' => '
			{_AddPlatesIfEmpty(records, document_id, 4)}
		</form>
	',

	'figures.plate_4_row' => '
		<div class="P-Plate-Part-Holder {_showRightPicMargin(rownum)}">
			<input type="hidden" value="{picid}" name="picture_id_{picid}">
			<div class="P-Plate-Part-WithPic">
				<div id="figures_image_plate_holder_{picid}" class="P-Add-Plate-Holder">
					<img id="uploaded_photo_{picid}" src="/showfigure.php?filename=c288x206y_{picid}.jpg&{rand}">
				</div>
				<script type="text/javascript">ajaxFileUpload(\'change_figure_plate_{picid}_photo\', \'figures_image_plate1_{picid}\', {document_id}, \'figures_image_plate_holder_{picid}\', \'picture_id_{picid}\', {plate_val}, \'c288x206y\', 0, {rownum});</script>
				<div class="P-Edit-Plate-Holder-Background"></div>
				<div class="P-Edit-Plate-Holder">
					<div class="P-Grey-Btn-Holder">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle" id="change_figure_plate_{picid}_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.change_image') . '</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="P-VSpace-10"></div>
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="P-Plate-Textarea-Holder">
				<textarea id="figures_plate1_{picid}_photo" name="{picid}">{description}</textarea>
			</div>
			<div class="P-Clear"></div>
		</div>

	',

	'figures.empty_plate_holder_4' => '
		{_ClearRowFloat(curr_holder_id, 4)}
		<div class="P-Plate-Part-Holder {_showRightPicMargin(holder_id)}">
			<input type="hidden" value="" name="picture_id_{holder_id}">
			<div class="P-Plate-Part">
				<div id="figures_image_plate_holder_row_{holder_id}" class="P-Add-Plate-Holder">
					<div class="P-Grey-Btn-Holder P-Add">
						<div class="P-Grey-Btn-Left"></div>
						<script type="text/javascript">ajaxFileUpload(\'add_figure_plate_row_{holder_id}_photo\', \'figures_image_plate1_row_{holder_id}\', {document_id}, \'figures_image_plate_holder_row_{holder_id}\', \'picture_id_{holder_id}\', {plate_val}, \'c288x206y\', 0, {holder_id});</script>
						<div class="P-Grey-Btn-Middle" id="add_figure_plate_row_{holder_id}_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.add_image') . ' {_GetPlateImageLetterByHolder(holder_id)}</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				</div>
				<script type="text/javascript">ajaxFileUpload(\'change_figure_plate_{holder_id}_photo\', \'figures_image_plate1_row_{holder_id}\', {document_id}, \'figures_image_plate_holder_row_{holder_id}\', \'picture_id_{holder_id}\', {plate_val}, \'c288x206y\', 0, {holder_id});</script>
				<div class="P-Edit-Plate-Holder-Background"></div>
				<div class="P-Edit-Plate-Holder">
					<div class="P-Grey-Btn-Holder" onclick="">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle" id="change_figure_plate_{holder_id}_photo"><div class="P-Btn-Icon"></div>' . getstr('pwt.change_image') . '</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="P-VSpace-10"></div>
				</div>
			</div>
			<div class="P-Plate-Textarea-Holder">
				<textarea id="figures_plate1_{holder_id}_photo" name="{holder_id}"></textarea>
			</div>
		</div>
		{_ClearRowFloat(next_holder_id, 4)}
	',

	'figures.single_ajax_row' => '
		<tr id="P-Figures-Row-{plate_id}{photo_id}" class="P-Data-Table-Holder">
			<td class="P-Picture-Holder" valign="top" >
				<div>
					{_showPlatePhotoSrc(plate_id, photo_id, c90x82y, photo_ids_arr, format_type, photo_positions_arr, ftype, link)}
					<div class="P-Clear"></div>
				</div>
			</td>
			<td class="P-Block-Title-Holder">
				<div class="P-Figure-Num">Figure {move_position}</div>
				<div class="P-Figure-Desc">{_showPlatePhotoDesc(plate_desc, photo_desc)}</div>
				<input type="hidden" name="plate_photo_id" value="{_showPlatePhotoVal(plate_id, photo_id)}"></input>
			</td>
			<td class="P-Block-Actions-Holder">
				<div class="P-Data-Resources-Head-Actions">
					<div onclick="{_showEditFigureAction(plate_id, photo_id, document_id, ftype)}" class="P-Grey-Btn-Holder2 P-Edit">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>{_EditFigurePhotoBtnText(photo_id, ftype)}</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					{_showUpDownMoveFigureArrows(records, rownum, document_id, plate_id, photo_id, move_position, false)}
					<div class="P-Remove-Btn-Holder P-Remove-Right">
						<div class="P-Remove-Btn-Left"></div>
						<div class="P-Remove-Btn-Middle" onclick="{_showDeletePlatePhotoAction(plate_id, photo_id, document_id)}">Remove</div>
						<div class="P-Remove-Btn-Right"></div>
					</div>
				</div>
			</td>
		</tr>
	',

	'figures.single_ajax_row_notr' => '
			<td class="P-Picture-Holder" valign="top" >
				<div>
					{_showPlatePhotoSrc(plate_id, photo_id, c90x82y, photo_ids_arr, format_type, photo_positions_arr, ftype, link)}
					<div class="P-Clear"></div>
				</div>
			</td>
			<td class="P-Block-Title-Holder">
				<div class="P-Figure-Num">Figure {move_position}</div>
				<div class="P-Figure-Desc">{_showPlatePhotoDesc(plate_desc, photo_desc)}</div>
				<input type="hidden" name="plate_photo_id" value="{_showPlatePhotoVal(plate_id, photo_id)}"></input>
			</td>
			<td class="P-Block-Actions-Holder">
				<div class="P-Data-Resources-Head-Actions">
					<div onclick="{_showEditFigureAction(plate_id, photo_id, document_id, ftype)}" class="P-Grey-Btn-Holder2 P-Edit">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>{_EditFigurePhotoBtnText(photo_id, ftype)}</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					{_showUpDownMoveFigurePositionArrows(document_id, plate_id, photo_id, curr_position, max_position, min_position)}
					<div class="P-Remove-Btn-Holder P-Remove-Right">
						<div class="P-Remove-Btn-Left"></div>
						<div class="P-Remove-Btn-Middle" onclick="{_showDeletePlatePhotoAction(plate_id, photo_id, document_id)}">Remove</div>
						<div class="P-Remove-Btn-Right"></div>
					</div>
				</div>
			</td>
	',

	'figures.zoomed_fig' => '
		<table>
			<tbody cellspacing="0" cellpadding="0" border="1">
				<tr>
					<td width="45%">&nbsp;</td>
					<td>
						<img alt="" src="' . SITE_URL . SHOWFIGURE_URL . 'big_{id}.jpg" />
					</td>	
					<td width="45%">&nbsp;</td>	
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="P-Fig-Caption-Large">
						{_unsafe_render_if(figure_descriprion, <div style="padding-bottom:10px">, </div>)}
						{photo_desc}	
					</td>	
					<td>&nbsp;</td>	
				</tr>		
			</tbody>
		</table>
		<div class="P-Clear"></div>
	',

	'figures.empty_row' => '
		<div class="P-Empty-Content">' . getstr('pwt.figures.nodata') . '</div>
	',
		
		
		
	//For figures preview list
	'figures.single_figure_preview' => '
		<div id="P-Figures-Row-{id}" class="P-PopUp-Data-Table-Row-Figs">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td class="P-PopUp-Checkbox-Holder" align="left" valign="top">
						<input type="checkbox" name="fig-{id}" id="fig-{id}" figurenum="{fignum}" value="{id}" figtype="{is_plate}" onclick="checkAllSubPhotos(this)"></input>
					</td>
					{preview}					
				</tr>
			</table>
			<div class="P-Clear"></div>
		</div>
		<div class="P-Clear"></div>
	',
		
	'figures.empty_row' => '
		<div class="P-Empty-Content">' . getstr('pwt.figures.nodata') . '</div>
	',
);

?>