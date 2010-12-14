<?php
namespace common\libraries;

use repository\RepositoryDataManager;
use repository\RepositoryManager;

require_once Path :: get_plugin_path() . 'getid3/getid3.php';

class HtmlEditorCkeditorHandbookTopicProcessor extends HtmlEditorProcessor
{

    function run()
    {
        $selected_object = $this->get_selected_content_objects();
        $object = RepositoryDataManager :: get_instance()->retrieve_content_object($selected_object);


        //TODO: return link with UID
        $handbook_topic_include_path = 'blablabla ' .$object->get_id();

        $html = array();
        $html[] = '<script type="text/javascript">';
        $html[] = 'window.opener.CKEDITOR.tools.callFunction(' . $this->get_parameter('CKEditorFuncNum') . ', \'' . $handbook_topic_include_path . '\', function() {';
        $html[] = '});';
        $html[] = 'window.close();';

        $html[] = '</script>';

        echo implode("\n", $html);
    }
}
?>