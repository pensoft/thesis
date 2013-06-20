var gEtaAttributeName = 'eta_idxid';
var gHomeUrl = "/resources/articles/";
var gSrvAddress = '/resources/articles/srv.php';//Адрес на php-то, което се занимава със статията - save, load ...
var gAttributesAddress = '/resources/articles/generateTags.php';//Адрес на php-то, което връща xml-a с таговете
var gMatchScriptAddress = '/resources/articles/match.php?id=';//Адреса на текущия скрипт
var gXmlTextNodeType = 3;
var gXmlElementNodeType = 1;
var dontusesoap = 1;
var soapbrowser = 'unknown';
var gGetAction = 1;
var gSaveAction = 2;
var gValidateAction = 3;
var gSaveAndCreateNewAction = 4;

var gAutotagSrv = "/resources/autotag/autotag.php";//Адресът на php файлът, в който се аутотагва xml-а
var gTaxonRuleId = 5;//Id-то на правилото за аутотагване на таксони
var gAutoTagMatchPageSize = 0;
var gMatchCheckboxPrefix = 'match_';//Prefix на имената на чекбоксите в попъпа за управление на мачовете
var gMatchReplacementPrefix = 'replacement_';//Prefix на имената на заменящия текст в попъпа за управление на мачовете
var gAutotagMarkerNodeName = 'autotag_marker';
var gAutotagMarkerMatchNumberAttributeName = 'match_number';
var gAutotagMarkerMatchPartNumberAttributeName = 'match_part';
var gAutoTagRulesSrvAddress = '/resources/autotag_rules/index.php/';//Адрес на php-то, което връща всички ауто таг правила
var gAutoNumerateRulesSrvAddress = '/resources/auto_numerate_rules/index.php/';//Адрес на php-то, което връща всички правила за автоматично номериране
var gDocType = '<!DOCTYPE article PUBLIC "-//NLM//DTD Journal Publishing DTD v3.0 20080202//EN" "' + gSiteUrl + '/lib/publishing/tax-treatment-NS0.dtd">';
var gExternalAutotagType = 2;
var gInternalAutotagType = 1;
var gUbioTaxonRuleId = 50;
var gAutotagWrappingTextLength = 20;//Броя на символите около мач-а, които показва
var gNamespaceDeclarations = 'xmlns:mml="http://www.w3.org/1998/Math/MathML" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:tp="http://www.plazi.org/taxpub"';
var gMatchPartColorStringHash = 'tyrquioqreuioytyutryrteaqwsgqgfasdjlzxmmnbvczxqweiopuhjklasd';//Ot tozi string vzimame pyrvite X + 6 simvola i po tqh pravim hash za cvqt. X  - nomera na chastta ot matcha
var gAutotagReplacementInputIdPrefix = 'autotag_replacement_';//Prefix за ид на текстареата в която се показва replacement-a за даден autotag match

var gRightClickMenuWidth = 200;//Широчина на менюто при натискане на десен бутон
var gRightClickMenuHeight = 400;//Височина на менюто при натискане на десен бутон
var gNewLineSymbol = '\n';

var gNodeNameDivSuffix = '_menu_link_holder';
var gNodeNameStylePrefix = 'css_';

var gTaxonNodeName = 'tp:taxon-name';
var gFakeFiguresTagName = 'article_figs_and_tables';
var gFigureTagName = 'fig';
var gTableTagName = 'table';
var gTableWrapTagName = 'table-wrap';

var TEXT_MODE = 1;//Vijdame teksta i sorsa i mojem da autotagvame
var SOURCE_MODE = 2;//Vijdame sorsa i ne mojem da pravim po4ti nishto

Function.prototype.bind = function(context) {
  var fn = this;
  return function() {
    if(arguments.length !== 0) {
        return fn.apply(context, arguments);
      } else {
        return fn.call(context); // faster in Firefox.
      }
  };
};

TextHolder = function(
		pDivText, pDivTextTableHolder, 
		pDivXml, pDivXmlTableHolder, 
		pDivMenu, pFormattingMenu, 
		pTextRightClickDiv, pTextRightClickDivHolder, 
		pPopupDiv, pPopupHolderDiv, 
		pBGDiv, 
		pSourceRightClickDiv, pSourceRightClickDivHolder, 
		pTagNameDiv, pTagNameHolderDiv, 
		pDocumentId, 
		pAutotagRightClickDiv, pAutotagRightClickDivHolder,
		pTextRightClickSubmenuDiv, pTextRightClickSubmenuDivHolder,
		pTextModeToolsDivId, pSourceModeToolsDivId, 
		pSourceTextAreaHolder,
		pSourceModeRightClickDiv, pSourceModeRightClickDivHolder
	){	
	this.divText = document.getElementById(pDivText);//Дива в който ще излиза текстовия прозорец
	this.divTextTable = document.getElementById(pDivTextTableHolder);//Дива в който ще излиза текстовия прозорец
	
	this.divXml = document.getElementById(pDivXml);	//Дива в който ще излиза прозореца със сорса
	this.divXmlTable = document.getElementById(pDivXmlTableHolder);	//Дива в който ще излиза прозореца със сорса
	
	this.divBackground = document.getElementById(pBGDiv);	//Дива който ще служи като background при излизане на попъпа и ще спира натискането на нещо извън попъпа
	
	this.divTextRightClick = document.getElementById(pTextRightClickDiv);//Дива в който ще излиза менюто при натискане на десен бутон в текстовия прозорец	
	this.divTextRightClickHolder = document.getElementById(pTextRightClickDivHolder);//Дива който обгражда divTextRightClick	
	
	this.divSourceRightClick = document.getElementById(pSourceRightClickDiv);//Дива в който ще излиза менюто при натискане на десен бутон в прозореца със сорса	
	this.divSourceRightClickHolder = document.getElementById(pSourceRightClickDivHolder);//Дива който обгражда divSourceRightClick
	
	this.divPopup = document.getElementById(pPopupDiv);//Дива, който представлява попъпа	
	this.divPopupHolder = document.getElementById(pPopupHolderDiv);//Дива който обгражда divPopup
	
	this.divTagNameMenu = document.getElementById(pTagNameDiv);//Дива, където ще излиза менюто с имената на node-овете
	this.divTagNameMenuHolder = document.getElementById(pTagNameHolderDiv);//wrapper на горния див
	
	this.divMenu = document.getElementById(pDivMenu);//Дива, където ще излиза дясното меню
	this.divFormattingMenu = document.getElementById(pFormattingMenu);//Дива, където ще излиза менюто с B, I, U
	
	this.divAutotagRightClick = document.getElementById(pAutotagRightClickDiv);//Дива в който ще излиза менюто при натискане на десен бутон в прозореца със аутотаг мачове	
	this.divAutotagRightClickHolder = document.getElementById(pAutotagRightClickDivHolder);//Дива който обгражда divAutotagRightClick
	
	this.divTextRightClickSubmenu = document.getElementById(pTextRightClickSubmenuDiv);//Дива в който ще излиза подменюто за добавяне на фигури таблици
	this.divTextRightClickSubmenuHolder = document.getElementById(pTextRightClickSubmenuDivHolder);//Дива който обгражда divTextRightClickSubmenu
	
	this.divTextModeTools = document.getElementById(pTextModeToolsDivId);//Дива в който стоят нещата за текстовия mode - текст, сорс, тагове ...
	this.divSourceModeTools = document.getElementById(pSourceModeToolsDivId);//Дива в който стои текстареата за сорса
	this.divTextArea = document.getElementById(pSourceTextAreaHolder);//Дива който ще държи ифраме-а с текстареата	
	this.sourceTempXmlDocument = EWorkaround.newDomDocument();
	
	this.divSourceModeRightClick = document.getElementById(pSourceModeRightClickDiv);//Дива в който ще излиза менюто при натискане на десен бутон в текстареата със сорса	
	this.divSourceModeRightClickHolder = document.getElementById(pSourceModeRightClickDivHolder);//Дива който обгражда divSourceModeRightClick
	
	this.addEvent('mouseout', function(){this.FiguresSubMenuTimeout = setTimeout(this.hideFugiresSubMenu.bind(this), 100);}.bind(this), this.divTextRightClickSubmenuHolder);
	this.addEvent('mouseover', function(){clearTimeout(this.FiguresSubMenuTimeout);}.bind(this), this.divTextRightClickSubmenuHolder);
	
	this.FiguresSubmenuIsDisplayed = false;//Използва се за скриване показване на менюто с фигурите
	this.FiguresSubMenuTimeout = false;//Понеже показваме на менюто с фигурите с таймаут
	
	this.xmlDomDocument = EWorkaround.newDomDocument();//DOM Document за xml-a
	
	this.documentId = pDocumentId;//Id на текущата статия - използва се при save/load	
	this.setXmlIndexesAllowed = false;
	
	this.AutotagAnnotateTags = new Array();
	this.iframeText = null;
	this.iframeXml = null;
	this.sourceIsHidden = 0;
	this.tagsAreHidden = false;
	this.changeTimeout = false;
	this.updatingNodes = false;
	this.indexNodeNames = false;//Когато е сетнат този флаг във функцията getXmlText ще се индексират имената на node-овете
	this.EDispetcher = new EDispecher();//Използва се за ajax заявките
	this.nodeNames = {};
	
	var lThis = this;
	this.currentXmlNodeIdx = 1;
	this.xmlNodeHash = {};
	this.lineBreakTags = ['br'];
	this.autotagRuleId = null;
	this.mode = TEXT_MODE;	//Определя в какъв режим сме - виждаме сорс и текст и правим всичко, или виждаме само сорс и не можем да правим почти нищо
	//~ this.xmlNodeHashReverse = {};
	
	//Бутони на форматиращото меню
	this.formattingButtons = {
		'B' : {
			'callback' : function(){
				this.annotateBlockSelection('bold');
			}
		},
		'I' : {
			'callback' : function(){
				this.annotateBlockSelection('italic');
			}
		},
		'U' : {
			'callback' : function(){
				this.annotateBlockSelection('underline');
			}
		}
	};	
	
	this.menuButtons = {
		'Reload XML' : {
			'callback' : function(){
				if( confirm('Are you sure you want to discard changes and reload the document?')){
					this.loadXmlDocument();
				}
			},
			'display_in_modes' : [TEXT_MODE, SOURCE_MODE]//Масива определя в кой mode, кои бутони да се показват
		},
		'Save' : {
			'callback' : this.saveXmlDocument,
			'display_in_modes' : [TEXT_MODE, SOURCE_MODE]
		},
		'Save and Create new' : {
			'callback' : this.saveAndCreateNew,
			'display_in_modes' : [TEXT_MODE, SOURCE_MODE]
		},
		'Save and Close' : {
			'callback' : this.saveAndCloseXmlDocument,
			'display_in_modes' : [TEXT_MODE, SOURCE_MODE]
		},
		'Close without saving' : {
			'callback' : function(){
				if( confirm('Are you sure you want to discard changes and close the document?')){
					this.redirectUserToHome();
				}
			},
			'display_in_modes' : [TEXT_MODE, SOURCE_MODE]
		},/*
		'Match taxons' : {
			'callback' : function(){
				alert('Match taxons');
			}
		},	*/
		'Perform auto numerate rule' : {
			'callback' : function(){
				this.buildAutoNumerateRulesPopup();
			},
			'display_in_modes' : [TEXT_MODE],
			'attributes' :{				
			}
		},		
		'Perform autotag rule' : {
			'callback' : function(){
				this.buildAutotagRulesPopup();
			},
			'attributes' :{				
			},
			'display_in_modes' : [TEXT_MODE]
		},
		'Autotag taxons @ ubio' : {
			'callback' : function(){
				this.autotagXML(gUbioTaxonRuleId, gExternalAutotagType);
			},
			'attributes' :{
				'id' : 'italicButton',				
			},
			'display_in_modes' : [TEXT_MODE]
		/*},
		'Autotag taxons' : {
			'callback' : function(){
				this.autotagXML(gTaxonRuleId, gInternalAutotagType);
			},
			'attributes' :{
				'id' : 'italicButton',				
			}*/
		},/*//Podava xml sys vsichki tagove i tehnite atributi
		'Export tags' : {
			'callback' : function(){
				this.exportTags();
			}
		},*/
		'Check reference citations count' : {
			'callback' : function(){
				this.checkReferenceCitationCount();
			},
			'display_in_modes' : [TEXT_MODE]			
		},
		'Position at next citation of fig/table' : {
			'callback' : function(){
				this.positionAtNextFigTableCitation();
			},
			'display_in_modes' : [TEXT_MODE, SOURCE_MODE]			
		},
		'Validate XML' : {
			'callback' : function(){
				this.validateXml();
			},
			'display_in_modes' : [TEXT_MODE]
		},
		'Normalize XML' : {
			'callback' : function(){
				this.normalizeXmlDocument();
			},
			'display_in_modes' : [TEXT_MODE]
		},
		'Hide tags menu' : {
			'callback' : this.hideTagsMenu,
			'attributes' :{
				'id' : 'hideTagsButton'
			},
			'display_in_modes' : [TEXT_MODE]
		},
		'Show tags menu' : {
			'callback' : this.showTagsMenu,
			'attributes' :{
				'id' : 'showTagsButton',
				'style' : 'display:none'
			},
			'display_in_modes' : [TEXT_MODE]
		},		
		'Hide XML' : {
			'callback' : this.hideXmlCode,
			'attributes' :{
				'id' : 'hideXmlButton'
			},
			'display_in_modes' : [TEXT_MODE]
		},
		'Show XML' : {
			'callback' : this.showXmlCode,
			'attributes' :{
				'id' : 'showXmlButton',
				'style' : 'display:none'
			},
			'display_in_modes' : [TEXT_MODE]
		},
		'Change to text mode' : {
			'callback' : function(){this.changeMode(TEXT_MODE)},			
			'display_in_modes' : [SOURCE_MODE]
		},
		'Change to source mode' : {
			'callback' : function(){this.changeMode(SOURCE_MODE)},			
			'display_in_modes' : [TEXT_MODE]
			
		},
		'Change to text mode and ignore changes' : {
			'callback' : function(){this.changeMode(TEXT_MODE, true)},			
			'display_in_modes' : [SOURCE_MODE]
			
		}
		/*,//Debug 
		'Show Text InnerHtml' : {
			'callback' : function(){
				alert(this.contentDocumentText.body.innerHTML);
			}
		},
		'Show XML content' : {
			'callback' : function(){
				alert(this.xmlDomDocument.xml);console.log(this.xmlDomDocument.xml);
			}
		},
		'Show Source InnerHtml' : {
			'callback' : function(){
				alert(this.contentDocumentXml.body.innerHTML);
			}
		},
		'Show Cloned XML' : {
			'callback' : function(){
				alert(this.autotagCloneXml.xml);console.log(this.autotagCloneXml.xml);
			}
		}*/
		
	};
	//Бутони за менюто на десен бутон върху текста
	this.textRightClickButtons = {
		'Annotate' : {
			'callback' : function(){
				this.hideTextRightClickMenu();
				this.buildAnnotatePopup();
			}
		},
		'Paste table or figure' : {
			'onmouseover' : function(pEvent){
				this.showFiguresSubMenu(pEvent);
			},
			'onmouseout' : function(){
				this.FiguresSubMenuTimeout = setTimeout(this.hideFugiresSubMenu.bind(this), 100);
			},
			'callback' : function(){				
			}
		},
		'Scroll Source Div' : {
			'callback' : function(){
				this.scrollSourceDiv();
				this.hideTextRightClickMenu();
			}
		}
		
	};
	//Бутони за менюто на десен бутон върху сорса
	this.sourceRightClickButtons = {
		'Change Attributes' : {
			'callback' : function(){
				this.hideSourceRightClickMenu();				
				this.buildTagAttrEditPopup();
			}
		},
		'Rename tag' : {
			'callback' : function(){				
				this.hideSourceRightClickMenu();
				this.buildRenameTagPopup();				
			}
		},
		'Remove tag' : {
			'callback' : function(){				
				this.hideSourceRightClickMenu();
				if( confirm('Are you sure you want to delete this node?') ){
					this.removeTag();					
				}
			}
		},
		'Scroll Text Div' : {
			'callback' : function(){
				this.scrollTextDiv();
				this.hideSourceRightClickMenu();
			}
		}
		
	};
	
	//Тагове за анотации + възможните атрибути за тези тагове
	
	this.loadTagsAndAttributes();
	
	this.nodesToChange = new Array();
}

/**
	Сменя mode-a в който работим. Връща true при успех и false при грешка.
	Ако искаме да минем в текстов режим и да игнорираме промените питаме дали потребителя е сигурен.
*/
TextHolder.prototype.changeMode = function( pMode, pIgnoreChangesFromSourceMode ){
	if( pMode == this.mode )
		return true;
	if( pMode == TEXT_MODE && pIgnoreChangesFromSourceMode ){
		if( ! confirm( 'Are you sure you want to ignore changes made in source mode?' ) )
			return false;
	}
	
	switch( pMode ){
		default:{
			alert( 'Unrecognized mode' );
			return false;
		}
		case TEXT_MODE:{//Minavame ot source mode v tekst mode
			if( pIgnoreChangesFromSourceMode ){//Samo kriem i pokazvame divovete - ne promenqme xml-a
			}else{//Pyrvo testvame dali xml-a e validen i sled toga go smenqme
				if( !this.syncSourceXml() )
					return false;
			}
			this.divSourceModeTools.style.display = 'none';
			this.divTextModeTools.style.display = 'block';
			break;
		}
		case SOURCE_MODE:{//Minavame ot text mode v source mode
			//Slagame source-a na xml-a i kriem/pokazvame divovete
			this.removeEtaAttribute(this.xmlDomDocument);
			this.sourceTextArea.value = this.xmlDomDocument.xml;
			this.divTextModeTools.style.display = 'none';
			this.divSourceModeTools.style.display = 'block';			
			break;
		}
	}
	this.mode = pMode;
	this.buildMenu();
	return true;
}

// Слага xml-a от текстареата в this.xmlDomDocument и връща true или връща false, ако xml-a е грешен
TextHolder.prototype.syncSourceXml = function(){
	
	if(!this.loadSourceTempDocument()){		
		return false;
	}
	
	var lNewXml = this.sourceTextArea.value;					
	this.m_xml = lNewXml;	
	this.xmlDomDocument.loadXML(this.m_xml);
	this.nullifyData();
	this.loadTextAndSourceAndIndexXml();
	return true;
}

//Зареждаме съдържанието на текстареата в темп DomDocument за да ползваме xml-a
TextHolder.prototype.loadSourceTempDocument = function(){
	var lNewXml = this.sourceTextArea.value;
	if(!this.sourceTempXmlDocument.loadXML(lNewXml)){
		alert('Xml is not valid, correct errors first!');
		return false;
	}	
	return true;
}

//Зарежда таговете и атрибутите им
TextHolder.prototype.loadTagsAndAttributes = function(){
	var lRes = this.EDispetcher.Call('', -2, '', '', '', gAttributesAddress, false, null, null, null, null);
	//alert(lRes.xml);
	var lXmlDomDoc =  EWorkaround.newDomDocument();;
	if(!lXmlDomDoc.loadXML(lRes.xml)){
		alert('Attribute XML not valid!');
		this.redirectUserToHome();
	}
	var lRoot = lXmlDomDoc.childNodes[0];
	this.annotationTags = {};
	/* //Primer kak izglejda 1 element ot masiva s tagovete
		'abbrev-journal-title' : {
			'display':'block',
			'attributes' : [
				'abbrev-type',
				'xml:lang'
			]
		},
	*/
	for( var i = 0; i < lRoot.childNodes.length; ++i){
		lTag = lRoot.childNodes[i];
		if( lTag.nodeType != gXmlElementNodeType )
			continue;
		var lTagName = lTag.getAttribute('name');
		var lAutotagAnnotateShow = parseInt(lTag.getAttribute('autotag_annotate_show'));
		if( !lTagName ){
			continue;
		}
		var lAttributeArr = [];
		for(var j = 0; j < lTag.childNodes.length; ++j){
			var lAttribute = lTag.childNodes[j];
			if( lAttribute.nodeType != gXmlElementNodeType )
				continue;
			var lAttributeName = lAttribute.getAttribute('name');
			if( !lAttributeName ){
				continue;
			}
			lAttributeArr.push(lAttributeName);
		}
		this.annotationTags[lTagName] = {
			'display' : 'block',
			'attributes' : lAttributeArr
		}
		if( lAutotagAnnotateShow > 0 )
			this.AutotagAnnotateTags.push(lTagName);
		
	}
}
//Прави ajax заявка до php-то и след нея зарежда текста и сорса
TextHolder.prototype.loadXmlDocument = function(){
	
	this.nullifyData();	
	var lPostParams = {
		'id' : this.documentId,
		'action':gGetAction
	};
	var lRes = this.EDispetcher.Call('', -2, '', '', '', gSrvAddress, false, null, null, null, lPostParams);
	if( lRes == '' ){
		lRes = '<article></article>';
	}
	this.m_xml = lRes;	
	if(this.xmlDomDocument.loadXML(this.m_xml)){
		
		//~ alert('XML Loaded');
		this.loadTextAndSourceAndIndexXml();
	}else{
		//alert('XML is not valid!');
		this.redirectUserToHome();
	}
	
	
	
	
	//~ this.m_xml = '<root> asdasdasdas <article name="article" id="1">article<body>body1 <innerbody>innerbodycontent</innerbody>ontentb </body> <body2>body2 content</body2> asdasdas</article></root>';
	
}

TextHolder.prototype.nullifyData = function(){
	this.contentDocumentText.body.innerHTML = '';
	this.contentDocumentXml.body.innerHTML = '';
	this.divTagNameMenu.innerHTML = '';
	this.nodeNames = {};
	this.xmlNodeHash = {};
	this.figuresMenuIsBuild = false;
	this.FiguresSubmenuIsDisplayed = false;//Използва се за скриване показване на менюто с фигурите
	this.FiguresSubMenuTimeout = false;//Понеже показваме на менюто с фигурите с таймаут
	this.sourceTextArea.value = '';
}

TextHolder.prototype.loadTextAndSourceAndIndexXml = function(){
	this.setXmlIndexesAllowed = true;
	this.indexNodeNames = true;
	this.getXmlText(this.xmlDomDocument.firstChild, this.contentDocumentText.body);			
	this.setXmlIndexesAllowed = false;
	this.setXmlIndexesAllowed = false;
	this.getXmlSource(this.xmlDomDocument.firstChild, this.contentDocumentXml.body);
	this.buildNodeNamesMenu();
	this.sourceTempXmlDocument.loadXML(this.xmlDomDocument.xml);
	this.removeEtaAttribute(this.sourceTempXmlDocument);
	this.sourceTextArea.value = this.sourceTempXmlDocument.xml;
}

TextHolder.prototype.buildNodeNamesMenu = function(){
	this.divTagNameMenu.innerHTML = '';	
	var lKeys = new Array();
	for( var lTagName in this.nodeNames ){		
		lKeys.push(lTagName);		
	}
	for( var lTagName in lKeys.sort() ){				
		this.buildNodeNameMenuLink( lKeys[lTagName] );
	}
}

TextHolder.prototype.buildNodeNameMenuLink = function(pNodeName, pFollowingDiv){
	var lColorCode = this.nodeNames[pNodeName];
	var lDiv = false;
	if( !pFollowingDiv ){
		lDiv = this.divTagNameMenu.appendChild(document.createElement('div'));
	}else{
		lDiv = this.divTagNameMenu.insertBefore(document.createElement('div'), pFollowingDiv);
	}
	var lCheckBox = lDiv.appendChild(document.createElement('input'));
	var lColorBox = lDiv.appendChild(document.createElement('div'));
	var lLabel = lDiv.appendChild(document.createElement('div'));
	var lUnfloat = lDiv.appendChild(document.createElement('div'));
	var lCheckBoxSuffix = '_checkbox';
	var lDivSuffix = gNodeNameDivSuffix;
	lDiv.setAttribute('id', pNodeName + lDivSuffix);
	lCheckBox.name = pNodeName + lCheckBoxSuffix;
	lCheckBox.setAttribute('type', 'checkbox');
	lCheckBox.className = 'tagNameCheckbox';
	lColorBox.className = 'tagNameColorBox';
	lLabel.className = 'tagNameLabel';
	lColorBox.style.background = '#' + lColorCode;
	lUnfloat.className = 'unfloat';		
	lLabel.innerHTML = pNodeName;
	var lCssStyle = this.contentDocumentText.getElementById(gNodeNameStylePrefix + pNodeName);
	if( lCssStyle ){
		lCheckBox.checked = true;
	}
	this.addEvent(
		'click', 
		function( pEvent ){
			var lCheckBox = pEvent.target;
			var lTagName = lCheckBox.getAttribute('name');
			lTagName = lTagName.substring(0, lTagName.length - lCheckBoxSuffix.length);
			if( lCheckBox.checked ){
				
				
				var lCss = this.contentDocumentText.createElement('style');
				lCss.setAttribute('type', 'text/css');
				lCss.setAttribute('id',gNodeNameStylePrefix + lTagName);
				var lColour = this.nodeNames[lTagName];
				var lStyle = this.contentDocumentText.createTextNode('.node_' + TextHolder.parseNodeNameForCss(lTagName) + '{ background:#' + lColour + '!important;} .node_' + TextHolder.parseNodeNameForCss(lTagName) + ' div {background:inherit}');									
				if(lCss.styleSheet){
					lCss.styleSheet.cssText = lStyle.nodeValue;
				}else{
					lCss.appendChild(lStyle);
				}										
				var lHead = this.contentDocumentText.getElementsByTagName("head")[0];
				lHead.appendChild(lCss);				
			}else{				
				var lCss = this.contentDocumentText.getElementById(gNodeNameStylePrefix + pNodeName);				
				if( lCss ){
					lCss.parentNode.removeChild(lCss);
				}
			}
		}.bind(this), 
		lCheckBox
	);
}

TextHolder.prototype.addNodeName = function(pNodeName){
	if( !this.nodeNames[pNodeName] )
		this.nodeNames[pNodeName] = TextHolder.getNodeColour(pNodeName);	
	var lDiv = document.getElementById(pNodeName + gNodeNameDivSuffix);
	if( !lDiv ){
		var lSortedKeys = new Array();
		for( var i in this.nodeNames ){
			lSortedKeys.push(i);
		}
		lSortedKeys = lSortedKeys.sort();
		lIndex = this.findArrayIndex(pNodeName, lSortedKeys);
		var lNextDiv = false;
		if( lIndex != lSortedKeys ){
			var lNextName = lSortedKeys[lIndex + 1];
			lNextDiv = document.getElementById(lNextName + gNodeNameDivSuffix);
		}
		this.buildNodeNameMenuLink( pNodeName, lNextDiv );
	}
}

TextHolder.prototype.removeNodeName = function(pNodeName){
	delete this.nodeNames[pNodeName];
	var lDiv = document.getElementById(pNodeName + gNodeNameDivSuffix);
	if( lDiv ){
		lDiv.parentNode.removeChild(lDiv);
	}
	var lCss = this.contentDocumentText.getElementById(gNodeNameStylePrefix + pNodeName);				
	if( lCss ){
		lCss.parentNode.removeChild(lCss);
	}
}

