
ajaxObjectsDescriptor = function(){
	/**
		Съдържа всички ajax обекти, като за всеки обект пази следните полета:
			req_objects - масив от обекти от които зависи текущия обект. Трябва първо те да са се заредили за да се покаже обекта
			temp_divid - временен див в който се пази резултата от ajax заявката. Използваме го за да инициализира евентуално подобекти на текущия
				обект, от които той зависи
			loaded - булева променлива, която показва дали обекта вече е зареден или чака обекти, от които зависи
			ajaxResult - резултата от ajax заявката за този обект
			callback - callback ф-я която да се извика след като обекта се зареди(т.е. след като се заредят всички обекти от които зависи)
		Един ajax обект се определя от името на див-а в който ще се върне, т.е. за 1 див не може да имаме повече от 1 заявка!!!
		
		TO DO  - може да се направи sequence за ajax oбектите и да се избяга от горното ограничение
	*/
	this.m_ajaxObjects = {};

	/**
		Хеш, който пази за всеки елемент от ajax заявките, елементите които зависят от него. Ползваме го за да може след като обекта
		се зареди да заредим обектите, които зависят от него
	*/
	this.m_objectIsRequiredIn = {}
}


/** Регистрира връзка между обект, и 2ри обект който зависи от 1я */
ajaxObjectsDescriptor.prototype.RegisterElementRequirement = function(pRequiredElementId, pParentElementId){
	if( this.m_ajaxObjects[pRequiredElementId] === null || this.m_ajaxObjects[pRequiredElementId] === undefined ){
		this.RegisterAjaxElement(pRequiredElementId, new Array(), null)
	}
	
	if( this.m_ajaxObjects[pParentElementId] === null || this.m_ajaxObjects[pParentElementId] === undefined ){
		this.RegisterAjaxElement(pParentElementId, new Array(), null)
	}
	
	if( this.m_objectIsRequiredIn[pRequiredElementId] === undefined || this.m_objectIsRequiredIn[pRequiredElementId] === null ){
		this.m_objectIsRequiredIn[pRequiredElementId] = new Array();
	}
	
	this.m_objectIsRequiredIn[pRequiredElementId].push(pParentElementId);
	this.m_ajaxObjects[pParentElementId]['req_objects'].push(pRequiredElementId);
}

/** Регистрира нов елемент за ajax заявка */
ajaxObjectsDescriptor.prototype.RegisterAjaxElement = function(pElementId, pRequiredObjectsToDisplay, pTempElementId, pCallBack){
	this.m_ajaxObjects[pElementId] = {
		'req_objects' :new Array(),
		'temp_divid': pTempElementId,
		'loaded':false,
		'ajaxResult':false,
		'callback':pCallBack
	}
	for( var i = 0; i < pRequiredObjectsToDisplay.length; ++i ){
		var lReqObject = pRequiredObjectsToDisplay[i];
		this.RegisterElementRequirement(lReqObject, pElementId);
	}
}

/** Функция, която се вика след като пристигне резултата от ajax заявката за даден обект.
	Гледа се дали всички обекти от които зависи обекта са готови и ако е така се сменя съдържанието на дива и се събуждат
	всички обекти, които зависят от този обект.
	В противен случай се запазва резултата от ajax заявката и се попълва съдържанието на temp див-а
*/
ajaxObjectsDescriptor.prototype.RegisterLoadedAjaxElement = function(pElementId, pAjaxResult){
	var lElementInfo = this.getElementInfo(pElementId);
	lElementInfo['ajaxResult'] = pAjaxResult;
	var lNewRequiredObjects = pAjaxResult['req_objects'];
	
	if( lNewRequiredObjects !== undefined && lNewRequiredObjects !== null ){
		for( var i =0 ; i < lNewRequiredObjects.length; ++i ){
			var lReqObject = lNewRequiredObjects[i];
			this.RegisterElementRequirement(lReqObject, pElementId);
		}
	}
	
	var lRequiredElementsAreLoaded = this.checkIfRequiredElementsAreLoaded(pElementId) ;
	
	if( lRequiredElementsAreLoaded ){//Vsi4ki obekti ot koito zavisi obekta sa gotovi
		lElementInfo['loaded'] = true;
		var lElement = document.getElementById(pElementId);
		this.replaceElementInnerHTML(lElement, pAjaxResult['result'], true);
		
		/** Vikame callback-a */
		this.executeElementCallback(pElementId);
		
		/** Budime obektite, koito zavisqt ot tekushtiq obekt */
		this.WakeUpAllParents(pElementId);
			
	}else{//Ima nezavyrshen obekt, ot koito zavisi tekushtiq obekt
		var lTempElementId = lElementInfo ['temp_divid'];
		var lElement = document.getElementById(lTempElementId);
		this.replaceElementInnerHTML(lElement, pAjaxResult['result'], true);
	}
}

