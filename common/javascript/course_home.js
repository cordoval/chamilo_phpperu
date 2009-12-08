( function($) 
{
	var handle_drop = function(ev, ui) 
	{ 
	       //$(this).empty();
		var target = $(this).attr("id");
		var source = $(ui.draggable).attr("id");
		var course_code = $("#coursecode").html();
	    
	    $(ui.draggable).parent().remove();
	    
	    $.post("./run.php?go=courseviewer&course=" + course_code + "&tool=course_sections&application=weblcms&tool_action=change_section", 
	    {
			target : target,
			source : source
		},  function(data) 
			{
	    		//alert(data);
	    		$("#" + target + " > * > .description").empty();
	    		$("#" + target + " > * > .description").append(data);
	    		$(".tooldrag").css('display', 'inline');
	    	}
	    );
	} 
	
	var handle_visible_clicked = function(ev, ui)
	{
		var visible_img = 'layout/aqua/images/common/action_visible.png';
		var invisible_img = 'layout/aqua/images/common/action_invisible.png';
	
		var parent = $(this).parent().parent();
		var tool = parent.attr('id');
		tool = tool.substring(5, tool.length);

		// Determine visibility icon		
		var img = $(this).attr("src");
		var imgtag = $(this);
		var pos = img.indexOf('invisible'); 
		
		var new_visible = 1;
		if(pos == -1)
			new_visible = 0;
		
		var new_img = new_visible?visible_img:invisible_img;
		
		// Determine tool icon
		var tool_img = $(".tool_image", parent);
		var src = tool_img.attr('src');
		
		// Determine tool text class
		var tool_text = $("#tool_text", parent);
		
		// List old variables
		var old_img = imgtag.attr('src');
		var old_class = tool_text.attr('class');
		var old_tool_img = tool_img.attr('src');
		
		// Changes icons and classes
		imgtag.attr('src', new_img);
   		if(new_visible == 0)
   		{
   			tool_text.addClass('invisible');
   			var new_src = src.replace('.png', '_na.png');
   		}
   		else
   		{
   			tool_text.removeClass('invisible');
   			var new_src = src.replace('_na.png', '.png');
   		}
   		
   		tool_img.attr('src', new_src);
		
		$.post("./application/lib/weblcms/ajax/change_course_module_visibility.php", 
	    {
	    	tool:  tool,
	    	visible: new_visible
	    },	function(data)
	    	{
	    		if(data.length > 0)
	    		{
	    			// On error : set the old icons and classes again
	    			//alert(data);
	    			imgtag.attr('src', old_img);
	    			tool_text.attr('class', old_class);
	    			tool_img.attr('src', old_tool_img);
	    		}
	    	}
	    );
		
		return false;
	}
	
	function toolsSortableStart(e, ui) {
		ui.helper.css("border", "4px solid #c0c0c0");
	}
	
	function toolSortableBeforeStop(e, ui) {
		ui.helper.css("border", "0px solid #c0c0c0");
	}
	
	function toolsSortableUpdate(e, ui) {
		var section = $(this).attr("id");
		var order = $(this).sortable("serialize");

		$.post("./application/lib/weblcms/ajax/block_sort.php", {
			column : column,
			order : order
		}// ,
				// function(data){alert("Data Loaded: " + data);}
				);
	}
	
	function toolsSortable() {
		$(".toolblock .block .description").sortable("destroy");
		$(".toolblock .block .description").sortable({
			cancel : 'a',
			opacity : 0.8,
			forceHelperSize : true,
			forcePlaceholderSize : true,
			cursor : 'move',
			helper : 'original',
			placeholder : 'toolSortHelper',
			revert : true,
			scroll : false,
			start : toolsSortableStart,
			beforeStop : toolSortableBeforeStop,
			//update : toolSortableUpdate
		});
	}

	$(document).ready( function() 
	{
		toolsSortable();
		
		$(".tool_visible").bind('click', handle_visible_clicked);
		
		$(".tooldrag").css('display', 'inline');
	});
	
})(jQuery);