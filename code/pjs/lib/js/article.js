var gArticleAjaxSrvUrl = gAjaxUrlsPrefix + 'article_ajax_srv.php'
var gArticleId = 0;
var gActiveMenuClass = 'P-Active-Menu';
var gArticlePreviewIframeId = 'articleIframe';
var gMapHolderId = 'localitiesMap';
var gBaloonId = 'ArticleBaloon';
var gArticleMap = false;
var gContentsMenuElementType = 1;
var gLocalitiesMenuElementType = 6;
var gReferencesMenuElementType = 4;
var gMenuActiveElementType = false;
var gTaxonParsedNameAttributeName = 'data-taxon-parsed-name';
var gTaxonParsedNameAttributePrefix = 'data-taxon-parsed-name-';
var gTaxonNameHolderNamesCountAttributeName = 'data-taxon-names-count';
var gCitationElementInstanceIdAttributeName = 'rid';

var gLocalitiesList = {};
var gActiveLocalityIds = [];
var gLocalityByCoordinatesArr = {};
var gLocalityByInstanceIdArr = {};
var gLocalitySelectAllInputValue = -2;
var gLocalitySelectAllInstancesInputValue = -1;

var gCurrentTaxonOccurrenceNavigationTaxonNamesNode = false;
var gCurrentTaxonOccurrenceNavigationTaxonNode = false;
var gCurrentCitatedElementNavigationNode = false;
var gHighlightedElementClass = 'P-Highlighted-Element';

var gTaxonDataUsageTypeTreatment = 1;
var gTaxonDataUsageTypeChecklist = 2;
var gTaxonDataUsageTypeIdKey = 3;
var gTaxonDataUsageTypeFigure = 4;
var gTaxonDataUsageTypeInline = 5;


Locality = function(pId, pLongitude, pLatitude, pInstanceIds){
	this.latitude = pLatitude;
	this.longitude = pLongitude;
	this.instanceIds = pInstanceIds;
	this.id = pId;
	this.marker = new google.maps.Marker({
      position: new google.maps.LatLng(pLatitude,pLongitude),
      map: null,
      title: pLatitude + ', ' + pLongitude
  });
};

Locality.prototype.showMarker = function(){
	this.marker.setMap(gArticleMap);
};

Locality.prototype.hideMarker = function(){
	this.marker.setMap(null);
};

function SetArticleId(pArticleId){
	gArticleId = pArticleId;
}

function initArticlePreviewOnLoadEvents(){
	resizePreviewIframe(gArticlePreviewIframeId);
	InitContentsCustomElementsEvents(1);
	LoadArticleLocalities();
	LoadArticleReferences();
	InitClearHighlightedElementsEvents();	
}

function InitArticleMenuEvents(){
	$('.P-Info-Menu li').each(function(pIdx, pElement){
		var lElementType = $(this).attr('data-info-type');
		$(this).bind('click', function(){
			LoadArticleMenuMainElement(lElementType);
		});
	});
}

function LoadArticleMenuMainElement(pElementType){
	$.ajax({
		url : gArticleAjaxSrvUrl,
		async : false,
		data : {
			action : 'get_main_list_element',
			element_type : pElementType,
			article_id : gArticleId
		},
		success : function(pAjaxResult) {
			if(pAjaxResult['err_cnt']){
				alert(pAjaxResult['err_msg']);
				return;
			}
			ClearActiveLocalities();
			LoadInfoContent(pAjaxResult['html'], pElementType);
		}
	});
}

function MarkActiveMenuElement(){
	$('.P-Info-Menu li.' + gActiveMenuClass).removeClass(gActiveMenuClass);
	$('.P-Info-Menu li[data-info-type="' + gMenuActiveElementType + '"]').addClass(gActiveMenuClass);
}

function LoadInfoContent(pContent, pActiveMenuType){
	gMenuActiveElementType = pActiveMenuType;
	$('.P-Info-Content').html(pContent);	
	MarkActiveMenuElement();
	InitContentsCustomElementsEvents();
}

function LoadArticleLocalities(){
	$.ajax({
		url : gArticleAjaxSrvUrl,
		data : {
			action : 'get_article_localities',
			article_id : gArticleId
		},
		success : function(pAjaxResult) {
			if(pAjaxResult['err_cnt']){
				alert(pAjaxResult['err_msg']);
				return;
			}
			for(var lLocalityId in pAjaxResult['localities']){
				lLocalityId = parseInt(lLocalityId);
				var lLocalityData = pAjaxResult['localities'][lLocalityId];
				var lLocalityLongitude = parseFloat(lLocalityData['longitude']);
				var lLocalityLatitude = parseFloat(lLocalityData['latitude']);
				var lLocalityInstanceIds = lLocalityData['instance_ids'];

				var lLocality = new Locality(lLocalityId, lLocalityLongitude, lLocalityLatitude, lLocalityInstanceIds);
				gLocalitiesList[lLocalityId] = lLocality;
				if(!gLocalityByCoordinatesArr[lLocalityLatitude]){
					gLocalityByCoordinatesArr[lLocalityLatitude] = {};
				}
				if(!gLocalityByCoordinatesArr[lLocalityLatitude][lLocalityLongitude]){
					gLocalityByCoordinatesArr[lLocalityLatitude][lLocalityLongitude] = lLocalityId;
				}
				for(var i = 0; i < lLocalityInstanceIds.length; ++i){
					var lInstanceId = parseInt(lLocalityInstanceIds[i]);
					if(!gLocalityByInstanceIdArr[lInstanceId]){
						gLocalityByInstanceIdArr[lInstanceId] = [];
					}
					gLocalityByInstanceIdArr[lInstanceId].push(lLocalityId);
				}
			}

		}
	});
}

