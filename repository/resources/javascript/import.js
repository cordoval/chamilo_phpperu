(function($)
{
    function disableImportButton(e, ui)
    {
        $(this).attr('disabled', 'disabled');
        $(this).removeClass('positive');
        $(this).addClass('loading');
        $(this).html(getTranslation('Uploading', null, 'common\libraries'));
    }
    
    $(document).ready(function()
    {
        $('#import_button').live('click', disableImportButton);
    });

})(jQuery);