<?php
namespace application\handbook;
use common\libraries\Request;
use common\libraries\Display;
use repository\RepositoryDataManager;

require_once dirname(__FILE__) . '/handbook_viewer.class.php';

/**
 * Component to pass the uid of the selected topic
 * @author Nathalie Blocry
 */
class HandbookManagerHandbookTopicUidPasserComponent extends HandbookManagerHandbookViewerComponent
{

    protected $handbook_id;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {

        $handbook_selection_id = Request::get(HandbookManager::PARAM_HANDBOOK_SELECTION_ID);

        if($handbook_selection_id == null)
        {
            //FF OM TE TESTEN
            $handbook_selection_id = 13;

        }
        $object = RepositoryDataManager :: get_instance()->retrieve_content_object($handbook_selection_id);
        $handbook_topic_include_path = $object->get_uuid();

        $html = array();
        $html[] = '<script type="text/javascript">';
        $html[] = 'window.opener.CKEDITOR.tools.callFunction(' . Request::get('CKEditorFuncNum') . ', \'' . $handbook_topic_include_path . '\', function() {';
        $html[] = '});';
        $html[] = 'window.close();';

        $html[] = '</script>';

        var_dump($html);

        echo implode("\n", $html);

    }

   

}

?>