function LoadArticleReferences(){
	$.ajax({
		url : gArticleAjaxSrvUrl,
		async : false,
		data : {
			action : 'get_main_list_element',
			element_type : gReferencesMenuElementType,
			article_id : gArticleId
		},
		success : function(pAjaxResult) {
			if(pAjaxResult['err_cnt']){
				return;
			}
			$('.P-Article-References-For-Baloon').html(pAjaxResult['html']);
		}
	});
	GetArticlePreviewContent().find('xref.bibr[rid]').each(function(pIdx, pReferenceNode){
		var lReferenceId = $(pReferenceNode).attr('rid');
		var lBaloon = $('#' + gBaloonId);
		var lReferenceContent = $('.P-Article-References-For-Baloon').find('.bibr[rid="' + lReferenceId + '"]');
		if(!lReferenceContent.length){
			return;
		}
		$(pReferenceNode).hover(function(pEvent){
			lBaloon.html(lReferenceContent.html());
			var lReferenceOffsetTop = $(pReferenceNode).offset().top + $(pReferenceNode).outerHeight();
			var lReferenceOffsetLeft = $(pReferenceNode).offset().left;
			lBaloon.css('top', lReferenceOffsetTop);
			lBaloon.css('left', lReferenceOffsetLeft);
			lBaloon.show();
		},
		function(pEvent){
			lBaloon.hide();
		}
		);
	});
}

function LoadElementInfo(pActionName, pElementId, pElementName){
	$.ajax({
		url : gArticleAjaxSrvUrl,
		data : {
			action : pActionName,
			article_id : gArticleId,
			element_id : pElementId,
			element_name : pElementName
		},
		success : function(pAjaxResult) {
			if(pAjaxResult['err_cnt']){
				alert(pAjaxResult['err_msg']);
				return;
			}
			LoadInfoContent(pAjaxResult['html'], pAjaxResult['element_type']);
		}
	});
}

function LoadFigureInfo(pElementId){
	LoadElementInfo('get_figure_element', pElementId);
//	console.log('Fig ' + pElementId);
	//LoadElementInfo('get_figure_element', 1);
}

function LoadTableInfo(pElementId){
	LoadElementInfo('get_table_element', pElementId);
//	console.log('Table ' + pElementId);
	//LoadElementInfo('get_table_element', 5);
}

function LoadSupFileInfo(pElementId){
	LoadElementInfo('get_sup_file_element', pElementId);
//	console.log('Sup file ' + pElementId);
	//LoadElementInfo('get_sup_file_element', 4);
}

function LoadReferenceInfo(pElementId){
	LoadElementInfo('get_reference_element', pElementId);
//	console.log('Reference ' + pElementId);
	//LoadElementInfo('get_reference_element', 9);
}

function LoadTaxonInfo(pTaxonName){
	pTaxonName = PrepareTaxonName(pTaxonName);
//	LoadElementInfo('get_taxon_element', '', pTaxonName);
//	console.log('Taxon ' + pTaxonName);
	LoadElementInfo('get_taxon_element', '', pTaxonName);
}

function LoadAuthorInfo(pElementId){
	LoadElementInfo('get_author_element', pElementId);
//	console.log('Author ' + pElementId);
//	LoadElementInfo('get_author_element', 4);
}

function ScrollInfoBarToTop(){
	$('.P-Article-Info-Bar').scrollTop(0);
}

function InitContentsCustomElementsEvents(pInPreviewIframe){
	PlaceTaxonNameEvents(pInPreviewIframe);
	PlaceFigureEvents(pInPreviewIframe);
	PlaceTableEvents(pInPreviewIframe);
	PlaceReferencesEvents(pInPreviewIframe);
	PlaceSupFilesEvents(pInPreviewIframe);
	PlaceLocalitiesEvents(pInPreviewIframe);
	PlaceAuthorEvents(pInPreviewIframe);
	PlaceTaxonUsageIconsEvents(pInPreviewIframe);
	PlaceTaxonNavigationLinkEvents(pInPreviewIframe);
	PlaceCitatedElementsNavigationEvents(pInPreviewIframe);
	ResetTaxonOccurrenceNavigation();
	ResetCitatedElementNavigation();
	ScrollInfoBarToTop();
}

function InitClearHighlightedElementsEvents(){
	var lClearActiveElementsHighlight = function(){
		RemoveCurrentTaxonNavigationNodeHighlight();
		RemoveCurrentCitatedElementNavigationNodeHighlight();
	};
	GetArticlePreviewContent().bind('mouseup', function(pEvent){
		lClearActiveElementsHighlight();
	});
	GetArticlePreviewContent().bind('keyup', function(pEvent) {
		lClearActiveElementsHighlight();
	});
	GetArticlePreviewContent().bind('selectionchange', function(pEvent) {
		lClearActiveElementsHighlight();
	});

	$(document).bind('mouseup', function(pEvent){
		lClearActiveElementsHighlight();
	});
	$(document).bind('keyup', function(pEvent) {
		lClearActiveElementsHighlight();
	});
	$(document).bind('selectionchange', function(pEvent) {
		lClearActiveElementsHighlight();
	});
}

