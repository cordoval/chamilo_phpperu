(function($)
{
    function showLinkHint(ev, ui)
    {
        var hintIdentifier = $(this).attr('id').replace('hint_', ''), ajaxUri = getPath('WEB_PATH')
                + 'ajax.php';

        var result = doAjaxPost(
                ajaxUri,
                {
                    'context' : 'repository\\content_object\\assessment',
                    'method' : 'hint',
                    'hint_identifier' : hintIdentifier
                });
        
        result = eval('(' + result + ')');

        $(this).after(result.properties.hint).remove();
    }

    $(document).ready(function()
    {
        $('a.hint_button').live('click', showLinkHint);
    });

})(jQuery);