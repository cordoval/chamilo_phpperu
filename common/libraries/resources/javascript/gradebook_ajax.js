(function ($) {
     
    
	$path = getPath('WEB_APP_PATH');
	
	$('.data_table tbody tr td:nth-child(5)').editable($path+'lib/gradebook/save.php',{
		width: '50px',
		indicator : 'Saving...',
        tooltip   : 'Click to edit...'
	});
	
	$('.data_table tbody tr td:nth-child(5)').each(function(){
		$id = $(this).parent().attr('id');	
		$(this).attr('id', $id);
	});
	
})(jQuery);