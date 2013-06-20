var gFigureCitationHolderTagName = 'fig-citation';
// Масива, в който са всички възможни plate темплейти
var gPlateTemplatesArr = {
	1 : 'a',
	2 : 'b',
	3 : 'c',
	4 : 'd',
	5 : 'e',
	6 : 'f'
};
var gFigureCitationType = 1;

figureCitation = function(pFiguresHolderId) {
	this.init(pFiguresHolderId);
};

figureCitation.prototype.init = function(pFiguresHolderId) {
	this.m_figuresHolderId = pFiguresHolderId;
	GetDocumentFigures(GetDocumentId(), this.m_figuresHolderId);
	this.m_selectedFigures = new Array();
}

figureCitation.prototype.addFigure = function(pFigureId) {
	if (this.m_selectedFigures.indexOf(pFigureId) == -1) {
		this.m_selectedFigures.push(pFigureId);
	}
};

figureCitation.prototype.removeFigure = function(pFigureId) {
	var lRefPos = this.m_selectedFigures.indexOf(pFigureId);
	if (lRefPos != -1) {
		this.m_selectedFigures.splice(lRefPos, 1);
	}
};

figureCitation.prototype.getSelectedFigures = function() {
	return this.m_selectedFigures;
};

CKEDITOR.plugins.add('figs', {
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
			lInstanceCitations = getInstanceFieldCitations(lInstanceId, lFieldId, gFigureCitationType);
		}

		editor.m_instanceId = lInstanceId;
		editor.m_fieldId = lFieldId;
		editor.m_instanceFigCitations = lInstanceCitations;
		editor.m_figuresHolderId = gFiguresHolderId + '_' + editor.m_instanceId + '_' + editor.m_fieldId;

		// Plugin logic goes here...
		var lIconPath = this.path + 'images/icon.png';
		editor.ui.addButton('Fig', {
			label : 'Cite figure',
			title : 'Insert smart figure citation.&#10;Do not type citations manually.',
			command : 'displayFigCitationDialog',
			icon : lIconPath
		});
		editor.addCommand('displayFigCitationDialog', new CKEDITOR.dialogCommand('figCitationDialog'));

		editor.addCommand('editFigCitation', {
			exec : function(editor) {
				alert(1);
			},
			modes : {
				wysiwyg : 1
			}
		});

		editor.addCommand('deleteFigCitation', {
			exec : function(editor) {
				var lSelection = editor.getSelection();
				var lElement = lSelection.getStartElement();
				if (lElement) {
					lElement = lElement.getAscendant('fig-citation', true);
				}

				if (lElement && lElement.getName() == gFigureCitationHolderTagName && !lElement.data('cke-realelement')) {
					var lCitationId = lElement.getAttribute('citation_id');
					if (lCitationId) {
						PerformRemoveCitation(lCitationId);
					}

					lElement.remove();
				}
			},
			modes : {
				wysiwyg : 1
			}
		});

		editor.addCommand('addFigCitation', {
			exec : function(editor) {
				var lSelection = editor.getSelection();
				var lElement = lSelection.getStartElement();
				if (lElement) {
					lElement = lElement.getAscendant('fig-citation', true);
				}

				if (lElement && lElement.getName() == 'fig-citation' && !lElement.data('cke-realelement')) {
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
				'fig-citation' : function(pElement) {
					while (pElement.children.length) {
						pElement.children.pop();
					}
				},
				'html' : function(element) {
					delete element.name;
				},
				'head' : function(element) {
					return false;
				},
				'body' : function(element) {
					delete element.name;
				}
			}
		});
		/**
		 * При показване на цитацията - трябва да заредим данните и от базата
		 */
		editor.dataProcessor.dataFilter.addRules({
			elements : {
				'fig-citation' : function(pElement) {

					while (pElement.children.length) {
						pElement.children.pop();
					}
					var lCitationId = pElement.attributes.citation_id;
					if (lCitationId && editor.m_instanceFigCitations[lCitationId]) {
						if (editor.m_instanceFigCitations[lCitationId]['preview']) {
							pElement.add(CKEDITOR.htmlParser.fragment.fromHtml(editor.m_instanceFigCitations[lCitationId]['preview']));
						}
					} else {// Няма такава цитация - може да е изтрита в базата
							// след изтриване на всички елементи в нея.
							// Изтриваме я
						return false;
					}
					// console.log('Load');
				},
				'html' : function(element) {
					delete element.name;
				},
				'head' : function(element) {
					return false;
				},
				'body' : function(element) {
					delete element.name;
				}
			}
		});

		if (editor.contextMenu) {
			editor.addMenuGroup('figCitationGroup');
			editor.addMenuItem('editFigCitationItem', {
				label : 'Edit figure citation',
				icon : lIconPath,
				command : 'displayFigCitationDialog',
				group : 'figCitationGroup'
			});
			editor.addMenuItem('deleteFigCitationItem', {
				label : 'Delete figure citation',
				icon : lIconPath,
				command : 'deleteFigCitation',
				group : 'figCitationGroup'
			});
			// Code creating context menu items goes here.
			editor.contextMenu.addListener(function(pElement) {
				if (pElement)
					pElement = pElement.getAscendant('fig-citation', true);
				if (pElement && !pElement.data('cke-realelement'))
					return {
						editFigCitationItem : CKEDITOR.TRISTATE_OFF,
						deleteFigCitationItem : CKEDITOR.TRISTATE_OFF
					};
				return null;
			});
		}

		CKEDITOR.dialog.add('figCitationDialog', function(editor) {
			return {
				title : 'Citation Properties',
				minWidth : 500,
				minHeight : 300,
				contents : [ {
					id : 'tab1',
					label : 'Settings',
					elements : [ {
						type : 'html',
						html : '<div class="' + gFiguresHolderId + '" id="' + editor.m_figuresHolderId + '"></div>',
						commit : function(pElement, figuresObject, pInsertMode) {

							var lSelectedFigures = figuresObject.getSelectedFigures();

							if (lSelectedFigures.length > 0) {

								pElement.setAttribute('contentEditable', 'false');
								pElement.addClass('P-Figure-Citation-Holder');

								pElement.setHtml('');

								var lCitationData = PerformCitationSave(editor.m_instanceId, editor.m_fieldId, pElement.getAttribute('citation_id'), gFigureCitationType, figuresObject.getSelectedFigures(), 0);

								pElement.setHtml(lCitationData['preview']);

								if (lCitationData !== false) {
									// Ако всичко е ок - добавяме цитацията в
									// текста
									editor.m_instanceFigCitations[lCitationData['citation_id']] = lCitationData;
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
					$('#' + editor.m_figuresHolderId).html('');
					var lSelection = editor.getSelection();
					var lElement = lSelection.getStartElement();
					if (lElement) {
						lElement = lElement.getAscendant('fig-citation', true);
					}
					if (!lElement || lElement.getName() != 'fig-citation' || lElement.data('cke-realelement')) {
						lElement = editor.document.createElement('fig-citation');
						lElement.setAttribute('contentEditable', 'false');
						this.insertMode = true;
					} else {
						this.insertMode = false;
					}
					this.element = lElement;
					this.setupContent(this.element);

					this.figuresObject = new figureCitation(editor.m_figuresHolderId);

					lFiguresObject = this.figuresObject;

					$('#' + editor.m_figuresHolderId).find('.P-PopUp-Checkbox-Holder, .P-Figure-InsertOnly').each(function() {
						var lCheck = $(this).find(':checkbox');
						lCheck.change(function() {
							if ($(this).is(':checked') && $(this).attr('figtype') != 1) {
								lFiguresObject.addFigure($(this).val());
							} else if ($(this).attr('figtype') != 1) {
								lFiguresObject.removeFigure($(this).val());
							}
						});
					});

					if (!this.insertMode) {
						pElement = CKEDITOR.dialog.getCurrent();
						if (pElement) {
							pElement = pElement.element;
							for ( var i = 0; i < pElement.getChildCount(); ++i) {
								var lChild = pElement.getChild(i);
								if (lChild.$.nodeType == 1 && lChild.$.nodeName.toLowerCase() == 'xref') {

									$('#' + editor.m_figuresHolderId).find(':checkbox').each(function() {
										if ($(this).val() == lChild.getAttribute('rid')) {
											$(this).attr('checked', 'checked');
										}
										if ($(this).is(':checked') && $(this).attr('figtype') != 1) {
											lFiguresObject.addFigure($(this).val());
										} else if ($(this).attr('figtype') != 1) {
											lFiguresObject.removeFigure($(this).val());
										}
									});
								}
							}
						}
						/*
						 * $('#' +
						 * editor.m_figuresHolderId).find('.P-PopUp-Checkbox-Holder,
						 * .P-Figure-InsertOnly').each(function() { var lCheck =
						 * $(this).find(':checkbox'); if (lCheck.is(':checked') &&
						 * lCheck.attr('figtype') != 1) {
						 * lFiguresObject.addFigure(lCheck.val()); } else
						 * if(lCheck.attr('figtype') != 1) {
						 * lFiguresObject.removeFigure(lCheck.val()); } });
						 */
					}

				},
				buttons : [ {
					id : 'addFigures',
					type : 'button',
					label : 'Cite selected figure(s)',
					className : 'P-Dialog-Button-InsertSelected',
					title : 'AddNewFigure',
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
						CKEDITOR.dialog.getCurrent().commitContent(lCitation, CKEDITOR.dialog.getCurrent().figuresObject, CKEDITOR.dialog.getCurrent().insertMode);
						CKEDITOR.dialog.getCurrent().hide();
						$('#' + editor.m_figuresHolderId).html('');
					}
				}, {
					id : 'addNewFigure',
					type : 'button',
					label : 'Add New Figure',
					className : 'P-Dialog-Button-AddNewFigure',
					title : 'AddNewFigure',
					disabled : false,
					onClick : function() {
						gCurrentDialog = CKEDITOR.dialog.getCurrent();
						gCurrentDialog.hide();
						ChangeFiguresForm('image', GetDocumentId(), 'P-PopUp-Content-Inner', 0, 2, 0, 0, 1);
						popUp(POPUP_OPERS.open, 'add-figure-popup', 'add-figure-popup');
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
						$('#' + editor.m_figuresHolderId).html('');
					}
				} ],

				onOk : function() {

				}
			};
		});
	}
});