function SetArticleOnLoadEvents(){
	$('#' + gArticlePreviewIframeId).load(function(){initArticlePreviewOnLoadEvents();});
//	$(window).resize(function(){SetLocalitiesHolderHeight();});
}

function GetArticlePreviewContent(){
	return $('#' + gArticlePreviewIframeId).contents();
}


function GetCustomElementsContents(pInPreviewIframe){
	if(pInPreviewIframe){
		return GetArticlePreviewContent();
	}
	return $('.P-Article-Info-Bar');
}


function PlaceTaxonNameEvents(pInPreviewIframe){
	var lPartsThatLeadToSelf = [
		'kingdom',
		'regnum',
		'subkingdom',
		'subregnum',
		'division',
		'phylum',
		'subdivision',
		'subphylum',
		'superclass',
		'superclassis',
		'class',
		'classis',
		'subclass',
		'subclassis',
		'superorder',
		'superordo',
		'order',
		'ordo',
		'suborder',
		'subordo',
		'infraorder',
		'infraordo',
		'superfamily',
		'superfamilia',
		'family',
		'familia',
		'subfamily',
		'subfamilia',
		'tribe',
		'tribus',
		'subtribe',
		'subtribus',
		'genus',
		'subgenus',
		'above-genus'
	];
	var lPartsThatDontLeadToSelf = {
		'species' : ['genus', 'species'],
		'subspecies' : ['genus', 'species', 'subspecies'],
		'variety' : ['genus', 'species', 'variety'],
		'varietas' : ['genus', 'species', 'varietas'],
		'form' : ['genus', 'species', 'form'],
		'forma' : ['genus', 'species', 'forma']
	};
	var lPartsThatLeadToSelfSelector = '.' + lPartsThatLeadToSelf.join(',.');
	var lAttributeThatHoldsPartFullName = 'full-name';
	GetCustomElementsContents(pInPreviewIframe).find('.tn').each(function(pIdx, pTaxonNode){
		$(pTaxonNode).find(lPartsThatLeadToSelfSelector).each(function(pIdx1, pTaxonNamePartNode){
			$(pTaxonNamePartNode).bind('click', function(pEvent){
				pEvent.stopPropagation();
				var lTaxonName = $(pTaxonNamePartNode).attr(lAttributeThatHoldsPartFullName);
				if(typeof lTaxonName === 'undefined' || lTaxonName == ''){
					lTaxonName = $(pTaxonNamePartNode).text();
				}
				LoadTaxonInfo(lTaxonName);
			});
		});
		for(var lPartName in lPartsThatDontLeadToSelf){
			var lParts = $(pTaxonNode).find('.' + lPartName);
			var lNamePartsOrder = lPartsThatDontLeadToSelf[lPartName];
			if(lParts.length > 0){
				var lTaxonName = '';
				for(var i = 0; i < lNamePartsOrder.length; ++i){
					var lCurrentPart = lNamePartsOrder[i];
					var lCurrentPartText = $(pTaxonNode).find('.' + lCurrentPart).attr(lAttributeThatHoldsPartFullName);
					if(typeof lCurrentPartText === 'undefined' || lCurrentPartText == ''){
						lCurrentPartText = $(pTaxonNode).find('.' + lCurrentPart).text();
					}
					if(lTaxonName != ''){
						lTaxonName += ' ';
					}
					lTaxonName += lCurrentPartText;
				}
				lParts.each(function(pIdx1, pTaxonNamePartNode){
					$(pTaxonNamePartNode).bind('click', function(pEvent){
						pEvent.stopPropagation();
						LoadTaxonInfo(lTaxonName);
					});
				});
			}
		}
	});
}

/**
 * Strips and removes multiple whitespaces from the taxon name
 * @param pTaxonName
 */
function PrepareTaxonName(pTaxonName){
	pTaxonName = $.trim(pTaxonName);
	pTaxonName = pTaxonName.replace(/\s+/, ' ');
	return pTaxonName;
}

function PlaceFigureEvents(pInPreviewIframe){
	GetCustomElementsContents(pInPreviewIframe).find('.fig[rid]').each(function(pIdx, pFigureNode){
		$(pFigureNode).bind('click', function(pEvent){
			pEvent.stopPropagation();
			LoadFigureInfo($(pFigureNode).attr('rid'));
		});
	});
}

function PlaceTableEvents(pInPreviewIframe){
	GetCustomElementsContents(pInPreviewIframe).find('.table[rid]').each(function(pIdx, pTableNode){
		$(pTableNode).bind('click', function(pEvent){
			pEvent.stopPropagation();
			LoadTableInfo($(pTableNode).attr('rid'));
		});
	});
}

