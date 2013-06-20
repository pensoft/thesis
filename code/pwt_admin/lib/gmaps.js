var map;
var gCoordinates;
function initCoordinates(pData){
	gCoordinates = eval('(' + pData + ')');
	//~ gCoordinates = {
		//~ 'Equator':[0, 0],
		//~ 'Asd':[31.4140833333,19.1573833333],
		//~ 'Asd2':[-23.363882,131.044922]
	//~ };	
}

function initialize(pData) {
	try{	
		initCoordinates(pData);
		lObjects = new Array();	
		var myOptions = {
			zoom: 2,		
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);    
		
		j = 0;
		for( i in gCoordinates ){
			var lCoordArray = gCoordinates[i];
			lObjects[j] = new google.maps.LatLng(lCoordArray['latitude'],lCoordArray['longitude']);
			var marker = new google.maps.Marker({
				position: lObjects[j], 
				map: map, 
				title:i,
				object:lObjects[j]
			});
			google.maps.event.addListener(marker, 'click', function() {
				map.setCenter(this.object);
				map.setZoom(6);
			});
			if( j++ == 0 ){
				map.setCenter(lObjects[0]);			
			}
		}
	}catch(e){
		alert(e);
	}
}