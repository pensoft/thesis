// Този плугин реализира автосейв след блур ивент
CKEDITOR.plugins.add('autosave', {
	init : function(editor) {
		editor.on('blur', function(pElement) {			
			PerformReviewFormAutosave();
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