TextHolder.prototype.saveXmlDocumentBase = function(pAction){
	if( this.mode == SOURCE_MODE ){
		if( !this.syncSourceXml() )
			return false;
	}
	var lCloneXml = EWorkaround.newDomDocument();
	if( !lCloneXml.loadXML(this.xmlDomDocument.xml) ){
		alert('Could not send xml!');
		return false;
	}
	
	var lDocType = gDocType;
	var lNodesWithAttributes = lCloneXml.selectNodes('//*[@' + gEtaAttributeName + ']');
	for( var i = 0; i < lNodesWithAttributes.length; ++i){
		lNodesWithAttributes[i].removeAttribute(gEtaAttributeName);
	}
	var lXml = lDocType + lCloneXml.xml;
	var lPostParams = {
		'id':this.documentId,
		'action':pAction,
		'xml': lXml
	};
	
	var lRes = this.EDispetcher.Call('', -2, '', '', '', gSrvAddress, false, null, null, null, lPostParams);
	return lRes;
}

//Запазва xml-a в базата чрез ajax заявка
TextHolder.prototype.saveXmlDocument = function(){
	var lRes = this.saveXmlDocumentBase(gSaveAction);
	if( parseInt(lRes) == this.documentId ){
		alert('Document saved successfully');
	}else{
		alert('Error while saving document');
		return false;
	}
	return true;
}

//Запазва xml-a в базата чрез ajax заявка
TextHolder.prototype.saveAndCreateNew = function(){
	var lRes = this.saveXmlDocumentBase(gSaveAndCreateNewAction);
	try{
		lRes = eval('(' + lRes + ')');
		var lOldId = lRes['oldId'];
		var lNewId = lRes['newId'];
		if( lOldId == this.documentId){
			alert('Document saved successfully');
			window.location.href = gMatchScriptAddress + lNewId;
		}
	}catch(e){
		alert('Error while saving document');
		return false;
	}
		
	return true;
}

TextHolder.prototype.removeEtaAttribute = function(pXMLDom){
	var lNodesWithAttributes = pXMLDom.selectNodes('//*[@' + gEtaAttributeName + ']');
	for( var i = 0; i < lNodesWithAttributes.length; ++i){
		lNodesWithAttributes[i].removeAttribute(gEtaAttributeName);
	}
}

TextHolder.prototype.removeAutotagNodes = function(pXMLDom){
	//TO DO - da se napravi da maha node-ovete, koito sme insertnali
	var lNodeToRemove = pXMLDom.selectSingleNode('//' + gAutotagMarkerNodeName);
	while( lNodeToRemove ){
		var lParent = lNodeToRemove.parentNode;
		for( var i = 0; i < lNodeToRemove.childNodes.length ; ++i ){
			var lCurrentChild = lNodeToRemove.childNodes[i];
			if( lCurrentChild.nodeType != gXmlElementNodeType && lCurrentChild.nodeType != gXmlTextNodeType )
				continue;
			lParent.insertBefore(lCurrentChild.cloneNode(true), lNodeToRemove );
			
		}
		lParent.removeChild(lNodeToRemove);
		lNodeToRemove = pXMLDom.selectSingleNode('//' + gAutotagMarkerNodeName);
	}
}

TextHolder.prototype.exportTags = function(){
	var lXmlDom = EWorkaround.newDomDocument();
	var lRoot = lXmlDom.appendChild(lXmlDom.createElement('tags'));
	for (var lTag in this.annotationTags ){
		var lTagNode = lXmlDom.createElement('tag');
		lTagNode.setAttribute('name', lTag);
		var lAttributes = this.annotationTags[lTag]['attributes'];
		if( lAttributes ){
			for( var i = 0; i < lAttributes.length; ++i ){
				var lAttributeName = lAttributes[i];
				var lAttrNode = lXmlDom.createElement('attribute');
				lAttrNode.setAttribute('name', lAttributeName);
				lTagNode.appendChild(lAttrNode);
			}
		}
		lRoot.appendChild(lTagNode);
	}
	
	
	
	var lPostParams = {		
		'xml': lXmlDom.xml
	};
	
	var lRes = this.EDispetcher.Call('', -2, '', '', '', '/resources/articles/importTags.php/', false, null, null, null, lPostParams);
	if( parseInt(lRes) == 1 ){
		alert('Tags exported successfully');
	}else{
		alert('Error while exporting tags');
		return false;
	}
	return true;
}

//Редиректва потребителя към страницата с листа на всички статии
//Откакто е попъп затваря попъпа и рефреш-ва прозореца от който е отворен попъпа
TextHolder.prototype.redirectUserToHome = function(){
	//window.location = gHomeUrl;	
	window.opener.location.reload();
	window.close();
}

//Запазва xml-а и редиректва потребителя към страницата с листа на всички статии
TextHolder.prototype.saveAndCloseXmlDocument = function(){
	if(this.saveXmlDocument()){
		this.redirectUserToHome();
	}
}

//Валидира xml-а спрямо dtd-то. Ще се прави с ajax заявка
TextHolder.prototype.validateXml = function(){
	var lCloneXml = EWorkaround.newDomDocument();
	this.showWaitingImage();
	if( !lCloneXml.loadXML(this.xmlDomDocument.xml) ){
		alert('Could not send xml!');
		this.hideWaitingImage();
		return false;
	}
	var lDocType = gDocType;
	var lNodesWithAttributes = lCloneXml.selectNodes('//*[@' + gEtaAttributeName + ']');
	for( var i = 0; i < lNodesWithAttributes.length; ++i){
		lNodesWithAttributes[i].removeAttribute(gEtaAttributeName);
	}
	
	var lXml = lDocType + lCloneXml.xml;//Слагаме доктайпа защото ДОМ парсера не си го пази.(Поне под Мозила);
	
	var lPostParams = {
		'id':this.documentId,
		'action':gValidateAction,
		'xml': lXml
	};
	
	var lRes = this.EDispetcher.Call('', -2, '', '', '', gSrvAddress, false, null, null, null, lPostParams);
	this.hideWaitingImage();
	alert(lRes);
	return true;
}

//Скрива менюто с имената на таговете
TextHolder.prototype.hideTagsMenu = function(){	
	if(!this.tagsAreHidden){
		this.tagsAreHidden = 1;
		this.divTagNameMenuHolder.style.display = 'none';
		document.getElementById('hideTagsButton').style.display = 'none';
		document.getElementById('showTagsButton').style.display = '';
		//this.divTextTable.style.width = this.divTextTable.clientWidth * 2 + 'px';
		//~ this.divTextTable.style.width = '80%';
		if( this.sourceIsHidden ){
			this.divTextTable.style.width = '90%';
		}else{
			this.divTextTable.style.width = '45%';
			this.divXmlTable.style.width = '45%';
		}
	}
}

//Показва менюто с имената на таговете
TextHolder.prototype.showTagsMenu = function(){
	if( this.tagsAreHidden ){
		this.tagsAreHidden = 0;				
		this.divTagNameMenuHolder.style.display = '';
		document.getElementById('showTagsButton').style.display = 'none';
		document.getElementById('hideTagsButton').style.display = '';
		//~ this.divTextTable.style.width = '40%';
		if( this.sourceIsHidden ){
			this.divTextTable.style.width = '80%';
		}else{
			this.divTextTable.style.width = '40%';
			this.divXmlTable.style.width = '40%';
		}
	}
}

//Скрива прозореца със сорса
TextHolder.prototype.hideXmlCode = function(){	
	if(!this.sourceIsHidden){
		this.sourceIsHidden = 1;
		this.divXmlTable.style.display = 'none';
		document.getElementById('hideXmlButton').style.display = 'none';
		document.getElementById('showXmlButton').style.display = '';
		//this.divTextTable.style.width = this.divTextTable.clientWidth * 2 + 'px';
		//~ this.divTextTable.style.width = '80%';
		if( this.tagsAreHidden ){
			this.divTextTable.style.width = '90%';
		}else{
			this.divTextTable.style.width = '80%';
		}
	}
}

//Показва прозореца със сорса - прегенерира го
TextHolder.prototype.showXmlCode = function(){
	if( this.sourceIsHidden ){
		this.sourceIsHidden = 0;
		this.contentDocumentXml.body.innerHTML = '';
		this.getXmlSource(this.xmlDomDocument.childNodes[0], this.contentDocumentXml.body);
		this.divXmlTable.style.display = '';
		document.getElementById('showXmlButton').style.display = 'none';
		document.getElementById('hideXmlButton').style.display = '';
		//~ this.divTextTable.style.width = '40%';
		this.divTextTable.style.width = '45%';
		if( this.tagsAreHidden ){
			this.divTextTable.style.width = '45%';
			this.divXmlTable.style.width = '45%';
		}else{
			this.divTextTable.style.width = '40%';
			this.divXmlTable.style.width = '40%';
		}
	}
}

TextHolder.prototype.Display = function(){
	this.createBoxes();
	this.buildMenu();
	this.buildFormattingMenu();
	this.buildTextRightClickMenu();
	this.setupEvents();
}

TextHolder.getNodeColour = function(pNodeName){//Връща цвета с който ще се оцветяват node-овете с това име
	var lHash = TextHolder.stringHash(pNodeName).toString();
	lHash = lHash.substr(0, 6);
	while(lHash.length < 6 ){
		lHash = lHash + '0';
	}
	return lHash;
}

TextHolder.stringHash = function(pString){
	var lHash = 0;
	for(var i = 0; i < pString.length; ++i ){
		lHash = pString.charCodeAt(i) + (lHash << 6) + (lHash << 16) - lHash;
	}
	return Math.abs(lHash);
}

TextHolder.parseNodeNameForCss = function( pNodeName ){
	return pNodeName.toLowerCase().replace(':', '__');
};

//Генерира текста за pNode xml node и го добавя в pHtmlNode node-а от текстовия прозорец
//В частност ако се пусне с root-a на xml-а и body-то на текстовия прозорец генерира целия текст
TextHolder.prototype.getXmlText = function(pNode, pHtmlNode){			
	var lShowNodeName = false;
	var lRes = '';	
		
				
	lRes = this.contentDocumentText.createElement('div');
	if( pNode.nodeName )//За да може да се дизайнира с цсс
		lRes.className = 'node_' + TextHolder.parseNodeNameForCss(pNode.nodeName);
	
	
	//Ако е позволено индексирането - добавя индекс на xml node-a и маркира node-а в текстовия прозорец
	if( this.setXmlIndexesAllowed && !pNode.getAttribute(gEtaAttributeName) ){
		pNode.setAttribute(gEtaAttributeName, this.currentXmlNodeIdx);
		this.xmlNodeHash[this.currentXmlNodeIdx] = pNode;		
		lRes.setAttribute('id', this.currentXmlNodeIdx++);
	}else{//Ако не е позволено индексирането или xml node-a вече има индекс - само маркира node-а в текстовия прозорец, че е към този xml node
		if(pNode.getAttribute(gEtaAttributeName)){
			var lIdx = pNode.getAttribute(gEtaAttributeName);
			if( this.setXmlIndexesAllowed )//Ако е позволено индексирането, но node-a си има индекс, само валидираме записа в таблицата с хешовете
				this.xmlNodeHash[lIdx] = pNode;
			lRes.setAttribute('id', lIdx);
		}
	}
	
	if(this.indexNodeNames && pNode.nodeName){//Индексираме имената на ноде-овете
		var lNodeName = TextHolder.trim(pNode.nodeName.toLowerCase());
		this.nodeNames[lNodeName] = TextHolder.getNodeColour(lNodeName);
	}
	
	lRes = pHtmlNode.appendChild(lRes);	
	this.getXmlTextSingleNode(pNode, lRes);
}

//Генерира текста за децата на pNode xml node и го добавя в pHtmlNode node-а от текстовия прозорец
TextHolder.prototype.getXmlTextSingleNode = function(pNode, pHtmlNode){
	for(var i = 0; i < pNode.childNodes.length; ++i){	
		var lChildNode = pNode.childNodes[i];				
		if( lChildNode.nodeType ==  gXmlTextNodeType){
			this.appendXmlTextContentToHtmlNode(pHtmlNode, lChildNode.nodeValue, this.contentDocumentText);
		}else if(lChildNode.nodeType ==  gXmlElementNodeType){			
			this.getXmlText(lChildNode, pHtmlNode);
		}
	}
}

TextHolder.prototype.appendXmlTextContentToHtmlNode = function(pHtmlNode, pTextContent, pIframeDocument){
	var lTextContent = pTextContent;			
	while(lTextContent){
		var lSymbolPos = lTextContent.indexOf(gNewLineSymbol);
		if( lSymbolPos >= 0 ){
			var lStartTextContent = lTextContent.substring(0, lSymbolPos);
			lTextContent = lTextContent.substring(lSymbolPos + gNewLineSymbol.length);
			if( lStartTextContent )
				pHtmlNode.appendChild(pIframeDocument.createTextNode(lStartTextContent));
			pHtmlNode.appendChild(pIframeDocument.createElement('br'));			
		}else{
			pHtmlNode.appendChild(pIframeDocument.createTextNode(lTextContent));			
			break;
		}
	}
}

//Генерира сорса за pNode xml node и го добавя в pHtmlNode node-а от прозореца на сорса
//В частност ако се пусне с root-a на xml-а и body-то на прозореца на сорса генерира целия сорс
TextHolder.prototype.getXmlSource = function(pNode, pHtmlNode){	
	var lRes = '';
	var lStart = '';
	var lEnd = '';
	var lWrapper = '';
	var lPlus = '';
	var lMinus = '';
	
	
	var lAttributes = '';
	for(var i = 0; pNode.attributes && i < pNode.attributes.length; ++i){
		var lAttribute = pNode.attributes.item(i);
		if( lAttribute.nodeName == gEtaAttributeName )
			continue;
		lAttributes = lAttributes + ' <span class="attributeName">' + lAttribute.nodeName + '</span>="<span class="attributeValue">' + lAttribute.nodeValue +'</span>"'; 
	}
	
	var lXmlIndex = pNode.getAttribute(gEtaAttributeName);	
	
	lWrapper = this.contentDocumentXml.createElement('div');
	lStart = this.contentDocumentXml.createElement('span');
	lEnd = this.contentDocumentXml.createElement('span');
	lPlus = this.contentDocumentXml.createElement('a');
	lMinus = this.contentDocumentXml.createElement('a');
	
	lWrapper.className = 'nodeContent';
	lPlus.innerHTML = '+';
	lMinus.innerHTML = '-';
	lStart.innerHTML = '&lt;<span class="nodeName">' + pNode.nodeName + '</span>' + lAttributes + '&gt;';
	lEnd.innerHTML = '&lt;/<span class="nodeName">' + pNode.nodeName + '</span>&gt;';
	
	lWrapper.style.margin = '0px 0px 0px 5px';	
	lPlus.style.display = 'none';
	
		
	lRes = this.contentDocumentXml.createElement('div');
	
	if( lXmlIndex ){//Ако xml node-а има индекс - слага event-ите за десния бутон в прозореца със сорса
		//~ this.addEvent('contextmenu', function(event){this.showSourceRightClickMenu(event, lXmlIndex);}.bind(this), lStart);		
		this.addEvent('contextmenu', function(event){this.showSourceRightClickMenu(event, lXmlIndex);}.bind(this), lWrapper);
		lWrapper.setAttribute('id', lXmlIndex);
		lStart.setAttribute('id', 'name_holder_start_' + lXmlIndex);
		lEnd.setAttribute('id', 'name_holder_end_' + lXmlIndex);
	}
	
	lRes.style.margin = '0px 0px 0px ' + 5 + 'px';	
	
	lPlus = lWrapper.appendChild(lPlus);
	lMinus = lWrapper.appendChild(lMinus);
	lStart = lWrapper.appendChild(lStart);
	lRes = lWrapper.appendChild(lRes);	
	
	lEnd = lWrapper.appendChild(lEnd);	
	lWrapper = pHtmlNode.appendChild(lWrapper);
	this.setXmlContentLinks(lPlus, lMinus, lRes);
	for(var i = 0; i < pNode.childNodes.length; ++i){		
		var lChildNode = pNode.childNodes[i];		
		if( lChildNode.nodeType ==  gXmlTextNodeType){			
			//~ lRes.appendChild(this.contentDocumentXml.createTextNode(lChildNode.nodeValue));			
			this.appendXmlTextContentToHtmlNode(lRes, lChildNode.nodeValue, this.contentDocumentXml);
		}else if(lChildNode.nodeType ==  gXmlElementNodeType){			
			this.getXmlSource(lChildNode, lRes);
		}
	}
}

//Добавя event-и на бутоните за скриване и показване на съдържанието на xml node-овете в прозореца със сорса
TextHolder.prototype.setXmlContentLinks = function(pPlus, pMinus, pContent){
	pMinus.onclick = function(){
		pPlus.style.display = "";
		pMinus.style.display = "none";
		pContent.style.display = "none";
	}
	pPlus.onclick = function(){
		pMinus.style.display = "";
		pPlus.style.display = "none";
		pContent.style.display = "";
	}
	return;
}

TextHolder.prototype.isBlockNode = function(pNodeName){	
	return false;
	for( var i = 0; i < this.blockElements.length; ++i){
		if( this.blockElements[i] == pNodeName )
			return true;
	}
	return false;
}

TextHolder.prototype.isNodeNameVisible = function(pNodeName){	
	if(this.visibleNodes[pNodeName] != undefined){		
		return true;
	}
	return false;
}

//Създава iframe-овете на 2-та прозореца
TextHolder.prototype.createBoxes = function(){
	
	
	this.iframeText = this.generateIframe(this.divText);
	this.iframeXml = this.generateIframe(this.divXml);
	this.iframeSourceTextarea = this.generateIframe(this.divTextArea);
		
	
	
	this.contentDocumentText = this.getIframeDocument(this.iframeText); 
	this.contentDocumentXml = this.getIframeDocument(this.iframeXml);
	this.contentDocumentSourceTextarea = this.getIframeDocument(this.iframeSourceTextarea);
	
	//Set iframe to be editable. Should be done before writing html and body tags because of IE bug.
	this.setIframeEditable(this.contentDocumentText);
	
	
	//Create html and body nodes
	this.writeIframeBodyTags(this.contentDocumentText);
	this.writeIframeBodyTags(this.contentDocumentXml);
	this.writeIframeBodyTags(this.contentDocumentSourceTextarea);
	
	this.addStyleSheet(this.contentDocumentXml, 'xmlSource.css');
	this.addStyleSheet(this.contentDocumentText, 'xmlText.css');
	this.addStyleSheet(this.contentDocumentSourceTextarea, 'xmlTextareaSource.css');
	
	//Слагаме текстареата и слагаме event-а за показване на менюто с таблиците и фигурите
	this.sourceTextArea = this.contentDocumentSourceTextarea.body.appendChild(this.contentDocumentSourceTextarea.createElement('textarea'));		
	this.addEvent('contextmenu', function(event){this.showSourceModeRightClickMenu(event);}.bind(this), this.sourceTextArea);
	this.addEvent("mousedown",function(){this.hideSourceModeRightClickMenu();}.bind(this),this.sourceTextArea);
	
	
	this.loadXmlDocument();
	
	
			
}

TextHolder.prototype.getIframeDocument = function(pIframe){
	var lIframeDocument = pIframe.contentWindow || pIframe.contentDocument;
	if(lIframeDocument && lIframeDocument.document) 
		lIframeDocument = lIframeDocument.document;
	return lIframeDocument;
}

//Добавя css файл pFileName в главата документ pIframeDocument
TextHolder.prototype.addStyleSheet = function(pIframeDocument, pFileName){
	var lHead = pIframeDocument.getElementsByTagName('head')[0];
	if( lHead ){
		var lLink = pIframeDocument.createElement('link');
		lLink.setAttribute('type', 'text/css');
		lLink.setAttribute('rel', 'stylesheet');
		lLink.setAttribute('media', 'all');
		lLink.setAttribute('title', 'default');
		lLink.setAttribute('href', '/lib/' + pFileName );
		lHead.appendChild(lLink);
	}
}

//Прави pIframeDocument editable iframe
TextHolder.prototype.setIframeEditable = function(pIframeDocument){
	pIframeDocument.designMode = "on";
	pIframeDocument.contentEditable = true;
}

//Пише началните тагове - html и body
TextHolder.prototype.writeIframeBodyTags = function(pIframeDocument){
	pIframeDocument.open();
	pIframeDocument.close();	
}

//Генерира iframe и го добавя като child на pHolder
TextHolder.prototype.generateIframe = function (pHolder){
	var lIframe = document.createElement("iframe");
	
	
	pHolder.appendChild(lIframe);
	lIframe.style.clear = "both";
	lIframe.style.width = "100%";	
	lIframe.style.height = "100%";	
	lIframe.style.background = "white";
	lIframe.style.border = "0px solid white";
	lIframe.style.display = "";
	lIframe.style.visibility = "";	
	return lIframe;
}

//Построява дясното меню
TextHolder.prototype.buildMenu = function(){
	this.divMenu.innerHTML = '';	
	for( var lButtonName in this.menuButtons ){
		var lElement = this.menuButtons[lButtonName];
		var lModes = lElement['display_in_modes'];
		
		if( !this.in_array(lModes, this.mode) )//Слагаме само менютата от нашия моде
			continue;

		var lButton = document.createElement('a');
		for( var lAttribName in lElement['attributes'] ){
			lButton.setAttribute(lAttribName, lElement['attributes'][lAttribName]);
		}
		lButton.innerHTML = lButtonName;
		lButton.className = 'menuButton';
		lButton.onclick = lElement['callback'].bind(this);
		this.divMenu.appendChild(lButton);
	}
}

TextHolder.prototype.buildFormattingMenu = function(){
	for( var lButtonName in this.formattingButtons ){
		var lElement = this.formattingButtons[lButtonName];
		var lButton = document.createElement('a');
		for( var lAttribName in lElement['attributes'] ){
			lButton.setAttribute(lAttribName, lElement['attributes'][lAttribName]);
		}
		lButton.innerHTML = lButtonName;
		lButton.className = 'menuButton menuButton' + lButtonName;
		lButton.onclick = lElement['callback'].bind(this);
		this.divFormattingMenu.appendChild(lButton);
	}
}


//Построява менюто, което се показва при слагане на фигура / таблица от фалшивия възел на правилното място в xml-а
TextHolder.prototype.buildFiguresSubMenu = function(){		
	this.divTextRightClickSubmenu.innerHTML = '';
	var lHasFiguresOrTables = false;
	var lFigures = this.xmlDomDocument.selectNodes('//' + gFakeFiguresTagName + '/' + gFigureTagName);
	for(var i = 0; i < lFigures.length; ++i){
		var lFigure = lFigures[i];
		var lFigId = lFigure.getAttribute('id');
		if( !lFigId )
			continue;
		lHasFiguresOrTables = true;
		var lButton = document.createElement('a');
		lButton.innerHTML = 'Figure #' + lFigId;
		var lCallbackFunction = function(pFigId){
			return function(){
				this.hideTextRightClickMenu();
				this.hideFugiresSubMenu();
				this.FigInsert(pFigId);
			}.bind(this);
		}.bind(this);		
		this.addEvent('click', lCallbackFunction(lFigId), lButton);
		//lButton.onclick = lElement['callback'].bind(this);
		this.divTextRightClickSubmenu.appendChild(lButton);
	}
	
	var lTables = this.xmlDomDocument.selectNodes('//' + gFakeFiguresTagName + '/' + gTableWrapTagName + '/' + gTableTagName);
	for(var i = 0; i < lTables.length; ++i){
		var lTable = lTables[i];
		var lTableId = lTable.getAttribute('id');
		if( !lTableId )
			continue;
		lHasFiguresOrTables = true;
		var lButton = document.createElement('a');
		lButton.innerHTML = 'Table #' + lTableId;
		var lCallbackFunction = function(pFigId){			
			return function(){
				this.hideTextRightClickMenu();
				this.hideFugiresSubMenu();
				this.TableInsert(lTableId);
			}.bind(this);
		}.bind(this);		
		this.addEvent('click', lCallbackFunction(lFigId), lButton);
		this.divTextRightClickSubmenu.appendChild(lButton);
	}
	if( !lHasFiguresOrTables ){
		this.divTextRightClickSubmenu.innerHTML = 'No figures or tables found';
	}
	
	this.addEvent("contextmenu",function(event){this.disableEvent(event)}.bind(this),this.divTextRightClickSubmenu);
}


//Построява менюто, което се показва при натискане на десен бутон на мишката в прозореца с текста
TextHolder.prototype.buildTextRightClickMenu = function(){	
	for( var lButtonName in this.textRightClickButtons){
		var lElement = this.textRightClickButtons[lButtonName];
		var lButton = document.createElement('a');		
		lButton.innerHTML = lButtonName;
		this.addEvent('click', lElement['callback'].bind(this), lButton);
		if( lElement['onmouseover'] )
			this.addEvent('mouseover', lElement['onmouseover'].bind(this), lButton);
		if( lElement['onmouseout'] )
			this.addEvent('mouseout', lElement['onmouseout'].bind(this), lButton);		
		this.divTextRightClick.appendChild(lButton);
	}	
	this.addEvent("contextmenu",function(event){this.disableEvent(event)}.bind(this),this.divTextRightClick);
}

//Построява менюто, което се показва при натискане на десен бутон на мишката в прозореца със сорса
TextHolder.prototype.buildSourceRightClickMenu = function(pEvent, pNodeIdx){	
	this.disableEvent(pEvent);
	this.divSourceRightClick.innerHTML = '';
	this.nodeEditIdx = pNodeIdx;
	for( var lButtonName in this.sourceRightClickButtons){
		var lElement = this.sourceRightClickButtons[lButtonName];
		
		var lButton = document.createElement('a');		
		lButton.innerHTML = lButtonName;
		//~ var lTemp = this;
		//lButton.onclick = function(){lElement['callback'](pNodeIdx).bind(lTemp)};
		this.addEvent('click', lElement['callback'].bind(this), lButton);
		this.divSourceRightClick.appendChild(lButton);
	}
	
	this.addEvent("contextmenu",function(event){this.disableEvent(event)}.bind(this),this.divSourceRightClick);
}

