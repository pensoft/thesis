var gSupFileCitationHolderTagName = 'sup-files-citation';
var gSupFileCitationFileIdAttributeName = 'fid';

var gSupFileCitationType = 4;

supFilesCitation = function(pSupFilesHolderId) {
	this.init(pSupFilesHolderId);
};

supFilesCitation.prototype.init = function(pSupFilesHolderId) {
	this.m_supFilesHolderId = pSupFilesHolderId;
	GetDocumentSupFiles(GetDocumentId(), this.m_supFilesHolderId);
	this.m_selectedSupFiles = new Array();
};

supFilesCitation.prototype.addSupFile = function(pSupFileId) {
	if (this.m_selectedSupFiles.indexOf(pSupFileId) == -1) {
		this.m_selectedSupFiles.push(pSupFileId);
	}
};

supFilesCitation.prototype.removeSupFile = function(pSupFileId) {
	var lSupFilePos = this.m_selectedSupFiles.indexOf(pSupFileId);
	if (lSupFilePos != -1) {
		this.m_selectedSupFiles.splice(lSupFilePos, 1);
	}
};

supFilesCitation.prototype.getSelectedSupFiles = function() {
	return this.m_selectedSupFiles;
};

CKEDITOR.plugins.add('sup_files', {
	init : function(editor) {
		var lEditorName = editor.name;
		var lRegMatch = RegExp("(\\d+)" + gInstanceFieldNameSeparator + "(\\d+)");
		var lMatch = lEditorName.match(lRegMatch);

		var lInstanceCitations = new Array();
		var lInstanceId = 0;
		var lFieldId = 0;
		if (lMatch && lMatch.length > 0) {
			lInstanceId = lMatch[1];
			lFieldId = lMatch[2];
			lInstanceCitations = getInstanceFieldCitations(lInstanceId, lFieldId, gSupFileCitationType);
		}

		editor.m_instanceId = lInstanceId;
		editor.m_fieldId = lFieldId;
		editor.m_instanceSupFileCitations = lInstanceCitations;
		editor.m_supFilesHolderId = gSupFilesHolderId + '_' + editor.m_instanceId + '_' + editor.m_fieldId;

		// Plugin logic goes here...
		var lIconPath = this.path + 'images/icon.png';
		editor.ui.addButton('SupFiles', {
			label : 'Cite suppl. material',
			title : 'Insert smart suppl. material citation.&#10;Do not type citations manually.',
			command : 'displaySupFileCitationDialog',
			icon : lIconPath
		});

		editor.addCommand('displaySupFileCitationDialog', new CKEDITOR.dialogCommand( 'supFilesCitationDialog' ));

		editor.addCommand('deleteSupFilesCitation', {
			exec : function(editor) {
				var lSelection = editor.getSelection();
				var lElement = lSelection.getStartElement();
				if (lElement) {
					lElement = lElement.getAscendant(gSupFileCitationHolderTagName, true);
				}

				if (lElement && lElement.getName() == gSupFileCitationHolderTagName && !lElement.data('cke-realelement')) {
					var lCitationId = lElement.getAttribute('citation_id');
					if (lCitationId) {
						PerformRemoveCitation(lCitationId);
					}

					lElement.remove();
					editor.fire('blur');
				}
			},
			modes : {
				wysiwyg : 1
			}
		});

		editor.addCommand('addSupFilesCitation', {
			exec : function(editor) {
				var lSelection = editor.getSelection();
				var lElement = lSelection.getStartElement();
				if (lElement) {
					lElement = lElement.getAscendant(gSupFileCitationHolderTagName, true);
				}

				if (lElement && lElement.getName() == gSupFileCitationHolderTagName && !lElement.data('cke-realelement')) {
					lElement.remove();
				}
			},
			modes : {
				wysiwyg : 1
			}
		});

		/**
		 * При запазване на полето трябва да оставим само възела
		 */
		editor.dataProcessor.htmlFilter.addRules({
			elements : {
				'sup-files-citation' : function(pElement) {
					while (pElement.children.length) {
						pElement.children.pop();
					}
				}
			}
		});
		/**
		 * При показване на цитацията - трябва да заредим данните и от базата
		 */
		editor.dataProcessor.dataFilter.addRules({
			elements : {
				'sup-files-citation' : function(pElement) {

					while (pElement.children.length) {
						pElement.children.pop();
					}
					pElement.attributes.contentEditable = 'false';
					var lCitationId = pElement.attributes.citation_id;
					if (lCitationId && editor.m_instanceSupFileCitations[lCitationId]) {
						if (editor.m_instanceSupFileCitations[lCitationId]['preview']) {
							pElement.add(CKEDITOR.htmlParser.fragment.fromHtml(editor.m_instanceSupFileCitations[lCitationId]['preview']));
							return;
						}
					} else {// Няма такава цитация - може да е изтрита в базата
							// след изтриване на всички елементи в нея.
							// Изтриваме я
						return false;
					}
					// console.log('Load');
				}
			}
		});

		if (editor.contextMenu) {
			editor.addMenuGroup('supFilesCitationGroup');
			editor.addMenuItem('editSupFileCitation', {
				label : 'Edit suppl. material citation',
				icon : lIconPath,
				command : 'displaySupFileCitationDialog',
				group : 'supFilesCitationGroup'
			});
			editor.addMenuItem('deleteSupFileCitationItem', {
				label : 'Delete suppl. material citation',
				icon : lIconPath,
				command : 'deleteSupFilesCitation',
				group : 'supFilesCitationGroup'
			});
			// Code creating context menu items goes here.
			editor.contextMenu.addListener(function(pElement) {
				if ( pElement.getAscendant( gSupFileCitationHolderTagName, true ) ) {
					return {
						editSupFileCitation : CKEDITOR.TRISTATE_OFF,
						deleteSupFileCitationItem : CKEDITOR.TRISTATE_OFF
					};
				}
			});
		}

		CKEDITOR.dialog.add('supFilesCitationDialog', function(editor) {
			return {
				title : 'Citation Properties',
				minWidth : 400,
				minHeight : 200,
				contents : [ {
					id : 'supFilesDialog',
					label : 'Settings',
					elements : [ {
						type : 'html',
						html : '<div class="' + gSupFilesHolderId + '" id="' + editor.m_supFilesHolderId + '"></div>',
						commit : function(pElement, supFilesObject, pInsertMode) {
							var lSelectedSupFiles = supFilesObject.getSelectedSupFiles();

							if (lSelectedSupFiles.length > 0) {
								pElement.setAttribute('contentEditable', 'false');
								pElement.addClass('P-SupFiles-Citation-Holder');

								pElement.setHtml('');

								var lCitationData = PerformCitationSave(editor.m_instanceId, editor.m_fieldId, pElement.getAttribute('citation_id'), gSupFileCitationType, supFilesObject.getSelectedSupFiles(), 0);

								pElement.setHtml(lCitationData['preview']);

								if (lCitationData !== false) {
									// Ако всичко е ок - добавяме цитацията в
									// текста
									editor.m_instanceSupFileCitations[lCitationData['citation_id']] = lCitationData;
									pElement.setAttribute('citation_id', lCitationData['citation_id']);
									var lDialog = CKEDITOR.dialog.getCurrent();
									var lEditor = lDialog.getParentEditor();
									lEditor.insertElement(pElement);
									if (pInsertMode) {
										autoSaveInstance();
									}
								} else {
									// Ако има проблем - трием елемента
									pElement.$.parentNode.removeChild(pElement.$);
								}
							}
						}
					} ]
				} ],
				onShow : function() {
					CKEDITOR.dialog.getCurrent().resize($(window).width() - 200, $(window).height() - 200);
					CKEDITOR.dialog.getCurrent().move(75, 40);
					$(CKEDITOR.dialog.getCurrent().getElement().$).find('div[name="supFilesDialog"]').height('100%');
					$(CKEDITOR.dialog.getCurrent().getElement().$).find('div[name="supFilesDialog"] > supFile').css({'float' : 'left'});

					$('#' + editor.m_supFilesHolderId).html('');

					var lSelection = editor.getSelection();
					var lElement = lSelection.getStartElement();
					if (lElement) {
						lElement = lElement.getAscendant(gSupFileCitationHolderTagName, true);
					}
					if (!lElement || lElement.getName() != gSupFileCitationHolderTagName || lElement.data('cke-realelement')) {
						lElement = editor.document.createElement(gSupFileCitationHolderTagName);
						lElement.setAttribute('contentEditable', 'false');
						this.insertMode = true;
					} else {
						this.insertMode = false;
					}

					this.element = lElement;
					this.setupContent(this.element);

					this.supFilesObject = new supFilesCitation(editor.m_supFilesHolderId);

					lSupFilesObject = this.supFilesObject;

					$('#' + editor.m_supFilesHolderId).find('.P-PopUp-Checkbox-Holder').each(function() {
						var lCheck = $(this).find(':checkbox');
						lCheck.change(function() {
							if ($(this).is(':checked')) {
								lSupFilesObject.addSupFile($(this).val());
							} else {
								lSupFilesObject.removeSupFile($(this).val());
							}
						});
					});

					if (!this.insertMode) { // Ако едитваме трябва да чекнем
											// всички елементи от цитацията
						pElement = CKEDITOR.dialog.getCurrent();
						if (pElement) {
							pElement = pElement.element;
							for ( var i = 0; i < pElement.getChildCount(); ++i) {
								var lChild = pElement.getChild(i);
								if (lChild.$.nodeType == 1 && lChild.$.nodeName.toLowerCase() == 'xref') {
									$('#' + editor.m_supFilesHolderId).find(':checkbox').each(function() {
										if ($(this).val() == lChild.getAttribute(gSupFileCitationFileIdAttributeName)) {
											$(this).attr('checked', 'checked');
										}
										if ($(this).is(':checked')) {
											lSupFilesObject.addSupFile($(this).val());
										} else {
											lSupFilesObject.removeSupFile($(this).val());
										}
									});
								}
							}
						}
					}
				},
				buttons : [ {
					id : 'addSupFiles',
					type : 'button',
					label : 'Cite selected file(s)',
					className : 'P-Dialog-Button-InsertSelected',
					title : '',
					disabled : false,
					onClick : function() {
						lCitation = CKEDITOR.dialog.getCurrent().element;
						if (CKEDITOR.dialog.getCurrent().insertMode) {
							var lSelection = editor.getSelection();
							var lRanges = lSelection.getRanges(true);

							if (lRanges.length > 0) {
								lRanges[lRanges.length - 1].collapse();
								lSelection.selectRanges([ lRanges[lRanges.length - 1] ]);
							}
							editor.insertElement(lCitation);
						}
						CKEDITOR.dialog.getCurrent().commitContent(lCitation, CKEDITOR.dialog.getCurrent().supFilesObject, CKEDITOR.dialog.getCurrent().insertMode);
						editor.fire('blur');
						CKEDITOR.dialog.getCurrent().hide();
						$('#' + editor.m_supFilesHolderId).html('');
					}
				}, {
					id : '',
					type : 'button',
					label : 'Add new file',
					className : 'P-Dialog-Button-AddNewFigure',
					title : '',
					disabled : false,
					onClick : function() {
						gCurrentDialog = CKEDITOR.dialog.getCurrent();
						gCurrentDialog.hide();
						CreateNewSupFilePopup(1);
						// popUp(POPUP_OPERS.open, 'add-supFile-popup',
						// 'add-supFile-popup');
					}
				}, {
					id : 'closeDialog',
					type : 'button',
					label : '',
					title : '',
					disabled : false,
					className : 'P-Dialog-Button-Close',
					style : '',
					onClick : function() {
						CKEDITOR.dialog.getCurrent().hide();
						$('#' + editor.m_supFilesHolderId).html('');
					}
				} ]
			};
		});
	}
});
