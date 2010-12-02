<?php
namespace common\extensions\video_conferencing_manager;

use common\libraries;

use repository;

use common\libraries\Utilities;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\EqualityCondition;

use repository\ExternalSync;
use repository\RepositoryDataManager;

require_once dirname(__FILE__) . '/../video_conferencing_object_display.class.php';

class VideoConferencingComponentViewerComponent extends VideoConferencingComponent
{
    function run()
    {
        $id = Request :: get(VideoConferencingManager :: PARAM_VIDEO_CONFERENCING_ID);

        if ($id)
        {
            $this->display_header();
            $condition = new EqualityCondition(ExternalSync::PROPERTY_ID, $id);
            $external_sync = RepositoryDataManager::get_instance()->retrieve_external_sync($condition);

            $object = $this->retrieve_video_conferencing_object($external_sync);
            $display = VideoConferencingObjectDisplay :: factory($object);

            $html = array();
            $html[] = $display->as_html();

            $toolbar = new Toolbar();
            $toolbar_item = new ToolbarItem(Translation :: get('Back', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_prev.png', 'javascript:history.back();');
            $toolbar->add_item($toolbar_item);

            $type_actions = $this->get_video_conferencing_object_actions($external_sync);
            foreach ($type_actions as $type_action)
            {
                $type_action->set_display(ToolbarItem :: DISPLAY_ICON_AND_LABEL);
                $toolbar->add_item($type_action);
            }

            $html[] = '<br/>' . $toolbar->as_html();
            echo (implode("\n", $html));

            $this->display_footer();
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected', array('OBJECT' => Translation :: get('ExternalObject')), Utilities :: COMMON_LIBRARIES)));
        }

    }
}
?>