/**
	Построява менюто, което се показва при натискане на десен бутон на мишката в текстареата със сорса
	Ако xml-a e невалиден и не можем да го load-нем в Dom Document вадим грешка и спираме показването на менюто
*/
TextHolder.prototype.buildSourceModeRightClickMenu = function(pEvent){	
	this.disableEvent(pEvent);
	if( !this.loadSourceTempDocument()){		
		return false;
	}
	this.divSourceModeRightClick.innerHTML = '';	
	
	var lHasFiguresOrTables = false;
	var lFigures = this.sourceTempXmlDocument.selectNodes('//' + gFakeFiguresTagName + '/' + gFigureTagName);
	for(var i = 0; i < lFigures.length; ++i){
		var lFigure = lFigures[i];
		var lFigId = lFigure.getAttribute('id');
		if( !lFigId )
			continue;
		lHasFiguresOrTables = true;
		var lButton = document.createElement('a');
		lButton.innerHTML = 'Figure #' + lFigId;
		var lCallbackFunction = function(pFigId){
			return function(){
				this.hideSourceModeRightClickMenu();				
				this.FigInsert(pFigId);
			}.bind(this);
		}.bind(this);		
		this.addEvent('click', lCallbackFunction(lFigId), lButton);
		//lButton.onclick = lElement['callback'].bind(this);
		this.divSourceModeRightClick.appendChild(lButton);
	}
	
	var lTables = this.sourceTempXmlDocument.selectNodes('//' + gFakeFiguresTagName + '/' + gTableWrapTagName + '/' + gTableTagName);
	for(var i = 0; i < lTables.length; ++i){
		var lTable = lTables[i];
		var lTableId = lTable.getAttribute('id');
		if( !lTableId )
			continue;
		lHasFiguresOrTables = true;
		var lButton = document.createElement('a');
		lButton.innerHTML = 'Table #' + lTableId;
		var lCallbackFunction = function(pTableId){			
			return function(){
				this.hideSourceModeRightClickMenu();				
				this.TableInsert(pTableId);
			}.bind(this);
		}.bind(this);		
		this.addEvent('click', lCallbackFunction(lTableId), lButton);
		this.divSourceModeRightClick.appendChild(lButton);
	}
	if( !lHasFiguresOrTables ){
		this.divSourceModeRightClick.innerHTML = 'No figures or tables found';
	}
	
	
	
	
	this.addEvent("contextmenu",function(event){this.disableEvent(event)}.bind(this),this.divSourceModeRightClick);
	return true;
}

//Построява менюто, което се показва при натискане на десен бутон на мишката в прозореца autotag match-ове
TextHolder.prototype.buildАutotagRightClickMenu = function(pEvent, pMatchPart, pMatchNum){		
	this.divAutotagRightClick.innerHTML = '';	
	//~ var lTagArr = ['aritcle', 'article-title', 'tp:taxon-name'];
	for( var i = 0; i < this.AutotagAnnotateTags.length; ++i){
		var lTagName = this.AutotagAnnotateTags[i];
		
		var lButton = document.createElement('a');
		lButton.setAttribute('tag_name', lTagName);
		lButton.innerHTML = lTagName;
		this.addEvent(
			'click', 
			function(event){
				this.autotagAnnotate(event, pMatchNum, pMatchPart)
			}.bind(this), 
			lButton
		);
		this.divAutotagRightClick.appendChild(lButton);
	}
	
	this.addEvent("contextmenu",function(event){this.disableEvent(event)}.bind(this),this.divAutotagRightClick);
}

//Добавя callback на обекта object за събитието event
TextHolder.prototype.addEvent = function(event, callback, object){
	if(typeof object == "undefined")
		object = this.contentDocumentText;
		
	if(object.addEventListener)
		object.addEventListener(event, callback, false);
	else if(object.attachEvent)
		object.attachEvent("on"+event, callback);
	else
		return false;
	
	return true;
}
//Маха callback на обекта object за събитието event
TextHolder.prototype.removeEvent = function(event, callback, object){
	if(typeof object == "undefined")
		object = this.contentDocumentText;
		
	if(object.removeEventListener)
		object.removeEventListener(event);
	else if(object.detachEvent)
		object.detachEvent("on"+event, callback);
	else
		return false;
	
	return true;
}

TextHolder.trim = function(str) {
	var	str = str.replace(/^[\s\xA0][\s\xA0]*/, ''),
		ws = /[\s\xA0]/,
		i = str.length;
	while (ws.test(str.charAt(--i)));
	return str.slice(0, i + 1);
}

TextHolder.prototype.in_array = function(pArray, pValue){
	for (var i = 0; i < pArray.length; ++i){
		if( pArray[i] == pValue )
			return true;
	}
	return false;
}

TextHolder.prototype.findArrayIndex = function( pValue, pArray){
	for (var i = 0; i < pArray.length; ++i){
		if( pArray[i] == pValue )
			return i;
	}
	return -1;
}

TextHolder.prototype.remove_array_value = function(pArray, pValue){	
	var i = 0;
	while (i < pArray.length) {
		if (pArray[i] == pValue) {
			pArray.splice(i, 1);
		} else {
			i++;
		}
	}
	return pArray;
}
//Връша масив с всички родители на pNode
TextHolder.prototype.getNodeParents = function(pNode){
	var lResult = new Array();
	var lNode = pNode.parentNode;
	while(lNode){
		lResult.push(lNode);
		lNode = lNode.parentNode;
	}
	return lResult;
}

//Връща 1-я общ родител на pNodeA и pNodeB
TextHolder.prototype.getFirstCommonParent = function(pNodeA, pNodeB){
	var lParentsA = this.getNodeParents(pNodeA);
	var lParentsB = this.getNodeParents(pNodeB);
	if( this.in_array(lParentsA, pNodeB ))
		return pNodeB;
	for(var i = 0; i < lParentsA.length; ++ i){		
		if( this.in_array(lParentsB, lParentsA[i]))
			return lParentsA[i];
	}
}

//Връща кода на клавиша, който е натиснат при събитието  pEvent
TextHolder.prototype.getKeyStrokeCode = function(pEvent){	
	return pEvent.keyCode;
}

//Проверява дали натиснатия клавиш е важен - т.е. дали е променил текста за да се ъпдейтне xml-a и сорса
TextHolder.prototype.checkImportantKeyStroke = function(pEvent){
	var lKeyCode = this.getKeyStrokeCode(pEvent);		
	if( !lKeyCode )//Кирилица или др layout на клавиатурата
		return false;
	if( pEvent.altKey || pEvent.metaKey ){//Osven klavisha e natisnat alt ili winkey
		return false;
	}
	if( !pEvent.ctrlKey ){
		var lAKeyKode = 65, lZKeyCode = 90, l0KeyCode = 48, l9KeyCode = 57;
		var lDelKey = 46;
		var lBackspaceKey = 8;
		var lSymbolKeys = new Array(32, 13, 9, lBackspaceKey, 186, 187, 188, 189, 190, 191, 192, 219, 220, 221, 222);//Space Enter Tab Backspace; = , - . / ` [, \ ] '
		var lNumpadSymbolKeys = new Array(107, 109, 106, 111, 13)//+ - * / Enter
		var lNumpadNumKeys = new Array(110, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 45, lDelKey)//. 0 1 2 3 4 5 6 7 8 9 Ins Del
		
		if( lKeyCode == lBackspaceKey ){			
			return !this.checkDeleteNode(pEvent, 0);//checkDeleteNode returns whether the next/previous node has to been modified and marks it
		}
		if( lKeyCode == lDelKey ){			
			return !this.checkDeleteNode(pEvent, 1);//checkDeleteNode returns whether the next/previous node has to been modified and marks it
		}		
		if( lKeyCode >= lAKeyKode && lKeyCode <= lZKeyCode )//A-z
			return true;
		if( lKeyCode >= l0KeyCode && lKeyCode <= l9KeyCode )//0-9
			return true;
		if( this.in_array(lSymbolKeys, lKeyCode))//Special
			return true;
		if( this.in_array(lNumpadSymbolKeys, lKeyCode))//Numpad special
			return true;
		if( this.in_array(lNumpadNumKeys, lKeyCode))//Numpad number
			return true;
	}else{
		//Prevent copy/paste/undo if Ctrl + Key is inputted
		this.disableEvent(pEvent);
	}
	return false;
}

//Ако текста е променен отбелязва мястото където се е случила промяната
TextHolder.prototype.onKeyStroke = function(pEvent){		
	if( !this.checkImportantKeyStroke(pEvent) ){		
		return;
	}
	this.setCorrectSelection();
	this.markXmlNodesToChange(pEvent);
	return;
}

TextHolder.prototype.setCorrectSelection = function(){
	var lSelection = new Selection(this.contentDocumentText);
	var lStartNode = lSelection.getAnchorNode();
	if( lStartNode.parentNode == this.contentDocumentText.documentElement ){
		var lRoot = this.xmlDomDocument.documentElement;
		var lRootIndex = lRoot.getAttribute(gEtaAttributeName);
		var lStartHtmlNode = this.contentDocumentText.getElementById(lRootIndex);
		if( !lStartHtmlNode )
			return;
		if( lStartNode.previousSibling ){//mestim do nai otzad						
			lSelection.setStartPoint(lStartHtmlNode,0);
		}else{			
			lSelection.setStartPoint(lStartHtmlNode,lRoot.textContent.length);
		}
	}
}

//Спира default-ното поведение на събитието pEvent
TextHolder.prototype.disableEvent = function(pEvent){
	if( pEvent.preventDefault ){
		pEvent.preventDefault();
	}
	if( pEvent.stopPropagation ){
		pEvent.stopPropagation();
	}
}

//Показва менюто за вмъкване на таблица / фигура
TextHolder.prototype.displayFugiresSubMenu = function(pEvent){
	clearTimeout(this.FiguresSubMenuTimeout);
	if( !this.FiguresSubmenuIsDisplayed ){
		var lMousePos = this.getMousePosition(pEvent);
		//~ console.log(this.getScrollXY());
		var lX = parseInt(this.divTextRightClickHolder.style.left) + gRightClickMenuWidth - 20;
		var lY = parseInt(this.divTextRightClickHolder.style.top) + 50;
		var lXMax = lMousePos['xMax'];
		var lYMax = lMousePos['yMax'];	
		
		if( lX + gRightClickMenuWidth <= lXMax ){
			this.divTextRightClickSubmenuHolder.style.left = lX + 'px';
		}else{
			this.divTextRightClickSubmenuHolder.style.left = lX - 2 * gRightClickMenuWidth + 10 + 'px';
		}
		this.divTextRightClickSubmenuHolder.style.top = lY + 'px';
		//~ console.log('X:' + lX + ' Y:' + lY + ' XMax:' + lXMax + ' YMax:' + lYMax);
		this.divTextRightClickSubmenuHolder.style.display = 'block';
		
		this.divTextRightClickSubmenuHolder.style.display = 'block';		
	}
	this.gFiguresSubmenuIsDisplayed = 1;	
	this.disableEvent(pEvent);	
}

//Показва менюто, при натискане на десен бутон в прозореца с текста
TextHolder.prototype.showTextRightClickMenu = function(pEvent){
	this.hideSourceRightClickMenu();
	var lMousePos = this.getMousePosition(pEvent);
	//~ console.log(this.getScrollXY());
	var lX = lMousePos['x'];
	var lY = lMousePos['y'];
	var lXMax = lMousePos['xMax'];
	var lYMax = lMousePos['yMax'];	
	
	if( lX + gRightClickMenuWidth <= lXMax ){
		this.divTextRightClickHolder.style.left = lX + 'px';
	}else{
		this.divTextRightClickHolder.style.left = lX - gRightClickMenuWidth + 'px';
	}
	if( lY + gRightClickMenuHeight <= lYMax ){
		this.divTextRightClickHolder.style.top = lY + 'px';
	}else{
		var lTop = lY - gRightClickMenuHeight;
		if( lTop < 0 )
			lTop = 0;
		this.divTextRightClickHolder.style.top = lTop + 'px';
	}
	//~ console.log('X:' + lX + ' Y:' + lY + ' XMax:' + lXMax + ' YMax:' + lYMax);
	this.divTextRightClickHolder.style.display = 'block';
	this.disableEvent(pEvent);

}
//Показва менюто, при натискане на десен бутон в прозореца със сорса
TextHolder.prototype.showSourceRightClickMenu = function(pEvent, pNodeIdx){
	this.hideTextRightClickMenu();
	this.buildSourceRightClickMenu(pEvent, pNodeIdx);
	var lMousePos = this.getMousePosition(pEvent);
	var lX = lMousePos['x'];
	var lY = lMousePos['y'];
	var lXMax = lMousePos['xMax'];
	var lYMax = lMousePos['yMax'];	
	if( lX + gRightClickMenuWidth <= lXMax ){
		this.divSourceRightClickHolder.style.left = lX + 'px';
	}else{
		this.divSourceRightClickHolder.style.left = lX - gRightClickMenuWidth + 'px';
	}
	
	if( lY + gRightClickMenuHeight <= lYMax ){
		this.divSourceRightClickHolder.style.top = lY + 'px';
	}else{		
		var lTop = lY - gRightClickMenuHeight;
		if( lTop < 0 )
			lTop = 0;
		this.divSourceRightClickHolder.style.top = lTop + 'px';
	}
	this.divSourceRightClickHolder.style.display = 'block';	

}

//Показва менюто, при натискане на десен бутон в текстареата със сорса
TextHolder.prototype.showSourceModeRightClickMenu = function(pEvent, pNodeIdx){	
	if( !this.buildSourceModeRightClickMenu(pEvent) )
		return;
	var lMousePos = this.getMousePosition(pEvent);
	var lX = lMousePos['x'];
	var lY = lMousePos['y'];
	var lXMax = lMousePos['xMax'];
	var lYMax = lMousePos['yMax'];	
	
	//Pokazvame go za da mu vzemem visochinata no go kriem s visibility hidden
	this.divSourceModeRightClickHolder.style.visibility = 'hidden';
	this.divSourceModeRightClickHolder.style.display = 'block';
	
	
	//~ console.log(lY, this.divSourceModeRightClick.offsetHeight, lY + this.divSourceModeRightClick.offsetHeight, lYMax);
	if( lX + gRightClickMenuWidth <= lXMax ){
		this.divSourceModeRightClickHolder.style.left = lX + 'px';
	}else{
		this.divSourceModeRightClickHolder.style.left = lX - gRightClickMenuWidth + 'px';
	}
	
	if( lY + this.divSourceModeRightClick.offsetHeight <= lYMax ){
		this.divSourceModeRightClickHolder.style.top = lY + 'px';
	}else{		
		var lTop = lY - this.divSourceModeRightClick.offsetHeight;
		if( lTop < 0 )
			lTop = 0;
		this.divSourceModeRightClickHolder.style.top = lTop + 'px';
	}
	
	this.divSourceModeRightClickHolder.style.visibility = '';		
}

//Показва менюто, при натискане на десен бутон върху част от аутотаг матч
TextHolder.prototype.displayAutotagRightClickMenu = function(pEvent, pMatchNum){
	this.disableEvent(pEvent);
	if( !this.AutotagAnnotateTags.length ){
		alert('No autotag annotate tags specified!');
		return false;
	}
	var lEventTarget = TextHolder.getEventTarget(pEvent);
	var lMatchPart = lEventTarget.getAttribute('match_part');	
	this.buildАutotagRightClickMenu(pEvent, lMatchPart, pMatchNum);
	var lMousePos = this.getMousePosition(pEvent);
	var lX = lMousePos['x'];
	var lY = lMousePos['y'];
	var lXMax = lMousePos['xMax'];
	var lYMax = lMousePos['yMax'];	
	var gRightClickMenuHeight = 100;
	if( lX + gRightClickMenuWidth <= lXMax ){
		this.divAutotagRightClickHolder.style.left = lX + 'px';
	}else{
		this.divAutotagRightClickHolder.style.left = lX - gRightClickMenuWidth + 'px';
	}
	
	if( lY + gRightClickMenuHeight <= lYMax ){
		this.divAutotagRightClickHolder.style.top = lY + 'px';
	}else{		
		var lTop = lY - gRightClickMenuHeight;
		if( lTop < 0 )
			lTop = 0;
		this.divAutotagRightClickHolder.style.top = lTop + 'px';
	}
	this.divAutotagRightClickHolder.style.display = 'block';	

}

//Крие подменюто за вмъкване на таблица / фигура
TextHolder.prototype.hideFugiresSubMenu = function(){
	this.FiguresSubmenuIsDisplayed = false;
	this.divTextRightClickSubmenuHolder.style.display = 'none';	
}

//Крие менюто, показано при натискане на десен бутон в прозореца с текста
TextHolder.prototype.hideTextRightClickMenu = function(){	
	this.divTextRightClickHolder.style.display = 'none';	
}
//Крие менюто, показано при натискане на десен бутон в прозореца със сорса
TextHolder.prototype.hideSourceRightClickMenu = function(){	
	this.divSourceRightClickHolder.style.display = 'none';	
}

//Крие менюто, показано при натискане на десен бутон в текстареата със сорса
TextHolder.prototype.hideSourceModeRightClickMenu = function(){	
	this.divSourceModeRightClickHolder.style.display = 'none';	
}


//Крие менюто, показано при натискане на десен бутон в прозореца със autotag match-овете
TextHolder.prototype.hideAutotagRightClickMenu = function(){	
	this.divAutotagRightClickHolder.style.display = 'none';	
}

//Добавя event-и за писане с клавиатурата и показване на менютата при десни бутони
TextHolder.prototype.setupEvents = function(){
	this.addEvent("keydown",function(event){this.onKeyStroke(event)}.bind(this),this.contentDocumentText);
	this.addEvent("contextmenu",function(event){this.showTextRightClickMenu(event)}.bind(this),this.contentDocumentText);	
	this.addEvent("mousedown",function(){this.hideTextRightClickMenu();this.hideSourceRightClickMenu();}.bind(this),this.contentDocumentText);
	this.addEvent("mousedown",function(){this.hideTextRightClickMenu();this.hideSourceRightClickMenu();}.bind(this),this.contentDocumentXml);	
	this.addEvent("contextmenu",function(event){this.disableEvent(event)}.bind(this),this.contentDocumentXml);	
	//~ this.addEvent("keypress",function(event){this.onCharStroke(event)}.bind(this),this.contentDocumentText);
	
		
}


//Проверява дали се трие символ, когато сме в началото/края на текстов node и ако е така - маркира предходния/следващия node като променен за да се синхронизира коректно
TextHolder.prototype.checkDeleteNode = function(pEvent, pForwardDir){
	//Ako selectiona e prazen i usera e natisnal Del/Backspace i toi e syotvetno v kraq/na4aloto na daden element
	//Pita usera da potvyrdi iztrivaneto na sledvashtiq/predhodniq element
	//Pri pForwardDir = 1 - natisnat Del - trie se sledvashtiq node, inache predhodniq
	var lSelection = new Selection(this.contentDocumentText);		
	if( !lSelection.isEmpty())
		return false;	
	var lSelectionStartNode = lSelection.getAnchorNode();
	var lSelectionStartOffset = lSelection.getAnchorOffset();	
	var lNodeToChange = false;
	
	if( !pForwardDir ){//Backspace
		if( lSelectionStartOffset )
			return false;		
		lNodeToChange = this.getPreviousTextNode(lSelectionStartNode);				
	}else{//Del
		if( lSelectionStartOffset != lSelectionStartNode.textContent.length)
			return false;				
		lNodeToChange = this.getNextTextNode(lSelectionStartNode);				
	}
	if( lNodeToChange ){
		clearTimeout(this.changeTimeout);
		if( pForwardDir ){
			lSelection.setStartPoint(lNodeToChange, 0);
			lSelection.setEndPoint(lNodeToChange, 0);
		}else{
			lSelection.setStartPoint(lNodeToChange, lNodeToChange.textContent.length);
			lSelection.setEndPoint(lNodeToChange, lNodeToChange.textContent.length);
		}
		this.markRealXmlNodeToChange(lNodeToChange);
		this.changeTimeout = setTimeout(this.syncNodes.bind(this), 500);
	}	
	return true;
}

//Връща следващият текстов node след pNode
TextHolder.prototype.getNextTextNode = function(pNode){
	var lNextSibling = false;
	var lParent = pNode;
	while( lParent ){
		lNextSibling = lParent.nextSibling;
		while( lNextSibling ){
			if( lNextSibling.nodeType == gXmlTextNodeType )
				return lNextSibling;
			if( lNextSibling.nodeType == gXmlElementNodeType ){
				var lTextNode = this.getFirstTextNodeChild(lNextSibling);
				if( lTextNode )
					return lTextNode;
			}
			lNextSibling = lNextSibling.nextSibling;
		}
		lParent = lParent.parentNode;
	}
	return false;
	
}

//Връща предходният текстов node преди pNode
TextHolder.prototype.getPreviousTextNode = function(pNode){
	var lPreviousSibling = false;
	var lParent = pNode;
	while( lParent ){
		lPreviousSibling = lParent.previousSibling;
		while( lPreviousSibling ){
			if( lPreviousSibling.nodeType == gXmlTextNodeType )
				return lPreviousSibling;
			if( lPreviousSibling.nodeType == gXmlElementNodeType ){
				var lTextNode = this.getLastTextNodeChild(lPreviousSibling);
				if( lTextNode )
					return lTextNode;
			}
			lPreviousSibling = lPreviousSibling.previousSibling;
		}
		lParent = lParent.parentNode;
	}
	return false;
	
}
//Връща първият текстов node, който е наследник на pNode
TextHolder.prototype.getFirstTextNodeChild = function(pNode){
	for( var i = 0; i < pNode.childNodes.length; ++i){
		var lChild = pNode.childNodes[i];
		if( lChild.nodeType == gXmlTextNodeType )
			return lChild;
		if( lChild.nodeType == gXmlElementNodeType ){
			var lTextNode = this.getFirstTextNodeChild(lChild);
			if( lTextNode )
				return lTextNode;
		}
	}
	return false;
}
//Връща последният текстов node, който е наследник на pNode
TextHolder.prototype.getLastTextNodeChild = function(pNode){
	for( var i = pNode.childNodes.length - 1; i >= 0; --i){
		var lChild = pNode.childNodes[i];
		if( lChild.nodeType == gXmlTextNodeType )
			return lChild;
		if( lChild.nodeType == gXmlElementNodeType ){
			var lTextNode = this.getLastTextNodeChild(lChild);
			if( lTextNode )
				return lTextNode;
		}
	}
	return false;
}
//Маркира node-а, който се променя за да може после да се ъпдейтне xml-a и сорса
TextHolder.prototype.markXmlNodesToChange = function(){
	var lSelection = new Selection(this.contentDocumentText);
	var lSelStartNode = lSelection.getStartNode();
	var lSelEndNode = lSelection.getEndNode();
	
	clearTimeout(this.changeTimeout);
	
	if( lSelStartNode != lSelEndNode ){
		var lStartOffset = lSelection.getStartOffset();
		var lEndOffset = lSelection.getEndOffset();
		
		this.deleteXmlNodes(lSelStartNode, lStartOffset, lSelEndNode, lEndOffset);		
		this.markRealXmlNodeToChange(lSelStartNode);
		this.markRealXmlNodeToChange(lSelEndNode);
		
	}else{
		this.markRealXmlNodeToChange(lSelStartNode);
	}
	
	this.changeTimeout = setTimeout(this.syncNodes.bind(this), 500);
	return;
}

//Трие node-овете между pStartNode и pEndNode в текста, както и в самия xml
TextHolder.prototype.deleteXmlNodes = function(pStartNode, pStartOffset, pEndNode, pEndOffset){	
	this.deleteSelection(pStartNode, pEndNode);//Delete HTML nodes
	//delete XML nodes
	var lIterNode = pStartNode;
	while( lIterNode && lIterNode.nodeType != gXmlElementNodeType){
		lIterNode = lIterNode.nextSibling;
	}
	if( !lIterNode ){
		lIterNode = pStartNode.parentNode;
	}
	var lXmlStartNodeIdx = lIterNode.getAttribute('id');	
	var lXmlStartNode = this.xmlNodeHash[lXmlStartNodeIdx];
	if( !lXmlStartNodeIdx || !lXmlStartNode){
		alert('ERROR cannot find XML Selection start node');
		return;
	}
	lIterNode = pEndNode;
	while( lIterNode && lIterNode.nodeType != gXmlElementNodeType){
		lIterNode = lIterNode.previousSibling;
	}
	if( !lIterNode ){
		lIterNode = pEndNode.parentNode;
	}
	var lXmlEndNodeIdx = lIterNode.getAttribute('id');
	
	var lXmlEndNode = this.xmlNodeHash[lXmlEndNodeIdx];
	if( !lXmlEndNodeIdx || !lXmlEndNode){
		alert('ERROR cannot find XML Selection end node');
		return;
	}
	
	this.deleteSelection(lXmlStartNode, lXmlEndNode);//Delete XML nodes
	this.updateXmlCode(this.getFirstCommonParent(lXmlStartNode, lXmlEndNode), 1);
	return;
}


//Трие node-овете между pStartNode и pEndNode
TextHolder.prototype.deleteSelection = function(pStartNode, pEndNode){
	var lCommonParent = this.getFirstCommonParent(pStartNode, pEndNode);
	var lPreviousParentA = this.deleteFollowingNodes(pStartNode, lCommonParent);
	var lPreviousParentB = this.deletePreviousNodes(pEndNode, lCommonParent);
	var lCurrentSibling = lPreviousParentA.nextSibling;
	while(lCurrentSibling && lCurrentSibling != lPreviousParentB){
		var lNodeToDel = lCurrentSibling;		
		lCurrentSibling = lCurrentSibling.nextSibling;
		this.removeNodeFromParent(lCommonParent, lNodeToDel);
		//~ lCommonParent.removeChild(lNodeToDel);
	}
}

//Трие node-овете, които са след pStartNode и се качва нагоре по дървото до pCommonParent
TextHolder.prototype.deleteFollowingNodes = function(pStartNode, pCommonParent){
	var lCurrentNode = pStartNode;
	while(lCurrentNode.parentNode != pCommonParent ){
		var lCurrentSibling = lCurrentNode.nextSibling;
		lCurrentNode = lCurrentNode.parentNode;		
		while( lCurrentSibling ){			
			var lNodeToDelete = lCurrentSibling;
			lCurrentSibling = lCurrentSibling.nextSibling;
			//~ lCurrentNode.removeChild(lNodeToDelete);
			this.removeNodeFromParent(lCurrentNode, lNodeToDelete);
		}		
	}
	return lCurrentNode;
}

//Трие node-овете, които са преди pStartNode и се качва нагоре по дървото до pCommonParent
TextHolder.prototype.deletePreviousNodes = function(pStartNode, pCommonParent){
	var lCurrentNode = pStartNode;
	while(lCurrentNode.parentNode != pCommonParent ){
		var lCurrentSibling = lCurrentNode.previousSibling;
		lCurrentNode = lCurrentNode.parentNode;		
		while( lCurrentSibling ){			
			var lNodeToDelete = lCurrentSibling;
			lCurrentSibling = lCurrentSibling.previousSibling;
			//~ lCurrentNode.removeChild(lNodeToDelete);
			this.removeNodeFromParent(lCurrentNode, lNodeToDelete);
		}		
	}
	return lCurrentNode;
}


//Гледа дали има възли с това име и ако няма - маха ги от менюто
TextHolder.prototype.checkNodeNameForDeletion = function(pNodeName){
	if( !pNodeName )
		return;
	var lNode = this.xmlDomDocument.selectSingleNode('//' + pNodeName );
	if( !lNode ){//Tova e bil posledniq element s tva ime
		this.removeNodeName(pNodeName);
	}	
}

//Ако трием възел в xml-a обикаляме и децата му за техните имена
TextHolder.prototype.prepareXmlNodeForDeletion = function(pNode, pParent){
	if( pParent && pParent.ownerDocument == this.xmlDomDocument && pNode.nodeType == gXmlElementNodeType ){//Ako triem v xml-a trqbva da gledame i dali ne e posledniq node s dadeno ime za da go mahnem ot menuto		
		for( var i = 0; i < pNode.childNodes.length; ++i ){
			var lChild = pNode.childNodes[i];
			if( lChild.nodeType == gXmlElementNodeType ){
				this.checkNodeNameForDeletion( lChild.nodeName );
				this.prepareXmlNodeForDeletion( lChild, pParent );
			}
		}
		return pNode.nodeName;
	}
	return false;
}

