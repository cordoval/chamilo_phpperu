<?php
class HtmlEditorCkeditorImageProcessor extends HtmlEditorProcessor
{
    function run()
    {
        $selected_object = $this->get_selected_content_objects();
        $object = RepositoryDataManager :: get_instance()->retrieve_content_object($selected_object);

        $path = Path :: get(WEB_PATH) . $this->get_repository_document_display_url(array(RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $object->get_id()));

        $html = array();
        $html[] = '<script type="text/javascript">';
        $html[] = 'window.opener.CKEDITOR.tools.callFunction(' . $this->get_parameter('CKEditorFuncNum') . ', \'' . $path . '\', function() {';

        $html[] = '    var element, dialog = this.getDialog();';

        $html[] = '    if (dialog.getName() == \'image\')';
        $html[] = '    {';

        // Set the alternate text of the image
        $html[] = '        element = dialog.getContentElement( \'info\', \'txtAlt\' );';
        $html[] = '        if ( element )';
        $html[] = '        {';
        $html[] = '            element.setValue( \'' . addslashes($object->get_title()) . '\' );';
        $html[] = '        }';

        $html[] = '    }';

        $html[] = '});';
        $html[] = 'window.close();';

        $html[] = '</script>';

        echo implode("\n", $html);
    }
}
?>