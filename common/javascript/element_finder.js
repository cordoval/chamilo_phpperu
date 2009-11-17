/*
 *
 * Element Finder QuickForm element JavaScript part
 *
 * Does all sorts of elite tomfoolery. Play with it and be amazed.
 *
 * @author Tim De Pauw <tim,pwnt,be>
 *
 */

// TODO: Provide an alternative if AJAX isn't supported.
// TODO: Find out what breaks stuff in IE.

var ElementFinder = new Object();

ElementFinder.locale = new Array();
ElementFinder.locale['Searching'] = 'Searching ...';
ElementFinder.locale['NoResults'] = 'No results';
ElementFinder.locale['Error'] = 'Error';

ElementFinder.serializationSeparator = "\t";

ElementFinder.searchDelay = 500;

ElementFinder.ajaxMethods = new Array(
	function() { return new ActiveXObject("Msxml2.XMLHTTP") },
	function() { return new ActiveXObject("Microsoft.XMLHTTP") },
	function() { return new XMLHttpRequest() }
);

ElementFinder.ajaxMethodIndex = -1;

for (var i = 0; i < ElementFinder.ajaxMethods.length; i++) {
	try {
		ElementFinder.ajaxMethods[i]();
		ElementFinder.ajaxMethodIndex = i;
		break;
	} catch (e) { }
}

ElementFinder.searches = new Array();
ElementFinder.lastSearches = new Array();
ElementFinder.timeouts = new Array();
ElementFinder.selectedElements = new Array();
ElementFinder.excludedElements = new Array();

function ElementFinderSearch (url, origin, destination) {
	if (ElementFinder.searches[destination.getAttribute('id')]) {
		ElementFinder.searches[destination.getAttribute('id')].active = false;
	}
	ElementFinder.searches[destination.getAttribute('id')] = this;
	this.origin = origin;
	this.destination = destination;
	this.active = true;
	this.ajax = ElementFinder.getAjaxObject();
	this.emptyDestination();
	ElementFinder.notify('Searching', destination);
	var searchObject = this;
	this.ajax.onreadystatechange = function() {
		searchObject.readyStateChanged();
	};
	this.ajax.open("GET", url, true);
	this.ajax.send("");
}

ElementFinderSearch.prototype.readyStateChanged = function () {
	if (!this.active || this.ajax.readyState != 4) return;
	if (this.ajax.status == 200) {
		this.returnResults();
	}
	else {
		ElementFinder.notify('Error', this.destination);
	}
	ElementFinder.searches[this.destination.getAttribute('id')] = null;
};

ElementFinderSearch.prototype.returnResults = function () {
	var xml = this.ajax.responseXML;
	if (!xml) {
		ElementFinder.notify('Error', this.destination);
		return;
	}
	var root = ElementFinder.lastChild(xml);
	if (!root) {
		ElementFinder.notify('Error', this.destination);
		return;
	}
	var mainLeaf = ElementFinder.lastChild(root);
	if (mainLeaf) {
		this.emptyDestination();
		var ul = document.createElement('ul');
		this.destination.appendChild(ul);
		ElementFinder.buildResults(mainLeaf, ul, this.destination.getAttribute('id'));
		ul.className = "tree-menu";
		//ElementFinder.init(ul, -1);
	}
	else {
		ElementFinder.notify('NoResults', this.destination);
	}
};

ElementFinderSearch.prototype.emptyDestination = function () {
	ElementFinder.emptyNode(this.destination);
};

ElementFinder.emptyNode = function(node) {
	var children = node.childNodes;
	for (var i = 0; i < children.length; i++) {
		node.removeChild(children[i]);
	}
};

ElementFinder.notify = function(msgID, destination) {
	ElementFinder.emptyNode(destination);
	destination.appendChild(document.createTextNode(ElementFinder.locale[msgID]));
};

ElementFinder.lastChild = function(node) {
	if (!node || !node.childNodes || node.childNodes.length == 0) {
		return null;
	}
	var a = ElementFinder.filterTextNodes(node.childNodes);
	return (a.length == 0 ? null : a[a.length - 1]);
}

