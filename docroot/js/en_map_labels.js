/**
 * @package    Mapanese (https://github.com/danielrhodeswarp/Mapanese)
 * @copyright  Copyright (c) 2007 - 2011 Warp Asylum Ltd (UK).
 * @license    see LICENCE file in source code root folder     New BSD License
 */

//CAN COMPRESS SAFELY WITH http://dynamic-tools.net/toolbox/javascript_compressor/

//------------------------------------//
//English map labelling via the InfoBox class (http://code.google.com/p/google-maps-utility-library-v3/)
//------------------------------------//

//Load labels of all levels dynamically
//(and town labels only if the current viewport's town labels haven't already been loaded)

var MAX_LABELS_IN_MEMORY = 150;

var EKI_LEVEL_MIN_ZOOM = 10; 
//var EKI_LEVEL_MAX_ZOOM = 15;

var PREF_LEVEL_MIN_ZOOM = 5;
var PREF_LEVEL_MAX_ZOOM = 10;
var CITY_LEVEL_MIN_ZOOM = 10;
var CITY_LEVEL_MAX_ZOOM = 15;
var WARD_LEVEL_MIN_ZOOM = 11;
var WARD_LEVEL_MAX_ZOOM = 15;
var GUN_LEVEL_MIN_ZOOM = 10;
var GUN_LEVEL_MAX_ZOOM = 15;
var GUN_CHO_LEVEL_MIN_ZOOM = 12;
var GUN_CHO_LEVEL_MAX_ZOOM = 15;

var EMBED_MODE = false;

var TOWN_LEVEL_MIN_ZOOM = 15;
var VILLAGE_LEVEL_MIN_ZOOM = 15;
var BASHO_LEVEL_MIN_ZOOM = 15;

var LABEL_OPACITY = 100;

var mapForEnglishLabelling;
var useEnglishLabels;
var englishLabels = {};	//Overlays (ie. InfoBoxes) used for English labelling
var gotBounds = [];	//Array of already calculated town level bounds
var labellingCopyrightControl;
var globalKeepSuffixes = true;
var globalTotalUniqueLabelsLoaded = 0;

var messageColorPerMapType = {};
messageColorPerMapType.roadmap = 'black';
messageColorPerMapType.satellite = 'white';
messageColorPerMapType.hybrid = 'white';
messageColorPerMapType.terrain = 'black';

var linkColorPerMessageColor = {};
linkColorPerMessageColor.black = '';
linkColorPerMessageColor.white = 'style="color:white;"';

//<iframe> mode
function setEmbedMode()
{
	EMBED_MODE = true;
}

//Copy the passed mapObject over to a local variable?
function initializeEnglishMapHinting(mapObject, startOnOrOffSetting)
{
	//Set the global toggle
	useEnglishLabels = startOnOrOffSetting;
	
	//Add events to trigger a redraws/recalcs of the labels
	google.maps.event.addListener(mapObject, 'dragend', showEnglishLabels);
		//wee bit "flashy" to do it like this but we need to kill certain labels when zooming out so what the heck...
	google.maps.event.addListener(mapObject, 'zoom_changed', function(){hideEnglishLabels(); showEnglishLabels()});
	google.maps.event.addListener(mapObject, 'maptypeid_changed', changeLabellingCopyrightColour);
	
	
	mapForEnglishLabelling = mapObject;
	
	setEnglishLabels(startOnOrOffSetting);
}

function changeLabellingCopyrightColour()
{
	if(!useEnglishLabels)
	{
		return;
	}
	
	var currentType = mapForEnglishLabelling.getMapTypeId();
	mapForEnglishLabelling.controls[google.maps.ControlPosition.BOTTOM_LEFT].pop();	//safe?
	labellingCopyrightControl = getControlForCopyright(messageColorPerMapType[currentType]);
	mapForEnglishLabelling.controls[google.maps.ControlPosition.BOTTOM_LEFT].push(labellingCopyrightControl);
}

