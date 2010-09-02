<?php

/**
 * $Id: forum_post_creator.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package repository.lib.complex_display.forum.component
 */
require_once dirname(__FILE__) . '/../forum_display.class.php';

class ForumDisplayForumPostCreatorComponent extends ForumDisplay implements RepoViewerInterface
{

    function run()
    {
        $selected_complex_content_object_item = $this->get_selected_complex_content_object_item();
        $rdm = RepositoryDataManager :: get_instance();

        if ($selected_complex_content_object_item)
        {
            $reply_lo = $rdm->retrieve_content_object($selected_complex_content_object_item->get_ref(), ForumPost :: get_type_name());
        }



        if (!RepoViewer::is_ready_to_be_published())
        {
            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->set_parameter(ComplexDisplay :: PARAM_DISPLAY_ACTION, ForumDisplay :: ACTION_CREATE_FORUM_POST);
            $repo_viewer->set_parameter(ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID, $this->get_complex_content_object_item_id());

            //$repo_viewer->parse_input_from_table();

            if ($reply_lo)
            {
                if (substr($reply_lo->get_title(), 0, 3) == 'RE:')
                {
                    $reply = $reply_lo->get_title();
                }
                else
                {
                    $reply = 'RE: ' . $reply_lo->get_title();
                }

                $repo_viewer->set_creation_defaults(array(ContentObject :: PROPERTY_TITLE => $reply));
            }
            
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ForumDisplay :: ACTION_VIEW_FORUM)), $this->get_root_content_object()->get_title()));
            $topic = RepositoryDataManager :: get_instance()->retrieve_content_object($this->get_complex_content_object_item()->get_ref());
            $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ForumDisplay :: ACTION_VIEW_TOPIC, ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item()->get_id())), $topic->get_title()));
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('reply_on_post')));

            $repo_viewer->run();
        }
        else
        {
            $object_ids = RepoViewer::get_selected_objects();

            if (!is_array($object_ids))
            {
                $object_ids = array($object_ids);
            }

            foreach ($object_ids as $object_id)
            {
                $cloi = ComplexContentObjectItem :: factory(ForumPost :: get_type_name());

                $cloi->set_ref($object_id);
                $cloi->set_user_id($this->get_user_id());
                $cloi->set_parent($this->get_complex_content_object_item()->get_ref());
                $cloi->set_display_order($rdm->select_next_display_order($cloi->get_parent()));

                if ($selected_complex_content_object_item)
                {
                    $cloi->set_reply_on_post($selected_complex_content_object_item->get_id());
                }

                $cloi->create();
            }

            $this->my_redirect();
        }
    }

    private function my_redirect()
    {
        $message = htmlentities(Translation :: get('ContentObjectCreated'));

        $params = array();
        $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_VIEW_TOPIC;
        $params[ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();

        $this->redirect($message, '', $params);
    }

    function get_allowed_content_object_types()
    {
        return array(ForumPost :: get_type_name());
    }

}

?>