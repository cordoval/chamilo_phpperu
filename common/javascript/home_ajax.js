/*global $, addBlock, bindIcons, bindIconsLegacy, blocksDraggable, tabsDroppable, columnsResizable, columnsSortable, confirm, document, editTab, filterComponents, getLoadingBox, getMessageBox, handleLoadingBox, jQuery, showAllComponents, tabsSortable */

$(function () {
	
	var columns = $(".column");
	
	function translation(string, application) {		
		var translated_string = $.ajax({
			type: "POST",
			url: "./common/javascript/ajax/translation.php",
			data: { string: string, application: application },
			async: false
		}).responseText;
		
		return translated_string;
	}
	
	function checkForEmptyColumns() {
		var emptyBlock  = '<div class="empty_column">';
		emptyBlock += translation('EmptyColumnText', 'home');
		emptyBlock += '<div class="deleteColumn"></div>';
		emptyBlock += '</div>';
		
		$("div.tab div.column").each(function (i) {
			var numberOfBlocks, emptyBlockExists;
			numberOfBlocks = $(".block", this).length;
			emptyBlockExists = $(".empty_column", this).length;
			
			if (numberOfBlocks === 0 && emptyBlockExists === 0)
			{
				$(this).append(emptyBlock);
			}
			else if (numberOfBlocks > 0 && emptyBlockExists >= 1)
			{
				$(".empty_column", this).remove();
			}
		});
		
		bindIconsLegacy();
	}
	
	function sortableStart(e, ui) {
		ui.helper.css("border", "4px solid #c0c0c0");
	}
	
	function sortableBeforeStop(e, ui) {
		ui.helper.css("border", "0px solid #c0c0c0");
	}
	
	function sortableStop(e, ui) {
		// Fade the action links / images
		$("div.title a").fadeOut(150);
		checkForEmptyColumns();
	}
	
	function showTab(e, ui) {
		e.preventDefault();
		var tabId, tab;
		tabId = $(this).attr('id');
		tab = tabId.split("_");
		
		$("div.tab:not(#tab_" + tab[2] + ")").css('display', 'none');
		$("div #tab_" + tab[2]).css('display', 'block');
		
		$("#tab_menu li").attr('class', 'normal');
		$("#tab_select_" + tab[2]).attr('class', 'current');
		
		$("li.current a.deleteTab").css('display', 'inline');
		$("li.normal a.deleteTab").css('display', 'none');
		
		tabsDroppable();
	}

	function sortableUpdate(e, ui) {
		var column, order;
		column = $(this).attr("id");
		order = $(this).sortable("serialize");

		$.post("./home/ajax/block_sort.php", {
			column : column,
			order : order
		}// ,
				// function(data){alert("Data Loaded: " + data);}
				);
	}
	
	function tabsSortableUpdate(e, ui) {
		var order = $(this).sortable("serialize");
		
		$.post("./home/ajax/tab_sort.php", {
			order : order
		} //,
				 //function(data){alert("Data Loaded: " + data);}
				);
	}

	function resizableStop(e, ui) {
		var columnId, rowId, countColumns, widthBox, widthRow, widthPercentage, widthCurrentTotal, widthSurplus;
		
		columnId = $(this).attr("id");
		rowId = $(this).parent().attr("id");
		countColumns = $("div.column", $(this).parent()).length;

		widthBox = $(this).width();
		widthRow = $(this).parent().width();
		widthPercentage = (widthBox / widthRow) * 100;
		widthPercentage = widthPercentage.toFixed(0);

		widthCurrentTotal = 0;

		$("#" + rowId + " div.column").each(function (i) {
			var curWidthBox, curWidthPercentage;
			curWidthBox = $(this).width();
			curWidthPercentage = (curWidthBox / widthRow) * 100;
			curWidthPercentage = parseInt(curWidthPercentage.toFixed(0), 10);

			widthCurrentTotal = widthCurrentTotal + curWidthPercentage;
		});

		widthCurrentTotal = widthCurrentTotal + countColumns - 1;

		if (widthCurrentTotal > 100) {
			widthSurplus = widthCurrentTotal - 100;

			widthPercentage = widthPercentage - widthSurplus;
			widthBox = ((widthRow / 100) * widthPercentage) - 1;
		}

		$(this).css('width', widthPercentage + "%");

		$.post("./home/ajax/column_width.php", {
			column : columnId,
			width : widthPercentage
		}// ,
				// function(data){alert("Data Loaded: " + data);}
				);
	}

	function collapseItem(e) {
		e.preventDefault();
		$(this).parent().next(".description").slideToggle(300);

		$(this).children(".invisible").toggle();
		$(this).children(".visible").toggle();

		$.post("./home/ajax/block_visibility.php", {
			block : $(this).parent().parent().attr("id")
		}// ,
				// function(data){alert("Data Loaded: " + data);}
				);
	}

	function hoverInItem() {
		$(this).children("a").fadeIn(150);
	}

	function hoverOutItem() {
		$(this).children("a").fadeOut(150);
	}

	function deleteItem(e) {
		e.preventDefault();
		
		var confirmation, columnId, order;
		
		confirmation = confirm('Are you sure ?');
		if (confirmation) {
			columnId = $(this).parent().parent().parent().attr("id");

			$(this).parent().parent().remove();
			$.post("./home/ajax/block_delete.php", {
				block : $(this).parent().parent().attr("id")
			}// ,
					// function(data){alert("Data Loaded: " + data);}
					);

			order = $("#" + columnId).sortable("serialize");
			$.post("./home/ajax/block_sort.php", {
				column : columnId,
				order : order
			}// ,
					// function(data){alert("Data Loaded: " + data);}
					);
		}
		
		checkForEmptyColumns();
	}

	function removeBlockScreen(e, ui) {
		$("#addBlock").slideToggle(300, function () {
			$("#addBlock").remove();
		});

		$("a.addEl").show();
	}

	function showBlockScreen(e, ui) {
		e.preventDefault();
		$.post("./home/ajax/block_list.php", function (data) {
			$("#tab_menu").after(data);
			$("#addBlock").slideToggle(300);

			$("a.addEl").hide();
			$("a.closeScreen").bind('click', removeBlockScreen);
			$(".component").bind('click', addBlock);
			$(".component").css('cursor', 'pointer');
			
			$("#applications .application").bind('click', filterComponents);
			$("#applications #show_all").bind('click', showAllComponents);
		});
	}

	function addBlock(e, ui) {
		var column, columnId, order, loadingMessage, loading;
		
		column = $(".tab:visible .column:first-child");
		columnId = column.attr("id");
		order = column.sortable("serialize");
		
		loadingMessage = 'YourBlockIsBeingAdded';

		loading = $.modal(getLoadingBox(loadingMessage), {
			overlayId : 'homeOverlay',
			containerId : 'homeContainer',
			opacity: 75,
			close: false
		});

		$.post("./home/ajax/block_add.php", {
			component : $(this).attr("id"),
			column : columnId,
			order : order
		}, function (data) {
			column.prepend(data);
			$("div.title a").css('display', 'none');
			order = column.sortable("serialize");

			bindIconsLegacy();
			blocksDraggable();

			$.post("./home/ajax/block_sort.php", {
				column : columnId,
				order : order
			},
			function (data)
			{
				$(".loadingBox", loading.dialog.container).html(getMessageBox(data.success, data.message));
				handleLoadingBox(loading);
			}, "json");
		});
	}
	
	function filterComponents(e, ui) {
		var applicationId = $(this).attr("id");

		$("#components #components_" + applicationId).show();
		$("#components").children(":not(#components_" + applicationId + ")").hide();
	}
	
	function showAllComponents(e, ui) {
		$("#components").children().show();
	}
	
	function addTab(e, ui) {
		e.preventDefault();
		
		var loadingMessage, loading;
			
		loadingMessage = 'YourTabIsBeingAdded';

		loading = $.modal(getLoadingBox(loadingMessage), {
			overlayId : 'homeOverlay',
			containerId : 'homeContainer',
			opacity : 75,
			close: false
		});
		
		$.post("./home/ajax/tab_add.php", {}, function (data) {
			$("#main .tab:last").after(data.html);
			$("#tab_menu ul").append(data.title);
			bindIconsLegacy();
			tabsSortable();
			columnsSortable();
			columnsResizable();
			tabsDroppable();
			
			$(".loadingBox", loading.dialog.container).html(getMessageBox(data.success, data.message));
			handleLoadingBox(loading);
		}, "json");
	}
	
	function addColumn(e, ui) {
		e.preventDefault();
		
		var row, rowId, loadingMessage, loading;
		
		row = $(".tab:visible .row:first");
		rowId = row.attr('id');
		
		loadingMessage = 'YourColumnIsBeingAdded';

		loading = $.modal(getLoadingBox(loadingMessage), {
			overlayId: 'homeOverlay',
			containerId: 'homeContainer',
			opacity: 75,
			close: false
		});
		
		$.post("./home/ajax/column_add.php", {row: rowId}, function (data) {
			var columnHtml, newWidths, lastColumn;
			
			columnHtml = data.html;
			newWidths = data.width;
			
			lastColumn = $("div.column:last", row);
			lastColumn.css('margin-right', '1%');
			
			$("div.column", row).each(function (i) {
				var newWidth = newWidths[this.id] + '%'; 
				this.style.width = newWidth;
			});
			
			$("div.column:last", row).after(columnHtml);
			
			bindIconsLegacy();
			columnsSortable();
			columnsResizable();
			
			$(".loadingBox", loading.dialog.container).html(getMessageBox(data.success, data.message));
			handleLoadingBox(loading);
		}, "json");
	}
	
	function getMessageBox(isError, message)
	{
		var messageClass, successMessage;
		
		if (isError === '0')
		{
			messageClass = 'statusError';
		}
		else
		{
			messageClass = 'statusConfirmation';
		}
		
		successMessage  = '<div class="' + messageClass + '" style="margin-bottom: 15px;">';
		successMessage += '</div>';
		successMessage += '<div>';
		successMessage += '<h3>' + message + '</h3>';
		successMessage += '</div>';
			
		return successMessage;
	}
	
	function getLoadingBox(message)
	{
		var loadingHTML  = '<div class="loadingBox">';
		loadingHTML += '<div class="loadingHuge" style="margin-bottom: 15px;">';
		loadingHTML += '</div>';
		loadingHTML += '<div>';
		loadingHTML += '<h3>' + translation(message, 'home') + '</h3>';
		loadingHTML += '</div>';
		loadingHTML += '</div>';
			
		return loadingHTML;
	}
	
	function handleLoadingBox(loading)
	{
		loading.dialog.container.append($(loading.opts.closeHTML).addClass(loading.opts.closeClass));
		loading.bindEvents();
		$.timeout(function () { 
			loading.close();
		}, 3000);
	}
	
	function deleteTab(e, ui)
	{
		e.preventDefault();
		
		var tab, tabId, loadingMessage, loading;
		
		tab = $(this).parent().attr('id');
		tab = tab.split("_");
		
		tabId = tab[2];
		
		loadingMessage = 'YourTabIsBeingDeleted';
	
		loading = $.modal(getLoadingBox(loadingMessage), {
			overlayId: 'homeOverlay',
			containerId: 'homeContainer',
			opacity: 75,
			close: false
		});
		
		$.post("./home/ajax/tab_delete.php", {tab: tabId}, function (data) {
			if (data.success === '1')
			{
				$('#tab_' + tabId).remove();
				$('#tab_select_' + tabId).remove();
				
				// Show the first existing tab			
				$("#tab_menu ul li:first").attr('class', 'current');
				var newTabId = $("#tab_menu ul li:first").attr('id');
				newTabId = newTabId.split("_");
				newTabId = newTabId[2];
				$("#tab_" + newTabId).css('display', 'block');
				
				$("li.current a.deleteTab").css('display', 'inline');
				$("li.normal a.deleteTab").css('display', 'none');
				
				$("#tab_menu li").unbind();
				$("#tab_menu li:not(.current)").bind('click', showTab);
				$("#tab_menu li.current").bind('click', editTab);
			}
			
			$(".loadingBox", loading.dialog.container).html(getMessageBox(data.success, data.message));
			handleLoadingBox(loading);
		}, "json");
	}
	
	function saveTabTitle(e)
	{
		e.preventDefault();
		
		var tab, tabId, newTitle;
		
		tab = e.data.tab.parent().attr('id');
		tab = tab.split("_");
		
		tabId = tab[2];
		newTitle = $('#tabTitle').attr('value');
		
		$.post("./home/ajax/tab_edit.php", {tab: tabId, title: newTitle}, function (data) {
			if (data.success === '1')
			{
				e.data.tab.html(data.title);
				e.data.loading.close();
				
				$('#tabSave').unbind();
				$('#tabTitle').unbind();
			}
		}, "json");
	}
	
	function editTab(e, ui)
	{
		e.preventDefault();
		
		var editTabHTML, loading;
		
		editTabHTML  = '<div id="editTab"><h3>Edit tab name</h3>';
		editTabHTML += '<input id="tabTitle" type="text" value="' + $('.tabTitle', this).html() + '"/>&nbsp;';
		editTabHTML += '<input id="tabSave" type="submit" class="button" value="' + translation('Save') + '"/>';
		editTabHTML += '</div>';
		
		loading = $.modal(editTabHTML, {
			overlayId : 'homeOverlay',
			containerId : 'homeEditContainer',
			opacity: 75
		});
		
		$("#tabTitle").bind('keypress', {loading : loading, tab: $('.tabTitle', this)}, function (e) {
			var code = (e.keyCode ? e.keyCode : e.which);
			// If ENTER is pressed we save the new tab title
			if (code === 13) {
				saveTabTitle(e);
			}
			else if (code === 27)
			{
				loading.close();
				$('#tabSave').unbind();
				$('#tabTitle').unbind();
			}
		});
		
		$('#tabSave').bind('click', {loading: loading, tab: $('.tabTitle', this)}, saveTabTitle);
	}
	
	function deleteColumn(e, ui) {
		var column, columnId, loadingMessage, loading;
		
		column = $(this).parent().parent();
		columnId = column.attr("id").split("_");
		columnId = columnId[1];
		
		loadingMessage = 'YourColumnIsBeingDeleted';
		
		loading = $.modal(getLoadingBox(loadingMessage), {
			overlayId: 'homeOverlay',
			containerId: 'homeContainer',
			opacity: 75,
			close: false
		});
		
		$.post("./home/ajax/column_delete.php", {column: columnId}, function (data) {
			if (data.success === '1')
			{
				var columnWidth, otherColumn, otherColumnWidth, newColumnWidth;
				
				// Get the deleted column's width
				columnWidth = column.css('width');
				columnWidth = parseInt(columnWidth.replace('%', ''), 10);
				column.remove();
				
				// Get the last column's width 
				otherColumn = $(".tab:visible .column:last");
				otherColumnWidth = otherColumn.css('width');
				otherColumnWidth = parseInt(otherColumnWidth.replace('%', ''), 10);
				
				// Calculate the new width
				newColumnWidth =  columnWidth + otherColumnWidth + 1;
				
				// Set the new width + postback
				otherColumn.css('margin-right', '0px');
				otherColumn.css('width', newColumnWidth + '%');
				
				$.post("./home/ajax/column_width.php", {
					column : otherColumn.attr('id'),
					width : newColumnWidth
				}// ,
						// function(data){alert("Data Loaded: " + data);}
						);
			}
			
			$(".loadingBox", loading.dialog.container).html(getMessageBox(data.success, data.message));
			handleLoadingBox(loading);
		}, "json");
	}
	
	function bindIconsLegacy() {
		$("div.title a").hide();
		$("div.title").unbind();
		$("div.title").bind('mouseenter', hoverInItem);
		$("div.title").bind('mouseleave', hoverOutItem);
	}

	function bindIcons() {
		$("a.closeEl").live('click', collapseItem);
		$("a.deleteEl").live('click', deleteItem);
		$("a.addEl").live('click', showBlockScreen);
		$("#tab_menu li:not(.current)").live('click', showTab);
		$("#tab_menu li.current").live('click', editTab);
		$("a.addTab").live('click', addTab);
		$("a.addColumn").live('click', addColumn);
		$("a.deleteTab").live('click', deleteTab);
		$(".deleteColumn").live('click', deleteColumn);	
	}
	
	function getDraggableParent(e, ui) {
		return $(this).parent().parent().html();
	}
	
	function beginDraggable() {
		$("div.title").unbind();
	}
	
	function endDraggable() {
		bindIconsLegacy();
	}
	
	function blocksDraggable() {
		$("a.dragEl").draggable("destroy");
		$("a.dragEl").draggable({
			//helper: getDraggableParent,
			revert : true,
			scroll : true,
			cursor : 'move',
			start : beginDraggable,
			stop : endDraggable,
			//helper : getDraggableParent,
			placeholder : 'blockSortHelper'
		});
	}
	
	function processDroppedBlock(e, ui) {
		var newTab, newTabSplit, newTabId, block, blockSplit, blockId, newColumn, newColumnSplit, newColumnId, theBlock, loadingMessage, loading;
		
		// Retrieving some variables
		newTab = $(this).attr('id');
		newTabSplit = newTab.split("_");
		newTabId = newTabSplit[2];
		
		block = ui.draggable.attr('id');
		blockSplit = block.split("_");
		blockId = blockSplit[2];
		
		newColumn = $("#tab_" + newTabId + " .row:first .column:first").attr('id');
		newColumnSplit = newColumn.split("_");
		newColumnId = newColumnSplit[1];
		
		theBlock = ui.draggable.parent().parent();
		
		// Show the processing modal
		loadingMessage = 'YourBlockIsBeingMoved';
		
		loading = $.modal(getLoadingBox(loadingMessage), {
			overlayId : 'homeOverlay',
			containerId : 'homeContainer',
			opacity : 75,
			close : false
		});
		
		// Do the actual move + postback		
		$.post("./home/ajax/block_move.php", {block: blockId, column: newColumnId}, function (data) {
			if (data.success === '1')
			{
				//Does the column have blocks
				var blockCount = $("#" + newColumn + " .block").length;
				if (blockCount > 0)
				{
					$("#" + newColumn + " .block:last").after(theBlock);
				}
				else
				{
					$("#" + newColumn).append(theBlock);
				}
				
				checkForEmptyColumns();
			}
			
			// Now we can get rid of the modal as well
			$(".loadingBox", loading.dialog.container).html(getMessageBox(data.success, data.message));
			handleLoadingBox(loading);
		}, "json");
	}
	
	function tabsDroppable() {
		$("#tab_elements li").droppable("destroy");
		$("#tab_elements li.normal").droppable({
			accept : "a.dragEl",
			drop : processDroppedBlock
		});
	}
	
	function columnsSortable() {
		$("div.column").sortable("destroy");
		$("div.column").sortable({
			handle : 'div.title',
			cancel : 'a',
			opacity : 0.8,
			forcePlaceholderSize : true,
			cursor : 'move',
			helper : 'original',
			placeholder : 'blockSortHelper',
			revert : true,
			scroll : true,
			connectWith : '.column',
			start : sortableStart,
			beforeStop : sortableBeforeStop,
			stop : sortableStop,
			update : sortableUpdate
		});
	}
	
	function tabsSortable() {
		$("#tab_menu #tab_elements").sortable("destroy");
		$("#tab_menu #tab_elements").sortable({
			cancel : 'a.deleteTab',
			opacity : 0.8,
			forcePlaceholderSize : true,
			cursor : 'move',
			helper : 'original',
			placeholder : 'tabSortHelper',
			revert : true,
			scroll : true,
			update : tabsSortableUpdate
		});
	}
	
	function columnsResizable() {
		$("div.column").resizable("destroy");
		$("div.column").resizable({
			handles : 'e',
			autoHide : true,
			ghost : true,
			preventDefault : true,
			helper: 'ui-state-highlight',
			stop : resizableStop
		});
	}

	// Extension to jQuery selectors which only returns visible elements
	$.extend($.expr[':'], {
	    visible: function (a) {
	        return $(a).css('display') !== 'none';
	    }
	});

	$(document).ready(function () {
		$("a.addEl").toggle();
		$("li.current a.deleteTab").css('display', 'inline');

		bindIconsLegacy();
		bindIcons();
		
		tabsSortable();
		
		blocksDraggable();
		tabsDroppable();
		
		columnsSortable();
		columnsResizable();

	});

});