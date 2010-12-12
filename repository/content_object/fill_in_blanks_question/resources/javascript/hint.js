(function($)
{
    function showImageHint(ev, ui)
    {
        var hintIdentifier = $(this).attr('id').replace('hint_', ''), identifiers = hintIdentifier
                .split('_'), complexContentObjectItemId = identifiers[0], position = parseInt(identifiers[1]), elementName = complexContentObjectItemId
                + '[' + position + ']', element = $('input[name="'
                + elementName + '"],select[name="' + elementName + '"]'), elementType = element
                .attr('type'), ajaxUri = getPath('WEB_PATH') + 'ajax.php';

        var result = doAjaxPost(ajaxUri, {
            'context' : 'repository\\content_object\\assessment',
            'method' : 'hint',
            'hint_identifier' : hintIdentifier
        });
        result = eval('(' + result + ')');

        if (elementType == 'text')
        {
            element.val(result.properties.hint);
        }
        else if (elementType == 'select-one')
        {
            var hintHtml = '<div class="splitter">' + getTranslation('Hint')
                    + ' #' + (position + 1) + '</div>';
            hintHtml += '<div class="with_borders">' + result.properties.hint
                    + '</div>';
            $(this).parent().after(hintHtml);
        }

        $(this).attr('src', $(this).attr('src').replace('.png', '_na.png'))
                .removeClass('hint').addClass('hint_disabled');
    }

    $(document).ready(function()
    {
        $('img.hint').live('click', showImageHint);
    });

})(jQuery);