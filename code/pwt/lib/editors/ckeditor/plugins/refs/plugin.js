var gReferenceCitationHolderTagName = 'reference-citation';
var gReferenceModeHolderId = 'reference-mode';
var gReferenceDefaultPreviewMode = 1;
var gReferenceAllowedModes = [ 1, 2 ];
var gReferenceModeNames = {
	1 : 'Style 1 citation',
	2 : 'Style 2 citation'
};
var gReferenceCitationType = 3;

function getReferencePreview(pReferenceId, pMode) {
	return $('#Ref-Preview-' + pReferenceId + '-Mode-' + pMode)[0].textContent;
};

referenceCitationPreview = function(pReferencesHolderId, pPreviewHolderId, pModeHolderId) {
	this.init(pReferencesHolderId, pPreviewHolderId, pModeHolderId);
};

referenceCitationPreview.prototype.init = function(pReferencesHolderId, pPreviewHolderId, pModeHolderId) {
	// Първо зареждаме референциите
	this.m_referencesHolderId = pReferencesHolderId;
	this.m_previewHolderId = pPreviewHolderId;
	this.m_modeHolderId = pModeHolderId;
	GetDocumentReferences(GetDocumentId(), this.m_referencesHolderId);
	this.m_selectedReferences = new Array();
	this.setMode(gReferenceDefaultPreviewMode);
};

referenceCitationPreview.prototype.setMode = function(pMode) {
	// console.log(pMode, gReferenceAllowedModes,
	// gReferenceAllowedModes.indexOf(parseInt(pMode)));
	if (this.m_mode != pMode && gReferenceAllowedModes.indexOf(parseInt(pMode)) > -1) {
		this.m_mode = pMode;
		this.generateModeElement();
		this.generatePreview();
	}
};

referenceCitationPreview.prototype.generateModeElement = function() {
	var lHolder = $('#' + this.m_modeHolderId);
	lHolder.html('');
	lHolder.addClass('P-PopUp-Menu-Holder P-Reference-Mode-Menu-Holder');
	var lUl = $(document.createElement('ul'));
	var lFakeThis = this;
	for ( var i = 0; i < gReferenceAllowedModes.length; ++i) {
		var lCurrentMode = gReferenceAllowedModes[i];
		var lLi = $(document.createElement('li'));
		lLi.html('\
				<div class="P-PopUp-Menu-Elem-Left"></div>\
				<div class="P-PopUp-Menu-Elem-Middle">' + gReferenceModeNames[lCurrentMode] + '</div>\
				<div class="P-PopUp-Menu-Elem-Right"></div>');
		lLi.attr('mode_id', lCurrentMode);
		if (lCurrentMode != this.getMode()) {
			lLi.bind('click', function() {
				lFakeThis.setMode($(this).attr('mode_id'));
			});
		} else {
			lLi.addClass('P-Active');
		}
		lUl.append(lLi);
	}
	lHolder.append(lUl);
};

referenceCitationPreview.prototype.addReference = function(pReferenceId) {
	if (this.m_selectedReferences.indexOf(pReferenceId) == -1) {
		this.m_selectedReferences.push(pReferenceId);
		$('#' + this.m_referencesHolderId).find('.P-PopUp-Checkbox-Holder').find(':checkbox[value="' + pReferenceId + '"]').attr('checked', 'checked');

		this.generatePreview();
	}
};

referenceCitationPreview.prototype.removeReference = function(pReferenceId) {
	var lRefPos = this.m_selectedReferences.indexOf(pReferenceId);
	if (lRefPos != -1) {
		this.m_selectedReferences.splice(lRefPos, 1);
		$('#' + this.m_referencesHolderId).find('.P-PopUp-Checkbox-Holder').find(':checkbox[value="' + pReferenceId + '"]').attr('checked', false);
		this.generatePreview();
	}
};

