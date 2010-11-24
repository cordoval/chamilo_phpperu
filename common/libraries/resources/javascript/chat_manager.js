$(function ()
{
    function send_message_clicked(evt, ui)
    {
    	var message = $('#chat_message').attr('value');
    	
    	$('#chat_message').attr('value', '');
    	
    	var response = $.ajax({
			type: "POST",
			dataType: "xml",
			url: 'user/ajax/chat_manager.php',
			data: { from_user_id: from_user_id, to_user_id: to_user_id, message: message, action: 'send_message' },
			async: false
		}).responseText;
    	
    	retrieve_messages();
    	
    	return false;
    }
    
    function retrieve_messages()
    {
    	var response = $.ajax({
			type: "POST",
			dataType: "xml",
			url: 'user/ajax/chat_manager.php',
			data: { from_user_id: from_user_id, to_user_id: to_user_id, action: 'retrieve_messages', last_message_date: last_message_date },
			async: false
		}).responseText;
    	
    	var tree = $.xml2json(response, true);

    	if(tree.message)
    	{
    		$.each(tree.message, function(i, the_node)
	    	{
	    		var value = $('#chat_window').attr('value');
	    		$('#chat_window').attr('value', value + the_node.message + "\n");
	    		last_message_date = the_node.date;
			});
    	}
    	
    	$('#chat_window').attr('scrollTop', $('#chat_window').attr('scrollHeight'));
    }
    
	$(document).ready(function ()
    {
    	$('#send_message').live('click', send_message_clicked);
    	$('#chat_window').attr('scrollTop', $('#chat_window').attr('scrollHeight'));
    	$.interval(retrieve_messages, 2000);
    	
    });
    
});