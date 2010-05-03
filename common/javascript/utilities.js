// Get a platform setting
function getPlatformSetting(variable, application)
{
	return getUtilities('platform_setting', { variable: variable, application: application }).platform_setting;
}

// Get a translation
function getTranslation(string, application)
{
	return getUtilities('translation', { string: string, application: application }).translation;
}

// Get a platform path
function getPath(path)
{
	return getUtilities('path', { path: path }).path;
}

// Get the current theme
function getTheme()
{
	return getUtilities('theme').theme;
}

// Get a memorized variable
function getMemory(variable)
{
	return getUtilities('memory', { action: 'get', variable: variable }).value;
}

// Set a memorized variable
function setMemory(variable, value)
{
	getUtilities('memory', { action: 'set', variable: variable, value: value });
}

// Clear a memorized variable
function clearMemory(variable)
{
	getUtilities('memory', { action: 'clear', variable: variable });
}

// General function to retrieve and process utilities-calls.
function getUtilities(type, parameters)
{
	var response;
	
	if (typeof parameters == "undefined")
	{
		parameters = new Object();
	}
	
	parameters.type = type;
	
	response = doAjaxPost("./common/javascript/ajax/utilities.php", parameters);
	return eval('(' + response + ')');
}

// Wrapper for an Ajax POST
function doAjaxPost(url, parameters)
{
	return doAjax("POST", url, parameters);
}

//Wrapper for an Ajax GET
function doAjaxGet(url, parameters)
{
	return doAjax("GET", url, parameters);
}

// Execute an Ajax postback
function doAjax(type, url, parameters)
{
	if (typeof parameters == "undefined")
	{
		parameters = new Object();
	}

	var response = $.ajax({
		type: type,
		url: url,
		data: parameters,
		async: false
	}).responseText;
	
	return response;
}

// Return an HTML Editor
function renderHtmlEditor(editorName, editorOptions, editorLabel, editorAttributes)
{
	var defaults = {
			"name": '',
			"label": '',
			"options": $.json.serialize({}),
			"attributes": $.json.serialize({})
	};
	
	var parameters = new Object();
	parameters.name = editorName;
	
	if (typeof editorOptions != "undefined")
	{
		parameters.options = $.json.serialize(editorOptions);
	}
	
	if (typeof editorAttributes != "undefined")
	{
		parameters.attributes = $.json.serialize(editorAttributes);
	}
	
	if (typeof editorLabel != "undefined")
	{
		parameters.label = editorLabel;
	}
	
	var ajaxParameters = $.extend(defaults, parameters);
	
	var result = doAjaxPost("./common/html/formvalidator/form_validator_html_editor_instance.php", ajaxParameters);
	
//	alert(result);
	
	return result;
}

// Destroy an HTML Editor
function destroyHtmlEditor(editorName)
{
	if ( typeof CKEDITOR != 'undefined' )
	{
		$('textarea.html_editor[name=\'' + editorName + '\']').ckeditorGet().destroy();
	}
	
	if ( typeof tinyMCE != 'undefined' )
	{
		$('textarea.html_editor[name=\'' + editorName + '\']').tinymce().destroy();
	}
}

//Popup window
function openPopup(url, width, height)
{
	width = width || '80%';
	height = height || '70%';

	if ( typeof width == 'string' && width.length > 1 && width.substr( width.length - 1, 1 ) == '%' )
		width = parseInt( window.screen.width * parseInt( width, 10 ) / 100, 10 );

	if ( typeof height == 'string' && height.length > 1 && height.substr( height.length - 1, 1 ) == '%' )
		height = parseInt( window.screen.height * parseInt( height, 10 ) / 100, 10 );

	if ( width < 640 )
		width = 640;

	if ( height < 420 )
		height = 420;

	var top = parseInt( ( window.screen.height - height ) / 2, 10 ),
		left = parseInt( ( window.screen.width  - width ) / 2, 10 ),
		options = 'location=no,menubar=no,toolbar=no,dependent=yes,minimizable=no,modal=yes,alwaysRaised=yes,resizable=yes,scrollbars=yes' +
		',width='  + width +
		',height=' + height +
		',top='  + top +
		',left=' + left;

	var popupWindow = window.open( '', null, options, true );

	// Blocked by a popup blocker.
	if ( !popupWindow )
		return false;

	try
	{
		popupWindow.moveTo( left, top );
		popupWindow.resizeTo( width, height );
		popupWindow.focus();
		popupWindow.location.href = url;
	}
	catch (e)
	{
		popupWindow = window.open( url, null, options, true );
	}
	
	return true;
}