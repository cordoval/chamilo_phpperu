/**
 * Copyright (c) 2009, Hans De Bisschop, conversion to seperate (non ui-tabs based) plugin
 */

(function($){
	$.fn.extend({ 
		elementselecter: function(options) {

			//Settings list and the default values
			var defaults = {
					name: '',
					search: '',
					nodesSelectable: false,
					loadElements: false,
					defaultQuery: ''
			};
			
			var settings = $.extend(defaults, options);
			var self = this, id, originalActivatedElements, activatedElements = new Array() , excludedElements,
				inactiveBox, activeBox;
			var timer;
			
			function setSelectedImage(imageProperties)
			{
				imageProperties = scaleDimensions(600, 450, imageProperties);
				
				$('input[name="' + settings.name + '"]').val(imageProperties.id);
				$('#selected_image').attr('src', imageProperties.webPath);
				$('#selected_image').css('width', imageProperties.thumbnailWidth + 'px');
				$('#selected_image').css('height', imageProperties.thumbnailHeight + 'px');
				$('#image_container').show();
				$('#image_select').hide();
			}
			
			function processSelectedImage(ev, ui)
			{
				ev.preventDefault();
				var contentObjectId = $(this).attr('id').replace('lo_', ''),
					imageProperties;
				
				imageProperties = doAjaxPost("./common/javascript/ajax/image_properties.php", { content_object: contentObjectId });
				imageProperties = eval('(' + imageProperties + ')');
				setSelectedImage(imageProperties);
			}
			
			function resetImage(ev, ui)
			{
				ev.preventDefault();
				$('input[name="' + settings.name + '"]').val('');
				$('#image_container').hide();
				$('#image_select').show();
			}
			
			function collapseItem(e) {
				$("ul:first", $(this).parent()).hide();
				if ($(this).hasClass("lastCollapse"))
				{
					$(this).removeClass("lastCollapse");
					$(this).addClass("lastExpand");
				}
				else if ($(this).hasClass("collapse"))
				{
					$(this).removeClass("collapse");
					$(this).addClass("expand");
				}
			}
			
			function expandItem(e) {
				$("ul:first", $(this).parent()).show();
				if ($(this).hasClass("lastExpand"))
				{
					$(this).removeClass("lastExpand");
					$(this).addClass("lastCollapse");
				}
				else if ($(this).hasClass("expand"))
				{
					$(this).removeClass("expand");
					$(this).addClass("collapse");
				}
			}
			
			function destroyTree()
			{
				$("div", self).removeClass("last");
				$("div", self).removeClass("collapse");
				$("div", self).removeClass("lastCollapse");
			}
			
			function processFinderTree()
			{
				destroyTree();
				$("ul li:last-child > div", self).addClass("last");
				$("ul li:last-child > ul", self).css("background-image", "none");
				
				$("ul li:not(:last-child):has(ul) > div", self).addClass("collapse");
				$("ul li:last-child:has(ul) > div", self).addClass("lastCollapse");
				
				$("ul li:has(ul) > div", self).toggle(collapseItem, expandItem);
				$("ul li:has(ul) > div > a", self).click(function(e){e.stopPropagation();});
			}
			
			function displayMessage(message, element)
			{
				element.html(message);
			};
			
			function getExcludedElements()
			{
				var elements = eval(settings.name + '_excluded');
				
				return elements;
			}
			
			function getSearchResults()
			{
				var query = $('#' + settings.name + '_search_field').val();
				
				var response = $.ajax({
					type: "GET",
					dataType: "xml",
					url: settings.search,
					data: { query: query, 'exclude[]': getExcludedElements() },
					async: false
				}).responseText;
				
				return response;
			}
			
			function buildElementTree(response)
			{
				var ul = $('<ul class="tree-menu"></ul>');
				
				var tree = $.xml2json(response, true);
				
				if((tree.node && $(tree.node).size() > 0) || (tree.leaf && $(tree.leaf).size() > 0))
				{
					if (tree.node && $(tree.node).size() > 0)
					{
						$.each(tree.node, function(i, the_node){
							var li = $('<li><div><a href="#" id="' + the_node.id + '" class="' + the_node.classes + '" title="' + the_node.description + '">' + the_node.title + '</a></div></li>');
							$(ul).append(li);
							buildElement(the_node, li);
						});
					}
					
					if (tree.leaf && $(tree.leaf).size() > 0)
					{
						$.each(tree.leaf, function(i, the_leaf){
							var li = $('<li><div><a href="#" id="' + the_leaf.id + '" class="' + the_leaf.classes + '" title="' + the_leaf.description + '">' + the_leaf.title + '</a></div></li>');
							$(ul).append(li);
						});
					}
					
					$(inactiveBox).html(ul);
				}
				else
				{
					displayMessage('No results', inactiveBox);
				}
			}
			
			function buildElement(the_node, element)
			{
				if((the_node.node && $(the_node.node).size() > 0) || (the_node.leaf && $(the_node.leaf).size() > 0))
				{
					var ul = $('<ul></ul>');
					$(element).append(ul);
					
					if (the_node.node && $(the_node.node).size() > 0)
					{
						$.each(the_node.node, function(i, a_node){
							var li = $('<li><div><a href="#" id="' + a_node.id + '" class="' + a_node.classes + '" title="' + a_node.description + '">' + a_node.title + '</a></div></li>');
							$(ul).append(li);
							buildElement(a_node, li);
						});
					}
					
					if (the_node.leaf && $(the_node.leaf).size() > 0)
					{
						$.each(the_node.leaf, function(i, a_leaf){
							var li = $('<li><div><a href="#" id="' + a_leaf.id + '" class="' + a_leaf.classes + '" title="' + a_leaf.description + '">' + a_leaf.title + '</a></div></li>');
							$(ul).append(li);
						});
					}
				}
			}
			
			function updateSearchResults()
			{
				var query = $('#' + settings.name + '_search_field').val();
				
				if (query.length === 0 && !settings.loadElements)
				{
					displayMessage('Please enter a search query', inactiveBox);
				}
				else
				{
					displayMessage('<div class="element_finder_loading"></div>', inactiveBox);
					var searchResults = getSearchResults();
					buildElementTree(searchResults);
					processFinderTree();
				}
			}
			
			function showElementFinder(e)
			{
				e.preventDefault();
				$(this).hide();
				$('#' + settings.name + '_collapse_button').show();
				$('#tbl_' + settings.name).show();
			}
			
			function hideElementFinder(e)
			{
				e.preventDefault();
				$(this).hide();
				$('#' + settings.name + '_expand_button').show();
				$('#tbl_' + settings.name).hide();
			}
			
			function init()
			{
				// Setup the actual image finder
				id = $(self).attr('id');
				inactiveBox = $('#elf_' + settings.name + '_inactive');
				
				if (settings.defaultQuery !== '')
				{
					$('#' + settings.name + '_search_field').val(settings.defaultQuery);
				}
				
				if (settings.loadElements)
				{
					updateSearchResults();
				}
				else
				{
					displayMessage('Please enter a search query', inactiveBox);
				}

				if (!settings.nodesSelectable)
				{
					$("a.category", inactiveBox).css("cursor", "default");
				}
				
				$('#' + settings.name + '_expand_button').click(showElementFinder);
				$('#' + settings.name + '_collapse_button').click(hideElementFinder);
				
				$('#' + settings.name + '_search_field').keypress( function(event) {
						// Avoid searches being started after every character
						clearTimeout(timer);
						timer = setTimeout(updateSearchResults, 750);
						
						if(event.keyCode == 13)
						{
							return false;
						}
					});
				
				$(this).bind('update_search', updateSearchResults);
				
				// Initialize the uploadify plugin
				$('#' + settings.name + '_uploadify').uploadify ({
					'uploader': getPath('WEB_LAYOUT_PATH') + getTheme() + '/plugin/jquery/uploadify2/uploadify.swf',
					'script': getPath('WEB_PATH') + 'common/javascript/ajax/upload_image.php',
					'cancelImg': getPath('WEB_LAYOUT_PATH') + getTheme() + '/plugin/jquery/uploadify2/cancel.png',
					'buttonText': getTranslation('Upload', 'repository').toUpperCase(),
					'folder': 'not_important',
					'auto': true,
					'scriptData': {'owner': getMemory('_uid')},
					onComplete: function (evt, queueID, fileObj, response, data)
					{
						imageProperties = eval('(' + response + ')');
						setSelectedImage(imageProperties);
					}
				});
				
				// Only show the selection options if no image was selected yet
				if ($('input[name="' + settings.name + '"]').val() == '')
				{
					$('#image_select').show();
				}
				
				// Process image selection
				$('a:not(.disabled, .category)', inactiveBox).live('click', processSelectedImage);
				
				// Allow selection of a different image
				$("#change_image").bind('click', resetImage);
			}
			
			return this.each(init);
    	}
	});
})(jQuery);
