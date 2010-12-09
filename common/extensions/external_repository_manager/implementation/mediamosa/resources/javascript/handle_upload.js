


$(function ()
{
    $('#uploadify').uploadify ({
            'uploader': getPath('WEB_PATH') + '/common/libraries/resources/images/' + getTheme() + '/plugin/jquery/uploadify2/uploadify.swf',
            'script': $('mediamosa_upload').attr('action'),
            'cancelImg': getPath('WEB_PATH') + '/common/libraries/resources/images/' + getTheme() + '/plugin/jquery/uploadify2/cancel.png',
            'folder': 'not_important',
            'auto': true,
            'displayData': 'percentage',
            'scriptData': {},
            onComplete: function (evt, queueID, fileObj, response, data)
            {
                   if (response !== '1')
                   alert(response);
                   window.location = $('#redirect_uri').val();
            }
    });
});