function PlaceSupFilesEvents(pInPreviewIframe){
	GetCustomElementsContents(pInPreviewIframe).find('.suppl[rid]').each(function(pIdx, pSupFileNode){
		$(pSupFileNode).bind('click', function(pEvent){
			pEvent.stopPropagation();
			LoadSupFileInfo($(pSupFileNode).attr('rid'));
		});
	});
}

function PlaceReferencesEvents(pInPreviewIframe){
	GetCustomElementsContents(pInPreviewIframe).find('.bibr[rid]').each(function(pIdx, pReferenceNode){
		$(pReferenceNode).bind('click', function(pEvent){
			pEvent.stopPropagation();
			LoadReferenceInfo($(pReferenceNode).attr('rid'));
		});
	});
}

function PlaceAuthorEvents(pInPreviewIframe){
	GetCustomElementsContents(pInPreviewIframe).find('*[data-author-id]').each(function(pIdx, pAuthorNode){
		$(pAuthorNode).bind('click', function(pEvent){
			pEvent.stopPropagation();
			LoadAuthorInfo($(pAuthorNode).attr('data-author-id'));
		});
	});
}


function PlaceLocalitiesEvents(pInPreviewIframe){
	GetCustomElementsContents(pInPreviewIframe).find('*[data-is-locality-coordinate]').each(function(pIdx, pLocalityNode){
		$(pLocalityNode).bind('click', function(pEvent){
			pEvent.stopPropagation();
			ShowSingleCoordinate($(pLocalityNode).attr('data-latitude'), $(pLocalityNode).attr('data-longitude'));
		});
	});
}

function PlaceTaxonUsageIconsEvents(pInPreviewIframe){
	var lTreatmentTitleSelector = '*[data-taxon-treatment-title]';
	var lChecklistTitleSelector = '*[data-checklist-taxon-title]';
	var lIDKeySelector = '*[data-id-key-taxon-name]';
	var lFigureSelector = '.figure';	
	var lUsageSelects = {};
	lUsageSelects[gTaxonDataUsageTypeTreatment] = lTreatmentTitleSelector;
	lUsageSelects[gTaxonDataUsageTypeChecklist] = lChecklistTitleSelector;
	lUsageSelects[gTaxonDataUsageTypeIdKey] = lIDKeySelector;
	lUsageSelects[gTaxonDataUsageTypeFigure] = lFigureSelector;
	lUsageSelects[gTaxonDataUsageTypeInline] = '';	
	GetCustomElementsContents(pInPreviewIframe).find('.taxon-usage').each(function(pIdx, pNode){		
		var lTaxonNameHolder = $(pNode).closest('*[' + gTaxonNameHolderNamesCountAttributeName + ']');
		var lTaxonNamesCount = lTaxonNameHolder.attr(gTaxonNameHolderNamesCountAttributeName);
		var lTaxonNames = [];
		var gTaxonNodeSelector = '';
		for(var i = 0; i < lTaxonNamesCount; ++i){
			var lName = lTaxonNameHolder.attr(gTaxonParsedNameAttributePrefix + i);
			if(lName != ''){
				lTaxonNames.push(lName);
				if(gTaxonNodeSelector != ''){
					gTaxonNodeSelector +=',';
				}
				gTaxonNodeSelector += '*[' + gTaxonParsedNameAttributeName + '="' + lName + '"]';
			}
		}


		var lUsageType = parseInt($(pNode).attr('data-usage-type'));
		var lNodeResult = false;
		var lItemsToSearchIn = false;
		var lSearchHasBeenPerformed = false;
		var lSelector = '';



		if(gTaxonNodeSelector != '' ){
			$(pNode).bind('click', function(pEvent){
				pEvent.stopPropagation();
				lSelector = lUsageSelects[lUsageType];
				if(lUsageType == gTaxonDataUsageTypeInline){
					lItemsToSearchIn = GetArticlePreviewContent();
					//For inline usage we have to check that the taxon node is not in any of the non-inline taxon holders
					var lOccurrences = lItemsToSearchIn.find(gTaxonNodeSelector).addBack(gTaxonNodeSelector);
					var lCombinedSelectors = lTreatmentTitleSelector + ',' + lChecklistTitleSelector + ',' + lIDKeySelector + ',' + lFigureSelector;
					lOccurrences.each(function(){
						if($(this).parents(lCombinedSelectors).length == 0){
							//The node is not in any of the non-inline taxon holders - we dont have to search any more
							lNodeResult = this;
							return false;
						}
					});
					lSearchHasBeenPerformed = true;
				}
	
				if(!lSearchHasBeenPerformed){
					if(lUsageType == gTaxonDataUsageTypeTreatment){
						console.log(1);
					}
					if(lUsageType == gTaxonDataUsageTypeChecklist){
						console.log(2);
					}
					lItemsToSearchIn = GetArticlePreviewContent().find(lSelector);
					var lOccurrences = lItemsToSearchIn.find(gTaxonNodeSelector).addBack(gTaxonNodeSelector);
					if(lOccurrences.length){
						lNodeResult = lOccurrences.first()[0];
					}
				}
	
				if(lNodeResult != false){										
						SetCurrentTaxonNavigationNode(lNodeResult, lTaxonNameHolder[0]);				
				}
			});
		}
	});
}