//Трие pNode от pParent и ако трием възел от xml-а - грижим се за имената
TextHolder.prototype.removeNodeFromParent = function(pParent, pNode){	
	var lResult = pParent.removeChild(pNode);
	var lNodeName = this.prepareXmlNodeForDeletion(lResult, pParent);
	this.checkNodeNameForDeletion(lNodeName);
	return lResult;
}
//Заменя pOldNode с pNewNode в pParent и ако трием възел от xml-а - грижим се за имената
TextHolder.prototype.replaceParentChild = function(pParent, pNewNode, pOldNode){	
	var lResult = pParent.replaceChild(pNewNode, pOldNode);
	var lNodeName = this.prepareXmlNodeForDeletion(lResult, pParent);
	this.checkNodeNameForDeletion(lNodeName);
	return lResult;
}

//Маркира кой xml node трябва да се синхронизира
TextHolder.prototype.markRealXmlNodeToChange = function(pNode){
	if( pNode.nodeType == gXmlTextNodeType ){//TextNode
		pNode = pNode.parentNode;
	}
	var lXmlNodeEtaAttribute = pNode.getAttribute('id');
	if( !lXmlNodeEtaAttribute ){		
		return false;
	}
	if( !this.nodesToChange || !this.in_array(this.nodesToChange, lXmlNodeEtaAttribute)){
		this.nodesToChange.push(lXmlNodeEtaAttribute);
	}
}

//Синхронизира промените между текста и xml-a
TextHolder.prototype.syncNodes = function(){
	if( !this.updatingNodes ){
		this.updatingNodes = true;
		for( var i = 0; i < this.nodesToChange.length; ++i ){
			this.updateSingleXmlNode(this.nodesToChange[i]);
		}
		this.nodesToChange = new Array();		
		this.updatingNodes = false;
	}else{
		clearTimeout(this.changeTimeout);
		this.changeTimeout = setTimeout(this.syncNodes.bind(this), 1000);
	}
	return;
}

//Трие даден node от текста и после го маха и от xml-a и от сорса(маха и child-овете)
TextHolder.prototype.deleteSpecificNode = function(pHtmlNode){
	if( !pHtmlNode )
		return;
	var lIdx = pHtmlNode.getAttribute('id');
	if( !lIdx ){
		alert('ERROR - specific node has no id');
		return false;
	}
	var lHtmlParent = pHtmlNode.parentNode;
	var lXmlNode = this.xmlNodeHash[lIdx];
	var lXmlParent = lXmlNode.parentNode;
	if( lHtmlParent )
		lHtmlParent.removeChild(pHtmlNode);
	if( lXmlParent ){
		//~ lXmlParent.removeChild(lXmlNode);
		this.removeNodeFromParent(lXmlParent, lXmlNode);
	}
	if( !this.sourceIsHidden ){
		var lSourceNode = this.contentDocumentXml.getElementById(lIdx);
		var lSourceParent = lSourceNode.parentNode;
		if( lSourceParent ){
			lSourceParent.removeChild(lSourceNode);
		}
	}
}

//Ъпдейтва node с атрибут gEtaAttributeName pNodeIndex спрямо текста
//На практика прегенерира само текстовите node-ове, а другите ги копира
TextHolder.prototype.updateSingleXmlNode = function(pNodeIndex){
	var lRealNodeIndex = this.getUpdateNodeRealIndex(pNodeIndex);
	if( !lRealNodeIndex ){
		alert('ERROR - there is no suitable node to update');
		return false;
	}	
	var lXmlNode = this.xmlNodeHash[lRealNodeIndex];	
	var lTextNode = this.contentDocumentText.getElementById(lRealNodeIndex);
	
	if( !lXmlNode ){
		alert('ERROR -XML node with such eta attribute does not exist');
		return false;
	}
	var lXmlParent = lXmlNode.parentNode;
	if( lXmlParent ){
		var newClonedXmlNode = lXmlNode.cloneNode(false);
		var lChildNodeIter = lTextNode.firstChild;
		while( lChildNodeIter ){
			var lChildNode = lChildNodeIter;
			lChildNodeIter = lChildNodeIter.nextSibling;
			if( lChildNode.nodeType == gXmlTextNodeType ){
				newClonedXmlNode.appendChild(this.xmlDomDocument.createTextNode(lChildNode.nodeValue));
			}
			if( lChildNode.nodeType == gXmlElementNodeType ){
				if( lChildNode.nodeName.toLowerCase() == 'br' ){//Slojili sme nov red
					newClonedXmlNode.appendChild(this.xmlDomDocument.createTextNode(gNewLineSymbol));
				}else{
					var lChildIdx = lChildNode.getAttribute('id');
					if( !lChildIdx ){//Браузера е счупил html-a
						//alert('ERROR Text node has no eta attribute node');
						lTextNode.removeChild(lChildNode);
						continue;
					}
					
					var lChildXmlNode = this.xmlNodeHash[lChildIdx];	
					if( !lChildXmlNode ){
						alert('ERROR XML node with such eta attribute does not exist');
						continue;
					}
					
					this.xmlNodeAppendChild(newClonedXmlNode,this.xmlNodeDeepCopy(lChildXmlNode));				
				}
			}
		}		
		lXmlParent.replaceChild(newClonedXmlNode, lXmlNode);	
		this.xmlNodeHash[lRealNodeIndex] = newClonedXmlNode;		
		this.updateXmlCode(newClonedXmlNode, 1);
	}
	
}

//Взима индекса на xml node-а който трябва да се прегенерира - гледа ако е изтрит цял node - катери се по дървото нагоре
TextHolder.prototype.getUpdateNodeRealIndex = function(pNodeIndex){
	var lNodeIndex = pNodeIndex;
	//~ var lXmlNode = this.xmlDomDocument.selectSingleNode('//*[@' + gEtaAttributeName + '="' + pNodeIndex + '"]');
	var lXmlNode = this.xmlNodeHash[pNodeIndex];	
	
	var lFoundRealIndex = false;
	while(!lFoundRealIndex && lXmlNode){
		var lCurrentIndex = lXmlNode.getAttribute(gEtaAttributeName);
		//~ var lCurrentIndex = this.xmlNodeHashReverse[lXmlNode];
		if(!lCurrentIndex)
			return false;		
		if( this.contentDocumentText.getElementById(lCurrentIndex))
			return lCurrentIndex;
		lXmlNode = lXmlNode.parentNode;
	}
	return false;
}

//Ъпдейтва се сорса за pXmlNode. Ако се предаде pDeepCopy функцията се вика рекурсивно надолу по дървото - иначе само се копират
TextHolder.prototype.updateXmlCode = function(pXmlNode, pDeepCopy){	
	if(this.sourceIsHidden)
		return;
	var lIndex = pXmlNode.getAttribute(gEtaAttributeName);
	//~ var lIndex = this.xmlNodeHashReverse[pXmlNode];
	if( lIndex ){
		var lSourceNode = this.contentDocumentXml.getElementById(lIndex);
		if( lSourceNode ){			
			var lSourceClone = this.updateXmlCodeSingleNode(pXmlNode, pDeepCopy);
			var lSourceParent = lSourceNode.parentNode;
			if( lSourceParent ){
				lSourceParent.replaceChild(lSourceClone, lSourceNode);								
			}			
		}
	}
	
}

//Връша се сорса за pXmlNode. Ако се предаде pDeepCopy функцията се вика рекурсивно надолу по дървото - иначе само се копират
TextHolder.prototype.updateXmlCodeSingleNode = function(pXmlNode, pDeepCopy){	
	//Pri deepCopy funkciqta se izvikva rekursivno. Togava se i dobavqt event-ite, koito inache se gubqt. 
	var lRes = '';
	var lStart = '';
	var lEnd = '';
	var lWrapper = '';
	var lPlus = '';
	var lMinus = '';
	
	
	var lAttributes = '';
	for(var i = 0; pXmlNode.attributes && i < pXmlNode.attributes.length; ++i){
		var lAttribute = pXmlNode.attributes.item(i);
		if( lAttribute.nodeName == gEtaAttributeName )
			continue;
		lAttributes = lAttributes + ' <span class="attributeName">' + lAttribute.nodeName + '</span>="<span class="attributeValue">' + lAttribute.nodeValue +'</span>"'; 
	}
	
	var lXmlIndex = pXmlNode.getAttribute(gEtaAttributeName);	
	
	lWrapper = this.contentDocumentXml.createElement('div');
	lStart = this.contentDocumentXml.createElement('span');
	lEnd = this.contentDocumentXml.createElement('span');
	lPlus = this.contentDocumentXml.createElement('a');
	lMinus = this.contentDocumentXml.createElement('a');
	
	lWrapper.className = 'nodeContent';
	lPlus.innerHTML = '+';
	lMinus.innerHTML = '-';
	lStart.innerHTML = '&lt;<span class="nodeName">' + pXmlNode.nodeName + '</span>' + lAttributes + '&gt;';
	lEnd.innerHTML = '&lt;/<span class="nodeName">' + pXmlNode.nodeName + '</span>&gt;';
	
	lWrapper.style.margin = '0px 0px 0px 5px';	
	lPlus.style.display = 'none';
	
		
	lRes = this.contentDocumentXml.createElement('div');
	
	if( lXmlIndex ){
		this.addEvent('contextmenu', function(event){this.showSourceRightClickMenu(event, lXmlIndex);}.bind(this), lStart);
		lStart.setAttribute('id', 'name_holder_start_' + lXmlIndex);
		lEnd.setAttribute('id', 'name_holder_end_' + lXmlIndex);
		lWrapper.setAttribute('id', lXmlIndex);
	}
	
	lRes.style.margin = '0px 0px 0px ' + 5 + 'px';	
	
	lPlus = lWrapper.appendChild(lPlus);
	lMinus = lWrapper.appendChild(lMinus);
	lStart = lWrapper.appendChild(lStart);
	lRes = lWrapper.appendChild(lRes);	
	
	lEnd = lWrapper.appendChild(lEnd);	
	
	this.setXmlContentLinks(lPlus, lMinus, lRes);
	
	if( !pDeepCopy ){
		for(var i = 0; i < pXmlNode.childNodes.length; ++i){		
			var lChildNode = pXmlNode.childNodes[i];		
			if( lChildNode.nodeType ==  gXmlTextNodeType){					
				//~ lRes.appendChild(this.contentDocumentXml.createTextNode(lChildNode.nodeValue));			
				this.appendXmlTextContentToHtmlNode(lRes, lChildNode.nodeValue, this.contentDocumentXml);
			}else if(lChildNode.nodeType ==  gXmlElementNodeType){			
				var lChildIndex = lChildNode.getAttribute(gEtaAttributeName);				
				if( lChildIndex ){
					var lHtmlChild = this.contentDocumentXml.getElementById(lChildIndex);
					if( lHtmlChild ){
						lRes.appendChild(lHtmlChild.cloneNode(true));
					}else{
						lRes.appendChild(this.updateXmlCodeSingleNode(lChildNode, 0));
					}
				}			
			}
		}
	}else{
		for(var i = 0; i < pXmlNode.childNodes.length; ++i){		
			var lChildNode = pXmlNode.childNodes[i];		
			if( lChildNode.nodeType ==  gXmlTextNodeType){					
				//~ lRes.appendChild(this.contentDocumentXml.createTextNode(lChildNode.nodeValue));			
				this.appendXmlTextContentToHtmlNode(lRes, lChildNode.nodeValue, this.contentDocumentXml);
			}else if(lChildNode.nodeType ==  gXmlElementNodeType){			
				lRes.appendChild(this.updateXmlCodeSingleNode(lChildNode, 1));		
			}
		}
	}
	return lWrapper;
}

/**
	Позиционира ни при следващият цитат на фигура/таблица от тези, които все още не са позиционирани в статията
*/
TextHolder.prototype.positionAtNextFigTableCitation = function(){
	var lXmlDocument;
	if( this.mode == TEXT_MODE ){
		lXmlDocument = this.xmlDomDocument;
	}else if( this.mode == SOURCE_MODE ){
		if( !this.loadSourceTempDocument()){//Слагаме xml-а от текстареата за да работим с последната версия 
			alert('Wrong XML document');
			return false;
		}lXmlDocument = this.sourceTempXmlDocument;
	}
	if( !lXmlDocument ){
		alert('Wrong XML document');
		return false;
	}
	var lFigures = lXmlDocument.selectNodes('//' + gFakeFiguresTagName + '/' + gFigureTagName + '/@id');
	var lFigIds = new Array();
	var lExpression = '';
	var lItemsFound = false;
	for(var i = 0; i < lFigures.length; ++i){
		var lItemsFound = true;
		var lElement = lFigures[i];
		var lId = lElement.nodeValue;
		lFigIds.push(lId);
		if( lExpression != '' )
			lExpression += '|';
		lExpression += '//xref[@ref-type=\'fig\'][@rid=\'' + lId + '\']';		
	}
	
	var lTables = lXmlDocument.selectNodes('//' + gFakeFiguresTagName + '/' + gTableWrapTagName + '/' + gTableTagName + '/@id');	
	var lTableIds = new Array();
	for(var i = 0; i < lTables.length; ++i){
		var lItemsFound = true;
		var lElement = lTables[i];
		var lId = lElement.nodeValue;
		lTableIds.push(lId);
		if( lExpression != '' )
			lExpression += '|';
		lExpression += '//xref[@ref-type=\'table\'][@rid=\'' + lId + '\']';
	}
	if( !lItemsFound ){
		alert('No figures or tables found');
		return false;
	}
	var lNodes = lXmlDocument.selectNodes(lExpression);
	
	if( !lNodes.length ){
		alert('No citations');
		return;
	}
	if( this.mode == TEXT_MODE ){//Работим по текста
		var lSelection = new Selection(this.contentDocumentText);
		var lSelectionStartNode = lSelection.getStartNode();
		while(lSelectionStartNode && lSelectionStartNode.nodeType != gXmlElementNodeType )
			lSelectionStartNode = lSelectionStartNode.parentNode;
		
		for( var i = 0; i < lNodes.length; ++i ){
			var lNode = lNodes[i];
			var lNodeEtaIdx = lNode.getAttribute(gEtaAttributeName);
			if( !lNodeEtaIdx )
				continue;
			var gTextNode = this.contentDocumentText.getElementById(lNodeEtaIdx );
			if( !gTextNode )
				continue;
			
			if( !lSelectionStartNode || lSelectionStartNode.offsetTop < gTextNode.offsetTop || (lSelectionStartNode.offsetTop == gTextNode.offsetTop && lSelectionStartNode.offsetLeft < gTextNode.offsetLeft)){						
				this.scrollDiv(this.contentDocumentText, gTextNode, 0, 1, gTextNode.textContent.length, 1)			
				return true;
			}
			
		}		
	}else if( this.mode == SOURCE_MODE ){//Работим по текстареата със кода
		var lTextareaEnd = parseInt(getTextAreaSelectionEnd(this.sourceTextArea, this.contentDocumentSourceTextarea));
		if( lTextareaEnd == this.sourceTextArea.value.length )
			lTextareaEnd = 0;
		for( var i = 0; i < lNodes.length; ++i ){
			var lNode = lNodes[i];			
			/**
				Slagame atributa za da moje ako ima nqkolko ednakvi node-a da hvanem to4no tozi koito ni trqbva
			*/			
			lNode.setAttribute('eta_temp_search_attribute', 'eta_temp_search_attribute');			
			var lXml = lXmlDocument.xml;			
			var lPos = lXml.search(lNode.xml);
			lNode.removeAttribute('eta_temp_search_attribute');			
			if( lPos >= 0 && lPos >= lTextareaEnd ){
				SetAreaSelectionRangeAndScroll(this.sourceTextArea, this.contentDocumentSourceTextarea, lPos, lPos + lNode.xml.length);				
				return true;
			}
			
		}
	}
	alert('Could not find more following references');
	return false;
}

//Инициализира попъпа - изтрива се предходното му съдържание и се инициализира background-а
TextHolder.prototype.initPopup = function(){
	var screensize = this.getScreenSize();
	var scrollsize = this.getScrollXY();
	
	// tva e zaradi IE-to razbira se :)
	var docheight = parseInt(document.body.parentNode.offsetHeight);
	var bodyheight = parseInt(document.body.offsetHeight);
	bgheight = Math.max(Math.max(docheight, bodyheight), (screensize[1]+scrollsize[1]));
	
	//~ this.divBackground.style.height = parseInt(bgheight)+'px';
	this.divBackground.style.height = '100%';
	
	bgwidth = screensize[0]+scrollsize[0];
	//~ this.divBackground.style.width = parseInt(bgwidth)+'px';
	this.divBackground.style.width = '100%';
	
	
	this.divPopup.innerHTML = '';
}

//Показва се попъпа
TextHolder.prototype.displayPopup = function(){
	var screensize = this.getScreenSize();
	var scrollsize = this.getScrollXY();
	this.divPopupHolder.style.top = '200px';
	this.divBackground.style.display = 'block';
	this.divPopupHolder.style.display = 'block';	//Показва се див-а понеже иначе няма ширина
	this.divPopup.style.height = 'auto';
	if( this.divPopup.offsetHeight > 400 ){
		this.divPopup.style.height = '400px';
		this.divPopup.style.overflow = 'auto';
	}
	this.divPopupHolder.style.left = ((screensize[0]-this.divPopup.offsetWidth)/2+scrollsize[0])+'px';
	
	
	
}


//Крие попъпа
TextHolder.prototype.hidePopup = function(){
	this.divBackground.style.display = 'none';
	this.divPopupHolder.style.display = 'none';
}

TextHolder.prototype.showWaitingImage = function(){	
	this.initPopup();
	this.divPopup.innerHTML = '';
	var lImage = new Image(508, 381);
	lImage.src = '/img/loading_wh.gif';
	this.divPopup.appendChild(lImage);
	this.displayPopup();	
	
	return;	
}

TextHolder.prototype.hideWaitingImage = function(){	
	this.hidePopup();
	return;
}

//Строи попъп за тагване
TextHolder.prototype.buildAnnotatePopup = function(){	
	var lSelection = new Selection(this.contentDocumentText);
	if( lSelection.isEmpty()){
		alert('You haven\'t selected any text!');
		return false;
	}
	
	this.initPopup();
	
	
	var lSelect = document.createElement('select');
	
	for( var lTagName in this.annotationTags){
		var lElement = this.annotationTags[lTagName];
		var lOption = document.createElement('option');		
		lOption.innerHTML = lTagName;
		lOption.setAttribute('value', lTagName);		
		lSelect.appendChild(lOption);
	}	
	var lMainLabel = document.createElement('div');
	lMainLabel.className = 'label';
	lMainLabel.appendChild(document.createTextNode('Create annotation'));
	
	var lSelectLabel = document.createElement('span');	
	lSelectLabel.innerHTML = 'Annotation type';
	
	
	var lSelectHolderTable = document.createElement('table');
	var lSelectHolderTr = lSelectHolderTable.appendChild(document.createElement('tr'));
	var lLabelTd = lSelectHolderTr.appendChild(document.createElement('td'));
	var lSelectTd = lSelectHolderTr.appendChild(document.createElement('td'));
	lSelectLabel = lLabelTd.appendChild(lSelectLabel);
	lSelect = lSelectTd.appendChild(lSelect);
	
	lMainLabel = this.divPopup.appendChild(lMainLabel);
	this.divPopup.appendChild(lSelectHolderTable);
	//lSelectLabel = this.divPopup.appendChild(lSelectLabel);
	//lSelect = this.divPopup.appendChild(lSelect);
	
	var lCancelButton = document.createElement('input');	
	lCancelButton.setAttribute('type', 'submit');
	var lAnnotateButton = document.createElement('input');	
	lAnnotateButton.setAttribute('type', 'submit');
	
	lCancelButton.value = 'Cancel';
	lAnnotateButton.value = 'Annotate';
	lAnnotateButton.className = 'leftInput';
	
	var lButtonHolder = document.createElement('div');
	lButtonHolder.className = 'buttonHolder';
	lButtonHolder = this.divPopup.appendChild(lButtonHolder);
	
	lAnnotateButton = lButtonHolder.appendChild(lAnnotateButton);
	lCancelButton = lButtonHolder.appendChild(lCancelButton);
	
	
	
	this.addEvent('click', function(){this.annotateSelection(lSelect);}.bind(this), lAnnotateButton);
	this.addEvent('click', function(){this.hidePopup();}.bind(this), lCancelButton);
	
	this.displayPopup();	
}

TextHolder.prototype.showFiguresSubMenu = function(pEvent){
	if( !this.figuresMenuIsBuild ){
		this.buildFiguresSubMenu();
		this.figuresMenuIsBuild = 1;
	}
	this.displayFugiresSubMenu(pEvent);
}

//Строи попъп за избор на правила за аутотагване, което да се изпълни
TextHolder.prototype.buildAutotagRulesPopup = function(){	
	this.initPopup();
	
	
	var lSelect = document.createElement('select');
	var lPostParams = {'force_json_output' : 1};
	var lRes = this.EDispetcher.Call('', -2, '', '', '', gAutoTagRulesSrvAddress, false, null, null, null, lPostParams);
	var lRules = false;
	try{
		lRes = eval('(' + lRes + ')');
		lRules = lRes['res'][0]['mdata'];
	}catch(e){
		lRules = [];
	}
	
	if( !lRules.length){
		alert('No rules found');
		return;
	}
	
	for( var i = 0; i < lRules.length; ++i ){
		var lRule = lRules[i];
		var lOption = document.createElement('option');		
		lOption.innerHTML = lRule['name'];
		lOption.setAttribute('value', lRule['id']);		
		lSelect.appendChild(lOption);
	}	
	var lMainLabel = document.createElement('div');
	lMainLabel.className = 'label';
	lMainLabel.appendChild(document.createTextNode('Choose autotag rule'));
	
	var lSelectLabel = document.createElement('span');	
	lSelectLabel.innerHTML = 'Autotag rule';
	
	
	var lSelectHolderTable = document.createElement('table');
	var lSelectHolderTr = lSelectHolderTable.appendChild(document.createElement('tr'));
	var lLabelTd = lSelectHolderTr.appendChild(document.createElement('td'));
	var lSelectTd = lSelectHolderTr.appendChild(document.createElement('td'));
	lSelectLabel = lLabelTd.appendChild(lSelectLabel);
	lSelect = lSelectTd.appendChild(lSelect);
	
	lMainLabel = this.divPopup.appendChild(lMainLabel);
	this.divPopup.appendChild(lSelectHolderTable);
	//lSelectLabel = this.divPopup.appendChild(lSelectLabel);
	//lSelect = this.divPopup.appendChild(lSelect);
	
	var lCancelButton = document.createElement('input');	
	lCancelButton.setAttribute('type', 'submit');
	var lOkButton = document.createElement('input');	
	lOkButton.setAttribute('type', 'submit');
	
	var lOkWithoutCheckButton = document.createElement('input');	
	lOkWithoutCheckButton.setAttribute('type', 'submit');
	
	lCancelButton.value = 'Cancel';
	lOkButton.value = 'Autotag';
	lOkButton.className = 'leftInput';
	lOkWithoutCheckButton.value = 'Autotag without check';
	lOkWithoutCheckButton.className = 'leftInput';
	
	var lButtonHolder = document.createElement('div');
	lButtonHolder.className = 'buttonHolder';
	lButtonHolder = this.divPopup.appendChild(lButtonHolder);
	
	lOkButton = lButtonHolder.appendChild(lOkButton);
	lOkWithoutCheckButton = lButtonHolder.appendChild(lOkWithoutCheckButton);
	lCancelButton = lButtonHolder.appendChild(lCancelButton);
	
	
	
	this.addEvent('click', function(){this.hidePopup();this.autotagXML(lSelect.value, gInternalAutotagType);}.bind(this), lOkButton);
	this.addEvent('click', function(){this.hidePopup();this.autotagXML(lSelect.value, gInternalAutotagType, 1);}.bind(this), lOkWithoutCheckButton);
	this.addEvent('click', function(){this.hidePopup();}.bind(this), lCancelButton);
	
	this.displayPopup();	
}

//Строи попъп за избор на правила за аутотагване, което да се изпълни
TextHolder.prototype.buildAutoNumerateRulesPopup = function(){	
	this.initPopup();	
	
	var lSelect = document.createElement('select');
	var lPostParams = {'force_json_output' : 1};
	var lRes = this.EDispetcher.Call('', -2, '', '', '', gAutoNumerateRulesSrvAddress, false, null, null, null, lPostParams);
	var lRules = false;
	try{
		lRes = eval('(' + lRes + ')');
		lRules = lRes['res'][0]['mdata'];
	}catch(e){
		lRules = [];
	}
	
	if( !lRules.length){
		alert('No rules found');
		return;
	}
	
	for( var i = 0; i < lRules.length; ++i ){
		var lRule = lRules[i];
		var lOption = document.createElement('option');		
		lOption.innerHTML = lRule['name'];
		lOption.setAttribute('value', lRule['id']);		
		lSelect.appendChild(lOption);
	}	
	var lMainLabel = document.createElement('div');
	lMainLabel.className = 'label';
	lMainLabel.appendChild(document.createTextNode('Choose auto numerate rule'));
	
	var lSelectLabel = document.createElement('span');	
	lSelectLabel.innerHTML = 'Autonumerate rule';
	
	
	var lSelectHolderTable = document.createElement('table');
	var lSelectHolderTr = lSelectHolderTable.appendChild(document.createElement('tr'));
	var lLabelTd = lSelectHolderTr.appendChild(document.createElement('td'));
	var lSelectTd = lSelectHolderTr.appendChild(document.createElement('td'));
	lSelectLabel = lLabelTd.appendChild(lSelectLabel);
	lSelect = lSelectTd.appendChild(lSelect);
	
	lMainLabel = this.divPopup.appendChild(lMainLabel);
	this.divPopup.appendChild(lSelectHolderTable);
	//lSelectLabel = this.divPopup.appendChild(lSelectLabel);
	//lSelect = this.divPopup.appendChild(lSelect);
	
	var lCancelButton = document.createElement('input');	
	lCancelButton.setAttribute('type', 'submit');
	var lOkButton = document.createElement('input');	
	lOkButton.setAttribute('type', 'submit');
	
	lCancelButton.value = 'Cancel';
	lOkButton.value = 'Autonumerate';
	lOkButton.className = 'leftInput';
	
	var lButtonHolder = document.createElement('div');
	lButtonHolder.className = 'buttonHolder';
	lButtonHolder = this.divPopup.appendChild(lButtonHolder);
	
	lOkButton = lButtonHolder.appendChild(lOkButton);
	lCancelButton = lButtonHolder.appendChild(lCancelButton);
	
	
	
	this.addEvent(
		'click', function(){
			var lAutoNumerateRuleId = lSelect.value;
			this.hidePopup();
			var lXpath = '';
			var lAttribute = '';
			var lStartValue = 0;
			for( var i = 0; i < lRules.length; ++i ){
				var lRule = lRules[i];
				if( lRule['id'] == lAutoNumerateRuleId ){
					lXpath = lRule['xpath'];
					lStartValue = lRule['starting_value'];
					lAttribute = lRule['attribute_name'];
					this.autonumerateXML(lXpath, lAttribute, lStartValue);
					return;
				}
			}	
			alert('No such rule');
		}.bind(this), 
		lOkButton
	);
	this.addEvent('click', function(){this.hidePopup();}.bind(this), lCancelButton);
	
	this.displayPopup();	
}



