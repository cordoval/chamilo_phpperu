/**
 * Viewable checkbox for forms.
 * Makes a checkbox render as an eye.
 * Use the viewableStyle function to render the checkbox.
 * Use the setViewableStyle function to reset the checkbox's eye
 * 		after you have changed the checkbox's value.
 * 		e.g. After the document has loaded or after the resetbutton was pressed.
 */

(function ($) 
{	
	common_image_path = getPath('WEB_LAYOUT_PATH') + getTheme() + "/images/common/";
	
	$.fn.setViewableStyle = function()
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