ElementFinder.buildResults = function(node, ul, destinationID) {
	var li = document.createElement('li');
	ul.appendChild(li);
	var div = document.createElement('div');
	li.appendChild(div);
	var a = document.createElement('a');
	a.appendChild(document.createTextNode(node.getAttribute('title')));
	a.setAttribute('href', 'javascript:void(0);');
	var className = node.getAttribute('class');
	if (className) {
		a.className = className;
	}
	div.appendChild(a);
	var ulSub = document.createElement('ul');
	li.appendChild(ulSub);
	var childNodes = ElementFinder.filterTextNodes(node.childNodes);
	for (var i = 0; i < childNodes.length; i++) {
		var child = childNodes[i];
		switch (child.nodeName) {
			case 'leaf':
				var title = child.getAttribute('title');
				var description = child.getAttribute('description');
				var id = child.getAttribute('id');
				var className = child.getAttribute('class');
				var li = document.createElement('li');
				var div = document.createElement('div');
				li.appendChild(div);
				var a = document.createElement('a');
				var aID = destinationID + '_' + id;
				a.setAttribute('id', aID);
				a.setAttribute('href', 'javascript:ElementFinder.toggleLinkSelectionState(document.getElementById("' + aID + '"), document.getElementById("' + destinationID + '"));');
				a.setAttribute('element', id);
				if (className) {
					a.className = className;
					a.setAttribute('extraClasses', className);
				}
				if (description) {
					a.setAttribute('title', description);
				}
				a.appendChild(document.createTextNode(title));
				div.appendChild(a);
				ulSub.appendChild(li);
				break;
			case 'node':
				ElementFinder.buildResults(child, ulSub, destinationID);
				break;
		}
	}
};

ElementFinder.toggleLinkSelectionState = function(link, destination) {
	ElementFinder.setLinkSelected(link, (link.getAttribute('selected') ? false : true), destination);
};

ElementFinder.setLinkSelected = function(link, selected, destination) {
	if (selected) {
		link.setAttribute('selected', 1);
		ElementFinder.addClassName(link, 'selected');
		if (destination) {
			ElementFinder.selectedElements[destination.getAttribute('id')] = ElementFinder.addToArray(ElementFinder.selectedElements[destination.getAttribute('id')], link);
		}
	}
	else {
		link.removeAttribute('selected');
		ElementFinder.removeClassName(link, 'selected');
		if (destination) {
			ElementFinder.selectedElements[destination.getAttribute('id')] = ElementFinder.removeFromArray(ElementFinder.selectedElements[destination.getAttribute('id')], link);
		}
	}
	if (destination) {
		var button = document.getElementById(destination.getAttribute('id')+'_button');
		button.disabled = (ElementFinder.selectedElements[destination.getAttribute('id')].length <= 0);
	}
};

ElementFinder.activate = function(inactive, active) {
	var toActivate = ElementFinder.selectedElements[inactive.getAttribute('id')];
	if (!toActivate || !toActivate.length) return;
	var hiddenElmt = document.getElementById(active.getAttribute('id')+'_hidden');
	var cached = ElementFinder.unserialize(hiddenElmt.getAttribute('value'));
	for (var j = 0; j < toActivate.length; j++) {
		var link = toActivate[j];
		var id = link.getAttribute('element');
		var label = link.firstChild.nodeValue;
		var description = link.getAttribute('title');
		var className = link.getAttribute('extraClasses');
		ElementFinder.activateElement(id, label, description, className, active);
		ElementFinder.setLinkSelected(link, false);
		ElementFinder.setLinkEnabled(link, false);
		cached = ElementFinder.addToArray(cached, new Array(id, (className ? className : ""), label, description));
	}
	hiddenElmt.setAttribute('value', ElementFinder.serialize(cached));
	toActivate.length = 0;
	document.getElementById(inactive.getAttribute('id')+'_button').disabled = true;
};

ElementFinder.deactivate = function(active, inactive) {
	var toDeactivate = ElementFinder.selectedElements[active.getAttribute('id')];
	if (!toDeactivate || !toDeactivate.length) return;
	var hiddenElmt = document.getElementById(active.getAttribute('id')+'_hidden');
	var cached = ElementFinder.unserialize(hiddenElmt.getAttribute('value'));
	for (var j = 0; j < toDeactivate.length; j++) {
		var link = toDeactivate[j];
		var id = link.getAttribute('element');
		ElementFinder.deactivateElement(id, active);
		var otherLink = document.getElementById(link.getAttribute('id').replace('_active_', '_inactive_'));
		if (otherLink) {
			ElementFinder.setLinkEnabled(otherLink, true);
		}
		cached = ElementFinder.removeFromCache(cached, id);
	}
	hiddenElmt.setAttribute('value', ElementFinder.serialize(cached));
	toDeactivate.length = 0;
	document.getElementById(active.getAttribute('id')+'_button').disabled = true;
};

ElementFinder.removeFromCache = function(cache, id) {
	if (!cache.length) {
		return cache;
	}
	var newCache = new Array();
	for (var i = 0; i < cache.length; i++) {
		if (cache[i][0] != id) {
			newCache[newCache.length] = cache[i];
		}
	}
	return newCache;
};

