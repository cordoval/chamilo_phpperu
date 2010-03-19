$(function () 
{
	
	function reset(evt,ui)
	{
		setTimeout(
			function()
			{
				$('.iphone').setIphoneCourseType();
				$('.viewablecheckbox').setViewableStyle();
			},30);	
	}
	
	$(document).ready(function ()
	{
		$('.iphone').iphoneStyle();
		$('.iphone').setiPhoneCourseType();
		$('.viewablecheckbox').viewableStyle();
		$('.viewablecheckbox').setViewableStyle();
		$('.empty').live('click', reset);
	});

});

(function ($) 
{		
	$.iphoneCourseType = {
	   defaults: { checkedLabel: 'ON', uncheckedLabel: 'OFF', background: '#fff' }
	}
	
	$.fn.setiPhoneCourseType = function()
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
})(jQuery);