//
function setLevelSuffixes(setting)
{
	globalKeepSuffixes = setting;
	
	//Refresh
	hideEnglishLabels();
	showEnglishLabels();
}

function setEnglishLabels(setting)
{
	useEnglishLabels = setting;
	
	if(!useEnglishLabels)
	{
		hideEnglishLabels();
		mapForEnglishLabelling.controls[google.maps.ControlPosition.BOTTOM_LEFT].pop();	//safe?
	}
	
	else
	{
		showEnglishLabels();
		var currentType = mapForEnglishLabelling.getMapTypeId();
		labellingCopyrightControl = getControlForCopyright(messageColorPerMapType[currentType]);
		mapForEnglishLabelling.controls[google.maps.ControlPosition.BOTTOM_LEFT].push(labellingCopyrightControl);
	}
}



function hideEnglishLabels()
{
	for(var item in englishLabels)
	{
		englishLabels[item].close();
	}
	
	englishLabels = {};
	globalTotalUniqueLabelsLoaded = 0;
}

function showEnglishLabels()
{
	if(!useEnglishLabels)
	{
		return;
	}

	//Reset if more than MAX_LABELS_IN_MEMORY labels in memory
	if(globalTotalUniqueLabelsLoaded > MAX_LABELS_IN_MEMORY)
	{
		hideEnglishLabels();
	}
	
	var zoom = mapForEnglishLabelling.getZoom();
	
	if(zoom < PREF_LEVEL_MIN_ZOOM)
	{
		return;
	}
	
	var bounds = mapForEnglishLabelling.getBounds();
	var sw = bounds.getSouthWest();
	var ne = bounds.getNorthEast();
	
	//---------------------
	/*
	if(zoom >= EKI_LEVEL_MIN_ZOOM)
	{
		showLabels('getVisibleEkiLabels', sw.lat(), sw.lng(), ne.lat(), ne.lng());
	}
	*/
	//-----------------------
	
	if(zoom >= PREF_LEVEL_MIN_ZOOM && zoom <= PREF_LEVEL_MAX_ZOOM)
	{
		showLabels('getVisiblePrefectureLabels', sw.lat(), sw.lng(), ne.lat(), ne.lng());
	}
	
	
	if(zoom >= CITY_LEVEL_MIN_ZOOM && zoom <= CITY_LEVEL_MAX_ZOOM)
	{
		showLabels('getVisibleShiLabels', sw.lat(), sw.lng(), ne.lat(), ne.lng());
	}
	
	if(zoom >= WARD_LEVEL_MIN_ZOOM && zoom <= WARD_LEVEL_MAX_ZOOM)
	{
		showLabels('getVisibleKuLabels', sw.lat(), sw.lng(), ne.lat(), ne.lng());
	}
	
	if(zoom >= GUN_LEVEL_MIN_ZOOM && zoom <= GUN_LEVEL_MAX_ZOOM)
	{
		showLabels('getVisibleGunLabels', sw.lat(), sw.lng(), ne.lat(), ne.lng());
	}
	
	if(zoom >= GUN_CHO_LEVEL_MIN_ZOOM && zoom <= GUN_CHO_LEVEL_MAX_ZOOM)
	{
		showLabels('getVisibleGunChoLabels', sw.lat(), sw.lng(), ne.lat(), ne.lng());
	}
	
	if(zoom >= TOWN_LEVEL_MIN_ZOOM)
	{
		/*
		//hmmmm, probably should use SOME overlap here...
		//gotBounds check
		var gotBoundsLength = gotBounds.length;
		for(loop = 0; loop < gotBoundsLength; loop++)
		{
			if(bounds.intersects(gotBounds[loop]))
			{
				return;
			}
		}
	
		gotBounds.push(bounds);	
		*/
			
		showLabels('getVisibleChoLabels', sw.lat(), sw.lng(), ne.lat(), ne.lng());
	}
	
	if(zoom >= VILLAGE_LEVEL_MIN_ZOOM)
	{
		showLabels('getVisibleSonLabels', sw.lat(), sw.lng(), ne.lat(), ne.lng());
		
	}
	
	
	if(zoom >= BASHO_LEVEL_MIN_ZOOM)
	{
		showLabels('getVisibleBashoLabels', sw.lat(), sw.lng(), ne.lat(), ne.lng());
		
	}
	
}