referenceCitationPreview.prototype.generatePreview = function() {
	$('#' + this.m_previewHolderId).html('');

	var lList = document.createElement('ol');
	$(lList).attr('class', 'draglist');
	var lFakeThis = this;
	for ( var i = 0; i < this.m_selectedReferences.length; ++i) {
		var lReferenceId = this.m_selectedReferences[i];
		var lHolder = document.createElement('li');
		$(lHolder).attr('itemHolder', 1);
		$(lHolder).attr('refId', lReferenceId);
		$(lHolder).html(getReferencePreview(lReferenceId, this.m_mode));

		var lRemoveLink = document.createElement('a');
		$(lRemoveLink).attr('style', 'display:none');
		$(lRemoveLink).attr('class', 'citationRemoveLink');
		$(lRemoveLink).attr('ref-id', lReferenceId);
		$(lRemoveLink).html('<img src="/i/reference_citation_remove.jpg" />');
		$(lRemoveLink).bind('click', function() {
			lFakeThis.removeReference($(this).attr('ref-id'));
		});
		$(lHolder).append(lRemoveLink);

		$(lHolder).hover(function(pEvent) {
			$(this).find('a').show();
		}, function() {
			$(this).find('a').hide();
		});
		$(lList).append(lHolder);
	}


	$(lList).dragsort({
		placeHolderTemplate : "<li class='placeHolder'>drop&nbsp;here</li>",
		dragEnd : function(pEvent, pUi) {
			var lItems = $('#' + lFakeThis.m_previewHolderId).find('*[itemHolder="1"]');
			var lNewOrder = new Array();
			for ( var i = 0; i < lItems.length; ++i) {
				lNewOrder.push($(lItems[i]).attr('refId'));
			}
			// console.log(lNewOrder, lFakeThis.m_selectedReferences);
			lFakeThis.m_selectedReferences = lNewOrder;

		}
	});

	$('#' + this.m_previewHolderId).append(lList);
};

referenceCitationPreview.prototype.checkIfReferenceIsSelected = function(pReferenceId) {
	if (this.m_selectedReferences.indexOf(pReferenceId) == -1) {
		return false;
	}
	return true;
};

referenceCitationPreview.prototype.getSelectedReferences = function() {
	return this.m_selectedReferences;
};

referenceCitationPreview.prototype.getMode = function() {
	return this.m_mode;
};

