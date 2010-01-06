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

// Return an FckEditor
function renderFckEditor(name, options)
{
	var defaults = {
			width: '100%',
			height: '100',
			fullPage: false,
			toolbarSet: 'Basic',
			toolbarExpanded: true,
			value: ''
	};
	
	var options = $.extend(defaults, options);
	
	var oFCKeditor = new FCKeditor(name);
	oFCKeditor.BasePath = getPath('WEB_PLUGIN_PATH') + 'html_editor/fckeditor/';
	oFCKeditor.Width = options.width;
	oFCKeditor.Height = options.height;
	oFCKeditor.Config[ "FullPage" ] = options.fullPage;
	oFCKeditor.Config[ "DefaultLanguage" ] = options.language ;
	if(options.value)
	{
		oFCKeditor.Value = options.value;
	}
	else
	{
		oFCKeditor.Value = "";
	}
	oFCKeditor.ToolbarSet = options.toolbarSet;
	oFCKeditor.Config[ "SkinPath" ] = oFCKeditor.BasePath + 'editor/skins/' + getTheme() + '/';
	oFCKeditor.Config["CustomConfigurationsPath"] = getPath('WEB_LIB_PATH') + 'configuration/html_editor/fckconfig.js';
	oFCKeditor.Config[ "ToolbarStartExpanded" ] = options.toolbarExpanded;
	
	return oFCKeditor.CreateHtml();
}