function ScrollArticleToInstance(pInstanceId){
	var lFirstInstanceElement = GetArticlePreviewContent().find('*[instance_id=' + pInstanceId + ']').first();
	ScrollArticleToNode(lFirstInstanceElement[0], 10);
}

function ScrollArticleToNode(pNode, pOffset){
	if(!pNode){
		return;
	}
	var lTopOffset = $(pNode).offset().top;
	$('#article-preview').scrollTop(lTopOffset - pOffset);
}

function PlaceTaxonNavigationLinkEvents(pInPreviewIframe){
	GetCustomElementsContents(pInPreviewIframe).find('.P-Taxon-Navigation-Link-Prev,.P-Taxon-Navigation-Link-Next').each(function(pIdx, pNode){
		var lTaxonNamesNode= $(pNode).closest('*[' + gTaxonNameHolderNamesCountAttributeName + ']');
		if(lTaxonNamesNode.length){
			$(pNode).bind('click', function(pEvent){
				pEvent.stopPropagation();
				NavigateToPrevNextTaxonOccurrence(lTaxonNamesNode[0], $(pNode).hasClass('P-Taxon-Navigation-Link-Prev'));
			});
		}
	});
}

function NavigateToPrevNextTaxonOccurrence(pTaxonNamesNode, pPrevious){
	//A taxon names node may contain many names because we group the similar together.
	//When we look for a taxon - we look for a taxon with any of the names in the taxon names node
	//That is why we keep a reference to the node with names as well to the current taxon name
	//The attribute 'data-taxon-names-count' of this node contains the number of names which are present in the node
	if(gCurrentTaxonOccurrenceNavigationTaxonNode != false && gCurrentTaxonOccurrenceNavigationTaxonNamesNode != pTaxonNamesNode){
		ResetTaxonOccurrenceNavigation();
	}

	var lTaxonNamesCount = $(pTaxonNamesNode).attr(gTaxonNameHolderNamesCountAttributeName);
	var lTaxonNames = [];
	var gTaxonNodeSelector = '';
	for(var i = 0; i < lTaxonNamesCount; ++i){
		var lName = $(pTaxonNamesNode).attr(gTaxonParsedNameAttributePrefix + i);
		if(lName != ''){
			lTaxonNames.push(lName);
			if(gTaxonNodeSelector != ''){
				gTaxonNodeSelector +=',';
			}
			gTaxonNodeSelector += '*[' + gTaxonParsedNameAttributeName + '="' + lName + '"]';
		}
	}

	var lOccurrences = GetArticlePreviewContent().find(gTaxonNodeSelector);
	if(!lOccurrences.length){
		return;
	}
	var lResult = false;
	if(gCurrentTaxonOccurrenceNavigationTaxonNode == false){
		if(pPrevious){
			lResult = lOccurrences.last()[0];
		}else{
			lResult = lOccurrences.first()[0];
		}
	}else{
		var lNodeFoundBefore = false;
		lOccurrences.each(function(pIdx, pNode){
			var lNodePositionRelativeToCurrentNavigationTaxonNode = compareNodesOrder(gCurrentTaxonOccurrenceNavigationTaxonNode, pNode);

			if(pPrevious){
				if(lNodePositionRelativeToCurrentNavigationTaxonNode >= 0 && lNodeFoundBefore){
					// If the node is after the selection and we have found one
					// before the selection - stop processing the other nodes
					return false;
				}
				lResult = pNode;
				if(lNodePositionRelativeToCurrentNavigationTaxonNode < 0 && !lNodeFoundBefore){
					lNodeFoundBefore = true;
				}
			}else{
				if(!lResult){
					lResult = pNode;
				}

				if(lNodePositionRelativeToCurrentNavigationTaxonNode > 0){
					// If the comment is after the selection - it is the first after it
					lResult = pNode;
					return false;
				}
			}
		});
	}
	if(lResult){
		SetCurrentTaxonNavigationNode(lResult, pTaxonNamesNode);
	}
}

function ResetTaxonOccurrenceNavigation(){
	RemoveCurrentTaxonNavigationNodeHighlight();
	gCurrentTaxonOccurrenceNavigationTaxonNode = false;
	gCurrentTaxonOccurrenceNavigationTaxonNamesNode = false;
}

function SetCurrentTaxonNavigationNode(pNode, pNamesNode){
	ScrollArticleToNode(pNode, 150);
	RemoveCurrentTaxonNavigationNodeHighlight();
	gCurrentTaxonOccurrenceNavigationTaxonNode = pNode;
	gCurrentTaxonOccurrenceNavigationTaxonNamesNode = pNamesNode;
	$(gCurrentTaxonOccurrenceNavigationTaxonNode).addClass(gHighlightedElementClass);
}

function RemoveCurrentTaxonNavigationNodeHighlight(){
	$(gCurrentTaxonOccurrenceNavigationTaxonNode).removeClass(gHighlightedElementClass);
}

