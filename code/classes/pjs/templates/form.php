<?php

$gTemplArr = array(
	'form.default_header' => '
		<div class="formWrapper">
		<form enctype="multipart/form-data" action="{^form_action}" method="{^form_method}" name="{@form_name}" {_htmlformid(htmlformid)}>
			{form_name}{selfurl}{backurl}
	',

	'form.default_header_version_popup' => '
		<div class="formWrapper" id="P-Version-PopUp-Form">
		<form enctype="multipart/form-data" action="{^form_action}" method="{^form_method}" name="{@form_name}" {_htmlformid(htmlformid)}>
			{form_name}{selfurl}{backurl}
	',

	'form.default_footer' => '
		</form>
		</div>
	',

	'form.text_input_row' => '
			<input type="text" name="{field_name}" value="{_h(field_cur_value)}" {additional_tags_string} />{calendar_icon}
	',

	'form.password_input_row' => '
			<input type="password" name="{field_name}" value="{_h(field_cur_value)}" {additional_tags_string} />{calendar_icon}
	',

	'form.hidden_input_row' => '
			<input type="hidden" name="{field_name}" value="{_h(field_cur_value)}" {additional_tags_string} />{calendar_icon}
	',

	'form.file_input_row' => '
			<input type="file" name="{field_name}" value="{_h(field_cur_value)}" {additional_tags_string} />{calendar_icon}
	',

	'form.textarea_input_row' => '
			<textarea name="{field_name}" {additional_tags_string} >{_h(field_cur_value)}</textarea>
	',

	'form.select_input_start' => '
			<select name="{field_name}" {additional_tags_string}>
	',

	'form.select_input_end' => '
			</select>
	',

	'form.select_input_row' => '
				<option {row_additional_tags_string} value="{value_key}" {_checkIfFormSelectRowIsSelected(value_is_selected)}>{value_label}</option>
	',

	'form.multiple_select_input_start' => '
				<select multiple name="{field_name}[]" {additional_tags_string}>
	',

	'form.multiple_select_input_end' => '
				{*form.select_input_end}
	',

	'form.multiple_select_input_row' => '
				{*form.select_input_row}
	',

	'form.radio_input_start' => '
	',

	'form.radio_input_end' => '
	',

	'form.radio_input_row' => '
				<input type="radio" name="{field_name}" value="{value_key}" {row_additional_tags_string} {_checkIfFormCheckboxRowIsSelected(value_is_selected)} />
				{value_label}<br/>
	',
	
	'form.radio_input_row_label_for' => '
				<input type="radio" id="{field_name}_{value_key}" name="{field_name}" value="{value_key}" {row_additional_tags_string} {_checkIfFormCheckboxRowIsSelected(value_is_selected)} />
				<label for="{field_name}_{value_key}">{value_label}</label><br/>
	',
	
	'form.radio_input_row_td' => '
				<td class="center"><input type="radio" name="{field_name}" value="{value_key}" {row_additional_tags_string} {_checkIfFormCheckboxRowIsSelected(value_is_selected)} />
				<span>{value_label}</span></td>
	',

	'form.radio_input_row_td_article_search' => '
				<td align="left">
					<input id="article_radio_{value_key}" type="radio" name="{field_name}" value="{value_key}" {row_additional_tags_string} {_checkIfFormCheckboxRowIsSelected(value_is_selected)} />
					<label for="article_radio_{value_key}">{value_label}</label>
				</td>
	',

	'form.checkbox_input_start' => '
	',

	'form.checkbox_input_end' => '
	',

	'form.checkbox_input_row' => '
				<input type="checkbox" name="{field_name}[]" value="{value_key}" {row_additional_tags_string} {_checkIfFormCheckboxRowIsSelected(value_is_selected)} />
				{value_label}<br/><br/>
	',
	
	'form.checkbox_input_row_with_label' => '
				<input onclick="$(\'#filter_articles\').submit()" type="checkbox" name="{field_name}[]" id="chk_{value_key}" value="{value_key}" {row_additional_tags_string} {_checkIfFormCheckboxRowIsSelected(value_is_selected)} />
				<label for="chk_{value_key}">{value_label}</label><br/><br/>
	',
	
	'form.checkbox_input_row_no_br' => '
				<input type="checkbox" name="{field_name}[]" value="{value_key}" {row_additional_tags_string} {_checkIfFormCheckboxRowIsSelected(value_is_selected)} />
				{value_label}
	',
	
	'form.checkbox_input_row_filer_articles' => '
				<input type="checkbox" name="{field_name}[]" value="{value_key}" onclick="$(\'#filter_articles\').submit()" {row_additional_tags_string} {_checkIfFormCheckboxRowIsSelected(value_is_selected)} />
				{value_label}<br/><br/>
	',
	
	'form.checkbox_input_row_label_for' => '
				<input  id="{field_name}_{value_key}" type="checkbox" name="{field_name}[]" value="{value_key}" {row_additional_tags_string} {_checkIfFormCheckboxRowIsSelected(value_is_selected)} />
				<label for="{field_name}_{value_key}">{value_label}</label><br/><br/>
	',
	

	'form.action_submit_btn_row' => '
			<input type="submit" name="tAction" value="{field_displayname}" {additional_tags_string} />
	',

	'form.action_image_row' => '
			<input type="image" src="{field_imgsrc}" name="tAction" value="{field_displayname}" {additional_tags_string} />
	',

	'form.action_link_row' => '
			<input type="submit" name="tAction" id="{field_name}" value="{field_name}" {additional_tags_string} style="display: none" />
			<a href="#" onclick="javascript: document.getElementById(\'{field_name}\').click();return false;">{field_displayname}</a>
	',
	
	'form.field_error_header' => '
			<{field_error_templ} id="{field_error_templ_id}" style="display:{field_show_hide_err_holder}" class="errstr">
	',

	'form.field_error_row' => '
			{field_label}: {field_err_msg}<br/>
	',
	
	'form.field_error_row_without_field_name' => '
			{field_err_msg}<br/>
	',
	
	
	'form.field_error_footer' => '
			</{field_error_templ}>
	',
	
	'form.global_error_row' => '
			{global_err_msg}<br/>
	',

	'form.captcha_row' => '
			<div class="capholder">
				<div class="capcode">
					<img src="/lib/frmcaptcha.php" id="cappic" border="0" alt="" /><br />
					<a class="antet" href="javascript: void(0);" onclick="return reloadCaptcha();">' . getstr('register.php.generatenew') . '</a>
				</div>
				<div class="loginFormRowHolder capinfo">
					<div class="loginFormLabel">' . getstr('register.php.spamconfirm') . ' <span>*</span></div>
					<div class="P-Input-Full-Width P-W300">
						<div class="P-Input-Holder">
							<div class="P-Input-Left"></div>
							<div class="P-Input-Middle">
								<input type="text" name="captcha" id="captcha" class="inputFld" onblur="changeFocus(2, this)" onfocus="changeFocus(1, this)" />			
							</div>
							<div class="P-Input-Right"></div>
							<div class="P-Clear"></div>
						</div>
					</div>
				</div>
				<div class="P-Clear"></div>
			</div>
	',

		'form.js_validation' => '
		<script type="text/javascript">
					$(\'#{field_id}\').bind(\'{field_check_event}\', function() {
						var formdata = $(\'#{field_id}\').closest(\'form\').serialize();
						$.ajax({
							type: \'POST\',
							url: \'' . DEF_AJAXCHECK_URL . '?check_field={field_name}&fields_templ_name={field_templ_name}\',
							data: formdata,
							dataType: \'json\',
							success: function(data) {
								if(data.error_string) {
									$(\'#\' + data.error_field).addClass(data.error_field_class);
									$(\'#\' + data.error_holder).html(data.error_string);
									$(\'#\' + data.error_holder).addClass(\'' . G_FIELD_ERROR_HOLDER_CLASS . '\');
									

									if(data.ajax_error_js) {
										$(\'#\' + data.error_holder).hide();
										eval(data.ajax_error_js);
									} else 
									
									if(data.error_js) {
										$(\'#\' + data.error_holder).show();
										eval(data.error_js);
									}
									
								} else {
									$(\'#\' + data.error_field).removeClass(data.error_field_class);
									$(\'#\' + data.error_holder).html(\'\');
									$(\'#\' + data.error_holder).removeClass(\'' . G_FIELD_ERROR_HOLDER_CLASS . '\');
									$(\'#\' + data.error_holder).hide();
									if(data.valid_js) {
										eval(data.valid_js);
									}
								}
							}
						});
					});
					{field_additional_js}
				</script>
	', 
	
	'form.js_only' => '
		<script type="text/javascript">
			{field_additional_js}
		</script>
	',

	// custom templates
	// step 2
	'form.registep_radio_input_row' => '
				<div class="P-User-Type-Radios">
					<input type="radio" name="{field_name}" value="{value_key}" {row_additional_tags_string} {_checkIfFormCheckboxRowIsSelected(value_is_selected)} />
					{value_label}
				</div>
	',

	// step 3
	'form.registep3_radio_input_row' => '
				<div class="P-Alerts-Radios">
					<input type="radio" name="{field_name}" value="{value_key}" {row_additional_tags_string} {_checkIfFormCheckboxRowIsSelected(value_is_selected)} />
					{value_label}
				</div>
	',

	'form.registep_checkbox_input_row' => '
				<div class="P-Registration-Email-Alerts-Journal-Checks">
					<input type="checkbox" name="{field_name}[]" value="{value_key}" {row_additional_tags_string} {_checkIfFormCheckboxRowIsSelected(value_is_selected)} />
					{value_label}
				</div>
	',
	
	'form.journal_story_checkbox_input_row' => '
				<input type="checkbox" name="{field_name}" value="{value_key}" {row_additional_tags_string} {_checkIfFormCheckboxRowIsSelected(value_is_selected)} />
				{value_label}<br/>
	',
	
	//Journal Sections 
	'form.checkbox_input_row_without_array_name' => '
				<input type="checkbox" name="{field_name}" value="{value_key}" {row_additional_tags_string} {_checkIfFormCheckboxRowIsSelected(value_is_selected)} />
				{value_label}<br/>
	',
	
	'form.radio_input_row_label_for_with_title' => '
				<input type="radio" id="{field_name}_{value_key}" name="{field_name}" value="{value_key}" {row_additional_tags_string} {_checkIfFormCheckboxRowIsSelected(value_is_selected)} />
				<label for="{field_name}_{value_key}" title="{field_select_title}">{value_label}</label><br/>
	',
	
	'form.richtext_editor_row' => '
		<textarea name="{field_name}" id="textarea_{field_name}" {additional_tags_string} >{_h(field_cur_value)}</textarea>
		<script  type="text/javascript">
			CKEDITOR.config.language = \'en\';
			CKEDITOR.replace(\'textarea_{field_name}\', {
				on: {
					key: function( evt ) {
						var leditor = evt.editor;
						leditor.updateElement();
					},
					paste: function( evt ) {
						var leditor = evt.editor;
						leditor.updateElement();
					},
					blur: function( evt ) {
						var leditor = evt.editor;
						leditor.updateElement();
					}
				},
				toolbar : \'SmallToolbar\',
				removePlugins: \'elementspath,resize\',
				height: 120,
				width: 451
			});
		</script>
	',
);

?>