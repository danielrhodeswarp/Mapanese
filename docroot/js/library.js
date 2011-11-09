/**
 * @package    Mapanese (https://github.com/danielrhodeswarp/Mapanese)
 * @copyright  Copyright (c) 2007 - 2011 Warp Asylum Ltd (UK).
 * @license    see LICENCE file in source code root folder     New BSD License
 */

//CAN COMPRESS SAFELY WITH http://dynamic-tools.net/toolbox/javascript_compressor/

//Toggle display of block-level elements (assuming no set display is visible)
function toggle(elementId)
{
	var element = document.getElementById(elementId);
	
	if(element.style.display == 'none')
	{
		element.style.display = 'block';
	}
	
	else
	{
		element.style.display = 'none';
	}
}

//Opera cheesing up with this. (name/id confusion)
function focusOn(elementId)
{
	document.getElementById(elementId).focus();
}

function insertAtCursor(myField, myValue)
{
  //IE support
  if(document.selection)
  {
    myField.focus();
    sel = document.selection.createRange();
    sel.text = myValue;
  }
  //MOZILLA/NETSCAPE support
  else if(myField.selectionStart || myField.selectionStart == '0')
  {
    var startPos = myField.selectionStart;
    var endPos = myField.selectionEnd;
    myField.value = myField.value.substring(0, startPos)
                  + myValue
                  + myField.value.substring(endPos, myField.value.length);
  }
  
  else
  {
    myField.value += myValue;
  }
}

function getCrossBrowserEventTarget(event)
{
	var theEvent = event || window.event;
	
	var theTarget = theEvent.target || theEvent.srcElement;
	
	return theTarget;
}