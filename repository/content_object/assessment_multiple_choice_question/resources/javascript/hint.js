(function($)
{
    function showHint(ev, ui)
    {
        var hintIdentifier = $(this).attr('id').replace('hint_', ''),
            ajaxUri = getPath('WEB_PATH') + 'repository/content_object/assessment_multiple_choice_question/php/ajax/hint.php';
        
        var result = doAjaxPost(ajaxUri, { 'complex_content_object_item_id': hintIdentifier });
        result = eval('(' + result + ')');
        
        $(this).after(result.properties.hint).remove();
    }

    $(document).ready(function()
    {
        $('a.hint_button').live('click', showHint);
    });

})(jQuery);