// Този плугин реализира автосейв след блур ивент
CKEDITOR.plugins.add('autosave', {
	init : function(editor) {
		editor.on('blur', function(pElement) {
			
			var lPattern = new RegExp("^(\\d+)__(\\d+)","i"); // С това зимаме ид на инстанса и на филда
			var lRootInstanceId = GetRootInstanceId();
			var lLevel = getInstanceLevel(lRootInstanceId);
			var lFieldName = editor.name;
			var lMatch = lPattern.exec(lFieldName);
			var lForm = $('form[name="' + gDocumentFormName + '"]');	
			var lValue = editor.getData();
			
			// We must update the textarea element
			this.updateElement();
			
			if(lMatch !== null && gStopAutoSaveInstance == 0){
				// submitting the form
				gAutoSaveFlag = 1;
				
				lForm.ajaxSubmit({
					'dataType' : 'json',
					'url' : gSaveInstanceSrv,
					'root_instance_id' : lRootInstanceId,
					'async': false,
					'data' : {
						'real_instance_id' : lMatch[1],
						'root_instance_id' : lRootInstanceId,
						'document_id' : GetDocumentId(),
						'level' : lLevel,
						'explicit_field_id' : lMatch[2],
						'auto_save_on' : 1
					},
					'success' : function(pAjaxResult){
						gAutoSaveFlag = 0;
						
						if(pAjaxResult['err_cnt']){
							
						}else{
							
						}
					}
				});
			} else {
				gStopAutoSaveInstance = 0;
			}
		});
		editor.on('key', function(pElement) {
			// We must update the textarea element
			// This is because if we submit something with ajax it must update the textarea before the ajax
			this.updateElement();
		});
		editor.on('paste', function(pElement) {
			// We must update the textarea element
			// This is because if we submit something with ajax it must update the textarea before the ajax
			this.updateElement();
		});
	}
});
