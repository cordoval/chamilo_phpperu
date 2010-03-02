<?php
class HtmlEditorCkeditorFlashProcessor extends HtmlEditorProcessor
{
    function run()
    {
        $selected_object = $this->get_selected_content_objects();
        $object = RepositoryDataManager :: get_instance()->retrieve_content_object($selected_object);

//        $path = Path :: get(WEB_PATH) . 'core.php?application=repository&go=document_downloader&display=1&object=' . $object->get_id();
        $path = $object->get_url();

        $html = array();
        $html[] = '<script type="text/javascript">';
        $html[] = 'window.opener.CKEDITOR.tools.callFunction(' . $this->get_parameter('CKEditorFuncNum') . ', \'' . $path . '\', function() {';

        $html[] = '    var element, dialog = this.getDialog();';

//        $html[] = '    if (dialog.getName() == \'chamiloyoutube\')';
//        $html[] = '    {';
//
//        // Set the width of the movie
//        $html[] = '        element = dialog.getContentElement( \'info\', \'width\' );';
//        $html[] = '        if ( element )';
//        $html[] = '        {';
//        $html[] = '            element.setValue( ' . $object->get_width() . ' );';
//        $html[] = '        }';
//
//        // Set the height of the movie
//        $html[] = '        element = dialog.getContentElement( \'info\', \'height\' );';
//        $html[] = '        if ( element )';
//        $html[] = '        {';
//        $html[] = '            element.setValue( ' . $object->get_height() . ' );';
//        $html[] = '        }';
//
//        $html[] = '    }';
//
        $html[] = '});';
        $html[] = 'window.close();';
//
        $html[] = '</script>';

        echo implode("\n", $html);
    }
}
?>