//Строи попъп за променяне на атрибутите на даден таг в сорс прозореца
TextHolder.prototype.buildTagAttrEditPopup = function(){
	//alert('Attr edit node' + pNodeIdx);
	var pNodeIdx = this.nodeEditIdx;
	var lXmlNode = this.xmlNodeHash[pNodeIdx];
	if( !lXmlNode ){
		alert('There is no such node!');
		return false;
	};
	this.initPopup();
	this.currentNodeAttributes = [];
	for(var i = 0; lXmlNode.attributes && i < lXmlNode.attributes.length; ++i){
		var lAttribute = lXmlNode.attributes.item(i);
		if( lAttribute.nodeName == gEtaAttributeName )
			continue;
		this.currentNodeAttributes[lAttribute.nodeName] = lAttribute.nodeValue;		
		//~ console.log(lAttribute.nodeName + ' - ' + lAttribute.nodeValue);
	}
	
	var lAttrSelect = document.createElement('select');
	var lAttrValueInput = document.createElement('input');
	
	lAttrValueInput.setAttribute('type', 'text');
	
	if( this.annotationTags[lXmlNode.nodeName] ){
		var lAllAttributes = this.annotationTags[lXmlNode.nodeName]['attributes'];
		if( lAllAttributes ){
			for( var i in lAllAttributes){		
				var lOption = document.createElement('option');		
				var lAttrName = lAllAttributes[i];
				lOption.innerHTML = lAttrName;
				lOption.setAttribute('value', lAttrName);		
				lAttrSelect.appendChild(lOption);
			}
		}
	}
	lAttribute = lAttrSelect.value;
	if( this.currentNodeAttributes[lAttribute] ){
		lAttrValueInput.value = this.currentNodeAttributes[lAttribute];
	}
	this.addEvent('change', 
		function(){
			var lAttribute = lAttrSelect.value;
			if( this.currentNodeAttributes[lAttribute] ){
				lAttrValueInput.value = this.currentNodeAttributes[lAttribute];
			}else{
				lAttrValueInput.value = '';
			}
		}.bind(this), lAttrSelect);
		
	var lMainLabel = document.createElement('div');
	lMainLabel.className = 'label';
	lMainLabel.appendChild(document.createTextNode('Attribute edit'));
	
	var lSelectLabel = document.createElement('span');	
	lSelectLabel.innerHTML = 'Attribute name';
	
	var lInputLabel = document.createElement('span');	
	lInputLabel.innerHTML = 'Attribute value';
	
	var lSelectHolderTable = document.createElement('table');
	var lSelectHolderTr = lSelectHolderTable.appendChild(document.createElement('tr'));
	var lLabelTd = lSelectHolderTr.appendChild(document.createElement('td'));
	var lSelectTd = lSelectHolderTr.appendChild(document.createElement('td'));
	lSelectLabel = lLabelTd.appendChild(lSelectLabel);
	lAttrSelect = lSelectTd.appendChild(lAttrSelect);
	
	var lInputHolderTr = lSelectHolderTable.appendChild(document.createElement('tr'));
	var lLabelTd = lInputHolderTr.appendChild(document.createElement('td'));
	var lInputTd = lInputHolderTr.appendChild(document.createElement('td'));
	lInputLabel = lLabelTd.appendChild(lInputLabel);
	lAttrValueInput = lInputTd.appendChild(lAttrValueInput);
	
	lMainLabel = this.divPopup.appendChild(lMainLabel);
	//lSelectLabel = this.divPopup.appendChild(lSelectLabel);
	//lAttrSelect = this.divPopup.appendChild(lAttrSelect);
	this.divPopup.appendChild(lSelectHolderTable);
	this.divPopup.appendChild(document.createElement('br'));
	//lInputLabel = this.divPopup.appendChild(lInputLabel);
	//lAttrValueInput = this.divPopup.appendChild(lAttrValueInput);
	
	var lCancelButton = document.createElement('input');	
	lCancelButton.setAttribute('type', 'submit');
	var lOkButton = document.createElement('input');	
	lOkButton.setAttribute('type', 'submit');
	
	var lAddAttrButton = document.createElement('input');	
	lAddAttrButton.setAttribute('type', 'submit');
	var lDeleteAllAttr = document.createElement('input');	
	lDeleteAllAttr.setAttribute('type', 'submit');
	
	lCancelButton.value = 'Cancel';
	lOkButton.value = 'Save';
	lDeleteAllAttr.value = 'Delete all attributes';
	lAddAttrButton.value = 'Add/SetAttribute';
	lOkButton.className = 'leftInput';
	lAddAttrButton.className = 'leftInput';
	
	var lActionsHolder = document.createElement('div');
	lActionsHolder.className = 'buttonHolder';
	lActionsHolder = this.divPopup.appendChild(lActionsHolder);
	
	lAddAttrButton = lActionsHolder.appendChild(lAddAttrButton);
	lDeleteAllAttr = lActionsHolder.appendChild(lDeleteAllAttr);
	
	
	var lButtonHolder = document.createElement('div');
	lButtonHolder.className = 'buttonHolder';
	
	
	lOkButton = lButtonHolder.appendChild(lOkButton);
	lCancelButton = lButtonHolder.appendChild(lCancelButton);
	
	var lAttrTable = document.createElement('table');
	var lTableTitles = document.createElement('tr');

	var lTitle = document.createElement('th');
	lTitle.appendChild(document.createTextNode('Attribute'));
	lTableTitles.appendChild(lTitle);
	
	lTitle = document.createElement('th');
	lTitle.appendChild(document.createTextNode('Value'));
	lTableTitles.appendChild(lTitle);
	
	lTitle = document.createElement('th');
	lTableTitles.appendChild(lTitle);
	
	lAttrTable.appendChild(lTableTitles);
	lAttrTable.style.width = '100%';
	for( var lAttribute in this.currentNodeAttributes ){
		var lValue = this.currentNodeAttributes[lAttribute];
		
		var lTr = document.createElement('tr');
		lTr.setAttribute('id', 'attribute_' + lAttribute);
		
		var lNameTd = document.createElement('td');
		lNameTd.innerHTML = lAttribute;
		var lValTd = document.createElement('td');
		lValTd.setAttribute('id', 'attr_value_' + lAttribute);
		lValTd.innerHTML = lValue;
		var lDeleteTd = document.createElement('td');
		var lDeleteLink = document.createElement('a');
		lDeleteLink.setAttribute('attribute_name', lAttribute);
		var lTemp = this;
		
		this.addEvent('click', function(){
			var lAttributeName = this.getAttribute('attribute_name');
			lAttrTable.removeChild(document.getElementById('attribute_' + lAttributeName));
			delete lTemp.currentNodeAttributes[lAttributeName];
		}, lDeleteLink);
		
		lDeleteLink.innerHTML = 'Delete';
		lDeleteTd.appendChild(lDeleteLink);
		
		lTr.appendChild(lNameTd);
		lTr.appendChild(lValTd);
		lTr.appendChild(lDeleteTd);
		
		lAttrTable.appendChild(lTr);
	}	
	
	lAttrTable = this.divPopup.appendChild(lAttrTable);
	
	this.addEvent('click', function(){
		var lAttrName = lAttrSelect.value;
		var lAttrValue = lAttrValueInput.value;
		if( !lAttrValue || !lAttrName ){
			return false;
		}
		this.currentNodeAttributes[lAttrName] = lAttrValue;
		var lTableValueTd = document.getElementById('attr_value_' + lAttrName);
		if( lTableValueTd ){
			lTableValueTd.innerHTML = lAttrValue;
		}else{
			var lTr = document.createElement('tr');
			lTr.setAttribute('id', 'attribute_' + lAttrName);
			
			var lNameTd = document.createElement('td');
			lNameTd.innerHTML = lAttrName;
			var lValTd = document.createElement('td');
			lValTd.setAttribute('id', 'attr_value_' + lAttrName);
			lValTd.innerHTML = lAttrValue;
			var lDeleteTd = document.createElement('td');
			var lDeleteLink = document.createElement('a');
			lDeleteLink.setAttribute('attribute_name', lAttribute);
			lAttrTable.appendChild(lTr);
			var lTemp = this;
			
			this.addEvent('click', function(){
				var lAttributeName = this.getAttribute('attribute_name');
				lAttrTable.removeChild(document.getElementById('attribute_' + lAttributeName));
				delete lTemp.currentNodeAttributes[lAttributeName];
			}, lDeleteLink);
			
			lDeleteLink.innerHTML = 'Delete';
			lDeleteTd.appendChild(lDeleteLink);
			
			lTr.appendChild(lNameTd);
			lTr.appendChild(lValTd);
			lTr.appendChild(lDeleteTd);
			
			
		}
	}.bind(this),lAddAttrButton);
	
	this.addEvent('click', function(){
		for( var lAttributeName in this.currentNodeAttributes){
			var lTd = document.getElementById('attribute_' + lAttributeName);
			lAttrTable.removeChild(lTd);
			delete this.currentNodeAttributes[lAttributeName];
		}
	}.bind(this),lDeleteAllAttr);
	
	lButtonHolder = this.divPopup.appendChild(lButtonHolder);
	
	this.addEvent('click', function(){this.saveNodeAttributes(pNodeIdx); this.hidePopup()}.bind(this), lOkButton);
	this.addEvent('click', function(){this.hidePopup();}.bind(this), lCancelButton);
	
	this.displayPopup();	
}

//Строи попъп за променяне на името на някой таг
TextHolder.prototype.buildRenameTagPopup = function(){
	//alert('Attr edit node' + pNodeIdx);
	var pNodeIdx = this.nodeEditIdx;
	var lXmlNode = this.xmlNodeHash[pNodeIdx];
	if( !lXmlNode ){
		alert('There is no such node!');
		return false;
	};
	this.initPopup();
	
	var lSelect = document.createElement('select');
	
	for( var lTagName in this.annotationTags){
		var lElement = this.annotationTags[lTagName];
		var lOption = document.createElement('option');		
		lOption.innerHTML = lTagName;
		lOption.setAttribute('value', lTagName);		
		lSelect.appendChild(lOption);
	}	
	var lMainLabel = document.createElement('div');
	
	lMainLabel.className = 'label';
	lMainLabel.appendChild(document.createTextNode('Rename tag'));
	
	var lSelectLabel = document.createElement('span');	
	lSelectLabel.innerHTML = 'Tag name';
	
	var lSelectHolderTable = document.createElement('table');
	var lSelectHolderTr = lSelectHolderTable.appendChild(document.createElement('tr'));
	var lLabelTd = lSelectHolderTr.appendChild(document.createElement('td'));
	var lSelectTd = lSelectHolderTr.appendChild(document.createElement('td'));
	lSelectLabel = lLabelTd.appendChild(lSelectLabel);
	lSelect = lSelectTd.appendChild(lSelect);
	
	lMainLabel = this.divPopup.appendChild(lMainLabel);
	this.divPopup.appendChild(lSelectHolderTable);
	//lSelectLabel = this.divPopup.appendChild(lSelectLabel);
	//lSelect = this.divPopup.appendChild(lSelect);
	
	var lCancelButton = document.createElement('input');	
	lCancelButton.setAttribute('type', 'submit');
	var lRenameSingleButton = document.createElement('input');	
	lRenameSingleButton.setAttribute('type', 'submit');
	var lRenameAllButton = document.createElement('input');	
	lRenameAllButton.setAttribute('type', 'submit');
	
	lCancelButton.value = 'Cancel';
	lRenameSingleButton.value = 'Rename';
	lRenameSingleButton.className = 'leftInput';
	lRenameAllButton.value = 'Rename all';
	lRenameAllButton.className = 'leftInput';
	
	var lButtonHolder = document.createElement('div');
	lButtonHolder.className = 'buttonHolder';
	lButtonHolder = this.divPopup.appendChild(lButtonHolder);
	
	lRenameSingleButton = lButtonHolder.appendChild(lRenameSingleButton);
	lRenameAllButton = lButtonHolder.appendChild(lRenameAllButton);
	lCancelButton = lButtonHolder.appendChild(lCancelButton);
	
	
	
	this.addEvent('click', function(){this.renameSingleTag(lSelect.value, lXmlNode);this.hidePopup();}.bind(this), lRenameSingleButton);
	this.addEvent('click', function(){this.renameAllTags(lSelect.value, lXmlNode.nodeName);this.hidePopup();}.bind(this), lRenameAllButton);
	this.addEvent('click', function(){this.hidePopup();}.bind(this), lCancelButton);
	
	this.displayPopup();	
}


//Променя името на pXmlNode на pNewName
TextHolder.prototype.renameSingleTag = function(pNewName, pXmlNode){
	if( !pNewName || !pXmlNode ){
		alert('No tag name or node');
		return false;
	}
	this.addNodeName(pNewName);
	
	var lIdx = pXmlNode.getAttribute(gEtaAttributeName);
	var lNewNode = this.xmlDomDocument.createElement(pNewName);
	lNewNode.setAttribute(gEtaAttributeName, lIdx);
	var lTextNode = this.contentDocumentText.getElementById(lIdx);
	lTextNode.className = 'node_' + TextHolder.parseNodeNameForCss(pNewName);
	
	var lAttributes = '';
	for(var i = 0; pXmlNode.attributes && i < pXmlNode.attributes.length; ++i){
		var lAttribute = pXmlNode.attributes.item(i);
		//~ if( lAttribute.nodeName == gEtaAttributeName )
			//~ continue;
		lNewNode.setAttribute(lAttribute.nodeName, lAttribute.nodeValue);
		lAttributes = lAttributes + ' <span class="attributeName">' + lAttribute.nodeName + '</span>="<span class="attributeValue">' + lAttribute.nodeValue +'</span>"'; 
	}
	
	for( var i = 0; i < pXmlNode.childNodes.length; ++i){
		//~ lNewNode.appendChild(pXmlNode.childNodes[i].cloneNode(true));
		lNewNode.appendChild(this.xmlNodeDeepCopy(pXmlNode.childNodes[i]));
	}
	
	//~ pXmlNode.parentNode.replaceChild(lNewNode, pXmlNode);
	this.replaceParentChild(pXmlNode.parentNode, lNewNode, pXmlNode);
	this.xmlNodeHash[lIdx] = lNewNode;
	
	var lNameStartHolder = this.contentDocumentXml.getElementById('name_holder_start_' + lIdx);
	var lNameEndHolder = this.contentDocumentXml.getElementById('name_holder_end_' + lIdx);
	if( lNameStartHolder ){
		lNameStartHolder.innerHTML = '&lt;<span class="nodeName">' + pNewName + '</span>' + lAttributes + '&gt;';
	}
	if( lNameEndHolder ){
		lNameEndHolder.innerHTML = '&lt;/<span class="nodeName">' + pNewName + '</span>&gt;';
	}
}

//Променя името на всички тагове с име pOldName на pNewName
TextHolder.prototype.renameAllTags = function(pNewName, pOldName){
	if( !pNewName || !pOldName )
		return false;
	var lAllNodes = this.xmlDomDocument.selectNodes('//' + pOldName);
	for(var i = 0; i < lAllNodes.length; ++i){
		var lNode = lAllNodes[i];
		this.renameSingleTag(pNewName, lNode);
	}
}

//Изтрива даден таг
TextHolder.prototype.removeTag = function(){
	var pNodeIdx = this.nodeEditIdx;
	var lXmlNode = this.xmlNodeHash[pNodeIdx];
	var lTextNode = this.contentDocumentText.getElementById(pNodeIdx);
	var lXmlParent = lXmlNode.parentNode;
	
	if( !lXmlNode ){
		alert('There is no such node!');
		return false;
	};
	if(this.removeSingleNode(lXmlNode, 1)){//Ako uspesho sme iztrili xml node-a prodyljavame natam	
		this.removeSingleNode(lTextNode);
		this.updateXmlCode(lXmlParent, 1);
	}
}

//Маха даден таг - децата не се изтриват а се добавят като наследници на родителя на този таг
TextHolder.prototype.removeSingleNode = function(pNode, pXmlParse){//Maha samo taga - childovete se dobavqt kato child-ove na parent-a na syshtoto mqsto
	var lParent = pNode.parentNode;
	var lCurrentNode = pNode.firstChild;
	if( pXmlParse ){//Ako rabotim s xml-a trqbva da opravim hash-a s indeksite
		if( lParent == this.xmlDomDocument ){
			alert('Can\'t remove root node!');
			return false;
		}
		while( lCurrentNode ){
			var lChildToMove = lCurrentNode;
			lCurrentNode = lCurrentNode.nextSibling;			
			this.xmlNodeInsertBefore(lParent, lChildToMove, pNode);			
		}
	}else{
		while( lCurrentNode ){
			var lChildToMove = lCurrentNode;
			lCurrentNode = lCurrentNode.nextSibling;			
			lParent.insertBefore(lChildToMove, pNode);			
		}
	}
	//~ lParent.removeChild(pNode);
	this.removeNodeFromParent(lParent, pNode);
	return true;
}

//Запазва атрибутите на таг-а с атрибут gEtaAttributeName pNodeIdx
TextHolder.prototype.saveNodeAttributes = function(pNodeIdx){
	var lXmlNode = this.xmlNodeHash[pNodeIdx];
	if( !lXmlNode ){
		alert('There is no such node!');
		return false;
	};
	while (lXmlNode.attributes.length > 0){
		lAttNode = lXmlNode.attributes[0];
		lXmlNode.removeAttributeNode(lAttNode);		
	}
	lXmlNode.setAttribute(gEtaAttributeName, pNodeIdx);
	//~ lAttributes = ' <span class="attributeName">' + gEtaAttributeName + '</span>="<span class="attributeValue">' + pNodeIdx +'</span>"'; 
	lAttributes = ''; 
	for( var lAttribute in this.currentNodeAttributes ){
		var lValue = this.currentNodeAttributes[lAttribute];	
		lXmlNode.setAttribute(lAttribute, lValue);		
		lAttributes = lAttributes + ' <span class="attributeName">' + lAttribute + '</span>="<span class="attributeValue">' + lValue +'</span>"'; 
	}
	
	var lNameHolder = this.contentDocumentXml.getElementById('name_holder_start_' + pNodeIdx);
	if( lNameHolder ){
		lNameHolder.innerHTML = '&lt;<span class="nodeName">' + lXmlNode.nodeName + '</span>' + lAttributes + '&gt;';
	}
	
}

//Тагва избрания selection
TextHolder.prototype.annotateSelection = function(pSelect){
	var lTagName = pSelect.value;
	if( !lTagName || !this.annotationTags[lTagName] ){
		alert('There is no such tag!');
	}else{
		if( this.annotationTags[lTagName]['display'] == 'block' ){
			this.annotateBlockSelection(lTagName);
		}else{	
				annotateInlineSelection(lTagName);
		}
	}
	this.hidePopup();
}

//Тагва избрания selection
TextHolder.prototype.annotateBlockSelection = function(pTagName){
	var lSelection = new Selection(this.contentDocumentText);
	lSelection.correctStartAndEndNodes();
	if( lSelection.isEmpty()){
		alert('You haven\'t selected any text!');
		return false;
	}
	var lSelectionStartNode = lSelection.getStartNode();
	var lSelectionEndNode = lSelection.getEndNode();
	
	var lStartParent = lSelectionStartNode.parentNode;
	var lEndParent = lSelectionEndNode.parentNode;
	
	var lStartChildIndex = 0;
	var lEndChildIndex = 0;
	
	var lTempSibling = lSelectionStartNode.previousSibling;
	while( lTempSibling ){
		lTempSibling = lTempSibling.previousSibling;
		++lStartChildIndex;
	}
	
	lTempSibling = lSelectionEndNode.previousSibling;
	while( lTempSibling ){
		lTempSibling = lTempSibling.previousSibling;
		++lEndChildIndex;
	}
	
	var lStartEtaIdx = lStartParent.getAttribute('id');
	var lEndEtaIdx = lEndParent.getAttribute('id');
	
	var lXmlStartParentNode = this.xmlNodeHash[lStartEtaIdx];
	var lXmlEndParentNode = this.xmlNodeHash[lEndEtaIdx];
	
	var lXmlStartNode = lXmlStartParentNode.childNodes[lStartChildIndex];
	var lXmlEndNode = lXmlEndParentNode.childNodes[lEndChildIndex];
	
	if ( !lStartEtaIdx || !lXmlStartParentNode || !lXmlStartNode || !lEndEtaIdx || !lXmlEndParentNode || !lXmlEndNode  ){
		alert('HTML is incorrect!');
		return false;
	}
	
	if( lSelectionStartNode != lSelectionEndNode ){//Selectnali sme nqkolko node-a
		//Tyrsim obshtiq parent. Vsichki siblingi mejdu parent-a na starta i parenta na enda gi prehvyrlqme da stanat child-ove na noviq node
		
		var lCommonParent = this.getFirstCommonParent(lXmlStartNode, lXmlEndNode);	
		var lParentIdx = lCommonParent.getAttribute(gEtaAttributeName);
		var lHtmlParent = this.contentDocumentText.getElementById(lParentIdx);
		if( ! lHtmlParent ){
			alert('HTML is incorrect!');
			return false;
		}
		
		this.annotateCurrentSelection(lXmlStartNode, 0, lXmlEndNode, 0, pTagName);
		this.updateXmlCode(lCommonParent, 1);
		lHtmlParent.innerHTML = '';
		this.getXmlTextSingleNode(lCommonParent, lHtmlParent);
		
	}else{ //Selectnali sme 4ast ot node 
		var lSelectionStartOffset = lSelection.getStartOffset();
		var lStartNodeText = lSelectionStartNode.textContent;
		
		//Tyrsim 1q whitespace predi selectiona
		var lSubStart = lStartNodeText.substr(0, lSelectionStartOffset);
		var lStartIndexCorrection = lSubStart.search(/\s(\S)*$/);
		if( lStartIndexCorrection > 0 ){//Ima nameren whitespace
			lSelectionStartOffset = lStartIndexCorrection + 1;
		}else{//Mestime selectiona do nachaloto na textoviq node
			lSelectionStartOffset = 0;
		}
	
	
		//Tyrsim 1q space sled selectiona
		var lSelectionEndOffset = lSelection.getEndOffset();	
		var lSubEnd = lStartNodeText.substr(lSelectionEndOffset);
		var lEndIndexCorrection = lSubEnd.search(/\s/);
		if( lEndIndexCorrection >= 0){//nameren e space - mestime selectiona do tozi space
			lSelectionEndOffset = lSelectionEndOffset + lEndIndexCorrection;
		}else{//mestime selectiona do kraq na node-a
			lSelectionEndOffset = lStartNodeText.length;
		}
		
		this.annotateCurrentSelection(lXmlStartNode, lSelectionStartOffset, lXmlEndNode, lSelectionEndOffset, pTagName);
		
		//Updatevame source-a
		this.updateXmlCode(lXmlStartParentNode, 1);
		lStartParent.innerHTML = '';
		this.getXmlTextSingleNode(lXmlStartParentNode, lStartParent);		
	}
	this.addNodeName(pTagName);
	

}

//Слага таг около селекцията, индексира го и го връща като резултат.
TextHolder.prototype.annotateCurrentSelection = function( pStartNode, pStartOffset, pEndNode, pEndNodeOffset, pTagName ){
	var lNewXmlNode = this.xmlDomDocument.createElement(pTagName);
	lNewXmlNode.setAttribute(gEtaAttributeName, this.currentXmlNodeIdx);
	this.xmlNodeHash[this.currentXmlNodeIdx++] = lNewXmlNode; 
	
	if( pStartNode != pEndNode ){
		var lCommonParent = this.getFirstCommonParent(pStartNode, pEndNode);	
		var lXmlStartPrevParent = pStartNode;
		var lXmlEndPrevParent = pEndNode;
		
		while( lXmlStartPrevParent.parentNode != lCommonParent ){
			lXmlStartPrevParent = lXmlStartPrevParent.parentNode;
		}
		while( lXmlEndPrevParent.parentNode != lCommonParent ){
			lXmlEndPrevParent = lXmlEndPrevParent.parentNode;
		}
		
		lCommonParent.insertBefore(lNewXmlNode, lXmlStartPrevParent);
		
		var lCurrentNode = lXmlStartPrevParent;
		var lXmlEndParentNextSibling = lXmlEndPrevParent.nextSibling;
		while(lCurrentNode != lXmlEndParentNextSibling){
			var lNode = lCurrentNode;
			lCurrentNode = lCurrentNode.nextSibling;
			
			this.xmlNodeAppendChild(lNewXmlNode, this.xmlNodeDeepCopy(lNode));
			
			lCommonParent.removeChild(lNode);
			
		}
	}else{//Selekciq ot node
		var lXmlStartParentNode = pStartNode.parentNode;
		var lStartNodeText = pStartNode.textContent;
		
		lNewXmlNode.appendChild(this.xmlDomDocument.createTextNode(lStartNodeText.substr(pStartOffset, pEndNodeOffset - pStartOffset)));
		lXmlStartParentNode.replaceChild(lNewXmlNode, pStartNode);
		if( pStartOffset > 0 ){
			lXmlStartParentNode.insertBefore(this.xmlDomDocument.createTextNode(lStartNodeText.substr(0, pStartOffset)), lNewXmlNode);
		}
		if( pEndNodeOffset < lStartNodeText.length ){
			if( lXmlStartParentNode.lastChild == lNewXmlNode ){
				lXmlStartParentNode.appendChild(this.xmlDomDocument.createTextNode(lStartNodeText.substr(pEndNodeOffset)));
			}else{
				var lTempNode = lNewXmlNode.nextSibling;
				lXmlStartParentNode.insertBefore(this.xmlDomDocument.createTextNode(lStartNodeText.substr(pEndNodeOffset)), lTempNode);
			}
		}
	}
	return lNewXmlNode;
	
}

//Тагва избрания selection - друг алгоритъм - още не работи
TextHolder.annotateInlineSelection = function(pTagName){
	var lSelection = new Selection(this.contentDocumentText);
	if( lSelection.isEmpty()){
		alert('You haven\'t selected any text!');
		return false;
	}

}

//Вкарва възела със фигурата на мястото на курсора и го маха от фалшивия възел
TextHolder.prototype.FigInsert = function(pFigId){	
	var lSingleFig;
	if( this.mode == TEXT_MODE ){//Ако работим директно с текста - ползваме си директно xml-a
		lSingleFig = this.xmlDomDocument.selectSingleNode('//' + gFakeFiguresTagName + '/' + gFigureTagName + '[@id=\'' + pFigId + '\']');
	}else{//Ако работим със сорса - работим с темп xml-a
		lSingleFig = this.sourceTempXmlDocument.selectSingleNode('//' + gFakeFiguresTagName + '/' + gFigureTagName + '[@id=\'' + pFigId + '\']');		
	}
	
	if( !lSingleFig ){
		alert('No such figure exists!');
		return;
	}		
	this.insertTableOrFig(lSingleFig, 1, pFigId);
}

