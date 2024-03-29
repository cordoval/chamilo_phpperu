<?php
namespace repository\content_object\forum;

use common\libraries\Translation;
use common\libraries\Utilities;
use common\extensions\repo_viewer\RepoViewerInterface;
use common\extensions\repo_viewer\RepoViewer;
use repository\ComplexDisplay;
use repository\content_object\forum_topic\ForumTopic;
use repository\ComplexContentObjectItem;
use repository\RepositoryDataManager;


/**
 * $Id: forum_topic_creator.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package repository.lib.complex_display.forum.component
 */

require_once dirname(__FILE__) . '/../forum_display.class.php';

class ForumDisplayForumTopicCreatorComponent extends ForumDisplay implements RepoViewerInterface
{

    function run()
    {


        if (!RepoViewer::is_ready_to_be_published())
        {
            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->set_parameter(ComplexDisplay :: PARAM_DISPLAY_ACTION, ForumDisplay :: ACTION_CREATE_TOPIC);
            $repo_viewer->set_parameter(ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID, $this->get_complex_content_object_item_id());
            $repo_viewer->run();
        }
        else
        {
            $object_id = RepoViewer::get_selected_objects();

            if (! is_array($object_id))
            {
                $object_id = array($object_id);
            }

            foreach ($object_id as $key => $value)
            {
                $cloi = ComplexContentObjectItem :: factory(ForumTopic :: get_type_name());

                if ($this->get_complex_content_object_item())
                {
                    $cloi->set_parent($this->get_complex_content_object_item()->get_ref());
                }
                else
                {
                    $cloi->set_parent($this->get_root_content_object_id());
                }

                $cloi->set_ref($value);
                $cloi->set_user_id($this->get_user_id());
                $cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($cloi->get_parent()));

                $cloi->create();
            }

            $this->my_redirect();
        }
    }

    private function my_redirect($pid, $forum, $is_subforum)
    {
        $message = htmlentities(Translation :: get('ObjectCreated', array('OBJECT' => Translation :: get('ForumTopic')), Utilities :: COMMON_LIBRARIES));

        $params = array();
        $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_VIEW_FORUM;
        $params[ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();

        $this->redirect($message, false, $params);
    }

    function get_allowed_content_object_types()
    {
        return array(ForumTopic :: get_type_name());
    }

}
?>