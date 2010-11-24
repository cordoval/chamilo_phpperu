<?php
namespace repository;

use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\ActionBarRenderer;
use common\libraries\ActionBarSearchForm;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\AndCondition;
use common\libraries\PatternMatchCondition;
use common\libraries\Utilities;
use rights\RightsManager;

require_once Path :: get_repository_path() . 'lib/video_conferencing_instance_manager/component/video_conferencing_instance_browser/video_conferencing_instance_browser_table.class.php';

class VideoConferencingInstanceManagerBrowserComponent extends VideoConferencingInstanceManager
{

    private $action_bar;

    function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            $this->not_allowed();
        }

        $this->action_bar = $this->get_action_bar();
        $parameters = $this->get_parameters();
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        $table = new VideoConferencingInstanceBrowserTable($this, $parameters, $this->get_condition());

        $this->display_header();
        echo $this->action_bar->as_html();
        echo $table->as_html();
        $this->display_footer();
    }

    function get_condition()
    {
        $query = $this->action_bar->get_query();

        if (isset($query) && $query != '')
        {
            $conditions = array();
            $conditions[] = new PatternMatchCondition(VideoConferencing :: PROPERTY_TITLE, '*' . $query . '*');
            $conditions[] = new PatternMatchCondition(VideoConferencing :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            $condition = new AndCondition($conditions);
        }
        else
        {
            $condition = null;
        }

        return $condition;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('AddVideoConferencingInstance'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(VideoConferencingInstanceManager :: PARAM_INSTANCE_ACTION => VideoConferencingInstanceManager :: ACTION_CREATE_INSTANCE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageRights', null, RightsManager :: APPLICATION_NAME), Theme :: get_common_image_path() . 'action_rights.png', $this->get_url(array(VideoConferencingInstanceManager :: PARAM_INSTANCE_ACTION => VideoConferencingInstanceManager :: ACTION_MANAGE_INSTANCE_RIGHTS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        return $action_bar;
    }
}
?>