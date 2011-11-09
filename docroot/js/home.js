/**
 * @package    Mapanese (https://github.com/danielrhodeswarp/Mapanese)
 * @copyright  Copyright (c) 2007 - 2011 Warp Asylum Ltd (UK).
 * @license    see LICENCE file in source code root folder     New BSD License
 */

//CAN COMPRESS SAFELY WITH http://dynamic-tools.net/toolbox/javascript_compressor/

var globalMap;
var globalGeocoder;
var venueIndicatorOverlays = [];	//overlay(s) for indicating the result(s)

function getCheckedType()
{
	var allInputs = document.getElementsByTagName('input');
	
	var allInputsLength = allInputs.length;	//Loop optimization
	for(var loop = 0; loop < allInputsLength; loop++)
	{
		if(allInputs[loop].type == 'radio' && allInputs[loop].name == 'type' && allInputs[loop].checked)
		{
			return allInputs[loop].value;
		}
	}
}

function loadHashState()
{
	var state = {};	//Load state into here
	
	var hashWithNoHash = window.location.hash.substring(1, window.location.hash.length);
	var parts = hashWithNoHash.split('&');
	
	var partsLength = parts.length;	//Loop optimization
	for(var loop = 0; loop < partsLength; loop++)
	{
		var sides = parts[loop].split('=');
		
		state[sides[0]] = sides[1];
	}
	
	if(state.type)
	{
		document.getElementById('type_' + state.type).checked = true;
	}
	
	if(state.q)
	{
		document.getElementById('q').value = state.q;
		ajaxSubmit();
	}
}

function ajaxSubmit()
{
	//Change URL?
	//document.location += '?q=' . document.getElementById('q').value; 
	
	var the_type = getCheckedType();	//Get the radio value
	
	
	var the_q = document.getElementById('q').value;
	
	//
	//window.location.hash = 'q=' + q + '&type=' + type;
	
	
	removeAllResultsOverlays();	
	
	a.jax({job:'search', method:'GET', responseType:'text', reaction:ajaxSubmitReaction, q:the_q, type:the_type});
	return false;
}

function ajaxSubmitReaction(responseJson)
{
	//var addresses = responseXml.getElementsByTagName('ja_address');

	eval('var addresses = ' + responseJson + ';');
	
	showResultLocation(addresses);
	
	//without this, if we are zoomed in and showing labels in, say, Okinawa
	//then we do a search for "gifu", we shoot to gifu
	//at the current zoom level but with NO labels (because labels are 
	//as a rule redisplayed on only drag / zoom events)
	//showEnglishLabels();
		//NOT WORKING HERE DUE TO EVENTS / TIMING ETC
}



function loadGoogleMaps(addressesToGeocode)
{
	var latlng = new google.maps.LatLng(39.2322531417148, 138.548828125);
	var myOptions =
	{
		zoom: 5,
		center: latlng,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};

	globalMap = new google.maps.Map(document.getElementById('map'), myOptions);
	
	globalGeocoder = new google.maps.Geocoder();
	
	
	//Enable English map hinting and default to OFF? (ON is best!)
	initializeEnglishMapHinting(globalMap, false);
	
	
	//need this for when the query is in the actual URL
	if(addressesToGeocode != null)
	{
		showResultLocation(addressesToGeocode);
	}
	
	
	
}


function removeAllResultsOverlays()
{
	var venueIndicatorOverlaysLength = venueIndicatorOverlays.length;
	for(loop = 0; loop < venueIndicatorOverlaysLength; loop++)
	{
		venueIndicatorOverlays[loop].setMap(null);
	}
	
	venueIndicatorOverlays = [];

}






var globalNumberOfResults = 0;

function showResultLocation(addresses)
{
	removeAllResultsOverlays();
	
	
	
	
	globalNumberOfResults = 0;
	for(var thing in addresses)
	{
		globalNumberOfResults++;
	}
	//alert(globalNumberOfResults + ' results from PHP/DB');
	
	
	
	
	
	venueIndicatorOverlays = [];	//new Array(addressesLength);

	//for(var loop = 0; loop < addressesLength; loop++)
	for(var itemy in addresses)
	{
	
		globalGeocoder.geocode
		(
			{address:itemy, language:'ja', region:'jp'},
			function(results, status)
			{
				if(status != google.maps.GeocoderStatus.OK)
				{
					//What to do?
				}
	  						
				else	//show ONLY THE FIRST returned result for the passed address
				{
					
					//alert(results.length + ' results from Google geocode');
					
					
					globalMap.setCenter(results[0].geometry.location);
					
					var thisItemy = itemy;	//need to set thisItemy to original and unique geocode query
					
					var theMarker = new google.maps.Marker({map:globalMap, position:results[0].geometry.location, title:addresses[thisItemy]});
					
					venueIndicatorOverlays.push(theMarker);
					
					//Google sometimes prepends '日本, ' to ja lang results
	    			var jaWithoutNihon = results[0].formatted_address.replace('日本, ', '');
		    		var infoWindowHtml = '<strong>en (from Mapanese)</strong><br/>' + addresses[thisItemy] + '<br/><br/><strong>ja (from Mapanese)</strong><br/>' + thisItemy + '<br/><br/><strong>ja (from Google)</strong><br/>' + jaWithoutNihon;	
						
					//Ensmallen for iframe embed mode
					if(EMBED_MODE)
					{
						infoWindowHtml = '<div style="font-size:smaller; line-height:0.85em;">' + infoWindowHtml + '</div>';
					}
					
					theMarker.infowindow = new google.maps.InfoWindow({content:infoWindowHtml});
			
					//We need to use "this" to get round a scope/referencing problem (-_-)a
					google.maps.event.addListener(theMarker, 'click', function(){this.infowindow.open(globalMap, this)});
				}
			}
		);
	}
	
	
	
}





//Needs to be more cross-browser
function convertNewlineToCommaIfPaste(event)
{
	target = event.target || window.event.srcElement;
	
	//CTRL-V
	if(event.keyCode == '86' && event.ctrlKey)
	{
		target.value = target.value.replace(/\n/g, ', ');
	}
	
	//SHIFT-INSERT
	
}

function linkToThisPage()
{
	var url = document.location.protocol + '//' + document.location.host + '/?q=' + encodeURIComponent(document.getElementById('q').value) + '&type=' + getCheckedType();
	
	document.getElementById('link_to_this_page').innerHTML = '<input id="link_to_this_page_input" onclick="this.select();" type="text" size="30" value="'+url+'">';
	
	//document.getElementById('link_to_this_page_input').focus();
	document.getElementById('link_to_this_page_input').select();
}

function removeLinkToThisPage()
{
	document.getElementById('link_to_this_page').innerHTML = '';
}