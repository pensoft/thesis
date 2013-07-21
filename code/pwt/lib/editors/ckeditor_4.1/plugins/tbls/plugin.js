var gTableCitationHolderTagName = 'tbls-citation';

var gTableCitationType = 2;

tablesCitation = function(pTablesHolderId) {
	this.init(pTablesHolderId);
};

tablesCitation.prototype.init = function(pTablesHolderId) {
	this.m_tablesHolderId = pTablesHolderId;
	GetDocumentTables(GetDocumentId(), this.m_tablesHolderId);
	this.m_selectedTables = new Array();
}

tablesCitation.prototype.addTable = function(pTableId) {
	if (this.m_selectedTables.indexOf(pTableId) == -1) {
		this.m_selectedTables.push(pTableId);
	}
};

tablesCitation.prototype.removeTable = function(pTableId) {
	var lTablePos = this.m_selectedTables.indexOf(pTableId);
	if (lTablePos != -1) {
		this.m_selectedTables.splice(lTablePos, 1);
	}
};

tablesCitation.prototype.getSelectedTables = function() {
	return this.m_selectedTables;
};

CKEDITOR.plugins.add('tbls', {
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
			lInstanceCitations = getInstanceFieldCitations(lInstanceId, lFieldId, gTableCitationType);
		}

		editor.m_instanceId = lInstanceId;
		editor.m_fieldId = lFieldId;
		editor.m_instanceTableCitations = lInstanceCitations;
		editor.m_tablesHolderId = gTablesHolderId + '_' + editor.m_instanceId + '_' + editor.m_fieldId;

		// Plugin logic goes here...
		var lIconPath = this.path + 'images/icon.png';
		editor.ui.addButton('Tbls', {
			label : 'Cite table',
			title : 'Insert smart table citation.&#10;Do not type citations manually.',
			command : 'displayTableCitationDialog',
			icon : lIconPath
		});

		editor.addCommand('displayTableCitationDialog', new CKEDITOR.dialogCommand('tablesCitationDialog'));

		editor.addCommand('editFigCitation', {
			exec : function(editor) {
				alert(1);
			},
			modes : {
				wysiwyg : 1
			}
		});

		editor.addCommand('deleteTblsCitation', {
			exec : function(editor) {
				var lSelection = editor.getSelection();
				var lElement = lSelection.getStartElement();
				if (lElement) {
					lElement = lElement.getAscendant('tbls-citation', true);
				}

				if (lElement && lElement.getName() == gTableCitationHolderTagName && !lElement.data('cke-realelement')) {
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

		editor.addCommand('addTblsCitation', {
			exec : function(editor) {
				var lSelection = editor.getSelection();
				var lElement = lSelection.getStartElement();
				if (lElement) {
					lElement = lElement.getAscendant('tbls-citation', true);
				}

				if (lElement && lElement.getName() == 'tbls-citation' && !lElement.data('cke-realelement')) {
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
				'tbls-citation' : function(pElement) {
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
				'tbls-citation' : function(pElement) {

					while (pElement.children.length) {
						pElement.children.pop();
					}
					var lCitationId = pElement.attributes.citation_id;
					if (lCitationId && editor.m_instanceTableCitations[lCitationId]) {
						if (editor.m_instanceTableCitations[lCitationId]['preview']) {
							pElement.add(CKEDITOR.htmlParser.fragment.fromHtml(editor.m_instanceTableCitations[lCitationId]['preview']));
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
			editor.addMenuGroup('tblsCitationGroup');
			editor.addMenuItem('editTableCitation', {
				label : 'Edit table citation',
				icon : lIconPath,
				command : 'displayTableCitationDialog',
				group : 'tblsCitationGroup'
			});
			editor.addMenuItem('deleteTblsCitationItem', {
				label : 'Delete table citation',
				icon : lIconPath,
				command : 'deleteTblsCitation',
				group : 'tblsCitationGroup'
			});
			// Code creating context menu items goes here.
			editor.contextMenu.addListener(function(pElement) {
				// we remove the context menu for Chrome because there is bug with contenteditable attribute in refs, tbls, figs
				var isChrome = /chrome/i.test(navigator.userAgent);
				if(!isChrome) {
					if (pElement)
						pElement = pElement.getAscendant('tbls-citation', true);
					if (pElement && !pElement.data('cke-realelement'))
						return {
							editTableCitation : CKEDITOR.TRISTATE_OFF,
							deleteTblsCitationItem : CKEDITOR.TRISTATE_OFF
						};
				}
				return null;
			});
		}

		CKEDITOR.dialog.add('tablesCitationDialog', function(editor) {
			return {
				title : 'Citation Properties',
				minWidth : 400,
				minHeight : 200,
				contents : [ {
					id : 'tablesDialog',
					label : 'Settings',
					elements : [ {
						type : 'html',
						html : '<div class="' + gTablesHolderId + '" id="' + editor.m_tablesHolderId + '"></div>',
						commit : function(pElement, tablesObject, pInsertMode) {
							var lSelectedTables = tablesObject.getSelectedTables();

							if (lSelectedTables.length > 0) {
								pElement.setAttribute('contentEditable', 'false');
								pElement.addClass('P-Tables-Citation-Holder');

								pElement.setHtml('');

								var lCitationData = PerformCitationSave(editor.m_instanceId, editor.m_fieldId, pElement.getAttribute('citation_id'), gTableCitationType, tablesObject.getSelectedTables(), 0);

								pElement.setHtml(lCitationData['preview']);

								if (lCitationData !== false) {
									// Ако всичко е ок - добавяме цитацията в
									// текста
									editor.m_instanceTableCitations[lCitationData['citation_id']] = lCitationData;
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
					$(CKEDITOR.dialog.getCurrent().getElement().$).find('div[name="tablesDialog"]').height('100%');
					$(CKEDITOR.dialog.getCurrent().getElement().$).find('div[name="tablesDialog"] > table').css({'float' : 'left'});

					$('#' + editor.m_tablesHolderId).html('');
					var lSelection = editor.getSelection();
					var lElement = lSelection.getStartElement();
					if (lElement) {
						lElement = lElement.getAscendant('tbls-citation', true);
					}
					if (!lElement || lElement.getName() != 'tbls-citation' || lElement.data('cke-realelement')) {
						lElement = editor.document.createElement('tbls-citation');
						lElement.setAttribute('contentEditable', 'false');
						this.insertMode = true;
					} else {
						this.insertMode = false;
					}

					this.element = lElement;
					this.setupContent(this.element);

					this.tablesObject = new tablesCitation(editor.m_tablesHolderId);

					lTablesObject = this.tablesObject;

					$('#' + editor.m_tablesHolderId).find('.P-PopUp-Checkbox-Holder').each(function() {
						var lCheck = $(this).find(':checkbox');
						lCheck.change(function() {
							if ($(this).is(':checked')) {
								lTablesObject.addTable($(this).val());
							} else {
								lTablesObject.removeTable($(this).val());
							}
						});
					});

					if (!this.insertMode) { // Ако едитваме трябва да чекнем
											// всички фигури от цитацията
						pElement = CKEDITOR.dialog.getCurrent();
						if (pElement) {
							pElement = pElement.element;
							for ( var i = 0; i < pElement.getChildCount(); ++i) {
								var lChild = pElement.getChild(i);
								if (lChild.$.nodeType == 1 && lChild.$.nodeName.toLowerCase() == 'xref') {
									$('#' + editor.m_tablesHolderId).find(':checkbox').each(function() {
										if ($(this).val() == lChild.getAttribute('tid')) {
											$(this).attr('checked', 'checked');
										}
										if ($(this).is(':checked')) {
											lTablesObject.addTable($(this).val());
										} else {
											lTablesObject.removeTable($(this).val());
										}
									});
								}
							}
						}
					}
				},
				buttons : [ {
					id : 'addTables',
					type : 'button',
					label : 'Cite selected table(s)',
					className : 'P-Dialog-Button-InsertSelected',
					title : 'AddNewTable',
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
						CKEDITOR.dialog.getCurrent().commitContent(lCitation, CKEDITOR.dialog.getCurrent().tablesObject, CKEDITOR.dialog.getCurrent().insertMode);
						editor.fire('blur');
						CKEDITOR.dialog.getCurrent().hide();
						$('#' + editor.m_tablesHolderId).html('');
					}
				}, {
					id : 'addNewTable',
					type : 'button',
					label : 'Add New Table',
					className : 'P-Dialog-Button-AddNewFigure',
					title : 'AddNewTable',
					disabled : false,
					onClick : function() {
						gCurrentDialog = CKEDITOR.dialog.getCurrent();
						gCurrentDialog.hide();
						CreateNewTablePopup(1);	
						// popUp(POPUP_OPERS.open, 'add-table-popup',
						// 'add-table-popup');
					}
				}, {
					id : 'closeDialog',
					type : 'button',
					label : '',
					title : 'CloseDialog',
					disabled : false,
					className : 'P-Dialog-Button-Close',
					style : '',
					onClick : function() {
						CKEDITOR.dialog.getCurrent().hide();
						$('#' + editor.m_tablesHolderId).html('');
					}
				} ]
			};
		});
	}
});
