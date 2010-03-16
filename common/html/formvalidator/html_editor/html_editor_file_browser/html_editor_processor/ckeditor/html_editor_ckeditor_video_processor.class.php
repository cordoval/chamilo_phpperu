<?php
require_once Path :: get_plugin_path() . 'getid3/getid3.php';

class HtmlEditorCkeditorVideoProcessor extends HtmlEditorProcessor
{
    function run()
    {
        $selected_object = $this->get_selected_content_objects();
        $object = RepositoryDataManager :: get_instance()->retrieve_content_object($selected_object);

        $video_include_path = Path :: get(WEB_PATH) . $this->get_repository_document_display_url(array(RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $object->get_id()));
        $video_getid3 = new getID3();
        $video_info = $video_getid3->analyze($object->get_full_path());

        $html = array();
        $html[] = '<script type="text/javascript">';
        $html[] = 'window.opener.CKEDITOR.tools.callFunction(' . $this->get_parameter('CKEditorFuncNum') . ', \'' . $video_include_path . '\', function() {';

        $html[] = '    var element, dialog = this.getDialog();';

        $html[] = '    if (dialog.getName() == \'chamilovideo\')';
        $html[] = '    {';

        // Set the type of the movie
        $html[] = '        element = dialog.getContentElement( \'info\', \'type\' );';
        $html[] = '        if ( element )';
        $html[] = '        {';
        $html[] = '            element.setValue( \'' . $object->get_mime_type() . '\' );';
        $html[] = '        }';

        // Set the width of the movie
        $html[] = '        element = dialog.getContentElement( \'info\', \'width\' );';
        $html[] = '        if ( element )';
        $html[] = '        {';
        $html[] = '            element.setValue( ' . $video_info['video']['resolution_x'] . ' );';
        $html[] = '        }';

        // Set the height of the movie
        $html[] = '        element = dialog.getContentElement( \'info\', \'height\' );';
        $html[] = '        if ( element )';
        $html[] = '        {';
        $html[] = '            element.setValue( ' . $video_info['video']['resolution_y'] . ' );';
        $html[] = '        }';

        $html[] = '    }';

        $html[] = '});';
        $html[] = 'window.close();';

        $html[] = '</script>';

        echo implode("\n", $html);
    }
}
?>