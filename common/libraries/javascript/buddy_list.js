( function($) 
{
	var item_clicked = function(ev, ui) 
	{ 
		//alert('test');
		
		var visible_img = 'bullet_toggle_plus.png';
		var invisible_img = 'bullet_toggle_minus.png';
		var current = $(this).attr('src');
		
		if(current.indexOf(visible_img) > -1)
			current = current.replace(visible_img, invisible_img)
		else
			current = current.replace(invisible_img, visible_img);
	
		$(this).attr('src', current);
		
		$('.buddy_list', $(this).parent()).toggle();
	}
	
	var buddy_dropped = function(event, ui)
	{
		//Variable initialisation
		var old_parent = ui.draggable.parent();
		var old_super_parent = old_parent.parent();
		var buddylist = $('.buddy_list', $(this));
		
		//Check if buddylist exists in new parent or add it
		if(buddylist.attr('class') != 'buddy_list')
		{
			$(this).append('<ul class="buddy_list"></ul>');
			buddylist = $('.buddy_list', $(this));
		}
		
		//Add item to buddylist
		buddylist.append(ui.draggable);
		
		//Determine postback variables
		var buddy = ui.draggable.attr('id');
		var new_category = $(this).attr('id');
		
		//Check wheter the buddylist from old parent may be removed
		var is_remove = false;
		var children = $('.buddy_list', old_super_parent).children();
		if(children.size() == 0)
		{
			$('.category_toggle', old_super_parent).css('visibility', 'hidden');
			$('.buddy_list', old_super_parent).remove();
			is_remove = true;
		}
		
		//Toggle the visibility of new parent's icon
		$('.category_toggle', $(this)).css('visibility', 'visible');
	
		var current = $(this);
		count_sizes();
		
		$.post('core.php?application=user&go=buddy_category_change',
		{
	    	buddy:  buddy,
	    	new_category: new_category
	    },	function(data)
	    	{
	    		if(data.length > 0)
	    		{ 
	    			//Turn back actions
	    			if(is_remove)
	    			{
	    				old_super_parent.append('<ul class="buddy_list"></ul>');
	    				old_parent = $('.buddy_list', old_super_parent)
	    			}
	    			
	    			old_parent.append(ui.draggable);
	    			$('.category_toggle', old_super_parent).css('visibility', 'visible');
	    			
	    			var children = $('.buddy_list', current).children();
	    			if(children.size() == 0)
	    			{
	    				$('.category_toggle', current).css('visibility', 'hidden');
	    				buddylist.remove();
	    			}
	    			
	    			count_sizes();
	    			alert(translation('CategoryChangeFailed', 'user'));
	    		}
	    	}
	    );
	}
	
	var delete_category_clicked = function(ev, ui) 
	{ 		
		var id = $(this).attr('id');
		var object = $(this).parent().parent().parent().parent();
		var object_parent = object.parent();
		
		var children = $('.buddy_list', object).children();
		
		var normal_category = $('.category_list_item[id="0"]', object_parent);
		var normal_buddy_list = $('.buddy_list', normal_category);
		
		if(normal_buddy_list.attr('class') != 'buddy_list')
		{
			normal_category.append('<ul class="buddy_list"></ul>');
			normal_buddy_list = $('.buddy_list', normal_category);
			$('.category_toggle', normal_category).css('visibility', 'visible');
		}
		
		normal_buddy_list.append(children);
		
		object.remove();
		count_sizes();
		
		$.get('core.php?application=user&go=buddy_delete_category',
		{
			buddylist_category: id,
			ajax: 1
	    },  function(data)
	    	{
	    		if(data.length > 0)
	    		{ 
	    			alert(data);
	    			object_parent.prepend(object);
	    			$('.buddy_list', object).append(children);
	    			
	    			if(normal_buddy_list.children().size() == 0)
	    			{
	    				normal_buddy_list.remove();
	    				$('.category_toggle', normal_category).css('visibility', 'hidden');
	    			}
	    			count_sizes();
	    		}
	    	}
		);
		
		return false;
	}
	
	var delete_item_clicked = function(ev, ui) 
	{ 
		var id = $(this).attr('id');
		var buddy_list_item = $(this).parent().parent().parent().parent();
		var buddy_list = buddy_list_item.parent();
		var category_list_item = buddy_list.parent();
		
		buddy_list_item.remove();
		
		var is_remove = false;
		if(buddy_list.children().size() == 0)
		{
			is_remove = true;
			$('.category_toggle', category_list_item).css('visibility', 'hidden');
			buddy_list.remove();
		}
		
		count_sizes();
		
		$.get('core.php?application=user&go=buddy_delete_item',
		{
			buddylist_item:  id,
			ajax: 1
	    },  function(data)
	    	{
	    		if(data.length > 0)
	    		{ 
	    			alert(data);
	    			
	    			if(is_remove)
	    			{
	    				category_list_item.append('<ul class="buddy_list"></ul>');
	    				$('.category_toggle', category_list_item).css('visibility', 'visible');
	    				buddy_list = $('.buddy_list', category_list_item);
	    			}
	    			
	    			buddy_list.prepend(buddy_list_item);
	    			count_sizes();
	    		}
	    	}
		);
		
		return false;
	}
	
	var accept_buddy_clicked = function(ev, ui) 
	{ 
		var id = $(this).attr('id');
		var buddy_list_item = $(this).parent().parent().parent().parent();
		var buddy_list = buddy_list_item.parent();
		var category_list_item = buddy_list.parent();
		
		var normal_category = $('.category_list_item[id="0"]', category_list_item.parent());
		var normal_buddy_list = $('.buddy_list', normal_category);
		
		if(normal_buddy_list.attr('class') != 'buddy_list')
		{
			normal_category.append('<ul class="buddy_list"></ul>');
			normal_buddy_list = $('.buddy_list', normal_category);
			$('.category_toggle', normal_category).css('visibility', 'visible');
		}
		
		normal_buddy_list.append(buddy_list_item);
	
		var is_remove = false;
		if(buddy_list.children().size() == 0)
		{
			$('.category_toggle', category_list_item).css('visibility', 'hidden');
			buddy_list.remove();
			is_remove = true; 
		}
		
		var reject_buddy = $('.reject_buddy', buddy_list_item);
		var accept_buddy = $(this);
		
		accept_buddy.css('visibility', 'hidden');
		reject_buddy.attr('class', 'delete_item');
		
		var src = reject_buddy.children().attr('src');
		src = src.replace('action_setting_false.png', 'action_unsubscribe.png');
		reject_buddy.children().attr('src', src);
		
		var href = reject_buddy.attr('href');
		href = href.replace('buddy_status_change', 'buddy_delete_item');
		href = href.replace('&status=2', '');
		reject_buddy.attr('href', href);
		
		count_sizes();
		
		$.get('core.php?application=user&go=buddy_status_change',
		{
			buddylist_item:  id,
			status: 0,
			ajax: 1
	    },  function(data)
	    	{
	    		if(data.length > 0)
	    		{ 
	    			alert(data);
	    			
	    			if(is_remove)
	    			{
	    				category_list_item.append('<ul class="buddy_list"></ul>');
	    				$('.category_toggle', category_list_item).css('visibility', 'visible');
	    				buddy_list = $('.buddy_list', category_list_item);
	    			}
	    		
	    			buddy_list.prepend(buddy_list_item);
	    			
	    			if(normal_buddy_list.children().size() == 0)
	    			{
	    				$('.category_toggle', normal_category).css('visibility', 'hidden');
	    				normal_buddy_list.remove();
	    			}
	    			
	    			reject_buddy.attr('class', 'reject_buddy');
	    			accept_buddy.css('visibility', 'visible');
	    			
	    			src = src.replace('action_unsubscribe.png', 'action_setting_false.png');
	    			reject_buddy.children().attr('src', src);
	    			
	    			href = href.replace('buddy_delete_item', 'buddy_status_change');
	    			href += '&status=2';
	    			reject_buddy.attr('href', href);
	    			
	    			count_sizes();
	    		}
	    	}
		);
		
		return false;
	}
	
	var reject_buddy_clicked = function(ev, ui) 
	{ 
		var id = $(this).attr('id');
		var buddy_list_item = $(this).parent().parent().parent().parent();
		var buddy_list = buddy_list_item.parent();
		var category_list_item = buddy_list.parent();
		
		buddy_list_item.remove();
		
		var is_remove = false;
		if(buddy_list.children().size() == 0)
		{
			is_remove = true;
			$('.category_toggle', category_list_item).css('visibility', 'hidden');
			buddy_list.remove();
		}
		
		count_sizes();
		
		$.get('core.php?application=user&go=buddy_status_change',
		{
			buddylist_item:  id,
			status: 2,
			ajax: 1
	    },  function(data)
	    	{
	    		if(data.length > 0)
	    		{ 
	    			alert(data);
	    			
	    			if(is_remove)
	    			{
	    				category_list_item.append('<ul class="buddy_list"></ul>');
	    				$('.category_toggle', category_list_item).css('visibility', 'visible');
	    				buddy_list = $('.buddy_list', category_list_item);
	    			}
	    			
	    			buddy_list.prepend(buddy_list_item);
	    			
	    			count_sizes();
	    		}
	    	}
		);
		
		return false;
	}
	
	$(document).ready( function() 
	{	
		$(".buddy_list_item").draggable({
			revert: true
		});
		
		$(".category_list_item").droppable({
			accept: '.buddy_list_item',
			hoverClass: 'buddyDrop',
			drop: buddy_dropped
		});
		
		bind_icons();
		count_sizes();
	});
	
	function bind_icons()
	{
		$(".category_toggle").live('click', item_clicked);
		$(".delete_category").live('click', delete_category_clicked);
		$(".delete_item").live('click', delete_item_clicked);
		$(".accept_buddy").live('click', accept_buddy_clicked);
		$(".reject_buddy").live('click', reject_buddy_clicked);
	}
	
	function count_sizes()
	{
		var total = 0;
		
		$('.category_list_item, .category_list_item_static').each(function()
		{
			var size = $('.buddy_list', $(this)).children().size();
			total += size;
			$('.userscount', $(this)).text(size);
		});
		
		$('.totalusers').text(total);
	}
	
	function translation(string, application) {
		
		var translated_string = $.ajax({
			type: "POST",
			url: "./common/javascript/ajax/translation.php",
			data: { string: string, application: application },
			async: false
		}).responseText;
		
		return translated_string;
	};
	
})(jQuery);