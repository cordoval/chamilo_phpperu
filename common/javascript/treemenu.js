/**
 *
 * Tree Menu script
 *
 * Turns all UL elements that have "tree-menu" as their class name into nice
 * tree menus.
 *
 * Here's a trivial example:
 *
 * <ul class="tree-menu">
 *   <li>
 *     <a href="category1.html">Category 1</a>
 *     <ul>
 *       <li>
 *         <a href="category1_1.html">Category 1.1 (empty)</a>
 *         <ul><li></li></ul>
 *       </li>
 *       <li>
 *         <a href="document1.html">Document 1</a>
 *       </li>
 *     </ul>
 *   </li>
 *   <li>
 *     <a href="category2.html">Category 2 (empty)</a>
 *   </li>
 * </ul>
 *
 * @author Tim De Pauw <tim,pwnt,be>
 *
 */

var TreeMenu = new Object();

TreeMenu.className = "tree-menu";
TreeMenu.collapseLevel = 1;

TreeMenu.initAll = function()
{
	var trees = TreeMenu.getElementsByClassName("ul", TreeMenu.className);
	for (var i = 0; i < trees.length; i++)
	{
		TreeMenu.init(trees[i]);
	}
};

TreeMenu.init = function(tree, collapseLevel)
{
	tree.style.visibility = "hidden";
	if (collapseLevel == null)
	{
		collapseLevel = TreeMenu.collapseLevel;
	}
	var activeNodes = new Array();
	TreeMenu.walkTree(tree, 0, collapseLevel, activeNodes);
	for (var i = 0; i < activeNodes.length; i++)
	{
		TreeMenu.expandNode(activeNodes[i], true);
	}
	tree.style.visibility = "visible";
};

TreeMenu.walkTree = function(tree, level, collapseLevel, activeNodes)
{
	var children = TreeMenu.filterTextNodes(tree.childNodes);
	var hasChildren = false;
	for (var i = 0; i < children.length; i++)
	{
		var child = children[i];
		if (child.tagName.toLowerCase() == "li")
		{
			if (i == children.length - 1)
			{
				TreeMenu.addClassName(child, "last");
			}
			if (TreeMenu.isRootNode(child))
			{
				TreeMenu.addClassName(child, "root");
			}
			var validChild = TreeMenu.parseNode(child, level + 1, collapseLevel, activeNodes);
			if (validChild)
			{
				hasChildren = true;
			}
			if (TreeMenu.hasClassName(child, "current"))
			{
				activeNodes[activeNodes.length] = child;
			}
			else if (collapseLevel >= 0 && level >= collapseLevel)
			{
				//TreeMenu.collapseNode(child);
			}
		}
	}
	return hasChildren;
};

TreeMenu.parseNode = function(node, level, collapseLevel, activeNodes)
{
	var children = TreeMenu.filterTextNodes(node.childNodes);
	// 0 = leaf, 1 = empty node, 2 = node with children
	var type = 0;
	var link;
	for (var i = 0; i < children.length; i++)
	{
		var child = children[i];
		switch (child.tagName.toLowerCase())
		{
			case "a":
				link = child;
				break;
			case "ul":
				var hasChildren = TreeMenu.walkTree(child, level, collapseLevel, activeNodes);
				if (hasChildren)
				{
					type = 2;
				}
				else
				{
					node.removeChild(child);
					type = 1;
				}
				if (TreeMenu.isLastNode(node))
				{
					TreeMenu.addClassName(child, "last");
				}
				break;
		}
	}
	switch (type)
	{
		case 0:
			TreeMenu.addClassName(node, "leaf");
			break;
		case 1:
			TreeMenu.addClassName(node, "empty");
			break;
	}
	if (link)
	{
		TreeMenu.wrapInDiv(link, hasChildren, TreeMenu.hasClassName(node, "current"));
		return true;
	}
	return false;
};

TreeMenu.expandOrCollapse = function(node)
{
	if (TreeMenu.isCollapsed(node))
	{
		TreeMenu.expandNode(node);
	}
	else
	{
		TreeMenu.collapseNode(node);
	}
};

TreeMenu.expandNode = function(node, climbUp)
{
	TreeMenu.removeClassName(node, "collapsed");
	if (climbUp && !TreeMenu.isRootNode(node)
	&& node && node.parentNode && node.parentNode.parentNode)
	{
		TreeMenu.expandNode(node.parentNode.parentNode, true);
	}
};

TreeMenu.collapseNode = function(node)
{
	TreeMenu.addClassName(node, "collapsed");
};