//Вкарва възела със таблицата на мястото на курсора и го маха от фалшивия възел
TextHolder.prototype.TableInsert = function(pTableId){		
	var lSingleTable;
	if( this.mode == TEXT_MODE ){//Ако работим директно с текста - ползваме си директно xml-a
		lSingleTable = this.xmlDomDocument.selectSingleNode('//' + gFakeFiguresTagName + '/' + gTableWrapTagName +'[' + gTableTagName + '/@id=\'' + pTableId + '\']');
	}else{//Ако работим със сорса - работим с темп xml-a
		lSingleTable = this.sourceTempXmlDocument.selectSingleNode('//' + gFakeFiguresTagName + '/' + gTableWrapTagName +'[' + gTableTagName + '/@id=\'' + pTableId + '\']');
	}
	if( !lSingleTable ){
		alert('No such table exists!');
		return;
	}
	this.insertTableOrFig(lSingleTable, 2, pTableId);
}

TextHolder.prototype.getTextSelectionRealXmlNodes = function(){
	var lSelection = new Selection(this.contentDocumentText);
	lSelection.correctStartAndEndNodes();

	var lSelectionStartNode = lSelection.getStartNode();	
	var lSelectionEndNode = lSelection.getEndNode();	
	
	var lStartParent = lSelectionStartNode.parentNode;	
	var lEndParent = lSelectionEndNode.parentNode;	
	
	var lStartChildIndex = 0;
	var lStartOffset = lSelection.getStartOffset();
	
	var lTempSibling = lSelectionStartNode.previousSibling;
	while( lTempSibling ){		
		if( lTempSibling.nodeType == gXmlElementNodeType )
			++lStartChildIndex;
		if( lTempSibling.nodeType == gXmlTextNodeType && lStartChildIndex == 0 ){//Ako obhojdame samo predhodni tekstovi child-ove
			lStartOffset += lTempSibling.textContent.length;
		}
		lTempSibling = lTempSibling.previousSibling;
	}
	
	var lEndChildIndex = 0;
	var lEndOffset = lSelection.getEndOffset();
	
	var lTempSibling = lSelectionEndNode.previousSibling;
	while( lTempSibling ){		
		if( lTempSibling.nodeType == gXmlElementNodeType )
			++lEndChildIndex;
		if( lTempSibling.nodeType == gXmlTextNodeType && lEndChildIndex == 0 ){//Ako obhojdame samo predhodni tekstovi child-ove
			lEndOffset += lTempSibling.textContent.length;
		}
		lTempSibling = lTempSibling.previousSibling;
	}
	
	var lStartParentEtaIdx = lStartParent.getAttribute('id');
	var lEndParentEtaIdx = lEndParent.getAttribute('id');
	
	if( !lStartParentEtaIdx ){
		lStartParentEtaIdx = 1;
		lStartChildIndex = 0;
	}
	
	if( !lEndParentEtaIdx ){
		lEndParentEtaIdx = 1;
		lEndChildIndex = 0;
	}
	
	var lXmlStartParentNode = this.xmlNodeHash[lStartParentEtaIdx];	
	var lXmlStartNode = false;
	var lXmlStartParentChildren = lXmlStartParentNode.childNodes;
	for( var i = 0; i < lXmlStartParentChildren.length; ++i ){
		var lChild = lXmlStartParentChildren[i];
		if( lChild.nodeType == gXmlElementNodeType )
			lStartChildIndex--;
		if( lStartChildIndex == 0 && lChild.nodeType == gXmlTextNodeType ){
			lXmlStartNode = lChild;
			if( lStartOffset < lChild.textContent.length )
				break;
			lStartOffset -= lChild.textContent.length;
		}
	}
	
	
	var lXmlEndParentNode = this.xmlNodeHash[lEndParentEtaIdx];	
	var lXmlEndNode = false;
	var lXmlEndParentChildren = lXmlEndParentNode.childNodes;
	for( var i = 0; i < lXmlEndParentChildren.length; ++i ){
		var lChild = lXmlEndParentChildren[i];
		if( lChild.nodeType == gXmlElementNodeType )
			lEndChildIndex--;
		if( lEndChildIndex == 0 && lChild.nodeType == gXmlTextNodeType ){
			lXmlEndNode = lChild;
			if( lEndOffset < lChild.textContent.length )
				break;
			lEndOffset -= lChild.textContent.length;
		}
	}
	
	return {
		'start_node' : lXmlStartNode,
		'start_offset' : lStartOffset,
		'end_node' : lXmlEndNode,
		'end_offset' : lEndOffset
	};
}

//Мести xml възела pXmlNode на текущото място на курсора
TextHolder.prototype.insertTableOrFig = function(pXmlNode, pItemType, pItemId){
	if( !pXmlNode ){
		alert('No xml node selected');
		return;
	}	
	if( this.mode == TEXT_MODE ){//Rabotim po texta - direktno s xml-a		
		var lSelectionXmlNodes = this.getTextSelectionRealXmlNodes();
		var lStartXmlNode = lSelectionXmlNodes['start_node'];
		var lStartXmlOffset = lSelectionXmlNodes['start_offset'];
		if( !lStartXmlNode ){
			alert('Text cursor not positioned');
			return;
		}
		if( lStartXmlNode.nodeType == gXmlElementNodeType ){//Slagame vyzela predi element-a kym koito sochi cursora
			if( lStartXmlNode.childNodes.length >= lStartXmlOffset ){
				var lChild = lStartXmlNode.childNodes[lStartXmlOffset];
				lStartXmlNode.insertBefore(pXmlNode, lChild);
			}else{
				lStartXmlNode.appendChild(pXmlNode);
			}			
		}else{
			if( lStartXmlNode.nodeType == gXmlTextNodeType ){//Rejem teksta na 2 4asti i mejdu tqh slagame vyzela
				var lStartParent = lStartXmlNode.parentNode;
				var lStartContent = lStartXmlNode.textContent.substr(0, lStartXmlOffset);
				var lEndContent = lStartXmlNode.textContent.substr(lStartXmlOffset);
				
				if( lStartContent != '' ){
					var lStartTextNode = this.xmlDomDocument.createTextNode(lStartContent);
					lStartParent.insertBefore(lStartTextNode, lStartXmlNode);
				}
				
				lStartParent.insertBefore(pXmlNode, lStartXmlNode);
				
				if( lEndContent != '' ){
					var lEndTextNode = this.xmlDomDocument.createTextNode(lEndContent);
					lStartParent.insertBefore(lEndTextNode, lStartXmlNode);
				}
				
				lStartParent.removeChild(lStartXmlNode);
			}
		}
		
		this.nullifyData();
		this.loadTextAndSourceAndIndexXml();
	}else{//Работим в текстареата - чрез темп документа
		/**
			Първо слагаме xml-a на таблицата в селектнатото място. 
			После слагаме xml-a от таблицата в temp документа, трием от него таблицата с даденото ид и 
			слагаме новия xml в текстареата
		*/
		var lSelectionStart = getTextAreaSelectionStart(this.sourceTextArea, this.contentDocumentSourceTextarea);
		var lSelectionEnd = getTextAreaSelectionEnd(this.sourceTextArea, this.contentDocumentSourceTextarea);
		var lTextAreaValue = this.sourceTextArea.value;
		if( lSelectionStart < lSelectionEnd )
			lSelectionStart = lSelectionEnd;
		var lItemXml = pXmlNode.xml;
		var lXml = lTextAreaValue.substr(0, lSelectionStart) + lItemXml + lTextAreaValue.substr(lSelectionStart);
		if(!this.sourceTempXmlDocument.loadXML(lXml)){
			alert('Invalid xml!');			
			return false;
		}
		
		var lNodeToRemove;
		
		if( pItemType == 1 ){//Fig
			lNodeToRemove = this.sourceTempXmlDocument.selectSingleNode('//' + gFakeFiguresTagName + '/' + gFigureTagName + '[@id=\'' + pItemId + '\']')
		}else{//Table
			lNodeToRemove = this.sourceTempXmlDocument.selectSingleNode('//' + gFakeFiguresTagName + '/' + gTableWrapTagName +'[' + gTableTagName + '/@id=\'' + pItemId + '\']');
		}
		
		if( lNodeToRemove ){//Mahame child-a
			lNodeToRemove.parentNode.removeChild(lNodeToRemove);
		}
		
		this.sourceTextArea.value = this.sourceTempXmlDocument.xml;
		SetAreaSelectionRangeAndScroll(this.sourceTextArea, this.contentDocumentSourceTextarea, lSelectionStart, lSelectionStart);
	}
	
}
//Нормализира XML документа - заменя двойните интервали с единични, non breaking space-ове и force line break-ове - с интервали
TextHolder.prototype.normalizeXmlDocument = function(){
	if( this.xmlDomDocument.firstChild ){
		var lXmlRoot = this.xmlDomDocument.firstChild;
		
		this.normalizeXmlNode(lXmlRoot);
		this.nullifyData();
		this.loadTextAndSourceAndIndexXml();
	}
}
//Нормализира наследниците на xml възел pXmlNode - заменя двойните интервали с единични, non breaking space-овв и force line break-ове - с интервали
TextHolder.prototype.normalizeXmlNode = function(pXmlNode){//	
	var lCurrentChild = pXmlNode.firstChild;
	while( lCurrentChild ){
		var lTempChild = lCurrentChild;
		lCurrentChild = lCurrentChild.nextSibling;
		if( lTempChild.nodeType == gXmlElementNodeType ){
			if( this.in_array(this.lineBreakTags, lTempChild.nodeName)){//Ako taga e <br> naprimer - zamestvame go s interval. Ako v taga ima tekst - izchezva; Ako masiva e prazen - nishto ne stava
				var lNewXmlNode = this.xmlDomDocument.createTextNode(' ');
				pXmlNode.replaceChild(lNewXmlNode, lTempChild);
			}else{
				this.normalizeXmlNode(lTempChild);
			}
		}else{
			if( lTempChild.nodeType == gXmlTextNodeType ){
				var lTextContent = lTempChild.nodeValue;
				lTextContent = this.normalizeString(lTextContent);
				var lNewXmlNode = this.xmlDomDocument.createTextNode(lTextContent);
				pXmlNode.replaceChild(lNewXmlNode, lTempChild);
			}
		}
	}	
}
//Замества специални символи с интервал и след това премахва двойните интервали
TextHolder.prototype.normalizeString = function(pString){
	var lReplaceHash = {
		'\u00A0' : ' ',
		'\u2007' : ' ',
		'\u202F' : ' ',
		'\u2029' : ' ',
		'\u2028' : ' ',
		
	}
	var lString = pString;
	for( var i in lReplaceHash ){
		var lRegExp = new RegExp(i, "g");//Setvame go s g za da zamesti vsichki
		lString = lString.replace(lRegExp, lReplaceHash[i]);
	}
	//var lString = pString.replace(/\u00A0|\u2007|\u202F|\u2029|\u2028/g, ' ');
	//~ var lString = pString.replace(/\s/g, ' ');
	lString = lString.replace(/  +/g, ' ');
	
	return lString;
}

//Връща съдържанието на текстовите node-ове между pStartTextNode и pEndTextNode. Съдържанието на pStartTextNode влиза в резултата, а това на pEndTextNode - не
TextHolder.prototype.getTextContentBetweenTextNodes = function(pStartTextNode, pEndTextNode){
	var lCurrentNode = pStartTextNode;
	var lContent = '';
	while( lCurrentNode && lCurrentNode != pEndTextNode ){
		lContent = lContent + lCurrent.nodeValue;
		lCurrentNode = this.getNextTextNode(lCurrentNode);
	}
	return lContent;
}


//Връща масив чиийто 1-ви елемент е node-a, където свършва селекцията, а вторият му параметър - разстоянието от началото на този node до края на селекцията
//pStartNode - стартов node на селекцията
//pStartOffset - отстояние от началото на node-a до началото на селекцията
//pLength - дължина на селекцията
TextHolder.prototype.getXmlSelectionEndNodeDetails = function(pStartNode, pStartOffset, pLength){
	var lStartNodeTextLength = pStartNode.textContent.length;
	if( lStartNodeTextLength >= (pStartOffset + pLength) ){//Kraq e v syshtiq node
		return new Array(pStartNode, (pStartOffset + pLength));
	}
	
	var lLength = pLength - (lStartNodeTextLength - pStartOffset);
	var lCurrentNode = this.getNextTextNode(pStartNode);
	
	//Обикаляме следвашите текстови node-ове докато стигнем до node-a в който селекцията свършва
	while( lLength > 0 && lCurrentNode ){
		var lCurrentNodeTextLength = lCurrentNode.textContent;
		if( lLength <= lCurrentNodeTextLength ){
			return new Array(lCurrentNode, lLength);
		}
		lCurrentNode = this.getNextTextNode(lCurrentNode);
		lLength = lLength - lCurrentNodeTextLength;		
	}
	return false;
}













//Прави deepCopy на xml node pXmlNode като коригира хеша с индексите this.xmlNodeHash
TextHolder.prototype.xmlNodeDeepCopy = function(pXmlNode){
	var lNewNode = pXmlNode.cloneNode(false);
	if( lNewNode.nodeType == gXmlElementNodeType ){
		var lIdx = lNewNode.getAttribute(gEtaAttributeName);
		if( lIdx ){
			this.xmlNodeHash[lIdx] = lNewNode;
		}
		for( var i = 0; i < pXmlNode.childNodes.length; ++i ){
			this.xmlNodeAppendChild(lNewNode, this.xmlNodeDeepCopy(pXmlNode.childNodes[i]));
		}
	}
	return lNewNode;
}

//Добавя pNewNode като дете на xml node-a pParent, като коригира хеша с индексите this.xmlNodeHash
TextHolder.prototype.xmlNodeAppendChild = function(pParent, pNewNode){
	if( pNewNode.parentNode ){
		pNewNode.parentNode.removeChild(pNewNode);
	}
	var lNewNode = pParent.appendChild(pNewNode);
	if( lNewNode.nodeType == gXmlElementNodeType ){
		var lIdx = lNewNode.getAttribute(gEtaAttributeName);
		if( lIdx ){
			this.xmlNodeHash[lIdx] = lNewNode;
		}		
	}
}

//Добавя pNewNode като дете на xml node-a pParent и го слага преди pRefNode, като коригира хеша с индексите this.xmlNodeHash
TextHolder.prototype.xmlNodeInsertBefore = function(pParent, pNewNode, pRefNode){
	var lNewNode = pParent.insertBefore(pNewNode, pRefNode);
	if( lNewNode.nodeType == gXmlElementNodeType ){
		var lIdx = lNewNode.getAttribute(gEtaAttributeName);
		if( lIdx ){
			this.xmlNodeHash[lIdx] = lNewNode;
		}		
	}
}

//Взима позицията на мишката
TextHolder.prototype.getMousePosition = function(pEvent){	
    if (document.layers) {
        // When the page scrolls in Netscape, the event's mouse position
        // reflects the absolute position on the screen. innerHight/Width
        // is the position from the top/left of the screen that the user is
        // looking at. pageX/YOffset is the amount that the user has
        // scrolled into the page. So the values will be in relation to
        // each other as the total offsets into the page, no matter if
        // the user has scrolled or not.
        xMousePos = pEvent.clientX;
        yMousePos = pEvent.clientY;
        xMousePosMax = window.innerWidth + window.pageXOffset;
        yMousePosMax = window.innerHeight + window.pageYOffset;
    } else if (document.all) {
        // When the page scrolls in IE, the event's mouse position
        // reflects the position from the top/left of the screen the
        // user is looking at. scrollLeft/Top is the amount the user
        // has scrolled into the page. clientWidth/Height is the height/
        // width of the current page the user is looking at. So, to be
        // consistent with Netscape (above), add the scroll offsets to
        // both so we end up with an absolute value on the page, no
        // matter if the user has scrolled or not.	;
        xMousePos = window.event.x + document.body.scrollLeft;
        yMousePos = window.event.y + document.body.scrollTop;
        xMousePosMax = document.body.clientWidth + document.body.scrollLeft;
        yMousePosMax = document.body.clientHeight + document.body.scrollTop;
    } else if (document.getElementById) {
        // Netscape 6 behaves the same as Netscape 4 in this regard
        xMousePos = pEvent.clientX;
        yMousePos = pEvent.clientY;
        xMousePosMax = window.innerWidth + window.pageXOffset;
        yMousePosMax = window.innerHeight + window.pageYOffset;
    }
    return {
	'x' : xMousePos,
	'y' : yMousePos,
	'xMax' : xMousePosMax,
	'yMax' : yMousePosMax
    };
}

//Взима позицията на мишката
TextHolder.prototype.getScrollXY = function() {
	var scrOfX = 0, scrOfY = 0;
	if( typeof( window.pageYOffset ) == 'number' ) {
		//Netscape compliant
		scrOfY = window.pageYOffset;
		scrOfX = window.pageXOffset;
	} else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
		//DOM compliant
		scrOfY = this.contentDocumentText.body.scrollTop;
		scrOfX = this.contentDocumentText.body.scrollLeft;
	} else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
		//IE6 standards compliant mode
		scrOfY = document.documentElement.scrollTop;
		scrOfX = document.documentElement.scrollLeft;
	}
	//~ console.log([ scrOfX, scrOfY ]);
	return [ scrOfX, scrOfY ];
}

//Взима размерите на екрана
TextHolder.prototype.getScreenSize = function() {
	var myHeight = 0; var myWidth = 0;
	if (window.innerWidth && window.innerHeight) {
		// Netscape & Mozilla
		myHeight = window.innerHeight;
		myWidth = window.innerWidth;
	} else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
		// IE > 6
		myHeight = document.documentElement.clientHeight;
		myWidth = document.documentElement.clientWidth;
	} else if (document.body.offsetWidth && document.body.offsetHeight) {
		// IE = 6
		myHeight = document.body.offsetHeight;
		myWidth = document.body.offsetWidth;
	} else if (document.body.clientWidth && document.body.clientHeight) {
		// IE < 6
		myHeight = document.body.clientHeight;
		myWidth = document.body.clientWidth;
	}

	return [ myWidth, myHeight ];
}

TextHolder.prototype.scrollSourceDiv = function(){
	lSelection = new Selection(this.contentDocumentText);
	lSelection.correctStartAndEndNodes();
	var lStartNode = lSelection.getStartNode();
	if( !lStartNode ){//Prazen selection
		return false;
	}
	var lRealStartNode = lStartNode;
	var lChildNumber = 0;
	var lStartOffset = lSelection.getAnchorOffset();
	var lMoveToChild = false;
	/*if( lStartNode.nodeType != gXmlTextNodeType ){		
		var lCurOffset = lStartOffset;			
		for( var i = 0 ; i < lStartNode.childNodes.length && lCurOffset >= i; ++i ){
			if( i == lStartOffset ){
				var lChild = lStartNode.childNodes[i];
				var lTextNode;
				if( lChild.nodeType != gXmlTextNodeType ){	
					lTextNode = this.getFirstTextNodeChild(lChild);
					if( !lTextNode ){
						lTextNode = this.getNextTextNode(lChild);
					}
				}else{
					lTextNode = lChild;
				}
				if( lTextNode ){
					lSelection.setStartPoint(lTextNode, 0);
					lStartNode = lTextNode;
				}else{
					return;
				}
			}
		}
		
		lStartOffset = 0;
	}
	*/
	var lSelectionLength = this.getSelectionTextLength(lSelection);
	if( lStartNode.nodeType == gXmlTextNodeType ){
		lRealStartNode = lStartNode.parentNode;
		lMoveToChild = true;
		var lTemp = lStartNode.previousSibling;
		while(lTemp){
			
			if( lTemp.nodeType == gXmlTextNodeType ){
				lStartOffset = lStartOffset + lTemp.textContent.length;
			}
			lTemp = lTemp.previousSibling;
		}
	}else{
		
	}
	var lIdx = lRealStartNode.getAttribute('id');
	if( !lIdx )
		return false;
	var lSourceRealStart = this.contentDocumentXml.getElementById(lIdx);
	if( !lSourceRealStart )
		return false;
	//~ if( lMoveToChild ){
		//~ var lSourceRealStart = lSourceRealStart.childNodes[3];//Vzimame holdera na texta - 0 i 1 sa + i -. 2 e start spana s imeto na node-a i 3 e holdera
	//~ }
	lSourceRealStart = lSourceRealStart.childNodes[3];
	//~ this.scrollDiv(this.contentDocumentXml, lSourceRealStart, lStartOffset, lMoveToChild, lSelectionLength, true);
	this.scrollDiv(this.contentDocumentXml, lSourceRealStart, lStartOffset, 1, lSelectionLength, true);
}

TextHolder.prototype.scrollTextDiv = function(){
	lSelection = new Selection(this.contentDocumentXml);
	lSelection.correctStartAndEndNodes();
	var lStartNode = lSelection.getStartNode();
	if( !lStartNode ){//Prazen selection
		return false;
	}
	var lRealStartNode = lStartNode;
	var lChildNumber = 0;
	var lSpanHolderNode = false;
	var lStartOffset = lSelection.getAnchorOffset();
	var lMoveToChild = false;
	var lSelectionLength = this.getSelectionTextLength(lSelection);
	
	if( lStartNode.nodeType == gXmlTextNodeType ){//Vinagi selectiona e v tekstov nod no za vseki slu4ai da se zastrahovame
		var lHolderNode = lStartNode.parentNode;//lHolderNode e holder-a na teksta
		if( !lHolderNode )
			return false;
		
		lRealStartNode = lHolderNode.parentNode;//Tova e div-a na node - t.e. ima id;//Moje da byde i spana na imeto na node-a, ako selectiona e v imeto na node-a, a ne v sydyrjanieto
		if( lRealStartNode && lRealStartNode.nodeName.toLowerCase() == 'span' ){//Ako selectiona e v imeto na node-a 
			lSpanHolderNode = lRealStartNode;
			lRealStartNode = lRealStartNode.parentNode;
		}else{
			if( lHolderNode.nodeName.toLowerCase() == 'span' ){//Selectiona e v skobite na imeto na node-a
				lSpanHolderNode = lHolderNode;
			}
		}
		if( !lRealStartNode )
			return false;
		
		lMoveToChild = true;
		var lTemp = lStartNode.previousSibling;
		while(lTemp){
			if( lTemp.nodeType == gXmlTextNodeType ){
				lStartOffset = lStartOffset + lTemp.textContent.length;
			}	
			lTemp = lTemp.previousSibling;
		}
	}else{
		return false;
	}
	
	var lIdx = lRealStartNode.getAttribute('id');
	if( !lIdx )
		return false;
	
	var lSourceRealStart = this.contentDocumentText.getElementById(lIdx);	
	if( !lSourceRealStart )
		return false;
	if( lSpanHolderNode ){//Ako sme selectnali imeto na node-a
		if( lSpanHolderNode.id == 'name_holder_end_' + lIdx ){//mestime selectiona do kraq na sydyrjanieto na node-a
			lStartOffset = lSourceRealStart.textContent.length;
		}else{
			lStartOffset = 0;
		}
	}else{
		if( lHolderNode.nodeName.toLowerCase() == 'a' )//Selectiona e v ikonkata na + ili -
			lStartOffset = 0;
	}
	
	this.scrollDiv(this.contentDocumentText, lSourceRealStart, lStartOffset, lMoveToChild, lSelectionLength);
}

TextHolder.prototype.scrollDiv = function(pIframe, pParentNode, pStartOffset, pMoveToChild, pSelectionLength, pMoveOffsetRow){
	var lTop = this.getElementTopPosition(pParentNode);
	if( pMoveToChild ){
		var lCurrentNode = false;
		var lCurrentOffset = pStartOffset;
		for( var i = 0; i < pParentNode.childNodes.length; ++i ){
			lCurrentNode = pParentNode.childNodes[i];
			if( lCurrentNode.nodeType != gXmlTextNodeType )
				continue;
			if( lCurrentOffset <= lCurrentNode.textContent.length )
				break;
			lCurrentOffset = lCurrentOffset - lCurrentNode.textContent.length;
		}
		if( lCurrentNode ){			
			
			var lNewSelection = new Selection(pIframe);			
			
			if( lCurrentOffset > lCurrentNode.textContent.length )
				lCurrentOffset = lCurrentNode.textContent.length;
			
			
			
			lNewSelection.setStartPoint(lCurrentNode, lCurrentOffset);
			
						
			var lCursorDiv = pIframe.createElement("div");
			lCursorDiv.innerHTML = 'CURSORDIV';
			var lRange = lNewSelection.getRange();
			try{
				lRange.insertNode(lCursorDiv);
				
				
				lTop = this.getElementTopPosition(lCursorDiv);
				
				if( lCurrentOffset && pMoveOffsetRow )
					lTop = lTop - lCursorDiv.offsetHeight;//Mestime offseta s 1 red nagore poneje div-a e po-nadolu ot teksta
				
				lCursorDiv.parentNode.removeChild(lCursorDiv);			
				
				var lEndNodeDetails = this.getSelectionEndNode(pIframe, lCurrentNode, lCurrentOffset, pSelectionLength);
				lNewSelection.setEndPoint(lEndNodeDetails[0], lEndNodeDetails[1]);
			}catch(e){
			
			}
		}
	}
	
	
	this.scrollIframe(pIframe, 0, lTop);
}

TextHolder.prototype.getSelectionTextLength = function(pSelection){
	var lStartNode = pSelection.getAnchorNode();
	var lStartOffset = pSelection.getAnchorOffset();
	var lEndNode = pSelection.getFocusNode();
	var lEndOffset = pSelection.getFocusOffset();
	var lSelectionLength = 0;
	var lContentDocument = pSelection.contentDocument;
	var lCurrentNode = lStartNode;
	
	//Pri selection v prozoreca na teksta vseki tekstov node vliza v dyljinata na selectiona
	//Pri selection v prozoreca na source-a tekstovite node-ove ot a-tata i tagovete s imenata ne vlizat v dyljinata na selectiona
	while( lCurrentNode && lCurrentNode != lEndNode ){//Razchitame na fakta 4e selectiona e izcqlo v tekstovi node-ove
		if( this.isSourceTextNodeXmlNode(lContentDocument, lCurrentNode)){
			if( lCurrentNode == lStartNode ){
				lSelectionLength = lCurrentNode.textContent.length - lStartOffset;
			}else{
				lSelectionLength = lSelectionLength + lCurrentNode.textContent.length;
			}
		}
		lCurrentNode = this.getNextTextNode(lCurrentNode);
	}
	//Veche sme na posledniq node
	if( this.isSourceTextNodeXmlNode(lContentDocument, lCurrentNode)){
		if( lCurrentNode == lStartNode ){//Selectiona po4va i svyrshva v 1 node
			lSelectionLength = lEndOffset - lStartOffset;
		}else{
			lSelectionLength = lSelectionLength + lEndOffset;
		}
	}
			
	return lSelectionLength;
}

TextHolder.prototype.getSelectionEndNode = function(pContentDocument, pStartNode, pStartOffset, pSelectionLength){//Vryshta node-a i offseta ot na4aloto na node-a, koito bi trqbvalo da otgovarqt na krai na selectiona
	var lCurrentNode = pStartNode;
	var lCurrentOffset = pSelectionLength;
	var lPrevNode = pStartNode;
	var lPrevLength = pStartNode.textContent.length;
	while( lCurrentNode && lCurrentOffset > 0 ){
		var lCurLength = lCurrentNode.textContent.length;
		if( this.isSourceTextNodeXmlNode(pContentDocument, lCurrentNode) ){
			if( lCurrentNode == pStartNode ){
				lCurrentOffset = lCurrentOffset + pStartOffset;
			}
			if( lCurrentOffset <= lCurLength )
				return new Array(lCurrentNode, lCurrentOffset );
			lCurrentOffset = lCurrentOffset - lCurLength;
			
		}
		lPrevNode = lCurrentNode;
		lPrevLength = lCurLength;
		lCurrentNode = this.getNextTextNode(lCurrentNode);
	}
	//Ako sme obikolili celiq xml - vryshtame posledniq tekstov node s kraq mu
	return new Array(lPrevNode, lPrevLength );
}