function PlaceCitatedElementsNavigationEvents(pInPreviewIframe){
	GetCustomElementsContents(pInPreviewIframe).find('.P-Citation-Navigation-Link-Prev,.P-Citation-Navigation-Link-Next').each(function(pIdx, pNode){
		var lInstanceIdNode= $(pNode).closest('*[data-cited-element-instance-id]');
		var lInstanceId = parseInt(lInstanceIdNode.attr('data-cited-element-instance-id'));
		if(lInstanceId > 0){

			$(pNode).bind('click', function(pEvent){
				pEvent.stopPropagation();
				NavigateToPrevNextElementCitation(lInstanceId, $(pNode).hasClass('P-Citation-Navigation-Link-Prev'));
			});
		}
	});
	GetCustomElementsContents(pInPreviewIframe).find('.P-Citation-Navigation-Link-First').each(function(pIdx, pNode){
		var lInstanceIdNode= $(pNode).closest('*[data-cited-element-instance-id]');
		var lInstanceId = parseInt(lInstanceIdNode.attr('data-cited-element-instance-id'));
		if(lInstanceId > 0){

			$(pNode).bind('click', function(pEvent){
				pEvent.stopPropagation();
				NavigateToFirstElementCitation(lInstanceId);
			});
		}
	});
}

function NavigateToFirstElementCitation(lInstanceId){
	ResetCitatedElementNavigation();
	NavigateToPrevNextElementCitation(lInstanceId, false);
}

function NavigateToPrevNextElementCitation(lInstanceId, pPrevious){
	if(gCurrentCitatedElementNavigationNode != false && GetCurrentCitatedElementNavigationNodeInstanceId() != lInstanceId){
		ResetCitatedElementNavigation();
	}

	var lOccurrences = GetArticlePreviewContent().find('xref[' + gCitationElementInstanceIdAttributeName + '="' + lInstanceId + '"]');
	if(!lOccurrences.length){
		return;
	}
	var lResult = false;
	if(gCurrentCitatedElementNavigationNode == false){
		if(pPrevious){
			lResult = lOccurrences.last()[0];
		}else{
			lResult = lOccurrences.first()[0];
		}
	}else{
		var lNodeFoundBefore = false;
		lOccurrences.each(function(pIdx, pNode){
			var lNodePositionRelativeToCurrentNavigationNode = compareNodesOrder(gCurrentCitatedElementNavigationNode, pNode);

			if(pPrevious){
				if(lNodePositionRelativeToCurrentNavigationNode >= 0 && lNodeFoundBefore){
					// If the node is after the selection and we have found one
					// before the selection - stop processing the other nodes
					return false;
				}
				lResult = pNode;
				if(lNodePositionRelativeToCurrentNavigationNode < 0 && !lNodeFoundBefore){
					lNodeFoundBefore = true;
				}
			}else{
				if(!lResult){
					lResult = pNode;
				}

				if(lNodePositionRelativeToCurrentNavigationNode > 0){
					// If the node is after the selection - it is the first after it
					lResult = pNode;
					return false;
				}
			}
		});
	}
	if(lResult){
		SetCurrentCitatedElementNavigationNode(lResult);
	}
}

function GetCurrentCitatedElementNavigationNodeInstanceId(){
	if(gCurrentCitatedElementNavigationNode != false){
		return $(gCurrentCitatedElementNavigationNode).attr(gCitationElementInstanceIdAttributeName);
	}
	return 0;
}

function ResetCitatedElementNavigation(){
	RemoveCurrentCitatedElementNavigationNodeHighlight();
	gCurrentCitatedElementNavigationNode = false;
}

function SetCurrentCitatedElementNavigationNode(pNode){
	ScrollArticleToNode(pNode, 10);
	RemoveCurrentCitatedElementNavigationNodeHighlight();
	gCurrentCitatedElementNavigationNode = pNode;
	$(gCurrentCitatedElementNavigationNode).addClass(gHighlightedElementClass);
}

function RemoveCurrentCitatedElementNavigationNodeHighlight(){
	$(gCurrentCitatedElementNavigationNode).removeClass(gHighlightedElementClass);
}

function LoadMapScript() {
//  var script = document.createElement("script");
//  script.type = "text/javascript";
//  script.src = "http://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&callback=InitLocalitiesMap";
//  document.body.appendChild(script);
	InitLocalitiesMap();
}

function SetLocalitiesHolderHeight(){
	var lHolder = $('.P-Localities-Menu');
	if(!lHolder.length){
		return;
	}
	var lWindowHeight = $(window).height();
	var lLocalitiesHolderOffset = $('.P-Localities-Menu').offset().top;
	$('.P-Localities-Menu').height(lWindowHeight - lLocalitiesHolderOffset);
}

function InitLocalitiesMap(){
	google.maps.visualRefresh = true;
	var lMapCenterCorrdinates = new google.maps.LatLng(0, 0);
	var lMapOptions = {
		zoom : 1,
		tileSize: new google.maps.Size(401, 512),
		center : lMapCenterCorrdinates
	};
//	SetLocalitiesHolderHeight();
	gArticleMap = new google.maps.Map(document.getElementById(gMapHolderId), lMapOptions);

}

function ShowSingleCoordinate(pLatitude, pLongitude){
	if(gMenuActiveElementType != gLocalitiesMenuElementType){
		LoadArticleMenuMainElement(gLocalitiesMenuElementType);
	}
	ClearActiveLocalities();
	var lLocalityId = gLocalityByCoordinatesArr[pLatitude][pLongitude];
	if(!lLocalityId){
		return;
	}
	var lLocality = gLocalitiesList[lLocalityId];
	if(!lLocality){
		return;
	}
	gActiveLocalityIds.push(lLocalityId);
	lLocality.showMarker();
}

