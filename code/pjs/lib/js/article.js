var gArticleAjaxSrvUrl = gAjaxUrlsPrefix + 'article_ajax_srv.php'
var gArticleId = 0;
var gActiveMenuClass = 'P-Active-Menu';
var gArticlePreviewIframeId = 'articleIframe';

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
			LoadInfoContent(pAjaxResult['html'], pElementType);
		}
	});
}

function LoadInfoContent(pContent, pActiveMenuType){
	$('.P-Info-Content').html(pContent);
	$('.P-Info-Menu li.' + gActiveMenuClass).removeClass(gActiveMenuClass);
	$('.P-Info-Menu li[data-info-type="' + pActiveMenuType + '"]').addClass(gActiveMenuClass);
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
//	LoadElementInfo('get_taxon_element', '', pTaxonName);
	console.log('Taxon ' + pTaxonName);
	LoadElementInfo('get_taxon_element', '', 'test');
}

function LoadAuthorInfo(pElementId){
	LoadElementInfo('get_author_element', pElementId);
	console.log('Author ' + pElementId);
//	LoadElementInfo('get_author_element', 4);
}

function initArticlePreviewOnLoadEvents(){
	resizePreviewIframe(gArticlePreviewIframeId);
	PlaceTaxonNameEvents();
	PlaceFigureEvents();
	PlaceTableEvents();
	PlaceReferencesEvents();
	PlaceSupFilesEvents();
}

function SetArticleOnLoadEvents(){
	$('#' + gArticlePreviewIframeId).load(function(){initArticlePreviewOnLoadEvents();});
}

function GetArticlePreviewContent(){
	return $('#' + gArticlePreviewIframeId).contents();
}

function PlaceTaxonNameEvents(){
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
	GetArticlePreviewContent().find('.tn').each(function(pIdx, pTaxonNode){
		$(pTaxonNode).find(lPartsThatLeadToSelfSelector).each(function(pIdx1, pTaxonNamePartNode){
			$(pTaxonNamePartNode).bind('click', function(){
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
					$(pTaxonNamePartNode).bind('click', function(){
						LoadTaxonInfo(lTaxonName);
					});
				});
			}
		}
	});
}

function PlaceFigureEvents(){
	GetArticlePreviewContent().find('.fig[rid]').each(function(pIdx, pFigureNode){
		$(pFigureNode).bind('click', function(){
			LoadFigureInfo($(pFigureNode).attr('rid'));
		});
	});
}

function PlaceTableEvents(){
	GetArticlePreviewContent().find('.table[rid]').each(function(pIdx, pTableNode){
		$(pTableNode).bind('click', function(){
			LoadTableInfo($(pTableNode).attr('rid'));
		});
	});
}

function PlaceSupFilesEvents(){
	GetArticlePreviewContent().find('.suppl[rid]').each(function(pIdx, pSupFileNode){
		$(pSupFileNode).bind('click', function(){
			LoadSupFileInfo($(pSupFileNode).attr('rid'));
		});
	});
}

function PlaceReferencesEvents(){
	GetArticlePreviewContent().find('.bibr[rid]').each(function(pIdx, pReferenceNode){
		$(pReferenceNode).bind('click', function(){
			LoadReferenceInfo($(pReferenceNode).attr('rid'));
		});
	});
}

function ScrollArticleToInstance(pInstanceId){
	var lFirstInstanceElement = GetArticlePreviewContent().find('*[instance_id=' + pInstanceId + ']').first();
	if(!lFirstInstanceElement.length){
		return;
	}
	var lTopOffset = $(lFirstInstanceElement).offset().top;
	$('.P-Article-Preview').scrollTop(lTopOffset);
}