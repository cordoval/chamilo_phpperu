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
		$('.iphone').iphoneCourseType();
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
	
    $.fn.iphoneCourseType = function(options) 
    {
	    options = $.extend($.iphoneCourseType.defaults, options);
						    
	    return this.each(function() 
	    {
	    	var elem = $(this);
					      
	    	if (!elem.is(':checkbox'))
	    			return;
						      
	      elem.css({ opacity: 0 });
	      elem.wrap('<div class="binary_checkbox" />');
	      elem.after('<div class="handle"><div class="bg" style="background: ' + options.background + '"/><div class="slider" /></div>')
	          .after('<label class="off">'+ options.uncheckedLabel + '</label>')
	          .after('<label class="on">' + options.checkedLabel   + '</label>');
						      
	      var handle    = elem.siblings('.handle'),
	          handlebg  = handle.children('.bg'),
	          offlabel  = elem.siblings('.off'),
	          onlabel   = elem.siblings('.on'),
	          container = elem.parent('.binary_checkbox'),
	          rightside = container.outerWidth() - 39;

				      
	      container.click(function() 
	      {
	    	  var is_onstate = (handle.position().left <= 0);
	    	  	  new_left   = (is_onstate) ? rightside : 0,
	    	  	  bgleft     = (is_onstate) ? 34 : 0;
		          tool = elem.attr('class').split(' ').slice(-1);
		          image = $('.'+tool+'_image'); 
		          defaultimage = $('.'+tool+'elementdefault');
		          imagesrc = image_path + 'tool_' + tool + '.png';
		          imagesrcdisabled = image_path + 'tool_' + tool + '_na.png';
		          
			  handlebg.hide();
			  handle.animate({ left: new_left }, 100, function() {
			  handlebg.css({ left: bgleft }).show();
			  });
							        
			  if (is_onstate) {
			      offlabel.animate({ opacity: 0 }, 200);
			      onlabel.animate({ opacity: 1 }, 200);
			  } else {
			      offlabel.animate({ opacity: 1 }, 200);
			      onlabel.animate({ opacity: 0 }, 200);
			   }
							        
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