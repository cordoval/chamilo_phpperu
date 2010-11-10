<?php
namespace common\libraries;

use repository\RepositoryDataManager;

class HtmlEditorCkeditorYoutubeProcessor extends HtmlEditorProcessor
{

    function run()
    {
        $selected_object = $this->get_selected_content_objects();
        $object = RepositoryDataManager :: get_instance()->retrieve_content_object($selected_object);

        $html = array();
        $html[] = '<script type="text/javascript">';
        $html[] = 'window.opener.CKEDITOR.tools.callFunction(' . $this->get_parameter('CKEditorFuncNum') . ', \'' . $object->get_video_url() . '?' . RepositoryManager :: PARAM_CONTENT_OBJECT_ID . '=' . $object->get_id() . '\', function() {';

        $html[] = '    var element, dialog = this.getDialog();';

        $html[] = '    if (dialog.getName() == \'chamiloyoutube\')';
        $html[] = '    {';

        // Set the width of the movie
        $html[] = '        element = dialog.getContentElement( \'info\', \'width\' );';
        $html[] = '        if ( element )';
        $html[] = '        {';
        $html[] = '            element.setValue( ' . $object->get_width() . ' );';
        $html[] = '        }';

        // Set the height of the movie
        $html[] = '        element = dialog.getContentElement( \'info\', \'height\' );';
        $html[] = '        if ( element )';
        $html[] = '        {';
        $html[] = '            element.setValue( ' . $object->get_height() . ' );';
        $html[] = '        }';

        $html[] = '    }';

        $html[] = '});';
        $html[] = 'window.close();';

        $html[] = '</script>';

        echo implode("\n", $html);
    }
}
?>