TreeMenu.isCollapsed = function(node)
{
	return TreeMenu.hasClassName(node, "collapsed");
};

TreeMenu.isRootNode = function(node)
{
	return (node && node.parentNode ? TreeMenu.hasClassName(node.parentNode, TreeMenu.className) : false);
};

TreeMenu.isLastNode = function(node)
{
	return TreeMenu.hasClassName(node, "last");
};

TreeMenu.wrapInDiv = function(link, collapsible, isCurrent)
{
	var div = document.createElement("div");
	var linkID = link.getAttribute('id');
	var oldOnclick = link.onclick;
	link.removeAttribute('id');
	var copy = link.cloneNode(true);
	copy.setAttribute('id', linkID);
	copy.onclick = function (e) {
		if (!e) e = window.event;
		e.cancelBubble = true;
		this.blur();
		return (oldOnclick ? oldOnclick(e) : true);
	};
	if (isCurrent)
	{
		div.className = "current";
	}
	div.appendChild(copy);
	var parent = link.parentNode;
	parent.replaceChild(div, link);
	if (TreeMenu.hasClassName(parent, "last"))
	{
		div.className = (div.className ? div.className + " last" : "last");
	}
	if (collapsible)
	{
		div.onclick = function (e) {
			TreeMenu.expandOrCollapse(parent);
		};
	}
};

TreeMenu.filterTextNodes = function(nodes) {
	var result = new Array();
	for (var i = 0; i < nodes.length; i++) {
		var node = nodes[i];
		if (node.nodeType != 3) {
			result[result.length] = node;
		}
	}
	return result;
};

TreeMenu.getElementsByClassName = function(tagName, className)
{
	var el = document.getElementsByTagName(tagName);
	var res = new Array();
	for (var i = 0; i < el.length; i++)
	{
		var elmt = el[i];
		if (TreeMenu.hasClassName(elmt, className))
		{
			res[res.length] = elmt;
		}
	}
	return res;
};

TreeMenu.addClassName = function(element, className)
{
	if (!TreeMenu.hasClassName(element, className))
	{
		var names = TreeMenu.getClassNames(element);
		names[names.length] = className;
		TreeMenu.setClassNames(element, names);
	}
	if (TreeMenu.requiresCssFix(className))
	{
		TreeMenu.ieCssFix(element);
	}
};

TreeMenu.removeClassName = function(element, className)
{
	var names = TreeMenu.getClassNames(element);
	var newNames = new Array();
	for (var i = 0; i < names.length; i++)
	{
		if (names[i] != className)
		{
			newNames[newNames.length] = names[i];
		}
	}
	TreeMenu.setClassNames(element, newNames);
	if (TreeMenu.requiresCssFix(className))
	{
		TreeMenu.ieCssFix(element);
	}
};

TreeMenu.hasClassName = function(element, className)
{
	return TreeMenu.arrayContains(TreeMenu.getClassNames(element), className);
};

TreeMenu.getClassNames = function(element)
{
	return (element && element.className ? element.className.split(/ +/) : new Array());
};

TreeMenu.setClassNames = function(element, classNames)
{
	if (!element) return;
	var n = "";
	for (var i = 0; i < classNames.length; i++)
	{
		n += classNames[i] + " ";
	}
	element.className = n;
};

TreeMenu.ieCssFix = function(element)
{
	TreeMenu.removeClassName(element, "last-empty");
	TreeMenu.removeClassName(element, "last-leaf");
	TreeMenu.removeClassName(element, "last-collapsed");
	var names = TreeMenu.getClassNames(element);
	if (TreeMenu.arrayContains(names, "last"))
	{
		if (TreeMenu.arrayContains(names, "empty"))
		{
			TreeMenu.addClassName(element, "last-empty");
		}
		else if (TreeMenu.arrayContains(names, "leaf"))
		{
			TreeMenu.addClassName(element, "last-leaf");
		}
		else if (TreeMenu.arrayContains(names, "collapsed"))
		{
			TreeMenu.addClassName(element, "last-collapsed");
		}
	}
};

TreeMenu.requiresCssFix = function(className)
{
	return (document.all && (className == "last" || className == "empty" || className == "leaf" || className == "collapsed"));
};

TreeMenu.arrayContains = function(haystack, needle)
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

TreeMenu.addOnloadFunction = function(f)
{
	if (window.onload)
	{
		var oldOnload = window.onload;
		window.onload = function (e) {
			oldOnload(e);
			f();
		};
	}
	else
	{
		window.onload = f;
	}
}

TreeMenu.addOnloadFunction(TreeMenu.initAll);