// Този плугин реализира автосейв след блур ивент
CKEDITOR.plugins.add('autosave', {
	init : function(editor) {
		editor.on('blur', function(pElement) {
			var lAOFCommentForm = $('form[name="article_comments_form"]');
			if(lAOFCommentForm && typeof lAOFCommentForm != 'undefined') {
				this.updateElement();
				PerformAOFCommentFormAutosave();
			}
		});
	}
});
