/**
 * @package    Mapanese (https://github.com/danielrhodeswarp/Mapanese)
 * @copyright  Copyright (c) 2007 - 2011 Warp Asylum Ltd (UK).
 * @license    see LICENCE file in source code root folder     New BSD License
 */

//CAN COMPRESS SAFELY WITH http://dynamic-tools.net/toolbox/javascript_compressor/

//container for requestObject, callback function etc
function AjaxContainer()
{
	//properties
	this.requestObject;
	
	//methods
	this.initialize = function()	//create a request object
	{
		//Mozilla, Opera, Safari
		if(window.XMLHttpRequest)
		{
			this.requestObject = new XMLHttpRequest();
		}
		
		//IE
		else if(window.ActiveXObject)	//is there a version 3.0?
		{
			try	//use version 2.0
			{
				this.requestObject = new ActiveXObject('MSXML2.XMLHTTP');
			}
			
			catch(error)	//use the original
			{
				this.requestObject = new ActiveXObject('Microsoft.XMLHTTP');
			}
		}
	}
}

//event queue ("Ajax" with a capital "A" clashes with the Prototype library's Ajax class)
var a =
{
	//properties
	reaction : '',
	method : 'GET',	//Mozilla recommend uppercase*
	asynchronous : true,
	responseType : '',	//'text' or 'xml'
	ajaxContainers : {},	//hash
	counter : 0,
	server : '/ajax_control.php',	//Ajax server (relative from client URL)
	
	//methods
	jax : function(allParms)
	{
		var counter = a.counter;	//using 'this' in the inline function refers to a different context
		
		a.ajaxContainers[counter] = new AjaxContainer();
		
		a.ajaxContainers[counter].initialize();
		
		//manage optional parameters
		if(allParms['method'])
		{
			a.method = allParms['method'];
			delete allParms['method'];	//not needed by PHP
		}
		
		if(allParms['asynchronous'])
		{
			a.asynchronous = allParms['asynchronous'];
			delete allParms['asynchronous'];	//not needed by PHP
		}
		
		if(allParms['server'])
		{
			a.server = allParms['server'];
			delete allParms['server'];	//not needed by PHP
		}
		
		if(allParms['asynchronous'])
		{
			a.asynchronous = allParms['asynchronous'];
			delete allParms['asynchronous'];	//not needed by PHP
		}
		
		//manage essential parameters
		a.responseType = allParms['responseType'];
		
		a.reaction = allParms['reaction'];
		delete allParms['reaction'];	//not needed by PHP
		
		//stops the IE cache
		queryString = 'counter=' + counter;
		
		//add user parameters
		for(var item in allParms)
		{
			queryString += '&' + item + '=' + encodeURIComponent(allParms[item]);
		}
		
		//set reaction handler to this.reaction
		a.ajaxContainers[counter].requestObject.onreadystatechange = function()
		{
			if(a.ajaxContainers[counter].requestObject.readyState == 4)
			{
				if(a.ajaxContainers[counter].requestObject.status == 200)	//need?**
				{
					if(a.responseType == 'text')
					{
						var responseText = a.ajaxContainers[counter].requestObject.responseText;
						
						//call the set reaction handler
						a.reaction.call(this, responseText);
					}
					
					else if(a.responseType == 'xml')
					{
						var responseXML = a.ajaxContainers[counter].requestObject.responseXML;
						var documentElement = responseXML.documentElement;
						
						//CUSTOM DEFAULT ERROR HANDLER (display error then die)-----------
						if(documentElement.getElementsByTagName('ajaxerror').length > 0)
						{
							alert(documentElement.getElementsByTagName('ajaxerror')[0].firstChild.nodeValue);
							
							//remove this request object
							delete a.ajaxContainers[counter];
							
							return;
						}
						//end CUSTOM DEFAULT ERROR HANDLER (display error then die)-------
						
						//call the set reaction handler
						a.reaction.call(this, documentElement);
					}
					
					//if Behaviour library is loaded, reapply the rules
					//(because Ajax reactions often update / change the DOM)
					/*
					if(typeof Behaviour != 'undefined')
					{
						Behaviour.apply();
					}
					*/
					
					//remove this request object
					delete a.ajaxContainers[counter];
					
					return;
				}
			}
		};
		
		//go!
		if(a.method == 'GET')
		{
			//the third parameter is "asynchronous or not"
			a.ajaxContainers[counter].requestObject.open(a.method, a.server + '?' + queryString, a.asynchronous);
			var poop = a.ajaxContainers[counter].requestObject.send(null);
			
			
		}
		
		else if(a.method == 'POST')
		{
			//the third parameter is "asynchronous or not"
			a.ajaxContainers[counter].requestObject.open(a.method, a.server, a.asynchronous);
			
			a.ajaxContainers[counter].requestObject.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			//this.ajaxContainers[timestamp].requestObject.setRequestHeader('Content-length', queryString.length);
			//this.ajaxContainers[timestamp].requestObject.setRequestHeader('Connection', 'close');
			
			a.ajaxContainers[counter].requestObject.send(queryString);
		}
		
		a.counter = counter + 1;
	}
};

//* Because it's the HTTP standard
//** Mozilla tutorial has it but works fine without