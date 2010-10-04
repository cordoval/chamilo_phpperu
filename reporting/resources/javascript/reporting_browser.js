( function($)
{
	var handle_click = function(ev, ui)
	{
        var div = $(this).attr('id');
        var parent = $(this).parent().parent().parent();

        $(parent.children(".applications-list")).each(function(i){
            $(this).children().each(function(j){
               if($(this).attr('id')=='application-'+div)
               {
                   $(this).fadeIn(500);
               }else
               {
                   $(this).hide();
               }
            });
        });

        $(parent.children("div.application-list")).each(function (i) {
            alert($("div.application-".div).attr("id"));
		});
	}

	$(document).ready( function()
	{
		$(".dock-item").bind('click',handle_click);
        $("div.applications-list").each(function(i){
           $(this).children().each(function(j){
              $(this).hide();
           });
        });
	});
})(jQuery);