ElementFinder.activateElement = function(element, label, description, extraClasses, activeList) {
	var ul = activeList.firstChild;
	if (!ul) {
		ul = document.createElement('ul');
		activeList.appendChild(ul);
	}
	var li = document.createElement('li');
	var containerID = activeList.getAttribute('id');
	var aID = containerID + '_' + element;
	var a = document.createElement('a');
	a.setAttribute('id', aID);
	a.setAttribute('href', 'javascript:ElementFinder.toggleLinkSelectionState(document.getElementById("' + aID + '"),document.getElementById("' + containerID + '"));');
	if (description) {
		a.setAttribute('title', description);
	}
	if (extraClasses) {
		li.className = extraClasses;
	}
	a.setAttribute('element', element);
	a.appendChild(document.createTextNode(label));
	li.appendChild(a);
	ul.appendChild(li);
};

ElementFinder.deactivateElement = function(element, activeList) {
	var ul = activeList.firstChild;
	for (var i = 0; i < ul.childNodes.length; i++) {
		var el = ul.childNodes[i];
		if (el.firstChild.getAttribute('element') == element) {
			ul.removeChild(el);
			break;
		}
	}
};

ElementFinder.setLinkEnabled = function(link, enabled) {
	var href = link.getAttribute('href');
	var realHref = link.getAttribute('realHref');
	if (enabled) {
		if (realHref) {
			link.setAttribute('href', realHref);
			link.removeAttribute('realHref');
			ElementFinder.removeClassName(link, 'disabled');
		}
	}
	else if (href) {
		link.setAttribute('realHref', href);
		link.setAttribute('href', 'javascript:void(0);');
		ElementFinder.addClassName(link, 'disabled');
	}
};

ElementFinder.addToArray = function(array, element) {
	if (!array || !array.length) {
		var newArray = new Array();
		newArray[0] = element;
		return newArray;
	}
	if (ElementFinder.arrayContains(array, element)) {
		return array;
	}
	var newArray = ElementFinder.cloneArray(array);
	newArray[newArray.length] = element;
	return newArray;
};

ElementFinder.removeFromArray = function(array, element) {
	var newArray = new Array();
	if (!array || !array.length) return newArray;
	for (var i = 0; i < array.length; i++) {
		if (array[i] != element) {
			newArray[newArray.length] = array[i];
		}
	}
	return newArray;
};

ElementFinder.cloneArray = function(array) {
	var newArray = new Array();
	if (!array) return newArray;
	for (var i = 0; i < array.length; i++) {
		newArray[i] = array[i];
	}
	return newArray;
};

ElementFinder.arrayContains = function(array, element) {
	if (!array) return false;
	for (var i = 0; i < array.length; i++) {
		if (array[i] == element) {
			return true;
		}
	}
	return false;
};

ElementFinder.find = function(query, searchURL, origin, destination) {
	var destID = destination.getAttribute('id');
	query = ElementFinder.stripWhitespace(query);
	if (query.length > 0 && query == ElementFinder.lastSearches[destID]) {
		return;
	}
	if (ElementFinder.timeouts[destID]) {
		clearTimeout(ElementFinder.timeouts[destID]);
		ElementFinder.timeouts[destID] = null;
	}
	if (query.length == 0) {
		// Display everything if no search query was entered
		query = '*';
		//ElementFinder.emptyNode(destination);
		//return;
	}
	ElementFinder.lastSearches[destID] = query;
	ElementFinder.timeouts[destID] = setTimeout(function () {
		new ElementFinderSearch(searchURL + '?query=' + escape(query) + ElementFinder.excludeString(origin), origin, destination);
	}, ElementFinder.searchDelay);
};

ElementFinder.stripWhitespace = function(str) {
	if (str.length == 0) return str;
	var start;
	for (start = 0; start < str.length; start++) {
		var ch = str.charAt(start);
		if (ch != " " && ch != "\t") {
			break;
		}
	}
	if (start == str.length) return "";
	var end;
	for (end = str.length - 1; end >= 0; end--) {
		var ch = str.charAt(end);
		if (ch != " " && ch != "\t") {
			break;
		}
	}
	return str.substring(start, end + 1);
};

ElementFinder.excludeString = function(destination) {
	var destID = destination.getAttribute('id');
	var elmt = document.getElementById(destID+'_hidden');
	var chunks = ElementFinder.unserialize(elmt.getAttribute('value'));
	var str = '';
	for (var i = 0; i < chunks.length; i++) {
		str += "&exclude[]=" + chunks[i][0];
	}
	if (ElementFinder.excludedElements[destID]) {
		for (var i = 0; i < ElementFinder.excludedElements[destID].length; i++) {
			str += "&exclude[]=" + ElementFinder.excludedElements[destID][i];
		}
	}
	return str;
};

