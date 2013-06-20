

CKEDITOR.plugins.add('imgs', {
	init : function(editor) {
		var pluginName = 'imgs';
		var lIconPath = this.path + 'images/icon.png';
		var lEditorName = editor.name;
		var lImage;
		
		// Plugin logic goes here...
		var lIconPath = this.path + 'images/icon.png';
		 editor.ui.addButton('Imgs', {
			label: 'Insert Image',
			command : 'displayUploadedImages',
			icon: lIconPath
		});
		editor.addCommand('displayUploadedImages', new CKEDITOR.dialogCommand('displayUploadedImagesDialog'));
		
		CKEDITOR.dialog.add('displayUploadedImagesDialog', function(editor) {
			return {
				title : 'Upload Images',
				minWidth : 650,
				minHeight : 400,
				resizable : CKEDITOR.DIALOG_RESIZE_NONE,
				contents : [ {
					id : 'uploadImages',
					label : 'Upload Images',
					elements : [ {
						type : 'html',
						html : '<div id="photos_holder"><iframe id="relpicsframe" name="relpframe" style="background-image: initial; background-attachment: initial; background-origin: initial; background-clip: initial; background-color: rgb(255, 255, 255); border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-width: 1px; border-top-style: solid; border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-top-color: rgb(153, 153, 153); border-right-color: rgb(153, 153, 153); border-bottom-color: rgb(153, 153, 153); border-left-color: rgb(153, 153, 153); border-image: initial; background-position: initial initial; background-repeat: initial initial; " height="300px" width="618px" scrolling="auto" src="/resources/stories/selphoto.php?editorflg=1"></iframe></div>',
						commit : function(pElement, pId) {
							pElement.setAttribute('src', '/showimg.php?filename=s100_' + pId + '.jpg');
							pElement.setAttribute('style', 'width: 100px; height: 80px;');
							var lDialog = CKEDITOR.dialog.getCurrent();
							var lEditor = lDialog.getParentEditor();
							lEditor.insertElement(pElement);
						}
					} ]
				} ],
				onShow : function() {
					var lSelection = editor.getSelection();
					var lElement = lSelection.getStartElement();
					if (lElement) {
						lElement = lElement.getAscendant('img', true);
					}
					if (!lElement || lElement.getName() != 'img' || lElement.data('cke-realelement')) {
						lElement = editor.document.createElement('img');
						//lElement.setAttribute('contentEditable', 'false');
						this.insertMode = true;
					} else {
						this.insertMode = false;
					}
					this.element = lElement;
					this.setupContent(lElement);
					//getSitePhotos(0);
				},
				
				buttons : [ {
					id : 'addNewImg',
					type : 'button',
					label : 'Качи нова снимка',
					className : 'P-Dialog-Button-AddNewImg',
					title : 'Качи нова снимка',
					disabled : false,
					onClick : function() {
						$('#relpicsframe').attr('src', '/media/photos/edit.php?editorflg=1');
						$('#photos_holder').closest('table').closest('tr').closest('table').find('tr:last').hide();
					}
				}, {
					id : 'closeDialog',
					type : 'button',
					label : 'Отказ',
					title : 'Откажи',
					disabled : false,
					className : 'P-Dialog-Button-Close',
					style : '',
					onClick : function() {
						CKEDITOR.dialog.getCurrent().hide();
					}
				} ],
				
				onOk : function() {
					//~ lElement = CKEDITOR.dialog.getCurrent().element;
					//~ CKEDITOR.dialog.getCurrent().commitContent(lElement);
				}
			};
		});
	}
});