TextHolder.prototype.isSourceTextNodeXmlNode = function(pContentDocument, pNode){//Opredelq dali tekstoviq node ot prozoreca na xmlSource-a e tekstov nod ot XML-a ili e tekstov node ot dobavenite
	//Tekstovite node-ove ot prozoreca na teksta vinagi sa tekstovi node-ove
	if( pContentDocument == this.contentDocumentText )
		return true;
		
	//Dobavenite tekstovi node-ove sa vyv span ili a dokato tezi ot xml-a se sydyrjat v divove
	if( pNode.parentNode.nodeName.toLowerCase() == 'div' )
		return true;
	return false;
}

TextHolder.prototype.getElementTopPosition = function(pElement){
	return pElement.offsetTop;
}

TextHolder.prototype.scrollIframe = function(pDocument, pLeft, pTop){
	pDocument.body.scrollTop = pTop;
	pDocument.body.scrollLeft = pLeft;
}

TextHolder.prototype.autonumerateXML = function(pXPath, pAttributeName, pStartValue){
	if( pXPath == '' ){
		alert('Wrong xpath');
		return;		
	}
	if( pAttributeName == '' ){
		alert('Wrong attribute name');
		return;		
	}
	var lNodes = this.xmlDomDocument.selectNodes(pXPath);
	if( lNodes.length ){
		var lCurrentNumber = pStartValue;
		for( var i = 0; i < lNodes.length; ++i){
			var lCurrentNode = lNodes[i];
			lCurrentNode.setAttribute(pAttributeName, lCurrentNumber++);			
		}
		//Reload source to show correct attributes
		this.contentDocumentXml.body.innerHTML = '';		
		this.getXmlSource(this.xmlDomDocument.firstChild, this.contentDocumentXml.body);
		alert('Auto numerate rule performed successfully');
	}else{
		alert('No nodes satisfy xpath expression');
		return;	
	}
	
}

TextHolder.prototype.checkReferenceCitationCount = function(){
	var lReferenceNodes = this.xmlDomDocument.selectNodes('/article/back/ref-list/ref[@id]');
	if( !lReferenceNodes.length ){
		alert('No reference nodes found!');
		return;
	}
	this.showWaitingImage();	
	this.initPopup();
	
	var lMainLabel = document.createElement('div');
	lMainLabel.className = 'label';
	lMainLabel.appendChild(document.createTextNode('Reference citations count'));
	lMainLabel = this.divPopup.appendChild(lMainLabel);
		
	
	
	var lMatchHolderTable = document.createElement('table');
	lMatchHolderTable.className = 'matchesTable';
	
	var lLabelTr = lMatchHolderTable.appendChild(document.createElement('tr'));	
	var lTextLabel = lLabelTr.appendChild(document.createElement('th'));	
	var lIdLabel = lLabelTr.appendChild(document.createElement('th'));	
	var lNumLabel = lLabelTr.appendChild(document.createElement('th'));
	
	lTextLabel.innerHTML = 'Reference text';
	lNumLabel.innerHTML = '# of citations';
	lIdLabel.innerHTML = 'Reference id';
	
	
	for ( var i = 0; i < lReferenceNodes.length; ++i ){
		var lReferenceNode = lReferenceNodes[i];
		var lReferenceId = lReferenceNode.getAttribute('id');
		var lCitations = this.xmlDomDocument.selectNodes('//xref[@rid=\'' + lReferenceId + '\']');
		
		
		var lRowHolder = lMatchHolderTable.appendChild(document.createElement('tr'));
		var lReferenceTextHolder = lRowHolder.appendChild(document.createElement('td'));
		var lReferenceIdHolder = lRowHolder.appendChild(document.createElement('td'));
		var lCitationsCountHolder = lRowHolder.appendChild(document.createElement('td'));		
		
		lReferenceTextHolder.innerHTML = lReferenceNode.textContent;
		lReferenceIdHolder.innerHTML = lReferenceId;
		lCitationsCountHolder.innerHTML = lCitations.length;
		
		lReferenceTextHolder.className = 'referenceCitationText';
		lReferenceIdHolder.className = 'referenceCitationId';
		lCitationsCountHolder.className = 'referenceCitationCount';
		
	}	
	
	this.divPopup.appendChild(lMatchHolderTable);
	this.divPopup.appendChild(document.createElement('br'));	
	
	var lButtonHolder = document.createElement('div');
	lButtonHolder.className = 'buttonHolder';
	lButtonHolder = this.divPopup.appendChild(lButtonHolder);
	
	var lCancelButton = document.createElement('input');	
	lCancelButton.setAttribute('type', 'submit');
	lCancelButton.value = 'Close';
	lCancelButton = lButtonHolder.appendChild(lCancelButton);
		
	this.addEvent('click', function(){this.hidePopup()}.bind(this), lCancelButton);	
	
	this.hideWaitingImage();
	this.displayPopup();
}
//Огражда в таг дадена част от autotag match-а
TextHolder.prototype.autotagAnnotate = function(pEvent, pMatchNum, pMatchPart){
	this.hideAutotagRightClickMenu();
	if( !pMatchPart || pMatchNum == "undefined" )
		return;
	var lEventTarget = TextHolder.getEventTarget(pEvent);
	if( !lEventTarget )
		return;
	var lTagName = lEventTarget.getAttribute('tag_name');
	if( !lTagName ){
		return;
	}
	var lReplacement = this.autotagReplacements[pMatchNum];
	var lReplacementDom;
	try{
		lReplacement = '<root ' + gNamespaceDeclarations + '>' + lReplacement + '</root>';
		lReplacementDom = EWorkaround.newDomDocument();
		lReplacementDom.loadXML(lReplacement);		
		var lMatchPartNode = lReplacementDom.selectSingleNode('//' + gAutotagMarkerNodeName + '[@' + gAutotagMarkerMatchNumberAttributeName + '=' + pMatchNum + ']' + '[@' + gAutotagMarkerMatchPartNumberAttributeName + '=' + pMatchPart + ']');
		if( lMatchPartNode ){
			var lHolderNode = lReplacementDom.createElement(lTagName);
			lHolderNode.appendChild(lMatchPartNode.cloneNode(true));
			lMatchPartNode.parentNode.replaceChild(lHolderNode, lMatchPartNode);
		}else{
			return;
		}
	}catch(e){
		alert(e);
		return;
	}
	var lReplacementXml = this.getReplacementXml(lReplacementDom);
	this.autotagReplacements[pMatchNum] = lReplacementXml;	
	this.displayAutotagReplacement( pMatchNum );
}

//Актуализира replacement-а за даден autotag match след като той е бил променен чрез autotagAnnotate
TextHolder.prototype.displayAutotagReplacement = function(pMatchNum){
	var lInput = document.getElementById(gAutotagReplacementInputIdPrefix + pMatchNum);
	if( lInput ){
		var lReplacement = this.parseReplacementXmlForDisplay(pMatchNum);
		lInput.value = lReplacement;
	}
	
}

TextHolder.prototype.autotagXML = function( pRuleId, pAutotagType, pDontCheckResult ){	
	this.showWaitingImage();	
	this.autotagCloneXml = EWorkaround.newDomDocument();
	this.autotagMatches = null;
	this.matchNumberOfResults = 0;
	this.flatMatches = null;
	this.ignoredMatches = new Array();
	
	if( !this.autotagCloneXml.loadXML(this.xmlDomDocument.xml) ){
		this.hideWaitingImage();
		alert('Could not send xml!');
		return false;
	}	
	var lDocType = gDocType;
	
	var lXml = lDocType + this.autotagCloneXml.xml;
	var lPostParams = {
		'id' : this.documentId,
		'rule_id' : pRuleId,
		'xml' : lXml,
		'autotag_type' : pAutotagType
	};
	
	var lRes = this.EDispetcher.Call('', -2, '', '', '', gAutotagSrv, false, null, null, null, lPostParams);
	
	if( lRes == '' ){
		this.hideWaitingImage();
		alert('Error while matching document!');
		return false;
	}
	try{
	//TO DO Da se napravi da e s JSON parser-a, a ne s eval
		lRes = eval('(' + lRes + ')');
		this.autotagRegularExpresions = lRes['re'];
		this.autotagMatches = lRes['match'];
		this.autotagRuleId = pRuleId;
	}catch(e){
		this.hideWaitingImage();
		alert('No matches found!');
		return false;
	}
	
	//~ this.matchNumberOfResults = this.getMatchResultsNumber(this.autotagMatches);
	this.autotagReplacements = new Array();
	this.flatMatches = this.flattenAutotagMatches(this.autotagMatches);	
	this.matchNumberOfResults = this.flatMatches.length;
	this.placeAutotagMarkers();//Слагаме маркери в xml-a, за да може да се ориентираме след като replace-нем 1-ят match; Освен това ако трябва да показваме къде в xml-a e match-a ще е по-лесно.	
	this.parseAutoMatch(0);
	this.hideWaitingImage();
	if( pDontCheckResult ){
		if( this.matchNumberOfResults == 0 ){
			alert('No matches found!');
			return false;
		}
		this.prepareAutotagReplacementsForDirectReplace();//Maha markerite ot replacement-ite
		this.replaceAutotagMatch(0);//Replace-va
	}else{
		this.displayAutoTagMatches();
	}
		
}

//Maha ot replacementite marker node-ovete poneje shte ima direkten replace bez check
TextHolder.prototype.prepareAutotagReplacementsForDirectReplace = function(){
	for( var i = 0; i < this.matchNumberOfResults; ++i ){
		this.autotagReplacements[i] = this.parseReplacementXmlForDisplay(i);
	}
}


TextHolder.prototype.displayAutoTagMatches = function( pPageNum ){	
	if(typeof pPageNum == "undefined" || !pPageNum)
		pPageNum = 0;
	if( this.matchNumberOfResults == 0 ){
		alert('No matches found!');
		return false;
	}
	this.initPopup();
	
	var lMainLabel = document.createElement('div');
	lMainLabel.className = 'label';
	lMainLabel.appendChild(document.createTextNode('Organize matches'));
	lMainLabel = this.divPopup.appendChild(lMainLabel);
	
	var lStartNum = pPageNum * gAutoTagMatchPageSize;
	if( !gAutoTagMatchPageSize || lStartNum > this.matchNumberOfResults )
		lStartNum = 0;
	var lEndNum = lStartNum + gAutoTagMatchPageSize;
	if( !gAutoTagMatchPageSize || lEndNum > this.matchNumberOfResults )
		lEndNum = this.matchNumberOfResults;
	
	
	var lMatchHolderTable = document.createElement('table');
	lMatchHolderTable.className = 'matchesTable';
	var lLabelTr = lMatchHolderTable.appendChild(document.createElement('tr'));
	var lNumLabel = lLabelTr.appendChild(document.createElement('th'));
	var lTextLabel = lLabelTr.appendChild(document.createElement('th'));
	var lRegExpLabel = lLabelTr.appendChild(document.createElement('th'));
	var lReplacementLabel = lLabelTr.appendChild(document.createElement('th'));
	var lInputLabel = lLabelTr.appendChild(document.createElement('th'));
	var lRemoveSimilarLabel = lLabelTr.appendChild(document.createElement('th'));
	
	lTextLabel.innerHTML = 'Matched text';
	lInputLabel.innerHTML = 'Remove match';
	lRegExpLabel.innerHTML = 'Regular expression';
	lReplacementLabel.innerHTML = 'Replacement';
	lNumLabel.innerHTML = 'Match #';
	lRemoveSimilarLabel.innerHTML = 'Check/Uncheck all matches from this rule';
	
	for ( var i = lStartNum; i < lEndNum; ++i){
		var lRowHolder = lMatchHolderTable.appendChild(document.createElement('tr'));
		var lNumHolder = lRowHolder.appendChild(document.createElement('td'));
		var lMatchedTextHolder = lRowHolder.appendChild(document.createElement('td'));		
		var lRegExpHolder = lRowHolder.appendChild(document.createElement('td'));
		var lReplacementHolder = lRowHolder.appendChild(document.createElement('td'));
		var lCheckboxHolder = lRowHolder.appendChild(document.createElement('td'));
		var lRemoveSimilarHolder = lRowHolder.appendChild(document.createElement('td'));
		
		var lRuleId = this.flatMatches[i][2];
		
		lCheckboxHolder.className = 'textAlignRight';
		
		var lCheckbox = lCheckboxHolder.appendChild(document.createElement('input'));
		var lCheckSimilarButton = lRemoveSimilarHolder.appendChild(document.createElement('button'));
		lRemoveSimilarHolder.appendChild(document.createElement('br'));
		var lUncheckSimilarButton = lRemoveSimilarHolder.appendChild(document.createElement('button'));
		var lReplacementInput = lReplacementHolder.appendChild(document.createElement('textarea'));		
		//~ lReplacementInput.value = this.autotagReplacements[i];
		lReplacementInput.value = this.parseReplacementXmlForDisplay(i);
		lRegExpHolder.innerHTML = this.autotagRegularExpresions[this.flatMatches[i][2]]['name'];
		lCheckSimilarButton.innerHTML = 'Check';
		lUncheckSimilarButton.innerHTML = 'Uncheck';
		
		lReplacementInput.setAttribute('id', gAutotagReplacementInputIdPrefix + i);//Slagame mu id za da moje posle da go update-vame lesno
		lReplacementHolder.className = 'replacementTd';
		lReplacementInput.className = 'coolinp replacementTextArea';
		lCheckSimilarButton.className = 'removeSimilarButton';
		lUncheckSimilarButton.className = 'removeSimilarButton';
		lCheckbox.setAttribute('type', 'checkbox');
		lCheckbox.setAttribute('rule_id', lRuleId);		
		lCheckSimilarButton.setAttribute('rule_id', lRuleId);
		lUncheckSimilarButton.setAttribute('rule_id', lRuleId);
		
		lReplacementInput.setAttribute('name', gMatchReplacementPrefix + i);
		lCheckbox.setAttribute('name', gMatchCheckboxPrefix + i);
		
		if( this.in_array( this.ignoredMatches, i)){
			lCheckbox.checked = true;
		}
		
		this.addEvent(
			'click', 
			function(event){
				this.UncheckSimilarMatches(event, lMatchHolderTable, 0) ;				
			}.bind(this), 
			lCheckSimilarButton
		);
		this.addEvent(
			'click', 
			function(event){
				this.UncheckSimilarMatches(event, lMatchHolderTable, 1) ;				
			}.bind(this), 
			lUncheckSimilarButton
		);
		
		lNumHolder.innerHTML = i + 1;
		lMatchedTextHolder.className = 'matchedText';
		
		//~ lMatchedTextHolder.innerHTML = this.flatMatches[i][0][0][0];
		var lMatchNode = this.autotagCloneXml.selectSingleNode('//' + gAutotagMarkerNodeName + '[@' + gAutotagMarkerMatchNumberAttributeName + '=\'' + i + '\']' + '[@' + gAutotagMarkerMatchPartNumberAttributeName + '=\'0\']');
		if( lMatchNode ){//Za vseki sluchai
			var lPreviousTextNode = this.getPreviousTextNode(lMatchNode);
			var lPreviousText = '';
			if( lPreviousTextNode ){
				var lPreviousText = lPreviousTextNode.textContent;
				while( lPreviousTextNode && lPreviousText.length <= gAutotagWrappingTextLength ){
					lPreviousTextNode = this.getPreviousTextNode(lPreviousTextNode);
					if( lPreviousTextNode )
						lPreviousText = lPreviousTextNode.textContent + lPreviousText
				}
			}
			var lNextText = '';
			var lNextTextNode = this.getNextTextNode(lMatchNode);
			if( lNextTextNode ){
				lNextText = lNextTextNode.textContent;
				while( lNextTextNode && lNextText.length <= gAutotagWrappingTextLength ){
					lNextTextNode = this.getPreviousTextNode(lNextTextNode);
					if( lNextTextNode )
						lNextText = lNextText + lNextTextNode.textContent;
				}
			}
			var lMatchText = this.displayAutoTagMatchText(lMatchNode);
			//~ var lTextContent = lPreviousText + '<b>' + lMatchNode.textContent + '</b>' + lNextText;
			//~ var lTextContent = lPreviousText + '<b>' + lMatchText.xml + '</b>' + lNextText;			
			//lMatchedTextHolder.innerHTML = lTextContent;
			lMatchedTextHolder.innerHTML = '';
			lMatchedTextHolder.appendChild(document.createTextNode(lPreviousText));
			lMatchedTextHolder.appendChild(lMatchText);
			lMatchedTextHolder.appendChild(document.createTextNode(lNextText));
			
			//~ lMatchedTextHolder.innerHTML = '<code>' + lMatchNode.xml + '</code>';
		}else{
			lMatchedTextHolder.innerHTML = this.flatMatches[i][0][0][0];
		}
		
	}	
	
	this.divPopup.appendChild(lMatchHolderTable);
	this.divPopup.appendChild(document.createElement('br'));	
	
	var lButtonHolder = document.createElement('div');
	lButtonHolder.className = 'buttonHolder';
	lButtonHolder = this.divPopup.appendChild(lButtonHolder);
	
	var lCancelButton = document.createElement('input');	
	lCancelButton.setAttribute('type', 'submit');
	lCancelButton.value = 'Cancel';
	
	var lProceedButton = document.createElement('input');	
	lProceedButton.setAttribute('type', 'submit');	
	lProceedButton.className = 'leftInput';
	
	var lPreviosButton = document.createElement('input');	
	lPreviosButton.setAttribute('type', 'submit');	
	lPreviosButton.className = 'leftInput';
	
	if( lEndNum < this.matchNumberOfResults ){//Move to next page
		lProceedButton.value = 'OK and move to next page';
		this.addEvent(
			'click', 
			function(){
				if( this.setIgnoredMatches(lMatchHolderTable) ){
					this.hidePopup(); 
					this.displayAutoTagMatches(pPageNum + 1);
				}
			}.bind(this), 
			lProceedButton
		);
	}else{//Move to next step
		lProceedButton.value = 'Replace matches';
		this.addEvent('click', 
			function(){
				if( this.setIgnoredMatches(lMatchHolderTable) ){
					this.hidePopup();
					this.showWaitingImage();	
					this.replaceAutotagMatch(0);
					this.hideWaitingImage();
				}				
			}.bind(this), 
			lProceedButton
		);
	}
	
	if( lStartNum > 0 ){//Move to previous page
		lPreviosButton.value = 'Previous page';
		this.addEvent('click', function(){this.hidePopup(); this.displayAutoTagMatches(pPageNum - 1);}.bind(this), lPreviosButton);
	}else{//Nothing
		lPreviosButton = null;
	}
	
	this.addEvent('click', function(){this.hidePopup()}.bind(this), lCancelButton);
	
	this.addEvent(
		'click', 
		function(event){
			this.hideAutotagRightClickMenu();				
		}.bind(this), 
		this.divPopupHolder
	);
	
	
	if( lPreviosButton )
		lPreviosButton = lButtonHolder.appendChild(lPreviosButton);
	lProceedButton = lButtonHolder.appendChild(lProceedButton);
	lCancelButton = lButtonHolder.appendChild(lCancelButton);
	
	
	

	
	this.displayPopup();	
	
}
//Display-va teksta ocveten i eventualno razlichnite 4asti kato se klikat shte moje da se tagvat
TextHolder.prototype.displayAutoTagMatchText = function(pMatchNode){
	if( !pMatchNode )
		return;
	var lMatchNumber = pMatchNode.getAttribute(gAutotagMarkerMatchNumberAttributeName);
	if( !lMatchNumber )
		return;	
	return this.buildAutotagMatchText(pMatchNode, null, lMatchNumber);
}

TextHolder.prototype.buildAutotagMatchText = function(pCurrentNode, pResultHolderNode, pMatchNumber){
	if( pCurrentNode.nodeName == gAutotagMarkerNodeName && pCurrentNode.getAttribute(gAutotagMarkerMatchNumberAttributeName) == pMatchNumber ){//Chastite na match-a trqbva da gi ocvetqvame i zatova gi slagame v span
		var lMatchPartSpan = document.createElement('span');
		var lMatchPartNum = pCurrentNode.getAttribute(gAutotagMarkerMatchPartNumberAttributeName);
		lMatchPartSpan.className = 'match_part_' + lMatchPartNum;
		lMatchPartSpan.setAttribute('match_part', lMatchPartNum);
		if( lMatchPartNum > 0 ){
			lMatchPartSpan.style.background = '#' + TextHolder.getNodeColour(gMatchPartColorStringHash.substring(0, lMatchPartNum + 6));
			this.addEvent(
				'contextmenu',
				function(event){					
					this.displayAutotagRightClickMenu(event, pMatchNumber);
				}.bind(this),
				lMatchPartSpan				
			);
		}
		if( pResultHolderNode ){
			pResultHolderNode = pResultHolderNode.appendChild(lMatchPartSpan);
		}else{
			pResultHolderNode = lMatchPartSpan;
		}
	}
	for( var i = 0; i < pCurrentNode.childNodes.length; ++i){
		var lCurrentChild = pCurrentNode.childNodes[i];
		if( lCurrentChild.nodeType == gXmlElementNodeType ){
			this.buildAutotagMatchText(lCurrentChild, pResultHolderNode, pMatchNumber);
		}else if(lCurrentChild.nodeType == gXmlTextNodeType){
			pResultHolderNode.appendChild(document.createTextNode(lCurrentChild.textContent));
		}
	}
	return pResultHolderNode;
}

TextHolder.prototype.placeAutotagMarkers = function(){	
	var lPreviousNodeIdx = 0;
	var lCurrentNode = false;
	var lCurrentNodeCustomText = '';	
	var lCurrentFormattingTags = new Array();
	var lCurrentPattern = '';
	for( var i = 0; i < this.matchNumberOfResults; ++i ){		
		if(this.in_array( this.ignoredMatches, i ))
			continue;
		var lRecalculateTextContent = false;
		var lMatch = this.flatMatches[i][0];
		var lNodeIdx = this.flatMatches[i][1];
		var lReId = this.flatMatches[i][2];	
		var lPattern = 	this.flatMatches[i][3];	
		if( lNodeIdx != lPreviousNodeIdx ){
			lPreviousNodeIdx = lNodeIdx;
			lCurrentNode = this.autotagCloneXml.selectSingleNode('//*[@' + gEtaAttributeName + '="' + lNodeIdx + '"]');			
			lRecalculateTextContent = true;
		}
		
		if( lCurrentPattern != lPattern ){
			lCurrentPattern = lPattern;
			lCurrentFormattingTags = this.getFormattingTagsFromRe(lCurrentPattern);			
			lRecalculateTextContent = true;
		}
		
		if( lRecalculateTextContent ){
			lCurrentNodeCustomText = this.getAutotagNodeCustomText(lCurrentNode, lCurrentFormattingTags);
		}
		
		
		if( !lCurrentNode )
			continue;
		for( var lMatchPart = 0; lMatchPart < lMatch.length; ++lMatchPart ){
			if( !lMatch[lMatchPart][0].length )
				continue;
			var lRealStartPos = this.calculateRealMatchLength(lCurrentNodeCustomText.substr(0, lMatch[lMatchPart][1]), lCurrentFormattingTags);			
			var lTextLength = this.calculateRealMatchLength(lMatch[lMatchPart][0], lCurrentFormattingTags);			
			if( !this.placeSingleAutotagMarker(lCurrentNode, lRealStartPos, lTextLength, i, lMatchPart, this.autotagRegularExpresions[lReId]['groupsupdepth'][lMatchPart] ) && lMatchPart == 0){
				/**
					Ако слагаме маркера на целия мач и сме в аутотаг правило за убио, и частта от мач-а е във възел tp:taxon-name,
					махаме мач-а от масива с мач-ове, намаляваме броя на масива с мачове с 1,
					намаляваме номера на текущия обработван мач за да можем да обработим следващия (номера му ще се смали с 1 и той ще стане равен на i)
					и излизаме от цикъла за слагане на маркерите на подмачовете на мач-а, който сме изтрили;
				*/
				this.fixAutotagIgnoredMatches(i);//Оправяме игнорираните мачове
				this.flatMatches.splice(i, 1);//Махаме мач-а
				--this.matchNumberOfResults;//Намаляваме броя на мачовете
				--i;//Намаляваме индекса на текущия мач
				break;//Спираме маркирането на подмачовете на вече изтрития мач
			}
		}
	}
}

/**
	След като сме премахнали даден мач от резултатите трябва да сменим индексите и на игнорираните мач-ове понеже се променят(намаляват с 1). Махаме и изтрития мач от този масив
*/
TextHolder.prototype.fixAutotagIgnoredMatches = function(pIndexRemoved){
	this.ignoredMatches = this.remove_array_value(this.ignoredMatches, pIndexRemoved);
	for( var i = 0; i < this.ignoredMatches.length; ++i ){
		if( this.ignoredMatches[i] < pIndexRemoved )
			continue;
		this.ignoredMatches[i] = this.ignoredMatches[i] - 1;
	}
}

//Връща текста от node-а като на местата в които почва таг от масива pReTagsArr добавя името му (напр. <bold>text</bold>, ако bold е в pReTagsArr - иначе ще върне само text)
TextHolder.prototype.getAutotagNodeCustomText = function(pNode, pReTagsArr){
	var lResult = '';
	if (pNode.hasChildNodes()) {		
		for(var i = 0; i <  pNode.childNodes.length; ++i ) {			
			var lItem = pNode.childNodes[i];
			if (lItem.nodeType == gXmlTextNodeType){ 
				lResult = lResult + lItem.nodeValue;				
			}else if (lItem.nodeType == gXmlElementNodeType)  {
				var lStartTag = '';
				var lEndTag = '';
				if ( this.in_array(pReTagsArr, lItem.nodeName) ) {
					lStartTag = "<" + lItem.nodeName + ">";
					lEndTag = "</" + lItem.nodeName + ">";
				}				
								
				lResult = lResult + lStartTag + this.getAutotagNodeCustomText(lItem, pReTagsArr) + lEndTag; 					
								
			}
		}
	} 
	return lResult;
}

//Връща масив с таговете, които участват в pRegularExpression
TextHolder.prototype.getFormattingTagsFromRe = function( pRegularExpression ){		
	var lPattern = '<[A-Z:_a-z][A-Z:_a-z0-9\-\.]*>';
	var lRegExp = new RegExp(lPattern, 'gim');
	//~ var lResult = lRegExp.exec(pRegularExpression);
	var lResult = pRegularExpression.match(lRegExp);//Pravime go prez string-a zashtoto inache ne raboti flaga g
	if( lResult ){
		for( var i = 0; i < lResult.length; ++i ){
			lResult[i] = lResult[i].slice(1, -1);//Mahame < i > 
		}
		return lResult;
	}
	return new Array();
}