/** Слага новия html, и ако е указано изпълнява js-a в него */
ajaxObjectsDescriptor.prototype.replaceElementInnerHTML = function(pElement, pNewHtml, pEvalInnerJs){
	if( pElement ){
		pElement.innerHTML = pNewHtml;
		if( pEvalInnerJs ){
			/** Izpylnqvame script-ove ot noviq HTML, ako ima takiva */
			
			var lScripts = pElement.getElementsByTagName("script");   
			for(var i=0; i<lScripts.length; i++) {  
				eval(lScripts[i].text);  
			}  
		}
	}
}

/** Връща информация за дадения обект */
ajaxObjectsDescriptor.prototype.getElementInfo = function(pElementId){
	return this.m_ajaxObjects[pElementId];
}

/** Връща обектите, които зависят от дадения обект */
ajaxObjectsDescriptor.prototype.getElementsRequiringElement = function(pElementId){
	return this.m_objectIsRequiredIn[pElementId];
}

/**   Функция, която се вика за обектите, които зависят от някой вече готов обект.
	Гледа дали всички елементи на обекта са готови и ако е така заменя съдържанието на главния див, вика callback ф-ята за обекта
	и вика себе си рекурсивно за всички обекти, зависещи от вече изпълнения обект.
*/
ajaxObjectsDescriptor.prototype.WakeUpParentElement = function(pElementId){
	var lElementInfo = this.getElementInfo(pElementId);
	if( lElementInfo['loaded'] == true )
		return;
	
	/** Gledame dali ima nezavyrshen obekt ot koito zavisi tekushtiq obekt */
	if( !this.checkIfRequiredElementsAreLoaded(pElementId) )
		return;
	lElementInfo['loaded'] = true;
	
	/** Smenqme sydyrjanieto na div-a s tova na temp div-a */
	var lTempElementId = lElementInfo['temp_divid'];
	var lTempElement = document.getElementById(lTempElementId);
	if( lTempElement ){
		var lElement = document.getElementById(pElementId);
		this.replaceElementInnerHTML(lElement, lTempElement.innerHTML, false);
		this.replaceElementInnerHTML(lTempElement, '', false);
	}
	
	/** Vikame callback-a */
	this.executeElementCallback(pElementId);
	
	/** Budime obektite, koito zavisqt ot tekushtiq obekt */
	this.WakeUpAllParents(pElementId);
}

/** Буди всички обекти, които зависят от дадения обект */
ajaxObjectsDescriptor.prototype.WakeUpAllParents = function(pElementId){
	var lElementIsReqIn = this.getElementsRequiringElement(pElementId);
	if( lElementIsReqIn !== null && lElementIsReqIn !== undefined ){
		for( var i = 0; i < lElementIsReqIn.length; ++i){
			this.WakeUpParentElement(lElementIsReqIn[i]);
		}	
	}
}

/** Гледа дали всички обекти от които зависи даден обект са заредени */
ajaxObjectsDescriptor.prototype.checkIfRequiredElementsAreLoaded = function(pElementId){
	var lElementInfo = this.getElementInfo(pElementId);
	var lRequiredElements = lElementInfo['req_objects'];
	
	/** Gledame dali ima nezavyrshen obekt ot koito zavisi tekushtiq obekt */
	for( var i = 0; i < lRequiredElements.length; ++i){
		var lRequiredObjectInfo = this.getElementInfo(lRequiredElements[i]);
		if( lRequiredObjectInfo['loaded'] == false ){
			return false;
		}
	}
	return true;
}

ajaxObjectsDescriptor.prototype.executeElementCallback = function(pElementId){
	var lElementInfo = this.getElementInfo(pElementId);
	var lCallBack = lElementInfo['callback'];
	if( lCallBack !== undefined && lCallBack  !== null ){
		lCallBack(pElementId, lElementInfo['ajaxResult']);
	}
}