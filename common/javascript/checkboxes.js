$(function ()
{
    function select_all_clicked(ui, evt)
    {
    	$('.chckbox').attr('checked', 'checked');
    }
    
    function unselect_all_clicked(ui, evt)
    {
    	$('.chckbox').attr('checked', null);
    }
    
	$(document).ready(function ()
    {
    	$("#selectall").live('click', select_all_clicked);
    	$("#unselectall").live('click', unselect_all_clicked);
    });
    
});