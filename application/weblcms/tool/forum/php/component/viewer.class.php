<?php
namespace application\weblcms\tool\forum;

use common\libraries\Display;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\AndCondition;
use common\libraries\EqualityCondition;
use common\libraries\Request;
use common\libraries\Path;
use common\libraries\DelegateComponent;
use common\libraries\Translation;
use common\libraries\WebApplication;

use repository\ComplexDisplay;
use repository\content_object\forum\Forum;
use repository\content_object\forum\ForumComplexDisplaySupport;
use repository\content_object\forum\ForumDisplay;

use application\weblcms\WeblcmsForumTopicViewsTracker;
use application\weblcms\WeblcmsDataManager;
use application\weblcms\WeblcmsRights;
use application\weblcms\WeblcmsManager;
use application\weblcms\Tool;

use tracking\Event;

/**
 * $Id: forum_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.forum.component
 */
require_once dirname(__FILE__) . '/../forum_tool.class.php';
require_once Path :: get_repository_content_object_path() . 'forum/php/display/forum_complex_display_support.class.php';

class ForumToolViewerComponent extends ForumTool implements DelegateComponent, ForumComplexDisplaySupport
{

    private $trail;
    private $root_content_object;
    private $publication_id;

    function run()
    {

        $this->publication_id = Request :: get(Tool :: PARAM_PUBLICATION_ID);
        $this->set_parameter(Tool :: PARAM_PUBLICATION_ID, $this->publication_id);

        if (is_null(Request :: get(ComplexDisplay :: PARAM_DISPLAY_ACTION)))
        {
            Request :: set_get(ComplexDisplay :: PARAM_DISPLAY_ACTION, ForumDisplay :: ACTION_VIEW_FORUM);
        }

        $this->trail = $trail = BreadcrumbTrail :: get_instance();

        $object = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication(Request :: get(Tool :: PARAM_PUBLICATION_ID));
        $this->root_content_object = $object->get_content_object();

        ComplexDisplay :: launch(Forum :: get_type_name(), $this);
    }

    function get_root_content_object()
    {
        return $this->root_content_object;
    }

    function display_header($trail)
    {
        //        if ($trail)
        //        {
        //            $this->trail->merge($trail);
        //        }


        return parent :: display_header();
    }

    function forum_topic_viewed($complex_topic_id)
    {
        require_once WebApplication :: get_application_class_path(WeblcmsManager :: APPLICATION_NAME) . 'trackers/weblcms_forum_topic_views_tracker.class.php';

        $parameters = array();
        $parameters[WeblcmsForumTopicViewsTracker :: PROPERTY_USER_ID] = $this->get_user_id();
        $parameters[WeblcmsForumTopicViewsTracker :: PROPERTY_PUBLICATION_ID] = $this->publication_id;
        $parameters[WeblcmsForumTopicViewsTracker :: PROPERTY_FORUM_TOPIC_ID] = $complex_topic_id;

        Event :: trigger('view_forum_topic', WeblcmsManager :: APPLICATION_NAME, $parameters);
    }

    function forum_count_topic_views($complex_topic_id)
    {
        require_once WebApplication :: get_application_class_path(WeblcmsManager :: APPLICATION_NAME) . 'trackers/weblcms_forum_topic_views_tracker.class.php';

        $conditions[] = new EqualityCondition(WeblcmsForumTopicViewsTracker :: PROPERTY_PUBLICATION_ID, $this->publication_id);
        $conditions[] = new EqualityCondition(WeblcmsForumTopicViewsTracker :: PROPERTY_FORUM_TOPIC_ID, $complex_topic_id);
        $condition = new AndCondition($conditions);

        $dummy = new WeblcmsForumTopicViewsTracker();
        return $dummy->count_tracker_items($condition);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('ForumToolBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(Tool :: PARAM_PUBLICATION_ID);
    }

}

?>