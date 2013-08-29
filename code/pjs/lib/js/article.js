var gArticleAjaxSrvUrl = gAjaxUrlsPrefix + 'article_ajax_srv.php'
var gArticleId = 0;
var gActiveMenuClass = 'P-Active-Menu';
var gArticlePreviewIframeId = 'articleIframe';
var gMapHolderId = 'localitiesMap';
var gArticleMap = false;
var gContentsMenuElementType = 1;
var gLocalitiesMenuElementType = 6;
var gMenuActiveElementType = false;

var gLocalitiesList = {};
var gActiveLocalityIds = [];
var gLocalityByCoordinatesArr = {};
var gLocalityByInstanceIdArr = {};
var gLocalitySelectAllInputValue = -2;
var gLocalitySelectAllInstancesInputValue = -1;

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
			gMenuActiveElementType = pElementType;
			LoadInfoContent(pAjaxResult['html'], pElementType);					
		}
	});
}

function LoadInfoContent(pContent, pActiveMenuType){
	$('.P-Info-Content').html(pContent);
	$('.P-Info-Menu li.' + gActiveMenuClass).removeClass(gActiveMenuClass);
	$('.P-Info-Menu li[data-info-type="' + pActiveMenuType + '"]').addClass(gActiveMenuClass);
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
	console.log('Fig ' + pElementId);
	//LoadElementInfo('get_figure_element', 1);
}

function LoadTableInfo(pElementId){
	LoadElementInfo('get_table_element', pElementId);
	console.log('Table ' + pElementId);
	//LoadElementInfo('get_table_element', 5);
}

function LoadSupFileInfo(pElementId){
	LoadElementInfo('get_sup_file_element', pElementId);
	console.log('Sup file ' + pElementId);
	//LoadElementInfo('get_sup_file_element', 4);
}

function LoadReferenceInfo(pElementId){
	LoadElementInfo('get_reference_element', pElementId);
	console.log('Reference ' + pElementId);
	//LoadElementInfo('get_reference_element', 9);
}

function LoadTaxonInfo(pTaxonName){
	pTaxonName = PrepareTaxonName(pTaxonName);
//	LoadElementInfo('get_taxon_element', '', pTaxonName);
	console.log('Taxon ' + pTaxonName);
	LoadElementInfo('get_taxon_element', '', pTaxonName);
}

function LoadAuthorInfo(pElementId){
	LoadElementInfo('get_author_element', pElementId);
	console.log('Author ' + pElementId);
//	LoadElementInfo('get_author_element', 4);
}

function InitContentsCustomElementsEvents(pInPreviewIframe){
	PlaceTaxonNameEvents(pInPreviewIframe);
	PlaceFigureEvents(pInPreviewIframe);
	PlaceTableEvents(pInPreviewIframe);
	PlaceReferencesEvents(pInPreviewIframe);
	PlaceSupFilesEvents(pInPreviewIframe);
	PlaceLocalitiesEvents(pInPreviewIframe);
	PlaceAuthorEvents(pInPreviewIframe);
}

function initArticlePreviewOnLoadEvents(){	
	resizePreviewIframe(gArticlePreviewIframeId);	
	InitContentsCustomElementsEvents(1);
	LoadArticleLocalities();
}

function SetArticleOnLoadEvents(){
	$('#' + gArticlePreviewIframeId).load(function(){initArticlePreviewOnLoadEvents();});
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
		'subkingdom', 
		'phylum', 
		'subphylum', 
		'superclass', 
		'class', 
		'subclass', 
		'superorder', 
		'order', 
		'suborder', 
		'infraorder', 
		'superfamily', 
		'family', 
		'subfamily', 
		'tribe', 
		'subtribe', 
		'genus', 
		'subgenus',
		'above-genus'
	];
	var lPartsThatDontLeadToSelf = {
		'species' : ['genus', 'species'],
		'subspecies' : ['genus', 'species', 'subspecies'],
		'variety' : ['genus', 'species', 'variety'],
		'form' : ['genus', 'species', 'form']
	};
	var lPartsThatLeadToSelfSelector = '.' + lPartsThatLeadToSelf.join(',.');
	GetCustomElementsContents(pInPreviewIframe).find('.tn').each(function(pIdx, pTaxonNode){
		$(pTaxonNode).find(lPartsThatLeadToSelfSelector).each(function(pIdx1, pTaxonNamePartNode){
			$(pTaxonNamePartNode).bind('click', function(pEvent){
				pEvent.stopPropagation();
				LoadTaxonInfo($(pTaxonNamePartNode).text());
			});
		});
		for(var lPartName in lPartsThatDontLeadToSelf){
			var lParts = $(pTaxonNode).find('.' + lPartName);
			var lNamePartsOrder = lPartsThatDontLeadToSelf[lPartName];
			if(lParts.length > 0){
				var lTaxonName = '';
				for(var i = 0; i < lNamePartsOrder.length; ++i){
					var lCurrentPart = lNamePartsOrder[i];
					var lCurrentPartText = $(pTaxonNode).find('.' + lCurrentPart).text();
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

function ScrollArticleToInstance(pInstanceId){
	var lFirstInstanceElement = GetArticlePreviewContent().find('*[instance_id=' + pInstanceId + ']').first();
	if(!lFirstInstanceElement.length){
		return;
	}
	var lTopOffset = $(lFirstInstanceElement).offset().top;
	$('#article-preview').scrollTop(lTopOffset);
}

function LoadMapScript() {
//  var script = document.createElement("script");
//  script.type = "text/javascript";
//  script.src = "http://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&callback=InitLocalitiesMap";
//  document.body.appendChild(script);
	InitLocalitiesMap();
}


function InitLocalitiesMap(){
	google.maps.visualRefresh = true;
	var lMapCenterCorrdinates = new google.maps.LatLng(0, 0);
	var lMapOptions = {
		zoom : 1,
		tileSize: new google.maps.Size(401, 512),
		center : lMapCenterCorrdinates
	};  
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

function ScrollToTaxonCategory(pCategoryName){
	var lCategoryLink = $('#category_' + pCategoryName);
	var lPosition = lCategoryLink.position().top;
	$('.P-Article-Info-Bar').scrollTop($('.P-Article-Info-Bar').scrollTop() + lPosition);
}