function showLabels(the_job, the_swLat, the_swLng, the_neLat, the_neLng)
{
	a.jax({job:the_job, method:'GET', responseType:'xml', reaction:showLabelsReaction, swLat:the_swLat, swLng:the_swLng, neLat:the_neLat, neLng:the_neLng});
}

function showLabelsReaction(responseXml)
{
	//var startTime = new Date();
	//startTime = startTime.getTime();
	
	
	
	var labels = responseXml.getElementsByTagName('label');
	
	var labelsLength = labels.length;	//Loop optimization
	for(var loop = 0; loop < labelsLength; loop++)
	{
		//Will a textual splitting be faster?
		var id = labels[loop].getAttribute('id');
		var text = labels[loop].getElementsByTagName('text')[0].firstChild.nodeValue;
		var lat = labels[loop].getElementsByTagName('lat')[0].firstChild.nodeValue;
		var lng = labels[loop].getElementsByTagName('lng')[0].firstChild.nodeValue;

		var styleText = labels[loop].getElementsByTagName('style')[0].firstChild.nodeValue;
		eval('var style = ' + styleText + ';');	//slow??
		//var style = {backgroundColor:'#ffa'};		
		style.opacity = LABEL_OPACITY;
		
		var theClass = labels[loop].getAttribute('class');	//InfoBox not support this anyway :-(
			
				
		if(!englishLabels[id])
		{
			globalTotalUniqueLabelsLoaded++;
			
			if(!globalKeepSuffixes)
			{
				text = removeSuffixes(text);
			}
			
			//InfoBox initialize and add
			
			//css = theClass
			
			var myOptions = {
                 content: text
                ,disableAutoPan: true
                ,maxWidth: 0
                ,pixelOffset: new google.maps.Size(0, -14)                     
                ,zIndex: null
                ,position: new google.maps.LatLng(lat, lng)
                ,boxStyle: style
                //,closeBoxMargin: "10px 2px 2px 2px"
                ,closeBoxURL: ''
                ,infoBoxClearance: new google.maps.Size(1, 1)
                ,isHidden: false
                ,pane: 'mapPane'
                ,enableEventPropagation: true
        };

			
			englishLabels[id] = new InfoBox(myOptions);
        	englishLabels[id].class = theClass;
        	englishLabels[id].open(mapForEnglishLabelling);
        	
		}
	}
	
	
	
	
	//var endTime = new Date();
	//endTime = endTime.getTime();
	//alert(endTime - startTime);
}

//
function removeSuffixes(text)
{
	//May have two parts with a comma
	var parts = text.split(',');
	
	for(var loop = 0; loop < parts.length; loop++)
	{
		parts[loop] = parts[loop].replace(/[-][^-]+$/, '');
	}
	
	return parts.join(',');
}

//
function toggleTransparentLabels(transparent)
{
	if(transparent)
	{
		LABEL_OPACITY = 50;
	}
	
	else
	{
		LABEL_OPACITY = 100;
	}
	
	//Refresh
	hideEnglishLabels();
	showEnglishLabels();
}

//Custom controls from here----------------

function getControlForCopyright(colour)
{
	var container = document.createElement('div');
	container.style.fontSize = 'smaller';
	container.style.color = colour;
	
	if(EMBED_MODE)	//Embed mode not properly finished (or started!)
	{
		//We use a crunch-safe space here
		container.innerHTML = 'Labels &copy;' + new Date().getFullYear() + ' <a href="http://' + document.location.host + '" target="_blank"' + String.fromCharCode(32) + linkColorPerMessageColor[colour] + '>Mapanese</a>';
	}
	
	else
	{
		container.appendChild(document.createTextNode('English labelling ©' + String.fromCharCode(32) + new Date().getFullYear() + ' Mapanese'));
	}
	
	return container;
}