ElementFinder.restoreFromCache = function(hiddenElmt, active) {
	var cached = ElementFinder.unserialize(hiddenElmt.getAttribute('value'));
	for (var j = 0; j < cached.length; j++) {
		var id = cached[j][0];
		var className = cached[j][1];
		var title = cached[j][2];
		var description = cached[j][3];
		ElementFinder.activateElement(id, title, description, className, active);
	}
};

ElementFinder.serialize = function(elements) {
	if (!elements.length) {
		return "";
	}
	var re = new RegExp(ElementFinder.serializationSeparator, "g");
	var string = ElementFinder.subserialize(elements[0], re);
	for (var i = 1; i < elements.length; i++) {
		string += ElementFinder.serializationSeparator + ElementFinder.subserialize(elements[i], re);
	}
	return string;
};

ElementFinder.subserialize = function(element, re) {
	var string = element[0];
	for (var i = 1; i < element.length; i++) {
		string += ElementFinder.serializationSeparator + element[i].replace(re, " ");
	}
	return string;
};

ElementFinder.unserialize = function(string) {
	if (!string.length) {
		return new Array();
	}
	var start = 0;
	var end = string.indexOf(ElementFinder.serializationSeparator);
	var elements = new Array();
	var element = new Array();
	while (end >= 0) {
		element[element.length] = string.substring(start, end);
		if (element.length == 4) {
			elements[elements.length] = element;
			element = new Array();
		}
		start = end + 1;
		end = string.indexOf(ElementFinder.serializationSeparator, start);
	}
	element[element.length] = string.substring(start);
	elements[elements.length] = element;
	return elements;
};

ElementFinder.getAjaxObject = function() {
	return ElementFinder.ajaxMethods[ElementFinder.ajaxMethodIndex]();
};

ElementFinder.filterTextNodes = function(nodes) {
	var result = new Array();
	for (var i = 0; i < nodes.length; i++) {
		var node = nodes[i];
		if (node.nodeType != 3) {
			result[result.length] = node;
		}
	}
	return result;
};

ElementFinder.getElementsByClassName = function(tagName, className)
{
	var el = document.getElementsByTagName(tagName);
	var res = new Array();
	for (var i = 0; i < el.length; i++)
	{
		var elmt = el[i];
		if (ElementFinder.hasClassName(elmt, className))
		{
			res[res.length] = elmt;
		}
	}
	return res;
};

ElementFinder.addClassName = function(element, className)
{
	if (!ElementFinder.hasClassName(element, className))
	{
		var names = ElementFinder.getClassNames(element);
		names[names.length] = className;
		ElementFinder.setClassNames(element, names);
	}
	if (ElementFinder.requiresCssFix(className))
	{
		ElementFinder.ieCssFix(element);
	}
};

ElementFinder.removeClassName = function(element, className)
{
	var names = ElementFinder.getClassNames(element);
	var newNames = new Array();
	for (var i = 0; i < names.length; i++)
	{
		if (names[i] != className)
		{
			newNames[newNames.length] = names[i];
		}
	}
	ElementFinder.setClassNames(element, newNames);
	if (ElementFinder.requiresCssFix(className))
	{
		ElementFinder.ieCssFix(element);
	}
};

ElementFinder.hasClassName = function(element, className)
{
	return ElementFinder.arrayContains(ElementFinder.getClassNames(element), className);
};

ElementFinder.getClassNames = function(element)
{
	return (element && element.className ? element.className.split(/ +/) : new Array());
};

ElementFinder.setClassNames = function(element, classNames)
{
	if (!element) return;
	var n = "";
	for (var i = 0; i < classNames.length; i++)
	{
		n += classNames[i] + " ";
	}
	element.className = n;
};

ElementFinder.ieCssFix = function(element)
{
	ElementFinder.removeClassName(element, "last-empty");
	ElementFinder.removeClassName(element, "last-leaf");
	ElementFinder.removeClassName(element, "last-collapsed");
	var names = ElementFinder.getClassNames(element);
	if (ElementFinder.arrayContains(names, "last"))
	{
		if (ElementFinder.arrayContains(names, "empty"))
		{
			ElementFinder.addClassName(element, "last-empty");
		}
		else if (ElementFinder.arrayContains(names, "leaf"))
		{
			ElementFinder.addClassName(element, "last-leaf");
		}
		else if (ElementFinder.arrayContains(names, "collapsed"))
		{
			ElementFinder.addClassName(element, "last-collapsed");
		}
	}
};

ElementFinder.requiresCssFix = function(className)
{
	return (document.all && (className == "last" || className == "empty" || className == "leaf" || className == "collapsed"));
};

ElementFinder.arrayContains = function(haystack, needle)
{
	for (var i = 0; i < haystack.length; i++)
	{
		if (haystack[i] == needle)
		{
			return true;
		}
	}
	return false;
};