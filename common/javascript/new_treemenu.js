( function($) 
{
	$(document).ready(
		function()
		{
			var tree = $('.myTree');
			$('li', tree.get(0)).each(
				function()
				{
					subbranch = $('ul', $(this));
					if (subbranch.size() > 0) {
						if (subbranch.eq(0).css('display') == 'none') {
							$(this).prepend('<div class="togglePlus expandImage">&nbsp;</div>');
						} else {
							$(this).prepend('<div class="toggleMinus expandImage">&nbsp;</div>');
						}
					} else {
						$(this).prepend('<div class="toggleSpacer expandImage">&nbsp;</div>');
					}
				}
			);
			$('div.expandImage', tree.get(0)).click(
				function()
				{
					if ($(this).attr("class").indexOf('spacer') == -1) {
						subbranch = $('ul', this.parentNode).eq(0);
						if (subbranch.css('display') == 'none') {
							subbranch.show();
							$(this).attr('class', 'toggleMinus expandImage');
						} else {
							subbranch.hide();
							$(this).attr('class', 'togglePlus expandImage');
						}
					}
				}
			);
			$('span.textHolder').Droppable(
				{
					accept			: 'treeItem',
					hoverclass		: 'dropOver',
					activeclass		: 'fakeClass',
					tollerance		: 'pointer',
					onhover			: function(dragged)
					{
						if (!this.expanded) {
							subbranches = $('ul', this.parentNode);
							if (subbranches.size() > 0) {
								subbranch = subbranches.eq(0);
								this.expanded = true;
								if (subbranch.css('display') == 'none') {
									var targetBranch = subbranch.get(0);
									this.expanderTime = window.setTimeout(
										function()
										{
											$(targetBranch).show();
											$('div.expandImage', targetBranch.parentNode).eq(0).attr('class',  'toggleMinus expandImage');
											$.recallDroppables();
										},
										500
									);
								}
							}
						}
					},
					onout			: function()
					{
						if (this.expanderTime){
							window.clearTimeout(this.expanderTime);
							this.expanded = false;
						}
					},
					ondrop			: function(dropped)
					{
						id = $(this).parents(".myTree").attr("id");
						
						//$.post("common/html/menu/ajax/" + id + ".php", 
						$.post(mover_url,
					    {
							target : this.id,
							source : $('span', dropped).attr("id")
						},  function(data) 
							{
	    						//alert(data);
	    					}
	    				);
	    				
						if(this.parentNode == dropped)
							return;
						if (this.expanderTime){
							window.clearTimeout(this.expanderTime);
							this.expanded = false;
						}
						subbranch = $('ul', this.parentNode);
						if (subbranch.size() == 0) {
							$(this).after('<ul></ul>');
							subbranch = $('ul', this.parentNode);
						}
						oldParent = dropped.parentNode;
						subbranch.eq(0).append(dropped);
						oldBranches = $('li', oldParent);
						if (oldBranches.size() == 0) {
							$('div.expandImage', oldParent.parentNode).attr('class', 'toggleSpacer expandImage');
							$('div.expandImage', oldParent.parentNode).html('&nbsp;');
							$(oldParent).remove();
						}
						expander = $('div.expandImage:first', this.parentNode);
						if (expander.attr("class").indexOf('toggleSpacer') > -1)
						{
							expander.attr('class', 'toggleMinus expandImage');
							expander.html('&nbsp;');
						}
					}
				}
			);
			$('li.treeItem').Draggable(
				{
					revert		: true,
					autoSize		: true,
					ghosting			: true/*,
					onStop		: function()
					{
						$('span.textHolder').each(
							function()
							{
								this.expanded = false;
							}
						);
					}*/
				}
			);
			
			$('#deletediv').toggle();
			
			$('#deleter').Droppable(
				{
					accept			: 'treeItem',
					hoverclass		: 'dropOver',
					activeclass		: 'fakeClass',
					tollerance		: 'pointer',
					ondrop			: function(dropped)
					{
						id = $(this).parents(".myTree").attr("id");
						
						//$.post("common/html/menu/ajax/" + id + "_remover.php",
						$.post(deleter_url,
					    {
							item : $('span', dropped).attr("id")
						},  function(data) 
							{
	    						if(data == "true")
	    						{
	    							oldParent = dropped.parentNode;
									oldBranches = $('li', oldParent);
									if (oldBranches.size() == 0) {
										$('img.expandImage', oldParent.parentNode).attr('src', 'layout/aqua/img/common/treemenu/spacer.gif');
										$(oldParent).remove();
									}
									
									$(dropped).remove();
	    						}
	    						else
	    						{
	    							//alert();
	    							//alert(data);
	    							alert(translation('CouldNotRemoveItem', 'admin'));
	    						}
	    					}
	    				);
						
					}
				}
			);
		}
	);
	
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