function PlaceLocalitiesMenuEvents(){
	$('.P-Clear-Localities').bind('click', function(){ClearActiveLocalities();});
	$('input[name="active-localities"]').bind('change', function(){
		var lInputValue = parseInt($(this).val());
		var lInputIsChecked = $(this).is(':checked');
		if(lInputIsChecked && lInputValue < 0){
			var lFollowingInputs = $('input[name="active-localities"]').filter(function(pIdx){
				var lCurrentValue = parseInt($(this).val());
				return lCurrentValue > lInputValue;
			});
			lFollowingInputs.each(function(pIdx, pElement){
				$(pElement).attr('disabled', 'disabled');
				$(pElement).attr('checked', 'checked');
			});
		}
		if(!lInputIsChecked && lInputValue < 0){
			var lFollowingInputs = $('input[name="active-localities"]').filter(function(pIdx){
				var lCurrentValue = parseInt($(this).val());
				if(lInputValue == gLocalitySelectAllInputValue){
					return lCurrentValue > lInputValue && lCurrentValue < 0;
				}else{
					return lCurrentValue > lInputValue;
				}
			});
			lFollowingInputs.each(function(pIdx, pElement){
				$(pElement).removeAttr('disabled');
			});
		}
		GetActiveLocalitiesFromMenuSelection();
	});
	 $('#all').prop('checked', true);
	 $('#all').trigger('change');
}

function correctIframeLinks(pIframeId, pLinkPrefix){
	document.getElementById(pIframeId).contentWindow.changeRootLocation = function(pLocation){
		parent.location.href = pLinkPrefix + encodeURIComponent(pLocation);
	};
	var lLinks = $('#' + pIframeId).contents().find('a');
	for( var i = 0; i < lLinks.length; ++i ){
		var lLink = lLinks[i];
		var lLinkHref = lLink.getAttribute('href');
		lLink.setAttribute('href', pLinkPrefix + encodeURIComponent(lLinkHref));
	}
}


function GetActiveLocalitiesFromMenuSelection(){
	var lSelectedInputs = $('input[name="active-localities"]:checked');
	var lNewActiveLocalities = [];
	if(lSelectedInputs.filter('*[value="' + gLocalitySelectAllInputValue + '"]').length > 0){
		for(var lLocalityId in gLocalitiesList){
			lNewActiveLocalities.push(lLocalityId);
		}
	}else if(lSelectedInputs.filter('*[value="' + gLocalitySelectAllInstancesInputValue + '"]').length > 0){
		for(var lInstanceId in gLocalityByInstanceIdArr){
			for(var i = 0; i < gLocalityByInstanceIdArr[lInstanceId].length; ++i){
				var lLocalityId = gLocalityByInstanceIdArr[lInstanceId][i];
				if(lNewActiveLocalities.indexOf(lLocalityId) == -1){
					lNewActiveLocalities.push(lLocalityId);
				}
			}
		}
	}else{
		lSelectedInputs.each(function(pIdx, pElement){
			var lInstanceId = parseInt($(pElement).val());
			for(var i = 0; i < gLocalityByInstanceIdArr[lInstanceId].length; ++i){
				var lLocalityId = gLocalityByInstanceIdArr[lInstanceId][i];
				if(lNewActiveLocalities.indexOf(lLocalityId) == -1){
					lNewActiveLocalities.push(lLocalityId);
				}
			}
		});
	}
	var lLocalitiesToRemove = arrayDiff(gActiveLocalityIds, lNewActiveLocalities);
	//Hide all the markers which should not be visible
	for(var i = 0; i < lLocalitiesToRemove.length; ++i){
		var lLocalityId = lLocalitiesToRemove[i];
		var lLocality = gLocalitiesList[lLocalityId];
		if(!lLocality){
			continue;
		}
		lLocality.hideMarker();
	}
	//Show all the markers which should be visible
	for(var i = 0; i < lNewActiveLocalities.length; ++i){
		var lLocalityId = lNewActiveLocalities[i];
		var lLocality = gLocalitiesList[lLocalityId];
		if(!lLocality){
			continue;
		}
		lLocality.showMarker();
	}
	gActiveLocalityIds = lNewActiveLocalities;

}

function ClearActiveLocalities(){
	for(var i = 0; i < gActiveLocalityIds.length; ++i){
		var lLocalityId = gActiveLocalityIds[i];
		if(!lLocalityId){
			continue;
		}
		var lLocality = gLocalitiesList[lLocalityId];
		if(!lLocality){
			continue;
		}
		lLocality.hideMarker();
	}
	gActiveLocalityIds = [];
	var lInputs = $('input[name="active-localities"]');
	lInputs.removeAttr('checked');
	lInputs.removeAttr('disabled');
}

function callFormattingService(){
	var style = $('#chosen-select').val().toLowerCase().replace(/ /g, '-');
	var oReq  = new XMLHttpRequest();
		oReq.onload = function(){ $('#formattedRef').html( $('<div/>').html(this.responseText).text() ); };
		oReq.open("get", server +'/format?ref=' + ref + '&style=' + style, true);
		oReq.send();
}