//Връща дължината на стринга, в който са премахнати форматиращите тагове
TextHolder.prototype.calculateRealMatchLength = function( pText, pFormattingTagsArr ){
	lText = pText;
	for( var i = 0; i < pFormattingTagsArr.length; ++i){
		var lStartRE = new RegExp('</?' + pFormattingTagsArr[i].toLowerCase() + '>', 'gim');		
		//~ lText = lText.replace('<' + pFormattingTagsArr[i].toLowerCase() + '>');
		//~ lText = lText.replace('</' + pFormattingTagsArr[i].toLowerCase() + '>');
		lText = lText.replace(lStartRE, '');
	}
	return lText.length;	
}

TextHolder.prototype.getAutotagMatchNodeDetails = function( pNode, pOffset, pRefNode, pRefNodeOffset, pFollowToNextNode ){
	var lCurrentTextNode;
	if( !pRefNode ){
		lCurrentTextNode = this.getFirstTextNodeChild(pNode);
	}else{
		lCurrentTextNode = pRefNode;
	}
	var lCurrentOffset = pOffset;
	
	if( pRefNodeOffset )
		lCurrentOffset += pRefNodeOffset;
	var lCurrentTextLength = lCurrentTextNode.textContent.length;
	
	while( lCurrentTextNode && lCurrentOffset > 0){		
		if( lCurrentTextLength >= lCurrentOffset )
			break;
		lCurrentOffset = lCurrentOffset - lCurrentTextLength;
		lCurrentTextNode = this.getNextTextNode(lCurrentTextNode);
		if( lCurrentTextNode.textContent )
			lCurrentTextLength = lCurrentTextNode.textContent.length;
		else{
			//~ console.log(1);
			lCurrentTextLength = 0;
		}	
	}
	if( pFollowToNextNode ){//Ako sme na kraq na textov node - otivame do nachaloto na sledvashtiq textov node, koito ne e prazen
		while( lCurrentTextLength == lCurrentOffset ){
			lCurrentTextNode = this.getNextTextNode(lCurrentTextNode);
			lCurrentTextLength = lCurrentTextNode.textContent.length;
			lCurrentOffset = 0;
		}		
	}
	if( lCurrentTextNode ){
		return new Array(lCurrentTextNode, lCurrentOffset);
	}else{	
		return new Array(null, 0);
	}
}

/**
	Функцията трябва да прави така, че стартовият и крайният нод да са директни наследници на даден node. Освен това ако е възможно да се катери нагоре по дървото с 	
	максимално pUpMaxDepth нива
	Връща масив с node-овете и офсетите при успех и null при грешка
*/
TextHolder.prototype.correctAutotagNodes = function(pSearchNode, pStartNode, pStartOffset, pEndNode, pEndOffset, pUpMaxDepth){
	//StartNode и EndNode са текстови node-ове.!!!!Задължително
	if( pStartNode.parentNode != pEndNode.parentNode ){		
		var lCommonParent = this.getFirstCommonParent(pStartNode, pEndNode);
		if( pSearchNode != this.getFirstCommonParent(lCommonParent, pSearchNode) ){//Ako obshtiq parent e nad node-a, v koito se provejda tyrseneto - greshka 
			return null;
		}
		
		if( pStartOffset && lCommonParent != pStartNode.parentNode){//Ako sys start-a shte trqbva da se katerim nagore prez dyrvoto, a selectiona ne pochva ot nachaloto na tekstoviq node
			return null;
		}
		if( pEndOffset != pEndNode.textContent.length && lCommonParent != pEndNode.parentNode){//Ako sys end-a shte trqbva da se katerim nagore prez dyrvoto, a selectiona svyrshva predi kraq na tekstoviq node
			return null;
		}
		while( pStartNode.parentNode != lCommonParent ){//Korigirame start-a
			var lPrevSibling = pStartNode.previousSibling;
			while( lPrevSibling && !lPrevSibling.textContent.length ){
				lPrevSibling = lPrevSibling.previousSibling;
			}
			if( lPrevSibling ){//Ima node predi start-a, koito e sys tekst, a ne e selectnat
				return null;
			}
			pStartNode = pStartNode.parentNode;
		}
		while( pEndNode.parentNode != lCommonParent ){//Korigirame end-a
			var lNextSibling = pEndNode.nextSibling;
			while( lNextSibling && !lNextSibling.textContent.length ){
				lNextSibling = lNextSibling.nextSibling;
			}
			if( lNextSibling ){//Ima node sled end-a, koito e sys tekst, a ne e selectnat
				return null;
			}
			pEndNode = pEndNode.parentNode;
		}
	}
	
	//Veche sme si osigurili che start i end imat obsht parent - sega trqbva da gledame dali trqbva da se katerim nagore po dyrvoto za da obhvashtame po-golqm range
	while( pUpMaxDepth != 0 && pStartNode.parentNode != pSearchNode && pStartOffset == 0 && pEndOffset == pEndNode.textContent.length){
		/**
			Ako e nula spirame; 
			Ako stignem node-a v koito tyrsim - spirame;
			Ako e otricatelno se katerim kolkoto mojem; 
		*/
		var lPrevSibling = pStartNode.previousSibling;
		while( lPrevSibling && !lPrevSibling.textContent.length ){
			lPrevSibling = lPrevSibling.previousSibling;
		}
		var lNextSibling = pEndNode.nextSibling;
		while( lNextSibling && !lNextSibling.textContent.length ){
			lNextSibling = lNextSibling.nextSibling;
		}
		if( lPrevSibling || lNextSibling ){//Ima predhoden ili sledvasht node s tekst, koito ne e selectnat
			break;
		}
		pStartNode = pStartNode.parentNode;
		pEndNode = pStartNode;
		--pUpMaxDepth;
	}
	
	return new Array(pStartNode, pStartOffset, pEndNode, pEndOffset);

}

//Огражда с маркери частта в ноде-а с индекс pNodeIdx между позиции pStartPos и pEndPos
/**
	Връща true, ако огради частта в маркери.
	Връща false, ако текущото аутотаг правило е правилото за Ubio, и частта от мач-а е в node tp:taxon-name
*/
TextHolder.prototype.placeSingleAutotagMarker = function( pNode, pStartPos, pLength, pMatchNumber, pMatchPartNumber, pUpMaxDepth ){	
	if( !pNode )
		return true;	
	
	var lStartNodeDetails = this.getAutotagMatchNodeDetails(pNode, pStartPos, null, 0, 1);
	var lStartNode = lStartNodeDetails[0];//Tekstov node
	var lStartOffset = lStartNodeDetails[1];
	
	var lEndNodeDetails = this.getAutotagMatchNodeDetails(pNode, pLength, lStartNode, lStartOffset);
	var lEndNode = lEndNodeDetails[0];//Tekstov node
	var lEndOffset = lEndNodeDetails[1];
	
	var lNodesDetails = this.correctAutotagNodes(pNode, lStartNode, lStartOffset, lEndNode, lEndOffset, pUpMaxDepth);
	if( !lNodesDetails ){//Stanala e greshka zashtoto na teoriq php-to vryshta match-ove s obsht parent
		return true;
	}
	lStartNode = lNodesDetails[0];
	lStartOffset = lNodesDetails[1];
	lEndNode = lNodesDetails[2];
	lEndOffset = lNodesDetails[3];
	
	if( !lStartNode || !lEndNode )
		return true;
		
	var lParentNode = lStartNode.parentNode;
	
	if( lParentNode != lEndNode.parentNode ){//Stanala e greshka zashtoto na teoriq php-to vryshta match-ove s obsht parent
		//No za vseki sluchai se podsigurqvame
		return true;		
	}
	
	if( pMatchPartNumber == 0 && this.autotagRuleId == gUbioTaxonRuleId ){
		if( lStartNode.nodeType == gXmlElementNodeType && lStartNode.nodeName == gTaxonNodeName )//Ако стартовия възел е възел за таксон
			return false;
		if( lEndNode.nodeType == gXmlElementNodeType && lEndNode.nodeName == gTaxonNodeName )//Ако крайния възел е възел за таксон
			return false;
		var lTempParent = lParentNode;
		while( lTempParent ){//Ако някой от родителите е възел за таксон
			if( lTempParent.nodeType == gXmlElementNodeType && lTempParent.nodeName.toLowerCase() == gTaxonNodeName.toLowerCase() )
				return false;
			lTempParent = lTempParent.parentNode;
		}
	}
	
	
	var lReplacementNode = this.autotagCloneXml.createElement(gAutotagMarkerNodeName);
	lReplacementNode.setAttribute(gAutotagMarkerMatchNumberAttributeName, pMatchNumber);
	lReplacementNode.setAttribute(gAutotagMarkerMatchPartNumberAttributeName, pMatchPartNumber);
	if( lStartNode != lEndNode ){
		if( lStartNode.nodeType != gXmlTextNodeType || lStartOffset == 0 ){//Vzimame celiq node;
			lReplacementNode.appendChild(lStartNode.cloneNode(true));
		}else{
			var lTextContent = lStartNode.textContent;
			var lOuterText = this.autotagCloneXml.createTextNode(lTextContent.slice(0, lStartOffset));
			var lInnerText = this.autotagCloneXml.createTextNode(lTextContent.slice(lStartOffset));
			lParentNode.insertBefore(lOuterText, lStartNode);
			lReplacementNode.appendChild( lInnerText );
		}
		var lCurrentNode = lStartNode.nextSibling;
		while( lCurrentNode != lEndNode ){
			var lTempNode = lCurrentNode;
			lCurrentNode = lCurrentNode.nextSibling;
			lReplacementNode.appendChild( lTempNode.cloneNode(true) );
			lParentNode.removeChild(lTempNode);
		}
		if( lEndNode.nodeType != gXmlTextNodeType || lEndOffset == lEndNode.textContent.length ){//Dobavqme celiq node
			lReplacementNode.appendChild(lEndNode.cloneNode(true));
			lParentNode.removeChild(lEndNode);
		}else{
			var lTextContent = lEndNode.textContent;
			var lInnerText = this.autotagCloneXml.createTextNode(lTextContent.slice(0, lEndOffset));
			var lOuterText = this.autotagCloneXml.createTextNode(lTextContent.slice(lEndOffset));
			lParentNode.replaceChild(lOuterText, lEndNode);
			lReplacementNode.appendChild( lInnerText );
		}
		
	}else{//Cql node ili tekstov node ot chasti
		if( lStartNode.nodeType == gXmlTextNodeType ){//Razdelqme tekstoviq node na chasti
			var lTextContent = lStartNode.textContent;
			var lInnerText = this.autotagCloneXml.createTextNode(lTextContent.slice(lStartOffset, lEndOffset));
			lReplacementNode.appendChild( lInnerText );
			if( lStartOffset > 0 ){//Dobavqme startova chast izvyn node-a
				var lOuterText = this.autotagCloneXml.createTextNode(lTextContent.slice(0, lStartOffset));
				lParentNode.insertBefore(lOuterText, lStartNode);
			}
			if( lEndOffset < lEndNode.textContent.length ){//Dobavqme kraina chast izvyn node-a
				var lOuterText = this.autotagCloneXml.createTextNode(lTextContent.slice(lEndOffset));
				var lNextSibling = lStartNode.nextSibling ;
				if( lNextSibling ){
					lParentNode.insertBefore(lOuterText, lNextSibling);
				}else{
					lParentNode.appendChild(lOuterText);
				}
			}
		}else{//Slagame celiq node v konteinera
			lReplacementNode.appendChild( lStartNode.cloneNode(true) );
		}		
	}
	lParentNode.replaceChild(lReplacementNode, lStartNode);	
	return true;
}

TextHolder.prototype.parseAutoMatch = function( pMatchIdx ){
	//~ while( this.in_array( this.ignoredMatches, pMatchIdx ))
		//~ pMatchIdx = pMatchIdx + 1;
	if( pMatchIdx >= this.matchNumberOfResults ){//Обработили сме всички мачове
		//TO DO replace-ваме копието на XML-a; Трябва да преиндексираме и node-овете в хеш-овете
		//~ this.replaceAutotaggedXml();
		return true;
	}else{		
		//~ var lTemplate = this.autotagRegularExpresions[this.flatMatches[pMatchIdx][2]]['replacement'];
		var lTemplate = this.flatMatches[pMatchIdx][4];
		lTemplate = '<root ' + gNamespaceDeclarations + '>' + lTemplate + '</root>';//Slagame root-a za da moje da slagame po nqkolko node-a edin sled drug - primerno <taxon>a</taxon><taxon>b</taxon>
		var lTemplateDom = EWorkaround.newDomDocument();
		if( lTemplateDom.loadXML(lTemplate)){
			var lMatch = this.flatMatches[pMatchIdx][0];
			for( var i = 1; i < lMatch.length; ++i ){//Replacevame vsichki dolari, koito sa v atributi
				var lNodesWithAttributes = lTemplateDom.selectNodes('//*[@*=\'$' + i + '\']');
				for( var k = 0; k < lNodesWithAttributes.length; ++k){
					var lNode = lNodesWithAttributes[k];
					for( var j = 0; j < lNode.attributes.length; ++j ){
						var lAttribute = lNode.attributes.item(j);
						if( lAttribute.nodeValue == ('$' + i) ){
							lNode.setAttribute(lAttribute.nodeName, lMatch[i][0]);
						}						
					}
				}	
			}
			//To DO - parsevame template-a
			var lCurrentTextNode = this.getFirstTextNodeChild(lTemplateDom);
			var lRegExp = new RegExp('\\$([\\d]+)(?=$|[\\D])', 'm');
			var lMoveToFirstChildIfExists = false;
			while( lCurrentTextNode ){				
				var lTextContent = lCurrentTextNode.textContent;
				var lMatchResult = lRegExp.exec(lTextContent);
				while( lMatchResult ){
					var lMatchPart = lMatchResult[1];
					var lStartTextContent = RegExp.leftContext;
					var lEndTextContent = RegExp.rightContext;
					if( lMatchPart > 0 && lMatchPart < lMatch.length){//Trqbva da replace-vame
						var lReplaceNode = this.autotagCloneXml.selectSingleNode('//' + gAutotagMarkerNodeName + '[@' + gAutotagMarkerMatchNumberAttributeName + '=' + pMatchIdx + ']' + '[@' + gAutotagMarkerMatchPartNumberAttributeName + '=' + lMatchPart + ']');
						var lReplaceClone;
						if( lReplaceNode )
							lReplaceClone = lReplaceNode.cloneNode(true);
						if( lStartTextContent )
							lCurrentTextNode.parentNode.insertBefore(lTemplateDom.createTextNode(lStartTextContent), lCurrentTextNode);						
						if( !lEndTextContent ){//Slagame tekushtiq node da byde tekushto insertnatiq i produljavame natam
							if( lReplaceNode ){
								lCurrentTextNode.parentNode.replaceChild(lReplaceClone, lCurrentTextNode);
								lCurrentTextNode = lReplaceClone;
							}else{//chastta ot match-a e prazna - samo mahame dolara
								var lTempNode = lCurrentTextNode;
								lCurrentTextNode = this.getPreviousTextNode(lCurrentTextNode);
								lMoveToFirstChildIfExists = true;
								lTempNode.parentNode.removeChild(lTempNode);
							}
							break;
						}else{
							if( lReplaceNode )//chastta ot match-a e prazna - samo mahame dolara
								lCurrentTextNode.parentNode.insertBefore(lReplaceClone, lCurrentTextNode);
							var lAfterPart = lTemplateDom.createTextNode(lEndTextContent);
							lCurrentTextNode.parentNode.replaceChild(lAfterPart, lCurrentTextNode);
							lCurrentTextNode = lAfterPart;//Prodyljavame tyrseneto ot kraq na replacement-a						
						}
					}else{//Prodyljavame tyrseneto sled kraq na match-a
						if( !lEndTextContent ){//Otivame na sledvashtiq node
							break;
						}
						lStartTextContent = lStartTextContent + lMatchResult[0];
						lCurrentTextNode.parentNode.insertBefore(lTemplateDom.createTextNode(lStartTextContent), lCurrentTextNode);
						lCurrentTextNode = lCurrentTextNode.parentNode.replaceChild(lTemplateDom.createTextNode(lEndTextContent), lCurrentTextNode);
						
					}									
					lTextContent = lEndTextContent;
					lMatchResult = lRegExp.exec(lTextContent);
				}
				if( lCurrentTextNode ){
					lCurrentTextNode = this.getNextTextNode(lCurrentTextNode);
				}else{
					if( lMoveToFirstChildIfExists )
						lCurrentTextNode = this.getFirstTextNodeChild(lTemplateDom);					
				}
				lMoveToFirstChildIfExists = false;
			}			
			
			var lReplacementXml = this.getReplacementXml(lTemplateDom);
			
			this.autotagReplacements[pMatchIdx] = lReplacementXml;	
						
			//Smenqme v kloniraniq xml match-a s parsenatiq template
			//~ var lMatchNode = this.autotagCloneXml.selectSingleNode('//' + gAutotagMarkerNodeName + '[@' + gAutotagMarkerMatchNumberAttributeName + '=\'' + pMatchIdx + '\']' + '[@' + gAutotagMarkerMatchPartNumberAttributeName + '=\'0\']');
			//~ if( lMatchNode ){//Za vseki sluchai
				//~ var lMatchNodeParent = lMatchNode.parentNode;
				//~ lMatchNodeParent.replaceChild(lTemplateDom.childNodes[0], lMatchNode);
			//~ }
			
		}else{
			alert('Template for match#' + pMatchIdx + ' is not a valid XML! Continuing with next match!');
 		}
		this.parseAutoMatch(pMatchIdx + 1);
	}
		
}

TextHolder.prototype.getReplacementXml = function(pTemplateDom, pRemoveAutotagNodes){
	this.removeEtaAttribute(pTemplateDom);
	if( pRemoveAutotagNodes )
		this.removeAutotagNodes(pTemplateDom);				
	var lReplacementRoot = pTemplateDom.childNodes[0];
	var lReplacementXml = '';
	for( var i = 0; i < lReplacementRoot.childNodes.length; ++i){
		var lCurrentChild = lReplacementRoot.childNodes[i];
		if( lCurrentChild.nodeType == gXmlElementNodeType || lCurrentChild.nodeType == gXmlTextNodeType ){
			lReplacementXml = lReplacementXml + lCurrentChild.xml;
		}
	}
	return lReplacementXml;
}

TextHolder.prototype.parseReplacementXmlForDisplay = function(pMatchNum){
	var lReplacementXml = '<root ' + gNamespaceDeclarations + '>' + this.autotagReplacements[pMatchNum] + '</root>';
	var lReplacementDom = EWorkaround.newDomDocument();
	if( lReplacementDom.loadXML(lReplacementXml)){
		return this.getReplacementXml(lReplacementDom, 1);
	}
	return '';
}

TextHolder.prototype.replaceAutotagMatch = function(pMatchIdx){
	while( this.in_array( this.ignoredMatches, pMatchIdx ))
		pMatchIdx = pMatchIdx + 1;
	if( pMatchIdx >= this.matchNumberOfResults ){//Заменили сме всички тагове		
		this.replaceAutotaggedXml();
		return true;
	}
	var lTemplateDom = EWorkaround.newDomDocument();
	var lReplacementXml = '<root ' + gNamespaceDeclarations + '>' + this.autotagReplacements[pMatchIdx] + '</root>';
	if( lTemplateDom.loadXML(lReplacementXml)){
		var lMatchNode = this.autotagCloneXml.selectSingleNode('//' + gAutotagMarkerNodeName + '[@' + gAutotagMarkerMatchNumberAttributeName + '=\'' + pMatchIdx + '\']' + '[@' + gAutotagMarkerMatchPartNumberAttributeName + '=\'0\']');
		if( lMatchNode ){//Za vseki sluchai
			var lMatchNodeParent = lMatchNode.parentNode;
			var lReplacementRoot = lTemplateDom.childNodes[0];//Fake root-a koito sme slojili otgore
			for( var i = 0; i < lReplacementRoot.childNodes.length; ++i){
				var lCurrentChild = lReplacementRoot.childNodes[i];
				if( lCurrentChild.nodeType == gXmlElementNodeType || lCurrentChild.nodeType == gXmlTextNodeType ){
					lMatchNodeParent.insertBefore(lCurrentChild.cloneNode(true), lMatchNode);
				}				
			}
			//~ lMatchNodeParent.replaceChild(lTemplateDom.childNodes[0], lMatchNode);
			lMatchNodeParent.removeChild(lMatchNode);
		}
	}else{
		alert('Replacement for match#' + pMatchIdx + ' is not a valid XML! Continuing with next match!');
	}
	this.replaceAutotagMatch(pMatchIdx + 1);
}

TextHolder.prototype.setIgnoredMatches = function(pHtmlTable){
	if( !this.checkForValidReplacements(pHtmlTable) )//Tuk ne pazim stoinostta na poletata za replacement poneje moje neshto da ne e validno i posle nqma da mojem da annotirame v nego
		return false;
	var lInputs = pHtmlTable.getElementsByTagName('input');
	for( var i = 0; i < lInputs.length; ++i ){
		var lInputName = lInputs[i].getAttribute('name');
		lInputName = parseInt(lInputName.substr(gMatchCheckboxPrefix.length));
		if( lInputName >= 0 ){
			if( lInputs[i].checked ){
				if( !this.in_array( this.ignoredMatches, lInputName ) ){
					this.ignoredMatches.push(lInputName);
				}
			}else{
				this.remove_array_value(this.ignoredMatches, lInputName );				
			}
		}
	}
	this.checkForValidReplacements(pHtmlTable, 1);//Pravim go samo za da zapazim stoinostta ot poletata za replacement
	return true;
}

TextHolder.prototype.UncheckSimilarMatches = function(pEvent, pHtmlTable, pUncheck){
	var lInputs = pHtmlTable.getElementsByTagName('input');
	var lEventTarget = pEvent.target || pEvent.srcElement;	
	var lRuleId = lEventTarget.getAttribute('rule_id');
	for( var i = 0; i < lInputs.length; ++i ){
		var lInputRuleId = lInputs[i].getAttribute('rule_id');
		if(lInputRuleId == lRuleId ){
			if( pUncheck ){
				lInputs[i].checked = false;
			}else{
				lInputs[i].checked = true;
			}
		}		
	}
}

//Гледа дали заместванията са валидни xml-и
TextHolder.prototype.checkForValidReplacements = function(pHtmlTable, pSaveValue){
	var lInputs = pHtmlTable.getElementsByTagName('textarea');
	var lTemplateDom = EWorkaround.newDomDocument();
	var lResult = true;
	for( var i = 0; i < lInputs.length; ++i ){
		var lInputName = lInputs[i].getAttribute('name');
		lInputName = parseInt(lInputName.substr(gMatchReplacementPrefix.length));
		if( lInputName >= 0 ){
			var lReplacementXml = '<root ' + gNamespaceDeclarations + '>' + lInputs[i].value + '</root>';
			if( !lTemplateDom.loadXML(lReplacementXml)){
				lInputs[i].className = 'coolinp replacementTextArea errReplacement';
				alert('Replacement for match #' + (lInputName + 1) + ' is not a valid XML');
				lResult = false;
			}else{
				lInputs[i].className = 'coolinp replacementTextArea';
				if( pSaveValue )
					this.autotagReplacements[lInputName] = this.getReplacementXml(lTemplateDom, 1);
			}
		}
	}
	return lResult;
}

TextHolder.prototype.getMatchResultsNumber = function( pMatchArray ){
	var lResult = 0;
	try{
		for( var lNodeIdx in pMatchArray ){			
			for( var lExpression in pMatchArray[lNodeIdx] ){
				lResult += pMatchArray[lNodeIdx][lExpression].length;
			}
		}
		
	}catch(e){
		lResult = 0;
	}
	return lResult;
}
/**
	1 match ima slednata struktura
	lREID => (
		0 => MatchParts,
		1 => Index na node-a, kydeto e prilojen regexp-a
		2 => Id na regexp-a, koito e matchnal
		3 => Pattern na regexp-a -pazime go zaradi iztochnicite
		4 => Replacement na regexp-a - pazime go zaradi iztochnicite
	)
*/
TextHolder.prototype.flattenAutotagMatches = function( pMatchArray ){
	var lResult = new Array();
	try{
		lNodeLoop:
		for( var lNodeIdx in pMatchArray ){			
			lRELoop:
			for( var lExpressionId in pMatchArray[lNodeIdx] ){
				lREPatternLoop:
				for( var lPattern in pMatchArray[lNodeIdx][lExpressionId] ){
					lREReplacementLoop:
					for( var lReplacement in pMatchArray[lNodeIdx][lExpressionId][lPattern] ){
						var lMatchArr = pMatchArray[lNodeIdx][lExpressionId][lPattern][lReplacement];
						lMatchLoop:
						for( var i = 0; i < lMatchArr.length; ++i ){
							lResult.push( new Array(lMatchArr[i], lNodeIdx, lExpressionId, lPattern, lReplacement) );
						}
					}
				}
			}
		}
		
	}catch(e){
		lResult =  new Array();
	}
	return lResult;
}


TextHolder.prototype.replaceAutotaggedXml = function(){
	var lCurrentXML = this.xmlDomDocument;
	this.removeEtaAttribute(this.autotagCloneXml);
	this.removeAutotagNodes(this.autotagCloneXml);
	
	if(this.xmlDomDocument.loadXML(this.autotagCloneXml.xml)){
		this.nullifyData();
		this.loadTextAndSourceAndIndexXml();
	}else{		
		this.xmlDomDocument.loadXML(lCurrentXML);
		alert('Could not load autotagged xml - reverted back to untagged xml');
	}
}

TextHolder.escapeHtmlCode = function(pText){
	lResult = pText.replace(/</g, '&lt;');
	lResult = lResult.replace(/>/g, '&gt;');
	return lResult;
}

TextHolder.getEventTarget = function(pEvent){
	return pEvent.target || pEvent.srcElement;
}

function getTextAreaSelectionStart(pTextArea, pDocument){
	return pTextArea.selectionStart;
}

function getTextAreaSelectionEnd(pTextArea, pDocument){
	return pTextArea.selectionEnd;
}

function setTextAreaSelectionStart(pTextArea, pDocument, pStartPos){
	pTextArea.selectionStart = pStartPos;
}

function setTextAreaSelectionEnd(pTextArea, pDocument, pEndPos){
	pTextArea.selectionEnd = pEndPos;
}

function SetAreaSelectionRangeAndScroll(pTextArea, pDocument, pStartPos, pEndPos){
	if( _EWORKAROUND_MZ && !_EWORKAROUND_CH && !_EWORKAROUND_SF ){
	//Tova e samo za Mozilla poneje samo tam ne se scrollva textareata
		var lEvent = document.createEvent ('KeyEvents');
		setTextAreaSelectionStart(pTextArea, pDocument, pStartPos - 1);
		setTextAreaSelectionEnd(pTextArea, pDocument, pStartPos);
		lEvent.initKeyEvent('keypress', true, true, window, false, false, false, false, 0, pTextArea.value.charCodeAt(pStartPos-1));
		pTextArea.dispatchEvent(lEvent);
	}
	
	setTextAreaSelectionStart(pTextArea, pDocument, pStartPos);
	setTextAreaSelectionEnd(pTextArea, pDocument, pEndPos);
	pTextArea.focus();
}