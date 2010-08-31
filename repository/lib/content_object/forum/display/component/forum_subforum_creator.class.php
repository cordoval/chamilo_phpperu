<?php
/**
 * $Id: forum_subforum_creator.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package repository.lib.complex_display.forum.component
 */
require_once dirname(__FILE__) . '/../forum_display.class.php';
class ForumDisplayForumSubforumCreatorComponent extends ForumDisplay implements RepoViewerInterface
{

    function run()
    {
        if ($this->get_parent()->is_allowed(ADD_RIGHT))
        {
            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->set_maximum_select(RepoViewer :: SELECT_SINGLE);
            $repo_viewer->set_parameter(ComplexDisplay :: PARAM_DISPLAY_ACTION, ForumDisplay :: ACTION_CREATE_SUBFORUM);
            $repo_viewer->set_parameter(ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID, $this->get_complex_content_object_item_id());

            if (! $repo_viewer->is_ready_to_be_published())
            {
                $repo_viewer->run();
            }
            else
            {
                $cloi = ComplexContentObjectItem :: factory(Forum :: get_type_name());

                if ($this->get_complex_content_object_item())
                {
                    $cloi->set_parent($this->get_complex_content_object_item()->get_ref());
                }
                else
                {
                    $cloi->set_parent($this->get_root_content_object_id());
                }

                $cloi->set_ref($repo_viewer->get_selected_objects());
                $cloi->set_user_id($this->get_user_id());
                $cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($cloi->get_parent()));

                $cloi->create();

                $this->my_redirect();
            }

        }
    }

    private function my_redirect($pid, $forum, $is_subforum)
    {
        $message = htmlentities(Translation :: get('SubforumCreated'));

        $params = array();
        $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_VIEW_FORUM;
        $params[ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();

        $this->redirect($message, '', $params);
    }

    function get_allowed_content_object_types()
    {
        return array(Forum :: get_type_name());
    }

}
?>