function ScrollToTaxonCategory(pCategoryName){
	var lCategoryLink = $('#category_' + pCategoryName);
	var lPosition = lCategoryLink.position().top;
	$('.P-Article-Info-Bar').scrollTop($('.P-Article-Info-Bar').scrollTop() + lPosition - 56);
}

function InitCommentForm(pDiv, pJournalId, pArticleId) {
	$.ajax({
		url : '/article_comment_form.php',
		async : false,
		data : {
			journal_id : pJournalId,
			article_id : pArticleId
		},
		success : function(pAjaxResult) {
			if(pAjaxResult['err_cnt']){
				alert(pAjaxResult['err_msg']);
				return;
			}
			$('#' + pDiv).html(pAjaxResult['html']);
		}
	});
}

function submitArticleNewComment(pOper, pFormName, pId) {
	if(pOper == 1){
		for(var lInstanceName in CKEDITOR.instances){
		    CKEDITOR.instances[lInstanceName].updateElement();
		}
	}
	var lJqFormSel = $('form[name="' + pFormName + '"]')
	var lFormData = lJqFormSel.formSerialize();
	if(pOper == 1){
		lFormData += '&tAction=comment';
	}
	
	if(typeof pId != 'undefined' && pId){
		lFormData += '&id=' + pId;
	}
	
	if(pOper == 2){
	//	if(!confirm('Are you sure you want to approve this comment?')){
	//		$("#approve_" + pId).attr('checked', false); 
	//		return false;
	//	}
		lFormData += '&tAction=approve';
	}
	if(pOper == 3){
	//	if(!confirm('Are you sure you want to reject this comment?')){
	//		$("#reject_" + pId).attr('checked', false); 
	//		return false;
	//	}
		lFormData += '&tAction=reject';
	}
	

	$.ajax({
		url : '/article_comment_form.php',
		type : 'POST',
		data : lFormData,
		success : function(pAjaxResult) {
			if(pAjaxResult['success']){
				if(pOper == 1){
					for(var lInstanceName in CKEDITOR.instances){
					    CKEDITOR.instances[lInstanceName].setData('');
					}
				}
		
				$('#article_comment_textarea').val('');
				if(pOper == 1){
					alert(pAjaxResult['success_msg']);
				}
				LoadCommentList('article_messages_wrapper_content');
				return;
			}
			if(pAjaxResult['err_cnt']){
				alert(pAjaxResult['err_msg']);
				return;
			}
			return;
		}
	});
}

function LoadCommentList(pHolder) {
	$.ajax({
		url : gArticleAjaxSrvUrl,
		async : false,
		data : {
			action : 'get_main_list_element',
			element_type : 13,
			article_id : $('#comments_article_id').val(),
			comment_list_flag : 1
		},
		success : function(pAjaxResult) {
			if(pAjaxResult['err_cnt']){
				alert(pAjaxResult['err_msg']);
				return;
			}
			$('#' + pHolder).html(pAjaxResult['html']);
		}
	});
}

function SetCommentDateLabel(pHolderId, pDateInSeconds, pDateString){
	var lCurrentDate = new Date();
	var lYear = lCurrentDate.getUTCFullYear();
	var lMonth = lCurrentDate.getUTCMonth();
	var lDays = lCurrentDate.getUTCDate();
	var lHours = lCurrentDate.getUTCHours();
	var lMinutes = lCurrentDate.getUTCMinutes();
	var lSeconds = lCurrentDate.getUTCSeconds();
	var lMilliseconds = lCurrentDate.getUTCMilliseconds();
	var lCurrentSeconds = Math.floor(Date.UTC(lYear, lMonth, lDays, lHours, lMinutes, lSeconds, lMilliseconds) / 1000);
	var lLabel = '';
	var lTimeoutSeconds = 0;
	var lDiff = lCurrentSeconds - pDateInSeconds;
	//Remove the offset of the current time to UFC time because pDateInSeconds is in UFC time
	lDiff -= lCurrentDate.getTimezoneOffset() * 60;//The offset is in minutes
	if(lDiff < 60){
		lLabel = 'less than a minute ago';
		lTimeoutSeconds = 60 - lDiff;		
	}else if(lDiff < 3600){
		lLabel = Math.floor(lDiff / 60);
		if(lLabel == 1){
			lLabel += ' minute';
		}else{
			lLabel += ' minutes';
		}
		lLabel += ' ago';
		lTimeoutSeconds = 60 - (lDiff % 60);		
	}else if(lDiff < 3600 * 24){
		lLabel = Math.floor(lDiff / 3600);
		if(lLabel == 1){
			lLabel += ' hour';
		}else{
			lLabel += ' hours';
		}
		lLabel += ' ago';
		lTimeoutSeconds = 3600 - (lDiff % 3600);		
	}else{
		lLabel = pDateString;
	}
	$('#' + pHolderId).html(lLabel);
	if(lTimeoutSeconds > 0){
		setTimeout(function(){SetCommentDateLabel(pHolderId, pDateInSeconds, pDateString);}, lTimeoutSeconds * 1000);
	}
}