CKEDITOR.plugins.add('refs', {
	init : function(editor) {
		// Plugin logic goes here...
		var lIconPath = this.path + 'images/icon.png';
		var lEditorName = editor.name;

		var lRegMatch = RegExp("(\\d+)" + gInstanceFieldNameSeparator + "(\\d+)");
		var lMatch = lEditorName.match(lRegMatch);

		var lInstanceCitations = new Array();
		var lInstanceId = 0;
		var lFieldId = 0;
		if (lMatch && lMatch.length > 0) {
			lInstanceId = lMatch[1];
			lFieldId = lMatch[2];
			lInstanceCitations = getInstanceFieldCitations(lInstanceId, lFieldId, gReferenceCitationType);
		}

		editor.m_instanceId = lInstanceId;
		editor.m_fieldId = lFieldId;
		editor.m_instanceRefCitations = lInstanceCitations;
		editor.m_referencesHolderId = gReferencesHolderId + '_' + editor.m_instanceId + '_' + editor.m_fieldId;
		editor.m_referencesPreviewHolderId = gReferencesCitationPreviewId  + '_' + editor.m_instanceId + '_' + editor.m_fieldId;
		editor.m_referenceModeHolderId = gReferenceModeHolderId  + '_' + editor.m_instanceId + '_' + editor.m_fieldId;
		editor.ui.addButton('Refs', {
			label : 'Cite reference',
			title : 'Insert smart reference citation.&#10;Do not type citations manually.',
			command : 'displayRefsCitationDialog',
			icon : lIconPath
		});

//		editor.on('getData', function() {
//			console.log('Before GetData');
//		});

		editor.addCommand('displayRefsCitationDialog', new CKEDITOR.dialogCommand('ReferenceCitationDialog'));

		editor.addCommand('deleteReferenceCitation', {
			exec : function(editor) {
				var lSelection = editor.getSelection();
				var lElement = lSelection.getStartElement();
				if (lElement) {
					lElement = lElement.getAscendant(gReferenceCitationHolderTagName, true);
				}

				if (lElement && lElement.getName() == gReferenceCitationHolderTagName && !lElement.data('cke-realelement')) {
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

		editor.addCommand('addReferenceCitation', {
			exec : function(editor) {
				var lSelection = editor.getSelection();
				var lElement = lSelection.getStartElement();
				if (lElement) {
					lElement = lElement.getAscendant(gReferenceCitationHolderTagName, true);
				}

				if (lElement && lElement.getName() == gReferenceCitationHolderTagName && !lElement.data('cke-realelement')) {
					lElement.remove();
				}
			},
			modes : {
				wysiwyg : 1
			}
		});

		/**
		 * При показване на цитацията - трябва да заредим данните и от базата
		 */
		editor.dataProcessor.dataFilter.addRules({
			elements : {
				'reference-citation' : function(pElement) {

					while (pElement.children.length) {
						pElement.children.pop();
					}
					var lCitationId = pElement.attributes.citation_id;
					if (lCitationId && editor.m_instanceRefCitations[lCitationId]) {
						if(editor.m_instanceRefCitations[lCitationId]['preview']){
							pElement.add(CKEDITOR.htmlParser.fragment.fromHtml(editor.m_instanceRefCitations[lCitationId]['preview']));
						}
					}else{//Няма такава цитация - може да е изтрита в базата след изтриване на всички елементи в нея. Изтриваме я
						return false;
					}
//					console.log('Load');
				}
			}
		});

		/**
		 * При запазване на полето трябва да оставим само възела
		 */
		editor.dataProcessor.htmlFilter.addRules({
			elements : {
				'reference-citation' : function(pElement) {
					 while(pElement.children.length){
						 pElement.children.pop();
					 }
//					console.log('UnLoad');
				}
			}
		});

		editor.addMenuGroup('ReferenceCitationGroup');
		editor.addMenuItem('deleteReferenceCitationItem', {
			label : 'Delete reference citation',
			icon : lIconPath,
			command : 'deleteReferenceCitation',
			group : 'ReferenceCitationGroup'
		});
		editor.addMenuItem('editReferenceCitationItem', {
			label : 'Edit reference citation',
			icon : lIconPath,
			command : 'displayRefsCitationDialog',
			group : 'ReferenceCitationGroup'
		});
		// Code creating context menu items goes here.
		editor.contextMenu.addListener(function(pElement) {
			if (pElement)
				pElement = pElement.getAscendant(gReferenceCitationHolderTagName, true);
			if (pElement && !pElement.data('cke-realelement'))
				return {
					editReferenceCitationItem : CKEDITOR.TRISTATE_OFF,
					deleteReferenceCitationItem : CKEDITOR.TRISTATE_OFF
				};
			return null;
		});

		CKEDITOR.dialog.add('ReferenceCitationDialog', function(editor) {
			return {
				title : 'Citation Properties',
				minWidth : 400,
				minHeight : 200,
				// width : $(window).width() - 200,
				// height : $(window).height() - 200,
				contents : [ {
					id : 'tab1',
					label : 'Settings',
					height : '100%',
					elements : [ {
						type : 'html',
						id : editor.m_referencesHolderId,
						html : '<div class="' + gReferencesHolderId + '" id="' + editor.m_referencesHolderId +'"></div>',
						commit : function(pElement, pPreviewObject, pInsertMode, pEditorTextareaName) {
							var lSelectedReferences = pPreviewObject.getSelectedReferences();

							if (pPreviewObject && lSelectedReferences.length > 0) {

								pElement.setAttribute('contentEditable', 'false');
								pElement.addClass('P-References-Citation-Holder');

								pElement.setHtml('');

								// Запазваме цитацията в базата.
								var lCitationData = PerformCitationSave(editor.m_instanceId, editor.m_fieldId, pElement.getAttribute('citation_id'), gReferenceCitationType, lSelectedReferences, pPreviewObject.getMode());
								if(lCitationData !== false){
									//Ако всичко е ок - добавяме цитацията в текста
									editor.m_instanceRefCitations[lCitationData['citation_id']] = lCitationData;
									pElement.setAttribute('citation_id', lCitationData['citation_id']);
									pElement.setHtml(lCitationData['preview']);
									var lDialog = CKEDITOR.dialog.getCurrent();
									var lEditor = lDialog.getParentEditor();
									lEditor.insertElement(pElement);

									//Ако сме вкарали нова цитация - трябва тук да направим ръчно авто сейв-а след като сме попълнили id-то на цитацията
									if(pInsertMode){
										autoSaveInstance();
									}
								}else{
									//Ако има проблем - трием елемента
									pElement.$.parentNode.removeChild(pElement.$);
								}
							}else{
								pElement.$.parentNode.removeChild(pElement.$);
							}
						}
					}, {
						type : 'html',
						id : editor.m_referencesPreviewHolderId,
						html : '<div id="' + editor.m_referenceModeHolderId + '"></div><div class="Editor-Item-Title">Citation Preview (Drag to reorder)</div><div id="' + editor.m_referencesPreviewHolderId + '"></div>'
					}

					]
				} ],
				onShow : function() {
					CKEDITOR.dialog.getCurrent().resize($(window).width() - 200, $(window).height() - 200);
					CKEDITOR.dialog.getCurrent().move(75, 40);
					$(CKEDITOR.dialog.getCurrent().getElement().$).find('*[name="tab1"] table').height('100%');
					$(CKEDITOR.dialog.getCurrent().getElement().$).find('*[name="tab1"] table td[role="presentation"]').css('vertical-align', 'top');
					$('#' + gReferencesCitationPreviewId + '_' + editor.m_instanceId + '_' + editor.m_fieldId).parent().height(20);

					var lSelection = editor.getSelection();
					var lElement = lSelection.getStartElement();
					if (lElement) {
						lElement = lElement.getAscendant(gReferenceCitationHolderTagName, true);
					}
					if (!lElement || lElement.getName() != gReferenceCitationHolderTagName || lElement.data('cke-realelement')) {
						lElement = editor.document.createElement(gReferenceCitationHolderTagName);
						lElement.setAttribute('contentEditable', 'false');
						this.insertMode = true;
					} else {
						this.insertMode = false;
					}

//					console.log('Insert Mode1 ' + this.insertMode);
					this.element = lElement;
					this.setupContent(this.element);
					this.previewObject = new referenceCitationPreview(editor.m_referencesHolderId, editor.m_referencesPreviewHolderId, editor.m_referenceModeHolderId);
					var lPreviewObject = this.previewObject;

					// Зареждаме избраните
					// референции в правилния
					// ред
					if (!this.insertMode) {
						var lCitationId = $(lElement.$).attr('citation_id');
						if(lCitationId && editor.m_instanceRefCitations[lCitationId]){
							if (editor.m_instanceRefCitations[lCitationId]['citation_mode']) {
								lPreviewObject.setMode(editor.m_instanceRefCitations[lCitationId]['citation_mode']);
							}
							var lCitationObjects = editor.m_instanceRefCitations[lCitationId]['citation_objects'];
							if (lCitationObjects && lCitationObjects.length) {
								for(var i = 0; i < lCitationObjects.length; ++i){
									lPreviewObject.addReference(lCitationObjects[i]);
								}
							}
						}

					}
					lPreviewObject.generatePreview();

					$('#' + editor.m_referencesHolderId).find('.P-PopUp-Checkbox-Holder').each(function() {
						var lCheck = $(this).find(':checkbox');
						lCheck.bind('click', function() {
							if ($(this).is(':checked')) {
								lPreviewObject.addReference($(this).val());
							} else {
								lPreviewObject.removeReference($(this).val());
							}
						});
					});
				
					var lCittHeight = $(CKEDITOR.dialog.getCurrent().getElement().$).find('div[name="tab1"]').height() - 120;
					$(CKEDITOR.dialog.getCurrent().getElement().$).find('.' + gReferencesHolderId).height(lCittHeight);
					
				},
				buttons : [ {
					id : 'addReferences',
					type : 'button',
					label : 'Cite selected reference(s)',
					className : 'P-Dialog-Button-Insert-Selected-References',
					title : 'AddSelectedReferences',
					disabled : false,
					onClick : function() {
						lCurrentElement = CKEDITOR.dialog.getCurrent().element;
						if (CKEDITOR.dialog.getCurrent().insertMode) {
							var lSelection = editor.getSelection();
							var lRanges = lSelection.getRanges(true);

							if (lRanges.length > 0) {
								lRanges[lRanges.length - 1].collapse();
								lSelection.selectRanges([ lRanges[lRanges.length - 1] ]);
							}
							editor.insertElement(lCurrentElement);
						}

						CKEDITOR.dialog.getCurrent().commitContent(lCurrentElement, CKEDITOR.dialog.getCurrent().previewObject, CKEDITOR.dialog.getCurrent().insertMode, CKEDITOR.dialog.getCurrent().getParentEditor().name);
						CKEDITOR.dialog.getCurrent().hide();
						$('#' + editor.m_referencesHolderId).html('');
					}
				},
				{
					id : 'addNewReference',
					type : 'button',
					label : 'Add New Reference',
					className : 'P-Dialog-Button-AddNewReference',
					title : 'AddNewReference',
					disabled : false,
					onClick : function() {
						gCurrentDialog = CKEDITOR.dialog.getCurrent();
						gCurrentDialog.hide();
						CreateNewReferencePopup(1);
					}
				},
					 {
					id : 'closeDialog',
					type : 'button',
					label : '',
					title : 'CloseDialog',
					disabled : false,
					className : 'P-Dialog-Button-Close',
					style : '',
					onClick : function() {
						CKEDITOR.dialog.getCurrent().hide();
						$('#' + editor.m_referencesHolderId).html('');
					}
				} ]
			};
		});
	}
});
