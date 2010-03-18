$(function () 
{
	
	function reset(evt,ui)
	{
		setTimeout(
			function()
			{
				$('.iphone').setiphoneCourseType();
				$('.viewablecheckbox').setviewableStyle();
			},30);	
	}
	
	$(document).ready(function ()
	{
		$('.iphone').iphoneStyle();
		$('.iphone').setiphoneCourseType();
		$('.viewablecheckbox').viewableStyle();
		$('.viewablecheckbox').setviewableStyle();
		$('.empty').live('click', reset);
	});

});

(function ($) 
{		
	$.iphoneCourseType = {
	   defaults: { checkedLabel: 'ON', uncheckedLabel: 'OFF', background: '#fff' }
	}
	
	$.fn.setiphoneCourseType = function()
	{
	    return this.each(function() 
	    {
	    	var elem = $(this);
	    	
	    	if (!elem.is(':checkbox'))
	    			return;
	    	
	        var handle    = elem.siblings('.handle'),
	          	handlebg  = handle.children('.bg'),
	          	offlabel  = elem.siblings('.off'),
	          	onlabel   = elem.siblings('.on'),
	          	container = elem.parent('.binary_checkbox'),
	          	rightside = container.outerWidth() - 39;
	        	tool = elem.attr('class').split(' ').slice(-1);
	        	image = $('.'+tool+'_image'); 
	        	defaultimage = $('.'+tool+'elementdefault');
	        	imagesrc = image_path + 'tool_' + tool + '.png';
	        	imagesrcdisabled = image_path + 'tool_' + tool + '_na.png';
			
	  	      container.click(function() 
	  	  	  {
	  	  	    var is_onstate = (handle.position().left <= 0);
	  	  		    tool = elem.attr('class').split(' ').slice(-1);
	  	  		    image = $('.'+tool+'_image'); 
	  	  		    defaultimage = $('.'+tool+'elementdefault');
	  	  		    imagesrc = image_path + 'tool_' + tool + '.png';
	  	  		    imagesrcdisabled = image_path + 'tool_' + tool + '_na.png';

	  	  		if (is_onstate)
	  	  		{
	  	  			elem.attr('checked', true);
	  	  			image.attr('src', imagesrc);
	  	  			defaultimage.css('display','inline');
	  	  		}
	  	  		else
	  	  		{
	  	  			elem.attr('checked', false);
	  	  			image.attr('src', imagesrcdisabled);
	  	  			defaultimage.css('display','none');
	  	  		}
	  	  							        
	  	  		return false;
	  	  	});	
	        	
	        if (elem.is(':checked')) 
			{
			    offlabel.css({ opacity: 0 });
			    onlabel.css({ opacity: 1 });
			    handle.css({ left: rightside });
			    handlebg.css({ left: 34 });
			    image.attr('src', imagesrc);
			    defaultimage.css('display','inline');
			}
			else
			{
			  	offlabel.css({ opacity: 1 });
			    onlabel.css({ opacity: 0 });
			    handle.css({ left: 0 });
			    handlebg.css({ left: 0 });
			  	image.attr('src', imagesrcdisabled);
			   	defaultimage.css('display','none');
			}
	    });
	};
	
	$.fn.setviewableStyle = function()
	{
		return this.each(function() 
		{
			var elem = $(this);
				 	
			if (!elem.is(':checkbox'))
				return;
			
			var eye = elem.siblings('.eye');
			
			// initial load
			if (!elem.is(':checked')) 
				eye.attr('src', common_image_path+'action_invisible.png');
			else
				eye.attr('src', common_image_path+'action_visible.png');
		});
	};
	
	$.fn.viewableStyle = function() 
	{
		 return this.each(function() 
		 {
		 	var elem = $(this);
		 	
			if (!elem.is(':checkbox'))
				return;

			elem.css('display','none');
			elem.wrap('<div class="viewable_checkbox" />');
			elem.after('<img class="eye" src="'+common_image_path+'action_visible.png" style="vertical-align: middle;" alt=""/>');

			var eye = elem.siblings('.eye'),
				container = elem.parent('.viewable_checkbox');

			container.click(function() 
			{
				if (!elem.is(':checked'))
					elem.attr('checked', true);
				else
					elem.attr('checked', false);
				
				if(!elem.is(':checked'))
					eye.attr('src', common_image_path+'action_invisible.png');
				else
  					eye.attr('src', common_image_path+'action_visible.png');
				
				return false;
			});
		});
	};
})(jQuery);